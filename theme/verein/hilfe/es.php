<?php
// =============================================================================
// Ayuda de la reserva de barcos – español (ventana de ayuda, ver ../hilfe.php)
//
// Misma estructura que hilfe/de.php y hilfe/en.php.
//   'texte'  textos de la ventana de ayuda
//   'seiten' una entrada por página; las claves vienen de theme.js (hilfeSeite())
//   'fuer'   quién ve el apartado: alle (todos), gast (sin iniciar sesión),
//            mitglied (con sesión), segellehrer (instructor), bootswartung
//            (mantenimiento), admin
//   'html'   HTML sencillo (p, ul, ol, li, strong, em, a)
//
// Las páginas propias de este proyecto (navegar como tripulante, barcos,
// libro de mantenimiento …) solo están en alemán; sus botones se citan aquí
// en alemán con la traducción.
// =============================================================================

return [
  'texte' => [
    'titel'        => 'Ayuda',
    'schliessen'   => 'Cerrar',
    'themen'       => 'Otro tema',
    'alle_themen'  => 'Ayuda de la reserva de barcos',
    'mehr'         => 'Guía completa',
    'ueber'        => 'Acerca de MRBS y contacto',
    'laden'        => 'Cargando la ayuda …',
    'fehler'       => 'No se ha podido cargar la ayuda. Inténtalo de nuevo más tarde.',
    'rolle_gast'         => 'Sin iniciar sesión',
    'rolle_segellehrer'  => 'Para instructores',
    'rolle_bootswartung' => 'Para mantenimiento',
    'rolle_admin'        => 'Para administradores',
  ],

  'seiten' => [

    // -------------------------------------------------------------------------
    'allgemein' => [
      'titel' => 'Resumen: ¿qué hay dónde?',
      'abschnitte' => [
        ['fuer' => 'alle', 'titel' => 'Esta ayuda',
         'html' => '<p>La ayuda explica siempre la página en la que estás. Otros temas se eligen abajo en la ventana.</p>'],
        ['fuer' => 'gast', 'titel' => 'Iniciar sesión',
         'html' => '<p>Pulsa <strong>Entrar</strong> arriba a la derecha (en el móvil, abajo). El nombre de usuario y la contraseña te los da el administrador.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'El menú',
         'html' => '<ul>'
                 . '<li><strong>Mitsegeln &amp; Kurse</strong> (navegar como tripulante y cursos): salidas con plazas libres y cursos a los que apuntarse.</li>'
                 . '<li><strong>Boote</strong> (barcos): todos los barcos con fotos, datos, manuales y libro de mantenimiento.</li>'
                 . '<li><strong>Benachrichtigungen</strong> (notificaciones): mensajes push en el móvil en lugar de (o además de) correos.</li>'
                 . '<li><strong>Lista de usuarios</strong>: todos los socios; pulsando tu propio nombre cambias contraseña y correo.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'En el móvil',
         'html' => '<p>Abajo hay una barra con <strong>Plan</strong>, <strong>Mitsegeln</strong> (tripulante), <strong>Boote</strong> (barcos), <strong>Meine</strong> (tus reservas e inscripciones) y <strong>Mehr</strong> (más). En «Mehr» están el resto del menú, la selección de fecha y la búsqueda.</p>'
                 . '<p>Consejo: en el menú del navegador elige <strong>«Añadir a pantalla de inicio»</strong> o <strong>«Instalar aplicación»</strong> y tendrás la reserva como una app.</p>'],
        ['fuer' => 'alle', 'titel' => 'Claro u oscuro',
         'html' => '<p>El botón con el círculo, el sol o la luna en la cabecera cambia entre <strong>Automatisch → Hell → Dunkel</strong> (automático, claro, oscuro). El navegador recuerda tu elección.</p>'],
        ['fuer' => 'alle', 'titel' => '¿A quién pregunto?',
         'html' => '<p>Para problemas con la reserva, al administrador; para preguntas sobre un barco, al responsable de mantenimiento o a la persona que cuida ese barco.</p>'],
        ['fuer' => 'admin', 'titel' => 'Menú para administradores',
         'html' => '<ul>'
                 . '<li><strong>Informes</strong>: analizar reservas; arriba, el enlace al informe de mantenimiento.</li>'
                 . '<li><strong>Verwaltung</strong> (administración): temporada (periodo de reserva), club y barcos.</li>'
                 . '<li><strong>Lista de usuarios</strong>: dar de alta socios y asignar roles (instructor, mantenimiento, administrador).</li>'
                 . '<li><strong>Importar</strong>: pasar reservas desde un archivo de calendario (.ics).</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'anmelden' => [
      'titel' => 'Iniciar sesión',
      'abschnitte' => [
        ['fuer' => 'gast', 'titel' => 'Cómo entrar',
         'html' => '<p>Escribe tu nombre de usuario y contraseña y pulsa <strong>Entrar</strong>. Te los da el administrador.</p>'
                 . '<p>Si no has usado la reserva durante una semana, se cierra la sesión automáticamente y tienes que volver a entrar.</p>'],
        ['fuer' => 'gast', 'titel' => '¿Has olvidado la contraseña?',
         'html' => '<p>Pide al administrador que te ponga una nueva. Después puedes cambiarla tú mismo en tu nombre.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'plan' => [
      'titel' => 'Plan de reservas (día, semana, mes)',
      'abschnitte' => [
        ['fuer' => 'alle', 'titel' => 'Ver y pasar páginas',
         'html' => '<ul>'
                 . '<li><strong>Día</strong>, <strong>semana</strong> y <strong>mes</strong> se eligen en la barra de arriba.</li>'
                 . '<li>Cada columna (o fila) es un <strong>barco</strong>, cada bloque de color una reserva.</li>'
                 . '<li>Con las flechas pasas hacia delante y atrás; <strong>hoy</strong> vuelve al día actual. Una fecha concreta se elige en la cabecera (en el móvil, en «Mehr»).</li>'
                 . '<li>Al pulsar una reserva ves los detalles.</li>'
                 . '</ul>'],
        ['fuer' => 'gast', 'titel' => '¿Solo se ve «ocupado»?',
         'html' => '<p>Sin iniciar sesión solo ves que un barco está ocupado, no por quién. Para reservar, entra primero.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Colores',
         'html' => '<ul>'
                 . '<li><strong>Verde claro</strong>: reserva normal.</li>'
                 . '<li><strong>Verde intenso</strong>: se buscan tripulantes. El número detrás (p. ej. «· 2/3») indica inscritos y plazas.</li>'
                 . '<li><strong>Naranja</strong>: curso de un instructor.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Reservar un barco',
         'html' => '<p>En la vista de día o semana pulsa un <strong>hueco libre</strong> en el barco y la hora que quieras. En el móvil también con el botón redondo <strong>+</strong>. Se abre el formulario de reserva.</p>'
                 . '<p>Solo se puede reservar durante la <strong>temporada</strong>. Con cuánta antelación y en qué horario lo decide el club; fuera de eso la reserva no se puede guardar.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'En el móvil: lista o cuadrícula',
         'html' => '<p>En el móvil la vista de día muestra cada barco como una tarjeta con sus reservas. Con el selector <strong>Liste / Raster</strong> (lista / cuadrícula) vuelves a la tabla clásica.</p>'],
        ['fuer' => 'admin', 'titel' => 'Reservas ajenas, series, temporada',
         'html' => '<p>Los administradores pueden abrir, cambiar y eliminar cualquier reserva y son los únicos que pueden crear <strong>series</strong> (reservas periódicas). La temporada se ajusta en <strong>Verwaltung</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'buchen' => [
      'titel' => 'Formulario de reserva',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Los campos',
         'html' => '<ul>'
                 . '<li><strong>Nombre</strong>: normalmente tu nombre; aparece en el plan.</li>'
                 . '<li><strong>Fecha de inicio</strong> y <strong>Fecha final</strong>: fecha y hora.</li>'
                 . '<li><strong>Sala</strong> (el barco): ya está elegido, pero puedes cambiarlo.</li>'
                 . '<li><strong>Pública</strong> o <strong>Privada</strong>: con «Privada» los demás solo ven «ocupado».</li>'
                 . '</ul>'
                 . '<p>Al final, <strong>Guardar</strong>. Recibirás una confirmación por correo con una entrada de calendario.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Invitar tripulantes',
         'html' => '<p>Marca <strong>Permitir registro</strong> (se buscan tripulantes). Opcionalmente marca <strong>Establecer límite de asistentes</strong> e indica el número de plazas; al lado ves el máximo (capacidad del barco menos tu plaza). La salida es entonces siempre pública y aparece en «Mitsegeln &amp; Kurse».</p>'],
        ['fuer' => 'mitglied', 'titel' => '¿Algo que comunicar?',
         'html' => '<p>Rellena <strong>Descripción completa</strong> solo si algo del barco está roto o falta. Entonces se envía automáticamente un correo al responsable de mantenimiento y a la persona que cuida el barco. Con el botón para subir fotos debajo del campo puedes añadir imágenes. También se puede rellenar después de la salida (abrir la reserva, <strong>Modificar</strong>).</p>'],
        ['fuer' => 'mitglied', 'titel' => '¿No se puede guardar?',
         'html' => '<ul>'
                 . '<li>Si el barco ya está reservado, el formulario muestra con qué reserva coincide. Elige otra hora u otro barco.</li>'
                 . '<li>El día está demasiado lejos o fuera de la temporada.</li>'
                 . '<li>En el móvil el botón <strong>Guardar</strong> está fijo abajo en la pantalla.</li>'
                 . '</ul>'],
        ['fuer' => 'segellehrer', 'titel' => 'Ofrecer un curso',
         'html' => '<p>Marca <strong>«Als Kurs anbieten»</strong> (ofrecer como curso). El curso queda abierto a inscripciones y es público. En los cursos puedes ofrecer todas las plazas del barco. Los cursos con varias fechas los crea un administrador como serie.</p>'],
        ['fuer' => 'admin', 'titel' => 'Series y reservas ajenas',
         'html' => '<p>Con <strong>Tipo de repetición</strong> creas una serie, p. ej. todos los sábados de mayo. Si la serie cambia más tarde, las inscripciones se mantienen y los participantes reciben un correo. Para un instructor, escribe su nombre.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'buchung' => [
      'titel' => 'Ver una reserva',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => '¿Qué aparece aquí?',
         'html' => '<p>Barco, hora, quién reservó y, si hay, la observación. En muchos barcos también hay un enlace a la <strong>documentación del barco</strong> (manual, listas de comprobación).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Cambiar o eliminar tu reserva',
         'html' => '<p>Abajo, <strong>Modificar</strong> o <strong>Eliminar</strong>. Las reservas de otros no se pueden cambiar. Por favor, elimina las reservas que no vayas a usar para que otro pueda aprovechar el barco. Los tripulantes inscritos reciben un correo automáticamente si cancelas o cambias la salida.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Tripulante: apuntarse y borrarse',
         'html' => '<p>Si la salida admite tripulantes, verás los botones para apuntarte o borrarte. Recibirás una confirmación y un recordatorio la tarde anterior. «Este evento está lleno.» significa que no quedan plazas.</p>'
                 . '<p>Si la fecha pertenece a una serie de cursos, además hay <strong>«Für alle Termine anmelden»</strong> (apuntarse a todas las fechas) y <strong>«Von allen Terminen abmelden»</strong> (borrarse de todas).</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Comunicar daños después de la salida',
         'html' => '<p>Abre tu reserva, pulsa <strong>Modificar</strong>, rellena «Descripción completa» y guarda. Las fotos se suben con el botón de fotos (también aparece aquí).</p>'],
        ['fuer' => 'segellehrer', 'titel' => 'Imprimir la lista de participantes',
         'html' => '<p>El botón <strong>«Teilnehmerliste drucken»</strong> (imprimir lista) abre una lista de todos los inscritos para imprimir o marcar en el pantalán. Pueden verla quien creó la reserva, los instructores y los administradores.</p>'],
        ['fuer' => 'admin', 'titel' => 'Reservas ajenas',
         'html' => '<p>Puedes modificar, eliminar y copiar cualquier reserva y borrar participantes.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'mitsegeln' => [
      'titel' => 'Tripulantes y cursos',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => '¿Qué aparece aquí?',
         'html' => '<p>Todas las próximas salidas con plazas libres y todos los cursos, ordenados por día. Filtra con <strong>Alle / Kurse / Mitsegeln / Meine</strong> (todo / cursos / tripulante / mío). «Meine» muestra tus reservas y todo aquello en lo que estás inscrito.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Apuntarse a una salida',
         'html' => '<ol><li>Pulsa una salida o un curso.</li><li>Apúntate.</li><li>Tú y el patrón recibís una confirmación por correo. La tarde anterior llega un recordatorio.</li></ol>'
                 . '<p>¿Al final no puedes? Abre la salida y bórrate, por favor lo antes posible.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Buscar tripulantes tú mismo',
         'html' => '<p>Al reservar marca <strong>Permitir registro</strong>. Tu salida aparecerá aquí. No puedes apuntarte a tu propia reserva: ya vas a bordo.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boote' => [
      'titel' => 'Vista de barcos',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Nuestros barcos',
         'html' => '<p>Todos los barcos con foto y nombre. Pulsa un barco para ver información, fotos, manuales (p. ej. para montar y desmontar) y el libro de mantenimiento. Desde ahí también puedes reservar el barco o comunicar un daño.</p>'],
        ['fuer' => 'admin', 'titel' => 'Mantener fotos y documentos',
         'html' => '<p>Las fotos y documentos vienen de la carpeta compartida de Nextcloud indicada en el campo «Unterlagen (Link)» del barco (opcional); como foto de la tarjeta se usa <strong>titelbild.jpg</strong>. Los barcos se crean y cambian en <strong>Verwaltung</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boot' => [
      'titel' => 'Página del barco',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => '¿Qué aparece aquí?',
         'html' => '<ul>'
                 . '<li>Arriba: foto, capacidad y datos del barco (eslora, manga, calado …).</li>'
                 . '<li><strong>Información</strong> sobre el barco, p. ej. amarre y dónde está la llave.</li>'
                 . '<li><strong>Bilder</strong> (fotos, se amplían al pulsar) y <strong>Dokumente</strong> (documentos) como manuales y listas de comprobación (se descargan al pulsar).</li>'
                 . '<li>En <strong>Wartung &amp; Reparaturen</strong> (mantenimiento y reparaciones) los últimos trabajos y los daños pendientes. <strong>Logbuch öffnen</strong> muestra todas las entradas.</li>'
                 . '</ul>'],
        ['fuer' => 'mitglied', 'titel' => 'Reservar',
         'html' => '<p><strong>Belegung ansehen &amp; buchen</strong> (ver ocupación y reservar) muestra la semana de este barco. Pulsa allí un hueco libre.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Comunicar un daño',
         'html' => '<p>Con <strong>Schaden melden</strong> (comunicar daño) describes qué está roto y añades fotos, también sin reserva. El responsable de mantenimiento y la persona que cuida el barco reciben un correo; el aviso aparece como <strong>offen</strong> (pendiente) en el libro.</p>'],
        ['fuer' => 'bootswartung', 'titel' => 'Llevar el libro de mantenimiento',
         'html' => '<p>Cada entrada tiene <strong>Bearbeiten · Löschen</strong> (modificar · eliminar); los avisos pendientes tienen el botón <strong>Schaden behoben</strong> (daño reparado).</p>'],
        ['fuer' => 'admin', 'titel' => 'Modificar el barco, recargar archivos',
         'html' => '<p><strong>Boot bearbeiten</strong> cambia el texto informativo, los datos y los enlaces. Los archivos nuevos de la carpeta de Nextcloud aparecen en 15 minutos como máximo, al momento con <strong>Dateien neu laden</strong> (recargar archivos) abajo del todo.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'logbuch' => [
      'titel' => 'Libro de mantenimiento',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => '¿Qué aparece aquí?',
         'html' => '<p>Todos los trabajos y avisos de daños de este barco, los más recientes primero. <strong>offen</strong> (pendiente) significa que el daño aún no está reparado. El botón <strong>Bericht</strong> muestra el informe de mantenimiento de todos los barcos.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Cambiar tu aviso',
         'html' => '<p>Mientras tu aviso esté pendiente puedes corregirlo o añadir fotos con <strong>Meldung ändern</strong> (cambiar aviso). El aviso modificado se envía de nuevo al responsable. Cuando el daño esté reparado, recibirás un correo.</p>'],
        ['fuer' => 'bootswartung', 'titel' => 'Añadir entradas y cerrar daños',
         'html' => '<ul>'
                 . '<li><strong>Neuer Eintrag</strong> (nueva entrada): fecha, estado, qué se hizo, quién, material, horas y coste.</li>'
                 . '<li><strong>Schaden behoben</strong> (daño reparado) en un aviso pendiente: indica qué se hizo y cuándo. Quien lo comunicó recibe un correo.</li>'
                 . '<li><strong>Bearbeiten · Löschen</strong> debajo de cada entrada. Las fotos de un aviso eliminado siguen en la carpeta de fotos y hay que borrarlas allí a mano; la página indica los archivos.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'schaden' => [
      'titel' => 'Comunicar un daño',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Cómo funciona',
         'html' => '<ol><li>En <strong>Was ist kaputt?</strong> (¿qué está roto?) describe brevemente qué pasa.</li>'
                 . '<li>Si hace falta, <strong>Fotos hinzufügen</strong> (añadir fotos): el móvil ofrece cámara, vídeo o archivos; puedes elegir varias fotos a la vez.</li>'
                 . '<li>Pulsa <strong>Meldung abschicken</strong> (enviar aviso).</li></ol>'
                 . '<p>El responsable de mantenimiento y la persona que cuida el barco reciben un correo; el aviso aparece como pendiente en el libro. En una emergencia (barco no utilizable, accidente) llama además directamente al responsable.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'wartungsbericht' => [
      'titel' => 'Informe de mantenimiento',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Analizar',
         'html' => '<p>Filtra por periodo (por defecto: 1 de enero hasta hoy), barco, tipo (trabajos o avisos de daños), estado y texto. Arriba ves los totales por barco (entradas, avisos pendientes, horas, coste) y debajo todas las entradas. <strong>Als CSV (Excel)</strong> descarga la misma selección como archivo.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'bericht' => [
      'titel' => 'Informes',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Analizar reservas',
         'html' => '<p>Elige un periodo y, si hace falta, un barco o términos de búsqueda, y pulsa <strong>Pedir Informe</strong>. En <strong>Salida</strong> eliges un informe (lista de reservas), un resumen (p. ej. horas por barco) o ambos; en el formato también un archivo para descargar (CSV, iCalendar).</p>'
                 . '<p>Para los trabajos y daños de los barcos hay arriba un enlace al informe de mantenimiento.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'suche' => [
      'titel' => 'Búsqueda',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Buscar reservas',
         'html' => '<p>El campo de búsqueda de la cabecera (en el móvil, en «Mehr») encuentra próximas reservas cuya descripción contiene el término, p. ej. un nombre. Pulsa un resultado para abrir la reserva.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'benachrichtigungen' => [
      'titel' => 'Notificaciones',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Push en el móvil',
         'html' => '<p>Con <strong>Push auf diesem Gerät einschalten</strong> (activar push en este dispositivo) recibes todo lo que la reserva te envía por correo también como mensaje en el móvil. Va por dispositivo: actívalo en cada móvil u ordenador. <strong>Test-Nachricht senden</strong> (enviar mensaje de prueba) muestra si funciona.</p>'
                 . '<p>Con <strong>«Keine Mails mehr, wenn die Push-Nachricht ankommt»</strong> (sin correos si llega el push) evitas recibirlo todo dos veces. Si el push no llega, se sigue enviando el correo.</p>'],
        ['fuer' => 'mitglied', 'titel' => '¿No funciona?',
         'html' => '<ul><li>En el iPhone el push solo funciona si la reserva está instalada con «Añadir a pantalla de inicio» y se abre desde allí.</li>'
                 . '<li>Si alguna vez bloqueaste las notificaciones de esta web, vuelve a permitirlas en los ajustes del navegador o del móvil.</li></ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'benutzer' => [
      'titel' => 'Lista de usuarios y tu perfil',
      'abschnitte' => [
        ['fuer' => 'mitglied', 'titel' => 'Encontrar socios',
         'html' => '<p>La lista muestra nombre, apellido y permisos de todos los socios. Con el campo de búsqueda encuentras a alguien rápidamente; pulsando un encabezado ordenas.</p>'],
        ['fuer' => 'mitglied', 'titel' => 'Cambiar contraseña y correo',
         'html' => '<p>Pulsa <strong>tu propio nombre</strong>, cambia la contraseña o el correo y guarda. Si tu correo no es correcto, no recibirás confirmaciones.</p>'],
        ['fuer' => 'admin', 'titel' => 'Gestionar socios',
         'html' => '<ul>'
                 . '<li>Nuevo usuario: nombre de usuario, nombre visible como «Nombre Apellido», correo, contraseña inicial, permisos «usuario».</li>'
                 . '<li>Solo los administradores marcan <strong>Segellehrer</strong> (instructor, puede ofrecer cursos) y <strong>Bootswartung</strong> (mantenimiento, puede escribir en el libro).</li>'
                 . '<li>Contraseña olvidada: abre el usuario y escribe una nueva. Baja de un socio: elimina el usuario; sus reservas antiguas se mantienen.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'verwaltung' => [
      'titel' => 'Administración',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Temporada (periodo de reserva)',
         'html' => '<p>En <strong>Buchungszeitraum (Saison)</strong> indica inicio y fin de temporada y guarda. Vale al momento; un campo vacío significa «sin límite». Se puede reservar el primer y el último día. <strong>¡Ajústalo cada año antes de que empiece la temporada!</strong></p>'],
        ['fuer' => 'admin', 'titel' => 'Club y barcos',
         'html' => '<ul>'
                 . '<li><strong>Modificar área</strong> (el club): entre otras cosas el correo del responsable de mantenimiento para los avisos de daños.</li>'
                 . '<li><strong>Agregar sala</strong> (un barco) o <strong>Modificar</strong> junto a un barco: nombre, capacidad, datos, información, enlaces a documentación y carpeta de fotos, correo de la persona que cuida el barco.</li>'
                 . '<li><strong>Deshabilitar</strong> un barco en lugar de eliminarlo: desaparece del plan, las reservas antiguas se mantienen.</li>'
                 . '</ul>'],
        ['fuer' => 'admin', 'titel' => 'Persona de contacto (página de ayuda)',
         'html' => '<p>Abajo del todo, en <strong>Ansprechpartner (Hilfeseite)</strong>, indicas a quién nombra la página de ayuda para preguntas: <strong>Name</strong> (nombre, se inserta en la frase «Póngase en contacto con …») y <strong>E-Mail</strong> (el nombre pasa a ser un enlace de correo). Un campo vacío usa el valor de config.inc.php.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'boot_bearbeiten' => [
      'titel' => 'Modificar un barco',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Los campos más importantes',
         'html' => '<ul>'
                 . '<li>El <strong>nombre</strong> es la columna en el plan, la <strong>Clave de ordenación</strong> fija el orden.</li>'
                 . '<li><strong>Capacidad</strong>: personas a bordo; limita las plazas para tripulantes (0 = sin límite).</li>'
                 . '<li><strong>Correo electrónico del administrador de la sala</strong>: la persona que cuida este barco; recibe los avisos de daños (varias direcciones separadas por comas).</li>'
                 . '<li>Datos del barco: eslora, manga, calado, altura del mástil, peso, cada uno con unidad, p. ej. «6,50 m».</li>'
                 . '<li>Información para socios: texto simple; una línea en blanco empieza un párrafo nuevo, los enlaces se pueden pulsar.</li>'
                 . '<li><strong>Unterlagen (Link)</strong> (opcional): enlace a manual y listas. Si es una carpeta de Nextcloud compartida «solo lectura» sin contraseña, la página del barco muestra también sus fotos y documentos.</li>'
                 . '<li><strong>Fotoablage (Link)</strong> (opcional): carpeta para subir fotos de daños, p. ej. una carpeta de Nextcloud «solo subir», no dentro de la carpeta de documentación.</li>'
                 . '</ul>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'verein_bearbeiten' => [
      'titel' => 'Modificar el club',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Ajustes del club',
         'html' => '<p>Lo más importante es el <strong>correo del administrador del área</strong>: el responsable de mantenimiento, que recibe cada aviso de daño. Además hay ajustes básicos como el horario de reservas y con cuánta antelación se puede reservar. La temporada se ajusta en <strong>Verwaltung → Buchungszeitraum</strong>.</p>'],
      ],
    ],

    // -------------------------------------------------------------------------
    'import' => [
      'titel' => 'Importar',
      'abschnitte' => [
        ['fuer' => 'admin', 'titel' => 'Reservas desde un archivo de calendario',
         'html' => '<p>Sube un archivo iCalendar (.ics, p. ej. de otro calendario), elige club y barco e importa. Con la opción de omitir conflictos, las reservas existentes no se tocan. Mejor pruébalo antes con pocas fechas.</p>'],
      ],
    ],
  ],
];
