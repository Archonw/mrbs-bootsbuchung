#!/usr/bin/env bash
# =============================================================================
# Bootsbuchung – Installation auf einem frischen Linux-Server
#
#   curl -fsSL https://raw.githubusercontent.com/Archonw/mrbs-bootsbuchung/main/install.sh | sudo bash
#
# oder nach einem git clone:   sudo ./install.sh
#
# Getestet mit Ubuntu 22.04/24.04 und Debian 12. Braucht Internetzugang,
# mindestens 1 GB RAM (das Skript legt bei Bedarf Swap an) und ~5 GB Platz.
#
# Was passiert:
#   1. Docker wird installiert (falls noch nicht da)
#   2. Fragen: Vereinsname, Adresse, Proxy, Admin-Konto, Mailserver
#   3. MRBS wird mit allen Anpassungen gebaut und gestartet
#   4. Datenbank, Admin-Konto und nächtliche Wartung werden eingerichtet
#
# Erneut ausführen ist harmlos: Eine bestehende Installation wird erkannt und
# nicht überschrieben (dafür gibt es update.sh).
# =============================================================================
set -Eeuo pipefail

REPO_URL="${BB_REPO_URL:-https://github.com/Archonw/mrbs-bootsbuchung.git}"
INSTALL_DIR="${BB_DIR:-/opt/bootsbuchung}"
MRBS_VERSION="${MRBS_VERSION:-v1.12.2}"

# --- Ausgabe -----------------------------------------------------------------
if [ -t 1 ]; then B=$'\e[1m'; G=$'\e[32m'; Y=$'\e[33m'; R=$'\e[31m'; N=$'\e[0m'; else B= G= Y= R= N=; fi
step() { echo; echo "${B}${G}==> $*${N}"; }
info() { echo "    $*"; }
warn() { echo "${Y}!!  $*${N}"; }
die()  { echo "${R}FEHLER: $*${N}" >&2; exit 1; }
trap 'die "Abbruch in Zeile $LINENO. Ausgabe oben prüfen – das Skript kann gefahrlos neu gestartet werden."' ERR

# Fragen kommen immer von der Tastatur – auch wenn das Skript per "curl | bash" läuft
ask() {      # ask "Frage" "Vorgabe" -> Antwort
  local q="$1" def="${2:-}" a
  if [ -n "$def" ]; then printf '%s [%s]: ' "$q" "$def" >/dev/tty; else printf '%s: ' "$q" >/dev/tty; fi
  IFS= read -r a </dev/tty || true
  echo "${a:-$def}"
}
ask_secret() {
  local q="$1" a
  printf '%s: ' "$q" >/dev/tty; IFS= read -rs a </dev/tty || true; echo >/dev/tty
  echo "$a"
}
ask_yn() {   # ask_yn "Frage" j|n
  local a; a=$(ask "$1 (j/n)" "$2")
  [[ "$a" =~ ^[jJyY] ]]
}
php_str() {  # Wert sicher in einen PHP-String '…' setzen
  local s="${1//\\/\\\\}"; printf "'%s'" "${s//\'/\\\'}"
}
sql_str() {  # Wert sicher in einen SQL-String '…' setzen
  local s="${1//\\/\\\\}"; printf "'%s'" "${s//\'/\'\'}"
}
randpw() { openssl rand -hex 32; }   # 64 Zeichen 0-9a-f

# --- Voraussetzungen ---------------------------------------------------------
[ "$(id -u)" -eq 0 ] || die "Bitte mit sudo bzw. als root starten."
[ -r /dev/tty ] || die "Keine Tastatur erreichbar – bitte in einer normalen SSH-Sitzung starten."
command -v apt-get >/dev/null || die "Dieses Skript unterstützt Debian/Ubuntu (apt)."

echo "${B}Bootsbuchung – Installation${N}"
echo "Basiert auf MRBS (Meeting Room Booking System), GPL v2."

