#!/usr/bin/env bash
# =============================================================================
# Bootsbuchung – aktualisieren
#
#   sudo /opt/bootsbuchung/update.sh            neuesten Stand holen, neu bauen, neu starten
#   sudo /opt/bootsbuchung/update.sh v1.12.3    eine bestimmte MRBS-Version bauen
#
# Holt MRBS in der gewünschten Version direkt vom MRBS-Projekt, spielt alle
# patches/*.patch ein und baut daraus das Docker-Image "bootsbuchung-mrbs".
# Passt ein Patch nicht, bricht das Update ab und die alte Version läuft weiter.
# Buchungen, Benutzer und Einstellungen bleiben unberührt.
# =============================================================================
set -Eeuo pipefail

DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$DIR"
[ "$(id -u)" -eq 0 ] || { echo "Bitte mit sudo starten."; exit 1; }

NUR_BAUEN=0
VERSION=""
for a in "$@"; do
  case "$a" in
    --nur-bauen) NUR_BAUEN=1 ;;      # wird von install.sh benutzt
    v*) VERSION="$a" ;;
    *) echo "Unbekannte Option: $a"; exit 1 ;;
  esac
done

# Version: Argument > .env > getestete Standardversion
if [ -z "$VERSION" ] && [ -f .env ]; then
  VERSION=$(grep -E '^MRBS_VERSION=' .env | cut -d= -f2- || true)
fi
VERSION="${VERSION:-v1.12.2}"

if [ "$NUR_BAUEN" = 0 ] && [ -d .git ]; then
  echo "==> Neuesten Stand der Bootsbuchung holen"
  git pull --ff-only
fi

# Schutz: nie versehentlich auf eine ältere MRBS-Version zurückbauen
RUNNING=$(docker exec mrbs cat /var/www/html/version_num.inc 2>/dev/null \
          | grep mrbs_version_number | grep -oE "[0-9]+(\.[0-9]+)+" | head -n 1 || true)
WANTED="${VERSION#v}"
if [ -n "$RUNNING" ] && [ "$RUNNING" != "$WANTED" ] && \
   [ "$(printf '%s\n%s\n' "$RUNNING" "$WANTED" | sort -V | head -n 1)" = "$WANTED" ] && \
   [ "${ALLOW_DOWNGRADE:-}" != "1" ]; then
  echo "ABBRUCH: Es läuft MRBS $RUNNING – $VERSION wäre älter."
  echo "Gewollt? Dann so aufrufen:  sudo ALLOW_DOWNGRADE=1 $0 $VERSION"
  exit 1
fi

BUILD_DIR="$(mktemp -d /tmp/bootsbuchung-build.XXXXXX)"
trap 'rm -rf "$BUILD_DIR"' EXIT

echo "==> MRBS $VERSION holen"
git clone -q --depth 1 --branch "$VERSION" \
  https://github.com/meeting-room-booking-system/mrbs-code.git "$BUILD_DIR" 2>/dev/null \
  || { echo "FEHLER: MRBS-Version $VERSION nicht gefunden."; exit 1; }

echo "==> Anpassungen einspielen"
for p in patches/*.patch; do
  echo "    $(basename "$p")"
  git -C "$BUILD_DIR" apply --check "$DIR/$p" || {
    echo "FEHLER: $(basename "$p") passt nicht zu MRBS $VERSION – Update abgebrochen, die laufende Version bleibt."
    exit 1
  }
  git -C "$BUILD_DIR" apply "$DIR/$p"
done

# Tabellen für eine neue Datenbank (nur beim allerersten Start benutzt)
mkdir -p data/initdb
cp "$BUILD_DIR/tables.my.sql" data/initdb/010-tables.sql

echo "==> Docker-Image bauen"
docker image tag bootsbuchung-mrbs:latest bootsbuchung-mrbs:vorher 2>/dev/null || true
# --pull: immer das aktuelle PHP-Image holen, damit PHP-Sicherheitsupdates mitkommen
docker build --pull -q -t bootsbuchung-mrbs:latest -t "bootsbuchung-mrbs:$VERSION" "$BUILD_DIR" >/dev/null

# Gebaute Version merken
if [ -f .env ]; then
  if grep -q '^MRBS_VERSION=' .env; then sed -i "s/^MRBS_VERSION=.*/MRBS_VERSION=$VERSION/" .env
  else echo "MRBS_VERSION=$VERSION" >> .env; fi
fi

[ "$NUR_BAUEN" = 1 ] && exit 0

echo "==> Neu starten"
docker compose up -d
docker image prune -f >/dev/null

