<?php
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


// Gérer OPTIONS (pré-vol)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;
 //----------- provisoire -------------
/*
$patientName = "patientName";
$birthdate = "birthDate";
$phone = "phoneNumber";
$sex = "sex";
$street = "street";

// Medical Tests & Examinations
$carePractitionerName = "careP";
$ppdPlantedOn = "ppdPlantedOn";
$ppdReadOn = "ppdReadOn";
$ppdResult = "ppdResult";
$xRayDate = "chestXrayOn";

//health care facility information
$facilityName ="facilityName";
$facilityPhone = "facilityPhone";
$facilityAddress = "facilityAddress";

//Healthcare Provider Information

$providerName = "providerName";
$signedDate = '2025-12-02';
//---------------------------------------------------------

*/

//patient info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientName = $_POST["patientName"];
    $birthdate = $_POST["birthDate"];
    $phone = $_POST["phoneNumber"];
    $sex = $_POST["sex"];
    $street = $_POST["street"];

// Medical Tests & Examinations
    $carePractitionerName = $_POST["careP"];
    $ppdPlantedOn = $_POST["ppdPlantedOn"];
    $ppdReadOn = $_POST["ppdReadOn"];
    $ppdResult = $_POST["ppdResult"];
    $xRayDate = $_POST["chestXrayOn"];

//health care facility information
    $facilityName = $_POST["facilityName"];
    $facilityPhone = $_POST["facilityPhone"];
    $facilityAddress = $_POST["facilityAddress"];

//Healthcare Provider Information

    $providerName = $_POST["providerName"];
    $signedDate = (new DateTime())->format('Y-m-d');

// Print the image to the pdf

    /*
    $dataURL = $_POST['signature'];
    $parts = explode(',', $dataURL);
    $base64 = end($parts);

    // Decode the base64 string
    $imageData = base64_decode($base64);

    // Create temporary image file
    $tmpFile = tempnam(sys_get_temp_dir(), 'sig') . '.png';
    file_put_contents($tmpFile, $imageData);
    $img = 'data:image/png;base64,' . base64_encode($imageData);
    */

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
    ['{{logo}}' ,'{{patientName}}', '{{birthdate}}', '{{phone}}', '{{sex}}','{{carePractitionerName}}','{{ppdPlantedOn}}','{{ppdReadOn}}','{{ppdResult}}',
'{{xRayDate}}','{{facilityName}}', '{{facilityPhone}}','{{facilityAddress}}','{{providerName}}','{{signedDate}}','{{tmpFile}}', '{{cachet}}'],
    [($base64Logo) ,htmlspecialchars($patientName), htmlspecialchars($birthdate), htmlspecialchars($phone), htmlspecialchars($sex),
        htmlspecialchars($carePractitionerName), htmlspecialchars($ppdPlantedOn), htmlspecialchars($ppdReadOn),
        htmlspecialchars($ppdResult), htmlspecialchars($xRayDate), htmlspecialchars($facilityName), htmlspecialchars($facilityPhone),
        htmlspecialchars($facilityAddress), htmlspecialchars($providerName), htmlspecialchars($signedDate),
        htmlspecialchars($img),($cachet)],
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
header('Content-Disposition: inline; filename="certificate.pdf"');

$dompdf->stream('certificate.pdf', ['Attachment' => false]);
exit;


?>