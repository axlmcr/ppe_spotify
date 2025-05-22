
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
    header("Location: login_index.php");
    exit();
}

// Inclure la configuration de la base de données
require_once("../config/db.php");

// Récupérer l'ID utilisateur de manière sécurisée
$user_id = (int)$_SESSION['user_id'];

// Récupérer les playlists de l'utilisateur
try {
    $playlists_stmt = $pdo->prepare("
        SELECT p.playlist_id, p.nom, COUNT(ps.song_id) as nombre_morceaux 
        FROM Playlists p
        LEFT JOIN Playlist_songs ps ON p.playlist_id = ps.playlist_id
        WHERE p.createur_id = ?
        GROUP BY p.playlist_id
        ORDER BY p.date_creation DESC
        LIMIT 3
    ");
    $playlists_stmt->execute([$user_id]);
    $recent_playlists = $playlists_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des playlists: ".$e->getMessage());
    $recent_playlists = [];
}


// Récupérer les informations de l'utilisateur
try {
        $stmt = $pdo->prepare("
         SELECT u.*, 
               COUNT(DISTINCT p.playlist_id) as playlist_count,
               COUNT(DISTINCT ps.song_id) as song_count
        FROM Users u
        LEFT JOIN Playlists p ON u.user_id = p.createur_id
        LEFT JOIN Playlist_songs ps ON ps.playlist_id = p.playlist_id
        
        WHERE u.user_id = ?
        ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Nettoyage complet si l'utilisateur n'existe pas
        session_unset();
        session_destroy();
        header("Location: login_index.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    // On continue avec les valeurs par défaut
}

$theme = $_SESSION['theme'] ?? 'dark';
function safe_output($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?= safe_output($theme) ?>">
<!-- [Le reste du code HTML reste inchangé...] -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">
    <style>


    </style>
</head>
<body>
<!-- Navbar (identique à vos autres pages) -->

<!-- Profile Header -->
<div class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-auto text-center mb-4 mb-md-0">
                <label class="avatar-upload">
                    <img src="<?= !empty($user['avatar']) ? 'uploads/avatars/'.$user['avatar'] : 'assets/images/default-avatar.jpg' ?>"
                         alt="Avatar de <?= safe_output($user['username']) ?>">
                    <input type="file" id="avatarInput" accept="image/*" class="d-none">
                </label>
            </div>
            <div class="col-md">
                <h1 class="display-4 mb-2"><?= safe_output($user['username']) ?></h1>
                <p class="mb-1"><i class="fas fa-envelope me-2"></i><?= safe_output($user['email']) ?></p>
                <p class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Membre depuis <?=
                    isset($user['date_inscription']) ? date('d/m/Y', strtotime($user['date_inscription'])) : date('d/m/Y')
                    ?></p>

                <div class="d-flex gap-3">
                    <div class="stat-card px-4 py-2 text-center">
                        <h5 class="mb-0"><?= $user['playlist_count'] ?></h5>
                        <small>Playlists</small>
                    </div>
                    <div class="stat-card px-4 py-2 text-center">
                        <h5 class="mb-0"><?= $user['song_count'] ?></h5>
                        <small>Morceaux</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Content -->
<div class="container py-5">
    <div class="row">
        <!-- Mes Playlists Récentes -->
        <div class="col-lg-6 mb-5">
            <h3 class="mb-4"><i class="fas fa-list me-2"></i>Mes Playlists Récentes</h3>
            <?php if (!empty($recent_playlists)): ?>
                <div class="row g-3">
                    <?php foreach ($recent_playlists as $playlist): ?>
                        <div class="col-md-6">
                            <a href="playlist.php?id=<?= $playlist['playlist_id'] ?>" class="text-decoration-none">
                                <div class="music-card p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 60px; height: 60px; background: rgba(67,97,238,0.1); border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-music" style="color: var(--primary);"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?= safe_output($playlist['nom']) ?></h6>
                                            <small class="text-muted"><?= $user['song_count'] ?> morceaux</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-end mt-3">
                    <a href="ma_Bibliotheque_index.php" class="btn btn-outline-primary">Voir toutes mes playlists</a>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-music fa-3x mb-3" style="color: var(--primary);"></i>
                    <p>Vous n'avez pas encore de playlists</p>
                    <a href="playlist_index.php" class="btn btn-primary">Créer une playlist</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Activité Récente -->
        <div class="col-lg-6">
            <h3 class="mb-4"><i class="fas fa-history me-2"></i>Activité Récente</h3>
            <div class="activity-list">
                <!-- À remplacer par des données réelles -->
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0 me-3">
                        <i class="fas fa-play-circle fs-4" style="color: var(--primary);"></i>
                    </div>
                    <div>
                        <p class="mb-1">Vous avez écouté <strong>Blinding Lights</strong></p>
                        <small class="text-muted">Il y a 2 heures</small>
                    </div>
                </div>
                <!-- Plus d'activités... -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../src/profil_avatar.js">

</script>
</body>
</html>