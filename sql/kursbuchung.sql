-- =============================================================================
-- Kursbuchung: zusätzliche Felder (einmalig ausführen, mehrfach ist harmlos)
--
--   mrbs_users.segellehrer  Haken "Segellehrer" beim Benutzer (nur Admins)
--   mrbs_entry.kurs         Buchung ist ein Kurs
--   mrbs_repeat.kurs        dasselbe für Serien (MRBS verlangt beide Tabellen)
--
-- MRBS zeigt Spalten, die es nicht selbst kennt, automatisch als
-- Zusatzfelder an (TINYINT(1) = Haken). Beschriftung über $vocab_override.
--
-- Aufruf auf dem Server:
--   docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < /opt/bootsbuchung/sql/kursbuchung.sql
-- =============================================================================
ALTER TABLE mrbs_users  ADD COLUMN IF NOT EXISTS segellehrer TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE mrbs_entry  ADD COLUMN IF NOT EXISTS kurs        TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE mrbs_repeat ADD COLUMN IF NOT EXISTS kurs        TINYINT(1) NOT NULL DEFAULT 0;
