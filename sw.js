// Sonya Collection — basit Service Worker
// Amaç: siteyi "Ana Ekrana Ekle" ile yüklenebilir (PWA) yapmak ve temel bir
// çevrimdışı deneyim sağlamak. Firebase/Firestore gibi dış kaynaklara asla
// dokunmaz — sadece kendi sitemizin sayfalarını önbelleğe alır.
//
// ÖNEMLİ: Sayfalarda büyük bir güncelleme yaptığında CACHE_NAME'in sonundaki
// sürüm numarasını artır (v1 -> v2 ...) — aksi halde kullanıcılar eski
// önbellekteki sürümü görmeye devam edebilir.
var CACHE_NAME = 'sonya-shell-v8';

var SHELL_FILES = [
  '/',
  '/index.html',
  '/yeni-sezon.html',
  '/indirim.html',
  '/pelerinler.html',
  '/kimonolar.html',
  '/takimlar.html',
  '/elbiseler.html',
  '/hesabim.html',
  '/sss.html',
  '/stil-rehberi.html',
  '/hakkimizda.html',
  '/404.html',
  '/manifest.webmanifest',
  '/icon-192.png',
  '/icon-512.png'
];

self.addEventListener('install', function(event){
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache){
      return cache.addAll(SHELL_FILES).catch(function(){
        // Bir dosya bulunamazsa (ör. deploy henüz tamamlanmadıysa) tüm kurulumu
        // başarısız kılma — sessizce devam et, eksik dosyalar zamanla önbelleğe girer.
      });
    })
  );
});

self.addEventListener('activate', function(event){
  event.waitUntil(
    caches.keys().then(function(names){
      return Promise.all(
        names.filter(function(name){ return name !== CACHE_NAME; })
          .map(function(name){ return caches.delete(name); })
      );
    }).then(function(){ return self.clients.claim(); })
  );
});

self.addEventListener('fetch', function(event){
  var req = event.request;
  if(req.method !== 'GET') return; // POST/PUT vs. asla önbelleklenmez
  var url = new URL(req.url);
  if(url.origin !== self.location.origin) return; // Firebase/Firestore/fontlar vs. — dokunma, tarayıcı normal şekilde halletsin

  if(req.mode === 'navigate'){
    // Sayfa gezinmeleri: önce ağ dene (güncel içerik), olmazsa önbellekten,
    // o da yoksa ana sayfayı göster (tamamen çevrimdışıyken bile site açılsın).
    event.respondWith(
      fetch(req).then(function(res){
        var resClone = res.clone();
        caches.open(CACHE_NAME).then(function(cache){ cache.put(req, resClone); });
        return res;
      }).catch(function(){
        return caches.match(req).then(function(cached){
          return cached || caches.match('/index.html');
        });
      })
    );
    return;
  }

  // Diğer aynı-origin GET istekleri (ikonlar, manifest vb.): önce önbellek, yoksa ağdan al ve önbelleğe ekle.
  event.respondWith(
    caches.match(req).then(function(cached){
      if(cached) return cached;
      return fetch(req).then(function(res){
        var resClone = res.clone();
        caches.open(CACHE_NAME).then(function(cache){ cache.put(req, resClone); });
        return res;
      }).catch(function(){ return cached; });
    })
  );
});