# Datenbank-Erweiterungen nachziehen (mehrfach ausführen ist harmlos)
for i in $(seq 30); do
  docker exec mrbs_db sh -c 'mariadb -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" mrbs -e "SELECT 1"' >/dev/null 2>&1 && break
  sleep 2
done
for f in sql/*.sql; do
  docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" mrbs' < "$f"
done

# Einstellungen für neue Funktionen nachtragen, falls sie in einer älteren
# config.inc.php noch fehlen (wird nur einmal ergänzt, nie überschrieben)
CONF=config/config.inc.php
if [ -f "$CONF" ] && grep -qE '^\s*\?>' "$CONF"; then
  echo "    Hinweis: $CONF endet mit '?>' – neue Einstellungen bitte von Hand ergänzen (siehe CHANGELOG)."
elif [ -f "$CONF" ]; then
  ERGAENZT=0
  if ! grep -q 'cookie_samesite_lax' "$CONF"; then
    cat >> "$CONF" <<'PHP'

// --- App: angemeldet bleiben, auch beim Öffnen aus App, Mail oder Push (ergänzt von update.sh) ---
$cookie_samesite_lax = true;
PHP
    ERGAENZT=1
  fi
  if ! grep -q 'users.nur_push' "$CONF"; then
    cat >> "$CONF" <<'PHP'

// --- Push-Benachrichtigungen (ergänzt von update.sh) ---
$vocab_override['de']['users.nur_push'] = 'Nur Push, keine Mails';
PHP
    ERGAENZT=1
  fi
  if ! grep -q '\$bb_push' "$CONF"; then
    BB_URL=$(grep -E '^BB_URL=' .env 2>/dev/null | cut -d= -f2- || true)
    if SCHLUESSEL=$(docker exec -i -e BB_PUSH_SUBJECT="$BB_URL" mrbs php < tools/push-schluessel.php); then
      printf '\n%s\n' "$SCHLUESSEL" >> "$CONF"
      ERGAENZT=1
    else
      echo "    Hinweis: Push-Schlüssel konnte nicht erzeugt werden – siehe README, Abschnitt Push."
    fi
  fi
  # Wartungs-Logbuch: die Rolle "Bootswartung" darf nur ein Admin vergeben
  if ! grep -qE "protected_fields.*bootswartung" "$CONF"; then
    cat >> "$CONF" <<'PHP'

// --- Rolle "Bootswartung" darf nur ein Admin vergeben (ergänzt von update.sh) ---
$auth['db']['protected_fields'][] = 'bootswartung';
PHP
    ERGAENZT=1
  fi
  if ! grep -q 'room.unterlagen' "$CONF"; then
    cat >> "$CONF" <<'PHP'

// --- Bootsübersicht, Bootsdaten, Wartungs-Logbuch, Unterlagen (ergänzt von update.sh) ---
$vocab_override['de']['room.infos']          = 'Infos für Mitglieder';
$vocab_override['de']['room.laenge']         = 'Länge üA';
$vocab_override['de']['room.breite']         = 'Breite üA';
$vocab_override['de']['room.tiefgang']       = 'Tiefgang';
$vocab_override['de']['room.masthoehe']      = 'Masthöhe';
$vocab_override['de']['room.gewicht']        = 'Gewicht';
$vocab_override['de']['users.bootswartung']  = 'Bootswartung';
$vocab_override['de']['room.unterlagen']     = 'Unterlagen (Link)';
$vocab_override['de']['room.fotos']          = 'Fotoablage (Link)';
// Gemeinsame Fotoablage für Boote ohne eigene (Nextcloud-Freigabe "Dateiablage"):
// $bb_fotos_url = 'https://cloud.mein-verein.de/s/…';
// Ordner der Schadensfotos zum Ansehen (Link in den Mails), {boot} = Bootsname:
// $bb_fotos_ordner_url = 'https://cloud.mein-verein.de/apps/files/?dir=/Schadensfotos/{boot}';
PHP
    ERGAENZT=1
  fi
  if [ "$ERGAENZT" = 1 ]; then
    echo "==> Fehlende Einstellungen in $CONF ergänzt"
    docker compose restart mrbs >/dev/null
  fi
fi

echo "==> Fertig: MRBS $VERSION läuft (PHP $(docker exec mrbs php -r 'echo PHP_VERSION;' 2>/dev/null || echo unbekannt))."
echo "    Zurück zur vorigen Version:  docker image tag bootsbuchung-mrbs:vorher bootsbuchung-mrbs:latest && docker compose up -d"
