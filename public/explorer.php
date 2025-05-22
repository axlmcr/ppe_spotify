<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_playlist'])) {
        try {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $public = isset($_POST['public']) ? 1 : 0;
            $songs = json_decode($_POST['songs'] ?? '[]', true) ?? [];

            if (empty($nom)) {
                throw new Exception("Le nom de la playlist est obligatoire");
            }

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO Playlists (nom, createur_id, public) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $user_id, $public]);
            $playlist_id = $pdo->lastInsertId();

            if (!empty($songs)) {
                $placeholders = implode(',', array_fill(0, count($songs), '?'));
                $stmt = $pdo->prepare("SELECT song_id FROM Songs WHERE song_id IN ($placeholders)");
                $stmt->execute($songs);

                if ($stmt->rowCount() !== count($songs)) {
                    throw new Exception("Certaines chansons sélectionnées sont invalides");
                }

                $stmt = $pdo->prepare("INSERT INTO Playlist_songs (playlist_id, song_id) VALUES (?, ?)");
                foreach ($songs as $song_id) {
                    $stmt->execute([$playlist_id, $song_id]);
                }
            }

            $pdo->commit();
            $success = "Playlist créée avec succès!";
            header("Refresh: 2; URL=playlist.php?id=" . $playlist_id);

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

$user_songs = [];
try {
    $stmt = $pdo->prepare("
        SELECT s.song_id, s.titre, a.nom_artiste, s.album, s.genre
        FROM Songs s
        JOIN Artistes a ON s.artiste = a.artiste_id
        ORDER BY a.nom_artiste, s.titre
    ");
    $stmt->execute();
    $user_songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des chansons: " . $e->getMessage();
}

$theme = $_SESSION['theme'] ?? 'dark';
function safe_output($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Créer une playlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">

</head>
<body>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    Morceaux sélectionnés
                    <span id="songCount" class="song-badge">0</span>
                </h3>
                <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#songModal">
                    <i class="fas fa-plus me-2"></i>Ajouter des titres
                </button>
            </div>

            <div id="playlistSongs" class="mb-5">
                <div class="empty-state">
                    <i class="fas fa-music"></i>
                    <h4>Votre playlist est vide</h4>
                    <p class="text-muted">Commencez par ajouter des titres à votre playlist</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#songModal">
                        <i class="fas fa-plus me-2"></i>Ajouter des titres
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating action button -->
<button type="submit" form="playlistForm" class="floating-btn btn btn-primary d-lg-none">
    <i class="fas fa-save"></i>
</button>

<!-- Song selection modal -->
<div class="modal fade" id="songModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter des chansons</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="search-box mb-3">
                    <i class="fas fa-search"></i>
                    <input type="text" id="modalSearch" class="form-control search-input" placeholder="Rechercher dans votre bibliothèque...">
                </div>

                <div id="songList" class="list-group" style="max-height: 60vh; overflow-y: auto;">
                    <?php foreach ($user_songs as $song): ?>
                        <div class="list-group-item song-select-item py-3" data-song-id="<?= $song['song_id'] ?>">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input me-3 song-checkbox">
                                <img src="https://via.placeholder.com/50" class="song-cover me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= htmlspecialchars($song['titre']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($song['nom_artiste']) ?> • <?= htmlspecialchars($song['album']) ?></small>
                                </div>
                                <small class="text-muted">3:42</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="addSongsBtn" class="btn btn-primary">Ajouter les titres sélectionnés</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../src/create_playlist_animation.js">

</script>
</body>
</html>