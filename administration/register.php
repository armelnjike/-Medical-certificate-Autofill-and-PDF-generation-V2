<?php
session_start();

// Configuration de la base de données (à adapter selon vos besoins)
$host = 'localhost';
$dbname = 'login_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$error = '';
$success = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($nom) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format d\'email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            // Créer le nouvel utilisateur
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (?, ?, ?)");

            if ($stmt->execute([$nom, $email, $hashed_password])) {
                $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            } else {
                $error = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Système d'authentification</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo">
                <div class="logo-icon">🔐</div>
                <h1>SecureLogin</h1>
            </div>
            <p class="subtitle">Créez votre compte personnel</p>
        </div>

        <!-- Messages d'erreur et de succès -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nom">Nom complet</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom complet" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">👁️</button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text">Faible</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirmer le mot de passe</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="terms" required>
                        <span class="checkmark"></span>
                        J'accepte les <a href="#" target="_blank">conditions d'utilisation</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <span class="btn-text">Créer un compte</span>
                    <span class="btn-icon">→</span>
                </button>
            </form>
        </div>

        <div class="register-link">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>