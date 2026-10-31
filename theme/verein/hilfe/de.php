<?php
// =============================================================================
// Hilfe zur Bootsbuchung – Deutsch (Fenster „Hilfe“, siehe ../hilfe.php)
//
// Aufbau:
//   'texte'  Beschriftungen des Hilfe-Fensters
//   'seiten' je Seite der Bootsbuchung ein Eintrag; der Schlüssel kommt aus
//            theme.js (Funktion hilfeSeite()). Reihenfolge = Reihenfolge in
//            der Themenliste.
//   'fuer'   wer den Abschnitt sieht: alle, gast (nicht angemeldet),
//            mitglied (angemeldet), segellehrer, bootswartung, admin
//   'html'   einfaches HTML (p, ul, ol, li, strong, em, a)
//
// Bei Änderungen bitte auch hilfe/en.php und hilfe/es.php anpassen.
// Eigene Texte des Vereins am besten in hilfe/de.local.php (gleicher Aufbau,
// gleichnamige Seiten werden ersetzt) – die übersteht jedes Update.
// =============================================================================

return [
  'texte' => [
    'titel'        => 'Hilfe',
    'schliessen'   => 'Schließen',
    'themen'       => 'Anderes Thema',
    'alle_themen'  => 'Hilfe zur Bootsbuchung',
    'mehr'         => 'Ausführliche Anleitung',
    'ueber'        => 'Über MRBS und Kontakt',
    'laden'        => 'Hilfe wird geladen …',
    'fehler'       => 'Die Hilfe ließ sich gerade nicht laden. Bitte später noch einmal versuchen.',
    'rolle_gast'         => 'Ohne Anmeldung',
    'rolle_segellehrer'  => 'Für Segellehrer',
    'rolle_bootswartung' => 'Für die Bootswartung',
    'rolle_admin'        => 'Für Admins',
  ],

  'seiten' => [

    // -------------------------------------------------------------------------
    'allgemein' => [
      'titel' => 'Überblick: Was finde ich wo?',
      'abschnitte' => [
        ['fuer' => 'alle', 'titel' => 'Diese Hilfe',
         'html' => '<p>Die Hilfe erklärt immer die Seite, auf der du gerade bist. Andere Themen wählst du unten im Fenster aus.</p>'],
        ['fuer' => 'gast', 'titel' => 'Anmelden',
         'html' => '<p>Ohne Anmeldung siehst du nur, wann welches Boot belegt ist. Zum Buchen oben rechts (am Handy unten) auf <strong>Anmelden</strong> tippen. Die Zugangsdaten bekommst du vom Admin.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Das Menü',
         'html' => '<ul>'
                 . '<li><strong>Mitsegeln &amp; Kurse</strong>: Fahrten mit freien Plätzen und Kurse, zum Anmelden.</li>'
                 . '<li><strong>Boote</strong>: alle Boote mit Bildern, Daten, Anleitungen und Wartungs-Logbuch.</li>'
                 . '<li><strong>Benachrichtigungen</strong>: Push-Nachrichten auf dem Handy statt (oder zusätzlich zu) Mails.</li>'
                 . '<li><strong>Benutzerliste</strong>: alle Mitglieder; über deinen eigenen Namen änderst du Passwort und E-Mail-Adresse.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Auf dem Handy',
         'html' => '<p>Unten steht eine Leiste mit <strong>Plan</strong>, <strong>Mitsegeln</strong>, <strong>Boote</strong>, <strong>Meine</strong> (deine Buchungen und Anmeldungen) und <strong>Mehr</strong>. Unter „Mehr“ findest du das übrige Menü, die Datumsauswahl und die Suche.</p>'
                 . '<p>Tipp: Im Browser-Menü <strong>„Zum Startbildschirm hinzufügen“</strong> bzw. <strong>„App installieren“</strong> wählen, dann liegt die Bootsbuchung wie eine App auf dem Handy.</p>'],
        ['fuer' => 'alle', 'titel' => 'Hell oder dunkel',
         'html' => '<p>Der Knopf mit dem Kreis-, Sonnen- oder Mondsymbol in der Kopfzeile schaltet um: <strong>Automatisch → Hell → Dunkel</strong>. Deine Wahl merkt sich der Browser.</p>'],
        ['fuer' => 'alle', 'titel' => 'Wen frage ich?',
         'html' => '<p>Bei Problemen mit der Bootsbuchung den Admin der Bootsbuchung, bei Fragen zu einem Boot den Bootswart oder den Boot-Paten.</p>'],
        ['fuer' => 'admin', 'titel' => 'Menüpunkte für Admins',
         'html' => '<ul>'
                 . '<li><strong>Bericht</strong>: Buchungen auswerten; oben der Link zum Wartungsbericht.</li>'
                 . '<li><strong>Verwaltung</strong>: Saison (Buchungszeitraum), Verein und Boote.</li>'
                 . '<li><strong>Benutzerliste</strong>: Mitglieder anlegen, Rollen vergeben (Segellehrer, Bootswartung, Admin).</li>'
                 . '<li><strong>Importieren</strong>: Buchungen aus einer Kalenderdatei (.ics) übernehmen.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'anmelden' => [
      'titel' => 'Anmelden',
      'abschnitte' => [
        ['fuer' => 'gast', 'titel' => 'So meldest du dich an',
         'html' => '<p>Benutzername und Passwort eintragen und auf <strong>Anmelden</strong> tippen. Die Zugangsdaten bekommst du vom Admin.</p>'
                 . '<p>Wer eine Woche lang nicht in der Bootsbuchung war, wird automatisch abgemeldet und muss sich neu anmelden.</p>'],
        ['fuer' => 'gast', 'titel' => 'Passwort vergessen?',
         'html' => '<p>Melde dich beim Admin, er setzt ein neues Passwort. Danach kannst du es unter deinem Namen selbst ändern.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'plan' => [
      'titel' => 'Belegungsplan (Tag, Woche, Monat)',
      'abschnitte' => [
        ['fuer' => 'alle', 'titel' => 'Ansehen und blättern',
         'html' => '<ul>'
                 . '<li><strong>Tag</strong>, <strong>Woche</strong> und <strong>Monat</strong> wählst du oben in der Leiste.</li>'
                 . '<li>Jede Spalte (bzw. Zeile) ist ein <strong>Boot</strong>, ein farbiger Block eine Buchung.</li>'
                 . '<li>Mit den Pfeilen blätterst du, <strong>Heute</strong> springt zurück zum aktuellen Tag. Ein bestimmtes Datum wählst du über die Datumsauswahl in der Kopfzeile (am Handy unter „Mehr“).</li>'
                 . '<li>Ein Tipp auf eine Buchung zeigt die Einzelheiten.</li>'
                 . '</ul>'],
        ['fuer' => 'gast', 'titel' => 'Nur „belegt“ zu sehen?',
         'html' => '<p>Du siehst nur „belegt“, aber nicht, wer gebucht hat. Zum Buchen erst <strong>Anmelden</strong>.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Farben',
         'html' => '<ul>'
                 . '<li><strong>Hellgrün</strong>: normale Buchung.</li>'
                 . '<li><strong>Kräftiges Grün</strong>: Mitsegler willkommen. Die Zahl dahinter (z. B. „· 2/3“) zeigt Angemeldete und Plätze.</li>'
                 . '<li><strong>Orange</strong>: Kurs eines Segellehrers.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Ein Boot buchen',
         'html' => '<p>In der Tages- oder Wochenansicht auf ein <strong>freies Feld</strong> beim gewünschten Boot und zur gewünschten Uhrzeit tippen. Am Handy geht es auch über den runden <strong>+</strong>-Knopf. Dann öffnet sich das Buchungsformular.</p>'
                 . '<p>Gebucht werden kann nur während der <strong>Saison</strong>. Wie weit im Voraus und zu welchen Uhrzeiten, legt der Verein fest; außerhalb davon lässt sich nicht speichern.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Am Handy: Liste oder Raster',
         'html' => '<p>Die Tagesansicht zeigt am Handy jedes Boot als Karte mit seinen Buchungen. Mit dem Umschalter <strong>Liste / Raster</strong> bekommst du die klassische Tabelle zurück.</p>'],
        ['fuer' => 'admin', 'titel' => 'Fremde Buchungen, Serien, Saison',
         'html' => '<p>Admins können auch fremde Buchungen öffnen, ändern und löschen und als Einzige <strong>Serien</strong> (regelmäßige Termine) anlegen. Die Saison stellst du unter <strong>Verwaltung</strong> ein.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'buchen' => [
      'titel' => 'Buchungsformular',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Die Felder',
         'html' => '<ul>'
                 . '<li><strong>Kurzbeschreibung</strong>: meist dein Name, er steht im Kalender. Bei Mitsegel-Fahrten heißt das Feld „Schiffsführer“, bei Kursen „Segellehrer“.</li>'
                 . '<li><strong>Anfang</strong> und <strong>Ende</strong>: Datum und Uhrzeit.</li>'
                 . '<li><strong>Boot</strong>: ist schon ausgewählt, lässt sich aber ändern.</li>'
                 . '<li><strong>Öffentlich</strong> oder <strong>Privat</strong>: Bei „Privat“ sehen andere nur „belegt“.</li>'
                 . '</ul>'
                 . '<p>Zum Schluss <strong>Speichern</strong>. Du bekommst eine Bestätigung per E-Mail mit einem Kalendereintrag.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Mitsegler einladen',
         'html' => '<p>Haken <strong>„Mitsegler willkommen“</strong> setzen. Optional <strong>„Plätze begrenzen“</strong> und die Zahl der <strong>Plätze für Mitsegler</strong> eintragen; daneben steht, wie viele höchstens gehen (Kapazität des Bootes minus dein Platz). Die Fahrt ist dann immer öffentlich und erscheint unter „Mitsegeln &amp; Kurse“.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Gibt es etwas zu melden?',
         'html' => '<p>Nur ausfüllen, wenn am Boot etwas kaputt ist oder fehlt. Dann geht automatisch eine Mail an den Bootswart und den Boot-Paten. Mit <strong>„Fotos vom Schaden hier hochladen“</strong> kannst du Bilder mitschicken. Das Feld lässt sich auch nach der Fahrt noch ausfüllen (Buchung öffnen, <strong>Bearbeiten</strong>).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Lässt sich nicht speichern?',
         'html' => '<ul>'
                 . '<li>Ist das Boot schon belegt, zeigt das Formular, mit welcher Buchung es sich überschneidet. Andere Zeit oder anderes Boot wählen.</li>'
                 . '<li>Der Tag liegt zu weit in der Zukunft oder außerhalb der Saison.</li>'
                 . '<li>Am Handy steht der Knopf <strong>Speichern</strong> fest unten am Bildschirm.</li>'
                 . '</ul>'],
        ['fuer' => 'segellehrer', 'titel' => 'Als Kurs anbieten',
         'html' => '<p>Haken <strong>„Als Kurs anbieten“</strong> setzen. Damit ist der Kurs automatisch zur Anmeldung offen und öffentlich, das erste Feld heißt „Segellehrer“. Bei Kursen darfst du alle Plätze des Bootes vergeben. Kursreihen mit mehreren Terminen legt ein Admin an.</p>'],
        ['fuer' => 'admin', 'titel' => 'Serien und fremde Buchungen',
         'html' => '<p>Unter <strong>Wiederholung</strong> legst du Serien an, z. B. jeden Samstag im Mai. Ändert sich später die Serie, bleiben Anmeldungen erhalten und die Teilnehmer bekommen eine Mail. Für einen Segellehrer trägst du dessen Namen ein.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'buchung' => [
      'titel' => 'Eine Buchung ansehen',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Was steht hier?',
         'html' => '<p>Boot, Zeit, wer gebucht hat und gegebenenfalls die Bemerkung. Bei vielen Booten steht dort auch der Link <strong>Bootsunterlagen</strong> (Handbuch, Checklisten, z. B. in der Nextcloud des Vereins).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Eigene Buchung ändern oder löschen',
         'html' => '<p>Unten auf <strong>Bearbeiten</strong> oder <strong>Löschen</strong>. Fremde Buchungen kannst du nicht ändern. Bitte lösche Buchungen, die du nicht wahrnimmst, damit jemand anderes das Boot nutzen kann. Angemeldete Mitsegler bekommen bei Absage oder Änderung automatisch eine Mail.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Mitsegeln: anmelden und abmelden',
         'html' => '<p>Ist die Fahrt offen für Mitsegler, steht hier <strong>Anmelden</strong> bzw. <strong>Abmelden</strong>. Du bekommst eine Bestätigung und am Vortag um 18 Uhr eine Erinnerung. „Alle Plätze sind belegt.“ heißt: die Fahrt ist voll.</p>'
                 . '<p>Gehört der Termin zu einer Kursreihe, gibt es zusätzlich <strong>„Für alle Termine anmelden“</strong> und <strong>„Von allen Terminen abmelden“</strong>.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Schaden nach der Fahrt melden',
         'html' => '<p>Eigene Buchung öffnen, <strong>Bearbeiten</strong>, Feld „Gibt es etwas zu melden?“ ausfüllen und speichern. Fotos lädst du über <strong>„Fotos vom Schaden hier hochladen“</strong> hoch (auch hier unter „Schadensfotos“).</p>'],
        ['fuer' => 'segellehrer', 'titel' => 'Teilnehmerliste drucken',
         'html' => '<p>Der Knopf <strong>„Teilnehmerliste drucken“</strong> öffnet eine Liste aller Angemeldeten zum Ausdrucken oder Abhaken am Steg. Sehen dürfen sie Ersteller, Segellehrer und Admins.</p>'],
        ['fuer' => 'admin', 'titel' => 'Fremde Buchungen',
         'html' => '<p>Du kannst jede Buchung bearbeiten, löschen und kopieren und Teilnehmer austragen.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'mitsegeln' => [
      'titel' => 'Mitsegeln & Kurse',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Was steht hier?',
         'html' => '<p>Alle kommenden Fahrten mit freien Plätzen und alle Kurse, nach Tagen sortiert. Mit <strong>Alle / Kurse / Mitsegeln / Meine</strong> filterst du. „Meine“ zeigt deine eigenen Buchungen und alles, wofür du angemeldet bist.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Bei einer Fahrt mitsegeln',
         'html' => '<ol><li>Fahrt oder Kurs antippen.</li><li><strong>Anmelden</strong> tippen.</li><li>Du bekommst eine Bestätigung per Mail, der Schiffsführer auch. Am Vortag um 18 Uhr kommt eine Erinnerung.</li></ol>'
                 . '<p>Doch keine Zeit? Fahrt öffnen und <strong>Abmelden</strong>, bitte so früh wie möglich.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Selbst Mitsegler suchen',
         'html' => '<p>Beim Buchen den Haken <strong>„Mitsegler willkommen“</strong> setzen. Deine Fahrt erscheint dann hier. Bei der eigenen Buchung kannst du dich nicht selbst anmelden, du fährst ja schon mit.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boote' => [
      'titel' => 'Bootsübersicht',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Unsere Boote',
         'html' => '<p>Alle Boote mit Bild und Name. Tippe auf ein Boot für Infos, Bilder, Anleitungen (z. B. zum Auf- und Abbau) und das Wartungs-Logbuch. Von dort aus kannst du das Boot auch buchen oder einen Schaden melden.</p>'],
        ['fuer' => 'admin', 'titel' => 'Bilder und Dokumente pflegen',
         'html' => '<p>Bilder und Dokumente kommen aus dem freigegebenen Nextcloud-Ordner im Feld „Unterlagen (Link)“ des Bootes (optional); als Kachelbild dient <strong>titelbild.jpg</strong>. Boote anlegen und ändern unter <strong>Verwaltung</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boot' => [
      'titel' => 'Bootsseite',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Was steht hier?',
         'html' => '<ul>'
                 . '<li>Oben Bild, Kapazität und Bootsdaten (Länge, Breite, Tiefgang …).</li>'
                 . '<li><strong>Infos</strong> zum Boot, z. B. Liegeplatz und wo der Schlüssel ist.</li>'
                 . '<li><strong>Bilder</strong> (ein Tipp zeigt sie groß) und <strong>Dokumente</strong> wie Anleitungen und Checklisten (ein Tipp lädt sie herunter).</li>'
                 . '<li>Unter <strong>Wartung &amp; Reparaturen</strong> die letzten Arbeiten am Boot und offene Schäden. <strong>Logbuch öffnen</strong> zeigt alle Einträge.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Buchen',
         'html' => '<p><strong>Belegung ansehen &amp; buchen</strong> zeigt die Woche dieses Bootes. Dort ein freies Feld antippen.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Schaden melden',
         'html' => '<p>Mit <strong>Schaden melden</strong> beschreibst du, was kaputt ist, und hängst Fotos an. Das geht auch ohne Buchung. Bootswart und Boot-Paten bekommen eine Mail, die Meldung steht danach als <strong>offen</strong> im Logbuch.</p>'],
        ['fuer' => 'bootswartung', 'titel' => 'Logbuch pflegen',
         'html' => '<p>Unter jedem Logbuch-Eintrag stehen <strong>Bearbeiten · Löschen</strong>, bei offenen Meldungen der Knopf <strong>Schaden behoben</strong>.</p>'],
        ['fuer' => 'admin', 'titel' => 'Boot bearbeiten, Dateien neu laden',
         'html' => '<p><strong>Boot bearbeiten</strong> ändert Infotext, Bootsdaten und Links. Neue Dateien aus dem Nextcloud-Ordner erscheinen nach spätestens 15 Minuten, sofort mit <strong>Dateien neu laden</strong> ganz unten.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'logbuch' => [
      'titel' => 'Wartungs-Logbuch',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Was steht hier?',
         'html' => '<p>Alle Arbeiten und Schadensmeldungen zu diesem Boot, die neuesten zuerst. <strong>Offen</strong> heißt: der Schaden ist noch nicht behoben. Der Knopf <strong>Bericht</strong> zeigt den Wartungsbericht über alle Boote.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Eigene Meldung ändern',
         'html' => '<p>Solange deine Meldung offen ist, kannst du sie über <strong>Meldung ändern</strong> korrigieren oder Fotos nachreichen. Die geänderte Meldung geht noch einmal an Bootswart und Boot-Paten. Ist der Schaden behoben, bekommst du eine Mail.</p>'],
        ['fuer' => 'bootswartung', 'titel' => 'Einträge anlegen und Schäden abschließen',
         'html' => '<ul>'
                 . '<li><strong>Neuer Eintrag</strong>: Datum, Status, was gemacht wurde, wer, Material, Stunden und Kosten.</li>'
                 . '<li><strong>Schaden behoben</strong> bei einer offenen Meldung: eintragen, was gemacht wurde und wann. Wer den Schaden gemeldet hat, bekommt eine Mail.</li>'
                 . '<li><strong>Bearbeiten · Löschen</strong> unter jedem Eintrag. Fotos einer gelöschten Meldung liegen weiter in der Fotoablage und müssen dort von Hand gelöscht werden; die Seite nennt die Dateien.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'schaden' => [
      'titel' => 'Schaden melden',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'So geht es',
         'html' => '<ol><li>Unter <strong>Was ist kaputt?</strong> kurz beschreiben, was los ist.</li>'
                 . '<li>Bei Bedarf <strong>Fotos hinzufügen</strong>: Das Handy fragt nach Kamera, Video oder Dateien, mehrere Fotos auf einmal gehen.</li>'
                 . '<li><strong>Meldung abschicken</strong>.</li></ol>'
                 . '<p>Bootswart und Boot-Paten bekommen eine Mail, die Meldung steht im Logbuch als offen. Für Notfälle (Boot nicht fahrbereit, Unfall) bitte zusätzlich direkt beim Bootswart anrufen.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'wartungsbericht' => [
      'titel' => 'Wartungsbericht',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Auswerten',
         'html' => '<p>Filtere nach Zeitraum (Vorgabe: 1. Januar bis heute), Boot, Art (Arbeiten oder Schadensmeldungen), Status und Suchtext. Oben stehen die Summen je Boot (Einträge, offene Meldungen, Stunden, Kosten), darunter alle Einträge. <strong>Als CSV (Excel)</strong> lädt dieselbe Auswahl als Datei herunter.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'bericht' => [
      'titel' => 'Bericht',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Buchungen auswerten',
         'html' => '<p>Zeitraum und bei Bedarf Boot oder Suchbegriffe wählen, dann <strong>Bericht erstellen</strong>. Unter <strong>Ausgabe</strong> wählst du zwischen Bericht (Liste der Buchungen), Zusammenfassung (z. B. Stunden je Boot) oder beidem, unter Format auch eine Datei zum Herunterladen (CSV, iCalendar).</p>'
                 . '<p>Für Arbeiten und Schäden an den Booten gibt es oben den Link <strong>Wartung &amp; Reparaturen auswerten</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'suche' => [
      'titel' => 'Suche',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Buchungen suchen',
         'html' => '<p>Das Suchfeld in der Kopfzeile (am Handy unter „Mehr“) findet kommende Buchungen, deren Beschreibung den Suchbegriff enthält, z. B. einen Namen. Ein Tipp auf einen Treffer öffnet die Buchung.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'benachrichtigungen' => [
      'titel' => 'Benachrichtigungen',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Push auf dem Handy',
         'html' => '<p>Mit <strong>Push auf diesem Gerät einschalten</strong> bekommst du alles, was die Bootsbuchung dir per Mail schickt, auch als Nachricht aufs Handy. Das gilt pro Gerät: auf jedem Handy oder Computer einzeln einschalten. <strong>Test-Nachricht senden</strong> zeigt, ob es klappt.</p>'
                 . '<p>Mit <strong>„Keine Mails mehr, wenn die Push-Nachricht ankommt“</strong> sparst du dir die doppelte Nachricht. Kommt Push nicht an, geht weiter die Mail raus.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Klappt nicht?',
         'html' => '<ul><li>Auf dem iPhone geht Push nur, wenn die Bootsbuchung über „Zum Home-Bildschirm“ installiert ist und von dort geöffnet wird.</li>'
                 . '<li>Hast du Benachrichtigungen für die Seite einmal abgelehnt, in den Einstellungen des Browsers bzw. Handys wieder erlauben.</li></ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'benutzer' => [
      'titel' => 'Benutzerliste und eigenes Profil',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Mitglieder finden',
         'html' => '<p>Die Liste zeigt Vorname, Nachname und Rechte aller Mitglieder. Mit dem Suchfeld findest du schnell jemanden, ein Tipp auf die Überschrift sortiert.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Passwort und E-Mail ändern',
         'html' => '<p>Auf deinen <strong>eigenen Namen</strong> tippen, Passwort bzw. E-Mail-Adresse ändern und speichern. Stimmt die E-Mail-Adresse nicht, bekommst du keine Bestätigungen.</p>'],
        ['fuer' => 'admin', 'titel' => 'Mitglieder verwalten',
         'html' => '<ul>'
                 . '<li><strong>Neuen Benutzer hinzufügen</strong>: Benutzername, Anzeigename als „Vorname Nachname“, E-Mail-Adresse, Start-Passwort, Rechte „Benutzer“.</li>'
                 . '<li>Haken <strong>Segellehrer</strong> (darf Kurse anbieten) und <strong>Bootswartung</strong> (darf ins Wartungs-Logbuch schreiben) setzen nur Admins.</li>'
                 . '<li>Passwort vergessen: Benutzer öffnen, neues Passwort eintragen. Ausgetreten: Benutzer löschen, alte Buchungen bleiben.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'verwaltung' => [
      'titel' => 'Verwaltung',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Saison (Buchungszeitraum)',
         'html' => '<p>Unter <strong>Buchungszeitraum (Saison)</strong> Saisonbeginn und -ende eintragen und speichern. Gilt sofort; ein leeres Feld heißt „keine Grenze“. Am ersten und letzten Tag darf gebucht werden. <strong>Jedes Jahr vor Saisonbeginn anpassen!</strong></p>'],
        ['fuer' => 'admin', 'titel' => 'Verein und Boote',
         'html' => '<ul>'
                 . '<li><strong>Verein bearbeiten</strong>: u. a. die E-Mail des Bootswarts für Schadensmeldungen.</li>'
                 . '<li><strong>Boot hinzufügen</strong> bzw. beim Boot <strong>Bearbeiten</strong>: Name, Kapazität, Bootsdaten, Infos, Links zu Unterlagen und Fotoablage, E-Mail des Boot-Paten.</li>'
                 . '<li>Ein Boot <strong>deaktivieren</strong> statt löschen: Es verschwindet aus dem Plan, alte Buchungen bleiben.</li>'
                 . '</ul>'],
        ['fuer' => 'admin', 'titel' => 'Ansprechpartner (Hilfeseite)',
         'html' => '<p>Ganz unten stellst du ein, wen die Hilfeseite bei Fragen nennt: <strong>Name</strong> (wird in den Satz „Bitte kontaktieren Sie … für Fragen …“ eingesetzt, z. B. „unseren Administrator“) und <strong>E-Mail</strong> (der Name wird zum Mail-Link). Ein leeres Feld nimmt den Wert aus der config.inc.php.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boot_bearbeiten' => [
      'titel' => 'Boot bearbeiten',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Die wichtigsten Felder',
         'html' => '<ul>'
                 . '<li><strong>Name</strong> erscheint als Spalte im Plan, <strong>Sortierschlüssel</strong> bestimmt die Reihenfolge.</li>'
                 . '<li><strong>Kapazität</strong>: Personen an Bord, begrenzt die Mitsegler-Plätze (0 = keine Grenze).</li>'
                 . '<li><strong>E-Mail des Boot-Paten</strong>: bekommt Schadensmeldungen zu diesem Boot (mehrere Adressen mit Komma).</li>'
                 . '<li><strong>Bootsdaten</strong>: Länge, Breite, Tiefgang, Masthöhe, Gewicht, jeweils mit Einheit, z. B. „6,50 m“.</li>'
                 . '<li><strong>Infos für Mitglieder</strong>: reiner Text, Leerzeile = neuer Absatz, Links werden klickbar.</li>'
                 . '<li><strong>Unterlagen (Link)</strong> (optional): Link zu Handbuch und Checklisten. Ist es eine Nextcloud-Freigabe „Nur lesen“ ohne Passwort, kommen daraus auch Bilder und Dokumente der Bootsseite.</li>'
                 . '<li><strong>Fotoablage (Link)</strong> (optional): Ablage für Schadensfotos, z. B. eine Nextcloud-Freigabe „Dateiablage“, nicht im Unterlagen-Ordner.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'verein_bearbeiten' => [
      'titel' => 'Verein bearbeiten',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Einstellungen des Vereins',
         'html' => '<p>Wichtig ist vor allem die <strong>E-Mail des Bootswarts</strong>: Sie bekommt jede Schadensmeldung. Dazu kommen Grundeinstellungen wie Buchungszeiten und wie weit im Voraus gebucht werden darf. Die Saison selbst stellst du unter <strong>Verwaltung → Buchungszeitraum</strong> ein.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'import' => [
      'titel' => 'Importieren',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Buchungen aus einer Kalenderdatei',
         'html' => '<p>Eine iCalendar-Datei (.ics, z. B. aus einem anderen Kalender) hochladen, Verein und Boot wählen und importieren. Mit dem Haken „Termine überspringen, die mit bestehenden Terminen in Konflikt sind“ bleiben vorhandene Buchungen unangetastet. Vorher am besten mit wenigen Terminen ausprobieren.</p>'],
      ],
    ],
  ],
];
