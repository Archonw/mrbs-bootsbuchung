<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Teilnehmerliste zum Drucken
//
// Aufruf: Themes/verein/teilnehmer.php?id=<Buchungs-ID>
// (Knopf "Teilnehmerliste drucken" in der Buchungsansicht, theme.js)
//
// Sehen dürfen die Liste: wer die Buchung angelegt hat, Segellehrer und Admins.
// Sie enthält Namen und E-Mail-Adressen der Angemeldeten, eine Spalte zum
// Abhaken vor Ort und leere Zeilen für Nachzügler bis zur Platzzahl.
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

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: private, no-store');

function bb_tn_fehler(int $code, string $text) : void
{
  http_response_code($code);
  echo '<!doctype html><html lang="de"><meta charset="utf-8"><title>Teilnehmerliste</title>';
  echo '<body style="font:16px system-ui,sans-serif;padding:2rem">' . escape_html($text) . '</body></html>';
  exit;
}

$user = session()->getCurrentUser();
if ($user === null)
{
  bb_tn_fehler(403, 'Bitte zuerst anmelden.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$data = ($id) ? get_booking_info($id, false, true) : null;
if (empty($data) || empty($data['allow_registration']))
{
  bb_tn_fehler(404, 'Diese Buchung gibt es nicht oder sie hat keine Teilnehmer-Anmeldung.');
}

// Berechtigung: Ersteller, Admins, Segellehrer
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
  }
}
if (!$segellehrer && (compare_usernames($user->username, $data['create_by']) !== 0))
{
  bb_tn_fehler(403, 'Die Teilnehmerliste können nur der Ersteller der Buchung, Segellehrer und Admins sehen.');
}

// Daten zusammenstellen
$is_kurs = !empty($data['kurs']);
$leiter  = auth()->getUser($data['create_by']);
$leiter_nm = (isset($leiter) && ($leiter->display_name !== '')) ? $leiter->display_name : $data['create_by'];

$rows = [];
foreach ($data['registrants'] as $r)
{
  $u = auth()->getUser($r['username']);
  $rows[] = [
    'name'  => (isset($u) && ($u->display_name !== '')) ? $u->display_name : $r['username'],
    'email' => (isset($u) && !empty($u->email)) ? $u->email : '',
    'date'  => !empty($r['registered']) ? datetime_format(['pattern' => 'dd.MM.yyyy'], (int) $r['registered']) : ''
  ];
}
usort($rows, function ($a, $b) { return strcasecmp($a['name'], $b['name']); });

$limit = !empty($data['registrant_limit_enabled']) ? (int) $data['registrant_limit'] : 0;
$empty_rows = max($limit - count($rows), 2);

$tag   = datetime_format(['pattern' => 'EEEE, d. MMMM yyyy'], (int) $data['start_time']);
$von   = datetime_format(['pattern' => 'HH:mm'], (int) $data['start_time']);
$bis   = datetime_format(['pattern' => 'HH:mm'], (int) $data['end_time']);
$titel = ($is_kurs ? 'Kurs' : 'Mitsegeln') . ' – ' . $data['room_name'];

