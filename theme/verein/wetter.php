<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Wind und Wetter für den Plan (theme.js)
//
// Vorhersage von Open-Meteo (open-meteo.com, kostenlos, ohne Schlüssel) für
// den Ort aus der config.inc.php:
//
//   $bb_wetter = ['breite' => 51.745, 'laenge' => 7.215, 'ort' => 'Halterner Stausee'];
//
// Ohne diese Zeile ist die Funktion aus. Der Server fragt höchstens einmal
// pro Stunde an und merkt sich das Ergebnis (temporäres Verzeichnis im
// Container); die Browser der Mitglieder fragen nur hier.
//
// Antwort: { "ort": "...", "tage": { "2026-11-12": { "bft": 3, "boeen_bft": 5,
//   "richtung": "SW", "temp": 13, "sonnenuntergang": "16:58",
//   "stunden": { "10": { "bft": 3, "boeen_bft": 4, "richtung": "SW" }, ... } } } }
// =============================================================================

$mrbs_root = null;
foreach ([dirname(__DIR__, 2),
          dirname((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''), 3),
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
require 'defaultincludes.inc';

require_once __DIR__ . '/wetter.inc';

header('Content-Type: application/json; charset=utf-8');

$daten = bb_wetter_daten();
if ($daten === null)
{
  header('Cache-Control: private, max-age=3600');
  echo json_encode(['aus' => true]);
  exit;
}
if (!$daten)
{
  header('Cache-Control: private, max-age=300');
  echo json_encode(['fehler' => true]);
  exit;
}

header('Cache-Control: private, max-age=900');
echo json_encode($daten, JSON_UNESCAPED_UNICODE);
