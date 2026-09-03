<?php
/**
 * Sipariş tutarını GÜVENİLİR kaynaklardan (Firestore'daki ürün/kupon verisi)
 * yeniden hesaplayan saf mantık — ağ çağrılarından ayrı tutuluyor ki test
 * edilebilsin (bkz. /tmp/pricing_test.php benzeri testler).
 *
 * @param array $items          Müşteriden gelen sepet satırları: [{productId,size,color,qty}, ...]
 * @param array $productsById   Firestore'dan okunan ürünler, id => ürün verisi
 * @param array|null $coupon    Firestore'dan okunan kupon verisi (varsa)
 * @return array ['basketItems'=>[...], 'subtotal'=>float, 'discount'=>float, 'total'=>float]
 * @throws Exception Geçersiz/stoksuz/eksik bir durumda, mesajı kullanıcıya gösterilebilir.
 */
function computeOrderTotals($items, $productsById, $coupon){
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
  if($coupon && ($coupon['active'] ?? true) !== false){
    $type = ($coupon['type'] ?? '') === 'amount' ? 'amount' : 'percent';
    $value = is_numeric($coupon['value'] ?? null) ? (float)$coupon['value'] : 0;
    $discount = $type === 'amount' ? $value : round($subtotal * $value / 100, 2);
    $discount = max(0, min($discount, $subtotal));
  }

  $total = round($subtotal - $discount, 2);
  if($total <= 0){
    throw new Exception('Sipariş tutarı geçersiz.');
  }

  // iyzico basketItems toplamının paidPrice'a eşit olmasını sağlamak için
  // indirimi son kalemden düşüyoruz.
  if($discount > 0 && count($basketItems)){
    $lastIdx = count($basketItems) - 1;
    $newPrice = round((float)$basketItems[$lastIdx]['price'] - $discount, 2);
    $basketItems[$lastIdx]['price'] = number_format(max(0, $newPrice), 2, '.', '');
  }

  return ['basketItems' => $basketItems, 'subtotal' => round($subtotal, 2), 'discount' => round($discount, 2), 'total' => $total];
}

function normalizePhoneForIyzico($phone){
  $digits = preg_replace('/[^0-9]/', '', $phone);
  if(strpos($phone, '+') === 0) return '+' . $digits;
  if(strlen($digits) === 10) return '+90' . $digits; // 5xx xxx xx xx
  if(strlen($digits) === 11 && $digits[0] === '0') return '+90' . substr($digits, 1);
  return '+90' . $digits;
}
