-- =============================================================================
-- Boote nur mit Freigabe (freigabe.patch)
--
-- Die Funktion ist aus, solange in der config.inc.php nicht $bb_freigaben = true;
-- steht. Die Tabellen schaden auch dann nicht. Mehrfach ausführen ist harmlos.
-- =============================================================================
CREATE TABLE IF NOT EXISTS mrbs_bb_freigabe_boot
(
  room_id  INT NOT NULL,
  PRIMARY KEY (room_id),
  FOREIGN KEY (room_id) REFERENCES mrbs_room(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mrbs_bb_freigabe
(
  room_id   INT NOT NULL,
  username  VARCHAR(191) NOT NULL,
  PRIMARY KEY (room_id, username),
  FOREIGN KEY (room_id) REFERENCES mrbs_room(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
