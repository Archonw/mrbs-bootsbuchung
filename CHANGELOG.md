# Änderungen

Neue Funktionen kommen mit `sudo /opt/bootsbuchung/update.sh`.
Steht bei einer Version **„Für bestehende Installationen“**, dann bitte die
genannten Zeilen von Hand in `/opt/bootsbuchung/config/config.inc.php`
ergänzen und danach `cd /opt/bootsbuchung && docker compose restart mrbs`.
(Neuinstallationen haben sie automatisch.)

## 2026-10-30 – App fürs Handy und Push-Benachrichtigungen

- **Als App installierbar:** Die Buchung bringt ein App-Manifest, Symbole und
  eine Offline-Seite mit. Am Handy „App installieren“ bzw. „Zum
  Home-Bildschirm“ – oder die Android-App
  [Bootsbuchung für Vereine](https://github.com/Archonw/bootsbuchung-app)
  nehmen und dort die Adresse der Buchung eintragen.
- **Push-Benachrichtigungen:** Neue Seite „Benachrichtigungen“. Wer Push
  einschaltet, bekommt jede Mail der Buchung zusätzlich sofort aufs Handy –
  auf Wunsch nur noch Push statt Mail. Braucht eingeschalteten Mailversand.
- **Handy-Ansicht wie eine App:** Navigation unten (Plan, Mitsegeln, Meine,
  Hinweise, Mehr), „+“-Knopf für eine neue Buchung, Tagesplan als Karten je
  Boot (umschaltbar auf das Raster), aufgeräumtes Buchungsformular mit fester
  Speichern-Leiste, großer Knopf „Anmelden“ für nicht angemeldete Besucher,
  Hinweis, wenn das Handy versehentlich die Desktop-Ansicht zeigt.
- `/.well-known/assetlinks.json` für die Android-App (Vollbild ohne Adressleiste).

Für bestehende Installationen ist nichts von Hand zu tun: `update.sh` legt die
neue Tabelle an, erzeugt den Push-Schlüssel und ergänzt in der
`config.inc.php` die nötigen Zeilen (`$cookie_samesite_lax = true;`, damit
man in der App angemeldet bleibt, den Push-Schlüssel `$bb_push` und die
Beschriftung „Nur Push, keine Mails“). Name und Farbe der App lassen sich mit
`$bb_app` ändern (siehe README, Abschnitt „App und Push“).

## 2026-10-21 – Installation nach Abbruch fortsetzen

Bricht `install.sh` mittendrin ab (z. B. Netzwerkfehler beim Bauen), setzt ein
erneuter Aufruf die Installation jetzt fort. Bisher meldete das Skript dann
„bereits eine Installation“ (ohne Admin-Konto und Wartung) oder scheiterte
beim Herunterladen, weil der Ordner schon da war. Eine fertige Installation
wird weiterhin erkannt und nicht angefasst.

Für bestehende Installationen ist nichts zu tun.

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
