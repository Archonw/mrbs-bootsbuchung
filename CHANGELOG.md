# Änderungen

Neue Funktionen kommen mit `sudo /opt/bootsbuchung/update.sh`.
Steht bei einer Version **„Für bestehende Installationen“**, dann bitte die
genannten Zeilen von Hand in `/opt/bootsbuchung/config/config.inc.php`
ergänzen und danach `cd /opt/bootsbuchung && docker compose restart mrbs`.
(Neuinstallationen haben sie automatisch.)

## 2026-11-12 – Wind und Wetter

- **Vorhersage** für das Revier im Plan (Tag: unter dem Datum, Woche: im
  Kopf jedes Tages) und im Buchungsformular für die gewählte Zeit, mit
  Warnung ab Böen 6 Bft. Daten von Open-Meteo, der Server fragt höchstens
  einmal pro Stunde.

**Für bestehende Installationen:** Ist aus, bis in der `config.inc.php` der
Ort eingetragen ist (siehe README, „Wind und Wetter“):

```php
$bb_wetter = ['breite' => 51.745, 'laenge' => 7.215, 'ort' => 'Halterner Stausee'];
```

## 2026-11-12 – Buchungsformular schneller ausfüllen

- Abschnitte **Wann?**, **Welches Boot?**, **Mitsegler und Kurs**.
- Der **Schiffsführer** ist bei einer neuen Buchung mit dem eigenen Namen
  vorbelegt.
- Unter „Ende“ Knöpfe für die **Dauer** (1 bis 4 Stunden).
- **Boote als Kacheln** mit Platzzahl statt Mehrfach-Liste. Ein Klick wählt
  das Boot, mit Strg/Cmd mehrere.
- **„Gibt es etwas zu melden?“** ist zugeklappt („Etwas am Boot kaputt oder
  fehlt etwas?“) und steht nach den Mitseglern; mit Text ist es offen.
- Der Knopf sagt, was passiert: **„Pirat buchen“** statt „Speichern“ (bei
  neuen Buchungen).
- Die Prüfung steht jetzt auch am Computer als Satz über den Knöpfen, grün
  „Das Boot ist frei …“ oder rot mit dem Problem, statt zweier kleiner Haken.

Für bestehende Installationen ist nichts von Hand zu tun.

## 2026-11-12 – Plan: freie Zeiten und lesbare Buchungen

- **Handy, Tagesplan:** Jede Bootskarte hat eine Zeitleiste über den Tag
  (belegt in den Farben der Buchungen, rote Linie für jetzt). Darunter stehen
  die freien Zeiten als Knöpfe – „Frei bis 14:00“, „Ab 16:00“, „Frei ab
  17:00 · buchen“. Ein Tipp öffnet die Buchung gleich mit dieser Startzeit.
- **Computer, Tagesansicht:** In jeder Buchung stehen unter dem Namen die
  Uhrzeit und die Belegung als Schild („0/5“).
- **Computer, Woche aller Boote:** Statt schmaler Streifen, in denen nur
  „Ja“ oder „Opt“ Platz hatte, stehen die Buchungen eines Tages als
  Kärtchen mit Uhrzeit und Name untereinander.
- **Mitsegeln** ist jetzt hell mit grünem Rand statt vollgrün: fällt auf,
  ohne die anderen Buchungen zu übertönen.
- **Legende** unter dem Plan: Buchung, Mitsegler willkommen, Kurs, voll.

Für bestehende Installationen ist nichts von Hand zu tun.

## 2026-11-12 – Kopfzeile am Computer in einer Zeile

- **Kopfzeile:** Am Computer steht alles in einer Zeile: vorne Plan,
  Mitsegeln & Kurse, Boote und Hilfe, die aktuelle Seite ist hervorgehoben.
  Bericht, Verwaltung und Importieren stecken für Admins im Menü
  „Verwaltung“, Meine Buchungen, Benachrichtigungen, Benutzerliste,
  Hell/Dunkel und Abmelden im Menü mit dem eigenen Namen.
- Der Knopf „Start“ neben dem Datum entfällt: Das Datumsfeld springt schon
  beim Auswählen zum Tag.
- Am Handy bleibt alles im Blatt „Mehr“, wie bisher untereinander.

Für bestehende Installationen ist nichts von Hand zu tun.

## 2026-11-12 – Plan zeigt, bis wann gebucht werden kann

- **Plan:** Unter dem Datum steht jetzt „Buchbar bis Do 26.11.“. Liegt der
  angezeigte Tag zu weit in der Zukunft oder außerhalb der Saison, sagt ein
  Hinweis das gleich und der Plan wird blass – bisher merkte man es erst beim
  Speichern. Am letzten buchbaren Tag steht, bis wann die Fahrt enden muss.
- **Hilfe:** Die Regeln (wie viele Tage im Voraus, Uhrzeiten) stehen in der
  deutschen Hilfe jetzt mit den echten Werten des Vereins. Sie kommen aus
  denselben Einstellungen, die MRBS beim Speichern prüft, auch aus den
  Einstellungen des Bereichs in der Verwaltung.

Für bestehende Installationen ist nichts von Hand zu tun.

## 2026-11-07 – Handy: Seitenende nicht mehr von der Navigation verdeckt

- **Handy:** Beim Runterscrollen verdeckten die App-Navigation unten und das
  Impressum-Schild das Ende der Seite, z. B. die letzte Karte in der
  Bootsübersicht. Der Abstand unten wirkt jetzt auf allen Seiten und reicht
  auch für das Impressum-Schild.

Für bestehende Installationen ist nichts von Hand zu tun.

## 2026-10-31 – update.sh räumt den Build-Cache weg

