-- =============================================================================
-- Boot vorübergehend sperren (sperre.patch)
--
--   mrbs_room.gesperrt_bis    letzter Tag der Sperre (JJJJ-MM-TT), leer = nicht gesperrt
--   mrbs_room.gesperrt_grund  kurzer Text für Mitglieder
--
-- Gepflegt unter Verwaltung → Boot bearbeiten. Mehrfach ausführen ist harmlos.
-- =============================================================================
-- Text statt DATE: MRBS speichert leere Felder als '' – das lehnt MariaDB bei DATE ab
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS gesperrt_bis VARCHAR(10) NULL COMMENT 'Boot gesperrt bis einschließlich (JJJJ-MM-TT)';
ALTER TABLE mrbs_room ADD COLUMN IF NOT EXISTS gesperrt_grund VARCHAR(255) NULL COMMENT 'Grund der Sperre';
