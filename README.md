# Sonya Collection — Hostinger'a Yükleme Rehberi

## Bu turda ne değişti (2026-09-03 — site denetimi ve düzeltme paketi)

Bu tur yeni bir özellik turu değil — sitenin tamamını (tıklanabilir her buton/link, kullanım akışları, görünüm) baştan sona denetleyip bulduğum gerçek sorunları düzelttiğim bir tur. Firestore kural değişikliği yok, "ÖNCE BUNU YAP" adımı gerekmiyor.

**Bulunan ve düzeltilen asıl sorun — SSS ve Stil Rehberi sayfaları geride kalmış.** `sss.html` ve `stil-rehberi.html`, geçmiş turlarda sitenin geri kalanına uyguladığımız güncellemelerin dışında kalmıştı. Somut etkisi: bu iki sayfada karanlık mod düğmesi hiç yoktu (ana sayfada karanlık moda geçip SSS'e tıklayan bir müşteri aniden aydınlık moda dönüyordu, geri döndürecek yolu da yoktu), üst menüde bu sayfalardayken "Anasayfa" sekmesi yanlışlıkla aktif görünüyordu. Her iki sayfa da artık sitenin geri kalanıyla birebir aynı alt yapıyı (karanlık mod, doğru menü durumu, tüm güncel özellikler) kullanıyor; SSS'teki soru-cevap akordeonu ve Stil Rehberi'nin içeriği aynen korundu.

**Erişilebilirlik.** Ürün kartlarındaki, ürün detayındaki büyük fotoğrafta ve karşılaştırma tablosundaki ürün görselleri artık ekran okuyucu kullanan ziyaretçiler için ürün adını duyuruyor (önceden bu görseller sadece CSS arka planıydı, hiçbir açıklaması yoktu).

**Yeni sayfa: Hakkımızda.** Footer'daki "Kurumsal" bölümüne, markayı kısaca tanıtan yeni bir `hakkimizda.html` sayfası eklendi. İçeriği sitede zaten var olan gerçek bilgilerden (renk paleti, ürün kategorileri, kumaş bakımı yaklaşımı) yola çıkarak yazıldı — kuruluş hikayesi, ekip veya spesifik bir tarih gibi elimde olmayan bilgileri uydurmadım. İstersen kendi hikayenizi/fotoğraflarınızı eklemek için bana yazabilirsin, ben sayfaya işlerim.

**Denetimde temiz çıkanlar (bilgi amaçlı):** 8 mağaza sayfasındaki 200'den fazla buton/link referansını tek tek karşılaştırdım — tıklandığında hiçbir şey yapmayan "ölü" bir buton yok. Tüm statik linkler doğru sayfalara gidiyor, kategori menüsü her sayfada doğru "aktif" durumu gösteriyor. Footer'daki Hediye Çeki, Stilini Paylaş, Ölçü Profilim ve İade/Değişim Talebi linklerini gerçek tarayıcıda tıklayarak test ettim, hepsi doğru ekranı açıyor.

## Bir önceki round (2026-09-03 — vitrin, kişiselleştirme ve dönüşüm paketi)

**ÖNCE BUNU YAP — Firestore kurallarını güncellemeden bu turun bazı özellikleri (Az Önce Satıldı bildirimi, Doğum Günü Kuponu) çalışmaz:**

1. [console.firebase.google.com](https://console.firebase.google.com) → `sonyacollection-62544` projesi → sol menüden **Firestore Database** → üstte **Rules (Kurallar)** sekmesi.
2. Zip içindeki **firestore-rules.txt** dosyasını aç, içeriğinin TAMAMINI kopyala.
3. Firebase Console'daki kural kutusunun içindekini sil, kopyaladığını yapıştır, sağ üstten **Publish (Yayınla)** butonuna bas.

Kurallara bu turda eklenenler: yeni `recentSales/{id}` koleksiyonu (Az Önce Satıldı bildirimi — sadece ürün adı ve zaman, hiçbir kişisel veri yok; herkes okuyabilir/ekleyebilir, sadece admin güncelleyip silebilir); `coupons` koleksiyonuna kendi kendine (self-service) `BDAY-` (doğum günü kuponu) önekli kupon kodu oluşturma izni.

Bu turda fikirler, senin "siteye girince insanlar wow demeli, hem satış desteklenmeli hem müşteriye her kolaylık olmalı" isteğine göre araştırdığım 20 öneriden seçtiğim en etkili 12 tanesi. 8 mağaza sayfası + admin.html güncellendi, yeni dosya yok:

**Vitrin & "Wow" etkisi**
- **Karanlık mod.** Header'a bir ay ikonu butonu eklendi; tıklayınca site tamamen koyu bir temaya geçiyor, tekrar tıklayınca normale dönüyor. Tercih tarayıcıda hatırlanıyor. Sitenin markası bilerek sistem karanlık mod ayarını otomatik takip etmiyor (kod içinde zaten böyle bir tasarım kararı vardı) — tamamen ziyaretçinin kendi seçimi.
- **Sipariş sonrası kutlama animasyonu.** Bir sipariş tamamlandığında (kartla, kapıda ödeme, havale, WhatsApp/Instagram fark etmez) ekranda kısa bir konfeti animasyonu oynuyor — küçük ama akılda kalan bir "wow" anı. Hareket azaltma tercihi olan ziyaretçilerde otomatik devre dışı kalıyor.
- **Yakınlaştırmalı tam ekran ürün galerisi.** Ürün fotoğrafına tıklayınca (veya köşesindeki büyüteç ikonuna) fotoğraf tam ekran açılıyor; telefonun parmakla kaydırmasıyla, bilgisayarda ok tuşlarıyla fotoğraflar arasında geziliyor. Kumaş dokusunu/detayı yakından görmek isteyen müşteriler için.
- **"Az önce satıldı" bildirimi.** Açarsan (Panel → Ayarlar), ekranın sol altında ara sıra "Az önce X satın alındı" gibi küçük bir bildirim beliriyor — gerçek, o an tamamlanan siparişlerden, sadece son 12 saat içindekilerden. Hiçbir müşteri bilgisi paylaşılmıyor, sadece ürün adı.

**Satışı destekleyen özellikler**
- **"Bu Ay Trend" rozeti.** Panelin gerçek son 30 günlük sipariş verisinden otomatik hesapladığı en çok satan ürünlere kırmızı bir "🔥 Bu Ay Trend" rozeti çıkıyor — elle işaretlediğin "Çok Satan" rozetinden farklı, tamamen gerçek veriden, senin hiçbir şey yapmana gerek kalmadan güncelleniyor.
- **Ürün bazlı flaş indirim.** Panel → ürün düzenle ekranına bir "Flaş İndirim Bitiş" tarih/saat alanı eklendi; indirimli bir ürüne gerçek bir bitiş zamanı girersen, ana sayfada canlı saniye saniye geri sayan bir "⚡ Flaş İndirim" vitrini beliriyor. Süre dolunca ürün otomatik vitrinden kalkıyor.
- **Kargoya en yakın ürün önerisi.** Sepette "ücretsiz kargoya X ₺ kaldı" çubuğunun altına artık fiyatı o eksiğe en yakın, stokta olan bir ürün de öneriliyor — müşteri tek tıkla o ürünü inceleyip kargoyu bedavaya getirebiliyor.
- **Altın üye erken erişimi.** Panel → ürün düzenle ekranına bir "Erken Erişim Bitişi" alanı eklendi; girersen o tarihe kadar ürünü sadece Altın sadakat seviyesindeki müşteriler görüp satın alabiliyor — diğer herkese o tarihe kadar hiç gösterilmiyor. Sadakat programına gerçek bir ayrıcalık katıyor.
- **Doğum günü kuponu.** Açarsan (Panel → Ayarlar, yüzdesini de sen belirliyorsun), müşteri "Ölçü Profilim" ekranına doğum gününü (sadece gün+ay, yıl hiç istenmiyor/saklanmıyor) bir kere kaydettiğinde, her yıl o gün kendisine otomatik özel bir indirim kuponu tanımlanıyor.

**Müşteriye kolaylık**
- **Düşük stok rozeti düzeltmesi.** Renk/beden bazında stok takibi yapılan ürünlerde "Son X adet kaldı" rozeti daha önce yanlış çalışıyordu (sadece tekli stoklu ürünlerde doğruydu); bu tur gerçek toplam stoktan hesaplanacak şekilde düzeltildi.
- **Hediye paketi seçeneği.** Açarsan (Panel → Ayarlar), ödeme ekranında "Hediye paketiyle gönder" seçeneği çıkıyor; müşteri işaretlerse sipariş üzerinde bir 🎁 etiketiyle Panel → Siparişler'de görünüyor, sen elle paketliyorsun.
- **Sepeti WhatsApp'a gönder.** Sepet ekranına, sipariş vermek için değil sadece paylaşmak için bir "Sepeti WhatsApp'a Gönder" butonu eklendi — müşteri sepetindeki ürünleri bir arkadaşına/eşine gösterip fikir alabiliyor.

**Aktifleştirmek için senin yapman gerekenler:**
- **Az Önce Satıldı / Doğum Günü Kuponu / Hediye Paketi:** Panel → Ayarlar'dan ilgili checkbox'ı işaretle (doğum günü kuponunun yüzdesini de orada belirle).
- **Altın Üye Erken Erişimi:** Panel → Ayarlar'da varsayılan süreyi (saat) belirle, sonra istediğin ürünlerin düzenleme ekranına "Erken Erişim Bitişi" gir.
- **Ürün Bazlı Flaş İndirim:** İndirimli (Eski Fiyat girilmiş) bir ürünün düzenleme ekranına "Flaş İndirim Bitiş" gir.
- **Firestore kuralları:** Yukarıdaki "ÖNCE BUNU YAP" adımını uygula.

## Bu turdan önceki round (2026-09-03 — ödeme, sadakat ve bildirim paketi)

**ÖNCE BUNU YAP — Firestore kurallarını güncellemeden bu turun bazı özellikleri (Dijital Hediye Çeki talepleri, Web Push token kaydı, Doğrulanmış Alıcı rozeti) çalışmaz:**

1. [console.firebase.google.com](https://console.firebase.google.com) → `sonyacollection-62544` projesi → sol menüden **Firestore Database** → üstte **Rules (Kurallar)** sekmesi.
2. Zip içindeki **firestore-rules.txt** dosyasını aç, içeriğinin TAMAMINI kopyala.
3. Firebase Console'daki kural kutusunun içindekini sil, kopyaladığını yapıştır, sağ üstten **Publish (Yayınla)** butonuna bas.

Kurallara bu turda eklenenler: yeni `giftCardRequests/{id}` koleksiyonu (Dijital Hediye Çeki talepleri — ziyaretçi oluşturabilir, sadece admin okuyup durumunu güncelleyebilir/silebilir); yeni `pushTokens/{token}` koleksiyonu (Web Push bildirim token kaydı — herkes kendi token'ını yazabilir, sadece admin okuyabilir); `reviews` koleksiyonuna yazılan `verifiedPurchase` alanının tip doğrulaması (Doğrulanmış Alıcı rozeti).

Bu turda fikirler; ödeme yöntemlerinde çeşitlilik, sadakat/sadakat hissi, satın alma güveni ve bildirim/teknik altyapı etrafında toplanan gerçek e-ticaret büyüme trendlerinden ve Ticaret Bakanlığı'nın Fiyat Etiketi Yönetmeliği'nden geldi. 8 mağaza sayfası + admin.html güncellendi, yeni dosya olarak **firebase-messaging-sw.js** eklendi (Web Push için) — 16 yeni özellik:

**Ödeme & Sipariş**
- **Kapıda Ödeme.** Panel → Ayarlar'da "Kapıda Ödeme'yi Etkinleştir" işaretlenirse, ödeme ekranında kredi kartı seçeneğinin yanına "Kapıda Ödeme ile Sipariş Ver" butonu eklenir. Gerçek bir ödeme altyapısı gerektirmez, sipariş WhatsApp/Instagram siparişleriyle aynı akışla kaydedilir.
- **Havale/EFT ile ödeme.** Panel → Ayarlar'a banka adı, hesap sahibi ve IBAN girersen (üçü de doluysa), ödeme ekranında bilgiler kutucuk halinde (kopyalama butonuyla birlikte) gösterilir ve "Havale/EFT ile Sipariş Ver" seçeneği açılır.
- **Tekrar Sipariş Ver.** Hesabım → Siparişlerim'de her siparişin altına eklendi; tıklayınca o siparişteki ürünleri (hâlâ satıştaysa) tek tıkla sepete ekler. Stoğu tükenmiş/kaldırılmış ürünler varsa, sadece satıştaki ürünleri ekleyip müşteriyi bilgilendirir.
- **Görsel sipariş takip çubuğu.** Sipariş durumu artık düz bir etiket yerine "Alındı → Hazırlanıyor → Kargoda → Teslim Edildi" adımlarını gösteren bir ilerleme çubuğuyla gösteriliyor; iptal edilen siparişler ayrı bir rozetle işaretleniyor.

**Satış & Sadakat**
- **Sadakat seviyeleri (Gümüş/Altın).** Panel → Ayarlar'da belirlediğin ömür boyu harcama eşiklerine göre müşteriler otomatik Gümüş veya Altın üye rozeti kazanıyor (Hesabım'daki sadakat kartında görünür); Altın üyeler ayrıca her siparişte otomatik ücretsiz kargo ayrıcalığı kazanıyor.
- **Dijital Hediye Çeki.** Footer'a "Hediye Çeki" linki eklendi; ziyaretçi 250-1000₺ arası bir tutar seçip e-postasını bırakarak talep oluşturabiliyor. Talepler Panel → Pazarlama → "Hediye Çeki Talepleri"nde listeleniyor; ödeme WhatsApp/Instagram üzerinden elle onaylandıktan sonra mevcut Kuponlar sekmesinden gerçek kodu oluşturup gönderiyorsun.
- **Favoride fiyat düşünce bildirim.** Bir ürünü favoriye eklediğin andaki fiyat tarayıcında saklanıyor; bir sonraki ziyaretinde fiyat gerçekten düşmüşse site otomatik bir bildirim (toast) gösteriyor. Sunucu/e-posta gerektirmez, tamamen tarayıcı tarafında çalışır.
- **Gerçek kampanya geri sayımı.** Panel → Pazarlama'ya bir kampanya etiketi ve gerçek bir bitiş tarihi/saati girersen, sitenin üstünde canlı saniye saniye işleyen bir geri sayım çubuğu beliriyor. Tarih girilmezse hiçbir şey görünmez — sahte bir "acele et" baskısı hiç uygulanmıyor.

**Güven**
- **"Son 30 günün en düşük fiyatı" şeffaflığı.** Ticaret Bakanlığı'nın Fiyat Etiketi Yönetmeliği'ne uyum için: bir ürünün fiyatı indirimliyse ve gerçek fiyat geçmişi varsa, ürün detayında son 30 gündeki en düşük fiyat da gösteriliyor. Bu özellik öncesi ürünler için veri uydurulmuyor, sadece bundan sonra panelden yapılan fiyat değişiklikleri kaydediliyor.
- **Doğrulanmış Alıcı rozeti.** Bir müşteri yorum yazdığında, o ürünü gerçekten satın alıp almadığı kendi sipariş geçmişinden kontrol ediliyor; gerçekten almışsa yorumunun yanında "✓ Doğrulanmış Alıcı" rozeti çıkıyor. Uydurma değil, gerçek sipariş verisinden hesaplanıyor.
- **Kişisel Ölçü Profili.** Footer'a "Ölçü Profilim" eklendi; müşteri göğüs/bel/kalça ölçülerini bir kere kaydediyor, sonrasında her ürün sayfasında (o ürünün beden aralığıyla eşleşiyorsa) "Sana önerilen beden: M" gibi kişisel bir ipucu otomatik çıkıyor.
- **Stil paylaşım ödülü.** Footer'a "Stilini Paylaş, Ödül Kazan" eklendi; ziyaretçiyi WhatsApp/Instagram üzerinden ürün fotoğrafını paylaşmaya yönlendiren bilgilendirici bir akış — karşılığında ödülü (kupon vb.) sen elle veriyorsun.

**Bildirim & Teknik**
- **Web Push bildirimleri.** Footer'da "Stok/Kampanya Bildirimlerine İzin Ver" butonu belirir (VAPID anahtarı girildiğinde); izin veren ziyaretçilerin cihaz token'ı Firestore'a kaydediliyor. Bildirim göndermek için kod yazmana gerek yok — Firebase Console → Cloud Messaging → "Bildirim Oluştur" ile herkese anlık bildirim yollayabiliyorsun.
- **Sipariş durumu değişince e-posta.** Panel → Siparişler'de bir siparişin durumunu değiştirdiğinde (örn. "Kargoda"), müşteriye otomatik bir bilgilendirme e-postası gönderilmesi altyapısı hazır — aktifleştirmek için EmailJS'te yeni bir şablon oluşturup ID'sini bana vermen gerekiyor (aşağıda detay var).
- **Gelişmiş ürün filtreleme — Kumaş.** Panel → ürün düzenle ekranına opsiyonel bir "Kumaş" alanı eklendi; en az bir üründe kumaş bilgisi girildiğinde mağaza sayfalarında otomatik bir "Kumaşa göre filtrele" seçeneği çıkıyor.
- **Breadcrumb (gezinme yolu) yapısal verisi.** Tüm sayfalara Google'ın arama sonuçlarında "Anasayfa › Kategori" şeklinde kırıntı gezinme yolu göstermesini sağlayan yapısal veri (JSON-LD) eklendi — SEO'ya küçük ama gerçek bir katkı.

**Aktifleştirmek için senin yapman gerekenler:**
- **Kapıda Ödeme / Havale-EFT:** Panel → Ayarlar'dan ilgili checkbox'ı işaretle / IBAN bilgilerini gir.
- **Sipariş durumu e-postası:** EmailJS hesabında ([emailjs.com](https://www.emailjs.com)) alıcısı `{{customer_email}}` olan yeni bir şablon oluştur, şablon ID'sini bana ilet — admin.html'deki `EMAILJS_STATUS_TEMPLATE_ID` sabitine yerleştireceğim.
- **Web Push:** Firebase Console → Proje Ayarları → Cloud Messaging → "Web Push sertifikaları"ndan bir VAPID anahtarı oluştur, Panel → Ayarlar → "Web Push Bildirimleri"ne yapıştır.
- **Firestore kuralları:** Yukarıdaki "ÖNCE BUNU YAP" adımını uygula.

## Daha önceki round (2026-09-03 — büyüme, güven ve analitik paketi)

**ÖNCE BUNU YAP — Firestore kurallarını güncellemeden bu turun bazı özellikleri (arkadaşını davet et, sadakat puanı harcama, ürün soru-cevap, gerçek zamanlı "bakılıyor" göstergesi, kritik stok e-posta uyarısı, kargo takip) çalışmaz:**

1. [console.firebase.google.com](https://console.firebase.google.com) → `sonyacollection-62544` projesi → sol menüden **Firestore Database** → üstte **Rules (Kurallar)** sekmesi.
2. Zip içindeki **firestore-rules.txt** dosyasını aç, içeriğinin TAMAMINI kopyala.
3. Firebase Console'daki kural kutusunun içindekini sil, kopyaladığını yapıştır, sağ üstten **Publish (Yayınla)** butonuna bas.

Kurallara bu turda eklenenler: `coupons` koleksiyonuna kendi kendine (self-service) `REF-` (arkadaşını davet et) ve `LOYAL-` (sadakat puanı harcama) önekli kupon kodu oluşturma izni; yeni `productQuestions/{id}` koleksiyonu (ürün soru-cevap); yeni `productViews/{productId}/viewers/{viewerId}` koleksiyonu ("şu an bakılıyor" göstergesi); yeni `settings/lowStockAlertMeta` belgesi (kritik stok e-posta uyarısının günde en fazla 1 e-postayla sınırlanması için); ve `orders/{orderId}` güncelleme kuralına artık `trackingCarrier`/`trackingCode` alanlarının da (yalnızca yönetici tarafından, tutar/ürünlere dokunmadan) eklenebilir olması (kargo takip).

Bu turda 8 mağaza sayfası + admin.html güncellendi, 3 de yeni sayfa eklendi (**404.html, sss.html, stil-rehberi.html**) — 16 yeni özellik:

**Satış & Dönüşüm**
- **Set/kombin indirimi.** Panel → Ayarlar'da bir ürün adedi eşiği ve bir yüzde girersen (varsayılan kapalı), sepetteki ürün sayısı o eşiğe ulaştığında toplam tutara otomatik bir indirim uygulanır; eşiğe daha ulaşmamış müşteriye sepette "X ürün daha ekle, %Y indirim kazan" şeklinde nazik bir hatırlatma (bundle nudge) gösterilir.
- **Arkadaşını Davet Et.** Hesabım sayfasına bir "referans kartı" eklendi — her müşterinin kendi uid'ine bağlı, sabit bir `REF-` kodu var; "Paylaş" butonuyla linkini (telefonda paylaşma menüsü, bilgisayarda kopyala-yapıştır ile) gönderebiliyor. Linkle gelen arkadaş siteye girdiğinde kod otomatik hatırlanır ve ilk siparişinde %10 indirim olarak uygulanır.
- **Sadakat puanı harcama.** Hesabım sayfasında, biriken sadakat puanı belli bir eşiği (20 puan) geçince "Puanı 50₺ Kupona Çevir" butonu aktifleşiyor; tıklayınca kendi hesabına özel, tek kullanımlık 50₺'lik bir `LOYAL-` kuponu oluşturuluyor ve harcanan puan düşülüyor.
- **Gerçek zamanlı "şu an bakılıyor" göstergesi.** Ürün detayında, o ürüne o an başka kaç ziyaretçinin baktığını gösteren küçük bir etiket var. Uydurma bir sayı değil: her ziyaretçi modalı açtığında Firestore'a 20 saniyede bir tazelenen bir "buradayım" kaydı düşülüyor, modal kapanınca siliniyor; sayaç sadece son 45 saniye içinde tazelenmiş kayıtları sayıyor, o yüzden sekme aniden kapanan bir "hayalet" ziyaretçi gösterilmiyor.
- **Güven rozetleri.** Footer'a ve ödeme ekranına "Güvenli Alışveriş", "Kolay İade & Değişim", "KVKK Korumalı Veri" rozetleri eklendi — dünya çapındaki büyük moda sitelerinin çoğunda olan, satın alma kararını kolaylaştıran görsel bir güven unsuru.

**Güven & Satış Sonrası**
- **Kargo takip.** Sipariş üzerine bir kargo firması seçip takip numarası girdiğinde (Panel → Siparişler), müşterinin Hesabım → Siparişlerim ekranında o firmanın kendi sorgu sayfasına giden bir "Kargomu Takip Et" linki beliriyor (Yurtiçi, Aras, MNG, PTT, Sürat Kargo tanınıyor; bilinmeyen/boş firma seçilirse sadece metin gösteriliyor).
- **Sipariş makbuzu (PDF).** Hesabım → Siparişlerim'de her siparişin altına "Makbuz İndir (PDF)" butonu eklendi; ürünler, tutar, kupon ve tarih bilgisini içeren sade bir PDF makbuz indiriyor. Resmi bir e-fatura değil (sunucu tarafı bir fatura sistemi yok), sadece müşterinin kendi kaydı için pratik bir özet.
- **Ürün soru-cevap.** Ürün detayına, yorumların yanına bir Soru-Cevap bölümü eklendi — ziyaretçi ürünle ilgili soru sorabiliyor, sen Panel → yeni **Soru-Cevap** sekmesinden (bekleyen soru sayısı bir rozetle gösteriliyor) yanıtlıyorsun; yanıtlanan sorular herkese görünür oluyor, yanıtlanmamışlar "Yanıt bekleniyor…" olarak listeleniyor.

**Analitik & Pazarlama**
- **Google Analytics (GA4) entegrasyonu.** Panel → Ayarlar → "Analitik & Reklam" bölümüne kendi GA4 Ölçüm Kimliğini (Measurement ID) girersen devreye giriyor; sayfa görüntüleme, sepete ekleme, ödeme başlatma ve satın alma olayları otomatik gönderiliyor. Boş bırakırsan GA hiç yüklenmiyor, siteye hiçbir ek yük binmiyor.
- **Meta Pixel entegrasyonu.** Aynı Ayarlar bölümüne Meta Pixel ID'ni girersen (Facebook/Instagram reklamları için) devreye giriyor, aynı olay setini (ViewContent, AddToCart, InitiateCheckout, Purchase) Meta'ya da gönderiyor. Boş bırakırsan yüklenmiyor.
- **Panelde satış grafiği.** Panel'in ana ekranına (Genel Bakış), son dönemdeki gerçek sipariş verisine dayalı basit bir satış grafiği eklendi — sahte/örnek veri değil, mevcut siparişlerinden hesaplanıyor.
- **Kritik stok e-posta uyarısı.** Bir ürünün stoğu 3 adedin altına düştüğünde, panele hiç girmesen bile haberin olsun diye **siparis@sonyacollection.com** adresine otomatik bir uyarı e-postası gidiyor. Gerçek bir arka uç/zamanlanmış görev olmadığı için tetikleyici bir ziyaretçinin siteyi açması (sen değil, herhangi bir müşteri) — bu yüzden günde en fazla 1 e-postayla sınırlandı, spam olmasın diye.

**Teknik, SEO & İçerik**
- **404 sayfası.** Artık kırık/yanlış bir linke gidildiğinde çıplak bir tarayıcı hatası yerine, sitenin kendi tasarımıyla uyumlu, anasayfaya/mağazaya kolayca dönebileceğin şık bir "sayfa bulunamadı" ekranı çıkıyor (bkz. aşağıdaki `.htaccess` notu).
- **SSS (Sıkça Sorulan Sorular) sayfası.** Kargo, iade/değişim, beden, ödeme gibi en sık sorulan sorulara yanıt veren yeni bir `sss.html` sayfası eklendi, footer'dan link veriliyor.
- **Stil rehberi sayfası.** Parçaları nasıl kombinleyeceğine dair içerik/ilham sayfası (`stil-rehberi.html`), footer'dan link veriliyor — dünya çapındaki büyük moda sitelerinde yaygın olan, kararsız ziyaretçiyi satın almaya yönlendiren bir içerik türü.
- **Performans: Cloudinary görsel optimizasyonu.** Cloudinary'den gelen tüm ürün fotoğrafları artık ziyaretçinin tarayıcısına göre otomatik en uygun format (WebP/AVIF) ve kalitede sunuluyor — dosya boyutu genelde %30-60 küçülüyor, gözle fark edilir bir kalite kaybı olmuyor. Cloudinary dışındaki (eski) fotoğraflar olduğu gibi bırakıldı.

**Bilerek admin-destekli bıraktığımız bir şey — Arkadaşını Davet Et'in ödül tarafı:** Davet edilen arkadaşın kazandığı %10 indirim tamamen otomatik (linkle gelip ilk siparişini verince kendiliğinden uygulanıyor). Ama davet edenin kendi ödülü otomatik verilmiyor — bunun yerine Panel → Siparişler ekranında `REF-` ile başlayan bir kupon kodu kullanılan siparişler artık bir rozetle vurgulanıyor; bu siparişleri gördükçe davet edene ödülünü (örn. yeni bir kupon oluşturarak) elle veriyorsun. Nedeni: iki taraflı bir ödülü, kötüye kullanımı (birinin kendi kendine sınırsız kupon/ödül üretmesini) engelleyecek bir sunucu tarafı doğrulama olmadan tam otomatikleştirmek güvenli değil — küçük bir mağaza için bu admin-onaylı ara adım daha sağlıklı bir çözüm.

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
