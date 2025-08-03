<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Foyet-medical</title>
    <link rel="stylesheet" href="../styles/login.css">
</head>
<body>
<div class="container">
    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo">
                <div class="logo-icon">🔐</div>
                <h1>Foyet-Medical</h1>
            </div>
            <p class="subtitle">Accédez à votre espace personnel</p>
        </div>

        <!-- Messages d'erreur et de succès -->
            <div class="alert alert-error" id="error-message">
                <span class="alert-icon">⚠️</span>

            </div>

            <div class="alert alert-success" id="sucess-message">
                <span class="alert-icon">✅</span>

            </div>

        <!-- Formulaire de connexion -->
        <div class="form-container">
            <form method="POST" action="" id="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">👁️</button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Se souvenir de moi
                    </label>
                   <!-- <a href="#" class="forgot-password">Mot de passe oublié ?</a>  -->
                </div>

                <button type="submit" class="btn btn-primary">
                    <span class="btn-text">Se connecter</span>
                    <span class="btn-icon">→</span>
                </button>
            </form>

            <div class="demo-info" id="demo-info">
                <h4>Comptes de démonstration :</h4>
                <p><strong>Admin:</strong> admin@example.com / admin123</p>
                <p><strong>User:</strong> user@example.com / user123</p>
            </div>
        </div>

        <div class="register-link" id="register-link">
            <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
        </div>
    </div>
</div>

<script src="../scripts/script.js"></script>
<script>
    let registerLink = document.getElementById('register-link');
    registerLink.setAttribute('style', 'display: none;');
    let demoInfo = document.getElementById('demo-info');
    demoInfo.setAttribute('style', 'display: none;');
    let erDiv = document.getElementById('error-message');
    let suDiv = document.getElementById('sucess-message');
    erDiv.setAttribute('style', 'display: none;');
    suDiv.setAttribute('style', 'display: none;');
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('../server/login_service.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                const messageDiv = document.getElementById('message');
                if (data.success) {
                    suDiv.setAttribute('style', 'display: block;');
                    window.location.href = '../index.php';
                } else {
                    erDiv.setAttribute('style', 'display: block;');
                    erDiv.innerHTML = `❌ ${data.message}`;
                }
            })
            .catch(() => {
                erDiv.setAttribute('style', 'display: block;');
                erDiv.innerHTML =  '<div class="alert alert-error">Erreur réseau.</div>';
            });
    });
</script>
</body>
</html>