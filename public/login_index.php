<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicWave - Connexion</title>
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
                    <a class="nav-link active" href="login_index.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Connexion
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register_index.php">
                        <i class="fas fa-user-plus me-1"></i>Inscription
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-auto py-5">
    <div class="login-container">
        <h2 class="text-center mb-4">
            <i class="fas fa-sign-in-alt me-2"></i>Connexion
        </h2>

        <form action="../src/login.php" method="post" id="loginForm">
            <div class="mb-4">
                <label for="identifier" class="form-label">
                    <i class="fas fa-envelope me-1"></i>Email ou nom d'utilisateur
                </label>
                <input type="text" class="form-control" id="identifier" name="identifier" required
                       placeholder="votre@email.com ou pseudonyme">
            </div>

            <div class="mb-4 position-relative">
                <label for="mot_de_passe" class="form-label">
                    <i class="fas fa-lock me-1"></i>Mot de passe
                </label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required
                       placeholder="••••••••">
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                </div>
                <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Mot de passe oublié ?</a>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>Se connecter
                </button>
            </div>

            <div class="divider">ou</div>

            <div class="social-login d-flex mb-4">
                <button type="button" class="btn btn-secondary me-2">
                    <i class="fab fa-google me-1"></i> Google
                </button>
                <button type="button" class="btn btn-secondary ms-2">
                    <i class="fab fa-apple me-1"></i> Apple
                </button>
            </div>

            <div class="text-center">
                <p>Nouveau sur WaveMusic ? <a href="register_index.php" style="color: var(--primary-color);">Créez un compte</a></p>
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
<script src="../src/login_animation.js">

</script>
</body>
</html>