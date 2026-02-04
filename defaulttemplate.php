<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'server/database.php';
use Dompdf\Dompdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
// À mettre tout en haut de  defaulttemplate.php
header("Access-Control-Allow-Origin: *"); // pour test, sinon mets ton vrai domaine
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

session_start();

$inactivityLimit = 30*60*1000;

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Pas connecté => rediriger vers la page de login
    header('Location: administration/login.php');
    exit();
}

// Vérifie la durée d'inactivité
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactivityLimit) {
    // Session expirée
    session_unset();
    session_destroy();
    header('Location: administration/login.php');
    exit();
}
// Mise à jour de l'heure de dernière activité
$_SESSION['last_activity'] = time();


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
function generateRandomKey($length = 8) {
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= random_int(0, 9);
    }

    return $code;

}


// Gérer OPTIONS (pré-vol)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


//patient info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientName = $_POST["patientName"];
    $birthdate = formatPostedDate("birthDate");
    $phone = $_POST["phoneNumber"];
    $sex = $_POST["sex"];
    $street = $_POST["street"];

// Medical Tests & Examinations
    $carePractitionerName = $_POST["careP"];

    $ppdPlantedOn = formatPostedDate("ppdPlantedOn");
    $ppdReadOn    = formatPostedDate("ppdReadOn");
    $ppdResult    = isset($_POST["Result"])? "positive" : "negative";
    $xRayDate     = formatPostedDate("chestXrayOn");
    $ppdResultNegative = isset($_POST["Result"])? "( )" : "(X)";
    $ppdResultPositive = isset($_POST["Result"])? "(X)" : "( )";

    //$isResult = isset($_POST["Result"])? "__" : "(X)";

    // ----------------------------------
    // xRay  a t il passé le xray ?
    $xRayReadOn = "";
    $xRayNormal = $xRayabnormal  = "";
    // ppdResultNegative

    // ----------------------------------
    $xRay = "( )";
if(isset($_POST["xRay"])){
    $xRay = "(X)";
    $xRayReadOn = formatPostedDate("xRayReadOn");
    $isResult = (isset($_POST["xRayResult"]) or isset($_POST["Result"]) )? "__" : "(X)";
    $xRayResult = isset($_POST["xRayResult"])?"()" : "(X)";
    $xRayNormal = isset($_POST["xRayResult"])?"()" : "(X)";
    $xRayabnormal = isset($_POST["xRayResult"])?"(X)" : "()";

}



//health care facility information
    $facilityName = $_POST["facilityName"];
    $facilityPhone = $_POST["facilityPhone"];
    $facilityAddress = $_POST["facilityAddress"];

//Healthcare Provider Information

    $providerName = $_POST["providerName"];
    $signedDate = isset($_POST["newdate"])? formatPostedDate("newdate") : (new DateTime())->format('m-d-Y');

    // generation et stckage de la clé du QR

    $randomKey = generateRandomKey(8);

    try {
        $conn = getDbConnection();
        // Générer un ID automatiquement (AUTO_INCREMENT)
        $date = new DateTime('now');
        $date= $date->format('Y-m-d');
        $conn->query("INSERT INTO doc_verification (id_doc, owner, creation_date) VALUES ('$randomKey','$patientName', '$date')");
        $certId = $conn->insert_id;

    }catch (Exception $e){
        throw $e;

    }

    // QRcode
    $qrCode = new QrCode("http://foyetmedical.fagiciel.com/verification.php?id=".$randomKey);


    $qrCode->setSize(150);
    $qrCode->setMargin(10);


    $qrImage = $qrCode->writeDataUri();

    $QR = "<img src='" . $qrImage . "' alt='QR Code' style='position: absolute;top: 16cm;left: 14cm;width: 3cm;height: 3cm;'>";


    // No More Need the Uploaded Image
    //$logoPath = __DIR__ . '/assets/signqture-foyetnobg.png'; // chemin absolu
    $matches = glob(__DIR__ . '/uploads/foyetsignature.*');
    $logoPath = isset($matches[0]) ? $matches[0] : null;

    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $img = 'data:image/' . $type . ';base64,' . base64_encode($data);

    //$img = '<img src="' . $base64Logo . '" width="70" style= "margin-right : 120">';

    // Logo
    //Image logo
    $logoPath = __DIR__ . '/assets/logo-foyetnobg.png'; // chemin absolu
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
    //$logo = '<img src="' . $base64Logo . '" width="70" style= "margin-right : 120">';

    // ------------------  cachet  -------------------------
    // cachet ----------------------
    $matches = glob(__DIR__ . '/uploads/foyetcachet.*');
    $cachetPath = isset($matches[0]) ? $matches[0] : null;

    $type = pathinfo($cachetPath, PATHINFO_EXTENSION);
    $data = file_get_contents($cachetPath);
    $base64Cachet = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $cachet = '<img src="' . $base64Cachet . '" width="200" style= "margin-left : 300">';


}else{
    echo json_encode("pas de donnes recu");
    //$rien = "";
}
//-----------------------------------------------------------
$template = file_get_contents('montemplate.html');

// 3. Remplacer les étiquettes {{...}} par les vraies données
$html = str_replace(
    ['{{logo}}' ,'{{patientName}}', '{{birthdate}}', '{{phone}}','{{isResult}}', '{{sex}}','{{carePractitionerName}}','{{ppdPlantedOn}}','{{ppdReadOn}}','{{ppdResultPositive}}','{{ppdResultNegative}}',
'{{xRayDate}}','{{facilityName}}', '{{facilityPhone}}','{{facilityAddress}}','{{providerName}}','{{signedDate}}','{{QR}}','{{tmpFile}}', '{{cachet}}','{{xRay}}','{{xRayDate}}','{{xRayNormal}}','{{xRayabnormal}}'],
    [($base64Logo) ,htmlspecialchars($patientName), htmlspecialchars($birthdate), htmlspecialchars($phone),htmlspecialchars($isResult), htmlspecialchars($sex),
        htmlspecialchars($carePractitionerName), htmlspecialchars($ppdPlantedOn), htmlspecialchars($ppdReadOn),
        htmlspecialchars($ppdResultPositive), htmlspecialchars($ppdResultNegative), htmlspecialchars($xRayReadOn), htmlspecialchars($facilityName), htmlspecialchars($facilityPhone),
        htmlspecialchars($facilityAddress), htmlspecialchars($providerName), htmlspecialchars($signedDate),($QR),
        htmlspecialchars($img),($cachet), htmlspecialchars($xRay), htmlspecialchars($xRayReadOn), htmlspecialchars($xRayNormal), htmlspecialchars($xRayabnormal)],
    $template
);

// 4. Générer le PDF avec DOMPDF

$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream('fiche_utilisateur.pdf', ['Attachment' => false]); // false = affichage dans le navigateur

// Send PDF as a blob to the navigator
//header('Content-Type: application/pdf');


$dompdf->stream('certificate.pdf', ['Attachment' => false]);
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="document.pdf"');
header('Content-Length: ' . strlen($pdfOutput));
exit;


?>