if [ -f "$INSTALL_DIR/.env" ] && [ -f "$INSTALL_DIR/config/config.inc.php" ]; then
  warn "In $INSTALL_DIR ist bereits eine Installation."
  info "Aktualisieren:      sudo $INSTALL_DIR/update.sh"
  info "Neu installieren:   vorher den Ordner $INSTALL_DIR entfernen (löscht alle Buchungen!)"
  exit 0
fi

step "Pakete installieren (git, curl)"
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y -qq git curl ca-certificates openssl >/dev/null

# Wenig Arbeitsspeicher: Swap anlegen, sonst kann das Bauen abbrechen
mem_mb=$(awk '/MemTotal/ {print int($2/1024)}' /proc/meminfo)
if [ "$mem_mb" -lt 2000 ] && ! swapon --show | grep -q .; then
  step "Wenig Arbeitsspeicher (${mem_mb} MB) – lege 2 GB Swap an"
  fallocate -l 2G /swapfile 2>/dev/null || dd if=/dev/zero of=/swapfile bs=1M count=2048 status=none
  chmod 600 /swapfile && mkswap /swapfile >/dev/null && swapon /swapfile
  grep -q '^/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

if ! command -v docker >/dev/null || ! docker compose version >/dev/null 2>&1; then
  step "Docker installieren (offizielles Skript von get.docker.com)"
  curl -fsSL https://get.docker.com | sh >/dev/null
fi
systemctl enable --now docker >/dev/null 2>&1 || true
docker compose version >/dev/null 2>&1 || die "Docker Compose fehlt."

# --- Programmdateien ---------------------------------------------------------
SRC_DIR="$(cd "$(dirname "${BASH_SOURCE[0]:-$0}")" 2>/dev/null && pwd || true)"
if [ -n "$SRC_DIR" ] && [ -f "$SRC_DIR/docker-compose.yml" ] && [ -d "$SRC_DIR/patches" ]; then
  if [ "$SRC_DIR" != "$INSTALL_DIR" ]; then
    step "Kopiere Programmdateien nach $INSTALL_DIR"
    mkdir -p "$INSTALL_DIR"
    cp -a "$SRC_DIR/." "$INSTALL_DIR/"
  fi
else
  step "Lade Programmdateien nach $INSTALL_DIR"
  git clone -q "$REPO_URL" "$INSTALL_DIR"
fi
cd "$INSTALL_DIR"

# --- Fragen ------------------------------------------------------------------
step "Ein paar Fragen (Enter übernimmt den Vorschlag in [Klammern])"

VEREIN=$(ask "Name des Vereins" "Mein Segelverein e.V.")
TZ_NAME=$(ask "Zeitzone" "Europe/Berlin")

echo
echo "Wie soll die Bootsbuchung erreichbar sein?"
echo "  1) Mit eigener Domain und HTTPS – Nginx Proxy Manager wird mitinstalliert"
echo "     (der Server hat noch keinen Webserver/Proxy, Ports 80 und 443 sind frei)"
echo "  2) Es gibt schon einen Proxy (Nginx Proxy Manager, Caddy, Traefik, …)"
echo "     – die Buchung lauscht nur auf einem Port, der Proxy leitet weiter"
echo "  3) Ohne Proxy direkt auf Port 80 (nur Heimnetz/Test, kein HTTPS)"
MODE=$(ask "Auswahl" "1")
case "$MODE" in
  1) PROFILES="npm"; BIND="127.0.0.1:8080"
     DOMAIN=$(ask "Domain der Buchung (DNS muss auf diesen Server zeigen)" "buchung.mein-verein.de")
     URL="https://$DOMAIN" ;;
  2) PROFILES=""
     PORT=$(ask "Port für die Buchung" "8080")
     if ask_yn "Läuft der Proxy auf DIESEM Server?" "j"; then BIND="127.0.0.1:$PORT"; else BIND="0.0.0.0:$PORT"; fi
     DOMAIN=$(ask "Domain, unter der der Proxy die Buchung anbietet" "buchung.mein-verein.de")
     URL="https://$DOMAIN" ;;
  3) PROFILES=""; BIND="0.0.0.0:80"
     IP=$(hostname -I 2>/dev/null | awk '{print $1}')
     DOMAIN=$(ask "Adresse (IP oder Name) dieses Servers" "${IP:-localhost}")
     URL="http://$DOMAIN" ;;
  *) die "Bitte 1, 2 oder 3 eingeben." ;;
