<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $mot_de_passe = password_hash($_POST["mot_de_passe"], PASSWORD_BCRYPT); // Hash the password once

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['username'] === $username) {
            echo "<p style='color: orange;'>Ce nom d'utilisateur est déjà utilisé.</p>";
        }
        if ($user['email'] === $email) {
            echo "<p style='color: red;'>Cet email est déjà utilisé.</p>";
        }
    } else {
        if ($email && $mot_de_passe) {
            $stmt = $pdo->prepare("INSERT INTO Users (username, email, mot_de_passe) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $mot_de_passe]);
            header("Location: ../public/login_index.php");
        } else {
            echo "Email invalide ou erreur de saisie.";
        }
    }
}
?>