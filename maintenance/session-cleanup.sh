#!/usr/bin/env bash
# =============================================================================
# Bootsbuchung (MRBS) – alte Login-Sitzungen aufräumen
#
# Jeder Aufruf ohne Cookie (vor allem Bots und Scanner, ca. 450 pro Stunde)
# erzeugt in MRBS eine Sitzung, die MRBS erst nach 30 Tagen löscht. Dieses
# Skript löscht Sitzungen, die seit 7 Tagen nicht mehr benutzt wurden.
# Folge für Mitglieder: Wer eine Woche nicht in der Bootbuchung war, muss
# sich neu anmelden.
#
# Läuft per /etc/cron.d/bootsbuchung jede Nacht um 02:45 Uhr
# (eingerichtet von install.sh).
# =============================================================================
set -Eeuo pipefail

DAYS="${DAYS:-7}"
DB_CONTAINER="${DB_CONTAINER:-mrbs_db}"
BATCH=5000

log() { echo "[$(date '+%F %T')] session-cleanup: $*"; }
sql() {
  docker exec -i "$DB_CONTAINER" sh -c \
    'exec mariadb -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -N mrbs'
}

# In Portionen löschen, damit die Tabelle nie lange gesperrt ist
total=0
while :; do
  n=$(echo "DELETE FROM mrbs_sessions WHERE access < UNIX_TIMESTAMP() - $DAYS*86400 LIMIT $BATCH; SELECT ROW_COUNT();" | sql)
  total=$((total + n))
  [ "$n" -lt "$BATCH" ] && break
  sleep 1
done
log "$total Sitzungen gelöscht (älter als $DAYS Tage ungenutzt)"

# Gelöschte Zeilen geben den Plattenplatz erst nach OPTIMIZE wieder frei.
# Sonntags oder nach großen Löschungen (z. B. beim ersten Lauf).
if [ "$(date +%u)" = 7 ] || [ "$total" -gt 50000 ]; then
  log "Gebe Plattenplatz frei (OPTIMIZE TABLE)"
  echo "OPTIMIZE TABLE mrbs_sessions;" | sql >/dev/null
fi
