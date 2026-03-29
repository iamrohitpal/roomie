const cacheName = 'Roomie-v1.0.0';
const assetsToCache = [
    '.',
    'manifest.json',
    'icon-192.png',
    'icon-512.png',
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
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Network-First strategy for the app shell and dynamic pages
    // This ensure CSRF tokens and session data stay fresh
    if (event.request.mode === 'navigate' || assetsToCache.includes(url.pathname)) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Update cache for these assets if successful
                    if (response.ok) {
                        const copy = response.clone();
                        caches.open(cacheName).then(cache => cache.put(event.request, copy));
                    }
                    return response;
                })
                .catch(() => caches.match(event.request))
        );
    } else {
        // Cache-First for other assets (images, fonts, etc.)
        event.respondWith(
            caches.match(event.request).then(response => {
                return response || fetch(event.request).then(fetchResponse => {
                    return fetchResponse;
                });
            })
        );
    }
});