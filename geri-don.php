<?php
/**
 * iyzico'nun ödeme formunu tamamladıktan sonra POST ile geri döndüğü adres
 * (config.php > IYZICO_CALLBACK_URL). Burada ÖDEMENİN GERÇEKTEN başarılı
 * olup olmadığını doğrulamıyoruz — o kontrol durum.php'de, sunucudan
 * sunucuya (iyzico'ya tekrar sorarak) yapılır. Bu dosyanın tek işi,
 * müşterinin tarayıcısını POST'tan temiz bir GET sayfasına yönlendirmek
 * (Post-Redirect-Get deseni) — token'ı URL'ye taşıyoruz, gerçek doğrulama
 * "sipariş tamamlandı" sayfası açıldığında durum.php üzerinden yapılıyor.
 */

require __DIR__ . '/config.php';

$token = $_POST['token'] ?? ($_GET['token'] ?? '');
$token = is_string($token) ? trim($token) : '';

if($token === ''){
  header('Location: ' . SITE_ODEME_TAMAMLANDI_URL . '?durum=hata');
  exit;
}

header('Location: ' . SITE_ODEME_TAMAMLANDI_URL . '?token=' . urlencode($token));
exit;
