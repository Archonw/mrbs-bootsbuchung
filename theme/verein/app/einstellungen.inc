<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Name und Farbe der App
//
// Benutzt von head.inc und app/manifest.php. Ohne eigene Angaben heißt die
// App „Bootsbuchung <Vereinsname>“ (Vereinsname = $mrbs_company), auf dem
// Startbildschirm „Boote“, und ist schwarz wie die Kopfzeile. Ändern in der
// config.inc.php, jede Zeile ist freiwillig:
//
//   $bb_app = [
//     'name'         => 'Bootsbuchung Mein Segelverein',  // beim Installieren
//     'kurzname'     => 'Boote',                          // unter dem Symbol
//     'beschreibung' => 'Boote buchen, mitsegeln und Kurse',
//     'farbe'        => '#0c0e0d',                        // Statusleiste, Startbild
//   ];
//
// Eigene Symbole: die PNG-Dateien in app/ durch gleich große ersetzen.
// =============================================================================

function bb_app_einstellungen() : array
{
  global $bb_app, $mrbs_company;

  $app = (isset($bb_app) && is_array($bb_app)) ? $bb_app : [];
  $text = function (string $schluessel, string $vorgabe) use ($app) : string
  {
    $wert = trim((string) ($app[$schluessel] ?? ''));
    return ($wert === '') ? $vorgabe : $wert;
  };

  $verein = trim(strip_tags((string) ($mrbs_company ?? '')));
  $farbe  = $text('farbe', '#0c0e0d');
  if (!preg_match('/^#[0-9a-fA-F]{6}$/', $farbe))
  {
    $farbe = '#0c0e0d';
  }

  return [
    'name'         => $text('name', ($verein === '') ? 'Bootsbuchung' : "Bootsbuchung $verein"),
    'kurzname'     => $text('kurzname', 'Boote'),
    'beschreibung' => $text('beschreibung', 'Boote buchen, mitsegeln und Kurse' . (($verein === '') ? '' : " – $verein")),
    'farbe'        => $farbe,
  ];
}
