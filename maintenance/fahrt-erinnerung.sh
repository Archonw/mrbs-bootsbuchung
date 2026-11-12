#!/bin/bash
# =============================================================================
# Push-Erinnerung etwa zwei Stunden vor der eigenen Fahrt
#
# Läuft per /etc/cron.d/bootsbuchung alle 15 Minuten (install.sh). Schickt nur
# Push-Nachrichten (keine Mails) an alle, die gebucht haben oder angemeldet
# sind – sofern Push eingerichtet ist und sie Push eingeschaltet haben.
#
# Von Hand:
#   sudo /opt/bootsbuchung/maintenance/fahrt-erinnerung.sh --probe   # nur anzeigen
# =============================================================================
set -euo pipefail
DIR="$(cd "$(dirname "$0")/.." && pwd)"
[ -f "$DIR/.env" ] && . "$DIR/.env"
URL="${BB_URL:-}"
docker exec mrbs php /var/www/html/Themes/verein/fahrt-erinnerung.php ${URL:+--url="$URL"} "$@"
