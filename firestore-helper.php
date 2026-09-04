<?php
/**
 * Firestore'a PHP tarafından salt-okunur erişim (REST API üzerinden).
 * Sadece herkese açık okumaya izin verilen koleksiyonlar için kullanılır
 * (products: allow read: if true; coupons: allow get: if true — bkz.
 * firestore-rules.txt). Bu yüzden hiçbir servis hesabı / gizli anahtar
 * gerekmiyor; ürün fiyatı ve kupon indirimi gibi PARA ile ilgili değerleri
 * müşteri tarayıcısından gelen veriye güvenmeden burada tekrar hesaplıyoruz.
 */

function firestoreDecodeValue($v){
  if(!is_array($v)) return null;
  if(array_key_exists('stringValue', $v)) return $v['stringValue'];
  if(array_key_exists('integerValue', $v)) return (int)$v['integerValue'];
  if(array_key_exists('doubleValue', $v)) return (float)$v['doubleValue'];
  if(array_key_exists('booleanValue', $v)) return (bool)$v['booleanValue'];
  if(array_key_exists('nullValue', $v)) return null;
  if(array_key_exists('mapValue', $v)) return firestoreDecodeFields($v['mapValue']['fields'] ?? []);
  if(array_key_exists('arrayValue', $v)){
    $out = [];
    foreach(($v['arrayValue']['values'] ?? []) as $item){ $out[] = firestoreDecodeValue($item); }
    return $out;
  }
  return null;
}

function firestoreDecodeFields($fields){
  $out = [];
  foreach(($fields ?: []) as $key => $val){ $out[$key] = firestoreDecodeValue($val); }
  return $out;
}

function firestoreHttpGet($url){
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
  ]);
  $body = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  if($body === false || $err){ throw new Exception('Firestore isteği başarısız: ' . $err); }
  if($status < 200 || $status >= 300){ throw new Exception('Firestore HTTP ' . $status . ': ' . $body); }
  $decoded = json_decode($body, true);
  if($decoded === null){ throw new Exception('Firestore yanıtı okunamadı.'); }
  return $decoded;
}

/**
 * Tüm ürünleri döner: [ ['id'=>1, 'name'=>'...', 'price'=>1290, 'stock'=>5,
 * 'variantStock'=>[...] veya null], ... ]. Basit tutmak için sayfalama
 * yapmıyoruz — küçük bir mağaza kataloğu için tek istekte (Firestore'un
 * varsayılan sayfa boyutu genelde yeterli) tüm koleksiyon dönüyor.
 */
function fetchAllProductsFromFirestore(){
  $url = 'https://firestore.googleapis.com/v1/projects/' . FIRESTORE_PROJECT_ID . '/databases/(default)/documents/products?pageSize=300';
  $products = [];
  $pageToken = null;
  do {
    $pageUrl = $url . ($pageToken ? '&pageToken=' . urlencode($pageToken) : '');
    $data = firestoreHttpGet($pageUrl);
    foreach(($data['documents'] ?? []) as $doc){
      $fields = firestoreDecodeFields($doc['fields'] ?? []);
      if(isset($fields['id']) && isset($fields['name'])){
        $products[] = $fields;
      }
    }
    $pageToken = $data['nextPageToken'] ?? null;
  } while($pageToken);
  return $products;
}

/**
 * Site ayarlarını (settings/site) döner — kargo ücreti ve ücretsiz kargo eşiği
 * için gerekli. Bu belge herkese açık okumaya izinli (firestore-rules.txt →
 * match /settings/{settingId} { allow read: if true }), o yüzden burada da
 * servis hesabı gerekmiyor. Okunamazsa boş dizi döner: o durumda kargo ücreti
 * 0 kabul edilir, yani müşteriden asla fazla para çekilmez.
 */
function fetchSiteSettingsFromFirestore(){
  $url = 'https://firestore.googleapis.com/v1/projects/' . FIRESTORE_PROJECT_ID . '/databases/(default)/documents/settings/site';
  try {
    $data = firestoreHttpGet($url);
  } catch (Exception $e){
    return [];
  }
  if(!isset($data['fields'])) return [];
  return firestoreDecodeFields($data['fields']);
}

/** Tek bir kupon kodunu getirir (doc id = normalize edilmiş kod), yoksa null. */
function fetchCouponFromFirestore($code){
  $docId = rawurlencode(strtoupper(trim($code)));
  $url = 'https://firestore.googleapis.com/v1/projects/' . FIRESTORE_PROJECT_ID . '/databases/(default)/documents/coupons/' . $docId;
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
  ]);
  $body = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if($status === 404) return null;
  if($status < 200 || $status >= 300 || $body === false) return null;
  $decoded = json_decode($body, true);
  if(!$decoded || !isset($decoded['fields'])) return null;
  return firestoreDecodeFields($decoded['fields']);
}
