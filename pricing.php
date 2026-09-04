<?php
/**
 * Sipariş tutarını GÜVENİLİR kaynaklardan (Firestore'daki ürün/kupon verisi)
 * yeniden hesaplayan saf mantık — ağ çağrılarından ayrı tutuluyor ki test
 * edilebilsin (bkz. /tmp/pricing_test.php benzeri testler).
 *
 * @param array $items          Müşteriden gelen sepet satırları: [{productId,size,color,qty}, ...]
 * @param array $productsById   Firestore'dan okunan ürünler, id => ürün verisi
 * @param array|null $coupon    Firestore'dan okunan kupon verisi (varsa)
 * @param array $settings       Firestore'dan okunan site ayarları (kargo ücreti/eşiği)
 * @param bool  $isGoldMember   Altın üye ayrıcalığı — kargo her zaman ücretsiz
 * @return array ['basketItems'=>[...], 'subtotal'=>float, 'discount'=>float, 'shipping'=>float, 'total'=>float]
 * @throws Exception Geçersiz/stoksuz/eksik bir durumda, mesajı kullanıcıya gösterilebilir.
 */

/**
 * Kargo ücreti — sitedeki JavaScript karşılığıyla BİREBİR aynı mantık
 * (bkz. shippingFeeAmount() / shippingCost()). İkisi ayrışırsa müşteriye
 * gösterilen tutar ile karttan çekilen tutar farklı olur; bu yüzden bu iki
 * fonksiyon her zaman birlikte değiştirilmeli.
 *
 * - Ayarlardaki kargo ücreti 0 ise (varsayılan) hiçbir siparişte ücret alınmaz.
 * - Ücretsiz kargo eşiği ve üzerindeki siparişlerde ücret alınmaz.
 * - Altın üyelerde her zaman ücretsizdir.
 */
function computeShipping($subtotal, $settings, $isGoldMember = false){
  $fee = isset($settings['shippingFee']) && is_numeric($settings['shippingFee']) ? (float)$settings['shippingFee'] : 0.0;
  if($fee <= 0) return 0.0;
  if($isGoldMember) return 0.0;
  $threshold = isset($settings['shippingThreshold']) && is_numeric($settings['shippingThreshold']) ? (float)$settings['shippingThreshold'] : 0.0;
  if($threshold > 0 && $subtotal >= $threshold) return 0.0;
  return round($fee, 2);
}

/**
 * Kuponun gerçekten kullanılabilir olup olmadığı — sitedeki JavaScript karşılığıyla
 * aynı kurallar (bkz. applyCoupon() / couponExpired()). Müşteri tarayıcı konsolundan
 * süresi dolmuş ya da minimum tutarı tutmayan bir kupon göndermeye çalışsa bile
 * indirim burada uygulanmaz.
 *
 * - active: false ise geçersiz.
 * - expiresAt ("YYYY-AA-GG") varsa o günün sonuna kadar geçerli.
 * - minAmount varsa, ara toplam bu tutarın altındayken geçersiz.
 * - Bu alanların hiçbirini içermeyen eski kuponlar sınırsız çalışmaya devam eder.
 */
function couponIsUsable($coupon, $subtotal){
  if(!is_array($coupon)) return false;
  if(($coupon['active'] ?? true) === false) return false;

  $expiresAt = $coupon['expiresAt'] ?? null;
  if(is_string($expiresAt) && $expiresAt !== ''){
    $end = strtotime($expiresAt . ' 23:59:59');
    if($end !== false && time() > $end) return false;
  }

  $minAmount = $coupon['minAmount'] ?? null;
  if(is_numeric($minAmount) && (float)$minAmount > 0 && $subtotal < (float)$minAmount) return false;

  return true;
}

