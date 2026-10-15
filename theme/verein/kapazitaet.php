<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Kapazitäten der Boote für das Buchungsformular (theme.js)
//
// Liefert für angemeldete Benutzer:
//   { "offset": 1, "rooms": { "1": 6, "2": 4, ... }, "segellehrer": true }
// offset = $registrant_capacity_offset aus der config.inc.php (Plätze, die
// z. B. der Schiffsführer belegt). Damit rechnet das Formular mit genau
// denselben Werten wie kapazitaet.patch beim Speichern.
// =============================================================================

// MRBS erwartet, im eigenen Web-Verzeichnis zu laufen. Normalerweise liegt
// das zwei Ebenen über Themes/verein/; die anderen Kandidaten decken ein per
// Symlink eingebundenes Theme ab.
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

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-cache');

$user = session()->getCurrentUser();
if ($user === null)
{
  http_response_code(403);
  echo json_encode(['error' => 'nicht angemeldet']);
  exit;
}

$rooms = [];
$res = db()->query("SELECT id, capacity FROM " . _tbl('room') . " WHERE disabled=0");
while (false !== ($row = $res->next_row_keyed()))
{
  $rooms[(string) $row['id']] = (int) $row['capacity'];
}

// Segellehrer (Spalte aus mrbs/sql/kursbuchung.sql) – Admins zählen mit
$segellehrer = ($user->level >= 2);
if (!$segellehrer)
{
  try
  {
    $segellehrer = (db()->query1("SELECT segellehrer FROM " . _tbl('users') . " WHERE name=? LIMIT 1",
                                 [$user->username]) === 1);
  }
  catch (\Throwable $e)
  {
    // Spalte fehlt – keine Kurse
  }
}

echo json_encode([
  'offset'      => isset($registrant_capacity_offset) ? (int) $registrant_capacity_offset : 0,
  'rooms'       => $rooms,
  'segellehrer' => $segellehrer
]);
