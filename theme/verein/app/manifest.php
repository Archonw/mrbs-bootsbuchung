<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – App-Manifest (Name, Farben, Symbole, Startseite)
//
// Als PHP-Datei, damit Name und Farbe aus der config.inc.php kommen
// (siehe einstellungen.inc). Eingebunden in head.inc. Daran erkennt auch die
// Android-App „Bootsbuchung für Vereine“, dass sie hier richtig ist.
// =============================================================================

// MRBS erwartet, im eigenen Web-Verzeichnis zu laufen: drei Ebenen über
// Themes/verein/app/ (wie in kapazitaet.php, dort auch die Symlink-Fälle)
$mrbs_root = null;
foreach ([dirname(__DIR__, 3),
          dirname((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''), 4),
          (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')] as $candidate)
{
  if ($candidate !== '' && is_file("$candidate/defaultincludes.inc"))
  {
    $mrbs_root = $candidate;
    break;
  }
}
if ($mrbs_root === null)
{
  http_response_code(500);
  exit;
}
chdir($mrbs_root);
// Nur die Einstellungen laden – keine Sitzung, kein Login nötig
require_once 'lib/autoload.inc';
require_once 'systemdefaults.inc.php';
require_once 'areadefaults.inc.php';
require_once 'config.inc.php';
require_once __DIR__ . '/einstellungen.inc';

$app = bb_app_einstellungen();

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

// Pfade relativ zu dieser Datei (Themes/verein/app/)
echo json_encode([
  'name'             => $app['name'],
  'short_name'       => $app['kurzname'],
  'description'      => $app['beschreibung'],
  'lang'             => 'de',
  'dir'              => 'ltr',
  'start_url'        => '../../../index.php',
  'scope'            => '../../../',
  'display'          => 'standalone',
  'orientation'      => 'any',
  'background_color' => $app['farbe'],
  'theme_color'      => $app['farbe'],
  'icons'            => [
    ['src' => 'icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
    ['src' => 'icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
    ['src' => 'icon-maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
  ],
  'shortcuts'        => [
    ['name' => 'Mitsegeln & Kurse', 'url' => '../../../bb_mitsegeln.php',
     'icons' => [['src' => 'icon-192.png', 'sizes' => '192x192']]],
  ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
