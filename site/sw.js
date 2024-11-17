//const cacheName = 'twig-cache-pwa';
/*const appShellFiles = [
  //'/index.php',
  '/local/templates/nmain/css/main.min.css',
  '/local/templates/nmain/css/index.min.css',
  '/upload/webp/upload/iblock/068/0684d5ef70dba50bcac1a16f90f6bd1d_07fb6ce39032041f0d9e1cbeb08f2fb2.webp',
];*/

// Installing Service Worker
// self.addEventListener('install', (e) => {
//   console.log('[Service Worker] Install');
//   e.waitUntil((async () => {
//     const cache = await caches.open(cacheName);
//     console.log('[Service Worker] Caching all: app shell and content');
//     // await cache.addAll(contentToCache);
// 	await cache.addAll(appShellFiles);
//   })());
// });
// self.addEventListener('fetch', (e) => {
//     // Cache http and https only, skip unsupported chrome-extension:// and file://...
//     if (!(
//       e.request.url.startsWith('http:') || e.request.url.startsWith('https:')
//     )) {
//         return; 
//     }

//   e.respondWith((async () => {
//     const r = await caches.match(e.request);
//     console.log(`[Service Worker] Fetching resource: ${e.request.url}`);
//     if (r) return r;
//     const response = await fetch(e.request);
//     const cache = await caches.open(cacheName);
//     console.log(`[Service Worker] Caching new resource: ${e.request.url}`);
//     cache.put(e.request, response.clone());
//     return response;
//   })());
// });

self.addEventListener('install', function(event) {
    console.log('sw.js install');
});

self.addEventListener('fetch', function(event) {
    console.log('sw.js fetch');
});
