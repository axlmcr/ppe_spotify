<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_index.php");
    exit();
}

include("../config/db.php"); // Include the database configuration file

// Retrieve the logged-in user's information
$user_id = $_SESSION['user_id'];

// If the form to change the theme is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_theme'])) {
    // Get the current theme of the user
    $stmt = $pdo->prepare("SELECT theme FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Determine the new theme (toggle between 'dark' and 'light')
    $new_theme = ($user['theme'] === 'dark') ? 'light' : 'dark';

    // Update the theme in the database
    $stmt = $pdo->prepare("UPDATE users SET theme = ? WHERE user_id = ?");
    $stmt->execute([$new_theme, $user_id]);

    // Reload the page to apply the new theme
    header("Location: index.php");
    exit();
}

// Retrieve the logged-in user's information again
$stmt = $pdo->prepare("SELECT username, theme FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // If the user does not exist, destroy the session and redirect to the login page
    session_destroy();
    header("Location: login_index.php");
    exit();
}

// Get the user's theme or set the default to 'dark'
$theme = $user['theme'] ?? 'dark';

// Determine the icon for the theme toggle button
$theme_icon = ($theme === 'dark') ? 'assets/images/lune.png' : 'assets/images/soleil.png';
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