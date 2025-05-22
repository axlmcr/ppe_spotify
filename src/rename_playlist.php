<?php
session_start();
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Non authentifié']));
}

$data = json_decode(file_get_contents('php://input'), true);
$playlist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$newName = trim($data['newName'] ?? '');

if (empty($newName)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Nom vide']));
}

try {
    $stmt = $pdo->prepare("UPDATE Playlists SET nom = ? WHERE playlist_id = ? AND createur_id = ?");
    $stmt->execute([$newName, $playlist_id, $_SESSION['user_id']]);

    echo json_encode(['success' => $stmt->rowCount() > 0]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}