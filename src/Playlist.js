document.addEventListener('DOMContentLoaded', function() {
    const addPlaylistBtn = document.getElementById('add-playlist-btn');
    const playlistsContainer = document.getElementById('playlists-container');
    const createPlaylistModal = new bootstrap.Modal('#createPlaylistModal');
    const createPlaylistForm = document.getElementById('create-playlist-form');

    // Vérifier si l'utilisateur est connecté (à adapter selon votre système)
    const isLoggedIn = checkUserLoggedIn(); // À remplacer par votre vérification

    if (isLoggedIn) {
        // Afficher le bouton +
        addPlaylistBtn.classList.remove('d-none');

        // Charger les playlists de l'utilisateur
        loadUserPlaylists();
    } else {
        // Afficher les playlists publiques ou un message
        playlistsContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <h4>Connectez-vous pour voir vos playlists</h4>
                <a href="/public/login_index.php" class="btn btn-primary mt-3">Se connecter</a>
            </div>
        `;
    }

    // Gestion du clic sur le bouton +
    addPlaylistBtn.addEventListener('click', function() {
        createPlaylistModal.show();
    });

    // Soumission du formulaire de création
    createPlaylistForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const playlistName = document.getElementById('playlist-name').value;

        // Envoyer la requête AJAX pour créer la playlist
        fetch('/api/create_playlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: playlistName
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    createPlaylistModal.hide();
                    loadUserPlaylists();
                    document.getElementById('playlist-name').value = '';
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // Fonction pour charger les playlists de l'utilisateur
    function loadUserPlaylists() {
        fetch('/api/get_playlists.php')
            .then(response => response.json())
            .then(playlists => {
                renderPlaylists(playlists);
            })
            .catch(error => console.error('Error loading playlists:', error));
    }

    // Fonction pour afficher les playlists
    function renderPlaylists(playlists) {
        if (playlists.length === 0) {
            playlistsContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <h4>Vous n'avez pas encore de playlists</h4>
                    <button id="add-playlist-btn" class="btn btn-primary mt-3">
                        Créer une playlist
                    </button>
                </div>
            `;
            return;
        }

        let html = '';
        playlists.forEach(playlist => {
            html += `
                <div class="col-md-4 mb-4">
                    <div class="playlist-card">
                        <div class="playlist-card-img" style="background-image: url('${playlist.cover || 'images/default-playlist.jpg'}')"></div>
                        <div class="playlist-card-body">
                            <h5 class="playlist-card-title">${playlist.name}</h5>
                            <p class="playlist-card-count">${playlist.track_count} morceaux</p>
                            <a href="playlist_detail.php?id=${playlist.id}" class="btn btn-sm btn-outline-light">Voir</a>
                        </div>
                    </div>
                </div>
            `;
        });

        playlistsContainer.innerHTML = html;
    }

    // Fonction pour vérifier si l'utilisateur est connecté (à adapter)
    function checkUserLoggedIn() {
        // Vous pouvez utiliser:
        // - Un cookie/session
        // - LocalStorage
        // - Une requête AJAX vers le serveur
        // Exemple simpliste:
        return localStorage.getItem('isLoggedIn') === 'true';
    }
});