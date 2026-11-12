<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Buchungsregeln des Vereins in einer Form, die Mitglieder
// verstehen. Benutzt von regeln.php (Hinweis im Plan, theme.js) und hilfe.php
// (Platzhalter in den Hilfetexten).
//
// Quelle sind dieselben Einstellungen, die MRBS beim Speichern prüft:
//   $max_create_ahead_enabled / $max_create_ahead_secs   wie weit im Voraus
//   (jeweils mit den Werten des Bereichs, falls dort gesetzt)
//   Saison aus der Verwaltung (buchungszeitraum.patch) bzw.
//   $min_booking_date / $max_booking_date
//   $morningstarts / $eveningends (+ _minutes)            Uhrzeiten am Tag
// =============================================================================

function bb_regeln(?int $area = null) : array
{
  // Die Regeln können je Bereich (Verein) verschieden sein und stehen dann in
  // der Datenbank (Verwaltung → Bereich bearbeiten), nicht in der config.inc.php
  if (function_exists(__NAMESPACE__ . '\\get_area_settings'))
  {
    if (!isset($area) && function_exists(__NAMESPACE__ . '\\get_default_area'))
    {
      $area = get_default_area();
    }
    if (isset($area) && $area > 0)
    {
      get_area_settings($area);
    }
  }

  global $max_create_ahead_enabled, $max_create_ahead_secs,
         $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes,
         $enable_periods, $measure_max_to_start_time;

  $voraus = null;
  if (!empty($max_create_ahead_enabled) && isset($max_create_ahead_secs) && $max_create_ahead_secs > 0)
  {
    $voraus = (int) floor($max_create_ahead_secs / 86400);
  }

  $saison = ['beginn' => '', 'ende' => ''];
  if (is_file(__DIR__ . '/../../bb_buchungszeitraum.inc'))
  {
    require_once __DIR__ . '/../../bb_buchungszeitraum.inc';
  }
  if (function_exists(__NAMESPACE__ . '\\bb_buchungszeitraum'))
  {
    $z = bb_buchungszeitraum();
    $saison = ['beginn' => (string) ($z['beginn'] ?? ''), 'ende' => (string) ($z['ende'] ?? '')];
  }
  else
  {
    global $min_booking_date_enabled, $min_booking_date, $max_booking_date_enabled, $max_booking_date;
    $saison = ['beginn' => !empty($min_booking_date_enabled) ? (string) $min_booking_date : '',
               'ende'   => !empty($max_booking_date_enabled) ? (string) $max_booking_date : ''];
  }

  $zeit = function ($h, $m) : string { return sprintf('%02d:%02d', (int) $h, (int) $m); };
  $tag = empty($enable_periods)
       ? ['von' => $zeit($morningstarts ?? 7, $morningstarts_minutes ?? 0),
          'bis' => $zeit($eveningends ?? 19, $eveningends_minutes ?? 0)]
       : null;

  $heute = new \DateTimeImmutable('today');
  // MRBS misst bis zum Ende der Buchung (oder bis zum Beginn, wenn
  // $measure_max_to_start_time gesetzt ist), und zwar ab jetzt auf die Sekunde
  $grenze = null;
  if (isset($voraus))
  {
    $grenze = (new \DateTimeImmutable('now'))->modify('+' . (int) $max_create_ahead_secs . ' seconds');
  }
  return [
    'heute'       => $heute->format('Y-m-d'),
    'voraus_tage' => $voraus,
    // so spät darf eine Buchung heute höchstens enden (bzw. beginnen)
    'buchbar_bis' => isset($grenze) ? $grenze->format('Y-m-d\TH:i') : null,
    'gemessen_an' => !empty($measure_max_to_start_time) ? 'beginn' : 'ende',
    'saison'      => $saison,
    'tag'         => $tag,
  ];
}


// "7 Uhr", "7:30 Uhr"
function bb_regeln_uhr(string $hhmm) : string
{
  [$h, $m] = array_map('intval', explode(':', $hhmm));
  return $h . ($m ? ':' . sprintf('%02d', $m) : '') . ' Uhr';
}


// Platzhalter für die deutschen Hilfetexte
function bb_regeln_platzhalter(?int $area = null) : array
{
  $r = bb_regeln($area);
  $teile = [];
  if (isset($r['voraus_tage']))
  {
    $teile[] = 'höchstens <strong>' . $r['voraus_tage'] . ' ' . ($r['voraus_tage'] === 1 ? 'Tag' : 'Tage') . ' im Voraus</strong>';
  }
  $teile[] = 'nur während der <strong>Saison</strong>';
  if (isset($r['tag']))
  {
    $teile[] = 'täglich zwischen <strong>' . bb_regeln_uhr($r['tag']['von']) . ' und ' . bb_regeln_uhr($r['tag']['bis']) . '</strong>';
  }
  $letzte = array_pop($teile);
  $satz = 'Gebucht werden kann ' . ($teile ? implode(', ', $teile) . ' und ' : '') . $letzte . '.';

  return [
    '{buchungsregeln}' => $satz,
    '{zu_weit}'        => isset($r['voraus_tage'])
                          ? 'mehr als ' . $r['voraus_tage'] . ' ' . ($r['voraus_tage'] === 1 ? 'Tag' : 'Tage') . ' in der Zukunft'
                          : 'zu weit in der Zukunft',
  ];
}
