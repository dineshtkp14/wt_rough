const CACHE_NAME = 'wt-vat-shell-v1';
const STATIC_ASSETS = [
  '/offline.html',
  '/assets/css/app.css',
  '/assets/css/modern-sidebar.css',
  '/assets/css/modern-stock.css',
  '/assets/js/common.js',
  '/assets/js/script.js',
  '/assets/images/favicon_white.png',
  '/assets/images/logo.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;

  if (event.request.mode === 'navigate') {
    event.respondWith(fetch(event.request).catch(() => caches.match('/offline.html')));
    return;
  }

  event.respondWith(
    caches.match(event.request).then(cached => cached || fetch(event.request).then(response => {
      if (response.ok && (url.pathname.startsWith('/assets/') || url.pathname === '/manifest.webmanifest')) {
        const copy = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
      }
      return response;
    }))
  );
});

// Push notification handling is ready for a future server subscription endpoint.
self.addEventListener('push', event => {
  const data = event.data ? event.data.json() : {};
  event.waitUntil(self.registration.showNotification(data.title || 'WT VAT System', {
    body: data.body || 'You have a new business notification.',
    icon: '/assets/images/logo.png',
    badge: '/assets/images/favicon_white.png',
    data: data.url || '/'
  }));
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data || '/'));
});