esac

echo
echo "Erstes Admin-Konto (damit meldest du dich gleich an):"
while :; do
  ADMIN_USER=$(ask "Benutzername (nur Buchstaben, Ziffern, . _ -)" "admin")
  [[ "$ADMIN_USER" =~ ^[A-Za-z0-9._-]{2,30}$ ]] && break
  warn "Bitte 2–30 Zeichen aus Buchstaben, Ziffern, Punkt, Unterstrich oder Bindestrich."
done
ADMIN_NAME=$(ask "Anzeigename" "Administrator")
ADMIN_MAIL=$(ask "E-Mail-Adresse" "")
while :; do
  ADMIN_PW=$(ask_secret "Passwort (mind. 10 Zeichen)")
  [ "${#ADMIN_PW}" -ge 10 ] || { warn "Zu kurz."; continue; }
  [ "$(ask_secret "Passwort wiederholen")" = "$ADMIN_PW" ] && break
  warn "Die Passwörter sind verschieden."
done

echo
MAIL_ON=0
if ask_yn "Mails verschicken (Bestätigungen, Bootswart-Meldungen, Erinnerungen)? Dafür braucht es ein Mailkonto mit SMTP." "j"; then
  MAIL_ON=1
  SMTP_HOST=$(ask "SMTP-Server" "smtp.gmail.com")
  SMTP_PORT=$(ask "SMTP-Port (587 = STARTTLS, 465 = SSL)" "587")
  if [ "$SMTP_PORT" = "465" ]; then SMTP_SECURE="ssl"; else SMTP_SECURE="tls"; fi
  SMTP_USER=$(ask "SMTP-Benutzer" "$ADMIN_MAIL")
  SMTP_PW=$(ask_secret "SMTP-Passwort (bei Gmail ein App-Passwort)")
  MAIL_FROM=$(ask "Absenderadresse" "$SMTP_USER")
fi

IMPRESSUM=$(ask "Link zum Impressum des Vereins (leer = keiner)" "")
DATENSCHUTZ=$(ask "Link zur Datenschutzerklärung (leer = keiner)" "")

# --- Dateien anlegen ---------------------------------------------------------
step "Einstellungen speichern"
DB_PASSWORD=$(randpw)
DB_ROOT_PASSWORD=$(randpw)
mkdir -p config data/mysql data/initdb

umask 077
cat > .env <<EOF
# Bootsbuchung – angelegt von install.sh am $(date '+%F %T')
# Enthält Passwörter: nicht weitergeben, nicht ins Internet laden.
COMPOSE_PROFILES=$PROFILES
BB_BIND=$BIND
BB_URL=$URL
TZ=$TZ_NAME
MRBS_VERSION=$MRBS_VERSION
DB_PASSWORD=$DB_PASSWORD
DB_ROOT_PASSWORD=$DB_ROOT_PASSWORD
EOF
umask 022

