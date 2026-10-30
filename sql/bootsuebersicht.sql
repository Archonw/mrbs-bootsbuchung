-- =============================================================================
-- Bootsübersicht für Mitglieder: Infotext pro Boot (einmalig ausführen,
-- mehrfach ist harmlos)
--
--   mrbs_room.infos   Text für die Detailseite des Bootes in bb_boote.php
--                     (Liegeplatz, Schlüssel, Besonderheiten …). Reiner Text,
--                     Zeilenumbrüche und https-Links werden übernommen.
--   mrbs_room.laenge, breite, tiefgang, masthoehe, gewicht
--                     Bootsdaten, je ein Feld unter "Boot bearbeiten" (nur
--                     Admins), freier Text mit Einheit, z. B. "6,50 m".
--                     Auf der Bootsseite als Tabelle.
--
-- MRBS zeigt die Spalte unter "Boot bearbeiten" automatisch als Textfeld an.
-- Beschriftungen über $vocab_override['de']['room.<spalte>'].
-- Bilder und Dokumente kommen aus dem Nextcloud-Ordner im Feld "Unterlagen
-- (Link)" (sql/unterlagen.sql), ohne die Spalte zeigt die Seite nur sie.
--
-- Aufruf auf dem Server:
--   docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < /opt/bootsbuchung/sql/bootsuebersicht.sql
-- =============================================================================
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS infos TEXT NULL;
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS laenge    VARCHAR(30) NULL;
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS breite    VARCHAR(30) NULL;
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS tiefgang  VARCHAR(30) NULL;
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS masthoehe VARCHAR(30) NULL;
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS gewicht   VARCHAR(30) NULL;
