#!/bin/bash
# =============================================================================
# Erinnerungsmail am Vortag für Kurse und "Mitsegler willkommen"
#
# Ruft im MRBS-Container bb_erinnerung.php auf (kommt mit kurs.patch).
# Läuft per /etc/cron.d/bootsbuchung jeden Tag um 18:00 Uhr (install.sh).
#
# Von Hand:
#   sudo /opt/bootsbuchung/maintenance/kurs-erinnerung.sh --probe          # nur anzeigen
#   sudo /opt/bootsbuchung/maintenance/kurs-erinnerung.sh --tag=2026-10-21 # bestimmter Tag
# =============================================================================
set -euo pipefail

DIR="$(cd "$(dirname "$0")/.." && pwd)"
# Adresse der Buchungsseite (für die Links in den Mails) aus der .env
[ -f "$DIR/.env" ] && . "$DIR/.env"
URL="${BB_URL:-}"

echo "[$(date '+%F %T')] Kurs-Erinnerung"
docker exec mrbs php /var/www/html/bb_erinnerung.php ${URL:+--url="$URL"} "$@"
