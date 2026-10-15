<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Bootsbuchung für Vereine
//
// MRBS lädt zuerst Themes/default/styling.inc und danach diese Datei, hier
// steht also nur, was vom Standard abweicht.
//
// Jede Farbe ist eine CSS-Variable (definiert in theme.css, getrennt für hell
// und dunkel). Der zweite Wert ist der Ersatz für Stellen ohne theme.css –
// vor allem E-Mails, deren Programme oft keine CSS-Variablen kennen. Dort
// erscheint dann einfach das helle Design.
// =============================================================================

$bb_var = function (string $name, string $fallback) : string {
  return "var(--bb-$name, $fallback)";
};

// ***** GRUNDFARBEN ********************
$body_background_color          = $bb_var('bg', '#e8ece7');
$standard_font_color            = $bb_var('text', '#121714');
$header_font_color              = $bb_var('banner-text', '#f2f4f2');
$highlight_font_color           = $bb_var('danger', '#b42318');
$color_key_font_color           = $bb_var('text', '#121714');

// Kopfzeile: bleibt in beiden Modi schwarz (Wiedererkennung)
$banner_back_color              = $bb_var('banner', '#0c0e0d');
$banner_border_color            = $bb_var('banner', '#0c0e0d');
$banner_font_color              = $bb_var('banner-text', '#f2f4f2');
$banner_nav_hover_color         = $bb_var('banner-hover', '#1b201d');

$minical_view_color             = $bb_var('accent', '#1b7d33');
$minical_today_color            = $bb_var('accent-tint', '#e3f1e6');

// Tabellenköpfe sind hell (bzw. dunkel), nicht mehr in Bannerfarbe
$header_back_color              = $bb_var('surface', '#ffffff');
$flatpickr_highlight_color      = $bb_var('accent', '#1b7d33');

$admin_table_header_back_color  = $bb_var('surface-2', '#f7f8f6');
$admin_table_header_sep_color   = $bb_var('border', '#d3dad2');
$admin_table_header_font_color  = $bb_var('text', '#121714');
$admin_table_border_color       = $bb_var('border', '#d3dad2');

// ***** HAUPTTABELLE (Tag/Woche/Monat) **
$main_table_border_color        = $bb_var('border', '#d3dad2');
$main_table_header_border_color = $bb_var('border', '#d3dad2');
$main_table_body_h_border_color = $bb_var('border-soft', '#e6eae5');
$main_table_body_v_border_color = $bb_var('border-soft', '#e6eae5');
$main_table_month_color         = $bb_var('surface', '#ffffff');
$main_table_month_weekend_color = $bb_var('weekend', '#f6f7f5');
$main_table_month_holiday_color = $bb_var('holiday', '#eef1ed');
$main_table_month_weekend_holiday_color = $bb_var('holiday', '#eef1ed');
$main_table_month_invalid_color = $bb_var('invalid', '#e6e9e5');
$main_table_slot_invalid_color  = $bb_var('invalid', '#e6e9e5');
$main_table_slot_private_type_color = $bb_var('invalid', '#e6e9e5');
$main_table_labels_back_color   = $bb_var('surface', '#ffffff');
$timeline_color                 = $bb_var('accent', '#1b7d33');

$report_table_border_color      = $bb_var('border', '#d3dad2');
$report_h2_border_color         = $bb_var('accent', '#1b7d33');
$report_h3_border_color         = $bb_var('border', '#d3dad2');
$search_table_border_color      = $bb_var('border', '#d3dad2');
$site_faq_entry_border_color    = $bb_var('border', '#d3dad2');

// ***** LINKS ***************************
$anchor_link_color              = $bb_var('link', '#155f27');
$anchor_visited_color           = $bb_var('link', '#155f27');
$anchor_hover_color             = $bb_var('link-hover', '#0e4a1d');

$anchor_link_color_banner       = $bb_var('banner-text', '#f2f4f2');
$anchor_visited_color_banner    = $bb_var('banner-text', '#f2f4f2');
$anchor_hover_color_banner      = $bb_var('banner-text', '#f2f4f2');

$anchor_link_color_header       = $bb_var('text', '#121714');
$anchor_visited_color_header    = $bb_var('text', '#121714');
$anchor_hover_color_header      = $bb_var('accent', '#1b7d33');

// ***** ZEILEN **************************
$column_hidden_color            = $bb_var('invalid', '#e6e9e5');
$calendar_hidden_color          = $bb_var('invalid', '#e6e9e5');
$row_highlight_color            = $bb_var('accent-tint', '#e3f1e6');
$row_even_color                 = $bb_var('surface', '#ffffff');
$row_odd_color                  = $bb_var('surface-2', '#f7f8f6');
$row_even_color_weekend         = $bb_var('weekend', '#f6f7f5');
$row_odd_color_weekend          = $bb_var('weekend-2', '#f0f2ef');
$row_even_color_holiday         = $bb_var('holiday', '#eef1ed');
$row_odd_color_holiday          = $bb_var('holiday', '#eef1ed');
$row_even_color_weekend_holiday = $bb_var('holiday', '#eef1ed');
$row_odd_color_weekend_holiday  = $bb_var('holiday', '#eef1ed');

$zebra_even_color = $bb_var('surface', '#ffffff');
$zebra_odd_color  = $bb_var('surface-2', '#f7f8f6');

$help_highlight_color           = $bb_var('accent-tint', '#e3f1e6');

// ***** BUTTONS *************************
$button_color_stops = array($bb_var('surface', '#ffffff'), $bb_var('surface', '#ffffff'));
$button_inset_color = $bb_var('accent', '#1b7d33');

// ***** BUCHUNGSTYPEN *******************
// E = normale Buchung, I = Kurs/intern (kräftig). Die übrigen sind ruhige,
// gut unterscheidbare Töne für den Fall, dass weitere Typen dazukommen.
$color_types = array(
    'A' => $bb_var('type-a', '#fdecc8'),
    'B' => $bb_var('type-b', '#d6e6f5'),
    'C' => $bb_var('type-c', '#efe3f7'),
    'D' => $bb_var('type-d', '#fde1dc'),
    'E' => $bb_var('type-e', '#dcefe0'),
    'F' => $bb_var('type-f', '#e4e8e3'),
    'G' => $bb_var('type-g', '#d8f0ea'),
    'H' => $bb_var('type-h', '#f5ecd9'),
    'I' => $bb_var('type-i', '#1b7d33'),
    'J' => $bb_var('type-j', '#e9f5df'));

// ***** FREIGABEN / AUSSTEHEND **********
$outstanding_color         = $bb_var('warning', '#b54708');
$pending_header_back_color = $bb_var('surface-2', '#f7f8f6');
$series_entry_back_color   = $bb_var('accent-tint', '#e3f1e6');
$pending_control_color     = $bb_var('accent-tint', '#e3f1e6');
$attention_color           = $bb_var('warning', '#b54708');

// ***** MASSE ***************************
$banner_border_width          = '0';
$banner_border_cell_width     = '0';
$main_table_border_width      = '0';
$main_table_cell_border_width = '1';
$main_cell_height             = '2.4em';

// ***** SCHRIFT *************************
$standard_font_family  = "'Figtree', system-ui, -apple-system, 'Segoe UI', sans-serif";

unset($bb_var);
