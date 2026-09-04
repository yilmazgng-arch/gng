# Sonya Collection — Hostinger'a Yükleme Rehberi

## Bu turda ne değişti (2026-09-04 — "Bu Ay Trend" rozeti kontrolü + KVKK çerez onay bandı)

**"Bu Ay Trend" rozeti çakışması:** Gönderdiğin ekran görüntüsünde "🔥 Bu Ay Trend" rozetinin "TAKIMLAR" etiketinin arkasında kaldığını gördüm. Bu, bir önceki turda zaten bulup düzelttiğim `.card-tag` çakışma hatasıyla birebir aynıydı — tam bu ikili kombinasyonu (kategori etiketi + Bu Ay Trend rozeti) izole bir testle tekrar doğruladım, düzeltme doğru çalışıyor. Ekran görüntüsü muhtemelen o düzeltmeyi içeren zip'in henüz Hostinger'a yüklenmediği bir andan — yani bu turda gönderdiğim zip'i yükleyince sorun kendiliğinden düzelecek, ekstra bir işlem gerekmiyor.

**Eklenti araştırması — KVKK çerez onay bandı eklendi:** "Site için gerekli bir eklenti var mı?" sorunu araştırdım. Türkiye'de bir e-ticaret sitesinin ihtiyaç duyduğu zorunlu metinlerin (mesafeli satış/cayma hakkı, gizlilik politikası, KVKK aydınlatma metni) hepsi sitede zaten vardı. Tek somut eksik: site GA4 (Google Analytics) ve Meta Pixel'i her ziyaretçide otomatik çalıştırıyordu ama hiçbir onay almıyordu — KVKK'ya göre bu tür analiz/reklam çerezleri için ziyaretçiden onay alınması gerekiyor. Bunun için:
1. Sayfanın altında, marka renkleriyle uyumlu bir "Çerez Kullanımı" bandı eklendi — "Tümünü Kabul Et" ve "Sadece Zorunlu Çerezler" seçenekleriyle.
2. **Gerçekten işlevsel**: ziyaretçi "Sadece Zorunlu Çerezler"i seçerse GA4 ve Meta Pixel o ziyaretçide hiç yüklenmez/çalışmaz. Sadece "Tümünü Kabul Et" seçilirse aktif olurlar. Sepet/favori gibi sitenin çalışması için zorunlu olan localStorage kullanımı bundan etkilenmiyor (zaten KVKK kapsamında onay gerektirmiyor).
3. Tercih tarayıcıda hatırlanıyor, bir daha sorulmuyor. Footer'a eklenen "Çerez Tercihleri" linkiyle ziyaretçi dilediği zaman kararını değiştirebilir.
4. Yeni bir "Çerez Politikası" metni eklendi (diğer yasal metinler gibi Panel → Ayarlar → Yasal Metinler'den düzenlenebilir).
5. Bant, sağ altta duran WhatsApp/sohbet balonunun üzerine binmiyor — bant açıkken balon otomatik olarak yukarı kayıyor.
6. Sadece 11 mağaza sayfasına eklendi, yönetim paneline dokunulmadı (panelde müşteri takip kodu zaten çalışmıyor).

Diğer araştırma notları: ETBİS kaydı bir web sitesi dosyası değil, Ticaret Bakanlığı'na yapılan bir kayıt işlemi — eğer daha önce yapılmadıysa bunu ayrıca hallettiğinizden emin olun, kod tarafında yapılabilecek bir şey değil. Yasal metinlerdeki köşeli parantez yer tutucuları (`[Şirket/Şahıs Adı]` vb.) hâlâ bekliyor — aşağıdaki "bekleyen işler" listesinde.

Tüm 11 sayfada ve panelde JavaScript hata kontrolü yaptım + çerez bandının her sayfada doğru açılıp kapandığını, tercihi hatırladığını ve karanlık modda okunaklı kaldığını tek tek test ettim, hepsi temiz.

## Bir önceki round (2026-09-04 — komple site denetimi: güvenlik, metin ve tasarım düzeltmeleri)

Sitenin tamamını (11 mağaza sayfası + yönetim paneli + kartla ödeme arka ucu) baştan sona taradım: bitişik/çakışan görünümler, Türkçe hataları, demodan kalan içerik ve güvenlik açıkları için. Firestore kural değişikliği var ama sadece bir tanesi (aşağıda "Firebase Console'a eklemen gereken" bölümünde) — panele giriş yapmadım, hiçbir kişisel/finansal veri girmedim.

**Güvenlik — bulundu ve düzeltildi:**
1. **Yönetim panelinde bir güvenlik açığı buldum ve kapattım.** Panelin sipariş listesi ekranı, sipariş içindeki ürün adını/bedenini/rengini ekrana yazarken bunları güvenli hale getirmiyordu (teknik adıyla "XSS"). Siparişleri herkes (misafir dahil) oluşturabildiği için, kötü niyetli biri tarayıcı konsolundan doğrudan Firestore'a, ürün adı alanına zararlı bir kod içeren sahte bir "sipariş" gönderebilir, sen panelde siparişleri açtığında bu kod paneldeki oturumunda çalışabilirdi (ürünlerini/fiyatlarını/kuponlarını değiştirebilecek bir yetkiyle). Şimdi tüm bu alanlar ekrana yazılmadan önce güvenli hale getiriliyor — panel artık bu tür bir saldırıya kapalı.
2. **Sipariş CSV dışa aktarımını da güçlendirdim.** Aynı sebeple (sipariş verisi herkesten gelebiliyor), "=" ile başlayan bir ürün adı Excel'de açıldığında bir formül gibi çalışabilirdi ("formül enjeksiyonu" olarak bilinen bir saldırı türü). Artık böyle bir alan varsa başına otomatik olarak koruma karakteri ekleniyor.
3. **Müşteri yorumlarındaki fotoğraf linki için küçük bir sızdırmazlık düzeltmesi** — yorum fotoğrafı linkinin gerçekten bir fotoğraf (https://) olduğunu doğruluyoruz artık, teorik bir link-tabanlı açığı kapatıyor.
4. **Firebase Console'a eklemen gereken tek şey**: `firestore-rules.txt` dosyasını güncelledim (yorum fotoğrafının https ile başlamasını zorunlu kılan tek satırlık ek kural). Firestore kurallarını daha önce hiç Console'a yapıştırmadıysan (bu zaten bekleyen bir işti), yapıştırdığında bu da otomatik gelmiş olacak — ayrıca bir şey yapmana gerek yok.
5. Ayrıca kod içini taradım: gizli/parola bilgisi sızıntısı yok, tehlikeli `eval` kullanımı yok, kartla ödeme arka ucu (henüz aktif değil) tutarı doğru şekilde sunucu tarafında yeniden hesaplıyor — bu kısımlar zaten sağlamdı.

**Metin/Türkçe düzeltmeleri:**
6. Stil Rehberi sayfasında bir yazım hatası düzeltildi: "arananmamış" → "aranmamış".
7. Panelin KVKK metni site ile birebir aynı olacak şekilde düzeltildi (panelde yanlışlıkla resmi "haklarınız" yazıyordu, sitedeki gibi samimi "hakların" oldu).
8. Hakkımızda, SSS ve Stil Rehberi sayfalarında, kod içinde küçük bir kopyala-yapıştır hatası vardı (bu sayfalar yanlışlıkla "hesap sayfası" gibi davranıyordu) — düzelttim, artık bu sayfalarda "Son Görüntülenenler" ürün şeridi doğru şekilde çalışıyor.

**Görünüm/tasarım düzeltmeleri:**
9. Anasayfada 3 fotoğraflık vitrinin altındaki "Sonbahar/Kış 26" başlığı ve üstündeki-altındaki boşluklar düzeltildi — hem fotoğraflara hem "Ürünleri Gör" butonunun altındaki foto banner'a artık yapışık değil.
10. Ürün listesinin altındaki "Son Görüntülenenler" şeridiyle ürün ızgarası arasına da boşluk eklendi (daha önce sadece üstteki boşluğu düzeltmiştim, alttakini kaçırmışım).
11. "Flaş İndirim" kutusunun (aktif olduğunda görünür) kendi içindeki ve çevresindeki boşluklar eksikti — artık diğer vitrin kutularıyla tutarlı.
12. SSS ve Stil Rehberi sayfalarının başlıkları menüye yapışıktı, düzelttim. Bu iki sayfadaki soru-cevap ve öneri kutularının hiç tasarımı yoktu (düz metin gibi görünüyorlardı) — artık sitenin geri kalanıyla tutarlı, kart görünümlü, güzel görünüyorlar (ekran görüntülerini gönderdim).
13. Bir ürün hem kategori etiketi hem "Yeni"/"Çok Satan" gibi bir rozet taşıdığında, ikisi üst üste biniyordu — artık düzgün alt alta diziliyorlar.
14. Bazı küçük rozet/panel öğeleri (favori paylaş butonu, sadakat puanı paneli, "şu an bakıyor" etiketi) gizlenmesi gerektiğinde bazen görünür kalabiliyordu (teknik bir CSS çakışması) — hepsini düzelttim.
15. Karanlık modda, aktif bir kampanya geri sayımı varsa üzerindeki yazı neredeyse görünmez oluyordu (koyu krem zemin üzerinde beyaz yazı) — okunabilir hale getirildi.

Tüm 11 sayfada ve panelde JavaScript hata kontrolü + sepet/favori/karanlık mod smoke testi yaptım, hepsi temiz.

## Bir önceki round (2026-09-04 — vitrin fotoğrafı/filtreler arası boşluk + beyaz alanlar bej oldu)

İki şey düzeltildi: (1) Anasayfa ve kategori sayfalarında (Yeni Sezon, İndirim, Pelerinler, Kimonolar, Takımlar, Elbiseler) vitrin fotoğrafının hemen altında filtre butonlarının (Tümü/İndirim/...) yapışık durması — bir önceki turda kaldırılan "Bu Haftanın Seçkisi" başlığıyla birlikte oradaki boşluk da gitmiş, düzelttim, artık aralarında belirgin bir nefes payı var. (2) Açık moddaki neredeyse-beyaz yüzey rengi (kartlar, filtre çipleri, header, sepet/favori çekmeceleri, form alanları, footer — sitede "beyaz" görünen her yer) daha sıcak, belirgin bir bej tona çevrildi; yazılar zaten koyu kahve tonundaydı, değişmedi. Sadece mağaza tarafına (11 sayfa) uygulandı, yönetim paneline dokunmadım — istersen orayı da aynı bej tona çevirebilirim. Karanlık mod hiç değişmedi. Firestore kural değişikliği yok.

## Bir önceki round (2026-09-04 — "Favorilerimi Paylaş" sepete taşındı)

Ürünler bölümünün üstünde, favoriler görünümünde çıkan "Favorilerimi Paylaş" butonu oradan kaldırıldı, sepet çekmecesine (Favorilerim satırının hemen altına) taşındı. Buton artık misafir dahil herkese, hangi sayfada olursan ol sepeti açtığında görünüyor — sadece favorilerin en az 1 ürün içerdiğinde beliriyor. Tıklandığında aynı eski davranış: paylaşım linki kopyalanıyor / cihaz paylaşım menüsü açılıyor. Firestore kural değişikliği yok.

## Bir önceki round (2026-09-04 — footer'dan "Kategoriler" sütunu kaldırıldı)

Footer'da "Kurumsal" sütununun üstünde duran "Kategoriler" başlığı ve altındaki liste tamamen kaldırıldı. Geriye "Kurumsal" ve "Bize Ulaşın" kalıyor, marka bloğuyla birlikte 3 sütun olarak daha dengeli/sofistike bir görünüm aldı — hem masaüstünde hem mobilde boş sütun ya da garip boşluk kalmıyor, ızgara (grid) genişlikleri buna göre yeniden ayarlandı. Bu, `#magaza` bölümündeki ürün/kategori filtrelerini etkilemiyor, sadece footer'daki bu tekrarlı linkler kaldırıldı. Firestore kural değişikliği yok.

## Bir önceki round (2026-09-04 — yönetim paneli de yeni ana tona geçti)

Bir önceki turda mağaza tarafında (sonya-collection.html ve diğer 10 sayfa) uyguladığım yeni taş/greige ana rengi (`#928273` / koyu ton `#706358`), senin isteğinle şimdi **yönetim paneline (panel.sonyacollection.com) de** uygulandı. Panelde kullanılan tüm "karamel" vurgu rengi (yükleniyor ikonu, sekme aktif çizgisi, form odak çerçeveleri, satış grafiği çubukları, fotoğraf yükleme çubuğu, linkler) artık sitenin geri kalanıyla aynı tonda. Panelin karanlık modu yok, o yüzden tek bir renk seti güncellendi. Firestore kural değişikliği yok, sadece görsel bir güncelleme.

## Bir önceki round (2026-09-04 — müşteriye giden sipariş e-postaları)

Sordun: "sepetinde ürün olan birine stok azaldı diye mail" ve "siparişi tamamlayana sipariş detayı mail" özellikleri var mıydı? Kontrol ettim — ikisi de yoktu. Konuştuğumuz gibi, senin onayınla **ikisini değil, en değerli iki şeyi** ekledim (sepet-stok e-postası yerine önerdiğim daha basit çözümü sen de tercih etmedin, o yüzden onu eklemedim — istersen ayrıca konuşuruz). Firestore kural değişikliği yok. **Sen de EmailJS'te yeni şablonu oluşturup ID'sini bana ilettin, ikisi de artık AKTİF.**

**1) Müşteriye sipariş onay e-postası (yeni, AKTİF).** Şu ana kadar sipariş tamamlanınca e-posta SADECE sana (siparis@sonyacollection.com) gidiyordu, müşteri hiçbir e-posta almıyordu. Artık müşteri de kendi mailine "Siparişiniz Alındı" başlığıyla ürün listesi + tutar özeti içeren bir onay e-postası alıyor. **Not:** bu sadece hesabına giriş yapmış müşterilerde çalışıyor — misafir siparişlerinde e-posta adresi hiç kaydedilmiyor, o yüzden misafir siparişlerde bu e-posta gidemiyor (checkout'a e-posta alanı eklemek istersen ayrı bir iş olarak yapabiliriz).

**2) Sipariş durumu değişince müşteriye e-posta (koddan hazırdı, şimdi AKTİF).** Panelden bir siparişin durumunu "Kargoya Verildi" gibi değiştirdiğinde, müşteriye otomatik e-posta gidiyor artık.

**Tek şablon yeterli oldu:** EmailJS'in ücretsiz planı en fazla 2 şablona izin veriyor, biri zaten sana giden sipariş bildirimi için kullanılıyordu (`template_spvadv9`). Senin oluşturduğun yeni şablon (`template_asd2tad`) yukarıdaki iki özelliğin ikisini de tek başına karşılıyor — kotanız tam dolu (2/2) ama fazlasına ihtiyaç yok. Ayda 200 e-posta hakkınız var, küçük bir mağaza için fazlasıyla yeterli.

**3) Footer'ın tamamı ortalandı (bir önceki turdaki düzeltme eksikmiş — düzelttim).** Bir önceki turda sadece en alttaki telif/ödeme rozeti satırını ortalamıştım; gönderdiğin ekran görüntüsünde gördüğün gibi bunun üstündeki Kategoriler/Kurumsal/Bize Ulaşın sütunları ve logo/açıklama hâlâ sola yaslıydı. Şimdi mobilde footer'ın tamamı (başlıklar, linkler, sosyal medya ikonları, marka bloğu dahil) ortalanmış duruyor.

