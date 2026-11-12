<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Freigaben pro Boot verwalten (nur Admins, freigabe.patch)
//
//   GET  Themes/verein/freigaben.php        { "an": true, "boote": [...], "mitglieder": [...] }
//   POST room_id, noetig (0/1), mitglieder[] (Benutzernamen), csrf_token
//
// Nur aktiv mit $bb_freigaben = true; in der config.inc.php, sonst { "an": false }.
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

if (!is_file('bb_freigabe.inc'))
{
  echo json_encode(['an' => false]);
  exit;
}
require_once 'bb_freigabe.inc';
$user = session()->getCurrentUser();
if (!bb_freigaben_an() || $user === null || $user->level < 2)
{
  echo json_encode(['an' => false]);
  exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
{
  \MRBS\Form\Form::checkToken();
  $room_id = (int) ($_POST['room_id'] ?? 0);
  $noetig  = !empty($_POST['noetig']);
  $namen   = array_values(array_filter(array_map('strval', (array) ($_POST['mitglieder'] ?? []))));
  if ($room_id <= 0)
  {
    http_response_code(400);
    echo json_encode(['error' => 'room_id fehlt']);
    exit;
  }
  db()->begin();
  db()->command("DELETE FROM " . _tbl('bb_freigabe_boot') . " WHERE room_id=?", [$room_id]);
  db()->command("DELETE FROM " . _tbl('bb_freigabe') . " WHERE room_id=?", [$room_id]);
  if ($noetig)
  {
    db()->command("INSERT INTO " . _tbl('bb_freigabe_boot') . " (room_id) VALUES (?)", [$room_id]);
  }
  foreach (array_unique($namen) as $name)
  {
    db()->command("INSERT INTO " . _tbl('bb_freigabe') . " (room_id, username) VALUES (?, ?)", [$room_id, $name]);
  }
  db()->commit();
  echo json_encode(['ok' => true]);
  exit;
}

$noetig = [];
$res = db()->query("SELECT room_id FROM " . _tbl('bb_freigabe_boot'));
while (false !== ($row = $res->next_row_keyed()))
{
  $noetig[(int) $row['room_id']] = true;
}
$freigaben = [];
$res = db()->query("SELECT room_id, username FROM " . _tbl('bb_freigabe'));
while (false !== ($row = $res->next_row_keyed()))
{
  $freigaben[(int) $row['room_id']][] = $row['username'];
}
$boote = [];
$res = db()->query("SELECT id, room_name FROM " . _tbl('room') . " WHERE disabled=0 ORDER BY sort_key, room_name");
while (false !== ($row = $res->next_row_keyed()))
{
  $id = (int) $row['id'];
  $boote[] = ['id' => $id, 'name' => $row['room_name'], 'noetig' => isset($noetig[$id]), 'mitglieder' => $freigaben[$id] ?? []];
}
$mitglieder = [];
$res = db()->query("SELECT name, display_name FROM " . _tbl('users') . " ORDER BY display_name, name");
while (false !== ($row = $res->next_row_keyed()))
{
  $mitglieder[] = ['name' => $row['name'], 'anzeige' => ($row['display_name'] !== '' && $row['display_name'] !== null) ? $row['display_name'] : $row['name']];
}
echo json_encode(['an' => true, 'boote' => $boote, 'mitglieder' => $mitglieder], JSON_UNESCAPED_UNICODE);
