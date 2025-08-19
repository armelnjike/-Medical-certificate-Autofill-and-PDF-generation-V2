<?php
session_start();
require_once '../server/database.php';

header('Content-Type: application/json');

// Vérifier la requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}
$error = '';
$success = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = "User";
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($nom) || empty($email) || empty($password) || empty($confirm_password)) {
        echo json_encode( ['success' =>false, 'message' =>'Veuillez remplir tous les champs.']);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' =>false, 'message' =>'Format d\'email invalide.']);
    } elseif (strlen($password) < 6) {
        echo json_encode(['success' =>false, 'message' =>'Le mot de passe doit contenir au moins 6 caractères.']);
    } elseif ($password !== $confirm_password) {
        echo json_encode(['success' =>false, 'message' =>'Les mots de passe ne correspondent pas.']);
    } else {
        // Vérifier si l'email existe déjà
        $con = getDbConnection();
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo json_encode(['success' =>false, 'message' =>'Cet email est déjà utilisé.']);
        } else {
            // Créer le nouvel utilisateur
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO users (nom, email, password) VALUES (?, ?, ?)");

            if ($stmt->execute([$nom, $email, $hashed_password])) {
                echo json_encode(['success' =>true, 'message' =>'Inscription réussie ! Vous pouvez maintenant vous connecter.']);
            } else {
                echo json_encode(['success' =>false, 'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.']);
            }
        }
    }
}
?>