- **Speicherplatz:** `update.sh` löscht am Ende jetzt Docker-Build-Cache, der
  30 Tage nicht benutzt wurde. Der wuchs bisher mit jedem Update und konnte
  auf kleinen Servern mehrere hundert MB belegen. Jüngerer Cache bleibt, damit
  ein Update nicht jedes Mal PHP neu kompiliert. Die Images bleiben, auch
  `bootsbuchung-mrbs:vorher` für den Rückweg.

Für bestehende Installationen ist nichts von Hand zu tun.

## 2026-10-31 – Ansprechpartner der Hilfeseite in der Verwaltung

- **Ansprechpartner:** Auf der MRBS-Hilfeseite stand „Bitte kontaktieren Sie
  Your Administrator …“. Name und E-Mail dafür stellen Admins jetzt unter
  **Verwaltung → Ansprechpartner (Hilfeseite)** ein (ganz unten), z. B.
  „unseren Administrator“. Der Name wird zum Mail-Link.
- Neuinstallationen tragen Name und Adresse des ersten Admins als Vorgabe ein.

Für bestehende Installationen ist nichts von Hand zu tun (gespeichert in der
vorhandenen Tabelle `mrbs_variables`). Einfach `update.sh` und danach unter
Verwaltung eintragen.

## 2026-10-31 – Hilfe zur aktuellen Seite (Deutsch, Englisch, Spanisch)

- **Hilfe:** Der Menüpunkt „Hilfe“ öffnet ein Fenster, das die gerade offene
  Seite erklärt (Belegungsplan, Buchungsformular, Buchung, Mitsegeln & Kurse,
  Boote, Bootsseite, Logbuch, Schaden melden, Benachrichtigungen,
  Benutzerliste, Verwaltung …), am Handy als Blatt von unten. Unten im
  Fenster lässt sich ein anderes Thema wählen; auf `help.php` steht die ganze
  Hilfe zum Aufklappen. Abschnitte für Segellehrer, Bootswartung und Admins
  sehen nur diese.
- **Sprachen:** Deutsch, Englisch, Spanisch, passend zur Sprache von MRBS.
  Weitere Sprachen und eigene Vereinstexte siehe README, „Hilfe und Sprachen“.

Für bestehende Installationen ist nichts von Hand zu tun (nur Theme, kein
SQL). Optional: `$bb_hilfe_url` für einen Link „Ausführliche Anleitung“.

## 2026-10-31 – Saison in der Verwaltung

- **Buchungszeitraum (Saison):** Ab wann und bis wann gebucht werden darf,
  stellen Admins jetzt unter **Verwaltung → Buchungszeitraum (Saison)** ein,
  statt `$min_booking_date` / `$max_booking_date` in der `config.inc.php` zu
  ändern. Gilt sofort, ohne Neustart. Leeres Feld = keine Grenze; Buchungen
  am ersten und letzten Tag sind erlaubt.

Für bestehende Installationen ist nichts von Hand zu tun. Gespeichert wird
in der vorhandenen Tabelle `mrbs_variables` (keine neue Spalte). Wer die
Saison bisher in der `config.inc.php` stehen hat: Die Werte gelten weiter,
bis zum ersten Mal in der Verwaltung gespeichert wird, danach zählt nur noch
die Verwaltung.

## 2026-10-31 – Bootsübersicht, Wartungs-Logbuch, Benutzerliste

- **Bootsübersicht:** Neuer Menüpunkt „Boote“ (am Handy auch unten in der
  App-Leiste) mit allen Booten als Kacheln. Pro Boot eine Seite mit
  Titelbild, Bootsdaten (Länge üA, Breite üA, Tiefgang, Masthöhe, Gewicht),
  „Infos für Mitglieder“, Bildern und Dokumenten aus einem freigegebenen
  Nextcloud-Ordner.
- **Wartungs-Logbuch** pro Boot: Arbeiten mit Datum, Wer, Material, Stunden,
  Kosten. Lesen alle, schreiben die neue Rolle **Bootswartung** und Admins.
- **Schaden melden** mit Text und Fotos; „Schaden behoben“ mit Notiz und Mail
  an das meldende Mitglied; eigene offene Meldungen ändern; beim Löschen
  einer Meldung eine Mail an den Admin mit den zugehörigen Fotos.
- **Wartungsbericht** über alle Boote mit Filter, Summen und CSV-Download.
- **Bootsunterlagen und Fotoablage:** pro Boot ein Link zu den Unterlagen und
  eine Fotoablage (Nextcloud-„Dateiablage“); Schadensfotos lassen sich direkt
  aus der Buchung hochladen.
- **Benutzerliste:** Vorname und Nachname getrennt sortierbar; normale
  Mitglieder sehen nur Namen und Rechte (keine Mailadressen, keine Exporte).
- **Menü:** „Verwaltung“ statt „Boot auswählen“ (nur noch für Admins),
  Reihenfolge: erst Mitglieder-, dann Admin-Punkte, Hilfe zuletzt. Am Handy
  öffnet „Mehr“ ein Blatt von unten; „Hinweise“ ist aus der App-Leiste
  heraus (Benachrichtigungen unter „Mehr“), dafür gibt es „Boote“.

Für bestehende Installationen ist nichts von Hand zu tun: `update.sh` legt
die neuen Spalten und die Tabelle `mrbs_bb_wartung` an und ergänzt in der
`config.inc.php` die Beschriftungen sowie
`$auth['db']['protected_fields'][] = 'bootswartung';` (damit sich niemand die
Rolle selbst geben kann). Danach die Links und Bootsdaten unter
**Verwaltung → Boot bearbeiten** eintragen und Bootswarten den Haken
„Bootswartung“ geben. Wer `$bb_fotos_url` oder `$bb_fotos_ordner_url` nutzen
will, siehe README, Abschnitt „Bootsübersicht und Unterlagen“.

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
