<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Foyet-medical</title>
    <link rel="stylesheet" href="../styles/register.css">
</head>
<body>
<div class="container">
    <div class="register-wrapper">
        <div class="register-header">
            <div class="logo">
                <div class="logo-icon"><img src="../logo-foyetnobg.png" alt="" style="width: 50px; height:auto;"></div>
                <h1>Foyet-Medical</h1>
            </div>
            <p class="subtitle">Créez votre compte personnel</p>
        </div>

        <!-- Messages d'erreur et de succès -->
        <div class="alert alert-error" id="error-message">
            <span class="alert-icon">⚠️</span>
            <span class="alert-text"></span>
        </div>

        <div class="alert alert-success" id="success-message">
            <span class="alert-icon">✅</span>
            <span class="alert-text"></span>
        </div>

        <!-- Formulaire d'inscription -->
        <div class="form-container">
            <form method="POST" action="" id="register-form">
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
                        <input type="password" id="password" name="password" placeholder="••••••••" required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">👁️</button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <span class="strength-text" id="strength-text">Faible</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirmer le mot de passe</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔐</span>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm-password')">👁️</button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="terms" required>
                        <span class="checkmark"></span>
                        J'accepte les conditions d'utilisation
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <span class="btn-text">Créer mon compte</span>
                    <span class="btn-icon">→</span>
                </button>
            </form>
        </div>

        <div class="login-link">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</div>

<script src="../scripts/register.js"></script>
</body>
</html>