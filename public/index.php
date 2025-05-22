
<?php
// Démarrer la session avec des paramètres sécurisés
session_start([
    'cookie_lifetime' => 86400, // 1 jour
    'cookie_secure'   => true,  // En production seulement avec HTTPS
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: Acceuil.php");
    exit();
}

// Inclure la configuration de la base de données
require_once("../config/db.php");

// Récupérer l'ID utilisateur de manière sécurisée
$user_id = (int)$_SESSION['user_id'];

// Gestion du changement de thème
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_theme'])) {
    try {
        // Récupérer le thème actuel
        $stmt = $pdo->prepare("SELECT theme FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $current_theme = $stmt->fetchColumn();

        // Basculer entre les thèmes
        $new_theme = ($current_theme === 'dark') ? 'light' : 'dark';

        // Mettre à jour le thème
        $update_stmt = $pdo->prepare("UPDATE users SET theme = ? WHERE user_id = ?");
        $update_stmt->execute([$new_theme, $user_id]);

        // Mettre à jour la session et recharger
        $_SESSION['theme'] = $new_theme;
        header("Location: ".filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL));
        exit();
    } catch (PDOException $e) {
        error_log("Erreur de changement de thème: ".$e->getMessage());
        // Gérer l'erreur sans interrompre l'expérience utilisateur
    }
}

// Récupérer les informations de l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT username, theme FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Nettoyage complet si l'utilisateur n'existe pas
        session_unset();
        session_destroy();
        header("Location: login_index.php");
        exit();
    }

    // Déterminer le thème (avec valeur par défaut)
    $theme = isset($user['theme']) && in_array($user['theme'], ['dark', 'light'])
        ? $user['theme']
        : 'dark';

    // Déterminer l'icône du thème (avec vérification de fichier)
    $theme_icon = ($theme === 'dark') ? 'assets/images/lune.png' : 'assets/images/soleil.png';
    $user_avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.jpg';

} catch (PDOException $e) {
    error_log("Erreur de base de données: ".$e->getMessage());
    // Valeurs par défaut en cas d'erreur
    $theme = 'dark';
    $theme_icon = 'assets/images/lune.png';
    $user_avatar = 'assets/images/default-avatar.jpg';
    $user = ['username' => 'Invité'];
}

// Protection XSS pour les données affichées
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr" ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Découvrez la musique autrement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="./assets/images/Logo.png" type="images/png">
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">

</head>
<body>

    <?php
    $show_search = false;
    include './assets/composents/navbar.php';
    include './assets/composents/result_audioPlayer.php'; ?>
    
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content animate__animated animate__fadeIn">
                <h1 class="hero-title mb-4">Plongez dans l'océan musical</h1>
                <p class="hero-subtitle mb-5">Découvrez, écoutez et partagez vos titres préférés parmi des millions de morceaux.</p>
            </div>
            <div class="col-lg-6 d-none d-lg-block animate__animated animate__fadeIn animate__delay-1s">
                <img src="assets/images/Logo.png" alt="Appareils musicaux" class="img-fluid">
            </div>
        </div>
    </div>

    <!-- Visualiseur audio décoratif -->
    <div class="music-visualizer">
        <?php for($i=0; $i<50; $i++): ?>
            <div class="bar" style="--i: <?= $i % 10 ?>; height: <?= rand(20, 80) ?>px;"></div>
        <?php endfor; ?>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-4 mb-3">Pourquoi choisir WaveMusic</h2>
            <p class="lead opacity-75">Une expérience musicale inégalée</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card p-4 h-100 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Bibliothèque illimitée</h3>
                    <p>Accédez à des millions de titres, des nouveaux releases aux classiques intemporels.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card p-4 h-100 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <h3>Son haute qualité</h3>
                    <p>Profitez d'un son haute résolution avec nos algorithmes d'amélioration audio exclusifs.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card p-4 h-100 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3>Recommandations intelligentes</h3>
                    <p>Notre IA apprend vos goûts pour vous suggérer des morceaux qui vous correspondent.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tendances du moment Section -->
<section class="trending-now py-5">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="display-5">Tendances du moment</h2>
            <a href="#" class="btn btn-outline-light">Voir plus</a>
        </div>

        <div class="row g-4">
            <?php for($i=0; $i<5; $i++): ?>
                <div class="col-md">
                    <div class="album-card text-center">
                        <img src="assets/images/0x1900-000000-80-0-0.jpg" alt="Album trending" class="album-artwork img-fluid mb-3">
                        <h5>Titre populaire <?= $i+1 ?></h5>
                        <p class="text-muted">Artiste <?= $i+1 ?></p>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Genres Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="se">Explorez par genre</h2>
            <p class="lead opacity-75">Trouvez la musique qui correspond à votre humeur</p>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <?php
            $genresImages = [
                'Pop' => 'pop.jpg',
                'Rock' => 'Rock.jpg',
                'Hip-Hop' => 'hiphop.jpg',
                'Electro' => 'electro.jpg',
                'Jazz' => 'jazz.png',
                'Classique' => 'classique.jpg',
                'R&B' => 'rnb.jpg',
                'Rap' => 'rap.jpg',
                'Reggae' => 'reggea.jpg',
                'Country' => 'country.jpg'
            ];

            foreach($genresImages as $genre => $image):?>
                <div class="genre-chip">
                    <img src="assets/images/<?= $image ?>" alt="<?= $genre ?>" class="genre-icon">
                    <?= $genre ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 bg-dark">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 mb-4">Restez connecté</h2>
                <p class="lead mb-5">Abonnez-vous à notre newsletter pour recevoir les dernières nouveautés et offres exclusives.</p>

                <form class="d-flex gap-2">
                    <input type="email" class="form-control newsletter-input flex-grow-1" placeholder="Votre email">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane me-2"></i> S'abonner
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-5">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <h3 class="mb-4"><i class="fas fa-wave-square me-2"></i>WaveMusic</h3>
                <p>La plateforme musicale qui vous emmène au cœur du son. Découvrez, partagez, vibrez.</p>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="mb-4">Navigation</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-decoration-none">Accueil</a></li>
                    <li class="mb-2"><a href="ma_Bibliotheque_index.php" class="text-decoration-none">Bibliothèque</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Premium</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="mb-4">Légal</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="legal.php" class="text-decoration-none">Conditions</a></li>
                    <li class="mb-2"><a href="legal.php#confidentialite" class="text-decoration-none">Confidentialité</a></li>
                    <li class="mb-2"><a href="legal.php#cookies" class="text-decoration-none">Cookies</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-4">
                <h5 class="mb-4">Réseaux sociaux</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="text-decoration-none fs-4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-decoration-none fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-decoration-none fs-4"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-decoration-none fs-4"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="text-decoration-none fs-4"><i class="fab fa-spotify"></i></a>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-10">

        <div class="text-center">
            <p class="small text-muted">© 2023 WaveMusic. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src = "../src/index.js"></script>
</body>
</html>