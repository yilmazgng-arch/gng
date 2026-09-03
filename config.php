<?php
/**
 * Sonya Collection — iyzico kartla ödeme ayarları
 * ------------------------------------------------
 * iyzico Merchant Panel'den (https://merchant.iyzipay.com veya sandbox için
 * https://sandbox-merchant.iyzipay.com) aldığınız API Key ve Secret Key
 * değerlerini aşağıya yapıştırın. Değerler "BURAYA_" ile başladığı sürece
 * kartla ödeme özelliği sitede sessizce kapalı kalır — hiçbir şeyi bozmaz,
 * "Kartla Öde" butonuna basan müşteriye nazik bir "yakında aktif olacak"
 * mesajı gösterilir.
 *
 * Sandbox (test) modunda geliştirme/deneme yapmak için iyzico başvurunuz
 * onaylanmadan önce de sandbox-... ile başlayan test anahtarlarını
 * kullanabilirsiniz (Merchant Panel > Ayarlar > API Anahtarları, sandbox
 * hesabınızdan). Gerçek (canlı) ödeme almaya başladığınızda IYZICO_BASE_URL
 * değerini production adresiyle değiştirmeyi unutmayın.
 */

define('IYZICO_API_KEY', 'BURAYA_IYZICO_API_KEY');
define('IYZICO_SECRET_KEY', 'BURAYA_IYZICO_SECRET_KEY');

// Test ederken: 'https://sandbox-api.iyzipay.com'
// Canlıya geçince: 'https://api.iyzipay.com'
define('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com');

// Müşteri kartla ödemeyi tamamladıktan sonra iyzico'nun geri yönlendireceği adres.
// Bu, aşağıdaki geri-don.php dosyasının canlıdaki tam adresi olmalı.
define('IYZICO_CALLBACK_URL', 'https://sonyacollection.com/odeme/geri-don.php');

// Ödeme onaylandıktan sonra müşterinin yönlendirileceği "sipariş tamamlandı" sayfası.
define('SITE_ODEME_TAMAMLANDI_URL', 'https://sonyacollection.com/odeme-tamamlandi.html');

// Firestore projesi — ürün fiyatlarını ve kupon kodlarını güvenli şekilde
// (müşteri tarayıcısından gelen tutara güvenmeden) doğrulamak için kullanılır.
// Bu, panelde/diğer sayfalarda kullanılan firebaseConfig.projectId ile aynı olmalı.
define('FIRESTORE_PROJECT_ID', 'sonyacollection-62544');

function iyzicoConfigured(){
  return strpos(IYZICO_API_KEY, 'BURAYA_') !== 0 && strpos(IYZICO_SECRET_KEY, 'BURAYA_') !== 0;
}
