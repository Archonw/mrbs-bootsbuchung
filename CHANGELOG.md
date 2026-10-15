# Änderungen

Neue Funktionen kommen mit `sudo /opt/bootsbuchung/update.sh`.
Steht bei einer Version **„Für bestehende Installationen“**, dann bitte die
genannten Zeilen von Hand in `/opt/bootsbuchung/config/config.inc.php`
ergänzen und danach `cd /opt/bootsbuchung && docker compose restart mrbs`.
(Neuinstallationen haben sie automatisch.)

## 2026-10-15 – Begriffe in der Verwaltung

In der Verwaltung („Boote“) heißt es jetzt „Boot hinzufügen“, „Boot bearbeiten“,
„E-Mail des Bootswarts“ und „E-Mail des Boot-Paten“ statt Raum/Bereichsadministrator.

**Für bestehende Installationen** – diese Zeilen ergänzen:

```php
$vocab_override['de']['addroom']            = 'Boot hinzufügen';
$vocab_override['de']['editroom']           = 'Boot bearbeiten';
$vocab_override['de']['viewroom']           = 'Boot ansehen';
$vocab_override['de']['norooms']            = 'Keine Boote vorhanden.';
$vocab_override['de']['norooms_enabled']    = 'Keine Boote aktiviert.';
$vocab_override['de']['no_rooms_selected']  = 'Bitte ein Boot auswählen.';
$vocab_override['de']['invalid_room']       = 'Ungültiges Boot!';
$vocab_override['de']['invalid_room_name']  = 'Dieser Bootsname ist hier schon vergeben!';
$vocab_override['de']['ctrl_click']         = 'Strg-Taste plus Mausklick, um mehr als ein Boot auszuwählen';
$vocab_override['de']['sort_key_note']      = 'Schlüssel zum Sortieren der Boote';
$vocab_override['de']['disabled_room_note'] = 'Ein deaktiviertes Boot erscheint nicht mehr im Kalender und kann nicht gebucht werden. Bestehende Buchungen bleiben erhalten.';
$vocab_override['de']['delarea']            = 'Bitte zuerst alle Boote in diesem Bereich löschen.<p>';
$vocab_override['de']['area_room_settings'] = 'Bereiche und Boote';
$vocab_override['de']['room_admin_email']   = 'E-Mail des Boot-Paten';
$vocab_override['de']['area_admin_email']   = 'E-Mail des Bootswarts';
```

## 2026-10-15 – Erste Veröffentlichung
