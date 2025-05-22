// Gestion des chansons sélectionnées
let selectedSongs = JSON.parse(document.getElementById('selectedSongs').value || '[]');
if (!Array.isArray(selectedSongs)) selectedSongs = [];

function updateSelectedSongs() {
    document.getElementById('selectedSongs').value = JSON.stringify(selectedSongs);
    renderPlaylistSongs();
}

function renderPlaylistSongs() {
    const container = document.getElementById('playlistSongs');
    const songCount = document.getElementById('songCount');

    if (selectedSongs.length === 0) {
        container.innerHTML = `
                <div class="empty-state fade-in">
                    <i class="fas fa-music"></i>
                    <h4>Votre playlist est vide</h4>
                    <p class="text-muted">Commencez par ajouter des titres à votre playlist</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#songModal">
                        <i class="fas fa-plus me-2"></i>Ajouter des titres
                    </button>
                </div>
            `;
        songCount.textContent = '0';
        return;
    }

    let html = '';
    selectedSongs.forEach(songId => {
        // Note: Dans une vraie application, vous devriez récupérer les détails des chansons depuis votre base de données
        html += `
                <div class="song-item mb-2 p-3 fade-in" data-song-id="${songId}">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/50" class="song-cover me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Titre de la chanson</h6>
                            <small class="text-muted">Artiste • Album</small>
                        </div>
                        <small class="text-muted me-3">3:42</small>
                        <button class="btn btn-sm btn-outline-danger remove-song" data-song-id="${songId}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
    });

    container.innerHTML = html;
    songCount.textContent = selectedSongs.length;

    // Ajout des écouteurs pour la suppression
    document.querySelectorAll('.remove-song').forEach(btn => {
        btn.addEventListener('click', function() {
            const songId = parseInt(this.getAttribute('data-song-id'));
            selectedSongs = selectedSongs.filter(id => id !== songId);
            updateSelectedSongs();
        });
    });
}

// Recherche dans le modal
document.getElementById('modalSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.song-select-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(searchTerm) ? 'block' : 'none';
    });
});

// Ajout de chansons depuis le modal
document.getElementById('addSongsBtn').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.song-checkbox:checked');
    checkboxes.forEach(checkbox => {
        const songId = parseInt(checkbox.closest('.song-select-item').getAttribute('data-song-id'));
        if (!selectedSongs.includes(songId)) {
            selectedSongs.push(songId);
        }
    });

    updateSelectedSongs();
    bootstrap.Modal.getInstance(document.getElementById('songModal')).hide();
});

// Initialisation
renderPlaylistSongs();

// Animation pour les nouveaux éléments
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) { // Element node
                node.classList.add('fade-in');
            }
        });
    });
});

observer.observe(document.getElementById('playlistSongs'), {
    childList: true
});