{
cat <<EOF
<?php
namespace MRBS;
// =============================================================================
// Bootsbuchung – Einstellungen (angelegt von install.sh)
// Nach jeder Änderung:  cd $INSTALL_DIR && docker compose restart mrbs
// Alle weiteren Möglichkeiten: systemdefaults.inc.php von MRBS
// =============================================================================

// --- Grundlagen ---
\$timezone = $(php_str "$TZ_NAME");
\$dbsys = 'mysql';
\$db_host = 'mrbs-db';
\$db_database = 'mrbs';
\$db_login = 'mrbs';
\$db_password = $(php_str "$DB_PASSWORD");
\$db_tbl_prefix = 'mrbs_';

\$auth['type'] = 'db';
\$auth['session'] = 'php';
\$auth['deny_public_access'] = true;     // nur angemeldete Mitglieder sehen Buchungen
\$url_base = $(php_str "$URL/");

// --- Aussehen ---
\$mrbs_company = $(php_str "$VEREIN");
\$mrbs_company_url = 'index.php';        // Logo führt zur Startseite
\$mrbs_company_logo = 'Themes/verein/logo.svg';
\$theme = 'verein';
\$custom_js_url = 'Themes/verein/theme.js';
\$bb_impressum_url = $(php_str "$IMPRESSUM");
\$bb_datenschutz_url = $(php_str "$DATENSCHUTZ");

\$default_language_tokens = 'de';
\$disable_automatic_language_changing = true;
\$override_locale = 'de_DE';
\$weekstarts = 1;
\$default_view = 'week';
unset(\$booking_types);                  // keine Buchungstypen

// --- Begriffe ---
\$vocab_override['de']['mrbs']             = 'Bootsbuchung';
\$vocab_override['de']['room']             = 'Boot';
\$vocab_override['de']['rooms']            = 'Boote';
\$vocab_override['de']['select_room']      = 'Boot auswählen';
\$vocab_override['de']['sort_room']        = 'Boot';
\$vocab_override['de']['match_room']       = 'Suche Boot';
\$vocab_override['de']['namebooker']       = 'Schiffsführer';
\$vocab_override['de']['fulldescription']  = 'Gibt es etwas zu melden?';
\$vocab_override['de']['entry.description']= 'Gibt es etwas zu melden?';
\$vocab_override['de']['addentry']         = 'Boot buchen';
\$vocab_override['de']['no_rooms_for_area']= 'In diesem Bereich sind noch keine Boote angelegt.';
\$vocab_override['de']['addroom']            = 'Boot hinzufügen';
\$vocab_override['de']['editroom']           = 'Boot bearbeiten';
\$vocab_override['de']['viewroom']           = 'Boot ansehen';
\$vocab_override['de']['norooms']            = 'Keine Boote vorhanden.';
\$vocab_override['de']['norooms_enabled']    = 'Keine Boote aktiviert.';
\$vocab_override['de']['no_rooms_selected']  = 'Bitte ein Boot auswählen.';
\$vocab_override['de']['invalid_room']       = 'Ungültiges Boot!';
\$vocab_override['de']['invalid_room_name']  = 'Dieser Bootsname ist hier schon vergeben!';
\$vocab_override['de']['ctrl_click']         = 'Strg-Taste plus Mausklick, um mehr als ein Boot auszuwählen';
\$vocab_override['de']['sort_key_note']      = 'Schlüssel zum Sortieren der Boote';
\$vocab_override['de']['disabled_room_note'] = 'Ein deaktiviertes Boot erscheint nicht mehr im Kalender und kann nicht gebucht werden. Bestehende Buchungen bleiben erhalten.';
\$vocab_override['de']['delarea']            = 'Bitte zuerst alle Boote in diesem Bereich löschen.<p>';
\$vocab_override['de']['area_room_settings'] = 'Bereiche und Boote';
\$vocab_override['de']['room_admin_email']   = 'E-Mail des Boot-Paten';
\$vocab_override['de']['area_admin_email']   = 'E-Mail des Bootswarts';

// --- Kapazität: der Schiffsführer belegt einen Platz ---
\$registrant_capacity_offset = 1;

// --- Kurse und "Mitsegler willkommen" ---
\$enable_registration = true;
\$enable_registration_users = true;
\$auth['show_registrant_names'] = true;
\$auth['db']['protected_fields'] = array('level', 'name', 'display_name', 'segellehrer');
\$vocab_override['de']['users.segellehrer']            = 'Segellehrer';
\$vocab_override['de']['entry.kurs']                   = 'Als Kurs anbieten';
\$vocab_override['de']['allow_registration']           = 'Mitsegler willkommen';
\$vocab_override['de']['registrant_limit_enabled']     = 'Plätze begrenzen';
\$vocab_override['de']['registrant_limit']             = 'Plätze für Mitsegler';
\$vocab_override['de']['event_registration']           = 'Mitsegeln / Kurs';
\$vocab_override['de']['n_registered']                 = 'Angemeldet';
\$vocab_override['de']['already_registered']           = 'Du bist angemeldet.';
\$vocab_override['de']['event_full']                   = 'Alle Plätze sind belegt.';
\$vocab_override['de']['registration_level_limited']   = ' · %1\$d/%2\$d';
\$vocab_override['de']['registration_level_unlimited'] = ' · %1\$d';
\$vocab_override['de']['registration_level_limited_with_names']   = ' · %1\$d/%2\$d: %3\$s';
\$vocab_override['de']['registration_level_unlimited_with_names'] = ' · %1\$d: %2\$s';
\$vocab_override['de']['confirm_edit_series'] = 'Für diese Reihe sind Mitglieder angemeldet. Ihre Anmeldungen werden auf die geänderten Termine übertragen, sie bekommen eine Mail. Fortfahren?';

// --- Mail ---
// Bootswart-Meldungen gehen an die E-Mail-Adresse des Bereichsadministrators
// (Bereich bearbeiten) und an die des Boots (Boot bearbeiten = Boot-Pate).
\$mail_settings['admin_lang'] = 'de';
\$mail_settings['admin_on_bookings'] = false;
\$mail_settings['area_admin_on_bookings'] = false;   // sonst Mail bei JEDER Buchung
\$mail_settings['room_admin_on_bookings'] = false;
\$mail_settings['booker'] = true;                    // Bestätigung an den Bucher
EOF
if [ "$MAIL_ON" = 1 ]; then
cat <<EOF
\$mail_settings['disabled'] = false;
\$mail_settings['admin_backend'] = 'smtp';
\$mail_settings['from'] = $(php_str "$MAIL_FROM");
\$mail_settings['organizer'] = $(php_str "$MAIL_FROM");
\$smtp_settings['host'] = $(php_str "$SMTP_HOST");
\$smtp_settings['port'] = $SMTP_PORT;
\$smtp_settings['auth'] = true;
\$smtp_settings['secure'] = $(php_str "$SMTP_SECURE");
\$smtp_settings['username'] = $(php_str "$SMTP_USER");
\$smtp_settings['password'] = $(php_str "$SMTP_PW");
EOF
else
cat <<EOF
\$mail_settings['disabled'] = true;   // Mailversand aus – siehe README "Mail einrichten"
EOF
fi
} > config/config.inc.php
# Lesbar nur für root und den Webserver im Container (www-data = 33)
chown root:33 config/config.inc.php && chmod 640 config/config.inc.php