**4) Sitenin ana rengi değişti — gönderdiğin taş/greige tonuna göre.** Gönderdiğin renk görselinden tam tonu aldım (`#8E8277`) ve sitenin her yerinde kullanılan "karamel" vurgu rengini bununla değiştirdim: butonlar, fiyatlar, aktif filtre çipleri, linkler, rozetler, ikonlar — hepsi artık bu yeni, daha sofistike/mat tonda. Hem açık hem karanlık mod için ayrı ayrı ayarladım (karanlık modda okunabilirlik için biraz açık tonu kullanıyor, tıpkı eskisi gibi). Sadece sonya-collection.html (mağaza tarafı) değişti, yönetici paneline (admin.html) dokunmadım — istersen onu da aynı tona çevirebilirim.

## Bir önceki round (2026-09-04 — mobil boşluk, footer düzeni ve anasayfa vitrini düzeltmesi)

Bu tur, gönderdiğin geri bildirime göre 3 isteği ele alıyor; ayrıca bir önceki turda "Bu Haftanın Seçkisi" ile ilgili yanlış anladığım bir noktayı da düzelttim. Firestore kural değişikliği yok.

**1) "Stilini Paylaş, Ödül Kazan" footer'dan tamamen kaldırıldı.** Bir önceki turda diğer 3 link taşınırken bu biri kasıtlı olarak bırakılmıştı, bu tur onu da tamamen kaldırdım — ne footer'da ne başka bir yerde artık görünmüyor.

