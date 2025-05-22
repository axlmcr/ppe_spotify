<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

require_once("../config/db.php");

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Récupérer l'ID de la playlist depuis l'URL
$playlist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($playlist_id === 0) {
    header("Location: ma_Bibliotheque_index.php");
    exit();
}

try {
    // Vérifiez les variables
    if (!is_int($playlist_id) || $playlist_id <= 0) {
        die("ID de playlist invalide.");
    }

    if (!is_int($user_id) || $user_id <= 0) {
        die("ID d'utilisateur invalide.");
    }

    // Récupérer les infos de la playlist
    $stmt = $pdo->prepare("
        SELECT p.*, u.username as createur 
        FROM Playlists p
        JOIN Users u ON p.createur_id = u.user_id
        WHERE p.playlist_id = ? AND (p.createur_id = ? OR p.public = 1)
    ");
    $stmt->execute([$playlist_id, $user_id]);
    $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$playlist) {
        die("La playlist n'existe pas ou vous n'avez pas les permissions.");
    }

    // Récupérer les morceaux de la playlist
    $songs_stmt = $pdo->prepare("
        SELECT s.song_id, s.titre, s.album, s.fichier_audio, 
               a.nom_artiste, al.titre as album_titre
        FROM Playlist_songs ps
        JOIN Songs s ON ps.song_id = s.song_id
        JOIN Artistes a ON s.artiste = a.artiste_id
        LEFT JOIN Albums al ON s.album = al.album_id
        WHERE ps.playlist_id = ?
        ORDER BY ps.song_id DESC -- Assurez-vous d'utiliser un champ valide ici
    ");
    $songs_stmt->execute([$playlist_id]);
    $songs = $songs_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($songs)) {
        echo "La playlist est vide.";
    }
} catch (PDOException $e) {
    die("Erreur PDO : " . $e->getMessage());
}

// Gestion du thème (comme dans vos autres pages)
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'dark';
$theme_icon = ($theme === 'dark') ? 'assets/images/lune.png' : 'assets/images/soleil.png';
$user_avatar = 'assets/images/default-avatar.jpg';

function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?= safe_output($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - <?= safe_output($playlist['nom']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="./assets/images/Logo.png" type="images/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-wave-square me-2"></i>WaveMusic
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-home me-1"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-search me-1"></i> Explorer
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ma_Bibliotheque_index.php">
                        <i class="fas fa-music me-1"></i> Ma bibliothèque
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Playlist Header -->
<div class="playlist-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-auto text-center text-md-start mb-4 mb-md-0">
                <img src="<?= !empty($playlist['cover_image']) ? 'uploads/playlist_covers/'.safe_output($playlist['cover_image']) : 'assets/images/Logo.png' ?>"
                     class="playlist-cover rounded img-fluid"
                     alt="Couverture de <?= safe_output($playlist['nom']) ?>"
                     onerror="this.src='assets/images/Logo.png'">
            </div>
            <div class="col-md">
                <p class="mb-2">PLAYLIST</p>
                <h1 class="display-4 mb-3"><?= safe_output($playlist['nom']) ?></h1>

                <p class="text-muted">
                    Créée par <?= safe_output($playlist['createur']) ?> •
                    <?= count($songs) ?> morceau<?= count($songs) > 1 ? 'x' : '' ?>
                </p>
                <div class="d-flex gap-3 mt-3">
                    <button class="btn btn-primary btn-lg px-4 py-2">
                        <i class="fas fa-play me-2"></i> Lire
                    </button>
                    <button class="btn btn-outline-light btn-lg px-4 py-2">
                        <i class="fas fa-random me-2"></i> Aléatoire
                    </button>
                    <?php if ($playlist['createur_id'] === $user_id): ?>
                        <a href="edit_playlist.php?id=<?= $playlist_id ?>" class="btn btn-outline-light btn-lg px-4 py-2">
                            <i class="fas fa-edit me-2"></i> Modifier
                        </a>
                        <button class="btn btn-outline-light btn-lg px-4 py-2" id="deletePlaylistBtn" data-playlist-id="<?= $playlist_id ?>">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Playlist Content -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="mb-4"><i class="fas fa-music me-2"></i>Morceaux</h3>

            <?php if (empty($songs)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-music fa-3x mb-3" style="color: var(--primary);"></i>
                    <h4>Cette playlist est vide.</h4>
                    <p class="text-muted">Ajoutez des morceaux pour commencer</p>
                    <a href="explorer.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Ajouter des titres
                    </a>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($songs as $index => $song): ?>
                        <div class="list-group-item song-item py-3 d-flex align-items-center">
                            <div class="me-3 text-muted" style="width: 40px;">
                                <?= $index + 1 ?>
                            </div>
                            <button class="play-btn me-3 bg-primary text-white">
                                <i class="fas fa-play"></i>
                            </button>
                            <div class="flex-grow-1">
                                <h5 class="mb-1"><?= safe_output($song['titre']) ?></h5>
                                <p class="mb-0 text-muted">
                                    <?= safe_output($song['nom_artiste']) ?> •
                                    <?= safe_output($song['album_titre'] ?? $song['album']) ?>
                                </p>
                            </div>
                            <div class="text-muted me-3">
                                3:42 <!-- À remplacer par la durée réelle -->
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-heart me-2"></i>Ajouter aux favoris</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-list me-2"></i>Ajouter à une playlist</a></li>
                                    <?php if ($playlist['createur_id'] === $user_id): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="remove_song.php?playlist=<?= $playlist_id ?>&song=<?= $song['song_id'] ?>">
                                                <i class="fas fa-trash me-2"></i>Retirer de la playlist
                                            </a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../src/Playlist.js"> </script>
</body>
</html>