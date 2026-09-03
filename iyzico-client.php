<?php
/**
 * Minimal iyzico REST istemcisi — sadece "Checkout Form" (ödeme formu) için
 * initialize ve retrieve (sonucu doğrulama) uç noktalarını kullanır. iyzico'nun
 * resmi SDK'sı yerine bunu yazdık çünkü bu sunucuda Composer/SSH erişimi her
 * Hostinger paylaşımlı hosting planında bulunmuyor olabilir — bu dosya tek
 * başına, hiçbir bağımlılık olmadan çalışır, sadece PHP'nin kendi cURL ve
 * hash_hmac fonksiyonlarını kullanır.
 *
 * Kimlik doğrulama: iyzico'nun güncel "IYZWSv2" (HMAC-SHA256) imzalama şeması.
 * Belgeler: https://docs.iyzico.com/en/getting-started/preliminaries/authentication/hmacsha256-auth
 */

class IyzicoClient {
  private $apiKey;
  private $secretKey;
  private $baseUrl;

  public function __construct($apiKey, $secretKey, $baseUrl){
    $this->apiKey = $apiKey;
    $this->secretKey = $secretKey;
    $this->baseUrl = rtrim($baseUrl, '/');
  }

  private function randomKey(){
    return (string) round(microtime(true) * 1000) . '-' . bin2hex(random_bytes(8));
  }

  private function authHeader($uriPath, $bodyJson, $randomKey){
    $dataToSign = $randomKey . $uriPath . $bodyJson;
    $signature = hash_hmac('sha256', $dataToSign, $this->secretKey);
    $authStr = 'apiKey:' . $this->apiKey . '&randomKey:' . $randomKey . '&signature:' . $signature;
    return 'IYZWSv2 ' . base64_encode($authStr);
  }

  private function post($uriPath, $payload){
    $bodyJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $randomKey = $this->randomKey();
    $headers = [
      'Authorization: ' . $this->authHeader($uriPath, $bodyJson, $randomKey),
      'x-iyzi-rnd: ' . $randomKey,
      'Content-Type: application/json',
      'Accept: application/json',
    ];
    $ch = curl_init($this->baseUrl . $uriPath);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $bodyJson,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_TIMEOUT => 20,
    ]);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if($response === false){ throw new Exception('iyzico bağlantı hatası: ' . $err); }
    $decoded = json_decode($response, true);
    if($decoded === null){ throw new Exception('iyzico yanıtı okunamadı (HTTP ' . $status . ').'); }
    return $decoded;
  }

  /** Ödeme formu oturumu başlatır. $payload iyzico'nun CF-Initialize şemasına uygun olmalı. */
  public function initializeCheckoutForm($payload){
    return $this->post('/payment/iyzipos/checkoutform/initialize/auth/ecom', $payload);
  }

  /** Ödeme tamamlandıktan sonra sonucu SUNUCU TARAFINDA doğrular — asla atlanmamalı. */
  public function retrieveCheckoutForm($token, $conversationId = null){
    $payload = ['locale' => 'tr', 'token' => $token];
    if($conversationId) $payload['conversationId'] = $conversationId;
    return $this->post('/payment/iyzipos/checkoutform/auth/ecom/detail', $payload);
  }
}
