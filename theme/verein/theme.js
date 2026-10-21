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
   Menüpunkt "Mitsegeln & Kurse" (Seite bb_mitsegeln.php aus kurs.patch)
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
