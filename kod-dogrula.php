<?php
/* ---------------------------------------------------------------------------
   SONYA COLLECTION — girilen doğrulama kodunun kontrolü
   ---------------------------------------------------------------------------
   Bu dosyayı DÜZENLEMEN GEREKMİYOR.
   --------------------------------------------------------------------------- */

define('SONYA_DOGRULAMA', true);
require __DIR__ . '/depo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sonya_cevap(array('ok' => false, 'hata' => 'yontem'), 405);
}

$ayar = require __DIR__ . '/ayarlar.php';

$istek  = sonya_istek_verisi();
$eposta = sonya_gecerli_eposta(isset($istek['eposta']) ? $istek['eposta'] : '');
$kod    = preg_replace('/\D+/', '', (string)(isset($istek['kod']) ? $istek['kod'] : ''));

if ($eposta === null || strlen($kod) !== 6) {
    sonya_cevap(array('ok' => false, 'hata' => 'eksik'), 400);
}

$yol   = sonya_kayit_yolu($eposta);
$kayit = sonya_kayit_oku($yol);

if (empty($kayit['kod'])) {
    sonya_cevap(array('ok' => false, 'hata' => 'kod-yok'), 400);
}
if (time() > (int)$kayit['bitis']) {
    @unlink($yol);
    sonya_cevap(array('ok' => false, 'hata' => 'suresi-doldu'), 400);
}
if ((int)$kayit['deneme'] >= (int)$ayar['azami_yanlis_deneme']) {
    @unlink($yol);
    sonya_cevap(array('ok' => false, 'hata' => 'cok-deneme'), 429);
}

// hash_equals: kodun doğruluğu karşılaştırılırken süre sızıntısı olmasın diye.
if (!hash_equals((string)$kayit['kod'], sonya_kod_karmasi($kod, $eposta))) {
    $kayit['deneme'] = (int)$kayit['deneme'] + 1;
    sonya_kayit_yaz($yol, $kayit);
    $kalan = max(0, (int)$ayar['azami_yanlis_deneme'] - (int)$kayit['deneme']);
    sonya_cevap(array('ok' => false, 'hata' => 'yanlis-kod', 'kalan' => $kalan), 400);
}

// Doğru: kayıt tek kullanımlık olduğu için hemen siliniyor.
@unlink($yol);
sonya_cevap(array('ok' => true, 'eposta' => $eposta));
