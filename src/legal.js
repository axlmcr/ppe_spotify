// Gestion des préférences cookies
document.addEventListener('DOMContentLoaded', function() {
    // Charger les préférences existantes
    const cookiePrefs = JSON.parse(localStorage.getItem('cookiePrefs') || '{}');

    // Initialiser les switchs
    if (cookiePrefs.performance !== undefined) {
        document.getElementById('performanceCookies').checked = cookiePrefs.performance;
    }
    if (cookiePrefs.marketing !== undefined) {
        document.getElementById('marketingCookies').checked = cookiePrefs.marketing;
    }

    // Sauvegarde des préférences
    document.getElementById('saveCookiePrefs').addEventListener('click', function() {
        const prefs = {
            performance: document.getElementById('performanceCookies').checked,
            marketing: document.getElementById('marketingCookies').checked,
            date: new Date().toISOString()
        };

        localStorage.setItem('cookiePrefs', JSON.stringify(prefs));
        alert('Préférences enregistrées !');

        // Ici vous devriez aussi envoyer ces préfs au serveur
        // pour les appliquer côté backend
    });
});