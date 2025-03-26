<?php
session_start();
session_destroy(); // Détruit toutes les données de la session
header("Location: ../public/login_index.php"); // Redirige vers la page de connexion
exit();
?>