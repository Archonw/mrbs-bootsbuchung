<?php
declare(strict_types=1);
namespace MRBS;

// =============================================================================
// Theme "verein" – Buchungsregeln für den Hinweis im Plan (theme.js)
//
//   { "heute": "2026-11-12", "voraus_tage": 7, "buchbar_bis": "2026-11-26T16:20", "gemessen_an": "ende",
//     "saison": { "beginn": "2026-04-16", "ende": "2026-10-30" },
//     "tag": { "von": "07:00", "bis": "22:00" } }
//
// Keine persönlichen Daten, daher auch ohne Anmeldung.
// =============================================================================

$mrbs_root = null;
foreach ([dirname(__DIR__, 2),
          dirname((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''), 3),
          (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')] as $candidate)
{
  if ($candidate !== '' && is_file("$candidate/defaultincludes.inc"))
  {
    $mrbs_root = $candidate;
    break;
  }
}
if ($mrbs_root === null)
{
  http_response_code(500);
  exit;
}
chdir($mrbs_root);
require 'defaultincludes.inc';
require_once __DIR__ . '/regeln.inc';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, max-age=300');

$area = (int) ($_GET['area'] ?? 0);
echo json_encode(bb_regeln($area > 0 ? $area : null), JSON_UNESCAPED_SLASHES);
