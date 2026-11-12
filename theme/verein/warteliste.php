<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Warteliste einer Buchung für die Buchungsansicht (theme.js)
//
//   Themes/verein/warteliste.php?id=123
//   { "aktiv": true, "voll": true, "ich": "tina", "angemeldet": false,
//     "leitung": false, "platz": 2, "anzahl": 3, "namen": [...] }
//
// namen nur für den Schiffsführer/Segellehrer und Admins. Gibt es freie Plätze
// und jemanden auf der Liste (z. B. weil die Platzzahl erhöht wurde), rückt
// er dabei gleich nach. Eintragen/Austragen über registration_handler.php
// (warteliste.patch).
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

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-store');

$user = session()->getCurrentUser();
$id = (int) ($_GET['id'] ?? 0);
if ($user === null || $id <= 0 || !is_file('bb_warteliste.inc'))
{
  echo json_encode(['aktiv' => false]);
  exit;
}
require_once 'bb_warteliste.inc';
if (!bb_warteliste_da())
{
  echo json_encode(['aktiv' => false]);
  exit;
}

$data = get_booking_info($id, false);
if (empty($data) || empty($data['allow_registration']) || ((int) $data['end_time'] < time()))
{
  echo json_encode(['aktiv' => false]);
  exit;
}
if (bb_warteliste_frei($data) !== 0 && bb_warteliste($id))
{
  bb_warteliste_nachruecken($id);
  $data = get_booking_info($id, false);
}

$liste = bb_warteliste($id);
$platz = null;
foreach ($liste as $i => $name)
{
  if (compare_usernames($name, $user->username) === 0)
  {
    $platz = $i + 1;
  }
}
$leitung = (compare_usernames($user->username, $data['create_by']) === 0) || ($user->level >= 2);
$namen = [];
if ($leitung)
{
  foreach ($liste as $name)
  {
    $u = auth()->getUser($name);
    $namen[] = (isset($u) && $u->display_name !== '') ? $u->display_name : $name;
  }
}

echo json_encode([
  'aktiv'      => true,
  'voll'       => bb_warteliste_frei($data) === 0,
  'ich'        => $user->username,
  'angemeldet' => in_arrayi($user->username, array_column($data['registrants'] ?? [], 'username')),
  'leitung'    => $leitung,
  'schiffsfuehrer' => compare_usernames($user->username, $data['create_by']) === 0,
  'platz'      => $platz,
  'anzahl'     => count($liste),
  'namen'      => $namen,
], JSON_UNESCAPED_UNICODE);
