<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Saisonstatistik je Boot (nur Admins), für die Verwaltung
//
//   Themes/verein/statistik.php?jahr=2026
//
// { "jahr": 2026, "saison": {"beginn": "2026-04-16", "ende": "2026-10-30"},
//   "jahre": [2025, 2026],
//   "boote": [ { "name": "Pirat", "buchungen": 41, "stunden": 96.5,
//                "auslastung": 9, "kurse": 6, "mitsegler": 12,
//                "schaeden": 2, "offen": 1 }, ... ] }
//
// auslastung = gebuchte Stunden / buchbare Stunden der Saison in Prozent
// (Saisontage × Stunden pro Tag laut morningstarts/eveningends). Ohne Saison
// für dieses Jahr gilt 1. April bis 31. Oktober.
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
require_once __DIR__ . '/regeln.inc';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-store');

$user = session()->getCurrentUser();
if ($user === null || $user->level < 2)
{
  http_response_code(403);
  echo json_encode(['error' => 'nur für Admins']);
  exit;
}

$jahr = (int) ($_GET['jahr'] ?? date('Y'));
if ($jahr < 2000 || $jahr > 2100)
{
  $jahr = (int) date('Y');
}

$r = bb_regeln();
$beginn = $r['saison']['beginn'] ?? '';
$ende   = $r['saison']['ende'] ?? '';
if (substr($beginn, 0, 4) !== (string) $jahr) { $beginn = "$jahr-04-01"; }
if (substr($ende, 0, 4) !== (string) $jahr)   { $ende = "$jahr-10-31"; }
$von = (new \DateTimeImmutable($beginn))->getTimestamp();
$bis = (new \DateTimeImmutable($ende))->modify('+1 day')->getTimestamp();
$tage = max(1, (int) round(($bis - $von) / 86400));
$stunden_pro_tag = 12;
if (isset($r['tag']))
{
  [$h1, $m1] = array_map('intval', explode(':', $r['tag']['von']));
  [$h2, $m2] = array_map('intval', explode(':', $r['tag']['bis']));
  $stunden_pro_tag = max(1, ($h2 * 60 + $m2 - $h1 * 60 - $m1) / 60);
}
$moeglich = $tage * $stunden_pro_tag;

$boote = [];
$res = db()->query("SELECT id, room_name FROM " . _tbl('room') . " WHERE disabled=0 ORDER BY sort_key, room_name");
while (false !== ($row = $res->next_row_keyed()))
{
  $boote[(int) $row['id']] = ['name' => $row['room_name'], 'buchungen' => 0, 'stunden' => 0.0, 'auslastung' => 0,
                              'kurse' => 0, 'mitsegler' => 0, 'schaeden' => 0, 'offen' => 0];
}

// Buchungen im Saisonzeitraum (auf die Saison zugeschnitten)
$res = db()->query(
  "SELECT E.room_id, COUNT(*) AS n,
          SUM(LEAST(E.end_time, ?) - GREATEST(E.start_time, ?)) AS sek,
          SUM(E.kurs = 1) AS kurse
     FROM " . _tbl('entry') . " E
    WHERE E.end_time > ? AND E.start_time < ?
    GROUP BY E.room_id",
  [$bis, $von, $von, $bis]);
while (false !== ($row = $res->next_row_keyed()))
{
  $id = (int) $row['room_id'];
  if (!isset($boote[$id])) { continue; }
  $boote[$id]['buchungen'] = (int) $row['n'];
  $boote[$id]['stunden'] = round((float) $row['sek'] / 3600, 1);
  $boote[$id]['kurse'] = (int) $row['kurse'];
  $boote[$id]['auslastung'] = round($boote[$id]['stunden'] / $moeglich * 100, 1);
}

// Angemeldete Mitsegler und Kursteilnehmer
$res = db()->query(
  "SELECT E.room_id, COUNT(*) AS n FROM " . _tbl('participants') . " P
     JOIN " . _tbl('entry') . " E ON E.id = P.entry_id
    WHERE E.end_time > ? AND E.start_time < ?
    GROUP BY E.room_id",
  [$von, $bis]);
while (false !== ($row = $res->next_row_keyed()))
{
  if (isset($boote[(int) $row['room_id']])) { $boote[(int) $row['room_id']]['mitsegler'] = (int) $row['n']; }
}

// Schadensmeldungen im Jahr und noch offene
try
{
  $res = db()->query(
    "SELECT room_id, SUM(YEAR(datum) = ?) AS jahr, SUM(erledigt = 0) AS offen
       FROM " . _tbl('bb_wartung') . " WHERE art = 'meldung' GROUP BY room_id",
    [$jahr]);
  while (false !== ($row = $res->next_row_keyed()))
  {
    $id = (int) $row['room_id'];
    if (!isset($boote[$id])) { continue; }
    $boote[$id]['schaeden'] = (int) $row['jahr'];
    $boote[$id]['offen'] = (int) $row['offen'];
  }
}
catch (\Throwable $e)
{
  // kein Wartungs-Logbuch
}

// Jahre mit Buchungen (für die Auswahl)
$jahre = [];
$res = db()->query("SELECT DISTINCT YEAR(FROM_UNIXTIME(start_time)) AS j FROM " . _tbl('entry') . " ORDER BY j");
while (false !== ($row = $res->next_row_keyed()))
{
  $jahre[] = (int) $row['j'];
}
if (!in_array($jahr, $jahre, true)) { $jahre[] = $jahr; sort($jahre); }

echo json_encode([
  'jahr'   => $jahr,
  'saison' => ['beginn' => $beginn, 'ende' => $ende],
  'jahre'  => $jahre,
  'boote'  => array_values($boote),
], JSON_UNESCAPED_UNICODE);
