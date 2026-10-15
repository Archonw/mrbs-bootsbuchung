#!/bin/bash
# =============================================================================
# Bootsbuchung – nächtliche Datenbank-Sicherung
#
# Schreibt jede Nacht einen Dump nach data/sicherung/ und behält 14 Tage.
# Läuft per /etc/cron.d/bootsbuchung um 03:15 Uhr (install.sh).
#
# WICHTIG: Die Sicherung liegt auf demselben Server. Für einen echten Schutz
# den Ordner data/sicherung/ (und config/ + .env) regelmäßig woandershin
# kopieren, z. B. per rsync, Nextcloud oder restic.
#
# Zurückspielen:
#   gunzip -c data/sicherung/mrbs-JJJJ-MM-TT.sql.gz | \
#     docker exec -i mrbs_db sh -c 'mariadb -umrbs -p"$MARIADB_PASSWORD" mrbs'
# =============================================================================
set -euo pipefail
DIR="$(cd "$(dirname "$0")/.." && pwd)"
ZIEL="$DIR/data/sicherung"
mkdir -p "$ZIEL"; chmod 700 "$ZIEL"

DATEI="$ZIEL/mrbs-$(date +%F).sql.gz"
# Login-Sitzungen nur als Struktur – die Inhalte sind wertlos und groß
docker exec mrbs_db sh -c 'mariadb-dump -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" --single-transaction --ignore-table=mrbs.mrbs_sessions mrbs \
  && mariadb-dump -h127.0.0.1 -umrbs -p"$MARIADB_PASSWORD" --no-data mrbs mrbs_sessions' | gzip > "$DATEI.tmp"
mv "$DATEI.tmp" "$DATEI"
find "$ZIEL" -name 'mrbs-*.sql.gz' -mtime +14 -delete
echo "[$(date '+%F %T')] Sicherung: $(basename "$DATEI") ($(du -h "$DATEI" | cut -f1))"