# --- Bauen und starten -------------------------------------------------------
step "MRBS $MRBS_VERSION mit allen Anpassungen bauen (dauert einige Minuten)"
./update.sh --nur-bauen

cp sql/kursbuchung.sql data/initdb/020-kursbuchung.sql

step "Starten"
docker compose up -d

printf '    Warte auf die Datenbank ' 
for i in $(seq 90); do
  if docker exec mrbs_db sh -c 'mariadb -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" mrbs -e "SELECT 1 FROM mrbs_users LIMIT 1"' >/dev/null 2>&1; then
    echo " ok"; break
  fi
  printf '.'; sleep 2
  [ "$i" = 90 ] && die "Die Datenbank startet nicht. Log: docker logs mrbs_db"
done

step "Admin-Konto anlegen"
HASH=$(docker exec -e PW="$ADMIN_PW" mrbs php -r 'echo password_hash(getenv("PW"), PASSWORD_DEFAULT);')
docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" mrbs' <<EOF
INSERT INTO mrbs_users (level, name, display_name, email, password_hash, segellehrer)
VALUES (2, $(sql_str "$ADMIN_USER"), $(sql_str "$ADMIN_NAME"), $(sql_str "$ADMIN_MAIL"), $(sql_str "$HASH"), 1);
EOF

