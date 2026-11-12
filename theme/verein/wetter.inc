<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Wettervorhersage von Open-Meteo (für wetter.php und die
// Erinnerung vor der Fahrt). Ort aus $bb_wetter in der config.inc.php, eine
// Stunde Zwischenspeicher im temporären Verzeichnis. Siehe wetter.php.
// =============================================================================

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

// null = aus (kein $bb_wetter), [] = gerade keine Daten, sonst
// ['ort' => …, 'tage' => [datum => [...]]]
function bb_wetter_daten() : ?array
{
  global $bb_wetter;
  if (empty($bb_wetter) || !isset($bb_wetter['breite'], $bb_wetter['laenge']))
  {
    return null;
  }
  $breite = round((float) $bb_wetter['breite'], 3);
  $laenge = round((float) $bb_wetter['laenge'], 3);
  $ort    = (string) ($bb_wetter['ort'] ?? '');

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
    return [];
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
  return ['ort' => $ort, 'tage' => $tage];
}


// Wind in einem Zeitraum eines Tages: "SW 3 Bft, Böen 5" oder ''
function bb_wetter_zeitraum(array $daten, string $datum, int $von_stunde, int $bis_stunde) : string
{
  $tag = $daten['tage'][$datum] ?? null;
  if (!$tag)
  {
    return '';
  }
  $max = 0;
  $boe = 0;
  $richtung = $tag['richtung'];
  for ($h = $von_stunde; $h < max($bis_stunde, $von_stunde + 1) && $h < 24; $h++)
  {
    $s = $tag['stunden'][(string) $h] ?? null;
    if ($s)
    {
      if ($s['bft'] >= $max) { $max = $s['bft']; $richtung = $s['richtung']; }
      $boe = max($boe, $s['boeen_bft']);
    }
  }
  return "$richtung $max Bft, Böen $boe";
}
