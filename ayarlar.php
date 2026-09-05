<?php
/* ---------------------------------------------------------------------------
   SONYA COLLECTION — doğrulama e-postası ayarları
   ---------------------------------------------------------------------------
   BURAYA KENDİ E-POSTA HESABININ BİLGİLERİNİ YAZ.

   Hostinger'da e-posta hesabı oluşturma:
     hPanel → E-postalar → E-posta Hesapları → "E-posta Hesabı Oluştur"
     Örnek: siparis@sonyacollection.com  (şifresini sen belirliyorsun)

   Hesabı oluşturduktan sonra aşağıdaki üç satırı doldurman yeterli.
   Diğer ayarlara dokunma.

   NOT: Bu dosyada şifre yazılı olduğu için sunucu dışına çıkarma, kimseye gönderme.
   --------------------------------------------------------------------------- */

return array(

  // --- DOLDURULACAK ÜÇ ALAN ---------------------------------------------
  'smtp_kullanici' => '',           // örn: 'siparis@sonyacollection.com'
  'smtp_sifre'     => '',           // o e-posta hesabının şifresi
  'gonderen_ad'    => 'Sonya Collection',

  // --- HOSTINGER İÇİN HAZIR AYARLAR (değiştirme) -------------------------
  'smtp_sunucu'    => 'smtp.hostinger.com',
  'smtp_port'      => 465,
  'smtp_guvenlik'  => 'ssl',        // 465 için 'ssl', 587 için 'tls'

  // --- DAVRANIŞ ----------------------------------------------------------
  'kod_gecerlilik_dk'   => 15,      // kod kaç dakika geçerli
  'saatte_azami_istek'  => 3,       // aynı e-postaya saatte en fazla kaç kod
  'ip_saatte_azami'     => 15,      // aynı bağlantıdan saatte en fazla kaç kod
  'azami_yanlis_deneme' => 5,       // kaç yanlış denemeden sonra kod iptal
  'site_adresi'         => 'https://sonyacollection.com',
  'iletisim_eposta'     => 'siparis@sonyacollection.com',
);
