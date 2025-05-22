<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Découvrez la musique autrement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="./assets/images/Logo.png" type="images/png">
    <link rel="stylesheet" href="assets/css/MusicWave.css">

</head>
<body>
<!-- Navigation simplifiée -->
<nav class="simple-nav navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-wave-square me-2"></i>WaveMusic
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#features">Fonctionnalités</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#trending">Tendances</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#genres">Genres</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a href="login_index.php" class="btn btn-gradient">Connexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content animate__animated animate__fadeIn">
                <h1 class="hero-title">Votre voyage musical commence ici</h1>
                <p class="hero-subtitle">Découvrez des millions de titres, créez des playlists personnalisées et partagez vos coups de cœur avec la communauté WaveMusic.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="register_index.php" class="btn btn-gradient btn-lg">
                        <i class="fas fa-user-plus me-2"></i> S'inscrire gratuitement
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play-circle me-2"></i> Découvrir
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block animate__animated animate__fadeIn animate__delay-1s">
                <img src="assets/images/Logo.png" alt="Écoute de musique" class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>

    <!-- Visualiseur audio stylisé -->
    <div class="music-visualizer">
        <?php for($i=0; $i<50; $i++): ?>
            <div class="bar" style="--i: <?= $i % 10 ?>; height: <?= rand(20, 80) ?>px;"></div>
        <?php endfor; ?>
    </div>


</section>

<!-- Features Section -->
<section id="features" class="py-5" style="background: var(--darker);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="section-title">Une expérience musicale unique</h2>
            <p class="lead opacity-75">Découvrez ce qui rend WaveMusic spécial</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headphones"></i>
                    </div>
                    <h3>Écoute illimitée</h3>
                    <p>Accédez à notre catalogue de millions de titres sans publicité interrompant votre session d'écoute.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Multiplateforme</h3>
                    <p>Passez de votre ordinateur à votre smartphone sans perdre le fil de votre musique.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Communauté active</h3>
                    <p>Partagez vos playlists et découvrez celles créées par d'autres passionnés de musique.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tendance du moment Section -->
<section id="trending" class="py-5" style="background: linear-gradient(to bottom, var(--darker) 0%, rgba(15,15,15,0.1) 100%);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="section-title">Découvrez les tendances</h2>
            <p class="lead opacity-75">Ce que la communauté écoute en ce moment</p>
        </div>

        <div class="row g-4">
            <?php
            $trending = [
                ['title' => 'Nouveaux Releases', 'artist' => 'Divers artistes', 'img' => 'https://source.unsplash.com/random/300x300/?music,album,1'],
                ['title' => 'Top 2023', 'artist' => 'Classement global', 'img' => 'https://source.unsplash.com/random/300x300/?music,album,2'],
                ['title' => 'Chill Vibes', 'artist' => 'WaveMusic Editorial', 'img' => 'https://source.unsplash.com/random/300x300/?music,album,3'],
                ['title' => 'Workout Energy', 'artist' => 'WaveMusic Editorial', 'img' => 'https://source.unsplash.com/random/300x300/?music,album,4'],
                ['title' => 'Indie Discoveries', 'artist' => 'Nouveaux talents', 'img' => 'https://source.unsplash.com/random/300x300/?music,album,5']
            ];

            foreach($trending as $item):
                ?>
                <div class="col-md-4 col-lg-2 col-6">
                    <div class="text-center">
                        <img src="<?= $item['img'] ?>" alt="<?= $item['title'] ?>" class="album-artwork img-fluid mb-3">
                        <h6 class="mb-1"><?= $item['title'] ?></h6>
                        <small class="text-muted"><?= $item['artist'] ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Genres Section -->
<section id="genres" class="py-5" style="background: var(--darker);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="section-title">Explorez par genre</h2>
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

<!-- Temoignage Section -->
<section class="py-5" style="background: linear-gradient(to bottom, rgba(15,15,15,0.1) 0%, var(--darker) 100%);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="section-title">Ce qu'ils disent de nous</h2>
            <p class="lead opacity-75">Rejoignez notre communauté de passionnés</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Marie D." class="testimonial-avatar me-3">
                        <div>
                            <h5 class="mb-0">Marie D.</h5>
                            <small class="text-muted">Utilisatrice depuis 2021</small>
                        </div>
                    </div>
                    <p>"La qualité audio est exceptionnelle, et les recommandations sont toujours pertinentes. Je découvre de nouveaux artistes chaque semaine!"</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Thomas L." class="testimonial-avatar me-3">
                        <div>
                            <h5 class="mb-0">Thomas L.</h5>
                            <small class="text-muted">Artiste indépendant</small>
                        </div>
                    </div>
                    <p>"En tant qu'artiste émergent, WaveMusic m'a offert une plateforme incroyable pour partager ma musique avec un public engagé."</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sophie T." class="testimonial-avatar me-3">
                        <div>
                            <h5 class="mb-0">Sophie T.</h5>
                            <small class="text-muted">DJ</small>
                        </div>
                    </div>
                    <p>"Les fonctionnalités de création de playlists et l'accès hors ligne ont révolutionné ma façon de préparer mes sets. Indispensable!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: var(--gradient-dark);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center text-white">
                <h2 class="display-5 mb-4">Prêt à commencer votre voyage musical?</h2>
                <p class="lead mb-5">Inscrivez-vous maintenant et obtenez 1 mois d'essai gratuit de WaveMusic Premium.</p>
                <a href="register_index.php" class="btn btn-light btn-lg px-5 py-3">
                    <i class="fas fa-play me-2"></i> Essai gratuit
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h3 class="mb-4"><i class="fas fa-wave-square me-2"></i>WaveMusic</h3>
                <p>La plateforme musicale qui vous emmène au cœur du son. Découvrez, partagez, vibrez.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="mb-4">WaveMusic</h5>
                <div class="footer-links">
                    <a href="#">À propos</a>
                    <a href="#">Carrières</a>
                    <a href="#">Fonctionnalités</a>
                    <a href="#">Premium</a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="mb-4">Communauté</h5>
                <div class="footer-links">
                    <a href="#">Artistes</a>
                    <a href="#">Développeurs</a>
                    <a href="#">Blog</a>
                    <a href="#">Événements</a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="mb-4">Légal</h5>
                <div class="footer-links">
                    <a href="#">Conditions</a>
                    <a href="#">Confidentialité</a>
                    <a href="#">Cookies</a>
                    <a href="#">RGPD</a>
                </div>
            </div>

            <div class="col-lg-2">
                <h5 class="mb-4">Télécharger</h5>
                <div class="d-flex flex-column gap-2">
                    <a href="#" class="btn btn-dark btn-sm">
                        <i class="fab fa-apple me-2"></i> iOS
                    </a>
                    <a href="#" class="btn btn-dark btn-sm">
                        <i class="fab fa-android me-2"></i> Android
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-5 opacity-10">

        <div class="text-center">
            <p class="small text-muted">© 2025 WaveMusic. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src="../src/animation_index.js"></script>

</body>
</html>