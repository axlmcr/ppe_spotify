<?php 
    // Database connection configuration
    $host = "localhost"; // Hostname of the database server
    $dbname = "ppe_spotify"; // Name of the database
    $username = "root"; // Database username
    $password = ""; // Database password (empty for local development)

    try {
        // Create a new PDO instance to connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        
        // Set PDO error mode to exception for better error handling
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // If the connection fails, display an error message and stop the script
        die("Erreur : " . $e->getMessage());
    }
?>