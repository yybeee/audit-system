const CACHE_NAME = "audit-system-v1";
const urlsToCache = [
    "/",
    "/dashboard",
    "/reports",
    "/login",
    "/offline.html",
    "/icon-192.png",
    "/icon-512.png",
    "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css",
    "https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css",
    "https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap",
    "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js",
];

// Install Service Worker
self.addEventListener("install", (event) => {
    console.log("[Service Worker] Installing...");
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("[Service Worker] Caching app shell");
                return cache.addAll(
                    urlsToCache.map(
                        (url) =>
                            new Request(url, { credentials: "same-origin" }),
                    ),
                );
            })
            .catch((err) => console.log("[Service Worker] Cache failed:", err)),
    );
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener("activate", (event) => {
    console.log("[Service Worker] Activating...");
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log(
                            "[Service Worker] Deleting old cache:",
                            cacheName,
                        );
                        return caches.delete(cacheName);
                    }
                }),
            );
        }),
    );
    return self.clients.claim();
});

// Fetch Strategy: Network First, falling back to Cache
self.addEventListener("fetch", (event) => {
    // Skip cross-origin requests
    if (
        !event.request.url.startsWith(self.location.origin) &&
        !event.request.url.includes("cdn.jsdelivr.net") &&
        !event.request.url.includes("fonts.googleapis.com")
    ) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Don't cache non-successful responses
                if (
                    !response ||
                    response.status !== 200 ||
                    response.type === "error"
                ) {
                    return response;
                }

                // Clone the response
                const responseToCache = response.clone();

                // Cache successful responses
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseToCache);
                });

                return response;
            })
            .catch(() => {
                // Network failed, try cache
                return caches.match(event.request).then((response) => {
                    if (response) {
                        return response;
                    }

                    // If it's a navigation request and not in cache, show offline page
                    if (event.request.mode === "navigate") {
                        return caches.match("/offline.html");
                    }

                    return new Response("Offline - Content not available", {
                        status: 503,
                        statusText: "Service Unavailable",
                        headers: new Headers({
                            "Content-Type": "text/plain",
                        }),
                    });
                });
            }),
    );
});

// Background Sync (untuk submit form ketika offline)
self.addEventListener("sync", (event) => {
    if (event.tag === "sync-reports") {
        event.waitUntil(syncReports());
    }
});

async function syncReports() {
    // Implementasi sync data ketika kembali online
    console.log("[Service Worker] Syncing reports...");
}

// Push Notification
self.addEventListener("push", (event) => {
    const options = {
        body: event.data ? event.data.text() : "New notification",
        icon: "/icon-192.png",
        badge: "/icon-72.png",
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1,
        },
        actions: [
            {
                action: "explore",
                title: "Lihat",
                icon: "/icon-72.png",
            },
            {
                action: "close",
                title: "Tutup",
                icon: "/icon-72.png",
            },
        ],
    };

    event.waitUntil(
        self.registration.showNotification("Audit System", options),
    );
});

// Notification Click
self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    if (event.action === "explore") {
        event.waitUntil(clients.openWindow("/"));
    }
});
