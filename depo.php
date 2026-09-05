<?php
/* ---------------------------------------------------------------------------
   SONYA COLLECTION — doğrulama kodlarının geçici saklanması
   ---------------------------------------------------------------------------
   Bu dosyayı DÜZENLEMEN GEREKMİYOR.

   Kodlar veritabanı gerektirmeden "kayitlar" klasöründe küçük dosyalar hâlinde
   tutuluyor. Dosya adı e-posta adresinin karışık hâli; kodun kendisi de
   şifrelenmiş olarak yazılıyor, düz metin olarak hiçbir yerde durmuyor.
   Süresi dolan kayıtlar kendiliğinden siliniyor.

   Kayıt dosyalarının uzantısı .php ve ilk satırı "çık" komutu: biri adresi
   tarayıcıya yazsa bile içerik görünmez, boş sayfa döner. Böylece klasördeki
   .htaccess yüklenmeyi atlasa dahi bilgi dışarı sızmaz.
   --------------------------------------------------------------------------- */

if (!defined('SONYA_DOGRULAMA')) { http_response_code(403); exit; }

function sonya_kayit_klasoru()
{
    $dizin = __DIR__ . '/kayitlar';
    if (!is_dir($dizin)) { @mkdir($dizin, 0700, true); }
    return $dizin;
}

define('SONYA_DOSYA_BASI', "<?php exit; ?>\n");

function sonya_dosya_oku_ham($yol)
{
    if (!is_file($yol)) { return ''; }
    $ham = (string)@file_get_contents($yol);
    if (strpos($ham, SONYA_DOSYA_BASI) === 0) {
        $ham = substr($ham, strlen(SONYA_DOSYA_BASI));
    }
    return $ham;
}

function sonya_dosya_yaz_ham($yol, $icerik)
{
    @file_put_contents($yol, SONYA_DOSYA_BASI . $icerik, LOCK_EX);
    @chmod($yol, 0600);
}

function sonya_gizli_anahtar()
{
    // Sunucuya özel, tahmin edilemez bir anahtar; ilk çalışmada bir kez üretilir.
    $yol = sonya_kayit_klasoru() . '/anahtar.php';
    $icerik = trim(sonya_dosya_oku_ham($yol));
    if ($icerik !== '') { return $icerik; }
    $yeni = bin2hex(random_bytes(32));
    sonya_dosya_yaz_ham($yol, $yeni);
    return $yeni;
}

function sonya_eposta_anahtari($eposta)
{
    return hash('sha256', mb_strtolower(trim($eposta), 'UTF-8') . '|' . sonya_gizli_anahtar());
}

function sonya_kayit_yolu($eposta)
{
    return sonya_kayit_klasoru() . '/e_' . sonya_eposta_anahtari($eposta) . '.php';
}

function sonya_ip_yolu($ip)
{
    return sonya_kayit_klasoru() . '/i_' . hash('sha256', $ip . '|' . sonya_gizli_anahtar()) . '.php';
}

function sonya_kayit_oku($yol)
{
    $ham = sonya_dosya_oku_ham($yol);
    if ($ham === '') { return array(); }
    $veri = json_decode($ham, true);
    return is_array($veri) ? $veri : array();
}

function sonya_kayit_yaz($yol, $veri)
{
    sonya_dosya_yaz_ham($yol, json_encode($veri));
}

function sonya_kod_karmasi($kod, $eposta)
{
    return hash('sha256', $kod . '|' . mb_strtolower(trim($eposta), 'UTF-8') . '|' . sonya_gizli_anahtar());
}

/** Süresi geçmiş kayıtları ara sıra temizler (her ~20 istekte bir). */
function sonya_eski_kayitlari_temizle()
{
    if (random_int(1, 20) !== 1) { return; }
    $dizin = sonya_kayit_klasoru();
    $liste = @scandir($dizin);
    if (!is_array($liste)) { return; }
    $sinir = time() - 7200; // 2 saat
    foreach ($liste as $ad) {
        if ($ad === '.' || $ad === '..' || $ad === 'anahtar.php' || $ad === '.htaccess' || $ad === 'README.txt') { continue; }
        $yol = $dizin . '/' . $ad;
        if (is_file($yol) && @filemtime($yol) < $sinir) { @unlink($yol); }
    }
}

function sonya_istemci_ip()
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/** Son bir saat içindeki zaman damgalarını süzer. */
function sonya_son_saat($damgalar)
{
    $sinir = time() - 3600;
    $temiz = array();
    foreach ((array)$damgalar as $t) {
        $t = (int)$t;
        if ($t > $sinir) { $temiz[] = $t; }
    }
    return $temiz;
}

/** JSON cevabı döndürüp çıkar. */
function sonya_cevap($veri, $durum = 200)
{
    http_response_code($durum);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($veri, JSON_UNESCAPED_UNICODE);
    exit;
}

/** İstek gövdesindeki JSON'u okur. */
function sonya_istek_verisi()
{
    $ham = file_get_contents('php://input');
    $veri = json_decode((string)$ham, true);
    return is_array($veri) ? $veri : array();
}

function sonya_gecerli_eposta($eposta)
{
    $eposta = trim((string)$eposta);
    if ($eposta === '' || strlen($eposta) > 190) { return null; }
    if (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) { return null; }
    return $eposta;
}
