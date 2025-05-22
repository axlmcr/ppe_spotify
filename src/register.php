<?php
session_start();
include("../config/db.php");

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['form_errors'] = ['global' => 'Token de sécurité invalide.'];
    $_SESSION['old_input'] = $_POST;
    header("Location: ../public/register_index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialisation des erreurs et anciennes valeurs
    $errors = [];
    $old_input = [
        'username' => trim($_POST["username"] ?? ''),
        'email' => trim($_POST["email"] ?? '')
    ];

    // Validation des données
    if (empty($old_input['username'])) {
        $errors['username'] = "Le nom d'utilisateur est requis.";
    } elseif (strlen($old_input['username']) < 3) {
        $errors['username'] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $old_input['username'])) {
        $errors['username'] = "Seuls les lettres, chiffres et underscores sont autorisés.";
    }

    if (empty($old_input['email'])) {
        $errors['email'] = "L'adresse email est requise.";
    } elseif (!filter_var($old_input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'adresse email n'est pas valide.";
    }

    if (empty($_POST["mot_de_passe"])) {
        $errors['mot_de_passe'] = "Le mot de passe est requis.";
    } elseif (strlen($_POST["mot_de_passe"]) < 8) {
        $errors['mot_de_passe'] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $_POST["mot_de_passe"]) || !preg_match('/[0-9]/', $_POST["mot_de_passe"])) {
        $errors['mot_de_passe'] = "Le mot de passe doit contenir au moins une majuscule et un chiffre.";
    }

    if ($_POST["mot_de_passe"] !== $_POST["confirm_password"]) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
    }

    // Vérification de l'unicité des données
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
        $stmt->execute([$old_input['username'], $old_input['email']]);
        $user = $stmt->fetch();

        if ($user) {
            if (strtolower($user['username']) === strtolower($old_input['username'])) {
                $errors['username'] = "Ce nom d'utilisateur est déjà utilisé.";
            }
            if (strtolower($user['email']) === strtolower($old_input['email'])) {
                $errors['email'] = "Cet email est déjà utilisé.";
            }
        }
    }

    // Traitement final
    if (empty($errors)) {
        $mot_de_passe = password_hash($_POST["mot_de_passe"], PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO Users (username, email, mot_de_passe) VALUES (?, ?, ?)");
            $stmt->execute([$old_input['username'], $old_input['email'], $mot_de_passe]);

            // Nettoyage des données de session
            unset($_SESSION['form_errors'], $_SESSION['old_input']);

            // Redirection vers la page de connexion avec message de succès
            $_SESSION['registration_success'] = true;
            header("Location: ../public/login_index.php");
            exit();
        } catch (PDOException $e) {
            $errors['global'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
            error_log("Erreur d'inscription: " . $e->getMessage());
        }
    }

    // Stockage des erreurs et anciennes valeurs pour réaffichage
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $old_input;
        header("Location: ../public/register_index.php");
        exit();
    }
} else {
    // Méthode non autorisée
    header("Location: ../public/register_index.php");
    exit();
}
?>