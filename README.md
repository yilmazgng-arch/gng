# Sonya Collection — Hostinger'a Yükleme Rehberi

## Elinizdeki dosyalar

**`sonyacollection.com`'un kök dizinine (`public_html`) yüklenecekler:**

- **sonyacollection-com-index.html** → ana site, adını **`index.html`** olarak değiştirerek yükleyin.
- **yeni-sezon.html, indirim.html, pelerinler.html, kimonolar.html, takimlar.html, elbiseler.html, hesabim.html** → sitenin diğer sayfaları, adlarını değiştirmeden aynen yükleyin (üst menüdeki bağlantılar bu dosya adlarını arar).
- **manifest.webmanifest, sw.js, icon-192.png, icon-512.png, icon-512-maskable.png, apple-touch-icon.png** → siteyi telefona/bilgisayara "uygulama gibi" yükleyebilme (PWA) özelliği için gereken dosyalar. Adlarını değiştirmeden, diğer sayfalarla aynı kök dizine yükleyin.
- **.htaccess** → küçük bir sunucu ayarı dosyası (aşağıda "küçük bir sunucu ayarı düzeltmesi" bölümünde anlatılıyor). Gizli bir dosya olduğu için Dosya Yöneticisi'nde göstermeniz gerekebilir.
- **odeme-tamamlandi.html** → kartla ödeme sonrası "sipariş tamamlandı" sayfası, adını değiştirmeden yükleyin (aşağıda "kartla ödeme (iyzico) entegrasyonu" bölümünde anlatılıyor — kurulum gerektirir, henüz siteye eklemeseniz de site bozulmaz).
- **odeme/** klasörü → kartla ödemenin sunucu tarafı dosyaları, klasör olarak (içindeki dosya adlarını değiştirmeden) yükleyin.

**`panel.sonyacollection.com` alt alan adının kök dizinine yüklenecek:**

- **panel-sonyacollection-com-index.html** → yönetici paneli, adını **`index.html`** olarak değiştirerek yükleyin.

Sayfaların hepsi tek parça HTML dosyası (fotoğraflar dosyanın içine gömülü) — npm/build gibi bir kurulum gerekmiyor, sadece dosyaları olduğu gibi yüklemeniz yeterli.

## Adım 1 — Alan adının Hostinger'a bağlı olduğundan emin olun

`sonyacollection.com` Hostinger üzerinden mi satın alındı, yoksa başka bir sağlayıcıdan mı (GoDaddy, Turhost, vb.)?

- Hostinger'dan alındıysa: genelde otomatik bağlıdır, bir şey yapmanıza gerek yok.
- Başka bir yerden alındıysa: o sağlayıcının panelinde alan adının **nameserver**'larını Hostinger'ın verdiği nameserver adresleriyle değiştirmeniz gerekir (hPanel > Domainler kısmında bu adresler gösterilir). Bu değişikliğin etkili olması birkaç saat sürebilir.

## Adım 2 — Yönetici paneli için alt alan adı oluşturun

1. **hPanel**'e giriş yapın.
2. **Web Siteleri** (veya Domainler) bölümünden `sonyacollection.com`'u seçin.
3. Sol menüden **Alt Alan Adları** (Subdomains) sekmesine girin.
4. Alt alan adı olarak `panel` yazıp oluşturun. Hostinger otomatik olarak `public_html/panel` gibi ayrı bir klasör açacaktır — tam adı `panel.sonyacollection.com` olur.

## Adım 3 — Dosyaları yükleyin

1. **hPanel > Dosyalar > Dosya Yöneticisi** (File Manager) açın.
2. `public_html` klasörüne girin:
   - Varsa mevcut `index.html`'i silin veya yedekleyin.
   - `sonyacollection-com-index.html` dosyasını buraya yükleyin, adını **`index.html`** olarak değiştirin.
   - `yeni-sezon.html, indirim.html, pelerinler.html, kimonolar.html, takimlar.html, elbiseler.html, hesabim.html` dosyalarını da aynı klasöre, adlarını değiştirmeden yükleyin.
   - `manifest.webmanifest, sw.js, icon-192.png, icon-512.png, icon-512-maskable.png, apple-touch-icon.png` dosyalarını da aynı klasöre, adlarını değiştirmeden yükleyin.
   - `.htaccess` dosyasını da aynı klasöre yükleyin — nokta ile başladığı için Dosya Yöneticisi'nde görünmeyebilir, sağ üstteki ayarlar menüsünden "Gizli dosyaları göster"ü açmanız gerekebilir. Eğer `public_html`'de zaten bir `.htaccess` varsa, üzerine yazmadan önce bana haber verin, mevcut içerikle birleştirelim.
   - `odeme-tamamlandi.html` dosyasını da aynı klasöre, adını değiştirmeden yükleyin.
   - `odeme` klasörünü de olduğu gibi (içindeki dosya adlarını değiştirmeden) bu klasöre yükleyin — kartla ödeme kurulumunu tamamlayana kadar bu klasörün orada durması siteyi etkilemez, sadece kartla ödemeyi kurmaya hazır hale getirir.
3. `public_html/panel` klasörüne girin:
   - `panel-sonyacollection-com-index.html` dosyasını buraya yükleyin, adını **`index.html`** olarak değiştirin.

## Adım 4 — SSL'i kontrol edin

1. **hPanel > Güvenlik > SSL** bölümüne gidin.
2. Hem `sonyacollection.com` hem `panel.sonyacollection.com` için ücretsiz SSL sertifikasının aktif (yeşil/"Active") olduğunu kontrol edin — Hostinger genelde otomatik sağlar, alt alan adı yeni oluşturulduysa birkaç dakika-birkaç saat sürebilir.

## Adım 5 — Test edin

- `https://sonyacollection.com` → site açılmalı.
- `https://panel.sonyacollection.com` → yönetici paneli giriş ekranı açılmalı. Daha önce oluşturduğunuz yönetici hesabıyla giriş yapabilmelisiniz — bu adres zaten Firebase'in Authorized Domains listesine eklendiği için giriş burada çalışacaktır (claude.ai önizleme bağlantısında çalışmıyordu, bu normaldi).

## Önemli — Firestore Security Rules güncellendi

Bu turda sipariş durumu takibi eklendiği için `firestore-rules.txt` dosyası da güncellendi (yönetici artık bir siparişin sadece durumunu/notunu değiştirebiliyor, tutarını/ürünlerini asla değiştiremiyor). Firebase Console'da **Firestore Database > Rules** sekmesine gidip mevcut kuralların üzerine `firestore-rules.txt` içeriğini yapıştırıp **Yayınla**'ya basmanız gerekiyor — aksi halde yönetici panelindeki yeni "Siparişler" sekmesinden durum değiştirmek çalışmaz.

## Önemli — Ürün fotoğrafları artık Cloudinary'de

Yönetici panelinde bundan sonra eklenen ürün fotoğrafları artık Firestore'a gömülmüyor, senin oluşturduğun Cloudinary hesabına (`gcuvsq2h`, `ml_default` yükleme ön ayarı) yükleniyor — site daha hızlı açılıyor. Panelin **Ayarlar** sekmesinin en altında **"Eski Fotoğrafları Cloudinary'ye Taşı"** diye bir buton var: bu buton, Cloudinary'den önce eklenmiş ürünlerin fotoğraflarını tek seferde Cloudinary'ye taşır. Paneli yeni dosyayla güncelledikten sonra bir kere bu butona basmanı öneririm — ürünlerin görünümünde hiçbir değişiklik olmaz, sadece arka plandaki depolama yöntemi değişir.

## Önemli — küçük bir sunucu ayarı düzeltmesi (.htaccess)

Site üzerinde genel bir kontrol yaparken şunu fark ettim: `manifest.webmanifest` dosyası Hostinger'ın varsayılan ayarlarıyla "text/plain" olarak sunuluyor (doğrusu `application/manifest+json` + UTF-8 olmalı). Sitenin kendisinde ve sepette bir sorun yaratmıyor, ama bazı tarayıcı denetimlerinde ve "Ana Ekrana Ekle" açıklamasında Türkçe karakterlerin bozuk görünmesine yol açabiliyor. Ekteki **`sonya-htaccess-duzeltme.zip`** içindeki `.htaccess` dosyasını `public_html` klasörünün köküne (diğer dosyalarla aynı yere) yükleyin:

1. hPanel > Dosyalar > Dosya Yöneticisi'ni açın, `public_html`'e girin.
2. Sağ üstteki ayarlar/görünüm menüsünden **"Gizli dosyaları göster"** seçeneğini açın (`.htaccess` bir nokta ile başladığı için varsayılan olarak gizlidir).
3. `sonya-htaccess-duzeltme.zip` içindeki `.htaccess` dosyasını yükleyin. Eğer `public_html`'de zaten bir `.htaccess` dosyası varsa, üzerine yazmadan önce bana söyleyin — mevcut içeriğiyle birleştirmemiz gerekir.

Bu adım isteğe bağlıdır, siteyi bozmaz, sadece küçük bir mükemmelleştirme. (Daha önce ayrı bir `sonya-htaccess-duzeltme.zip` olarak gönderdiysem ve zaten yüklediyseniz, bu turda tekrar yapmanıza gerek yok — dosya bu ana zip'e de eklendi, sadece kolaylık olsun diye.)

## Önemli — sayfa hızı ince ayarları

Bu turda 8 mağaza sayfasının tamamı tekrar değişti (aşağıdaki iki iyileştirme için), bu yüzden hepsini yeniden yüklemeniz gerekiyor:

- Ürün kartlarının fotoğrafları artık **yalnızca ekrana yaklaştıkça** yükleniyor (lazy loading) — özellikle çok ürünlü kategori sayfalarında ilk açılışta onlarca fotoğrafın aynı anda indirilmesini önlüyor, sayfa daha çabuk kullanılabilir hale geliyor.
- Firebase ve Cloudinary sunucularına önceden bağlantı kuruluyor (`preconnect`) — veri/fotoğraf istekleri birkaç yüz milisaniye daha hızlı başlıyor.
- `.htaccess` dosyasındaki önbellekleme ayarları sayesinde ikonlar/fotoğraflar tekrar eden ziyaretlerde yeniden indirilmiyor.

Görsel olarak hiçbir şey değişmedi, sadece arka planda daha hızlı çalışıyor. Ayrıca panelinizdeki **"Eski Fotoğrafları Cloudinary'ye Taşı"** butonuna henüz basmadıysanız basmanızı öneririm (Ayarlar sekmesi) — hâlâ eski (Cloudinary öncesi) fotoğrafı olan ürünler varsa, bunlar hâlâ büyük dosyalar olarak yükleniyor demektir.

## Yeni sipariş e-posta bildirimi — KURULDU ve AKTİF

Artık bir müşteri WhatsApp veya Instagram üzerinden sipariş verdiğinde, sipariş Firestore'a kaydedildikten hemen sonra otomatik olarak **siparis@sonyacollection.com** adresine bir bildirim e-postası gidiyor (EmailJS + Hostinger'dan aldığınız kendi mail hesabınız üzerinden). Şablonda ürünler, tutar, kupon, müşteri bilgisi ve varsa hediye notu yer alıyor. Ayda 200 e-postaya kadar ücretsiz (küçük bir mağaza için fazlasıyla yeterli).

Kartla ödeme (aşağıdaki bölüm) aktif edildiğinde, o kanaldan gelen siparişler için de aynı bildirim otomatik çalışacak — ek bir kurulum gerekmiyor.

## Önemli — kartla ödeme (iyzico) entegrasyonu eklendi (kurulum gerekiyor)

Bu turda sitede gerçek kredi/banka kartıyla ödeme alma altyapısı tamamlandı — **iyzico** üzerinden. Kodun tamamı hazır ve test edildi, ama güvenlik gereği sitede **tamamen kapalı** durumda: "Kartla Öde" butonu hiçbir müşteriye görünmez, ta ki siz iyzico'ya başvurup gerçek API anahtarlarınızı bana gönderene ve panelden özelliği elle açana kadar. Aşağıdaki adımları takip edin.

### 1) Yeni dosyaları yükleyin

Bu zip'te iki yeni şey var, ikisini de `public_html` köküne yükleyin (panel dosyasıyla karıştırmayın, bunlar ana sitenin yanına gider):

- **`odeme-tamamlandi.html`** → ödeme sonrası müşterinin yönlendirildiği "sipariş tamamlandı" sayfası. Adını değiştirmeyin.
- **`odeme/`** klasörü (içinde `baslat.php`, `durum.php`, `config.php` ve birkaç dosya daha) → ödeme sunucu tarafı mantığı. Klasörü olduğu gibi, alt dosyalarıyla birlikte `public_html/odeme/` altına yükleyin. Bu klasördeki `.htaccess` dosyası `config.php` gibi hassas dosyaların tarayıcıdan doğrudan açılmasını engelliyor — o yüzden klasörü olduğu gibi (dosya adlarını değiştirmeden) yüklemeniz önemli.

Panel tarafında da küçük bir değişiklik var (yeni bir "Kartla Ödeme (iyzico)" ayar kutusu) — panel dosyasını da her zamanki gibi güncel tutun.

### 2) Neden önce ücretsiz bir "sandbox" (test) hesabıyla başlamalısınız

Gerçek başvuru onayı biraz zaman alabileceğinden, önce iyzico'nun ücretsiz test ortamıyla her şeyin doğru çalıştığını görmenizi öneririm — gerçek para hareket etmez, sahte test kartlarıyla uçtan uca deneme yaparsınız:

1. [sandbox-merchant.iyzipay.com/auth/register](https://sandbox-merchant.iyzipay.com/auth/register) adresinden ücretsiz bir test hesabı açın (gerçek başvurudan bağımsız, belge istemez).
2. Giriş yaptıktan sonra sol menüden **Ayarlar > Şirket Ayarları**'na gidin, sayfanın altında **API Anahtarları** bölümünü bulun. Buradaki **API Key** ve **Secret Key** değerlerini bana gönderin.
3. Ben bu değerleri `odeme/config.php` içine yerleştirip (`IYZICO_BASE_URL` zaten `sandbox-api.iyzipay.com` olarak ayarlı, dokunmanıza gerek yok) size güncellenmiş dosyayı geri göndereceğim — sadece bu tek dosyayı `public_html/odeme/config.php` üzerine tekrar yükleyeceksiniz.
4. Panelden **Ayarlar > Kartla Ödeme (iyzico)** kutusunu işaretleyip kaydedin.
5. Sitede bir ürünü sepete atıp, giriş yapıp, bir adres ekleyip "Kartla Öde"ye basın — iyzico'nun test ödeme sayfasına yönlendirilmelisiniz. [Test kartı numaraları için buraya bakın](https://docs.iyzico.com/ek-bilgiler/test-kartlari) (gerçek kart bilgisi girmeyin, bunlar sahte test kartları). Ödeme "başarılı" sonuçlanınca sitenize geri dönüp sipariş özetini görmelisiniz, panelde de **Siparişler** sekmesinde yeni siparişi görmelisiniz.
6. Her şey doğru çalışıyorsa panelden kutuyu tekrar kapatın (canlıya geçene kadar müşteriler görmesin) ve bir sonraki adıma geçin.

### 3) Gerçek (canlı) başvuru

Web'den güncel bilgiye göre iyzico'nun başvuru şartları şöyle (kaynak: iyzico'nun kendi yardım merkezi — [Başvuru Koşulları](https://www.iyzico.com/destek/yardim-merkezi/basvuru), [Gerekli Evraklar](https://www.iyzico.com/destek/yardim-merkezi/basvuru/gerekli-evraklar)):

- **Şahıs şirketiniz varsa (vergi levhanız varsa):** doğrudan **Sanal POS** (bu sitede kullandığımız asıl ürün) için başvurabilirsiniz. İstenen belgeler: vergi levhası, imza sirküleri, ortak(lar)ın kimlik fotokopisi, IBAN'ı gösteren banka teyit yazısı/ekstre.
- **Hiç şirketiniz yoksa (şahıs şirketi bile değilse):** iyzico'nun daha basit **"Link ile Ödeme"** ürününü sunuyor — şirket kurmadan, ödeme linki paylaşarak çalışıyor. Ama bu, sitedeki "Kartla Öde" butonuyla aynı otomatik akış **değil**; bu entegrasyonun çalışması için Sanal POS başvurusu (yani en azından bir şahıs şirketi/vergi levhası) gerekiyor. Şirketiniz yoksa önce bir muhasebeciyle şahıs şirketi açmayı değerlendirmeniz gerekebilir.
- Başvuru [iyzico Merchant Panel](https://merchant.iyzipay.com) üzerinden online yapılıyor, belgeler PDF/PNG/JPEG olarak (dosya başına max 10 MB) yükleniyor. iyzico'ya göre onay genelde **24 saat** içinde sonuçlanıyor, sonuç kayıtlı e-postanıza geliyor.

Başvurunuz onaylanınca panelinizden **canlı (production)** API Key ve Secret Key'i alıp bana gönderin — bu sefer `IYZICO_BASE_URL`'i de `https://api.iyzipay.com` (sandbox değil, canlı) olarak güncelleyip size son halini göndereceğim. Yeni `config.php`'yi yükleyip panelden kutuyu tekrar işaretlediğinizde site gerçek kartlarla ödeme almaya başlar.

### Bilinmesi gerekenler

- Kartla ödeme, sepete ekleme/WhatsApp/Instagram akışlarının **hiçbirini değiştirmiyor** — sadece yanlarına ek bir seçenek olarak ekleniyor. Kutuyu hiç işaretlemezseniz site tamamen eskisi gibi çalışmaya devam eder.
- Kartla ödeme için müşterinin **giriş yapmış** ve **en az bir teslimat adresi** eklemiş olması gerekiyor (giriş yapmadan kartla ödeme seçeneği görünmez, "Giriş Yap" ya da "Adres Ekle" yönlendirmesi çıkar).
- Ödeme tutarı **hiçbir zaman** müşterinin tarayıcısından gelen bilgiye göre değil, sunucuda Firestore'daki güncel ürün fiyatları ve kupon kaydı üzerinden yeniden hesaplanıyor — biri tarayıcı konsolundan sahte bir tutar göndermeye çalışsa bile işe yaramaz.
- `config.php` dosyasındaki gizli anahtar (`IYZICO_SECRET_KEY`) hiçbir zaman tarayıcıya gönderilmiyor, sadece sunucu (PHP) tarafında kullanılıyor; `odeme/.htaccess` dosyası bu dosyanın doğrudan bir tarayıcıdan açılmasını da ayrıca engelliyor.
- Kart numarası, CVV gibi bilgiler bu sitenin sunucusuna **hiç uğramıyor** — müşteri doğrudan iyzico'nun kendi güvenli ödeme sayfasına yönlendiriliyor, kart bilgisini orada giriyor.

## Notlar

- Dosyalar büyük olabilir (özellikle ana site ~1.8 MB, tüm ürün fotoğrafları dosyanın içinde) — yükleme birkaç dakika sürebilir, normaldir.
- Yasal metinlerdeki `[iletişim e-postası]` alanları artık `siparis@sonyacollection.com` ile dolduruldu. Hâlâ placeholder olan bilgiler var: gerçek WhatsApp numarası/telefon (hat henüz alınmadı, beklemede), şirket/şahıs adı ve adres gibi yasal metinlerdeki diğer [köşeli parantez] alanları. Bunları netleştirdiğinizde yönetici panelinin **Ayarlar** sekmesinden kendiniz güncelleyebilirsiniz — kod değişikliği gerekmez.
- PWA dosyaları (manifest/sw.js/ikonlar) sayesinde ziyaretçiler siteyi telefonlarına "Ana Ekrana Ekle" diyerek uygulama gibi kurabilir. Bu özellik yalnızca gerçek `https://` adresinde çalışır (yerel önizlemede çalışmaz), bu yüzden Hostinger'a yükledikten sonra telefonunuzdan test edin.
