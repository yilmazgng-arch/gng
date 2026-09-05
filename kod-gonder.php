<?php
/* ---------------------------------------------------------------------------
   SONYA COLLECTION — "bana doğrulama kodu gönder" isteği
   ---------------------------------------------------------------------------
   Bu dosyayı DÜZENLEMEN GEREKMİYOR. Ayarlar ayarlar.php dosyasında.
   --------------------------------------------------------------------------- */

define('SONYA_DOGRULAMA', true);
require __DIR__ . '/depo.php';
require __DIR__ . '/eposta.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sonya_cevap(array('ok' => false, 'hata' => 'yontem'), 405);
}

$ayar = require __DIR__ . '/ayarlar.php';
sonya_eski_kayitlari_temizle();

$istek  = sonya_istek_verisi();
$eposta = sonya_gecerli_eposta(isset($istek['eposta']) ? $istek['eposta'] : '');
if ($eposta === null) {
    sonya_cevap(array('ok' => false, 'hata' => 'eposta'), 400);
}

// --- Aynı bağlantıdan gelen aşırı istekler ---------------------------------
$ipYolu = sonya_ip_yolu(sonya_istemci_ip());
$ipKayit = sonya_kayit_oku($ipYolu);
$ipGonderim = sonya_son_saat(isset($ipKayit['gonderim']) ? $ipKayit['gonderim'] : array());
if (count($ipGonderim) >= (int)$ayar['ip_saatte_azami']) {
    sonya_cevap(array('ok' => false, 'hata' => 'cok-istek'), 429);
}

// --- Aynı e-postaya aşırı istek --------------------------------------------
$yol   = sonya_kayit_yolu($eposta);
$kayit = sonya_kayit_oku($yol);
$gonderim = sonya_son_saat(isset($kayit['gonderim']) ? $kayit['gonderim'] : array());
if (count($gonderim) >= (int)$ayar['saatte_azami_istek']) {
    sonya_cevap(array('ok' => false, 'hata' => 'cok-istek'), 429);
}

// --- Kodu üret ve gönder ----------------------------------------------------
$kod     = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$dakika  = max(1, (int)$ayar['kod_gecerlilik_dk']);
$konu    = 'Doğrulama kodun: ' . $kod . ' — Sonya Collection';
$html    = sonya_dogrulama_epostasi_html($ayar, $kod, $dakika);
$duz     = sonya_dogrulama_epostasi_duz($ayar, $kod, $dakika);

$hata = null;
$gonderildi = sonya_smtp_gonder($ayar, $eposta, $konu, $html, $duz, $hata);

if (!$gonderildi) {
    error_log('[sonya-dogrulama] e-posta gonderilemedi: ' . (string)$hata);
    sonya_cevap(array('ok' => false, 'hata' => 'gonderilemedi'), 502);
}

// --- Kaydı yaz --------------------------------------------------------------
$gonderim[] = time();
sonya_kayit_yaz($yol, array(
    'kod'    => sonya_kod_karmasi($kod, $eposta),
    'bitis'  => time() + ($dakika * 60),
    'deneme' => 0,
    'gonderim' => $gonderim,
));

$ipGonderim[] = time();
sonya_kayit_yaz($ipYolu, array('gonderim' => $ipGonderim));

sonya_cevap(array('ok' => true, 'dakika' => $dakika));
