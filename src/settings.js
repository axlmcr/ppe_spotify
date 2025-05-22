function selectTheme(theme) {
    document.getElementById('themeInput').value = theme;
    document.querySelectorAll('.theme-selector').forEach(el => {
        el.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
}