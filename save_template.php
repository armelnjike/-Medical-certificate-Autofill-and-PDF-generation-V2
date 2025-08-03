<?php

// Autoriser CORS si besoin
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

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

// Lire les données JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['templateHtml'])) {
    http_response_code(400);
    echo "Erreur : contenu HTML manquant.";
    exit;
}

$templateHtml = $data['templateHtml'];

// Sécurité basique : supprimer scripts dangereux
$templateHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $templateHtml);

// Enregistrer dans un fichier à la racine (par exemple : templates/default_template.html)
$path = __DIR__ . '/mestemplates/lasttemplate.html';
if (!file_exists(dirname($path))) {
    mkdir(dirname($path), 0755, true);
}

if (file_put_contents($path, $templateHtml)) {
    echo "Template enregistré avec succès.";
} else {
    http_response_code(500);
    echo "Erreur lors de l'enregistrement du template.";
}

?>