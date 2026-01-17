const CACHE_NAME = 'astro-v1';
const STATIC_ASSETS = [
    '/css/app.css',
    '/css/variables.css',
    '/offline',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch(err => {
                console.log('Cache add failed', err);
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // 1. NETWORK ONLY (Never Cache)
    if (url.pathname.startsWith('/api') || 
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/user') || 
        url.pathname.startsWith('/astrologer') ||
        url.pathname.includes('/dashboard')) {
        event.respondWith(fetch(event.request));
        return;
    }

    // 2. STALE-WHILE-REVALIDATE for Static Assets (CSS, Images, JS)
    if (event.request.destination === 'style' || 
        event.request.destination === 'script' || 
        event.request.destination === 'image') {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                const fetchPromise = fetch(event.request).then((networkResponse) => {
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                    });
                    return networkResponse;
                });
                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // 3. NETWORK FIRST for HTML Pages (Documents)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match('/offline') || caches.match(event.request);
                })
        );
        return;
    }

    event.respondWith(fetch(event.request));
});
