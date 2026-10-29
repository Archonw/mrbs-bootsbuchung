<?php
// =============================================================================
// Schlüssel für Push-Benachrichtigungen erzeugen (einmalig)
//
//   docker exec -i mrbs php < /opt/bootsbuchung/tools/push-schluessel.php
//
// Gibt die Zeilen für die config.inc.php aus. install.sh und update.sh machen
// das automatisch, von Hand ist es nur nötig, wenn der Schlüssel fehlt.
// Den privaten Schlüssel nicht weitergeben und nicht ins Repo legen. Wird er
// später ausgetauscht, müssen alle Mitglieder Push auf ihren Geräten neu
// einschalten.
//
// Kontakt für die Push-Dienste (Google, Apple, Mozilla): Umgebungsvariable
// BB_PUSH_SUBJECT, z. B. "mailto:vorstand@mein-verein.de" oder die Adresse
// der Buchung "https://buchung.mein-verein.de".
// =============================================================================

$key = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
if ($key === false)
{
  fwrite(STDERR, "Fehler: openssl kann keinen Schlüssel erzeugen.\n");
  exit(1);
}
$ec  = openssl_pkey_get_details($key)['ec'];
$pad = function (string $s) : string { return str_pad($s, 32, "\0", STR_PAD_LEFT); };
$b64 = function (string $s) : string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); };

$subject = trim((string) getenv('BB_PUSH_SUBJECT'));
if (!preg_match('#^(mailto:\S+@\S+|https://\S+)$#', $subject))
{
  $subject = 'mailto:HIER-VEREINSADRESSE-EINTRAGEN';
}

echo "// --- Push-Benachrichtigungen (Schlüssel nicht weitergeben) ---\n";
echo "\$bb_push = [\n";
echo "  'public'  => '" . $b64("\x04" . $pad($ec['x']) . $pad($ec['y'])) . "',\n";
echo "  'private' => '" . $b64($pad($ec['d'])) . "',\n";
echo "  'subject' => " . var_export($subject, true) . ",\n";
echo "];\n";
