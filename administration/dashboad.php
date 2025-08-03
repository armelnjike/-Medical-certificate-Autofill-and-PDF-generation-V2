<?php
session_start();

// Vérifier si l'utilisateur est connecté
/*
if (!isset($_SESSION['user_id'])) {
    header('Location: login_service.php');
    exit;
}
*/
// Traitement de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login_service.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - SecureLogin</title>
    <link rel="stylesheet" href="../styles/login.css   ">
</head>
<body>
<div class="dashboard">
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">🔐</div>
                <h1>SecureLogin</h1>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <span class="user-avatar">👤</span>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_nom']); ?></span>
                        <span class="user-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                    </div>
                </div>
                <a href="?logout=1" class="logout-btn">
                    <span>Déconnexion</span>
                    <span class="logout-icon">🚪</span>
                </a>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="welcome-section">
            <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_nom']); ?> !</h2>
            <p>Vous êtes maintenant connecté à votre espace personnel.</p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon">📊</div>
                <h3>Statistiques</h3>
                <p>Consultez vos données et statistiques personnelles.</p>
                <button class="card-btn">Voir plus</button>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">⚙️</div>
                <h3>Paramètres</h3>
                <p>Gérez vos préférences et paramètres de compte.</p>
                <button class="card-btn">Configurer</button>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">📁</div>
                <h3>Mes fichiers</h3>
                <p>Accédez à vos documents et fichiers personnels.</p>
                <button class="card-btn">Parcourir</button>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">💬</div>
                <h3>Messages</h3>
                <p>Consultez vos messages et notifications.</p>
                <button class="card-btn">Lire</button>
            </div>
        </div>

        <div class="session-info">
            <h3>Informations de session</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>ID utilisateur:</strong>
                    <span><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                </div>
                <div class="info-item">
                    <strong>Connexion:</strong>
                    <span><?php echo date('d/m/Y H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>