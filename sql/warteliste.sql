-- =============================================================================
-- Warteliste für volle Fahrten und Kurse (warteliste.patch)
--
-- Wird ein Platz frei, rückt der Erste automatisch nach. Fällt die Buchung
-- weg, verschwindet auch ihre Warteliste. Mehrfach ausführen ist harmlos.
-- =============================================================================
CREATE TABLE IF NOT EXISTS mrbs_bb_warteliste
(
  id           INT NOT NULL AUTO_INCREMENT,
  entry_id     INT NOT NULL,
  username     VARCHAR(191) NOT NULL,
  eingetragen  INT NOT NULL COMMENT 'Unix-Zeit, bestimmt die Reihenfolge',
  PRIMARY KEY (id),
  UNIQUE KEY uq_entry_user (entry_id, username),
  FOREIGN KEY (entry_id) REFERENCES mrbs_entry(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