**2) Anasayfadaki "Bu Haftanın Seçkisi" başlığı kaldırıldı, ürünler ve filtreler olduğu gibi duruyor.** Not: bir önceki turda bunu yanlış yapmıştım — o zaman bölümün tamamını (ürün ızgarası + filtre butonları dahil) kaldırmıştım, ama senin isteğin sadece "Bu Haftanın Seçkisi" başlık yazısının kaldırılmasıydı. Şimdi düzelttim: anasayfada ürünler ve üstteki filtre butonları (Tümü/İndirim/Yeni Sezon/Pelerinler/Kimonolar vb.) yine eskisi gibi tam çalışıyor, sadece o başlık metni yok.

**3) Karanlık modun koyuluğu azaltıldı, daha yumuşak bir "acı kahve" tonuna çevrildi.** Önceki karanlık mod neredeyse siyaha yakındı; şimdi sıcak, yumuşak bir kahverengi tonunda — okunabilirlik ve kontrast aynen korunuyor, sadece göze daha rahat.

**4) Mobil üst menüdeki ikonlar arasına gerçekten belirgin bir boşluk eklendi — 2 aşamada.** İlk ince ayar (karanlık modu ☰ menüsüne taşımak) tek başına yeterince belirgin olmamıştı, geri bildirimin üzerine bir adım daha attım: **favoriler ikonunu da** üst sıradan çıkarıp sepet çekmecesinin en üstüne, "❤ Favorilerim" şeklinde tıklanabilir bir satır olarak taşıdım (misafirler dahil herkes ulaşabiliyor, giriş gerektirmiyor). Böylece üst sırada 5 yerine sadece 3 ikon kalıyor (ara/hesabım/sepet) ve aralarında artık gerçekten belirgin, masaüstündekiyle aynı ölçüde bir boşluk var — ikonlar da eski boyutuna (40px) geri döndü. Karanlık mod hâlâ ☰ menüsünün altında, favoriler artık sepet ikonuna tıklayınca çekmecenin en üstünde duruyor. Masaüstünde hiçbir şey değişmedi, tüm ikonlar hâlâ üst menüde.

