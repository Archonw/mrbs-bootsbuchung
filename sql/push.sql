-- =============================================================================
-- Push-Benachrichtigungen (einmalig ausführen, mehrfach ist harmlos)
--
--   mrbs_bb_push        ein Eintrag je Gerät, auf dem ein Mitglied Push
--                       eingeschaltet hat (Adresse beim Push-Dienst + Schlüssel)
--   mrbs_users.nur_push Mitglied möchte keine Mails mehr, wenn Push ankommt
--
-- Aufruf auf dem Server:
--   docker exec -i mrbs_db sh -c 'mariadb -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < /opt/bootsbuchung/sql/push.sql
-- =============================================================================
CREATE TABLE IF NOT EXISTS mrbs_bb_push (
  id            INT NOT NULL AUTO_INCREMENT,
  username      VARCHAR(255) NOT NULL,
  endpoint_hash CHAR(64) NOT NULL,
  endpoint      TEXT NOT NULL,
  p256dh        VARCHAR(255) NOT NULL,
  auth          VARCHAR(255) NOT NULL,
  created       INT NOT NULL DEFAULT 0,
  last_used     INT NOT NULL DEFAULT 0,
  user_agent    VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY endpoint_hash (endpoint_hash),
  KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE mrbs_users ADD COLUMN IF NOT EXISTS nur_push TINYINT(1) NOT NULL DEFAULT 0;
