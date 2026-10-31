<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Hilfe zur aktuellen Seite (Fenster „Hilfe“ in theme.js)
//
//   Themes/verein/hilfe.php?seite=plan    Hilfe zu einer Seite
//   Themes/verein/hilfe.php?seite=alle    alle Themen (für help.php)
//
// Liefert JSON. Die Texte stehen in hilfe/<sprache>.php. Welche Abschnitte
// jemand sieht, hängt von der Rolle ab: Gast, Mitglied, Segellehrer,
// Bootswartung, Admin. Die Rolle prüft der Server, nicht der Browser.
//
// Sprache: die von MRBS (wie <html lang>), sonst Englisch, dann Deutsch.
// Mitgeliefert: de, en, es. Weitere Sprache = neue Datei hilfe/<kürzel>.php
// mit gleichem Aufbau, z. B. hilfe/fr.php (Vorlage: hilfe/en.php).
// Eigene Ergänzungen, die ein Update überstehen: hilfe/<kürzel>.local.php
// (gleicher Aufbau; gleichnamige Seiten werden ersetzt, neue hinzugefügt).
//
// Optional in der config.inc.php: Link „Ausführliche Anleitung“ im Fenster
//   $bb_hilfe_url = 'https://…';
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
header('Cache-Control: private, no-cache');

// --- Sprache -----------------------------------------------------------------
function bb_hilfe_texte(string $dir) : array
{
  $wunsch = [];
  try
  {
    $lang = strtolower(str_replace('_', '-', (string) Language::getInstance()->getWebLang()));
    if ($lang !== '')
    {
      $wunsch[] = $lang;
      $wunsch[] = explode('-', $lang)[0];
    }
  }
  catch (\Throwable $e)
  {
  }
  // Fehlt die Sprache: Englisch, dann Deutsch
  $wunsch[] = 'en';
  $wunsch[] = 'de';

  foreach (array_unique($wunsch) as $kuerzel)
  {
    if (!preg_match('/^[a-z]{2,3}(-[a-z0-9]{2,8})?$/', $kuerzel) || !is_file("$dir/$kuerzel.php"))
    {
      continue;
    }
    $daten = include "$dir/$kuerzel.php";
    if (!is_array($daten))
    {
      continue;
    }
    $daten['sprache'] = $kuerzel;
    $lokal = "$dir/$kuerzel.local.php";
    if (is_file($lokal))
    {
      $extra = include $lokal;
      if (is_array($extra))
      {
        $daten['texte']  = array_merge($daten['texte'] ?? [], $extra['texte'] ?? []);
        $daten['seiten'] = array_merge($daten['seiten'] ?? [], $extra['seiten'] ?? []);
      }
    }
    return $daten;
  }
  return ['sprache' => 'de', 'texte' => [], 'seiten' => []];
}

// --- Rolle -------------------------------------------------------------------
$user = session()->getCurrentUser();
$rollen = ['alle' => true, 'gast' => ($user === null), 'mitglied' => ($user !== null),
           'segellehrer' => false, 'bootswartung' => false, 'admin' => false];
if ($user !== null)
{
  $rollen['admin'] = ($user->level >= 2);
  foreach (['segellehrer', 'bootswartung'] as $spalte)
  {
    if ($rollen['admin'])
    {
      $rollen[$spalte] = true;   // Admins dürfen alles, was diese Rollen dürfen
      continue;
    }
    try
    {
      $rollen[$spalte] = (db()->query1("SELECT $spalte FROM " . _tbl('users') . " WHERE name=? LIMIT 1",
                                       [$user->username]) === 1);
    }
    catch (\Throwable $e)
    {
      // Spalte fehlt (SQL noch nicht eingespielt) – Rolle gibt es dann nicht
    }
  }
}

$daten  = bb_hilfe_texte(__DIR__ . '/hilfe');
$texte  = $daten['texte'] ?? [];
$seiten = $daten['seiten'] ?? [];

// Nur die Abschnitte, die diese Person betreffen. Eine Seite ohne passenden
// Abschnitt fällt ganz weg (z. B. Verwaltung für Mitglieder).
function bb_hilfe_sichtbar(array $seite, array $rollen) : array
{
  $abschnitte = [];
  foreach ($seite['abschnitte'] ?? [] as $a)
  {
    $fuer = $a['fuer'] ?? 'alle';
    if (!empty($rollen[$fuer]))
    {
      $abschnitte[] = ['titel' => (string) ($a['titel'] ?? ''),
                       'html'  => (string) ($a['html'] ?? ''),
                       'fuer'  => $fuer];
    }
  }
  return $abschnitte;
}

$themen = [];
$alle = [];
foreach ($seiten as $key => $seite)
{
  $abschnitte = bb_hilfe_sichtbar($seite, $rollen);
  if (!$abschnitte)
  {
    continue;
  }
  $themen[] = ['seite' => (string) $key, 'titel' => (string) ($seite['titel'] ?? $key)];
  $alle[] = ['seite' => (string) $key, 'titel' => (string) ($seite['titel'] ?? $key), 'abschnitte' => $abschnitte];
}

$antwort = [
  'sprache' => $daten['sprache'],
  'texte'   => $texte,
  'themen'  => $themen,
  'mehr'    => (isset($bb_hilfe_url) && preg_match('#^https?://#i', (string) $bb_hilfe_url)) ? (string) $bb_hilfe_url : null,
];

$wahl = (string) ($_GET['seite'] ?? '');
if ($wahl === 'alle')
{
  $antwort['seiten'] = $alle;
}
else
{
  // Unbekannte Seite oder nichts für diese Rolle: allgemeine Hilfe
  $treffer = null;
  foreach ($alle as $s)
  {
    if ($s['seite'] === $wahl)
    {
      $treffer = $s;
      break;
    }
  }
  if ($treffer === null)
  {
    foreach ($alle as $s)
    {
      if ($s['seite'] === 'allgemein')
      {
        $treffer = $s;
        break;
      }
    }
  }
  $antwort['seite'] = $treffer;
}

echo json_encode($antwort, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
