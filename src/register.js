document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const passwordInput = document.getElementById('mot_de_passe');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const passwordToggle = document.getElementById('togglePassword');
    const confirmPasswordToggle = document.getElementById('toggleConfirmPassword');
    const submitBtn = document.getElementById('submitBtn');

    // Fonctions utilitaires
    const showError = (fieldId, message) => {
        const field = document.getElementById(fieldId);
        field.classList.add('is-invalid');

        let errorElement = field.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }
        errorElement.textContent = message;
    };

    const clearError = (fieldId) => {
        const field = document.getElementById(fieldId);
        field.classList.remove('is-invalid');
        const errorElement = field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
    };

    const updateRequirement = (elementId, isValid) => {
        const element = document.getElementById(elementId);
        if (isValid) {
            element.classList.add('valid');
        } else {
            element.classList.remove('valid');
        }
    };

    // Validation en temps réel
    document.getElementById('username').addEventListener('input', function() {
        const username = this.value.trim();
        if (username.length > 0 && username.length < 3) {
            showError('username', '3 caractères minimum');
        } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            showError('username', 'Caractères spéciaux non autorisés');
        } else {
            clearError('username');
        }
    });

    document.getElementById('email').addEventListener('input', function() {
        const email = this.value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Email invalide');
        } else {
            clearError('email');
        }
    });

    // Gestion des mots de passe
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        updatePasswordStrength(password);
        validatePasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', validatePasswordMatch);

    function updatePasswordStrength(password) {
        let strength = 0;

        // Longueur minimale
        const hasMinLength = password.length >= 8;
        updateRequirement('req-length', hasMinLength);
        if (hasMinLength) strength += 30;

        // Majuscule
        const hasUppercase = /[A-Z]/.test(password);
        updateRequirement('req-uppercase', hasUppercase);
        if (hasUppercase) strength += 30;

        // Chiffre
        const hasNumber = /[0-9]/.test(password);
        updateRequirement('req-number', hasNumber);
        if (hasNumber) strength += 30;

        // Caractère spécial
        const hasSpecialChar = /[^A-Za-z0-9]/.test(password);
        if (hasSpecialChar) strength += 10;

        passwordStrengthBar.style.width = strength + '%';

        if (strength < 40) {
            passwordStrengthBar.style.backgroundColor = '#dc3545';
        } else if (strength < 80) {
            passwordStrengthBar.style.backgroundColor = '#ffc107';
        } else {
            passwordStrengthBar.style.backgroundColor = '#28a745';
        }
    }

    function validatePasswordMatch() {
        if (passwordInput.value !== confirmPasswordInput.value && confirmPasswordInput.value.length > 0) {
            showError('confirm_password', 'Les mots de passe ne correspondent pas');
        } else {
            clearError('confirm_password');
        }
    }

    // Basculer la visibilité du mot de passe
    [passwordToggle, confirmPasswordToggle].forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.setAttribute('aria-label', type === 'password' ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
        });
    });

    // Soumission du formulaire
    registerForm.addEventListener('submit', function(e) {
        let isValid = true;

        // Validation finale avant envoi
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const termsChecked = document.getElementById('terms').checked;

        // Réinitialiser les erreurs
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // Valider le nom d'utilisateur
        if (username.length < 3) {
            showError('username', '3 caractères minimum');
            isValid = false;
        } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            showError('username', 'Caractères spéciaux non autorisés');
            isValid = false;
        }

        // Valider l'email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Email invalide');
            isValid = false;
        }

        // Valider le mot de passe
        if (password.length < 8) {
            showError('mot_de_passe', '8 caractères minimum');
            isValid = false;
        } else if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
            showError('mot_de_passe', 'Majuscule et chiffre requis');
            isValid = false;
        }

        // Valider la confirmation
        if (password !== confirmPassword) {
            showError('confirm_password', 'Les mots de passe ne correspondent pas');
            isValid = false;
        }

        // Valider les conditions
        if (!termsChecked) {
            showError('terms', 'Vous devez accepter les conditions');
            isValid = false;
        }

        // Empêcher l'envoi si invalide
        if (!isValid) {
            e.preventDefault();
            // Scroll vers la première erreur
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Désactiver le bouton pendant l'envoi
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
        }
    });

    // Animation des labels au focus
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('.form-label').style.color = 'var(--primary-color)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.querySelector('.form-label').style.color = '';
        });
    });
});