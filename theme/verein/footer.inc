<?php
declare(strict_types=1);
namespace MRBS;

// Theme "verein": wie der MRBS-Standard, zusätzlich Links zu Impressum und
// Datenschutz – Adressen in der config.inc.php:
//   $bb_impressum_url   = 'https://www.mein-verein.de/impressum';
//   $bb_datenschutz_url = 'https://www.mein-verein.de/datenschutz';
function print_theme_footer()
{
  global $bb_impressum_url, $bb_datenschutz_url;

  $links = [];
  if (!empty($bb_impressum_url))
  {
    $links[] = '<a href="' . escape_html($bb_impressum_url) . '" target="_blank" rel="noopener">Impressum</a>';
  }
  if (!empty($bb_datenschutz_url))
  {
    $links[] = '<a href="' . escape_html($bb_datenschutz_url) . '" target="_blank" rel="noopener">Datenschutz</a>';
  }
  if (!empty($links))
  {
    echo '<footer class="bb-footer">' . implode(' ', $links) . "</footer>\n";
  }
  echo "</div>\n";  // schließt das contents-div aus print_theme_header()
  echo "</body>\n";
  echo "</html>\n";
}
