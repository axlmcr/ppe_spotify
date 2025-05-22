<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure'   => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

require_once("../config/db.php");

// Récupérer l'ID utilisateur de manière sécurisée
$user_id = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT username, theme FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Nettoyage complet si l'utilisateur n'existe pas
        session_unset();
        session_destroy();
        header("Location: login_index.php");
        exit();
    }

    // Déterminer le thème (avec valeur par défaut)
    $theme = isset($user['theme']) && in_array($user['theme'], ['dark', 'light'])
        ? $user['theme']
        : 'dark';

    // Déterminer l'icône du thème (avec vérification de fichier)
    $theme_icon = ($theme === 'dark') ? 'assets/images/lune.png' : 'assets/images/soleil.png';
    $user_avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.jpg';

} catch (PDOException $e) {
    error_log("Erreur de base de données: ".$e->getMessage());
    // Valeurs par défaut en cas d'erreur
    $theme = 'dark';
    $theme_icon = 'assets/images/lune.png';
    $user_avatar = 'assets/images/default-avatar.jpg';
    $user = ['username' => 'Invité'];
}

// Protection XSS pour les données affichées
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?= safe_output($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WaveMusic - Confidentialité & Cookies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="./assets/images/Logo.png" type="images/png">
    <link rel="stylesheet" href="assets/css/<?= safe_output($theme) ?>.css">
</head>
<body>
<!-- Navbar (identique à vos autres pages) -->
<?php
    $show_search = false;
    $show_button = false;
    include './assets/composents/navbar.php';
?>  

<div class="legal-container mt-5 pt-4">
    <div class="text-center mb-5">
        <h1><i class="fas fa-lock me-2" id="espace"></i>Confidentialité & Cookies</h1>
        <p class="lead">Dernière mise à jour : <?= date('d/m/Y') ?></p>
    </div>

    <!-- Section Confidentialité -->
    <div class="legal-section" id ="confidentialite">       
        <h2 class="legal-title">1. Politique de Confidentialité</h2>

        <h3>1.1 Données collectées</h3>
        <p>WaveMusic collecte et traite les données suivantes :</p>
        <ul>
            <li>Informations de compte (email, nom d'utilisateur)</li>
            <li>Données de navigation et d'utilisation</li>
            <li>Historique d'écoute et préférences musicales</li>
            <li>Playlists et interactions sociales</li>
        </ul>

        <h3>1.2 Utilisation des données</h3>
        <p>Vos données sont utilisées pour :</p>
        <ul>
            <li>Fournir et améliorer nos services</li>
            <li>Personnaliser vos recommandations musicales</li>
            <li>Communiquer avec vous (notifications, newsletters)</li>
            <li>Analyser l'utilisation de la plateforme</li>
        </ul>

        <h3>1.3 Partage des données</h3>
        <p>Nous ne vendons pas vos données personnelles. Elles peuvent être partagées avec :</p>
        <ul>
            <li>Prestataires techniques (hébergement, analyse)</li>
            <li>Services de paiement pour les abonnements premium</li>
            <li>Artistes/labels pour les statistiques d'écoute (de manière anonyme et agrégée)</li>
        </ul>

        <h3>1.4 Vos droits</h3>
        <p>Conformément au RGPD, vous avez le droit :</p>
        <ul>
            <li>D'accès et de rectification</li>
            <li>D'opposition et de suppression</li>
            <li>De portabilité de vos données</li>
            <li>De limitation du traitement</li>
        </ul>
        <p>Pour exercer ces droits, contactez : <a href="mailto:privacy@wavemusic.com">privacy@wavemusic.com</a></p>
    </div>

    <!-- Section Cookies -->
    <div class="legal-section" id="cookies">
        <h2 class="legal-title">2. Politique des Cookies</h2>

        <h3>2.1 Qu'est-ce qu'un cookie ?</h3>
        <p>Un cookie est un petit fichier texte stocké sur votre appareil pour :</p>
        <ul>
            <li>Mémoriser vos préférences</li>
            <li>Améliorer la performance du site</li>
            <li>Personnaliser votre expérience</li>
        </ul>

        <div class="cookie-settings">
            <h4><i class="fas fa-cookie-bite me-2"></i>Gestion des cookies</h4>

            <div class="cookie-toggle">
                <div>
                    <h5>Cookies essentiels</h5>
                    <small>Nécessaires au fonctionnement du site</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="essentialCookies" checked disabled>
                    <label class="form-check-label" for="essentialCookies">Toujours actifs</label>
                </div>
            </div>

            <div class="cookie-toggle">
                <div>
                    <h5>Cookies de performance</h5>
                    <small>Améliorent l'expérience utilisateur</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="performanceCookies" checked>
                    <label class="form-check-label" for="performanceCookies">Activer</label>
                </div>
            </div>

            <div class="cookie-toggle">
                <div>
                    <h5>Cookies marketing</h5>
                    <small>Pour des publicités personnalisées</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="marketingCookies">
                    <label class="form-check-label" for="marketingCookies">Activer</label>
                </div>
            </div>

            <button id="saveCookiePrefs" class="btn btn-primary mt-3">
                <i class="fas fa-save me-2"></i>Enregistrer les préférences
            </button>
        </div>

        <h3 class="mt-4">2.2 Cookies tiers</h3>
        <p>Nous utilisons des services qui peuvent déposer des cookies :</p>
        <table class="table">
            <thead>
            <tr>
                <th>Service</th>
                <th>Finalité</th>
                <th>Politique</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Google Analytics</td>
                <td>Analyse d'audience</td>
                <td><a href="https://policies.google.com/privacy" target="_blank">Voir</a></td>
            </tr>
            <tr>
                <td>Stripe</td>
                <td>Paiements</td>
                <td><a href="https://stripe.com/privacy" target="_blank">Voir</a></td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Section Contact -->
    <div class="legal-section">
        <h2 class="legal-title">3. Nous contacter</h2>
        <p>Pour toute question concernant cette politique :</p>
        <p>
            <i class="fas fa-envelope me-2"></i>Email : <a href="mailto:privacy@wavemusic.com">privacy@wavemusic.com</a><br>
            <i class="fas fa-map-marker-alt me-2"></i>Adresse : 10 Rue de la Musique, 75000 Paris, France
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../src/legal.js">

</script>
</body>
</html>