**5) Sayfanın en altındaki (footer) telif yazısı ve ödeme rozetleri mobilde artık ortalanmış duruyor.** Önceden sol köşeye yapışık görünüyorlardı, bu da özensiz bir izlenim veriyordu; şimdi ortalanmış ve daha düzenli/sofistike duruyor.

**Bulup düzelttiğim ek bir sorun (önceki turdan, hâlâ geçerli): bazı telefonlarda sepet ikonu tamamen ekran dışında kalıyordu.** Kök nedeni: 5 ikon + logo bir arada, bazı telefon genişliklerinde tam sığmıyordu. Bu tur karanlık mod + favoriler ikonlarının taşınmasıyla bu sorun tamamen çözüldü — artık en dar telefonlarda (360px) bile sepet ikonu rahatça sığıyor, kaydırmaya bile gerek kalmadı; güvenlik amacıyla üst menünün yatay kaydırma özelliği yine de duruyor.

## Bu turdan önceki round (2026-09-03 — Hediye Çeki/Ölçü Profili/İade taşıma ve mobil kaydırma düzeltmesi)

Bu tur, ekran görüntüsüyle işaret ettiğin footer düzeni sorununu ve ayrıca bildirdiğin bir mobil kaydırma hatasını ele alıyor. Firestore kural değişikliği yok.

