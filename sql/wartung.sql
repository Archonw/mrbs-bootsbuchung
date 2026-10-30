-- =============================================================================
-- Wartungs- und Reparatur-Logbuch pro Boot (einmalig ausführen, mehrfach ist
-- harmlos)
--
--   mrbs_users.bootswartung  Rolle "Bootswartung": darf Einträge anlegen,
--                            ändern und löschen. Haken beim Benutzer, den nur
--                            Admins setzen dürfen – dafür 'bootswartung' in
--                            $auth['db']['protected_fields'] aufnehmen!
--   mrbs_bb_wartung         ein Eintrag je Arbeit am Boot oder Schadensmeldung
--
-- Lesen dürfen alle angemeldeten Mitglieder. Seite: bb_wartung.php
-- (bootsuebersicht.patch).
--
-- Aufruf auf dem Server:
--   docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < /opt/bootsbuchung/sql/wartung.sql
-- =============================================================================
ALTER TABLE mrbs_users ADD COLUMN IF NOT EXISTS bootswartung TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS mrbs_bb_wartung
(
  id             INT NOT NULL AUTO_INCREMENT,
  room_id        INT NOT NULL,
  datum          DATE NOT NULL,
  erledigt       TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = offen',
  art            VARCHAR(10) NOT NULL DEFAULT 'arbeit' COMMENT 'arbeit oder meldung (Schaden gemeldet)',
  arbeit         TEXT NOT NULL COMMENT 'Was wurde gemacht? Bei Meldungen: was ist kaputt?',
  gemeldet_von   VARCHAR(100) NULL COMMENT 'Name des Mitglieds, das den Schaden gemeldet hat',
  erledigt_von   VARCHAR(100) NULL COMMENT 'Wer?',
  material       TEXT NULL,
  stunden        DECIMAL(6,2) NULL,
  kosten         DECIMAL(10,2) NULL,
  erstellt_von   VARCHAR(255) NULL,
  erstellt_am    INT NULL COMMENT 'Unix-Zeit',
  geaendert_von  VARCHAR(255) NULL,
  geaendert_am   INT NULL COMMENT 'Unix-Zeit',
  PRIMARY KEY (id),
  KEY idx_room_datum (room_id, datum),
  FOREIGN KEY (room_id) REFERENCES mrbs_room(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- „Schaden behoben“ (nachgerüstet): was bei einer Schadensmeldung gemacht
-- wurde und wann. Die Meldung selbst bleibt in "arbeit" stehen.
ALTER TABLE mrbs_bb_wartung ADD COLUMN IF NOT EXISTS behebung TEXT NULL COMMENT 'Schadensmeldung: was wurde gemacht?' AFTER arbeit;
ALTER TABLE mrbs_bb_wartung ADD COLUMN IF NOT EXISTS behoben_am DATE NULL COMMENT 'Schadensmeldung: behoben am' AFTER behebung;

-- Fotos einer Schadensmeldung (nachgerüstet): Dateinamen in der Fotoablage
-- des Bootes, einer pro Zeile, so wie bb_fotos.php sie benannt hat.
ALTER TABLE mrbs_bb_wartung ADD COLUMN IF NOT EXISTS fotos TEXT NULL COMMENT 'Schadensmeldung: Fotos in der Fotoablage, ein Dateiname pro Zeile' AFTER behoben_am;
