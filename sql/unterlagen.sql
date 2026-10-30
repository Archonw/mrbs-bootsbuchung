-- =============================================================================
-- Bootsunterlagen und Fotoablage: Links pro Boot (einmalig ausführen,
-- mehrfach ist harmlos)
--
--   mrbs_room.unterlagen   Freigabelink (z. B. Nextcloud) zu Handbuch,
--                          Checklisten usw. des Bootes
--   mrbs_room.fotos        Dateiablage-Link für Schadensfotos dieses Bootes
--                          (leer = gemeinsamer Link $bb_fotos_url)
--
-- MRBS zeigt die Spalten unter "Boot bearbeiten" automatisch als Textfelder an.
-- Beschriftung über $vocab_override['de']['room.unterlagen'] / ['room.fotos'].
-- Angezeigt werden die Links von unterlagen.patch.
--
-- Aufruf auf dem Server:
--   docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < /opt/bootsbuchung/sql/unterlagen.sql
-- =============================================================================
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS unterlagen VARCHAR(255) NULL;
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS fotos      VARCHAR(255) NULL;
