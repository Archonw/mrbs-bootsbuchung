<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Push-Erinnerung kurz vor der eigenen Fahrt
//
// Wer eine Buchung angelegt hat oder bei einer Fahrt / einem Kurs angemeldet
// ist, bekommt etwa zwei Stunden vorher eine Push-Nachricht aufs Handy:
//   „In 2 Stunden: Pirat“ – „10:00–12:00 · Wind SW 3 Bft, Böen 5“
// Nur Push, keine Mail (wer Push nicht eingeschaltet hat, bekommt nichts).
//
// Nur von der Kommandozeile, alle 15 Minuten per Cron:
//   docker exec mrbs php /var/www/html/Themes/verein/fahrt-erinnerung.php --url=https://buchung.mein-verein.de
// Optionen:
//   --url=...       Adresse der Buchungsseite für den Link in der Nachricht
//   --vorher=120    wie viele Minuten vor dem Beginn (Vorgabe 120)
//   --takt=15       wie oft der Cron läuft, in Minuten (Vorgabe 15)
//   --probe         nichts verschicken, nur anzeigen
// Jede Fahrt fällt genau in ein Zeitfenster [jetzt+vorher, jetzt+vorher+takt),
// deshalb kommt jede Erinnerung genau einmal.
// =============================================================================

if (PHP_SAPI !== 'cli')
{
  http_response_code(404);
  exit;
}

$opts = getopt('', ['url::', 'vorher::', 'takt::', 'probe']);
$bb_url    = isset($opts['url']) ? rtrim((string) $opts['url'], '/') : '';
$bb_vorher = max(1, (int) ($opts['vorher'] ?? 120));
$bb_takt   = max(1, (int) ($opts['takt'] ?? 15));
$bb_probe  = isset($opts['probe']);

$mrbs_root = dirname(__DIR__, 2);
chdir($mrbs_root);
require 'defaultincludes.inc';
if (!is_file('bb_push.inc'))
{
  fwrite(STDERR, "push.patch fehlt – keine Push-Nachrichten möglich.\n");
  exit(1);
}
require_once 'bb_push.inc';
require_once __DIR__ . '/wetter.inc';

if (!bb_push_aktiv() && !$bb_probe)
{
  exit(0);   // Push nicht eingerichtet
}

// auf den Takt gerundet, damit sich die Fenster lückenlos aneinanderreihen
$jetzt = (int) (floor(time() / ($bb_takt * 60)) * $bb_takt * 60);
$von = $jetzt + $bb_vorher * 60;
$bis = $von + $bb_takt * 60;

$res = db()->query(
  "SELECT E.id, E.start_time, E.end_time, E.create_by, R.room_name
     FROM " . _tbl('entry') . " E
     JOIN " . _tbl('room') . " R ON R.id = E.room_id
    WHERE R.disabled = 0 AND E.start_time >= ? AND E.start_time < ?
    ORDER BY E.start_time",
  [$von, $bis]);
$fahrten = [];
while (false !== ($row = $res->next_row_keyed()))
{
  $fahrten[] = $row;
}

$wetter = null;
$basis = ($bb_url !== '' ? $bb_url . '/' : '');
$stunden = intdiv($bb_vorher, 60);
$titel_vorne = ($bb_vorher % 60 === 0)
  ? 'In ' . ($stunden === 1 ? 'einer Stunde' : $stunden . ' Stunden') . ': '
  : 'In ' . $bb_vorher . ' Minuten: ';

foreach ($fahrten as $f)
{
  $leute = [$f['create_by']];
  $p = db()->query("SELECT username FROM " . _tbl('participants') . " WHERE entry_id=?", [(int) $f['id']]);
  while (false !== ($row = $p->next_row_keyed()))
  {
    $leute[] = $row['username'];
  }
  $leute = array_values(array_unique(array_filter($leute)));

  if (!isset($wetter))
  {
    $wetter = bb_wetter_daten() ?: [];
  }
  $ende = (int) $f['end_time'];
  $bis_stunde = (int) date('G', $ende) + ((int) date('i', $ende) > 0 ? 1 : 0);
  if (date('Y-m-d', $ende) !== date('Y-m-d', (int) $f['start_time']))
  {
    $bis_stunde = 24;
  }
  $wind = $wetter ? bb_wetter_zeitraum($wetter, date('Y-m-d', (int) $f['start_time']),
                                       (int) date('G', (int) $f['start_time']), $bis_stunde)
                  : '';
  $nachricht = [
    'title' => $titel_vorne . $f['room_name'],
    'body'  => date('H:i', (int) $f['start_time']) . '–' . date('H:i', (int) $f['end_time']) .
               ($wind !== '' ? ' · Wind ' . $wind : ''),
    'url'   => $basis . 'view_entry.php?id=' . (int) $f['id'],
  ];

  foreach ($leute as $name)
  {
    if ($bb_probe)
    {
      echo "Probe: an $name: {$nachricht['title']} – {$nachricht['body']}\n";
      continue;
    }
    bb_push_an_benutzer($name, $nachricht);
  }
}

if ($bb_probe)
{
  echo 'Zeitfenster ' . date('d.m.Y H:i', $von) . ' bis ' . date('H:i', $bis) . ': ' . count($fahrten) . " Fahrt(en)\n";
}
