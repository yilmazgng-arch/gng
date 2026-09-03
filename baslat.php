<?php
/**
 * Kartla ödeme — oturum başlatma uç noktası.
 *
 * Müşterinin tarayıcısından SADECE ürün id/beden/renk/adet ve teslimat
 * bilgilerini kabul eder — fiyatı ASLA müşteriden gelen veriden almaz.
 * Tutar, Firestore'daki GÜNCEL ürün fiyatları ve kupon kaydı üzerinden
 * burada yeniden hesaplanır. Bu, birinin tarayıcı konsolundan sahte bir
 * tutar göndererek daha az ödeme yapmasını engeller.
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0'); // hatalar asla ham HTML olarak müşteriye sızmasın

require __DIR__ . '/config.php';
require __DIR__ . '/firestore-helper.php';
require __DIR__ . '/iyzico-client.php';
require __DIR__ . '/pricing.php';

function respondError($message, $httpCode = 400){
  http_response_code($httpCode);
  echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
  exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  respondError('Yalnızca POST kabul edilir.', 405);
}

if(!iyzicoConfigured()){
  respondError('Kartla ödeme henüz aktif değil.', 503);
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if(!is_array($input)){
  respondError('Geçersiz istek gövdesi.');
}

$items = isset($input['items']) && is_array($input['items']) ? $input['items'] : [];
if(!count($items) || count($items) > 30){
  respondError('Sepet boş ya da çok fazla ürün içeriyor.');
}

function trimmed($v, $max = 200){
  $s = is_string($v) ? trim($v) : '';
  return mb_substr($s, 0, $max);
}

$buyerName = trimmed($input['buyerName'] ?? '', 120);
$buyerEmail = trimmed($input['buyerEmail'] ?? '', 160);
$buyerPhone = trimmed($input['buyerPhone'] ?? '', 30);
$address = is_array($input['address'] ?? null) ? $input['address'] : [];
$addrLine1 = trimmed($address['line1'] ?? '', 300);
$addrDistrict = trimmed($address['district'] ?? '', 100);
$addrCity = trimmed($address['city'] ?? '', 100);
$couponCode = trimmed($input['couponCode'] ?? '', 30);

if($buyerName === '' || $buyerEmail === '' || $buyerPhone === '' || $addrLine1 === '' || $addrCity === ''){
  respondError('Ad soyad, e-posta, telefon ve teslimat adresi gerekli.');
}
if(!filter_var($buyerEmail, FILTER_VALIDATE_EMAIL)){
  respondError('E-posta adresi geçersiz.');
}

try {
  $products = fetchAllProductsFromFirestore();
} catch (Exception $e){
  respondError('Ürün bilgileri şu an alınamıyor, birazdan tekrar dene.', 502);
}
$productsById = [];
foreach($products as $p){
  if(isset($p['id'])) $productsById[(int)$p['id']] = $p;
}

$coupon = $couponCode !== '' ? fetchCouponFromFirestore($couponCode) : null;

try {
  $totals = computeOrderTotals($items, $productsById, $coupon);
} catch (Exception $e){
  respondError($e->getMessage());
}
$basketItems = $totals['basketItems'];
$subtotal = $totals['subtotal'];
$discount = $totals['discount'];
$total = $totals['total'];

$nameParts = preg_split('/\s+/', trim($buyerName), 2);
$firstName = $nameParts[0] ?? $buyerName;
$lastName = $nameParts[1] ?? $nameParts[0] ?? $buyerName;

$fullAddress = $addrLine1 . ($addrDistrict ? ', ' . $addrDistrict : '');
$conversationId = 'sonya-' . bin2hex(random_bytes(6));

$payload = [
  'locale' => 'tr',
  'conversationId' => $conversationId,
  // Not: kendi kupon indirimimiz iyzico'nun kendi kampanya mekanizmasından bağımsız
  // olduğu için "price" ve "paidPrice"ı EŞİT ve İNDİRİMLİ tutara göre gönderiyoruz —
  // basketItems toplamı da aynı tutara eşit olacak şekilde hesaplanıyor (bkz. pricing.php).
  // (price != paidPrice yalnızca iyzico'nun KENDİ taksit/kampanya farkını yansıttığı
  // durumlar için, burada geçerli değil.)
  'price' => number_format($total, 2, '.', ''),
  'paidPrice' => number_format($total, 2, '.', ''),
  'currency' => 'TRY',
  'basketId' => $conversationId,
  'paymentGroup' => 'PRODUCT',
  'callbackUrl' => IYZICO_CALLBACK_URL,
  'buyer' => [
    'id' => 'BY' . substr(md5($buyerEmail), 0, 10),
    'name' => $firstName,
    'surname' => $lastName,
    'identityNumber' => '11111111111', // müşteriden TC kimlik no toplamıyoruz; iyzico format doğrulaması için yer tutucu
    'email' => $buyerEmail,
    'gsmNumber' => normalizePhoneForIyzico($buyerPhone),
    'registrationAddress' => $fullAddress,
    'city' => $addrCity,
    'country' => 'Turkey',
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
  ],
  'shippingAddress' => [
    'contactName' => $buyerName,
    'city' => $addrCity,
    'country' => 'Turkey',
    'address' => $fullAddress,
  ],
  'billingAddress' => [
    'contactName' => $buyerName,
    'city' => $addrCity,
    'country' => 'Turkey',
    'address' => $fullAddress,
  ],
  'basketItems' => $basketItems,
];

try {
  $client = new IyzicoClient(IYZICO_API_KEY, IYZICO_SECRET_KEY, IYZICO_BASE_URL);
  $result = $client->initializeCheckoutForm($payload);
} catch (Exception $e){
  respondError('Ödeme sağlayıcısına bağlanılamadı, birazdan tekrar dene.', 502);
}

if(($result['status'] ?? '') !== 'success' || empty($result['paymentPageUrl'])){
  $msg = $result['errorMessage'] ?? 'Ödeme başlatılamadı.';
  respondError($msg, 502);
}

echo json_encode([
  'ok' => true,
  'paymentPageUrl' => $result['paymentPageUrl'],
  'token' => $result['token'] ?? null,
], JSON_UNESCAPED_UNICODE);
