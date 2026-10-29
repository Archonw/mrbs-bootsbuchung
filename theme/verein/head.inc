<?php
declare(strict_types=1);
namespace MRBS;

// Theme "verein": zusätzliche Zeilen im <head> jeder Seite, damit sich die
// Bootsbuchung als App installieren lässt (Android-App, „Zum Startbildschirm“).
// Eingebunden über patches/app-head.patch. Die Dateien liegen in app/, Name
// und Farbe kommen aus app/einstellungen.inc ($bb_app in der config.inc.php).

require_once __DIR__ . '/app/einstellungen.inc';

$bb_app_daten = bb_app_einstellungen();
$bb_app_pfad  = "Themes/$theme/app";
echo '<link rel="manifest" href="' . $bb_app_pfad . '/manifest.php">' . "\n";
echo '<meta name="theme-color" content="' . escape_html($bb_app_daten['farbe']) . '">' . "\n";
echo '<link rel="icon" type="image/png" sizes="192x192" href="' . $bb_app_pfad . '/icon-192.png">' . "\n";
echo '<link rel="apple-touch-icon" href="' . $bb_app_pfad . '/apple-touch-icon.png">' . "\n";
echo '<meta name="apple-mobile-web-app-title" content="' . escape_html($bb_app_daten['kurzname']) . '">' . "\n";
echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
// Service Worker: zeigt offline.html, wenn keine Verbindung besteht
echo "<script>if ('serviceWorker' in navigator) { window.addEventListener('load', function () { " .
     "navigator.serviceWorker.register('$bb_app_pfad/sw.php', { scope: './' }).catch(function () {}); }); }</script>\n";
