<?php
/* ---------------------------------------------------------------------------
   SONYA COLLECTION — e-posta gönderimi (SMTP)
   ---------------------------------------------------------------------------
   Bu dosyayı DÜZENLEMEN GEREKMİYOR. Ayarlar ayarlar.php dosyasında.

   Dışarıdan hiçbir kütüphane kullanılmıyor; SMTP konuşması burada elle
   yapılıyor. Böylece sunucuya kurulum yapman gerekmiyor, dosyayı yükleyip
   ayarlar.php'yi doldurman yetiyor.
   --------------------------------------------------------------------------- */

if (!defined('SONYA_DOGRULAMA')) { http_response_code(403); exit; }

/**
 * Basit SMTP istemcisi. Başarılıysa true, değilse false döner;
 * hata metni $hata değişkenine yazılır (sadece sunucu günlüğü için).
 */
function sonya_smtp_gonder($ayar, $alici, $konu, $htmlGovde, $duzMetin, &$hata = null)
{
    $kullanici = trim((string)$ayar['smtp_kullanici']);
    $sifre     = (string)$ayar['smtp_sifre'];
    $sunucu    = trim((string)$ayar['smtp_sunucu']);
    $port      = (int)$ayar['smtp_port'];
    $guvenlik  = strtolower(trim((string)$ayar['smtp_guvenlik']));

    $sinir  = '=_sonya_' . bin2hex(random_bytes(12));
    $baslik = sonya_eposta_basliklari($ayar, $kullanici, $alici, $konu, $sinir);
    $govde  = sonya_eposta_govdesi($sinir, $htmlGovde, $duzMetin);

    // SMTP ayarı doldurulmamışsa sunucunun kendi posta işlevine düşülür.
    if ($kullanici === '' || $sunucu === '') {
        $ekBaslik = "MIME-Version: 1.0\r\n"
            . 'Content-Type: multipart/alternative; boundary="' . $sinir . "\"\r\n"
            . 'From: ' . sonya_mime_ad($ayar['gonderen_ad']) . ' <' . ($kullanici !== '' ? $kullanici : ('noreply@' . sonya_alan_adi($ayar))) . ">\r\n";
        $ok = @mail($alici, sonya_mime_konu($konu), $govde, $ekBaslik);
        if (!$ok) { $hata = 'mail() basarisiz'; }
        return (bool)$ok;
    }

    $adres = ($guvenlik === 'ssl') ? ('ssl://' . $sunucu) : $sunucu;
    $baglanti = @stream_socket_client($adres . ':' . $port, $eno, $estr, 15,
        STREAM_CLIENT_CONNECT, stream_context_create(array('ssl' => array(
            'verify_peer' => true, 'verify_peer_name' => true,
        ))));
    if (!$baglanti) { $hata = 'baglanti: ' . $estr; return false; }
    stream_set_timeout($baglanti, 15);

    $oku = function () use ($baglanti) {
        $cevap = '';
        while (($satir = fgets($baglanti, 1024)) !== false) {
            $cevap .= $satir;
            if (strlen($satir) < 4 || $satir[3] !== '-') { break; }
        }
        return $cevap;
    };
    $yaz = function ($komut) use ($baglanti) { fwrite($baglanti, $komut . "\r\n"); };
    $kod = function ($cevap) { return (int)substr(trim($cevap), 0, 3); };

    if ($kod($oku()) !== 220) { $hata = 'karsilama'; fclose($baglanti); return false; }

    $yaz('EHLO ' . sonya_alan_adi($ayar));
    $ehlo = $oku();
    if ($kod($ehlo) !== 250) { $hata = 'ehlo'; fclose($baglanti); return false; }

    if ($guvenlik === 'tls') {
        $yaz('STARTTLS');
        if ($kod($oku()) !== 220) { $hata = 'starttls'; fclose($baglanti); return false; }
        if (!@stream_socket_enable_crypto($baglanti, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $hata = 'tls kurulamadi'; fclose($baglanti); return false;
        }
        $yaz('EHLO ' . sonya_alan_adi($ayar));
        if ($kod($oku()) !== 250) { $hata = 'ehlo2'; fclose($baglanti); return false; }
    }

    $yaz('AUTH LOGIN');
    if ($kod($oku()) !== 334) { $hata = 'auth'; fclose($baglanti); return false; }
    $yaz(base64_encode($kullanici));
    if ($kod($oku()) !== 334) { $hata = 'auth-kullanici'; fclose($baglanti); return false; }
    $yaz(base64_encode($sifre));
    if ($kod($oku()) !== 235) { $hata = 'auth-sifre (kullanici adi/sifre hatali olabilir)'; fclose($baglanti); return false; }

    $yaz('MAIL FROM:<' . $kullanici . '>');
    if ($kod($oku()) !== 250) { $hata = 'mail from'; fclose($baglanti); return false; }
    $yaz('RCPT TO:<' . $alici . '>');
    $rc = $kod($oku());
    if ($rc !== 250 && $rc !== 251) { $hata = 'rcpt to'; fclose($baglanti); return false; }

    $yaz('DATA');
    if ($kod($oku()) !== 354) { $hata = 'data'; fclose($baglanti); return false; }

    // Gövde base64 ile kodlandığı için satır uzunluğu / nokta kaçışı sorunu olmuyor.
    fwrite($baglanti, $baslik . "\r\n" . $govde . "\r\n.\r\n");
    if ($kod($oku()) !== 250) { $hata = 'gonderim reddedildi'; fclose($baglanti); return false; }

    $yaz('QUIT');
    @fclose($baglanti);
    return true;
}

function sonya_alan_adi($ayar)
{
    $host = parse_url((string)$ayar['site_adresi'], PHP_URL_HOST);
    return $host ? $host : 'sonyacollection.com';
}

function sonya_mime_ad($ad)
{
    return '=?UTF-8?B?' . base64_encode((string)$ad) . '?=';
}

function sonya_mime_konu($konu)
{
    return '=?UTF-8?B?' . base64_encode((string)$konu) . '?=';
}

function sonya_eposta_basliklari($ayar, $gonderen, $alici, $konu, $sinir)
{
    $gonderenAdres = $gonderen !== '' ? $gonderen : ('noreply@' . sonya_alan_adi($ayar));
    $satirlar = array(
        'Date: ' . date('r'),
        'From: ' . sonya_mime_ad($ayar['gonderen_ad']) . ' <' . $gonderenAdres . '>',
        'To: <' . $alici . '>',
        'Subject: ' . sonya_mime_konu($konu),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . sonya_alan_adi($ayar) . '>',
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $sinir . '"',
        'X-Mailer: Sonya Collection',
    );
    if (!empty($ayar['iletisim_eposta'])) {
        $satirlar[] = 'Reply-To: <' . $ayar['iletisim_eposta'] . '>';
    }
    return implode("\r\n", $satirlar) . "\r\n";
}

function sonya_eposta_govdesi($sinir, $html, $duz)
{
    $p = '';
    $p .= '--' . $sinir . "\r\n";
    $p .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $p .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $p .= chunk_split(base64_encode($duz), 76, "\r\n");
    $p .= '--' . $sinir . "\r\n";
    $p .= "Content-Type: text/html; charset=UTF-8\r\n";
    $p .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $p .= chunk_split(base64_encode($html), 76, "\r\n");
    $p .= '--' . $sinir . "--\r\n";
    return $p;
}

/**
 * Doğrulama kodu e-postasının Sonya Collection tasarımındaki HTML gövdesi.
 * Dış görsel kullanılmıyor: e-posta programlarının çoğu dış görselleri
 * engelliyor ve o durumda e-posta kırık görünüyor.
 */
function sonya_dogrulama_epostasi_html($ayar, $kod, $dakika)
{
    $site     = htmlspecialchars((string)$ayar['site_adresi'], ENT_QUOTES, 'UTF-8');
    $iletisim = htmlspecialchars((string)$ayar['iletisim_eposta'], ENT_QUOTES, 'UTF-8');
    $kodHtml  = htmlspecialchars((string)$kod, ENT_QUOTES, 'UTF-8');
    $dk       = (int)$dakika;

    return <<<HTML
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FBF7F1;padding:32px 12px;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
<tr><td align="center">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:520px;background-color:#FFFEFB;border:1px solid #EEE3D1;border-radius:18px;">
  <tr><td align="center" style="padding:34px 32px 0 32px;">
    <div style="font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#6E5744;letter-spacing:0.5px;line-height:1;">Sonya</div>
    <div style="font-size:10px;letter-spacing:5px;color:#946C44;margin-top:5px;text-transform:uppercase;">Collection</div>
  </td></tr>
  <tr><td style="padding:28px 32px 0 32px;">
    <h1 style="margin:0 0 14px 0;font-family:Georgia,'Times New Roman',serif;font-size:22px;font-weight:500;color:#6E5744;text-align:center;">Hoş geldin!</h1>
    <p style="margin:0;font-size:15px;line-height:1.65;color:#6E5744;text-align:center;">
      İlk siparişine özel indirim kodunu görebilmen için, sitedeki pencereye aşağıdaki doğrulama kodunu yazman yeterli.
    </p>
  </td></tr>
  <tr><td align="center" style="padding:26px 32px 0 32px;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:320px;">
      <tr><td align="center" bgcolor="#FBF7F1" style="border:1px solid #EEE3D1;border-radius:14px;padding:20px 12px;">
        <div style="font-size:11px;letter-spacing:2.5px;color:#946C44;text-transform:uppercase;margin-bottom:10px;">Doğrulama Kodun</div>
        <div style="font-family:Georgia,'Times New Roman',serif;font-size:34px;font-weight:600;letter-spacing:9px;color:#6E5744;line-height:1;">$kodHtml</div>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="padding:16px 32px 0 32px;">
    <p style="margin:0;font-size:13px;line-height:1.6;color:#6E5744;opacity:0.8;text-align:center;">
      Kod <b>$dk dakika</b> geçerli. Süresi dolarsa siteden yeni bir kod isteyebilirsin.
    </p>
  </td></tr>
  <tr><td style="padding:24px 32px 0 32px;">
    <div style="height:1px;background-color:#EEE3D1;line-height:1px;font-size:0;">&nbsp;</div>
  </td></tr>
  <tr><td style="padding:18px 32px 30px 32px;">
    <p style="margin:0 0 8px 0;font-size:12px;line-height:1.6;color:#6E5744;opacity:0.7;text-align:center;">
      Bu kodu sen istemediysen bu e-postayı görmezden gelebilirsin; hiçbir işlem yapılmaz.
    </p>
    <p style="margin:0;font-size:12px;line-height:1.6;color:#6E5744;opacity:0.7;text-align:center;">
      Sorularınız için: <a href="mailto:$iletisim" style="color:#7E5E3A;text-decoration:none;">$iletisim</a><br>
      <a href="$site" style="color:#7E5E3A;text-decoration:none;">sonyacollection.com</a>
    </p>
  </td></tr>
</table>
</td></tr>
</table>
HTML;
}

function sonya_dogrulama_epostasi_duz($ayar, $kod, $dakika)
{
    return "Sonya Collection\n\n"
        . "Hos geldin!\n\n"
        . "Ilk siparisine ozel indirim kodunu gorebilmen icin sitedeki pencereye "
        . "asagidaki dogrulama kodunu yaz:\n\n"
        . "    " . $kod . "\n\n"
        . "Kod " . (int)$dakika . " dakika gecerli.\n\n"
        . "Bu kodu sen istemediysen bu e-postayi gormezden gelebilirsin.\n\n"
        . $ayar['site_adresi'] . "\n";
}