step "Bereich \"Boote\" anlegen"
# Über MRBS selbst, damit alle Bereichs-Einstellungen sinnvoll vorbelegt sind
docker exec -w /var/www/html mrbs php -r 'namespace MRBS; require "defaultincludes.inc"; require_once "mrbs_sql.inc"; $e = ""; mrbsAddArea("Boote", $e);' >/dev/null
docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" mrbs' <<EOF
UPDATE mrbs_area SET area_admin_email = $(sql_str "$ADMIN_MAIL") WHERE area_name = 'Boote';
EOF
unset ADMIN_PW HASH

step "Nächtliche Wartung einrichten"
chmod +x maintenance/*.sh update.sh
{
  echo "# Bootsbuchung – Wartung (angelegt von install.sh)"
  echo "45 2 * * * root $INSTALL_DIR/maintenance/session-cleanup.sh >> /var/log/bootsbuchung.log 2>&1"
  echo "15 3 * * * root $INSTALL_DIR/maintenance/db-sicherung.sh >> /var/log/bootsbuchung.log 2>&1"
  [ "$MAIL_ON" = 1 ] && echo "0 18 * * * root $INSTALL_DIR/maintenance/kurs-erinnerung.sh >> /var/log/bootsbuchung.log 2>&1"
} > /etc/cron.d/bootsbuchung
chmod 644 /etc/cron.d/bootsbuchung

# --- Fertig ------------------------------------------------------------------
IP=$(hostname -I 2>/dev/null | awk '{print $1}')
echo
echo "${B}${G}Fertig – die Bootsbuchung läuft.${N}"
echo
case "$MODE" in
  1) cat <<EOF
Noch ein Schritt: HTTPS im Nginx Proxy Manager einrichten
  1. http://$IP:81 öffnen und das Admin-Konto des Proxy Managers anlegen
     (ältere Versionen: admin@example.com / changeme, danach ändern)
  2. "Hosts" → "Proxy Hosts" → "Add Proxy Host"
       Domain Names:          $DOMAIN
       Forward Hostname / IP: mrbs        Forward Port: 80
       "Block Common Exploits" anhaken
     Reiter "SSL": "Request a new SSL Certificate", "Force SSL" anhaken, speichern
  3. https://$DOMAIN öffnen und mit "$ADMIN_USER" anmelden
  Danach Port 81 in der Firewall schließen oder nur fürs Heimnetz freigeben.
EOF
  ;;
  2) cat <<EOF
Im vorhandenen Proxy eine Weiterleitung anlegen:
  $DOMAIN  →  http://$( [ "${BIND%%:*}" = 127.0.0.1 ] && echo 127.0.0.1 || echo "$IP" ):${BIND##*:}
Dann https://$DOMAIN öffnen und mit "$ADMIN_USER" anmelden.
EOF
  ;;
  3) echo "Öffne http://$DOMAIN und melde dich mit \"$ADMIN_USER\" an." ;;
esac
cat <<EOF

Als Nächstes in der Buchung:
  - "Boote" → Bereich "Boote" → Boote anlegen (Name, Kapazität = Plätze inkl. Schiffsführer)
  - "Benutzerliste" → Mitglieder anlegen, Segellehrern den Haken "Segellehrer" geben

Programmordner: $INSTALL_DIR   (Einstellungen: config/config.inc.php)
Aktualisieren:  sudo $INSTALL_DIR/update.sh
Anleitung:      https://github.com/Archonw/mrbs-bootsbuchung
EOF
