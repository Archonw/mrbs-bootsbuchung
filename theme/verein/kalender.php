<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – persönliches Kalender-Abo (iCalendar)
//
//   Themes/verein/kalender.php?link=1          (angemeldet) liefert den
//                                              persönlichen Abo-Link als JSON
//   Themes/verein/kalender.php?u=NAME&t=TOKEN   der Kalender selbst (.ics)
//
// Der Kalender enthält die eigenen Buchungen und die Fahrten und Kurse, bei
// denen man angemeldet ist (von 30 Tagen zurück bis 180 Tage voraus). Kalender-
// Apps (Google, Apple, Outlook, Thunderbird) holen ihn selbst regelmäßig neu.
//
// Der Token ist ein HMAC über den Benutzernamen mit einem Geheimnis, das beim
// ersten Aufruf zufällig erzeugt und in mrbs_variables (bb_kalender_geheim)
// gespeichert wird. Wird das Geheimnis gelöscht, sind alle Abo-Links ungültig
// und jedes Mitglied holt sich auf „Meine Fahrten“ einen neuen.
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

function bb_kalender_geheim() : string
{
  $tabelle = _tbl('variables');
  $zeile = db()->query("SELECT variable_content FROM $tabelle WHERE variable_name=? LIMIT 1", ['bb_kalender_geheim'])->next_row_keyed();
  $wert = $zeile ? (string) $zeile['variable_content'] : '';
  if (strlen($wert) >= 32)
  {
    return $wert;
  }
  $neu = bin2hex(random_bytes(32));
  db()->command("INSERT INTO $tabelle (variable_name, variable_content) VALUES (?, ?)", ['bb_kalender_geheim', $neu]);
  return $neu;
}

function bb_kalender_token(string $name) : string
{
  return substr(hash_hmac('sha256', 'kalender|' . $name, bb_kalender_geheim()), 0, 32);
}

function bb_kalender_basis() : string
{
  global $bb_url;
  if (!empty($bb_url))
  {
    return rtrim((string) $bb_url, '/') . '/';
  }
  // url_base() zeigt hier auf Themes/verein/, gebraucht wird die Startseite
  return preg_replace('#Themes/[^/]+/$#', '', url_base());
}

// --- Link für das angemeldete Mitglied -----------------------------------------
if (isset($_GET['link']))
{
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: private, no-store');
  $user = session()->getCurrentUser();
  if ($user === null)
  {
    http_response_code(403);
    echo json_encode(['error' => 'nicht angemeldet']);
    exit;
  }
  $url = bb_kalender_basis() . 'Themes/verein/kalender.php?u=' . rawurlencode($user->username) .
         '&t=' . bb_kalender_token($user->username);
  echo json_encode(['url' => $url, 'webcal' => preg_replace('#^https?://#', 'webcal://', $url)], JSON_UNESCAPED_SLASHES);
  exit;
}

// --- Der Kalender -------------------------------------------------------------
$name  = (string) ($_GET['u'] ?? '');
$token = (string) ($_GET['t'] ?? '');
if ($name === '' || !hash_equals(bb_kalender_token($name), $token))
{
  http_response_code(403);
  header('Content-Type: text/plain; charset=utf-8');
  echo "Ungültiger Kalender-Link. Bitte auf der Seite „Meine Fahrten“ einen neuen holen.\n";
  exit;
}

$jetzt = time();
$sql = "SELECT E.id, E.start_time, E.end_time, E.name, E.create_by, E.allow_registration,
               E.kurs, E.timestamp, R.room_name,
               (SELECT COUNT(*) FROM " . _tbl('participants') . " P WHERE P.entry_id=E.id AND P.username=?) AS angemeldet
          FROM " . _tbl('entry') . " E
          JOIN " . _tbl('room') . " R ON R.id=E.room_id
         WHERE E.end_time > ? AND E.start_time < ?
           AND (E.create_by=? OR E.id IN (SELECT entry_id FROM " . _tbl('participants') . " WHERE username=?))
         ORDER BY E.start_time";
$res = db()->query($sql, [$name, $jetzt - 30 * 86400, $jetzt + 180 * 86400, $name, $name]);

function bb_ics_text(string $t) : string
{
  $t = str_replace(["\\", ";", ",", "\r\n", "\n"], ["\\\\", "\;", "\\,", "\\n", "\\n"], $t);
  // Zeilen nach 74 Zeichen falten (RFC 5545), ohne UTF-8-Zeichen zu zerteilen
  $zeilen = [];
  while (strlen($t) > 74)
  {
    $teil = mb_strcut($t, 0, 74, 'UTF-8');
    $zeilen[] = $teil;
    $t = substr($t, strlen($teil));
  }
  $zeilen[] = $t;
  return implode("\r\n ", $zeilen);
}

$verein = trim(strip_tags((string) ($mrbs_company ?? '')));
$host = parse_url(bb_kalender_basis(), PHP_URL_HOST) ?: 'bootsbuchung';
$z = [];
$z[] = 'BEGIN:VCALENDAR';
$z[] = 'VERSION:2.0';
$z[] = 'PRODID:-//Bootsbuchung für Vereine//DE';
$z[] = 'CALSCALE:GREGORIAN';
$z[] = 'METHOD:PUBLISH';
$z[] = 'X-WR-CALNAME:' . bb_ics_text('Bootsbuchung' . ($verein !== '' ? ' ' . $verein : ''));
$z[] = 'X-PUBLISHED-TTL:PT1H';
$z[] = 'REFRESH-INTERVAL;VALUE=DURATION:PT1H';
while (false !== ($row = $res->next_row_keyed()))
{
  $eigene = ($row['create_by'] === $name);
  $art = !empty($row['kurs']) ? 'Kurs' : (!empty($row['allow_registration']) ? 'Mitsegeln' : 'Buchung');
  $titel = $row['room_name'] . ($eigene ? '' : ' – ' . $art . ' bei ' . $row['name']);
  if ($eigene && $art !== 'Buchung')
  {
    $titel .= ' (' . $art . ')';
  }
  $z[] = 'BEGIN:VEVENT';
  $z[] = 'UID:bootsbuchung-' . (int) $row['id'] . '@' . $host;
  $z[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
  $z[] = 'DTSTART:' . gmdate('Ymd\THis\Z', (int) $row['start_time']);
  $z[] = 'DTEND:' . gmdate('Ymd\THis\Z', (int) $row['end_time']);
  $z[] = 'SUMMARY:' . bb_ics_text($titel);
  $z[] = 'URL:' . bb_kalender_basis() . 'view_entry.php?id=' . (int) $row['id'];
  $z[] = 'DESCRIPTION:' . bb_ics_text(($eigene ? 'Deine Buchung' : 'Du bist angemeldet') . "\n" .
                                       bb_kalender_basis() . 'view_entry.php?id=' . (int) $row['id']);
  $z[] = 'END:VEVENT';
}
$z[] = 'END:VCALENDAR';

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="bootsbuchung.ics"');
header('Cache-Control: private, max-age=900');
echo implode("\r\n", $z) . "\r\n";