**1) Hediye Çeki artık footer'da değil, direkt ödeme ekranında.** Haklıydın — "Hediye Çeki" isteği Kurumsal bölümünde durması saçmaydı, alakası ödeme akışıyla. Şimdi sepette "Ödemeye Geç" adımında, IBAN/ödeme yöntemi bilgilerinin hemen altında "🎁 Sevdiklerine hediye çeki almak ister misin?" şeklinde tıklanabilir bir link olarak çıkıyor — tıklayınca aynı hediye çeki formu açılıyor, sadece yeri değişti.

**2) Ölçü Profilim ve İade/Değişim Talebi Oluştur artık Hesabım sayfasında.** İkisi de hesap işlemleri, footer'da (Kurumsal) değil Hesabım sayfasında olmaları mantıklı — haklıydın. Hesabım sayfasına, "Siparişlerim" kartının altına yeni bir "Hesap İşlemleri" kartı eklendi; "📏 Ölçü Profilim" ve "İade/Değişim Talebi Oluştur" linkleri artık orada, tıklayınca yine aynı formları açıyorlar.

**3) Kurumsal (footer) bölümü sadeleşti.** Artık sadece gerçekten kurumsal/bilgi amaçlı linkler var: Hakkımızda, Stil Rehberi, Sıkça Sorulan Sorular, İletişim, Gizlilik Politikası, İade & Değişim, Stilini Paylaş Ödül Kazan, Kişisel Verilerin Korunması. Aksiyon gerektiren (form açan) üç link buradan çıkarıldı, normalde ait oldukları yere taşındı.

