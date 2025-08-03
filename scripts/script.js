// Basculer la visibilité du mot de passe
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentNode.querySelector('.toggle-password');

    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '🙈';
    } else {
        input.type = 'password';
        button.textContent = '👁️';
    }
}

// Vérification de la force du mot de passe
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = 'Très faible';

    // Critères de force
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]/)) strength += 1;
    if (password.match(/[A-Z]/)) strength += 1;
    if (password.match(/[0-9]/)) strength += 1;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

    // Déterminer le niveau et la couleur
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');

    if (!strengthBar || !strengthText) return;

    switch (strength) {
        case 0:
        case 1:
            strengthBar.style.width = '20%';
            strengthBar.style.background = '#e53e3e';
            feedback = 'Très faible';
            break;
        case 2:
            strengthBar.style.width = '40%';
            strengthBar.style.background = '#fd7f28';
            feedback = 'Faible';
            break;
        case 3:
            strengthBar.style.width = '60%';
            strengthBar.style.background = '#fbb040';
            feedback = 'Moyen';
            break;
        case 4:
            strengthBar.style.width = '80%';
            strengthBar.style.background = '#9ae65c';
            feedback = 'Fort';
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.style.background = '#38a169';
            feedback = 'Très fort';
            break;
    }

    strengthText.textContent = feedback;
}

// Validation en temps réel
function setupValidation() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm-password');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;

            if (confirm && password !== confirm) {
                this.style.borderColor = '#e53e3e';
                this.style.boxShadow = '0 0 0 3px rgba(229, 62, 62, 0.1)';
            } else {
                this.style.borderColor = '#e2e8f0';
                this.style.boxShadow = 'none';
            }
        });
    }
}

// Animation des cartes du dashboard
function animateCards() {
    const cards = document.querySelectorAll('.dashboard-card');

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Effet de particules sur le bouton de connexion
function addButtonEffects() {
    const buttons = document.querySelectorAll('.btn-primary');

    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Effet de ripple
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Gestion du thème sombre (bonus)
function setupThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle');

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            localStorage.setItem('darkTheme', document.body.classList.contains('dark-theme'));
        });

        // Charger le thème sauvegardé
        if (localStorage.getItem('darkTheme') === 'true') {
            document.body.classList.add('dark-theme');
        }
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    setupValidation();
    addButtonEffects();
    setupThemeToggle();

    // Animer les cartes du dashboard si présentes
    if (document.querySelector('.dashboard-card')) {
        animateCards();
    }

    // Ajouter les styles CSS pour l'effet ripple
    const style = document.createElement('style');
    style.textContent = `
          .btn-primary {
              position: relative;
              overflow: hidden;
          }
          
          .ripple {
              position: absolute;
              border-radius: 50%;
              background: rgba(255, 255, 255, 0.3);
              transform: scale(0);
              animation: ripple-animation 0.6s linear;
              pointer-events: none;
          }
          
          @keyframes ripple-animation {
              to {
                  transform: scale(4);
                  opacity: 0;
              }
          }
          
          .dark-theme {
              --bg-color: #1a202c;
              --text-color: #e2e8f0;
              --card-bg: #2d3748;
          }
      `;
    document.head.appendChild(style);
});

// Fonction utilitaire pour afficher des notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
          <span class="notification-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
          <span class="notification-message">${message}</span>
          <button class="notification-close" onclick="this.parentElement.remove()">×</button>
      `;

    // Ajouter les styles pour les notifications
    if (!document.querySelector('#notification-styles')) {
        const notificationStyles = document.createElement('style');
        notificationStyles.id = 'notification-styles';
        notificationStyles.textContent = `
              .notification {
                  position: fixed;
                  top: 20px;
                  right: 20px;
                  background: white;
                  padding: 15px 20px;
                  border-radius: 10px;
                  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                  display: flex;
                  align-items: center;
                  gap: 10px;
                  z-index: 1000;
                  animation: slideInRight 0.3s ease-out;
                  max-width: 400px;
              }
              
              .notification-success {
                  border-left: 4px solid #38a169;
              }
              
              .notification-error {
                  border-left: 4px solid #e53e3e;
              }
              
              .notification-info {
                  border-left: 4px solid #667eea;
              }
              
              .notification-close {
                  background: none;
                  border: none;
                  font-size: 1.2rem;
                  cursor: pointer;
                  color: #999;
                  margin-left: auto;
              }
              
              @keyframes slideInRight {
                  from {
                      transform: translateX(100%);
                      opacity: 0;
                  }
                  to {
                      transform: translateX(0);
                      opacity: 1;
                  }
              }
          `;
        document.head.appendChild(notificationStyles);
    }

    document.body.appendChild(notification);

    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}