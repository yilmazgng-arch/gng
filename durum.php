<?php
/**
 * Ödeme sonucunu SUNUCU TARAFINDA doğrulayan uç nokta. "Sipariş tamamlandı"
 * sayfası bu dosyayı çağırır ve DÖNEN CEVABA göre Firestore'a siparişi kaydeder
 * — müşterinin tarayıcısındaki URL'de "başarılı" gibi görünen hiçbir parametreye
 * asla güvenilmez, çünkü tarayıcı tarafındaki hiçbir bilgi sahte olmadığından
 * emin olunamaz. Burada iyzico'ya token ile tekrar sorulup gerçek sonuç alınır.
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '/config.php';
require __DIR__ . '/iyzico-client.php';

function respond($data, $httpCode = 200){
  http_response_code($httpCode);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

if(!iyzicoConfigured()){
  respond(['ok' => false, 'error' => 'Kartla ödeme aktif değil.'], 503);
}

$token = $_GET['token'] ?? '';
$token = is_string($token) ? trim($token) : '';
if($token === ''){
  respond(['ok' => false, 'error' => 'token eksik.'], 400);
}

try {
  $client = new IyzicoClient(IYZICO_API_KEY, IYZICO_SECRET_KEY, IYZICO_BASE_URL);
  $result = $client->retrieveCheckoutForm($token);
} catch (Exception $e){
  respond(['ok' => false, 'error' => 'iyzico\'ya ulaşılamadı, birazdan tekrar dene.'], 502);
}

$apiOk = ($result['status'] ?? '') === 'success';
$paid = $apiOk && ($result['paymentStatus'] ?? '') === 'SUCCESS';

if(!$paid){
  respond([
    'ok' => true,
    'paid' => false,
    'reason' => $result['errorMessage'] ?? 'Ödeme tamamlanmadı.',
  ]);
}

$items = [];
foreach(($result['itemTransactions'] ?? []) as $it){
  $items[] = [
    'name' => $it['itemId'] ?? null, // itemId burada bizim gönderdiğimiz basketItems[].id/name karşılığı değil, iyzico kendi alanlarını döner
    'price' => isset($it['price']) ? (float)$it['price'] : null,
    'paidPrice' => isset($it['paidPrice']) ? (float)$it['paidPrice'] : null,
  ];
}

respond([
  'ok' => true,
  'paid' => true,
  'paymentId' => $result['paymentId'] ?? null,
  'conversationId' => $result['conversationId'] ?? null,
  'basketId' => $result['basketId'] ?? null,
  'price' => isset($result['price']) ? (float)$result['price'] : null,
  'paidPrice' => isset($result['paidPrice']) ? (float)$result['paidPrice'] : null,
  'currency' => $result['currency'] ?? 'TRY',
  'installment' => $result['installment'] ?? 1,
  'cardAssociation' => $result['cardAssociation'] ?? null,
  'lastFourDigits' => $result['lastFourDigits'] ?? null,
  'items' => $items,
]);
