// Fonction pour basculer la visibilité du mot de passe
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;

    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '🙈';
    } else {
        input.type = 'password';
        button.textContent = '👁️';
    }
}

// Fonction pour évaluer la force du mot de passe
function checkPasswordStrength(password) {
    let score = 0;
    let feedback = '';

    // Longueur
    if (password.length >= 8) score += 1;
    if (password.length >= 12) score += 1;

    // Complexité
    if (/[a-z]/.test(password)) score += 1;
    if (/[A-Z]/.test(password)) score += 1;
    if (/[0-9]/.test(password)) score += 1;
    if (/[^A-Za-z0-9]/.test(password)) score += 1;

    // Déterminer le niveau
    if (score <= 2) {
        feedback = 'Faible';
        return { strength: 'weak', feedback };
    } else if (score <= 4) {
        feedback = 'Moyen';
        return { strength: 'fair', feedback };
    } else if (score <= 5) {
        feedback = 'Bon';
        return { strength: 'good', feedback };
    } else {
        feedback = 'Fort';
        return { strength: 'strong', feedback };
    }
}

// Fonction pour valider l'email
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Fonction pour afficher les messages
function showMessage(type, message) {
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');

    if (type === 'error') {
        errorDiv.querySelector('.alert-text').textContent = message;
        errorDiv.style.display = 'flex';
        successDiv.style.display = 'none';
    } else {
        successDiv.querySelector('.alert-text').textContent = message;
        successDiv.style.display = 'flex';
        errorDiv.style.display = 'none';
    }

    // Faire défiler vers le haut pour voir le message
    document.querySelector('.register-wrapper').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Fonction pour masquer les messages
function hideMessages() {
    document.getElementById('error-message').style.display = 'none';
    document.getElementById('success-message').style.display = 'none';
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const emailInput = document.getElementById('email');
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');

    // Masquer les messages au démarrage
    hideMessages();

    // Validation en temps réel du mot de passe
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const result = checkPasswordStrength(password);

        // Mettre à jour l'indicateur visuel
        strengthFill.className = `strength-fill ${result.strength}`;
        strengthText.textContent = result.feedback;

        // Vérifier la correspondance avec la confirmation
        if (confirmPasswordInput.value && confirmPasswordInput.value !== password) {
            confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas !!! JSSSS');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });

    // Validation de la confirmation du mot de passe
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value !== passwordInput.value) {
            this.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validation de l'email
    emailInput.addEventListener('blur', function() {
        if (this.value && !validateEmail(this.value)) {
            this.setCustomValidity('Veuillez entrer une adresse email valide');
        } else {
            this.setCustomValidity('');
        }
    });

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');
        const email = formData.get('email');
        const terms = formData.get('terms');

        // Validation côté client
        if (!validateEmail(email)) {
            showMessage('error', 'Veuillez entrer une adresse email valide');
            return;
        }

        if (password.length < 6) {
            showMessage('error', 'Le mot de passe doit contenir au moins 6 caractères');
            return;
        }

        if (password !== confirmPassword) {
            showMessage('error', 'Les mots de passe ne correspondent pas');
            return;
        }

        if (!terms) {
            showMessage('error', 'Vous devez accepter les conditions d\'utilisation');
            return;
        }

        // Désactiver le bouton pendant la soumission
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.querySelector('.btn-text').textContent;
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text').textContent = 'Création en cours...';

        // Envoyer les données au serveur
        fetch("../server/registerServ.php", {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    form.reset();
                    hideMessages();

                    // Redirection après un délai
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showMessage('error', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('error', 'Une erreur réseau s\'est produite. Veuillez réessayer.JS');
            })
            .finally(() => {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.querySelector('.btn-text').textContent = originalText;
        });
    });
});