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

header('Content-Type: application/json; charset=utf-8');

if (empty($bb_wetter) || !isset($bb_wetter['breite'], $bb_wetter['laenge']))
{
  header('Cache-Control: private, max-age=3600');
  echo json_encode(['aus' => true]);
  exit;
}

$breite = round((float) $bb_wetter['breite'], 3);
$laenge = round((float) $bb_wetter['laenge'], 3);
$ort    = (string) ($bb_wetter['ort'] ?? '');

// Beaufort aus km/h
function bb_bft(float $kmh) : int
{
  $grenzen = [1, 6, 12, 20, 29, 39, 50, 62, 75, 89, 103, 118];
  foreach ($grenzen as $bft => $g)
  {
    if ($kmh < $g)
    {
      return $bft;
    }
  }
  return 12;
}

function bb_richtung(float $grad) : string
{
  $namen = ['N', 'NO', 'O', 'SO', 'S', 'SW', 'W', 'NW'];
  return $namen[((int) round($grad / 45)) % 8];
}

$cache = rtrim(sys_get_temp_dir(), '/') . '/bb-wetter-' . md5("$breite,$laenge") . '.json';
$roh = null;
if (is_file($cache) && (time() - filemtime($cache)) < 3600)
{
  $roh = json_decode((string) file_get_contents($cache), true);
}
if (!is_array($roh))
{
  $url = 'https://api.open-meteo.com/v1/forecast?' . http_build_query([
    'latitude'        => $breite,
    'longitude'       => $laenge,
    'hourly'          => 'wind_speed_10m,wind_gusts_10m,wind_direction_10m',
    'daily'           => 'wind_speed_10m_max,wind_gusts_10m_max,wind_direction_10m_dominant,temperature_2m_max,sunset',
    'timezone'        => 'Europe/Berlin',
    'forecast_days'   => 16,
    'wind_speed_unit' => 'kmh',
  ]);
  $ctx = stream_context_create(['http' => ['timeout' => 5, 'header' => "User-Agent: Bootsbuchung\r\n"]]);
  $antwort = @file_get_contents($url, false, $ctx);
  $neu = is_string($antwort) ? json_decode($antwort, true) : null;
  if (is_array($neu) && isset($neu['daily']['time']))
  {
    $roh = $neu;
    @file_put_contents($cache, $antwort);
  }
  elseif (is_file($cache))
  {
    // Open-Meteo nicht erreichbar: lieber die alte Vorhersage als keine
    $roh = json_decode((string) file_get_contents($cache), true);
  }
}

if (!is_array($roh) || !isset($roh['daily']['time']))
{
  header('Cache-Control: private, max-age=300');
  echo json_encode(['fehler' => true]);
  exit;
}

$tage = [];
foreach ($roh['daily']['time'] as $i => $datum)
{
  $sonne = (string) ($roh['daily']['sunset'][$i] ?? '');
  $tage[$datum] = [
    'bft'             => bb_bft((float) ($roh['daily']['wind_speed_10m_max'][$i] ?? 0)),
    'boeen_bft'       => bb_bft((float) ($roh['daily']['wind_gusts_10m_max'][$i] ?? 0)),
    'richtung'        => bb_richtung((float) ($roh['daily']['wind_direction_10m_dominant'][$i] ?? 0)),
    'temp'            => (int) round((float) ($roh['daily']['temperature_2m_max'][$i] ?? 0)),
    'sonnenuntergang' => strlen($sonne) >= 16 ? substr($sonne, 11, 5) : '',
    'stunden'         => [],
  ];
}
foreach ($roh['hourly']['time'] ?? [] as $i => $zeit)
{
  $datum = substr($zeit, 0, 10);
  $stunde = (string) (int) substr($zeit, 11, 2);
  if (!isset($tage[$datum]))
  {
    continue;
  }
  $tage[$datum]['stunden'][$stunde] = [
    'bft'       => bb_bft((float) ($roh['hourly']['wind_speed_10m'][$i] ?? 0)),
    'boeen_bft' => bb_bft((float) ($roh['hourly']['wind_gusts_10m'][$i] ?? 0)),
    'richtung'  => bb_richtung((float) ($roh['hourly']['wind_direction_10m'][$i] ?? 0)),
  ];
}

header('Cache-Control: private, max-age=900');
echo json_encode(['ort' => $ort, 'tage' => $tage], JSON_UNESCAPED_UNICODE);
