<?php
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // This can be either email or username
    $mot_de_passe = $_POST["mot_de_passe"]; // Do not hash the password here

    // Query to check if the identifier matches either email or username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user) {
        // Retrieve the hashed password from the database
        $hashed_password = $user['mot_de_passe'];

        // Verify the plain-text password against the hashed password
        if (password_verify($mot_de_passe, $hashed_password)) {
            session_start();
            $_SESSION['user_id'] = $user['user_id']; // Stocke l'ID de l'utilisateur dans la session
            $_SESSION['username'] = $user['username']; // Stocke le nom d'utilisateur
            header("Location: ../public/index.php");
            exit();
        } else {
            echo "<p style='color: red;'>❌ Mot de passe incorrect.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Identifiant introuvable.</p>";
    }
}
?>