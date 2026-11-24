/* Service Worker: guarda recursos estáticos y muestra una página alternativa sin conexión. */
const CACHE_NAME = 'belleza-citas-v2';
const APP_SHELL = [
    './offline.html',
    './manifest.json',
    './assets/css/style.css',
    './assets/images/salon-interior.png',
    './assets/js/app.js',
    './assets/icons/icon-192.png',
    './assets/icons/icon-512.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    if (request.mode === 'navigate') {
        // Las pantallas PHP requieren conexión; si no hay red se presenta el aviso offline.
        event.respondWith(
            fetch(request).catch(() => caches.match('./offline.html'))
        );
        return;
    }

    // Los archivos estáticos se sirven desde caché cuando están disponibles.
    event.respondWith(
        caches.match(request).then((cached) => cached || fetch(request).then((response) => {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
            return response;
        }))
    );
});
