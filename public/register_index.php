<?php
session_start();
// Générer le token CSRF une seule fois
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupérer les erreurs et anciennes valeurs
$errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicWave - Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="./assets/images/Logo.png" type="images/png">
    <link rel="stylesheet" href="assets/css/login_register.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-wave-square me-2"></i>WaveMusic
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login_index.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Connexion
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="register_index.php">
                        <i class="fas fa-user-plus me-1"></i>Inscription
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-auto py-5">
    <div class="register-container">
        <h2 class="text-center mb-4">
            <i class="fas fa-user-plus me-2"></i>Inscription
        </h2>

        <!-- Message d'erreur global -->
        <?php if (isset($errors['global'])): ?>
            <div class="alert alert-danger global-error">
                <?= htmlspecialchars($errors['global']) ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="post" action="../src/register.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Champ username -->
            <div class="mb-4">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                       id="username" name="username" required
                       value="<?= htmlspecialchars($old_input['username'] ?? '') ?>"
                       autocomplete="username">
                <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['username']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Champ email -->
            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-1"></i>Adresse email
                </label>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                       id="email" name="email" required
                       value="<?= htmlspecialchars($old_input['email'] ?? '') ?>"
                       placeholder="votre@email.com" autocomplete="email">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Champ mot de passe -->
            <div class="mb-4 position-relative">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password" class="form-control <?= isset($errors['mot_de_passe']) ? 'is-invalid' : '' ?>"
                       id="mot_de_passe" name="mot_de_passe" required
                       autocomplete="new-password">
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>

                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>

                <?php if (isset($errors['mot_de_passe'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['mot_de_passe']) ?></div>
                <?php endif; ?>

                <div class="password-requirements">
                    <small>Le mot de passe doit contenir :</small>
                    <ul>
                        <li id="req-length">Minimum 8 caractères</li>
                        <li id="req-uppercase">Au moins une majuscule</li>
                        <li id="req-number">Au moins un chiffre</li>
                    </ul>
                </div>
            </div>

            <!-- Champ confirmation mot de passe -->
            <div class="mb-4 position-relative">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock me-1"></i>Confirmer le mot de passe
                </label>

                <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                       id="confirm_password" name="confirm_password" required
                       placeholder="••••••••" autocomplete="new-password">
                <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Conditions d'utilisation -->
            <div class="form-check mb-4">
                <input class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>"
                       type="checkbox" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    J'accepte les <a href="#" style="color: var(--primary-color);">conditions d'utilisation</a>
                </label>
                
                <?php if (isset($errors['terms'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['terms']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Bouton d'inscription -->
            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-user-plus me-1"></i>S'inscrire
                </button>
            </div>

            <div class="divider">ou</div>

            <!-- Boutons de connexion sociale -->
            <div class="social-register d-flex mb-4">
                <button type="button" class="btn btn-secondary me-2">
                    <i class="fab fa-google me-1"></i> Google
                </button>
                <button type="button" class="btn btn-secondary ms-2">
                    <i class="fab fa-apple me-1"></i> Apple
                </button>
            </div>

            <!-- Lien vers la page de connexion -->
            <div class="text-center">
                <p>Déjà membre ? <a href="login_index.php" style="color: var(--primary-color);">Connectez-vous</a></p>
            </div>
        </form>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 WaveMusic. Tous droits réservés.</p>
        <p>
            <a href="#" class="text-decoration-none me-2" style="color: rgba(255, 255, 255, 0.6);">Confidentialité</a>
            <a href="#" class="text-decoration-none ms-2" style="color: rgba(255, 255, 255, 0.6);">Conditions</a>
        </p>
    </div>
</footer>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../src/register.js"> </script>
</body>
</html>