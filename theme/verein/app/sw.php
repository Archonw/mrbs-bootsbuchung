<?php
declare(strict_types=1);

// =============================================================================
// Service Worker der App (Theme "verein")
//
// Liegt als PHP-Datei im Theme, damit wir den Header "Service-Worker-Allowed"
// setzen können: Nur so darf ein Service Worker aus Themes/verein/app/ für die
// ganze Bootsbuchung zuständig sein. Eingebunden wird er in head.inc.
//
// Was er tut:
// - Ist das Handy offline, erscheint statt einer Browser-Fehlerseite die Seite
//   offline.html. Buchungsdaten werden bewusst NICHT zwischengespeichert,
//   damit niemand einen veralteten Plan sieht.
// - Push-Nachrichten vom Server (bb_push.inc) als Benachrichtigung anzeigen;
//   ein Tipp darauf öffnet die passende Buchung.
// =============================================================================

// Basisverzeichnis von MRBS = drei Ebenen über dieser Datei (Themes/verein/app/)
$base = dirname((string) $_SERVER['SCRIPT_NAME'], 4);
$base = rtrim(str_replace('\\', '/', $base), '/') . '/';

header('Content-Type: application/javascript; charset=utf-8');
header('Service-Worker-Allowed: ' . $base);
header('Cache-Control: no-cache');
?>
'use strict';

// Bei Änderungen an offline.html hochzählen
const VERSION = 'bb-app-1';
const OFFLINE_URL = 'offline.html';

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(VERSION)
      .then((cache) => cache.addAll([OFFLINE_URL]))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => k !== VERSION).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

// Nur Seitenaufrufe abfangen: erst das Netz, bei Fehler die Offline-Seite
self.addEventListener('fetch', (event) => {
  if (event.request.mode !== 'navigate') {
    return;
  }
  event.respondWith(
    fetch(event.request).catch(() => caches.match(OFFLINE_URL, { cacheName: VERSION }))
  );
});

// Push vom Server: { title, body, url }
self.addEventListener('push', (event) => {
  let data = {};
  try {
    data = event.data ? event.data.json() : {};
  } catch (e) {
    data = { body: event.data ? event.data.text() : '' };
  }
  event.waitUntil(
    self.registration.showNotification(data.title || 'Bootsbuchung', {
      body: data.body || '',
      icon: 'icon-192.png',
      badge: 'badge-96.png',
      lang: 'de',
      data: { url: data.url || self.registration.scope }
    })
  );
});

// Tipp auf die Nachricht: offenes Fenster der App nutzen, sonst neues öffnen
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const url = event.notification.data && event.notification.data.url
    ? event.notification.data.url
    : self.registration.scope;
  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((fenster) => {
      for (const f of fenster) {
        if ('navigate' in f && f.url.startsWith(self.registration.scope)) {
          return f.focus().then(() => f.navigate(url));
        }
      }
      return self.clients.openWindow(url);
    })
  );
});
