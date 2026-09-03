// Sonya Collection — Firebase Cloud Messaging arka plan bildirim işleyicisi.
// Bu dosya, sekme kapalıyken/arka plandayken gelen bildirimleri göstermek için
// Firebase'in kendi standardına göre kök dizinde (/firebase-messaging-sw.js) durmalı.
// Ana PWA service worker'ı (sw.js) ile karışmaması için tamamen ayrı ve basit tutulur —
// sayfa önbellekleme gibi başka hiçbir işe karışmaz, yalnızca push bildirimlerini gösterir.
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyBBJkyfBH0w9HnhEyxx59V0jZLMcW9Nan0",
  authDomain: "sonyacollection-62544.firebaseapp.com",
  projectId: "sonyacollection-62544",
  storageBucket: "sonyacollection-62544.firebasestorage.app",
  messagingSenderId: "667694882654",
  appId: "1:667694882654:web:15bb2eb5455b2f4678b9cf"
});

var messaging = firebase.messaging();

// Firebase Console'dan "Bildirim Oluştur" ile gönderilen yayınlar zaten kendi
// başlık/gövde/ikonunu taşır — burada ek bir şey yapmaya gerek yok, varsayılan
// davranış (Firebase SDK'sının kendisi) bildirimi gösterir. Sadece bir hata olursa
// sessizce yut, ana siteyi asla etkilemez.
messaging.onBackgroundMessage(function(payload){
  try{
    var data = payload.notification || {};
    self.registration.showNotification(data.title || 'Sonya Collection', {
      body: data.body || '',
      icon: '/icon-192.png'
    });
  }catch(e){ /* sessizce yut */ }
});
