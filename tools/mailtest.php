<?php
// =============================================================================
// Testmail – prüft, ob MRBS über die SMTP-Einstellungen der config.inc.php
// Mails verschicken kann. Zeigt das Gespräch mit dem Mailserver an
// (Passwort und Mailinhalt werden ausgeblendet).
//
// Aufruf auf dem Server:
//   docker exec -i mrbs php < /opt/bootsbuchung/tools/mailtest.php
//
// Die Testmail geht an die Absenderadresse ($mail_settings['from']).
// =============================================================================
namespace MRBS;
chdir('/var/www/html');
require 'lib/PHPMailer/PHPMailer/Exception.php';
require 'lib/PHPMailer/PHPMailer/PHPMailer.php';
require 'lib/PHPMailer/PHPMailer/SMTP.php';
require 'config.inc.php';
echo "disabled=" . var_export($mail_settings['disabled'] ?? null, true) . " backend=" . ($mail_settings['admin_backend'] ?? '?') . " host=" . $smtp_settings['host'] . ":" . $smtp_settings['port'] . "\n";
$m = new \PHPMailer\PHPMailer\PHPMailer(true);
$m->isSMTP();
$m->Host = $smtp_settings['host']; $m->Port = $smtp_settings['port'];
$m->SMTPAuth = $smtp_settings['auth']; $m->SMTPSecure = $smtp_settings['secure'];
$m->Username = $smtp_settings['username']; $m->Password = $smtp_settings['password'];
$m->SMTPDebug = 2; $m->Debugoutput = function($s){ if (preg_match('/CLIENT -> SERVER: (?!(EHLO|STARTTLS|AUTH|MAIL|RCPT|DATA|QUIT))/', $s)) return; echo preg_replace("/(SERVER: AUTH \\w+).*/", "$1 [versteckt]", trim($s)), "\n"; }; $m->Timeout = 20;
$m->CharSet = 'utf-8';
$m->setFrom($mail_settings['from']); $m->addAddress($mail_settings['from']);
$m->Subject = 'MRBS Testmail von der VM'; $m->Body = 'Wenn das ankommt, kann die VM Mails senden.';
try { $m->send(); echo "\n=== GESENDET ===\n"; } catch (\Throwable $e) { echo "\n=== FEHLER: " . $e->getMessage() . "\n"; }