?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Teilnehmerliste <?php echo escape_html($titel); ?></title>
<style>
  @font-face { font-family: "Figtree"; src: url("fonts/figtree-latin-400-normal.woff2") format("woff2"); font-weight: 400; }
  @font-face { font-family: "Figtree"; src: url("fonts/figtree-latin-600-normal.woff2") format("woff2"); font-weight: 600; }
  @font-face { font-family: "Figtree"; src: url("fonts/figtree-latin-700-normal.woff2") format("woff2"); font-weight: 700; }
  :root { --text: #121714; --muted: #59635d; --line: #c9d1c8; --accent: #1b7d33; }
  * { box-sizing: border-box; }
  body { margin: 0; padding: 24px 16px; font: 15px/1.45 "Figtree", system-ui, sans-serif; color: var(--text); background: #fff; }
  main { max-width: 820px; margin: 0 auto; }
  header { display: flex; align-items: center; gap: 14px; border-bottom: 3px solid var(--accent); padding-bottom: 12px; }
  header img { height: 34px; }
  header .club { font-size: 13px; color: var(--muted); }
  h1 { font-size: 22px; margin: 0; }
  dl { display: grid; grid-template-columns: max-content 1fr; gap: 4px 16px; margin: 16px 0 20px; }
  dt { color: var(--muted); }
  dd { margin: 0; font-weight: 600; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border-bottom: 1px solid var(--line); padding: 9px 8px; text-align: left; vertical-align: middle; }
  th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); border-bottom: 2px solid var(--text); }
  td.nr { width: 2.5em; color: var(--muted); }
  td.check, th.check { width: 6em; text-align: center; }
  td.check span { display: inline-block; width: 18px; height: 18px; border: 1.5px solid var(--text); border-radius: 3px; }
  td.mail { color: var(--muted); font-size: 13px; word-break: break-all; }
  tr.leer td { height: 2.4em; }
  .foot { margin-top: 18px; color: var(--muted); font-size: 12px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .actions { margin: 0 0 18px; display: flex; gap: 8px; }
  .actions button, .actions a { font: inherit; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line); background: #fff; color: var(--text); text-decoration: none; cursor: pointer; }
  .actions button { background: var(--accent); border-color: var(--accent); color: #fff; font-weight: 600; }
  @media (max-width: 560px) { td.mail, th.mail { display: none; } }
  @media print {
    body { padding: 0; font-size: 12pt; }
    .actions { display: none; }
    td.mail, th.mail { display: table-cell; }
    tr { break-inside: avoid; }
    @page { margin: 15mm; }
  }
</style>
</head>
<body>
<main>
  <div class="actions">
    <button type="button" onclick="window.print()">Drucken</button>
    <a href="../../view_entry.php?id=<?php echo (int) $id; ?>">Zurück zur Buchung</a>
  </div>

  <header>
    <img src="logo.svg" alt="">
    <div>
      <div class="club"><?php echo escape_html((string) ($mrbs_company ?? '')); ?> · Teilnehmerliste</div>
      <h1><?php echo escape_html($titel); ?></h1>
    </div>
  </header>

  <dl>
    <dt>Datum</dt>          <dd><?php echo escape_html($tag); ?>, <?php echo escape_html("$von – $bis"); ?> Uhr</dd>
    <dt>Boot</dt>           <dd><?php echo escape_html($data['room_name']); ?></dd>
    <dt><?php echo $is_kurs ? 'Segellehrer' : 'Schiffsführer'; ?></dt>  <dd><?php echo escape_html($data['name']); ?></dd>
    <dt>Angelegt von</dt>   <dd><?php echo escape_html($leiter_nm); ?></dd>
    <dt>Belegung</dt>       <dd><?php echo count($rows) . ($limit ? " von $limit Plätzen" : ' angemeldet'); ?></dd>
  </dl>

  <table>
    <thead>
      <tr><th>#</th><th>Name</th><th class="mail">E-Mail</th><th>Angemeldet</th><th class="check">Anwesend</th></tr>
    </thead>
    <tbody>
<?php foreach ($rows as $i => $r): ?>
      <tr>
        <td class="nr"><?php echo $i + 1; ?></td>
        <td><?php echo escape_html($r['name']); ?></td>
        <td class="mail"><?php echo escape_html($r['email']); ?></td>
        <td><?php echo escape_html($r['date']); ?></td>
        <td class="check"><span></span></td>
      </tr>
<?php endforeach; ?>
<?php for ($i = 0; $i < $empty_rows; $i++): ?>
      <tr class="leer"><td class="nr"><?php echo count($rows) + $i + 1; ?></td><td></td><td class="mail"></td><td></td><td class="check"><span></span></td></tr>
<?php endfor; ?>
    </tbody>
  </table>

  <div class="foot">
    <span>Stand: <?php echo escape_html(datetime_format(['pattern' => 'dd.MM.yyyy HH:mm'], time())); ?> Uhr</span>
    <span>Unterschrift <?php echo $is_kurs ? 'Segellehrer' : 'Schiffsführer'; ?>: ________________________</span>
  </div>
</main>
</body>
</html>
