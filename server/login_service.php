<?php
session_start();
require_once '../server/database.php';

header('Content-Type: application/json');

// Vérifier la requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

// Sécuriser les entrées
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs.']);
    exit;
}

try {
    $con = getDbConnection();
    $req = $con->prepare("SELECT id, nom, password, email FROM users WHERE email = ?");
    $req->bind_param('s', $email);
    $req->execute();
    $result = $req->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['last_activity'] = time();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.'.$user['email'].' mot de passe : '.$user['password']]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}
?>
