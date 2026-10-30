/* =============================================================================
   Theme "verein" – Umschalter Automatisch / Hell / Dunkel

   Eingebunden über $custom_js_url in der config.inc.php. MRBS lädt die Datei
   im <head>, also bevor die Seite gezeichnet wird – die gespeicherte Wahl
   greift deshalb sofort, ohne kurzes Aufblitzen im falschen Modus.

   Die Wahl gilt pro Browser (localStorage). "Automatisch" folgt der
   Einstellung des Geräts.
   ============================================================================= */
(function () {
  'use strict';

  var KEY = 'bb-theme';
  var ORDER = ['auto', 'light', 'dark'];
  var LABEL = { auto: 'Automatisch', light: 'Hell', dark: 'Dunkel' };

  // Symbole: Halbkreis (automatisch), Sonne (hell), Mond (dunkel)
  var ICON = {
    auto:  '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 3a9 9 0 0 0 0 18z" fill="currentColor"></path></svg>',
    light: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"></path></svg>',
    dark:  '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path></svg>'
  };

  function load() {
    try {
      var m = window.localStorage.getItem(KEY);
      return ORDER.indexOf(m) >= 0 ? m : 'auto';
    } catch (e) {
      return 'auto';   // z. B. privates Fenster ohne Speicher
    }
  }

  function save(m) {
    try { window.localStorage.setItem(KEY, m); } catch (e) { /* egal */ }
  }

  function apply(m) {
    var root = document.documentElement;
    if (m === 'auto') {
      root.removeAttribute('data-theme');
    } else {
      root.setAttribute('data-theme', m);
    }
  }

  // Sofort anwenden (läuft im <head>)
  apply(load());

  function addButton() {
    var menu = document.querySelector('header.banner:not(.simple) nav.menu');
    if (!menu || menu.querySelector('.bb-theme-toggle')) {
      return;
    }

    var button = document.createElement('button');
    button.type = 'button';
    button.className = 'bb-theme-toggle';

    function render() {
      var m = load();
      var next = ORDER[(ORDER.indexOf(m) + 1) % ORDER.length];
      button.innerHTML = ICON[m] + '<span class="bb-theme-label">' + LABEL[m] + '</span>';
      button.setAttribute('aria-label', 'Darstellung: ' + LABEL[m] + '. Wechseln zu: ' + LABEL[next]);
      button.title = 'Darstellung: ' + LABEL[m] + ' (klicken für ' + LABEL[next] + ')';
    }

    button.addEventListener('click', function () {
      var m = ORDER[(ORDER.indexOf(load()) + 1) % ORDER.length];
      save(m);
      apply(m);
      render();
    });

    render();
    menu.appendChild(button);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', addButton);
  } else {
    addButton();
  }

  // MRBS wechselt Tag/Woche/Monat ohne Neuladen und ersetzt dabei den ganzen
  // Seiteninhalt (index.js: body.html(...)). Danach den Knopf neu einsetzen.
  function watch(fn) {
    var start = function () {
      new MutationObserver(fn).observe(document.body, { childList: true });
    };
    if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
  }
  watch(addButton);
})();


/* =============================================================================
   Handy: Menü-Knopf in der Kopfzeile

   Fügt einen ☰-Knopf ein, der Menü, Datum und Suche auf- und zuklappt.
   Ob er sichtbar ist und was eingeklappt wird, regelt theme.css (nur bei
   schmalen Bildschirmen) – am Desktop bleibt alles wie gehabt.
   ============================================================================= */
