<?php
header('Content-Type: application/json');

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

$response = [];

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/foyet-medical/uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['photo_identite']) && isset($_FILES['certificat_medical'])) {

    // Map fixed filenames to the form input fields
    $fileMappings = [
        'photo_identite' => 'foyetlogo',
        'certificat_medical' => 'foyetcachet',
    ];


    foreach ($fileMappings as $field => $fixedName) {
        $fileTmp = $_FILES[$field]['tmp_name'];

        // Get original extension (preserve correct file type)
        $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

        // Build final target path with fixed name + original extension
        $targetPath = $uploadDir . $fixedName . '.' . $extension;

        // Supprimer l'ancien fichier s'il existe, quelle que soit l'extension
        $oldFiles = glob($uploadDir . $fixedName . '.*');
        foreach ($oldFiles as $oldFile) {
            unlink($oldFile);
        }

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $response[$field] = [
                'status' => 'success',
                'filename' => $fixedName . '.' . $extension,
                'path' => __DIR__ . '/uploads/' . $fixedName . '.' . $extension
            ];
        } else {
            http_response_code(500);
            $response[$field] = [
                'status' => 'error',
                'message' => 'Erreur lors du téléchargement.'
            ];
        }
    }

    echo json_encode($response);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Fichiers manquants.']);
}
?>
