const cacheName = 'Roomie-v1.0.0';
const assetsToCache = [
    '/',
    '/manifest.json',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js',
    'https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js'
];

self.addEventListener('install', event => {
    console.log('[Service Worker] Installing Service Worker...');
    event.waitUntil(
        caches.open(cacheName).then(cache => {
            console.log('[Service Worker] Pre-caching offline page');
            return cache.addAll(assetsToCache);
        })
    );
});

self.addEventListener('activate', event => {
    console.log('[Service Worker] Activating Service Worker...');
    event.waitUntil(
        caches.keys().then(keyList => {
            return Promise.all(keyList.map(key => {
                if (key !== cacheName) {
                    console.log('[Service Worker] Removing old cache.', key);
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request).then(fetchResponse => {
                // Determine if we should cache this new request
                // In this case, we'll just return it
                return fetchResponse;
            }).catch(() => {
                // If both fail (offline), you can return a fallback page here
                if (event.request.mode === 'navigate') {
                    return caches.match('/');
                }
            });
        })
    );
});