(function () {
  'use strict';

  var ICON_OPEN  = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>';
  var ICON_CLOSE = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg>';

  function init() {
    var banner = document.querySelector('header.banner:not(.simple)');
    var nav = banner && banner.querySelector('nav.container');
    if (!nav || banner.querySelector('.bb-menu-toggle')) {
      return;
    }
    if (!nav.id) {
      nav.id = 'bb-banner-nav';
    }

    var button = document.createElement('button');
    button.type = 'button';
    button.className = 'bb-menu-toggle';
    button.setAttribute('aria-controls', nav.id);

    function render(open) {
      button.innerHTML = open ? ICON_CLOSE : ICON_OPEN;
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      button.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
    }

    button.addEventListener('click', function () {
      var open = !banner.classList.contains('bb-menu-open');
      banner.classList.toggle('bb-menu-open', open);
      render(open);
    });

    render(false);
    banner.insertBefore(button, nav);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // MRBS wechselt Tag/Woche/Monat ohne Neuladen und ersetzt dabei den ganzen
  // Seiteninhalt (index.js: body.html(...)). Danach den Knopf neu einsetzen.
  function watch(fn) {
    var start = function () {
      new MutationObserver(fn).observe(document.body, { childList: true });
    };
    if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
  }
  watch(init);
})();


/* =============================================================================
   Buchungsformular: Teilnehmerzahl an die Bootskapazität binden

   Holt Kapazitäten und Abzug (Schiffsführer) aus Themes/verein/kapazitaet.php –
   das sind dieselben Werte, die kapazitaet.patch beim Speichern prüft.
   - Obergrenze am Zahlenfeld: der Pfeil nach oben stoppt dort
   - zu große Eingaben werden auf die Obergrenze gesetzt
   - Hinweis "max. 5 (Kapazität 6, 1 Platz für den Schiffsführer)"
   - bei Anmeldung + Boot mit Kapazität wird "Plätze begrenzen"
     automatisch angehakt (der Patch verlangt das ohnehin)
   Mehrere Boote ausgewählt: es gilt das kleinste.
   Klappt das Laden nicht, bleibt das Formular wie es ist – der Patch
   prüft beim Speichern trotzdem.
   ============================================================================= */
(function () {
  'use strict';

  function init() {
    var limit = document.getElementById('registrant_limit');
    var rooms = document.getElementById('rooms');
    if (!limit || !rooms) {
      return;   // nicht das Buchungsformular
    }
    var allow = document.getElementById('allow_registration');
    var limitEnabled = document.getElementById('registrant_limit_enabled');
    var kursBox = document.getElementById('f_kurs');

    // Bei Kursen: kein Platz für den Schiffsführer abziehen (der Segellehrer
    // sitzt nicht unbedingt im Boot) – der Server rechnet genauso.
    function istKurs() {
      return !!(kursBox && kursBox.checked);
    }
    function abzug() {
      return istKurs() ? 0 : data.offset;
    }

    var hint = document.createElement('span');
    hint.className = 'bb-capacity-hint';
    hint.setAttribute('aria-live', 'polite');
    limit.insertAdjacentElement('afterend', hint);
    limit.setAttribute('aria-describedby', 'bb-capacity-hint');
    hint.id = 'bb-capacity-hint';

    var data = null;

    function currentMax() {
      if (!data) {
        return null;
      }
      var max = null, cap = null;
      Array.prototype.forEach.call(rooms.selectedOptions || [], function (opt) {
        var c = data.rooms[opt.value];
        if (c && c > 0) {
          var m = Math.max(0, c - abzug());
          if (max === null || m < max) {
            max = m;
            cap = c;
          }
        }
      });
      return max === null ? null : { max: max, cap: cap };
    }

    function clamp() {
      var info = currentMax();
      if (!info) {
        return;
      }
      var v = parseInt(limit.value, 10);
      if (isNaN(v) || v < 1) {
        limit.value = Math.min(1, info.max);
      } else if (v > info.max) {
        limit.value = info.max;
      }
    }

    // Formular aufräumen: "Plätze für Mitsegler" erst zeigen, wenn
    // "Mitsegler willkommen" angehakt ist. "Plätze begrenzen" ausblenden,
    // wenn das Boot eine Kapazität hat – dann wird es ohnehin automatisch
    // gesetzt. (Ohne Kapazität bleibt es sichtbar und frei wählbar.)
    function zeile(el) {
      return el ? el.closest('div') : null;
    }
    function felder(info) {
      var an = !allow || allow.checked;
      var r1 = zeile(limitEnabled), r2 = zeile(limit);
      if (r1) { r1.classList.toggle('bb-zeile-aus', !an || !!info); }
      if (r2) { r2.classList.toggle('bb-zeile-aus', !an); }
    }

    function update() {
      var info = currentMax();
      felder(info);
      if (!info) {
        limit.removeAttribute('max');
        hint.textContent = '';
        return;
      }
      limit.max = info.max;
      limit.min = Math.min(1, info.max);
      var seats = data.offset === 1 ? '1 Platz' : data.offset + ' Plätze';
      if (istKurs()) {
        hint.textContent = 'max. ' + info.max + ' (Kapazität ' + info.cap + ')' +
          (data.offset > 0 ? ' – sitzt der Segellehrer mit im Boot, bitte höchstens ' +
            Math.max(0, info.cap - data.offset) + ' eintragen' : '');
      } else {
        hint.textContent = 'max. ' + info.max + ' (Kapazität ' + info.cap +
          (data.offset > 0 ? ', ' + seats + ' für den Schiffsführer' : '') + ')';
      }

      // Der Patch verlangt bei Booten mit Kapazität eine begrenzte Teilnehmerzahl
      if (allow && allow.checked && limitEnabled && !limitEnabled.checked) {
        limitEnabled.checked = true;
        limitEnabled.dispatchEvent(new Event('change', { bubbles: true }));
      }
      clamp();
    }

    felder(null);
    rooms.addEventListener('change', update);
    if (allow) {
      // nach MRBS' eigenem Handler, der die Felder erst freischaltet
      allow.addEventListener('change', function () { window.setTimeout(update, 0); });
    }
    limit.addEventListener('change', clamp);
    limit.addEventListener('blur', clamp);

    fetch('Themes/verein/kapazitaet.php', { credentials: 'same-origin' })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (json) {
        if (json && json.rooms) {
          data = json;
          update();
        }
        kurs(!!(json && json.segellehrer));
      })
      .catch(function () { kurs(false); /* ohne Daten: Patch prüft beim Speichern */ });

    // "Als Kurs anbieten" (Zusatzfeld kurs) – nur für Segellehrer sichtbar.
    // Das Feld wandert in den Abschnitt "Mitsegler willkommen"; ein Kurs ist
    // immer zur Anmeldung offen. Der Server prüft die Rolle beim Speichern.
    function kurs(segellehrer) {
      var box = document.getElementById('f_kurs');
      var row = box && box.closest('div');
      var fieldset = document.getElementById('registration');
      if (!row) {
        return;
      }
      if (fieldset) {
        var legend = fieldset.querySelector('legend');
        fieldset.insertBefore(row, legend ? legend.nextSibling : fieldset.firstChild);
      }
      row.classList.add('bb-kurs-field');
      // Nicht-Segellehrer sehen das Feld nur, wenn die Buchung schon ein Kurs ist
      // (dann können sie den Haken entfernen)
      row.hidden = !segellehrer && !box.checked;

      // Bei Kursen heißt das erste Feld "Segellehrer" statt "Schiffsführer"
      var nameLabel = document.querySelector('label[for="name"]');
      var nameText = nameLabel ? nameLabel.textContent : '';
      function beschriften() {
        if (nameLabel) {
          nameLabel.textContent = box.checked ? nameText.replace(/^[^:]*/, 'Segellehrer') : nameText;
        }
      }
      beschriften();

      box.addEventListener('change', function () {
        if (box.checked && allow && !allow.checked) {
          allow.checked = true;
          allow.dispatchEvent(new Event('change', { bubbles: true }));
        }
        beschriften();
        update();
      });
      if (allow) {
        allow.addEventListener('change', function () {
          if (!allow.checked && box.checked) {
            box.checked = false;
            beschriften();
          }
        });
      }
    }

    // Mitsegler/Kurs sollen alle sehen können: bei privaten Buchungen zeigt
    // MRBS anderen weder Namen noch Belegung. Deshalb beim Einschalten auf
    // "Öffentlich" umstellen (bleibt danach von Hand änderbar).
    if (allow) {
      allow.addEventListener('change', function () {
        var pub = document.querySelector('input[name="private"][value="0"]');
        if (allow.checked && pub && !pub.checked && !pub.disabled) {
          pub.checked = true;
          pub.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    }

    // Kurzer Hinweis unter "Mitsegler willkommen"
    if (allow && !document.querySelector('.bb-mitsegler-hint')) {
      var help = document.createElement('span');
      help.className = 'bb-mitsegler-hint';
      help.textContent = 'Andere Mitglieder können sich eintragen und mitsegeln.';
      allow.insertAdjacentElement('afterend', help);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();


/* =============================================================================
   Buchung ansehen: Teilnehmerliste drucken

   Bei Buchungen mit Anmeldung ("Mitsegler willkommen" oder Kurs) gibt es
   unter der Liste einen Knopf, der Themes/verein/teilnehmer.php öffnet.
   Er erscheint nur, wo MRBS die Teilnehmerliste zeigt (Ersteller, Admins).
   Die Seite prüft selbst noch einmal, wer die Liste sehen darf.
   ============================================================================= */
(function () {
  'use strict';

  function init() {
    var reg = document.getElementById('registration');
    if (!reg || !reg.querySelector('#registrants') || reg.querySelector('.bb-print-list')) {
      return;
    }
    var id = new URLSearchParams(location.search).get('id');
    if (!id || !/^\d+$/.test(id)) {
      return;
    }
    var a = document.createElement('a');
    a.className = 'bb-print-list';
    a.href = 'Themes/verein/teilnehmer.php?id=' + id;
    a.target = '_blank';
    a.rel = 'noopener';
    a.textContent = 'Teilnehmerliste drucken';
    reg.appendChild(a);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();


/* =============================================================================
   Menüpunkte "Mitsegeln & Kurse" (Seite bb_mitsegeln.php aus kurs.patch)
   und "Boote" (bb_boote.php aus bootsuebersicht.patch)
   – nur für angemeldete Benutzer
   ============================================================================= */
(function () {
  'use strict';

  function add() {
    var menu = document.querySelector('header.banner:not(.simple) nav.menu');
    if (!menu || menu.querySelector('.bb-menu-mitsegeln')) {
      return;
    }
    // Angemeldet = in der Kopfzeile steht der Knopf mit dem eigenen Namen
    if (!document.getElementById('show_my_entries')) {
      return;
    }
    var a = document.createElement('a');
    a.className = 'bb-menu-mitsegeln';
    a.href = 'bb_mitsegeln.php';
    a.textContent = 'Mitsegeln & Kurse';
    if (/bb_mitsegeln\.php/.test(location.pathname)) {
      a.setAttribute('aria-current', 'page');
    }
    menu.insertBefore(a, menu.firstChild);

    // "Boote": Bootsübersicht (bb_boote.php aus bootsuebersicht.patch)
    var b = document.createElement('a');
    b.className = 'bb-menu-boote';
    b.href = 'bb_boote.php';
    b.textContent = 'Boote';
    if (/bb_boote\.php/.test(location.pathname)) {
      b.setAttribute('aria-current', 'page');
    }
    menu.insertBefore(b, a.nextSibling);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', add);
  } else {
    add();
  }
  var start = function () {
    new MutationObserver(add).observe(document.body, { childList: true });
  };
  if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
})();


/* =============================================================================
   Benachrichtigungen (Push)

   Menüpunkt "Benachrichtigungen" für angemeldete Benutzer und die Knöpfe auf
   der Seite bb_benachrichtigungen.php (aus push.patch). Der Service Worker
   (app/sw.php) zeigt die Nachrichten an.
   ============================================================================= */
(function () {
  'use strict';

  function addMenu() {
    var menu = document.querySelector('header.banner:not(.simple) nav.menu');
    if (!menu || menu.querySelector('.bb-menu-push') || !document.getElementById('show_my_entries')) {
      return;
    }
    var a = document.createElement('a');
    a.className = 'bb-menu-push';
    a.href = 'bb_benachrichtigungen.php';
    a.textContent = 'Benachrichtigungen';
    if (/bb_benachrichtigungen\.php/.test(location.pathname)) {
      a.setAttribute('aria-current', 'page');
    }
    var mitsegeln = menu.querySelector('.bb-menu-mitsegeln');
    menu.insertBefore(a, mitsegeln ? mitsegeln.nextSibling : menu.firstChild);
  }

  function b64ToBytes(s) {
    s = s.replace(/-/g, '+').replace(/_/g, '/');
    var raw = atob(s + '==='.slice((s.length + 3) % 4));
    var out = new Uint8Array(raw.length);
    for (var i = 0; i < raw.length; i++) { out[i] = raw.charCodeAt(i); }
    return out;
  }

  function post(data) {
    var token = document.querySelector('meta[name="csrf_token"]');
    var body = new URLSearchParams(data);
    body.append('csrf_token', token ? token.content : '');
    return fetch('bb_benachrichtigungen.php', { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) {
        return r.json().then(function (j) {
          if (!r.ok) { throw new Error(j.fehler || ('Fehler ' + r.status)); }
          return j;
        });
      });
  }

  function initPage() {
    var root = document.getElementById('bb-push');
    if (!root || root.dataset.init || !root.dataset.key) {
      return;
    }
    root.dataset.init = '1';

    var status = document.getElementById('bb-push-status');
    var btnAn = document.getElementById('bb-push-an');
    var btnAus = document.getElementById('bb-push-aus');
    var btnTest = document.getElementById('bb-push-test');
    var nur = document.getElementById('bb-push-nur');
    var geraete = document.getElementById('bb-push-geraete');

    function zeigeGeraete(n) {
      geraete.textContent = (n === 0)
        ? 'Push ist noch auf keinem deiner Geräte eingeschaltet. Bis dahin bekommst du auf jeden Fall Mails.'
        : 'Push ist auf ' + (n === 1 ? 'einem Gerät' : n + ' Geräten') + ' eingeschaltet. ' +
          'Kommt eine Push-Nachricht nicht an, schicken wir trotzdem eine Mail.';
    }
    function update(j) {
      if (typeof j.geraete === 'number') { zeigeGeraete(j.geraete); }
      if (typeof j.nur_push === 'boolean') { nur.checked = j.nur_push; }
    }
    zeigeGeraete(parseInt(root.dataset.geraete, 10) || 0);
    nur.checked = root.dataset.nurPush === '1';

    function zustand(text, an) {
      status.textContent = text;
      btnAn.hidden = (an !== false);
      btnAus.hidden = btnTest.hidden = (an !== true);
    }

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
      var ios = /iPhone|iPad/.test(navigator.userAgent);
      zustand(ios
        ? 'Auf dem iPhone geht Push nur, wenn die Bootsbuchung auf dem Home-Bildschirm liegt: in Safari „Teilen“ → „Zum Home-Bildschirm“, dann von dort öffnen.'
        : 'Dieser Browser kann leider keine Push-Nachrichten empfangen.', null);
      return;
    }

    var reg;
    function pruefen() {
      return navigator.serviceWorker.ready.then(function (r) {
        reg = r;
        return r.pushManager.getSubscription();
      }).then(function (sub) {
        if (Notification.permission === 'denied') {
          zustand('Benachrichtigungen sind für diese Seite blockiert. Erlaube sie in den Einstellungen des Browsers bzw. der App und lade die Seite neu.', null);
        } else if (sub) {
          zustand('Push ist auf diesem Gerät eingeschaltet.', true);
          // Anmeldung auffrischen, falls der Server sie gelöscht hat
          post({ action: 'anmelden', abo: JSON.stringify(sub) }).then(update).catch(function () {});
        } else {
          zustand('Push ist auf diesem Gerät ausgeschaltet.', false);
        }
      });
    }

    btnAn.addEventListener('click', function () {
      btnAn.disabled = true;
      Notification.requestPermission().then(function (p) {
        if (p !== 'granted') { throw new Error('Benachrichtigungen wurden nicht erlaubt.'); }
        return reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: b64ToBytes(root.dataset.key) });
      }).then(function (sub) {
        return post({ action: 'anmelden', abo: JSON.stringify(sub) });
      }).then(function (j) {
        update(j);
        return pruefen();
      }).catch(function (e) {
        status.textContent = 'Das hat nicht geklappt: ' + e.message;
      }).then(function () { btnAn.disabled = false; });
    });

    btnAus.addEventListener('click', function () {
      btnAus.disabled = true;
      reg.pushManager.getSubscription().then(function (sub) {
        if (!sub) { return null; }
        var endpoint = sub.endpoint;
        return sub.unsubscribe().then(function () { return post({ action: 'abmelden', endpoint: endpoint }); });
      }).then(function (j) {
        if (j) { update(j); }
        return pruefen();
      }).catch(function (e) {
        status.textContent = 'Das hat nicht geklappt: ' + e.message;
      }).then(function () { btnAus.disabled = false; });
    });

    btnTest.addEventListener('click', function () {
      btnTest.disabled = true;
      post({ action: 'test' }).then(function (j) {
        update(j);
        status.textContent = j.zugestellt > 0
          ? 'Test-Nachricht verschickt. Sie sollte gleich erscheinen.'
          : 'Die Test-Nachricht konnte nicht zugestellt werden. Bitte Push aus- und wieder einschalten.';
      }).catch(function (e) {
        status.textContent = 'Das hat nicht geklappt: ' + e.message;
      }).then(function () { btnTest.disabled = false; });
    });

    nur.addEventListener('change', function () {
      post({ action: 'nur_push', wert: nur.checked ? '1' : '0' }).then(update).catch(function (e) {
        nur.checked = !nur.checked;
        status.textContent = 'Das hat nicht geklappt: ' + e.message;
      });
    });

    pruefen();
  }

  function init() {
    addMenu();
    initPage();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  var start = function () {
    new MutationObserver(addMenu).observe(document.body, { childList: true });
  };
  if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
})();


/* =============================================================================
   Handy: App-Navigation unten

   Wie in nativen Apps: eine Leiste unten mit Plan, Mitsegeln, Meine,
   Benachrichtigungen und Mehr (klappt das Menü der Kopfzeile auf). Auf dem
   Plan zusätzlich ein runder „+“-Knopf für eine neue Buchung. Wer nicht
   angemeldet ist, sieht dort stattdessen einen großen Knopf „Anmelden“.
   Nur auf schmalen Bildschirmen (siehe theme.css, 14.).
   ============================================================================= */
(function () {
  'use strict';

  var svg = function (pfad) {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
           'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + pfad + '</svg>';
  };
  var ICON = {
    plan:   svg('<rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path>'),
    segeln: svg('<path d="M12 3v14M11 4 4 15h7zM13 6l6 9h-6zM3 18h18l-2 3H5z"></path>'),
    boote:  svg('<circle cx="12" cy="5" r="2"></circle><path d="M12 7v14M8 10h8M4 13a8 8 0 0 0 16 0"></path>'),
    meine:  svg('<circle cx="12" cy="8" r="4"></circle><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"></path>'),
    push:   svg('<path d="M18 8a6 6 0 0 0-12 0c0 7-3 8-3 8h18s-3-1-3-8M13.7 21a2 2 0 0 1-3.4 0"></path>'),
    mehr:   svg('<circle cx="5" cy="12" r="1.2"></circle><circle cx="12" cy="12" r="1.2"></circle><circle cx="19" cy="12" r="1.2"></circle>'),
    plus:   svg('<path d="M12 5v14M5 12h14"></path>')
  };

  function seite() {
    var p = location.pathname;
    var q = location.search;
    if (/bb_mitsegeln\.php/.test(p)) { return /filter=meine/.test(q) ? 'meine' : 'segeln'; }
    if (/bb_benachrichtigungen\.php/.test(p)) { return 'push'; }
    if (/bb_boote\.php/.test(p)) { return 'boote'; }
    if (/(index\.php|\/)$/.test(p) || /index\.php/.test(p)) { return 'plan'; }
    return '';
  }

  // Echte Höhe der Leiste als CSS-Variable: Speichern-Leiste und Abstand
  // unten richten sich danach (Schriftgröße und Gesten-Leiste sind je
  // Handy verschieden)
  function merkeHoehe(nav) {
    function setzen() {
      document.documentElement.style.setProperty('--bb-appnav-h', nav.offsetHeight + 'px');
    }
    setzen();
    if (window.ResizeObserver) {
      new ResizeObserver(setzen).observe(nav);
    } else {
      window.addEventListener('resize', setzen);
    }
  }

  // Nicht angemeldet: statt der Leiste ein großer Knopf „Anmelden“ – sonst
  // steckt die Anmeldung im Menü ☰ und ist schwer zu finden
  function addGast() {
    var knopf = document.querySelector('nav.logon input[type="submit"]');
    if (!knopf || document.getElementById('username')) {
      return;
    }
    var nav = document.createElement('nav');
    nav.className = 'bb-appnav bb-appnav-gast';
    nav.setAttribute('aria-label', 'Anmelden');
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'bb-anmelden';
    b.innerHTML = ICON.meine + '<span>Anmelden</span>';
    b.addEventListener('click', function () { knopf.click(); });
    nav.appendChild(b);
    document.body.appendChild(nav);
    document.documentElement.classList.add('bb-hat-appnav');
    merkeHoehe(nav);
  }

  function menuZu() {
    var banner = document.querySelector('header.banner.bb-menu-open');
    var t = banner && banner.querySelector('.bb-menu-toggle');
    if (t) { t.click(); }
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { menuZu(); }
  });

  function add() {
    if (document.querySelector('.bb-appnav')) {
      return;
    }
    // Angemeldet erkennt man am Link „Meine Einträge“ in der Kopfzeile
    if (!document.getElementById('show_my_entries')) {
      addGast();
      return;
    }
    var aktiv = seite();
    var nav = document.createElement('nav');
    nav.className = 'bb-appnav';
    nav.setAttribute('aria-label', 'App-Navigation');
    var eintraege = [
      ['plan', 'Plan', 'index.php'],
      ['segeln', 'Mitsegeln', 'bb_mitsegeln.php'],
      ['boote', 'Boote', 'bb_boote.php'],
      ['meine', 'Meine', 'bb_mitsegeln.php?filter=meine'],
      ['mehr', 'Mehr', null]
    ];
    eintraege.forEach(function (e) {
      var el = document.createElement(e[2] ? 'a' : 'button');
      if (e[2]) {
        el.href = e[2];
      } else {
        el.type = 'button';
      }
      el.innerHTML = ICON[e[0]] + '<span>' + e[1] + '</span>';
      if (e[0] === aktiv) {
        el.setAttribute('aria-current', 'page');
      }
      if (e[0] === 'mehr') {
        el.className = 'bb-appnav-mehr';
        el.setAttribute('aria-haspopup', 'true');
        el.addEventListener('click', function () {
          var t = document.querySelector('.bb-menu-toggle');
          if (t) {
            t.click();
            // Ohne obere Leiste klappt das Menü unten auf (theme.css),
            // sonst oben in der Kopfzeile
            if (!window.matchMedia('(max-width: 700px)').matches) {
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }
          }
        });
      }
      nav.appendChild(el);
    });
    document.body.appendChild(nav);
    document.documentElement.classList.add('bb-hat-appnav');
    merkeHoehe(nav);

    // Angemeldet am Handy bleibt oben nur Wimpel und Name: das Menü
    // (Links, Datum, Suche) öffnet sich über „Mehr“ als Blatt von unten.
    // Ein Tipp daneben oder Esc schließt es.
    document.documentElement.classList.add('bb-ohne-kopf');
    if (!document.querySelector('.bb-menu-hintergrund')) {
      var hg = document.createElement('div');
      hg.className = 'bb-menu-hintergrund';
      hg.addEventListener('click', menuZu);
      document.body.appendChild(hg);
    }

    // Plan: "+" für eine neue Buchung am angezeigten Tag
    if (aktiv === 'plan' && !document.querySelector('.bb-fab')) {
      var fab = document.createElement('a');
      fab.className = 'bb-fab';
      fab.href = 'edit_entry.php' + location.search;
      fab.setAttribute('aria-label', 'Neue Buchung');
      fab.innerHTML = ICON.plus;
      document.body.appendChild(fab);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', add);
  } else {
    add();
  }
  // MRBS ersetzt beim Blättern den Seiteninhalt (siehe oben)
  var start = function () {
    new MutationObserver(add).observe(document.body, { childList: true });
  };
  if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
})();


/* =============================================================================
   Handy: Tagesplan als Liste

   Die Tabelle der Tagesansicht (ein Boot je Spalte) ist am Handy eng. Hier
   wird daraus eine Karte je Boot mit seinen Buchungen und einem Knopf
   „Buchen“. Ein Umschalter „Liste | Raster“ holt die Tabelle zurück, die
   Wahl merkt sich der Browser. Gebaut wird aus der Tabelle selbst, deshalb
   gilt alles, was MRBS dort berechnet (Rechte, private Buchungen …).
   Sichtbar nur auf schmalen Bildschirmen (theme.css, 15.).
   ============================================================================= */
(function () {
  'use strict';

  var KEY = 'bb-tagesansicht';

  function modus() {
    try { return window.localStorage.getItem(KEY) === 'raster' ? 'raster' : 'liste'; } catch (e) { return 'liste'; }
  }

  function setzeModus(m) {
    try { window.localStorage.setItem(KEY, m); } catch (e) { /* egal */ }
    document.documentElement.classList.toggle('bb-tagesraster', m === 'raster');
    document.querySelectorAll('.bb-ansicht-umschalter button').forEach(function (b) {
      b.setAttribute('aria-pressed', b.dataset.modus === m ? 'true' : 'false');
    });
  }

  function uhrzeit(sek) {
    var h = Math.floor(sek / 3600) % 24;
    var m = Math.floor((sek % 3600) / 60);
    return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
  }

  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) { e.className = cls; }
    if (text != null) { e.textContent = text; }
    return e;
  }

  // Tabelle auslesen: je Boot Name, Kapazität, Buchungen und erster freier Platz
  function auslesen(table) {
    var aufloesung = parseInt(table.dataset.resolution, 10) || 1800;
    var boote = [];
    table.querySelectorAll('thead th[data-room]').forEach(function (th) {
      var a = th.querySelector('a');
      var kap = th.querySelector('.capacity');
      boote.push({
        name: (a ? a.childNodes[0].textContent : th.textContent).trim(),
        kapazitaet: kap ? kap.textContent.trim() : '',
        woche: a ? a.getAttribute('href') : null,
        buchungen: [],
        frei: null
      });
    });
    var belegt = [];   // je Spalte: bis zu welcher Zeile durch rowspan belegt
    var zeilen = table.querySelectorAll('tbody > tr');
    zeilen.forEach(function (tr, r) {
      var th = tr.querySelector('th[data-seconds]');
      if (!th) { return; }
      var start = parseInt(th.dataset.seconds, 10);
      var spalte = 0;
      Array.prototype.forEach.call(tr.children, function (td) {
        if (td.tagName !== 'TD') { return; }
        while (belegt[spalte] > r) { spalte++; }
        var span = parseInt(td.getAttribute('rowspan'), 10) || 1;
        belegt[spalte] = r + span;
        var boot = boote[spalte];
        if (boot) {
          var link = td.querySelector('.booking a, a[href*="view_entry"]');
          var box = td.querySelector('.booking');
          if (link) {
            var text = link.textContent.trim();
            var platz = text.match(/\s*\[(\d+)(?:\/(\d+))?\]\s*$/);
            boot.buchungen.push({
              von: start,
              bis: start + span * aufloesung,
              name: platz ? text.slice(0, platz.index) : text,
              plaetze: platz ? (platz[1] + (platz[2] ? ' / ' + platz[2] : '')) : '',
              href: link.getAttribute('href'),
              kurs: !!(box && box.classList.contains('bb-kurs')),
              mitsegeln: !!(box && (box.classList.contains('spaces') || box.classList.contains('full'))),
              voll: !!(box && box.classList.contains('full'))
            });
          } else if (!boot.frei && td.classList.contains('new')) {
            var neu = td.querySelector('a');
            if (neu) { boot.frei = neu.getAttribute('href'); }
          }
        }
        spalte++;
      });
    });
    return boote;
  }

  function bauen(table) {
    if (table.dataset.bbListe === '1') { return; }
    table.dataset.bbListe = '1';
    var container = table.closest('.table_container') || table;
    var alt = container.parentNode.querySelector('.bb-tagesliste');
    if (alt) { alt.remove(); }

    var liste = el('div', 'bb-tagesliste');
    auslesen(table).forEach(function (boot) {
      var karte = el('section', 'bb-bootkarte');
      var kopf = el('header');
      var titel = el(boot.woche ? 'a' : 'span', 'bb-bootname', boot.name);
      if (boot.woche) { titel.href = boot.woche; }
      kopf.appendChild(titel);
      if (boot.kapazitaet && boot.kapazitaet !== '0') {
        kopf.appendChild(el('span', 'bb-kapazitaet', boot.kapazitaet + (boot.kapazitaet === '1' ? ' Platz' : ' Plätze')));
      }
      karte.appendChild(kopf);

      if (!boot.buchungen.length) {
        karte.appendChild(el('p', 'bb-frei', 'Den ganzen Tag frei'));
      } else {
        var ul = el('ul');
        boot.buchungen.forEach(function (b) {
          var li = el('li');
          var a = el('a');
          a.href = b.href;
          a.className = b.kurs ? 'kurs' : (b.mitsegeln ? 'mitsegeln' : '');
          a.appendChild(el('span', 'zeit', uhrzeit(b.von) + ' – ' + uhrzeit(b.bis)));
          var mitte = el('span', 'wer');
          if (b.kurs || b.mitsegeln) {
            mitte.appendChild(el('span', 'marke', b.kurs ? 'Kurs' : (b.voll ? 'voll' : 'Mitsegeln')));
          }
          mitte.appendChild(document.createTextNode(b.name));
          a.appendChild(mitte);
          if (b.plaetze) { a.appendChild(el('span', 'plaetze', b.plaetze)); }
          li.appendChild(a);
          ul.appendChild(li);
        });
        karte.appendChild(ul);
      }
      if (boot.frei) {
        var knopf = el('a', 'bb-buchen', '+ Buchen');
        knopf.href = boot.frei;
        karte.appendChild(knopf);
      }
      liste.appendChild(karte);
    });
    container.parentNode.insertBefore(liste, container.nextSibling);

    // Umschalter Liste | Raster neben Tag/Woche/Monat
    if (!document.querySelector('.bb-ansicht-umschalter')) {
      var nav = document.querySelector('nav.main_calendar') || container.parentNode;
      var u = el('div', 'bb-ansicht-umschalter');
      u.setAttribute('role', 'group');
      u.setAttribute('aria-label', 'Darstellung');
      [['liste', 'Liste'], ['raster', 'Raster']].forEach(function (m) {
        var b = el('button', null, m[1]);
        b.type = 'button';
        b.dataset.modus = m[0];
        b.addEventListener('click', function () { setzeModus(m[0]); });
        u.appendChild(b);
      });
      nav.parentNode.insertBefore(u, nav.nextSibling);
    }
    setzeModus(modus());
  }

  var geplant = false;
  function pruefen() {
    if (geplant) { return; }
    geplant = true;
    window.requestAnimationFrame(function () {
      geplant = false;
      var t = document.getElementById('day_main');
      if (t) { bauen(t); }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', pruefen);
  } else {
    pruefen();
  }
  // MRBS lädt die Tabelle beim Blättern und regelmäßig neu
  var start = function () {
    new MutationObserver(pruefen).observe(document.body, { childList: true, subtree: true });
  };
  if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
})();

/* =============================================================================
   Handy: Buchungsformular – Prüfung als Satz

   MRBS prüft beim Ausfüllen, ob das Boot frei ist und die Regeln passen, und
   zeigt das nur als ✗/✓ mit Text beim Überfahren mit der Maus. Am Handy gibt
   es kein Überfahren: Hier steht bei einem Problem der Text über dem Knopf
   „Speichern“ (theme.css, 16.).
   ============================================================================= */
(function () {
  'use strict';

  function einrichten() {
    var checks = document.getElementById('checks');
    if (!checks) { return; }
    var p = document.createElement('p');
    p.className = 'bb-pruefung';
    p.setAttribute('aria-live', 'polite');
    p.hidden = true;
    var leiste = checks.closest('fieldset') || checks.parentNode;
    leiste.insertBefore(p, leiste.firstChild);

    function zeigen() {
      var text = '';
      ['conflict_check', 'policy_check'].forEach(function (id) {
        var s = document.getElementById(id);
        if (!text && s && s.classList.contains('bad')) {
          text = (s.getAttribute('title') || '').replace(/[ \t]+$/gm, '').replace(/\n{2,}/g, '\n').trim();
        }
      });
      p.textContent = text;
      p.hidden = !text;
    }
    zeigen();
    new MutationObserver(zeigen).observe(checks, { subtree: true, attributes: true, attributeFilter: ['class', 'title'] });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', einrichten);
  } else {
    einrichten();
  }
})();


/* =============================================================================
   Hinweis "Desktop-Ansicht" auf dem Handy

   Hat jemand im Browser „Desktopwebsite“ eingeschaltet, meldet der Browser
   eine PC-Breite und die Handy-Ansicht greift nicht – auch nicht in der App.
   Abschalten kann das nur der Browser selbst. Wir zeigen dann oben einen
   Hinweis, wie es geht. Er ist so groß gesetzt, dass er trotz der
   verkleinerten Desktop-Darstellung lesbar bleibt, und lässt sich für
   30 Tage wegklicken.
   ============================================================================= */
(function () {
  'use strict';

  var KEY = 'bb-desktop-hinweis';
  var TAGE = 30;

  function istDesktopAufHandy() {
    var kurz = Math.min(window.screen.width, window.screen.height);
    return navigator.maxTouchPoints > 0 && kurz > 0 && kurz < 600 &&
           window.innerWidth > 800;
  }

  function weggeklickt() {
    try {
      var t = parseInt(window.localStorage.getItem(KEY), 10);
      return t > 0 && (Date.now() - t) < TAGE * 864e5;
    } catch (e) {
      return false;
    }
  }

  function zeigen() {
    if (document.getElementById('bb-desktop-hinweis') || !istDesktopAufHandy() || weggeklickt()) {
      return;
    }
    // Faktor, um den der Browser die Desktop-Seite verkleinert
    var f = Math.max(1, window.innerWidth / Math.min(window.screen.width, window.screen.height));
    var px = function (n) { return Math.round(n * f) + 'px'; };

    var box = document.createElement('div');
    box.id = 'bb-desktop-hinweis';
    box.setAttribute('role', 'status');
    box.style.cssText = 'position:relative;z-index:1000;margin:' + px(8) + ';padding:' + px(14) + ' ' + px(16) +
      ';border-radius:' + px(12) + ';background:var(--bb-surface,#fff);color:var(--bb-text,#121714);' +
      'box-shadow:0 0 0 ' + px(2) + ' var(--bb-accent,#1b7d33);font-size:' + px(16) + ';line-height:1.45;';
    box.innerHTML =
      '<strong style="display:block;font-size:' + px(18) + ';margin-bottom:' + px(6) + '">Desktop-Ansicht ist eingeschaltet</strong>' +
      'Auf dem Handy ist die Bootsbuchung so schwer zu bedienen. Bitte im Browser ausschalten: ' +
      'Menü ⋮ → <b>Einstellungen</b> → <b>Website-Einstellungen</b> → <b>Desktopwebsite</b> aus. ' +
      'Danach die Seite bzw. App neu öffnen.' +
      '<button type="button" style="display:block;margin-top:' + px(10) + ';font:inherit;font-weight:600;' +
      'padding:' + px(8) + ' ' + px(14) + ';border-radius:' + px(8) + ';border:' + px(1) + ' solid var(--bb-border,#d3dad2);' +
      'background:transparent;color:inherit;cursor:pointer">Ausblenden</button>';
    box.querySelector('button').addEventListener('click', function () {
      try { window.localStorage.setItem(KEY, String(Date.now())); } catch (e) { /* egal */ }
      box.remove();
    });
    document.body.insertBefore(box, document.body.firstChild);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', zeigen);
  } else {
    zeigen();
  }
})();


/* =============================================================================
   Schadensfotos direkt hochladen (unterlagen.patch, bb_fotos.php)

   Der Link „Fotos vom Schaden hier hochladen“ führt sonst auf die
   Nextcloud-Seite: dort erst „Hochladen“ tippen, danach zurückwischen, und
   eine Rückmeldung gibt es kaum. Trägt der Link data-bb-upload, wird daraus
   ein Knopf, der sofort die Auswahl des Handys öffnet (Kamera, Video,
   Dateien). Die Dateien gehen über bb_fotos.php in die Ablage des Bootes,
   jede Datei zeigt ihren Fortschritt, am Ende steht, wie es weitergeht.
   Große Fotos werden vorher verkleinert (schneller am Steg), Videos nicht.
   ============================================================================= */
(function () {
  'use strict';

  var STUECK = 1536 * 1024;          // passt unter upload_max_filesize = 2 MB
  var VERKLEINERN_AB = 2.5 * 1024 * 1024;
  var KANTE = 2560;

  var ICON_KAMERA = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"></path><circle cx="12" cy="13.5" r="3.5"></circle></svg>';

  function kennung() {
    var z = new Uint8Array(12);
    window.crypto.getRandomValues(z);
    return Array.prototype.map.call(z, function (b) { return ('0' + b.toString(16)).slice(-2); }).join('');
  }

  function token() {
    var m = document.querySelector('meta[name="csrf_token"]');
    var i = document.querySelector('input[name="csrf_token"]');
    return m ? m.content : (i ? i.value : '');
  }

  // Boot: im Formular das gerade gewählte, sonst das der Buchung
  function boot(link) {
    var sel = document.getElementById('rooms');
    if (sel && sel.selectedIndex >= 0) {
      return { id: sel.options[sel.selectedIndex].value, name: sel.options[sel.selectedIndex].text };
    }
    return { id: link.getAttribute('data-room'), name: '' };
  }

  function verkleinern(datei) {
    if (!/^image\/(jpeg|png|webp)$/.test(datei.type) || datei.size < VERKLEINERN_AB || !window.createImageBitmap) {
      return Promise.resolve(datei);
    }
    return createImageBitmap(datei, { imageOrientation: 'from-image' }).then(function (bild) {
      var f = Math.min(1, KANTE / Math.max(bild.width, bild.height));
      var c = document.createElement('canvas');
      c.width = Math.round(bild.width * f);
      c.height = Math.round(bild.height * f);
      c.getContext('2d').drawImage(bild, 0, 0, c.width, c.height);
      return new Promise(function (ok) {
        c.toBlob(function (blob) {
          ok(blob && blob.size < datei.size ? new File([blob], datei.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }) : datei);
        }, 'image/jpeg', 0.85);
      });
    }).catch(function () { return datei; });
  }

  // Eine Datei in Stücken senden; fortschritt(0…1)
  function senden(url, raum, datei, nr, fortschritt) {
    var id = kennung();
    function stueck(offset) {
      var body = new FormData();
      body.append('csrf_token', token());
      body.append('room', raum);
      body.append('id', id);
      body.append('name', datei.name);
      body.append('offset', offset);
      body.append('size', datei.size);
      body.append('nr', nr);
      body.append('teil', datei.slice(offset, offset + STUECK), 'teil');
      return fetch(url, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (r) {
          return r.json().catch(function () { return {}; }).then(function (j) {
            if (r.status === 409 && typeof j.weiter === 'number') {
              return stueck(j.weiter);
            }
            if (!r.ok) {
              throw new Error(j.fehler || ('Fehler ' + r.status));
            }
            if (j.ok) {
              fortschritt(1);
              return j;
            }
            fortschritt(j.weiter / datei.size);
            return stueck(j.weiter);
          });
        }, function () {
          throw new Error('Keine Verbindung');
        });
    }
    return stueck(0);
  }

  function esc(t) {
    var d = document.createElement('span');
    d.textContent = t;
    return d.innerHTML;
  }

  function zahl(n) {
    return n === 1 ? '1 Datei' : n + ' Dateien';
  }

  function init(link) {
    var imFormular = !!link.closest('form');
    var box = document.createElement('div');
    box.className = 'bb-fotos-upload';
    // Ohne accept zeigt Android erst die Auswahl Kamera / Video / Dateien.
    // Mit accept="image/*" öffnet es direkt die Galerie, ohne Kamera.
    // Nur Fotos und Videos lässt bb_fotos.php durch.
    box.innerHTML =
      '<input type="file" multiple hidden>' +
      '<button type="button" class="bb-fotos-knopf">' + ICON_KAMERA + '<span></span></button>' +
      '<ul class="bb-fotos-liste"></ul>' +
      '<p class="bb-fotos-status" role="status" aria-live="polite"></p>';
    var eingabe = box.querySelector('input');
    var knopf = box.querySelector('button');
    var liste = box.querySelector('ul');
    var status = box.querySelector('p');
    var knopfText = link.textContent;
    knopf.querySelector('span').textContent = knopfText;
    link.parentNode.insertBefore(box, link);
    // Der Nextcloud-Link bleibt als Ausweg, falls das Hochladen hier scheitert
    link.classList.add('bb-fotos-nc');
    link.textContent = 'Fotoablage in der Nextcloud öffnen';
    link.hidden = true;
    box.appendChild(link);

    var laeuft = false;
    var nr = 0;

    knopf.addEventListener('click', function () {
      if (!laeuft) { eingabe.click(); }
    });

    eingabe.addEventListener('change', function () {
      var dateien = Array.prototype.slice.call(eingabe.files);
      eingabe.value = '';
      if (!dateien.length) { return; }
      var b = boot(link);
      laeuft = true;
      knopf.disabled = true;
      link.hidden = true;
      status.className = 'bb-fotos-status';
      var fertig = 0;

      var zeilen = dateien.map(function (d) {
        var li = document.createElement('li');
        var vorschau = /^image\//.test(d.type)
          ? '<img alt="" src="' + URL.createObjectURL(d) + '">'
          : '<span class="bb-fotos-video" aria-hidden="true">▶</span>';
        li.innerHTML = vorschau + '<span class="bb-fotos-text"><span class="bb-fotos-name"></span>' +
          '<span class="bb-fotos-balken"><span></span></span><span class="bb-fotos-meldung">wartet …</span></span>';
        li.querySelector('.bb-fotos-name').textContent = d.name;
        liste.appendChild(li);
        return li;
      });

      var kette = Promise.resolve();
      dateien.forEach(function (d, i) {
        var li = zeilen[i];
        var balken = li.querySelector('.bb-fotos-balken > span');
        var meldung = li.querySelector('.bb-fotos-meldung');
        kette = kette.then(function () {
          status.textContent = 'Wird hochgeladen … ' + (i + 1) + ' von ' + dateien.length;
          meldung.textContent = 'wird hochgeladen …';
          li.classList.add('bb-fotos-aktiv');
          return verkleinern(d);
        }).then(function (datei) {
          nr += 1;
          return senden(link.getAttribute('data-bb-upload'), b.id, datei, nr, function (anteil) {
            balken.style.width = Math.round(anteil * 100) + '%';
          });
        }).then(function (antwort) {
          fertig += 1;
          if (antwort.boot) { b.name = antwort.boot; }
          // Schadensmeldung: Dateinamen mitschicken, damit die Fotos zur Meldung gehören
          var meldeForm = link.closest('form.bb-wartung-formular');
          if (meldeForm && antwort.datei) {
            var feld = document.createElement('input');
            feld.type = 'hidden';
            feld.name = 'fotos[]';
            feld.value = antwort.datei;
            meldeForm.appendChild(feld);
          }
          li.className = 'bb-fotos-ok';
          meldung.textContent = '✓ hochgeladen';
        }, function (e) {
          li.className = 'bb-fotos-fehler';
          meldung.textContent = '✗ ' + e.message;
        });
      });

      kette.then(function () {
        laeuft = false;
        knopf.disabled = false;
        var wo = b.name ? 'in der Fotoablage von „' + esc(b.name) + '“' : 'in der Fotoablage des Bootes';
        var text;
        if (fertig === dateien.length) {
          status.className = 'bb-fotos-status bb-fotos-status-ok';
          text = '<strong>Fertig: ' + zahl(fertig) + (fertig === 1 ? ' ist ' : ' sind ') + wo + '.</strong> ';
          text += imFormular
            ? 'Schreib jetzt noch oben kurz, was los ist, und tippe auf <b>' +
              (link.closest('.bb-wartung-formular') ? (link.closest('form').querySelector('button[type=submit]') || {}).textContent || 'Meldung abschicken' : 'Speichern') +
              '</b>. Erst dann bekommt der Bootswart die Mail.'
            : 'Der Bootswart findet sie dort. Steht der Schaden noch nicht in der Buchung, dann <b>Bearbeiten</b> und unter „Gibt es etwas zu melden?“ kurz beschreiben.';
        } else {
          status.className = 'bb-fotos-status bb-fotos-status-fehler';
          text = '<strong>' + (fertig ? zahl(fertig) + ' hochgeladen, ' + zahl(dateien.length - fertig) + ' nicht.' : 'Das Hochladen hat nicht geklappt.') + '</strong> ' +
            'Tippe nochmal auf den Knopf oder lade die Fotos direkt in der Nextcloud hoch:';
          link.hidden = false;
        }
        status.innerHTML = text;
        knopf.querySelector('span').textContent = fertig ? 'Weitere Fotos hochladen' : knopfText;
      });
    });

    // Nicht mitten im Hochladen die Seite verlassen
    var form = link.closest('form');
    if (form) {
      form.addEventListener('submit', function (e) {
        if (laeuft) {
          e.preventDefault();
          e.stopImmediatePropagation();
          status.textContent = 'Bitte warten, bis die Fotos hochgeladen sind, dann Speichern.';
          box.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }
      }, true);
    }
    window.addEventListener('beforeunload', function (e) {
      if (laeuft) { e.preventDefault(); e.returnValue = ''; }
    });
  }

  function start() {
    Array.prototype.forEach.call(document.querySelectorAll('a.bb-fotos-link[data-bb-upload]'), function (link) {
      if (!link.dataset.bbInit) {
        link.dataset.bbInit = '1';
        init(link);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();


/* =============================================================================
   Boot bearbeiten (edit_room.php): eigene Felder in Gruppen

   MRBS hängt alle Zusatzspalten des Bootes einfach untereinander an. Hier
   kommen sie in eigene Abschnitte, damit klar ist, was wohin gehört:
   "Bootsdaten" (Tabelle auf der Bootsseite) und "Bootsübersicht für
   Mitglieder" (Nextcloud-Links und Infotext). Die Felder bleiben im selben
   Formular, es ändert sich nur die Anordnung.
   ============================================================================= */
(function () {
  'use strict';

  var GRUPPEN = [
    ['Bootsdaten (Bootsseite)', ['laenge', 'breite', 'tiefgang', 'masthoehe', 'gewicht']],
    ['Bootsübersicht für Mitglieder', ['infos', 'unterlagen', 'fotos']]
  ];

  function ordnen() {
    var form = document.getElementById('edit_room');
    if (!form || form.querySelector('.bb-feldgruppe')) {
      return;
    }
    var knoepfe = form.querySelector('.buttons');
    GRUPPEN.forEach(function (g) {
      var felder = g[1].map(function (name) {
        var el = form.querySelector('[name="f_' + name + '"]');
        return el && el.closest('fieldset > div');
      }).filter(Boolean);
      if (!felder.length) {
        return;
      }
      var fs = document.createElement('fieldset');
      fs.className = 'bb-feldgruppe';
      var legend = document.createElement('legend');
      legend.textContent = g[0];
      fs.appendChild(legend);
      var ziel = knoepfe ? knoepfe.parentNode : felder[0].parentNode;
      ziel.insertBefore(fs, knoepfe || null);
      felder.forEach(function (f) { fs.appendChild(f); });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ordnen);
  } else {
    ordnen();
  }
})();


/* =============================================================================
   Berichtsseite (report.php): Hinweis auf den Wartungsbericht
   (bb_wartung_bericht.php aus bootsuebersicht.patch)
   ============================================================================= */
(function () {
  'use strict';
  function add() {
    var form = document.getElementById('report_form');
    if (!form || document.querySelector('.bb-bericht-hinweis')) {
      return;
    }
    var p = document.createElement('p');
    p.className = 'bb-bericht-hinweis';
    var a = document.createElement('a');
    a.href = 'bb_wartung_bericht.php';
    a.textContent = 'Wartung & Reparaturen auswerten (Wartungsbericht)';
    p.appendChild(a);
    form.parentNode.insertBefore(p, form);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', add);
  } else {
    add();
  }
})();


/* =============================================================================
   Menü der Kopfzeile ordnen

   Erst was alle Mitglieder brauchen, dann die Punkte für Admins, Hilfe zum
   Schluss. MRBS liefert Hilfe, Bericht, Importieren, Räume (admin.php) und
   Benutzerliste; die eigenen Punkte kommen weiter oben aus diesem Skript.
   „Räume“ heißt hier „Verwaltung“: auf admin.php stellt man Verein und Boote
   ein. Den Wortlaut nicht über $vocab_override['de']['rooms'] ändern – der
   gilt auch für die Bootsauswahl im Buchungsformular.
   Läuft nach den anderen Menüpunkten (steht deshalb am Ende der Datei).
   ============================================================================= */
(function () {
  'use strict';

  var REIHENFOLGE = [
    [/bb_mitsegeln\.php/, 10],
    [/bb_boote\.php/, 20],
    [/report\.php/, 30],
    [/bb_benachrichtigungen\.php/, 40],
    [/admin\.php/, 60],
    [/edit_users\.php/, 70],
    [/import\.php/, 80],
    [/help\.php/, 99]
  ];

  function rang(a) {
    var href = a.getAttribute('href') || '';
    for (var i = 0; i < REIHENFOLGE.length; i++) {
      if (REIHENFOLGE[i][0].test(href)) { return REIHENFOLGE[i][1]; }
    }
    return 50; // Unbekanntes (z. B. Kiosk) vor die Admin-Punkte
  }

  function ordnen() {
    var menu = document.querySelector('header.banner:not(.simple) nav.menu');
    if (!menu) {
      return;
    }
    var links = Array.prototype.filter.call(menu.children, function (el) {
      return el.tagName === 'A';
    });
    links.forEach(function (a) {
      if (/(^|\/)admin\.php/.test(a.getAttribute('href') || '')) {
        a.textContent = 'Verwaltung';
      }
    });
    var sortiert = links.slice().sort(function (x, y) { return rang(x) - rang(y); });
    if (sortiert.every(function (a, i) { return a === links[i]; })) {
      return;
    }
    // Der Knopf Hell/Dunkel bleibt hinter den Links
    var vor = menu.querySelector('.bb-theme-toggle');
    sortiert.forEach(function (a) { menu.insertBefore(a, vor); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ordnen);
  } else {
    ordnen();
  }
  var start = function () {
    new MutationObserver(ordnen).observe(document.body, { childList: true });
  };
  if (document.body) { start(); } else { document.addEventListener('DOMContentLoaded', start); }
})();
