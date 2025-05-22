<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

require_once("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_profile'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);

            // Validation
            if (empty($username) || empty($email)) {
                throw new Exception("Tous les champs sont obligatoires");
            }

            $stmt = $pdo->prepare("UPDATE Users SET username = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $user_id]);

            $_SESSION['success'] = "Profil mis à jour avec succès";
            header("Location: settings.php");
            exit();
        }

        if (isset($_POST['update_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Vérifier le mot de passe actuel
            $stmt = $pdo->prepare("SELECT mot_de_passe FROM Users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!password_verify($current_password, $user['mot_de_passe'])) {
                throw new Exception("Mot de passe actuel incorrect");
            }

            if ($new_password !== $confirm_password) {
                throw new Exception("Les nouveaux mots de passe ne correspondent pas");
            }

            if (strlen($new_password) < 8) {
                throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE Users SET mot_de_passe = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $user_id]);

            $_SESSION['success'] = "Mot de passe mis à jour avec succès";
            header("Location: settings.php");
            exit();
        }

        if (isset($_POST['update_theme'])) {
            $theme = $_POST['theme'] === 'dark' ? 'dark' : 'light';

            $stmt = $pdo->prepare("UPDATE Users SET theme = ? WHERE user_id = ?");
            $stmt->execute([$theme, $user_id]);

            $_SESSION['theme'] = $theme;
            $_SESSION['success'] = "Thème mis à jour";
            header("Location: settings.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Récupérer les infos utilisateur
try {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données");
}
$user_avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.jpg';
$theme = $_SESSION['theme'] ?? 'dark';
function safe_output($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?= safe_output($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Paramètres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">

</head>
<body>
<?php
 $show_search = false;
 $show_button = false;
 include "assets/composents/navbar.php";
 ?>

<div class="container py-5">
    <div class="row" id="espace">
        <!-- Menu Paramètres -->
        <div class="col-lg-3 mb-4">
            <div class="settings-card p-3 sticky-top" style="top: 20px;">
                <h4 class="mb-4">Paramètres</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                            <i class="fas fa-user me-2"></i>Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#password" data-bs-toggle="tab">
                            <i class="fas fa-lock me-2"></i>Mot de passe
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#appearance" data-bs-toggle="tab">
                            <i class="fas fa-paint-brush me-2"></i>Apparence
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#notifications" data-bs-toggle="tab">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenu Paramètres -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Onglet Profil -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="settings-card p-4 mb-4">
                        <h4 class="mb-4"><i class="fas fa-user me-2"></i>Modifier le profil</h4>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" name="username"
                                       value="<?= safe_output($user['username']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email"
                                       value="<?= safe_output($user['email']) ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                Enregistrer les modifications
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Onglet Mot de passe -->
                <div class="tab-pane fade" id="password">
                    <div class="settings-card p-4 mb-4">
                        <h4 class="mb-4"><i class="fas fa-lock me-2"></i>Changer le mot de passe</h4>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le nouveau mot de passe</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">
                                Changer le mot de passe
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Onglet Apparence -->
                <div class="tab-pane fade" id="appearance">
                    <div class="settings-card p-4 mb-4">
                        <h4 class="mb-4"><i class="fas fa-paint-brush me-2"></i>Thème de l'application</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="theme-selector <?= $theme === 'dark' ? 'active' : '' ?>"
                                         onclick="selectTheme('dark')">
                                        <div class="p-3" style="background: #121212; border-radius: 5px;">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Sombre</span>
                                                <?php if ($theme === 'dark'): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex">
                                                <div style="width: 20px; height: 20px; background: #4361ee; margin-right: 5px;"></div>
                                                <div style="width: 20px; height: 20px; background: #f72585; margin-right: 5px;"></div>
                                                <div style="width: 20px; height: 20px; background: #1a1a1a; margin-right: 5px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="theme-selector <?= $theme === 'light' ? 'active' : '' ?>"
                                         onclick="selectTheme('light')">
                                        <div class="p-3" style="background: #f8f9fa; border-radius: 5px; color: #333;">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Clair</span>
                                                <?php if ($theme === 'light'): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex">
                                                <div style="width: 20px; height: 20px; background: #4361ee; margin-right: 5px;"></div>
                                                <div style="width: 20px; height: 20px; background: #f72585; margin-right: 5px;"></div>
                                                <div style="width: 20px; height: 20px; background: #e9ecef; margin-right: 5px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="theme" id="themeInput" value="<?= $theme ?>">
                            <button type="submit" name="update_theme" class="btn btn-primary">
                                Appliquer le thème
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Onglet Notifications -->
                <div class="tab-pane fade" id="notifications">
                    <div class="settings-card p-4">
                        <h4 class="mb-4"><i class="fas fa-bell me-2"></i>Préférences de notifications</h4>
                        <form>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notif1" checked>
                                <label class="form-check-label" for="notif1">Nouveaux morceaux de mes artistes favoris</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notif2" checked>
                                <label class="form-check-label" for="notif2">Suggestions hebdomadaires</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notif3">
                                <label class="form-check-label" for="notif3">Newsletter</label>
                            </div>
                            <button type="button" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../src/settings.js">

</script>
</body>
</html>