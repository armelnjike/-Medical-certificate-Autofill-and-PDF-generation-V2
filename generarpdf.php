<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;


session_start();

$inactivityLimit = 30*60*1000;

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Pas connecté => rediriger vers la page de login
    header('Location: administration/login.php');
    exit();
}

// Mise à jour de l'heure de dernière activité
$_SESSION['last_activity'] = time();

// Gérer OPTIONS (pré-vol)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Formatte une date venant du $_POST
 *
 * @param string $key    La clé dans $_POST
 * @param string $format Le format de sortie
 * @return string        Chaîne formatée ou '' si invalide
 */
function formatPostedDate($key, $format = 'm-d-Y') {
    if (!empty($_POST[$key])) {
        try {
            $date = new DateTime($_POST[$key]);
            return $date->format($format);
        } catch (Exception $e) {
            // Retourne chaîne vide si la date est invalide
            return '';
        }
    }
    return '';
}


//patient info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientName = $_POST["patientName"];
    $birthdate =formatPostedDate("birthDate");
    $phone = $_POST["phoneNumber"];
    $sex = $_POST["sex"];
    $street = $_POST["street"];

    // Medical Tests & Examinations
    $carePractitionerName = $_POST["careP"];

    $ppdPlantedOn = formatPostedDate("ppdPlantedOn");
    $ppdReadOn    = formatPostedDate("ppdReadOn");
    $ppdResult    = formatPostedDate("ppdResult");
    $xRayDate     = formatPostedDate("chestXrayOn");

    $isResult = isset($_POST["Result"])? "__" : "(X)";

    //health care facility information
    $facilityName = $_POST["facilityName"];

    $facilityPhone = $_POST["facilityPhone"];
    $facilityAddress = $_POST["facilityAddress"];

    //Healthcare Provider Information
    $providerName = $_POST["providerName"];
    $signedDate = (new DateTime())->format('m-d-Y');

    //--------------------  signature ---------------------
    $matches = glob(__DIR__ . '/uploads/foyetsignature.*');
    $logoPath = isset($matches[0]) ? $matches[0] : null;

    if ($logoPath && file_exists($logoPath)) {
        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($logoPath);
        $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $img = '<img src="' . $base64Logo . '" width="200" style="margin-right: 120px;">';
    } else {
        $img = '';
    }

    // Charger le template HTML
    $templatePath = __DIR__ . '/mestemplates/lasttemplate.html';
    if (!file_exists($templatePath)) {
        die('Template HTML introuvable');
    }
    $documentHtml = file_get_contents($templatePath);

    //Image logo
    $logoPath = __DIR__ . '/assets/logo-foyetnobg.png';
    if (file_exists($logoPath)) {
        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($logoPath);
        $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $logo = '<img src="' . $base64Logo . '" width="70" style="margin-right: 120px;">';
    } else {
        $logo = '';
    }

    // cachet ----------------------
    $matches = glob(__DIR__ . '/uploads/foyetcachet.*');
    $cachetPath = isset($matches[0]) ? $matches[0] : null;

    if ($cachetPath && file_exists($cachetPath)) {
        $type = pathinfo($cachetPath, PATHINFO_EXTENSION);
        $data = file_get_contents($cachetPath);
        $base64Cachet = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $cachet = '<img src="' . $base64Cachet . '" width="160" style="margin-left: 300px;">';
    } else {
        $cachet = '';
    }

    // 3. Remplacer les étiquettes {{...}} par les vraies données
    $html = str_replace(
        [ '{{logo}}' ,'{{patientName}}', '{{birthdate}}', '{{phone}}', '{{isResult}}' ,'{{sex}}','{{carePractitionerName}}','{{ppdPlantedOn}}','{{ppdReadOn}}','{{ppdResult}}',
            '{{xRayDate}}','{{facilityName}}', '{{facilityPhone}}','{{facilityAddress}}','{{providerName}}','{{signedDate}}','{{tmpFile}}', '{{cachet}}' ],
        [($logo),htmlspecialchars($patientName), htmlspecialchars($birthdate), htmlspecialchars($phone), htmlspecialchars($isResult) , htmlspecialchars($sex),
            htmlspecialchars($carePractitionerName), htmlspecialchars($ppdPlantedOn), htmlspecialchars($ppdReadOn),
            htmlspecialchars($ppdResult), htmlspecialchars($xRayDate), htmlspecialchars($facilityName), htmlspecialchars($facilityPhone),
            htmlspecialchars($facilityAddress), htmlspecialchars($providerName), htmlspecialchars($signedDate),
            ($img), ($cachet)],
        $documentHtml
    );

    // 4. Générer le PDF avec DOMPDF
    try {
        // Nettoyer le buffer de sortie avant de générer le PDF
        if (ob_get_level()) {
            ob_end_clean();
        }

        $dompdf = new Dompdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isPhpEnabled', true);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Headers pour forcer l'affichage dans le navigateur
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="certificate.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Accept-Ranges: bytes');

        // Optionnel : empêcher le téléchargement automatique par IDM
        header('Content-Description: File Transfer');
        header('X-Content-Type-Options: nosniff');

        // Sortir le PDF
        echo $dompdf->output();
        exit();

    } catch (Exception $e) {
        header('Content-Type: text/html; charset=utf-8');
        echo "Erreur lors de la génération du PDF : " . $e->getMessage();
        exit();
    }

} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Aucune donnée reçue"]);
    exit();
}

?>