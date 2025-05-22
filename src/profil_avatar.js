// Gestion du changement d'avatar
document.getElementById('avatarInput').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const formData = new FormData();
        formData.append('avatar', this.files[0]);

        fetch('upload_avatar.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.avatar-upload img').src = data.filePath + '?' + new Date().getTime();
                } else {
                    alert('Erreur: ' + data.message);
                }
            });
    }
});