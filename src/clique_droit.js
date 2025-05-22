let currentPlaylistId = null;

    function showPlaylistMenu(event, playlistId) {
        event.preventDefault();
        currentPlaylistId = playlistId;

        const menu = document.getElementById('playlistContextMenu');
        menu.style.display = 'block';
        menu.style.left = `${event.pageX}px`;
        menu.style.top = `${event.pageY}px`;

        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', closeMenu);
    }

    function closeMenu() {
        document.getElementById('playlistContextMenu').style.display = 'none';
        document.removeEventListener('click', closeMenu);
    }

    // Gestion des actions du menu
    document.getElementById('deletePlaylistBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm(`Voulez-vous vraiment supprimer cette playlist ?`)) {
            fetch(`../src/delete_playlist.php?id=${currentPlaylistId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
        }
        closeMenu();
    });

    document.getElementById('renamePlaylistBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const newName = prompt('Nouveau nom pour la playlist:');
        if (newName) {
            fetch(`rename_playlist.php?id=${currentPlaylistId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ newName })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
        }
        closeMenu();
    });
    // Animation d'entrée
    document.addEventListener('DOMContentLoaded', () => {
        gsap.from(".navbar", {duration: 0.8, y: -50, opacity: 0, ease: "power2.out"});
        gsap.from(".music-card", {
            duration: 0.6,
            y: 20,
            opacity: 0,
            stagger: 0.1,
            ease: "back.out(1.2)"
        });
    });

    
    // Theme switch without reload
    document.querySelector('.theme-toggle')?.addEventListener('click', function() {
        document.documentElement.setAttribute('data-theme',
            document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });