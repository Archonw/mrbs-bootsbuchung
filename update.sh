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

echo "==> Fertig: MRBS $VERSION läuft (PHP $(docker exec mrbs php -r 'echo PHP_VERSION;' 2>/dev/null || echo unbekannt))."
echo "    Zurück zur vorigen Version:  docker image tag bootsbuchung-mrbs:vorher bootsbuchung-mrbs:latest && docker compose up -d"