function computeOrderTotals($items, $productsById, $coupon, $settings = [], $isGoldMember = false){
  $basketItems = [];
  $subtotal = 0.0;
  $lineIndex = 0;

  foreach($items as $line){
    $productId = isset($line['productId']) ? (int)$line['productId'] : 0;
    $size = isset($line['size']) ? (string)$line['size'] : '';
    $color = isset($line['color']) ? (string)$line['color'] : '';
    $qty = isset($line['qty']) ? (int)$line['qty'] : 0;
    $qty = max(1, min(20, $qty));

    if(!isset($productsById[$productId])){
      throw new Exception('Sepetteki bir ürün artık mevcut değil, lütfen sepeti kontrol et.');
    }
    $p = $productsById[$productId];
    $price = isset($p['price']) ? (float)$p['price'] : 0;
    if($price <= 0){
      throw new Exception('Sepetteki bir ürünün fiyatı okunamadı.');
    }

    $variantStock = is_array($p['variantStock'] ?? null) ? $p['variantStock'] : null;
    if($variantStock && isset($variantStock[$color]) && is_array($variantStock[$color])){
      $available = isset($variantStock[$color][$size]) ? (int)$variantStock[$color][$size] : 0;
    } else {
      $available = isset($p['stock']) ? (int)$p['stock'] : 0;
    }
    if($available < $qty){
      throw new Exception('"' . ($p['name'] ?? 'Bir ürün') . '" için yeterli stok yok.');
    }

    $lineTotal = round($price * $qty, 2);
    $subtotal += $lineTotal;
    $lineIndex++;
    $basketItems[] = [
      'id' => 'P' . $productId . '-' . $lineIndex,
      'name' => mb_substr(($p['name'] ?? 'Ürün') . ' x' . $qty . ($size ? ' (' . $size . ')' : ''), 0, 120),
      'category1' => isset($p['cat']) ? mb_substr((string)$p['cat'], 0, 60) : 'Giyim',
      'itemType' => 'PHYSICAL',
      'price' => number_format($lineTotal, 2, '.', ''),
    ];
  }

  $discount = 0.0;
  if($coupon && couponIsUsable($coupon, round($subtotal, 2))){
    $type = ($coupon['type'] ?? '') === 'amount' ? 'amount' : 'percent';
    $value = is_numeric($coupon['value'] ?? null) ? (float)$coupon['value'] : 0;
    $discount = $type === 'amount' ? $value : round($subtotal * $value / 100, 2);
    $discount = max(0, min($discount, $subtotal));
  }

  $shipping = computeShipping(round($subtotal, 2), $settings, $isGoldMember);

  $total = round($subtotal - $discount + $shipping, 2);
  if($total <= 0){
    throw new Exception('Sipariş tutarı geçersiz.');
  }

  // iyzico basketItems toplamının paidPrice'a eşit olmasını sağlamak için
  // indirimi son ÜRÜN kaleminden düşüyoruz.
  if($discount > 0 && count($basketItems)){
    $lastIdx = count($basketItems) - 1;
    $newPrice = round((float)$basketItems[$lastIdx]['price'] - $discount, 2);
    $basketItems[$lastIdx]['price'] = number_format(max(0, $newPrice), 2, '.', '');
  }

  // Kargo, sepette ayrı bir kalem olarak gönderiliyor — böylece basketItems
  // toplamı paidPrice'a eşit kalıyor ve müşteri iyzico ekranında kargo ücretini
  // ayrı bir satır olarak görüyor (ürün fiyatına gizlice eklenmiş gibi durmuyor).
  if($shipping > 0){
    $basketItems[] = [
      'id' => 'KARGO',
      'name' => 'Kargo Ücreti',
      'category1' => 'Kargo',
      'itemType' => 'PHYSICAL',
      'price' => number_format($shipping, 2, '.', ''),
    ];
  }

  return [
    'basketItems' => $basketItems,
    'subtotal' => round($subtotal, 2),
    'discount' => round($discount, 2),
    'shipping' => $shipping,
    'total' => $total,
  ];
}

function normalizePhoneForIyzico($phone){
  $digits = preg_replace('/[^0-9]/', '', $phone);
  if(strpos($phone, '+') === 0) return '+' . $digits;
  if(strlen($digits) === 10) return '+90' . $digits; // 5xx xxx xx xx
  if(strlen($digits) === 11 && $digits[0] === '0') return '+90' . substr($digits, 1);
  return '+90' . $digits;
}
