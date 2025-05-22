<?php
// Start the session with secure parameters
session_start([
    'cookie_lifetime' => 86400, // 1 day
    'cookie_secure'   => true,  // Only in production with HTTPS
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.php");
    exit();
}

// Include the database configuration
require_once("../config/db.php");

// Retrieve the user ID securely
$user_id = (int)$_SESSION['user_id'];

// Handle theme change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_theme'])) {
    try {
        // Get the current theme
        $stmt = $pdo->prepare("SELECT theme FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $current_theme = $stmt->fetchColumn();

        // Toggle between themes
        $new_theme = ($current_theme === 'dark') ? 'light' : 'dark';

        // Update the theme
        $update_stmt = $pdo->prepare("UPDATE users SET theme = ? WHERE user_id = ?");
        $update_stmt->execute([$new_theme, $user_id]);

        // Update the session and reload
        $_SESSION['theme'] = $new_theme;
        header("Location: ".filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL));
        exit();
    } catch (PDOException $e) {
        error_log("Error changing theme: ".$e->getMessage());
        // Handle the error without disrupting the user experience
    }
}

// Retrieve the user's playlists
try {
    $playlists_stmt = $pdo->prepare("
        SELECT p.playlist_id, p.nom, COUNT(ps.song_id) as nombre_morceaux 
        FROM Playlists p
        LEFT JOIN Playlist_songs ps ON p.playlist_id = ps.playlist_id
        WHERE p.createur_id = ?
        GROUP BY p.playlist_id
        ORDER BY p.date_creation DESC
        LIMIT 5
    ");
    $playlists_stmt->execute([$user_id]);
    $playlists = $playlists_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error retrieving playlists: ".$e->getMessage());
    $playlists = [];
}

// Retrieve user information
try {
    $stmt = $pdo->prepare("SELECT username, theme FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Fully clean up if the user does not exist
        session_unset();
        session_destroy();
        header("Location: login_index.php");
        exit();
    }

    // Determine the theme (with default value)
    $theme = isset($user['theme']) && in_array($user['theme'], ['dark', 'light'])
        ? $user['theme']
        : 'dark';

    // Determine the theme icon (using Font Awesome classes)
    $theme_icon = ($theme === 'dark') ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
    $user_avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.jpg';

} catch (PDOException $e) {
    error_log("Database error: ".$e->getMessage());
    // Default values in case of error
    $theme = 'dark';
    $theme_icon = '<i class="fas fa-moon"></i>';
    $user_avatar = 'assets/images/default-avatar.jpg';
    $user = ['username' => 'Guest'];
}

// XSS protection for displayed data
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Tableau de bord</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Favicon -->
    <link rel="icon" href="./assets/images/Logo.png" type="images/png">
    <!-- Theme-specific CSS -->
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">

    <!-- YouTube IFrame API -->
    <script src="https://www.youtube.com/iframe_api"></script>
</head>
<body>
    <?php 
        include './assets/composents/navbar.php';
        include './assets/composents/result_audioPlayer.php';
    ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 d-none d-lg-block" id="espace">
                <div class="sticky-top pt-3">
                    <h5 class="mb-3"><i class="fas fa-compact-disc me-2"></i>Playlists</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-heart me-2"></i> Favoris
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="#">
                                <i class="fas fa-clock me-2"></i> Récemment écoutés
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="create_playlist.php">
                                <i class="fas fa-plus-circle me-2"></i> Créer une playlist
                            </a>
                        </li>
                    </ul>

                    <h5 class="mt-4 mb-3"><i class="fas fa-tag me-2"></i>Genres</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">Pop</span>
                        <span class="badge bg-primary">Rock</span>
                        <span class="badge bg-primary">Hip-Hop</span>
                        <span class="badge bg-primary">Electro</span>
                        <span class="badge bg-primary">Jazz</span>
                    </div>
                </div>
            </div>

            <!-- Main Area -->
            <div class="col-lg-9" id="espace">
                <h2 class="mb-4">Bonjour, <?= safe_output($user['username']) ?> <i class="fas fa-hand-wave"></i></h2>

                <!-- Section: My Playlists -->
                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="fas fa-list me-2"></i>Mes Playlists</h4>
                        <a href="create_playlist.php" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-plus me-1"></i> Créer
                        </a>
                    </div>

                    <?php if (!empty($playlists)): ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            <?php foreach ($playlists as $playlist): ?>
                                <div class="col">
                                    <div class="playlist-card p-3 rounded position-relative"
                                        data-playlist-id="<?= $playlist['playlist_id'] ?>"
                                        oncontextmenu="showPlaylistMenu(event, <?= $playlist['playlist_id'] ?>)">
                                        <a href="playlist.php?id=<?= $playlist['playlist_id'] ?>" class="text-decoration-none text-reset">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 80px; height: 80px; background: rgba(67,97,238,0.1); border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-music fs-4" style="color: var(--primary);"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1"><?= safe_output($playlist['nom']) ?></h5>
                                                    <small class="text-muted">
                                                        <?= $playlist['nombre_morceaux'] ?> morceau<?= $playlist['nombre_morceaux'] > 1 ? 'x' : '' ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="playlistContextMenu" class="dropdown-menu shadow" style="display: none; position: absolute; z-index: 1000;">
                            <a class="dropdown-item" href="#" id="renamePlaylistBtn"><i class="fas fa-edit me-2"></i>Renommer</a>
                            <a class="dropdown-item" href="#" id="deletePlaylistBtn"><i class="fas fa-trash me-2"></i>Supprimer</a>
                            <a class="dropdown-item" href="#" id="sharePlaylistBtn"><i class="fas fa-share me-2"></i>Partager</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="exportPlaylistBtn"><i class="fas fa-file-export me-2"></i>Exporter</a>
                        </div>

                    <?php else: ?>
                        <div class="text-center py-4" style="background: rgba(255,255,255,0.05); border-radius: 10px;">
                            <i class="fas fa-music fs-1 mb-3" style="color: var(--primary);"></i>
                            <h5>Vous n'avez pas encore de playlists</h5>
                            <p class="text-muted">Commencez par créer votre première playlist</p>
                            <a href="create_playlist.php" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Créer une playlist
                            </a>
                        </div>
                    <?php endif; ?>
                </section>


                <!-- Recently Played -->
                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="fas fa-history me-2"></i>Récemment écouté</h4>
                        <a href="#" class="btn btn-outline-light">Voir tout</a>
                    </div>
                    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
                        <!-- Music cards would be generated dynamically from DB -->
                        <div class="col">
                            <div class="music-card p-3 rounded text-center">
                                <img src="https://via.placeholder.com/150" class="img-fluid rounded mb-2" alt="Album cover">
                                <h6 class="mb-1">Titre de la chanson</h6>
                                <small class="text-muted">Artiste</small>
                            </div>
                        </div>
                        <!-- Repeat for other items -->
                    </div>
                </section>

                <!-- Recommended For You -->
                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="fas fa-star me-2"></i>Recommandé pour vous</h4>
                        <a href="#" class="text-decoration-none">Voir tout</a>
                    </div>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <div class="col">
                            <div class="music-card-alt p-4 rounded d-flex align-items-center">
                                <img src="https://via.placeholder.com/80" class="rounded me-3" alt="Album cover">
                                <div>
                                    <h5>Playlist du jour</h5>
                                    <p class="mb-2">Découvrez vos recommandations quotidiennes</p>
                                    <button class="btn btn-sm btn-primary">Écouter</button>
                                </div>
                            </div>
                        </div>
                        <!-- Repeat for other recommendations -->
                    </div>
                </section>
            </div>
        </div>
    </div>


    <div id="fixedPlayerContainer">
        <div id="currentTrackInfo">
            <p><strong>Titre :</strong> <span id="trackTitle">Aucun</span></p>
            <p><strong>Artiste :</strong> <span id="trackArtist">Inconnu</span></p>
            <p><strong>Durée :</strong> <span id="tracklength">0:00</span></p>
        </div>

        <div id="playerControls">
            <button id="prevTrackBtn" class="btn btn-outline-light btn-sm rounded-circle">
                <img src="assets/icones/backward-solid.svg" alt="Précédent" width="24">
            </button>
            <button id="playPauseBtn" class="btn btn-outline-light btn-sm rounded-circle">
                <img src="assets/icones/play-solid.svg" alt="Lecture" width="24">
            </button>
            <button id="nextTrackBtn" class="btn btn-outline-light btn-sm rounded-circle">
                <img src="assets/icones/forward-solid.svg" alt="Suivant" width="24">
            </button>
            <span id="currentTime">0:00</span> / <span id="totalTime">0:00</span>
            <input id="seekBar" type="range" min="0" max="100" value="0" style="flex-grow: 1;">
            <input id="volumeControl" type="range" min="0" max="1" step="0.1" value="1" style="width: 80px;">
        </div>
    </div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
<script src="../src/research.js"></script>
<script src="../src/clique_droit.js"></script>
</body>
</html>