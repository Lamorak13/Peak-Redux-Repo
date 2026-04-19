const CACHE_NAME = 'peaks-cache-v1';
const ASSETS = [
  '/',
  './home.php',
  './customer_gate.js',
  './peakscinemastransparent.png' 
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
  );
});

self.addEventListener('fetch', event => {
  if (event.request.url.includes('pc_api.php')) {
    return event.respondWith(fetch(event.request));
  }

  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then(response => {
        return response || caches.match('/offline.html');
      });
    })
  );
});