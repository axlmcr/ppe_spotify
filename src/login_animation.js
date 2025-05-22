// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('mot_de_passe');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
});

// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const identifier = document.getElementById('identifier').value.trim();
    const password = document.getElementById('mot_de_passe').value.trim();

    if (!identifier || !password) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs');
    }
});

// Animate elements on load
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.querySelector('.form-label').style.color = 'var(--primary-color)';
        });
        input.addEventListener('blur', () => {
            input.parentElement.querySelector('.form-label').style.color = '';
        });
    });
});