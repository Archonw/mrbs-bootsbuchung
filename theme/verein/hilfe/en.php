<?php
// =============================================================================
// Boat booking help – English (help window, see ../hilfe.php)
//
// Same structure as hilfe/de.php; use this file as the template for another
// language (e.g. copy to hilfe/fr.php and translate the texts).
//   'texte'  labels of the help window
//   'seiten' one entry per page; the keys come from theme.js (hilfeSeite())
//   'fuer'   who sees the section: alle (everyone), gast (not logged in),
//            mitglied (logged in), segellehrer (instructor), bootswartung
//            (boat maintenance), admin
//   'html'   simple HTML (p, ul, ol, li, strong, em, a)
//
// Pages added by this project (sail along, boats, logbook …) are in German
// only, so their buttons are quoted in German here, with a translation.
// =============================================================================

return [
  'texte' => [
    'titel'        => 'Help',
    'schliessen'   => 'Close',
    'themen'       => 'Other topic',
    'alle_themen'  => 'Boat booking help',
    'mehr'         => 'Full guide',
    'ueber'        => 'About MRBS and contact',
    'laden'        => 'Loading help …',
    'fehler'       => 'The help could not be loaded. Please try again later.',
    'rolle_gast'         => 'Not logged in',
    'rolle_segellehrer'  => 'For instructors',
    'rolle_bootswartung' => 'For boat maintenance',
    'rolle_admin'        => 'For admins',
  ],

  'seiten' => [

    // -------------------------------------------------------------------------
    'allgemein' => [
      'titel' => 'Overview: what is where?',
      'abschnitte' => [
        ['fuer' => 'alle', 'titel' => 'This help',
         'html' => '<p>The help always explains the page you are on. Choose other topics at the bottom of the window.</p>'],
        ['fuer' => 'gast', 'titel' => 'Logging in',
         'html' => '<p>Tap <strong>Log in</strong> at the top right (at the bottom on a phone). You get your user name and password from the admin.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'The menu',
         'html' => '<ul>'
                 . '<li><strong>Mitsegeln &amp; Kurse</strong> (sail along &amp; courses): trips with free places and courses you can sign up for.</li>'
                 . '<li><strong>Boote</strong> (boats): all boats with pictures, data, manuals and maintenance logbook.</li>'
                 . '<li><strong>Benachrichtigungen</strong> (notifications): push messages on your phone instead of (or as well as) emails.</li>'
                 . '<li><strong>Users</strong>: all members; tap your own name to change your password and email address.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'On a phone',
         'html' => '<p>At the bottom there is a bar with <strong>Plan</strong>, <strong>Mitsegeln</strong> (sail along), <strong>Boote</strong> (boats), <strong>Meine</strong> (your bookings and sign-ups) and <strong>Mehr</strong> (more). “Mehr” opens the rest of the menu, the date picker and the search.</p>'
                 . '<p>Tip: choose <strong>“Add to Home screen”</strong> or <strong>“Install app”</strong> in your browser menu to use the booking like an app.</p>'],
        ['fuer' => 'alle', 'titel' => 'Light or dark',
         'html' => '<p>The button with the circle, sun or moon icon in the header switches between <strong>Automatisch → Hell → Dunkel</strong> (automatic, light, dark). Your browser remembers the choice.</p>'],
        ['fuer' => 'alle', 'titel' => 'Who can I ask?',
         'html' => '<p>For problems with the booking system ask its admin; for questions about a boat ask the boat keeper or the person looking after that boat.</p>'],
        ['fuer' => 'admin', 'titel' => 'Menu items for admins',
         'html' => '<ul>'
                 . '<li><strong>Report</strong>: analyse bookings; at the top the link to the maintenance report.</li>'
                 . '<li><strong>Verwaltung</strong> (administration): season (booking period), club and boats.</li>'
                 . '<li><strong>Users</strong>: add members, give roles (instructor, boat maintenance, admin).</li>'
                 . '<li><strong>Import</strong>: take over bookings from a calendar file (.ics).</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'anmelden' => [
      'titel' => 'Logging in',
      'abschnitte' => [
        ['fuer' => 'gast', 'titel' => 'How to log in',
         'html' => '<p>Enter your user name and password and tap <strong>Log in</strong>. You get them from the admin.</p>'
                 . '<p>If you have not used the booking for a week, you are logged out automatically and need to log in again.</p>'],
        ['fuer' => 'gast', 'titel' => 'Forgotten your password?',
         'html' => '<p>Ask the admin to set a new one. You can then change it yourself under your name.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'plan' => [
      'titel' => 'Booking plan (day, week, month)',
      'abschnitte' => [
        ['fuer' => 'alle', 'titel' => 'Viewing and paging',
         'html' => '<ul>'
                 . '<li>Choose <strong>day</strong>, <strong>week</strong> or <strong>month</strong> in the bar at the top.</li>'
                 . '<li>Each column (or row) is a <strong>boat</strong>, each coloured block a booking.</li>'
                 . '<li>The arrows page back and forward; <strong>today</strong> jumps back to the current day. Pick a specific date with the date picker in the header (on a phone under “Mehr”).</li>'
                 . '<li>Tap a booking to see its details.</li>'
                 . '</ul>'],
        ['fuer' => 'gast', 'titel' => 'Only “booked” visible?',
         'html' => '<p>Without logging in you only see that a boat is booked, not by whom. Log in to book.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Colours',
         'html' => '<ul>'
                 . '<li><strong>Light green</strong>: normal booking.</li>'
                 . '<li><strong>Strong green</strong>: crew welcome. The number after it (e.g. “· 2/3”) shows people signed up and places.</li>'
                 . '<li><strong>Orange</strong>: course run by an instructor.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Booking a boat',
         'html' => '<p>In the day or week view tap a <strong>free slot</strong> for the boat and time you want. On a phone you can also use the round <strong>+</strong> button. The booking form opens.</p>'
                 . '<p>Bookings are only possible during the <strong>season</strong>. How far ahead and at what times is set by the club; outside of that the booking cannot be saved.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'On a phone: list or grid',
         'html' => '<p>On a phone the day view shows each boat as a card with its bookings. The <strong>Liste / Raster</strong> (list / grid) switch brings back the classic table.</p>'],
        ['fuer' => 'admin', 'titel' => 'Other people’s bookings, series, season',
         'html' => '<p>Admins can open, change and delete any booking and are the only ones who can create <strong>series</strong> (repeating bookings). Set the season under <strong>Verwaltung</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'buchen' => [
      'titel' => 'Booking form',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'The fields',
         'html' => '<ul>'
                 . '<li><strong>Brief description</strong>: usually your name; it is shown in the plan.</li>'
                 . '<li><strong>Start time</strong> and <strong>End time</strong>: date and time.</li>'
                 . '<li><strong>Room</strong> (the boat): already selected, but you can change it.</li>'
                 . '<li><strong>Public</strong> or <strong>Private</strong>: with “Private” others only see “booked”.</li>'
                 . '</ul>'
                 . '<p>Finally tap <strong>Save</strong>. You get a confirmation email with a calendar entry.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Inviting crew',
         'html' => '<p>Tick <strong>Allow registration</strong> (crew welcome). Optionally tick <strong>Set event capacity</strong> and enter the number of places; next to it you see the maximum (boat capacity minus your own place). The trip is then always public and appears under “Mitsegeln &amp; Kurse”.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Anything to report?',
         'html' => '<p>Only fill in <strong>Full description</strong> if something on the boat is broken or missing. An email then goes automatically to the boat keeper and the person looking after the boat. With the photo upload button below the field you can add pictures. You can also fill it in after your trip (open the booking, <strong>Edit</strong>).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Cannot save?',
         'html' => '<ul>'
                 . '<li>If the boat is already booked, the form shows which booking overlaps. Choose another time or boat.</li>'
                 . '<li>The day is too far ahead or outside the season.</li>'
                 . '<li>On a phone the <strong>Save</strong> button stays at the bottom of the screen.</li>'
                 . '</ul>'],
        ['fuer' => 'segellehrer', 'titel' => 'Offering a course',
         'html' => '<p>Tick <strong>“Als Kurs anbieten”</strong> (offer as course). The course is then automatically open for sign-up and public. For courses you may give away all places on the boat. Courses with several dates are set up as a series by an admin.</p>'],
        ['fuer' => 'admin', 'titel' => 'Series and other people’s bookings',
         'html' => '<p>Use <strong>Repeat type</strong> to create a series, e.g. every Saturday in May. If the series changes later, sign-ups are kept and participants get an email. For an instructor, enter their name.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'buchung' => [
      'titel' => 'Viewing a booking',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'What is shown here?',
         'html' => '<p>Boat, time, who booked it and any remarks. For many boats there is also a link to the <strong>boat documents</strong> (manual, checklists).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Changing or deleting your booking',
         'html' => '<p>Tap <strong>Edit</strong> or <strong>Delete</strong> at the bottom. You cannot change other people’s bookings. Please delete bookings you will not use so someone else can take the boat. Crew who signed up get an email automatically if you cancel or change the trip.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Sailing along: sign up and cancel',
         'html' => '<p>If the trip is open for crew, you see buttons to sign up or cancel. You get a confirmation and a reminder the evening before. “This event is full.” means there are no places left.</p>'
                 . '<p>If the date belongs to a course series, there are also <strong>“Für alle Termine anmelden”</strong> (sign up for all dates) and <strong>“Von allen Terminen abmelden”</strong> (cancel all dates).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Reporting damage after your trip',
         'html' => '<p>Open your own booking, tap <strong>Edit</strong>, fill in “Full description” and save. Photos can be uploaded with the photo button (also shown here).</p>'],
        ['fuer' => 'segellehrer', 'titel' => 'Printing the participant list',
         'html' => '<p>The button <strong>“Teilnehmerliste drucken”</strong> (print participant list) opens a list of everyone signed up, to print or tick off at the jetty. The creator of the booking, instructors and admins can see it.</p>'],
        ['fuer' => 'admin', 'titel' => 'Other people’s bookings',
         'html' => '<p>You can edit, delete and copy any booking and remove participants.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'mitsegeln' => [
      'titel' => 'Sail along & courses',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'What is shown here?',
         'html' => '<p>All upcoming trips with free places and all courses, sorted by day. Filter with <strong>Alle / Kurse / Mitsegeln / Meine</strong> (all / courses / sail along / mine). “Meine” shows your own bookings and everything you have signed up for.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Joining a trip',
         'html' => '<ol><li>Tap a trip or course.</li><li>Sign up.</li><li>You and the skipper get a confirmation email. A reminder comes the evening before.</li></ol>'
                 . '<p>Can’t make it after all? Open the trip and cancel, as early as possible please.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Looking for crew yourself',
         'html' => '<p>Tick <strong>Allow registration</strong> when booking. Your trip then appears here. You cannot sign up for your own booking, you are already on board.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boote' => [
      'titel' => 'Boat overview',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Our boats',
         'html' => '<p>All boats with picture and name. Tap a boat for information, pictures, manuals (e.g. rigging and unrigging) and the maintenance logbook. From there you can also book the boat or report damage.</p>'],
        ['fuer' => 'admin', 'titel' => 'Maintaining pictures and documents',
         'html' => '<p>Pictures and documents come from the shared Nextcloud folder in the boat’s “Unterlagen (Link)” field (optional); <strong>titelbild.jpg</strong> is used as the tile picture. Add and change boats under <strong>Verwaltung</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boot' => [
      'titel' => 'Boat page',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'What is shown here?',
         'html' => '<ul>'
                 . '<li>At the top: picture, capacity and boat data (length, beam, draught …).</li>'
                 . '<li><strong>Information</strong> about the boat, e.g. berth and where to find the key.</li>'
                 . '<li><strong>Bilder</strong> (pictures, tap to enlarge) and <strong>Dokumente</strong> (documents) such as manuals and checklists (tap to download).</li>'
                 . '<li>Under <strong>Wartung &amp; Reparaturen</strong> (maintenance &amp; repairs) the latest work on the boat and open damage. <strong>Logbuch öffnen</strong> shows all entries.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Booking',
         'html' => '<p><strong>Belegung ansehen &amp; buchen</strong> (view and book) shows this boat’s week. Tap a free slot there.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Reporting damage',
         'html' => '<p>With <strong>Schaden melden</strong> (report damage) you describe what is broken and attach photos, also without a booking. The boat keeper and the person looking after the boat get an email; the report then appears as <strong>offen</strong> (open) in the logbook.</p>'],
        ['fuer' => 'bootswartung', 'titel' => 'Maintaining the logbook',
         'html' => '<p>Each logbook entry has <strong>Bearbeiten · Löschen</strong> (edit · delete); open reports have the button <strong>Schaden behoben</strong> (damage fixed).</p>'],
        ['fuer' => 'admin', 'titel' => 'Editing the boat, reloading files',
         'html' => '<p><strong>Boot bearbeiten</strong> changes information text, boat data and links. New files from the Nextcloud folder appear within 15 minutes, immediately with <strong>Dateien neu laden</strong> (reload files) at the very bottom.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'logbuch' => [
      'titel' => 'Maintenance logbook',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'What is shown here?',
         'html' => '<p>All work and damage reports for this boat, newest first. <strong>offen</strong> (open) means the damage has not been fixed yet. The <strong>Bericht</strong> button shows the maintenance report for all boats.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Changing your report',
         'html' => '<p>While your report is open you can correct it or add photos with <strong>Meldung ändern</strong> (change report). The changed report is sent again to the boat keeper. When the damage is fixed, you get an email.</p>'],
        ['fuer' => 'bootswartung', 'titel' => 'Adding entries and closing damage',
         'html' => '<ul>'
                 . '<li><strong>Neuer Eintrag</strong> (new entry): date, status, what was done, by whom, material, hours and cost.</li>'
                 . '<li><strong>Schaden behoben</strong> (damage fixed) on an open report: enter what was done and when. Whoever reported it gets an email.</li>'
                 . '<li><strong>Bearbeiten · Löschen</strong> under each entry. Photos of a deleted report stay in the photo folder and must be deleted there by hand; the page lists the files.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'schaden' => [
      'titel' => 'Reporting damage',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'How it works',
         'html' => '<ol><li>Describe briefly under <strong>Was ist kaputt?</strong> (what is broken?) what happened.</li>'
                 . '<li>If useful, <strong>Fotos hinzufügen</strong> (add photos): your phone offers camera, video or files; several photos at once are fine.</li>'
                 . '<li>Tap <strong>Meldung abschicken</strong> (send report).</li></ol>'
                 . '<p>The boat keeper and the person looking after the boat get an email; the report appears as open in the logbook. In an emergency (boat not usable, accident) please also call the boat keeper directly.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'wartungsbericht' => [
      'titel' => 'Maintenance report',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Analysing',
         'html' => '<p>Filter by period (default: 1 January to today), boat, type (work or damage reports), status and search text. At the top you see totals per boat (entries, open reports, hours, cost), below them all entries. <strong>Als CSV (Excel)</strong> downloads the same selection as a file.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'bericht' => [
      'titel' => 'Report',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Analysing bookings',
         'html' => '<p>Choose a period and, if needed, a boat or search terms, then <strong>Run Report</strong>. Under <strong>Output</strong> choose a report (list of bookings), a summary (e.g. hours per boat) or both; under format also a file to download (CSV, iCalendar).</p>'
                 . '<p>For work and damage on the boats there is a link to the maintenance report at the top.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'suche' => [
      'titel' => 'Search',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Searching bookings',
         'html' => '<p>The search box in the header (on a phone under “Mehr”) finds upcoming bookings whose description contains the search term, e.g. a name. Tap a result to open the booking.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'benachrichtigungen' => [
      'titel' => 'Notifications',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Push on your phone',
         'html' => '<p><strong>Push auf diesem Gerät einschalten</strong> (turn on push on this device) sends everything the booking emails you as a message to your phone as well. This is per device: turn it on on each phone or computer. <strong>Test-Nachricht senden</strong> (send test message) shows whether it works.</p>'
                 . '<p>With <strong>“Keine Mails mehr, wenn die Push-Nachricht ankommt”</strong> (no more emails when push arrives) you avoid getting everything twice. If push does not arrive, the email is still sent.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Not working?',
         'html' => '<ul><li>On an iPhone push only works if the booking is installed via “Add to Home Screen” and opened from there.</li>'
                 . '<li>If you once blocked notifications for this site, allow them again in your browser or phone settings.</li></ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'benutzer' => [
      'titel' => 'User list and your profile',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Finding members',
         'html' => '<p>The list shows first name, last name and rights of all members. Use the search box to find someone quickly; tap a heading to sort.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Changing password and email',
         'html' => '<p>Tap <strong>your own name</strong>, change the password or email address and save. If your email address is wrong, you will not receive confirmations.</p>'],
        ['fuer' => 'admin', 'titel' => 'Managing members',
         'html' => '<ul>'
                 . '<li>Add a new user: user name, display name as “First Last”, email address, initial password, rights “user”.</li>'
                 . '<li>Only admins can tick <strong>Segellehrer</strong> (instructor, may offer courses) and <strong>Bootswartung</strong> (boat maintenance, may write in the logbook).</li>'
                 . '<li>Forgotten password: open the user and enter a new one. Member left: delete the user; old bookings remain.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'verwaltung' => [
      'titel' => 'Administration',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Season (booking period)',
         'html' => '<p>Under <strong>Buchungszeitraum (Saison)</strong> enter the start and end of the season and save. Takes effect immediately; an empty field means “no limit”. Bookings on the first and last day are allowed. <strong>Update it every year before the season starts!</strong></p>'],
        ['fuer' => 'admin', 'titel' => 'Club and boats',
         'html' => '<ul>'
                 . '<li><strong>Edit Area</strong> (the club): among other things the boat keeper’s email address for damage reports.</li>'
                 . '<li><strong>Add Room</strong> (a boat) or <strong>Edit</strong> next to a boat: name, capacity, boat data, information, links to documents and photo folder, email of the person looking after the boat.</li>'
                 . '<li><strong>Disable</strong> a boat instead of deleting it: it disappears from the plan, old bookings remain.</li>'
                 . '</ul>'],
        ['fuer' => 'admin', 'titel' => 'Contact person (help page)',
         'html' => '<p>At the very bottom, under <strong>Ansprechpartner (Hilfeseite)</strong>, set who the help page names for questions: <strong>Name</strong> (inserted into the sentence “Please contact … for any questions”) and <strong>E-Mail</strong> (the name becomes a mail link). An empty field uses the value from config.inc.php.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boot_bearbeiten' => [
      'titel' => 'Editing a boat',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'The most important fields',
         'html' => '<ul>'
                 . '<li><strong>Name</strong> is the column in the plan, <strong>Sort key</strong> sets the order.</li>'
                 . '<li><strong>Capacity</strong>: people on board; limits the crew places (0 = no limit).</li>'
                 . '<li><strong>Notification emails</strong>: the person looking after this boat; gets damage reports (several addresses separated by commas).</li>'
                 . '<li>Boat data: length, beam, draught, mast height, weight, each with unit, e.g. “6.50 m”.</li>'
                 . '<li>Information for members: plain text; an empty line starts a new paragraph, links become clickable.</li>'
                 . '<li><strong>Unterlagen (Link)</strong> (optional): link to manual and checklists. If it is a read-only Nextcloud share without password, the boat page also shows its pictures and documents.</li>'
                 . '<li><strong>Fotoablage (Link)</strong> (optional): drop folder for damage photos, e.g. a Nextcloud “file drop” share, not inside the documents folder.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'verein_bearbeiten' => [
      'titel' => 'Editing the club',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Club settings',
         'html' => '<p>Most important is the <strong>Notification emails</strong> field: the boat keeper, who receives every damage report. There are also basic settings such as booking times and how far ahead bookings are allowed. The season itself is set under <strong>Verwaltung → Buchungszeitraum</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'import' => [
      'titel' => 'Import',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Bookings from a calendar file',
         'html' => '<p>Upload an iCalendar file (.ics, e.g. from another calendar), choose club and boat and import. With the option to skip conflicts, existing bookings stay untouched. Best try it with a few dates first.</p>'],
      ],
    ],
  ],
];
