<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../server/database.php';
use Dompdf\Dompdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Utilitaire
function formatPostedDate($key, $format = 'm-d-Y') {
    if (!empty($_POST[$key])) {
        try {
            $date = new DateTime($_POST[$key]);
            return $date->format($format);
        } catch (Exception $e) { /* ignore */ }
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode("pas de données reçues"); exit; }

// === Récupération des champs ===
$authorizingStudent   = $_POST["authorizingStudent"] ?? '';
$studentSignature     = $_POST["studentSignature"] ?? '';
$studentSignatureDate = formatPostedDate("studentSignatureDate");
$className            = $_POST["className"] ?? '';
$completedBy          = $_POST["completedBy"] ?? '';
$PrimaryCareProvider  = $_POST["PrimaryCareProvider"] ?? '';
$formCompletedDate    = formatPostedDate("formCompletedDate");
$tbTestDate           = formatPostedDate("tbTestDate");
$tbResult             = $_POST["tbResult"] ?? '';
$PrimaryCarePSignDate = formatPostedDate("PrimaryCarePSignDate");
$PrimaryCarePAddress  = $_POST["PrimaryCarePAddress"] ?? '';
$studentName          = $_POST["studentName"] ?? '';

// === Yes/No TB ===
$tbYes = ($_POST['checkbox1'] ?? '') === 'yes';
$tbExplanation = '';

if ($tbYes) {
    $tbExplanation = ''; // pas nécessaire
} else {
    // case No → on attend un texte
    if (!empty($_POST['tbExplanation'])) {
        $tbExplanation = $_POST['tbExplanation'];
    } else {
        echo json_encode(['success'=>false,'message'=>'Please enter TB explanation']);
        exit;
    }
}

// === Yes/No Physical condition ===
$phYes = ($_POST['checkbox3'] ?? '') === 'yes';
$phExplanation = '';

if ($phYes) {
    $phExplanation = ''; // pas nécessaire
} else {
    // case No → on attend un texte
    if (!empty($_POST['phExplanation'])) {
        $phExplanation = $_POST['phExplanation'];
    } else {
        echo json_encode(['success'=>false,'message'=>'Please enter Physical Condition explanation']);
        exit;
    }
}


// === Logo en base64 ===
$logoPath = __DIR__ . '/assets/Logo_QualityFirst.png';
$base64Logo = '';
if (is_file($logoPath)) {
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
}
$logoTag = $base64Logo ? '<img src="'.$base64Logo.'" />' : '';

// === Cachet en base64 ===
$cachetPath = __DIR__ . '/assets/foyetcachet.png';
$base64Cachet = '';
if (is_file($logoPath)) {
    $type = pathinfo($cachetPath, PATHINFO_EXTENSION);
    $data = file_get_contents($cachetPath);
    $base64Cachet = 'data:image/' . $type . ';base64,' . base64_encode($data);
}
$cachetTag = $base64Cachet ? '<img src="'.$base64Cachet.'" />' : '';

// === Charger le template ===
$templatePath = __DIR__ . '/templatehsi.html';
$template = file_get_contents($templatePath);
if ($template === false) { echo "Template introuvable"; exit; }

// === Remplacements ===
// NB: on n’échappe PAS les champs qui contiennent déjà du HTML (logoTag, classes)
$replacements = [
    '{{logo}}'                => $logoTag,
    '{{studentName}}'         => htmlspecialchars($studentName),
    '{{className}}'           => htmlspecialchars($className),
    '{{authorizingStudent}}'  => htmlspecialchars($authorizingStudent),
    '{{studentSignature}}'    => htmlspecialchars($studentSignature),
    '{{studentSignatureDate}}'=> htmlspecialchars($studentSignatureDate),
    '{{completedBy}}'         => htmlspecialchars($completedBy),
    '{{PrimaryCareProvider}}' => htmlspecialchars($PrimaryCareProvider),
    '{{formCompletedDate}}'   => htmlspecialchars($formCompletedDate),
    '{{tbTestDate}}'          => htmlspecialchars($tbTestDate),
    '{{tbResult}}'            => htmlspecialchars($tbResult),

    // Cases à cocher via classes CSS
    '{{tbYesClass}}'          => $tbYes ? 'checked' : '',
    '{{tbNoClass}}'           => $tbYes ? '' : 'checked',
    '{{tbExplanation}}'       => htmlspecialchars($tbExplanation),

    '{{phYesClass}}'          => $phYes ? 'checked' : '',
    '{{phNoClass}}'           => $phYes ? '' : 'checked',
    '{{phExplanation}}'       => htmlspecialchars($phExplanation),

    '{{PrimaryCarePSignature}}' => htmlspecialchars($_POST['PrimaryCarePSignature'] ?? ''),
    '{{PrimaryCarePSignDate}}'  => htmlspecialchars($PrimaryCarePSignDate),
    '{{PrimaryCarePAddress}}'   => htmlspecialchars($PrimaryCarePAddress),
    '{{cachet}}'  => $cachetTag,
];

// Appliquer les remplacements
$html = strtr($template, $replacements);

// === Générer le PDF ===
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream('health_certification.pdf', ['Attachment' => false]);
exit;

?>