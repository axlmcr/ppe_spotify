<?php
session_start();
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Non authentifié']));
}

$playlist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Vérifier que l'utilisateur est bien le propriétaire
    $stmt = $pdo->prepare("DELETE FROM Playlists WHERE playlist_id = ? AND createur_id = ?");
    $stmt->execute([$playlist_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Playlist non trouvée ou non autorisée']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}
