<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../server/database.php';
use Dompdf\Dompdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

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
            return '';
        }
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function generateRandomKey($length = 8) {
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= random_int(0, 9);
    }

    return $code;

}

//patient info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //checkbox
    if (isset($_POST['adult'])) {
        $adult = "<input type='checkbox' checked>";
    }else{
        $adult = "<input type='checkbox'>";
    }
    if (isset($_POST['adultCI'])) {
        $adultCI = "<input type='checkbox' checked>";
    }else{
        $adultCI = "<input type='checkbox'>";
    }
    if (isset($_POST['adultC'])) {
        $adultC = "<input type='checkbox' checked>";
    }else{
        $adultC = "<input type='checkbox'>";
    }
    if (isset($_POST['adultI'])) {
        $adultI = "<input type='checkbox' checked>";
    }else{
        $adultI = "<input type='checkbox'>";
    }

    $registryN = $_POST["RegistryN"];
    $studentName = $_POST["studentName"];
    $instructorName = $_POST["instructorName"];
    $CompletionDate = formatPostedDate("CompletionDate");
    $ExpirationDate    = formatPostedDate("ExpirationDate");
    $TrainingCenterPhone = $_POST["TrainingCenterPhone"];
    $TrainingCenterId = $_POST["TrainingCenterId"];
//----------------------------------------------------------------------------
    $date = new DateTime('now');
    $date= $date->format('Y-m-d');
    $conn = getDbConnection();

    $randomKey = generateRandomKey(8);

    try {
        // Générer un ID automatiquement (AUTO_INCREMENT)
        $conn->query("INSERT INTO doc_verification (id_doc, owner, creation_date) VALUES ('$randomKey','$studentName', '$date')");
        $certId = $conn->insert_id;

    }catch (Exception $e){
        throw $e;

    }


    // No More Need the Uploaded Image
    // chemin absolu
    $matches = glob(__DIR__ . '/uploads/foyetsignature.*');
    $logoPath = isset($matches[0]) ? $matches[0] : null;

    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $signature = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $signature = '<img src="' . $signature . '" width="140" style= "margin-right : 120">';

    //Image logo
    $logoPath = __DIR__ . '/assets/logo_hsi2.png'; // chemin absolu
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $logo = '<img src="' . $base64Logo . '"  style= "margin-right : 120;width: 100%; height:auto ">';

    // ------------------  cachet  -------------------------
    $matches = glob(__DIR__ . '/uploads/foyetcachet.*');
    $cachetPath = isset($matches[0]) ? $matches[0] : null;

    $type = pathinfo($cachetPath, PATHINFO_EXTENSION);
    $data = file_get_contents($cachetPath);
    $base64Cachet = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $cachet = '<img src="' . $base64Cachet . '" width="200" style= "margin-left : 300">';

    /********************************************************************/

    $qrCode = new QrCode("http://foyetmedical.fagiciel.com/verification.php?id=".$randomKey);


    $qrCode->setSize(150);
    $qrCode->setMargin(10);


    $qrImage = $qrCode->writeDataUri();

    $QR = "<img src='" . $qrImage . "' alt='QR Code' style='width:100px; height:100px;'>";



}else{
    echo json_encode("pas de donnes recu");
    //$rien = "";
}
//-----------------------------------------------------------
$template = file_get_contents('templatetab.html');


$html = str_replace(
    ['{{logo}}' ,'{{checkbox1}}', '{{checkbox2}}', '{{checkbox3}}', '{{checkbox4}}','{{instructorName}}','{{deputyNo}}','{{completionDate}}','{{expirationDate}}',
'{{trainingPhoneNumber}}','{{trainingCenterId}}', '{{studentName}}','{{instructorSignature}}', '{{cachet}}','{{QR}}'],
    [($logo) ,($adult), ($adultCI), ($adultC), ($adultI),
        htmlspecialchars($instructorName), htmlspecialchars($registryN), htmlspecialchars($CompletionDate),htmlspecialchars($ExpirationDate),
        htmlspecialchars($TrainingCenterPhone), htmlspecialchars($TrainingCenterId), htmlspecialchars($studentName), ($signature),($cachet),
         ($QR)],

    $template
);

// 4. Générer le PDF avec DOMPDF

$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream('fiche_utilisateur.pdf', ['Attachment' => false]); // false = affichage dans le navigateur



exit;


?>