**4) Mobil görünümde siteyi sağa/sola çekince boşluk çıkması düzeltildi.** Bildirdiğin gibi, telefonda sayfayı parmakla sağa-sola oynatınca sağ tarafta boş bir alan görünüyordu. Kök nedeni: ekranın kenarından açılan sepet çekmecesi (sağdan kayan panel), kapalıyken bile ekranın sağına taşan görünmez bir alan bırakıyordu ve bu alan telefonun yatay kaydırmasına izin veriyordu. Bu görünmez taşmayı tamamen kapattım — artık sayfa hiçbir sayfada yatay olarak kaydırılamıyor/oynatılamıyor. Hem anasayfada hem diğer sayfalarda (ör. İndirim) test ettim, sıfır yatay taşma var.

## Daha önceki round (2026-09-03 — mobil menü konumu)

Bu tur, gönderdiğin ekran görüntülerine göre tek bir ince ayarı ele alıyor. Firestore kural değişikliği yok.

**Mobil görünümde üç çizgili menü ikonu artık "Sonya Collection" logosunun hemen yanında.** Önceden bu ikon sağdaki diğer ikonlarla (karanlık mod, ara, hesabım, favoriler, sepet) aynı grupta, logodan ayrı duruyordu. Referans olarak gönderdiğin örnek siteye (bensubuyruk.com) uygun şekilde, artık logonun solunda/yanında, kendi grubunda duruyor — diğer ikonlar sağda kalmaya devam ediyor. "Bu Haftanın Seçkisi" altındaki kategori butonlarına (Tümü/İndirim/Yeni Sezon/Pelerinler/Kimonolar vb.) hiç dokunmadım, olduğu gibi duruyorlar. Hem açık hem karanlık modda, menü açma/kapama davranışının bozulmadığını gerçek tarayıcıda test ettim.

