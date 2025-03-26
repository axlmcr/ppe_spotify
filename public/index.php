<?php
session_start(); // Démarre la session

if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: login_index.php");
    exit();
}

include("../config/db.php");

// Récupère les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Si le formulaire pour changer le thème est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_theme'])) {
    // Récupère le thème actuel de l'utilisateur
    $stmt = $pdo->prepare("SELECT theme FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Détermine le nouveau thème (bascule entre 'dark' et 'light')
    $new_theme = ($user['theme'] === 'dark') ? 'light' : 'dark';

    // Met à jour le thème dans la base de données
    $stmt = $pdo->prepare("UPDATE users SET theme = ? WHERE user_id = ?");
    $stmt->execute([$new_theme, $user_id]);

    // Recharge la page pour appliquer le nouveau thème
    header("Location: index.php");
    exit();
}

// Récupère les informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT username, theme FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Si l'utilisateur n'existe pas, détruisez la session et redirigez
    session_destroy();
    header("Location: login_index.php");
    exit();
}

$theme = $user['theme'] ?? 'dark'; // Par défaut, le thème est 'dark'


$theme_icon = ($theme === 'dark') ? 'asset/images/lune.png' : 'asset/images/soleil.png';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicWave</title>
    <link rel="stylesheet" href="assets/css/<?= $theme ?>.css">
</head>
<body>
    <header>
        <div>
            <a href="index.php"><h1>MusicWave</h1></a>
            <a href="playlist_index.php">Playlist</a>
        </div>
        <nav>
            <p>Bienvenue, <?php echo htmlspecialchars($user['username']); ?> !</p>
            <form method="POST" style="display: inline;">
                <button type="submit" name="change_theme">
                        <img src="<?= $theme_icon ?>" alt="Changer le thème" style="width: 24px; height: 24px;">
                </button>
            </form>
            <a class ="logout" href="../src/logout.php">Déconnexion</a>
        </nav>
    </header>
    <section>
        <h2>Découvrez des artistes</h2>
        <p>Écoutez gratuitement des morceaux</p>
    </section>
</body>
</html>