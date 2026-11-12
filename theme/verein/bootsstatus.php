<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Zustand der Boote für Plan und Formular (theme.js)
//
//   { "5": { "gesperrt_bis": "2026-11-20", "grund": "Mast gebrochen", "offen": 1 }, ... }
//
// gesperrt_bis/grund: Sperre aus Verwaltung → Boot bearbeiten (sperre.patch),
// nur solange sie gilt. offen: Zahl der offenen Schadensmeldungen im
// Wartungs-Logbuch (nur für angemeldete Mitglieder, wie das Logbuch selbst).
// freigabe: false, wenn das Boot eine Freigabe braucht, die dem angemeldeten
// Mitglied fehlt (nur mit $bb_freigaben). Boote ohne Besonderheit fehlen.
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
header('Cache-Control: private, max-age=60');

$status = [];
if (is_file('bb_sperre.inc'))
{
  require_once 'bb_sperre.inc';
  $res = db()->query("SELECT id FROM " . _tbl('room') . " WHERE disabled=0");
  while (false !== ($row = $res->next_row_keyed()))
  {
    $s = bb_sperre((int) $row['id']);
    if (isset($s))
    {
      $status[(string) $row['id']] = ['gesperrt_bis' => $s['bis'], 'grund' => $s['grund']];
    }
  }
}

// Boote, für die dem angemeldeten Mitglied die Freigabe fehlt (freigabe.patch,
// nur wenn $bb_freigaben eingeschaltet ist)
$ich = session()->getCurrentUser();
if ($ich !== null && is_file('bb_freigabe.inc'))
{
  require_once 'bb_freigabe.inc';
  if (bb_freigaben_an())
  {
    $res = db()->query("SELECT id FROM " . _tbl('room') . " WHERE disabled=0");
    while (false !== ($row = $res->next_row_keyed()))
    {
      if (!bb_freigabe_hat((int) $row['id'], $ich->username))
      {
        $status[(string) $row['id']]['freigabe'] = false;
      }
    }
  }
}

if (session()->getCurrentUser() !== null)
{
  try
  {
    $res = db()->query("SELECT room_id, COUNT(*) AS n FROM " . _tbl('bb_wartung') .
                       " WHERE art='meldung' AND erledigt=0 GROUP BY room_id");
    while (false !== ($row = $res->next_row_keyed()))
    {
      $status[(string) $row['room_id']]['offen'] = (int) $row['n'];
    }
  }
  catch (\Throwable $e)
  {
    // kein Wartungs-Logbuch (sql/wartung.sql fehlt)
  }
}

echo json_encode((object) $status, JSON_UNESCAPED_UNICODE);