## Elinizdeki dosyalar

**`sonyacollection.com`'un kök dizinine (`public_html`) yüklenecekler:**

- **sonyacollection-com-index.html** → ana site, adını **`index.html`** olarak değiştirerek yükleyin.
- **yeni-sezon.html, indirim.html, pelerinler.html, kimonolar.html, takimlar.html, elbiseler.html, hesabim.html** → sitenin diğer sayfaları, adlarını değiştirmeden aynen yükleyin (üst menüdeki bağlantılar bu dosya adlarını arar).
- **404.html, sss.html, stil-rehberi.html, hakkimizda.html** → sayfa bulunamadı ekranı, Sıkça Sorulan Sorular, Stil Rehberi ve Hakkımızda sayfaları, adlarını değiştirmeden diğer sayfalarla aynı kök dizine yükleyin.
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

Zip'i indirip açtığınızda içinde bir de `panel` adlı alt klasör göreceksiniz — kafa karıştırmasın diye net söyleyelim: **zip'in kendisi `public_html`'e, içindeki `panel` klasörü ise `public_html/panel`'e gidiyor.** İki ayrı hedef, iki ayrı yükleme.

1. **hPanel > Dosyalar > Dosya Yöneticisi** (File Manager) açın.
2. Önce `public_html` klasörüne girin (panel klasörüne değil, ana klasöre):
   - Zip'i açtığınızda çıkan **`panel` klasörü hariç, geri kalan her şeyi** (index.html, sonya-collection.html, yeni-sezon.html, indirim.html, pelerinler.html, kimonolar.html, takimlar.html, elbiseler.html, hesabim.html, admin.html, manifest.webmanifest, sw.js, ikonlar, .htaccess, odeme-tamamlandi.html, odeme/ klasörü, README.md hariç geri kalanlar) buraya yükleyip üzerine yazın (Replace).
   - `.htaccess` dosyası nokta ile başladığı için Dosya Yöneticisi'nde görünmeyebilir, sağ üstteki ayarlar menüsünden "Gizli dosyaları göster"ü açmanız gerekebilir.
   - Not: `admin.html` dosyasının burada (public_html'in kendisinde) durması sorun değil, sadece yedek kopya gibi düşünün — asıl kullanılan panel dosyası aşağıdaki adımdaki.
3. Şimdi `public_html/panel` klasörüne girin (yoksa Adım 2'deki gibi önce alt alan adını oluşturun):
   - Zip içindeki **`panel` klasörünün İÇİNDEKİ** `index.html` dosyasını buraya yükleyip üzerine yazın (Replace). Dosya zaten `index.html` adıyla geliyor, ismini değiştirmenize gerek yok.

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

## Müşteriye giden e-postalar (sipariş onayı + durum güncellemesi) — KURULDU ve AKTİF

Sizin oluşturduğunuz "Contact Us" şablonu (`template_asd2tad`) hem sonya-collection.html (+ diğer 10 sayfa) içindeki `EMAILJS_CUSTOMER_TEMPLATE_ID`, hem admin.html içindeki `EMAILJS_STATUS_TEMPLATE_ID` alanına işlendi. Artık iki şey otomatik çalışıyor: (1) sipariş tamamlayan **giriş yapmış** müşterilere kendi mailine "Siparişiniz Alındı" başlıklı bir onay e-postası gidiyor, (2) panelden sipariş durumunu değiştirdiğinizde ("Kargoya Verildi" vb.) müşteriye otomatik güncelleme e-postası gidiyor. Reply-To olarak siparis@sonyacollection.com ayarlı, yani müşteri "yanıtla" derse size gelir.

**Misafir siparişlerinde çalışmaz** — checkout'ta e-posta alanı olmadığı için misafirin adresi hiç kaydedilmiyor, bu yüzden bu iki e-posta sadece hesabı olan müşterilere gidiyor. İsterseniz checkout'a bir e-posta alanı eklemeyi ayrıca konuşabiliriz.

EmailJS'in ücretsiz planı ayda 200 e-posta ve en fazla 2 şablona izin veriyor — hem sizin bildirim e-postanız (`template_spvadv9`) hem bu yeni müşteri şablonu (`template_asd2tad`) olmak üzere şu an 2 şablon kullanılıyor, tam kapasitedesiniz. Talep sayısı (Requests) EmailJS panelinde "X talep kaldı" şeklinde görünüyor, ay sonunda sıfırlanıyor — mağaza büyüdükçe bu sınıra yaklaşırsanız haber verin, ücretli plana geçmeyi konuşuruz.

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

## Google'da logo ve alt bağlantılar (organik/ücretsiz SEO)

Google aramasında sitenizin yanında logo görünmesi ve "adL" örneğindeki gibi alt bağlantılar (Bluz/Ceket vb.) çıkması istendi. Bunlar **kodla doğrudan zorlanamıyor** — Google, siteyi kendi taradıktan ve güvenilir bulduktan sonra kendi kararıyla gösteriyor. Bu turda kod tarafında yapılabilecek olan yapıldı (sayfaların gizli "structured data" kısmına sitenin logosu ve Instagram bağlantısı eklendi — `icon-512.png` + `https://instagram.com/sonyacollectiion`). Sürecin geri kalanı sizin yapmanız gereken, ücretsiz ama Google hesabınızla:

1. **Google Search Console'a gidin**: [search.google.com/search-console](https://search.google.com/search-console) — kendi Google hesabınızla giriş yapın.
2. **Mülk (Property) ekleyin**: "URL öneki" seçeneğiyle `https://sonyacollection.com` girin.
3. **Sahiplik doğrulama**: en kolay yöntem genelde "HTML dosyası" veya "HTML etiketi" seçeneği — Search Console size küçük bir kod/dosya verir, onu Hostinger'a yüklemeniz ya da sitenin `<head>` kısmına eklemem gerekir (bu adıma geldiğinizde bana kodu iletin, ben sitenin koduna ekleyeyim).
4. **Site haritasını (sitemap) gönderin**: doğrulama bittikten sonra sol menüden "Sitemaps" (Site Haritaları) → `sitemap.xml` yazıp gönderin (bu dosya zaten sitede mevcut).
5. Bundan sonrası Google'ın elinde — site tarandıkça, ziyaretçi/tıklama geçmişi oluştukça, zamanla (genelde haftalar/aylar) logo ve alt bağlantılar kendiliğinden belirebilir. **Garantisi yok**, küçük/yeni bir site için biraz sabır gerektiren bir süreç.

Alternatif (ücretli) yol Google Ads'te "Business Logo Asset" + "Sitelink Asset" kurmak — bu, kendi Google Ads hesabınızı açıp reklam bütçesi ayırmanızı gerektiriyor, o yüzden şimdilik bu ücretsiz yolla ilerliyoruz.

## Notlar

- Dosyalar büyük olabilir (özellikle ana site ~1.8 MB, tüm ürün fotoğrafları dosyanın içinde) — yükleme birkaç dakika sürebilir, normaldir.
- Yasal metinlerdeki `[iletişim e-postası]` alanları artık `siparis@sonyacollection.com` ile dolduruldu. Hâlâ placeholder olan bilgiler var: gerçek WhatsApp numarası/telefon (hat henüz alınmadı, beklemede), şirket/şahıs adı ve adres gibi yasal metinlerdeki diğer [köşeli parantez] alanları. Bunları netleştirdiğinizde yönetici panelinin **Ayarlar** sekmesinden kendiniz güncelleyebilirsiniz — kod değişikliği gerekmez.
- PWA dosyaları (manifest/sw.js/ikonlar) sayesinde ziyaretçiler siteyi telefonlarına "Ana Ekrana Ekle" diyerek uygulama gibi kurabilir. Bu özellik yalnızca gerçek `https://` adresinde çalışır (yerel önizlemede çalışmaz), bu yüzden Hostinger'a yükledikten sonra telefonunuzdan test edin.
