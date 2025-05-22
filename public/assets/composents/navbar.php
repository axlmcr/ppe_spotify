<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="Accueil.php">
                <i class="fas fa-wave-square me-2"></i>WaveMusic
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ma_Bibliotheque_index.php">
                            <i class="fa-solid fa-headphones"></i> Playlists
                        </a>
                    </li>
                    <?php if (!isset($show_search) || $show_search): ?>
                        <li class="nav-item">
                            <div class="navbar-search">
                                <input type="text" id="searchInput" placeholder="Rechercher une musique...">
                                <button id="searchBtn">Rechercher</button>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if (!isset($show_button) || $show_button): ?>
                            <li class="nav-item">
                                <!-- Theme toggle button -->
                                <form method="POST" class="d-flex align-items-center">
                                    <button type="submit" name="change_theme" class="theme-toggle btn btn-link p-0">
                                        <?php if ($theme === 'dark'): ?>
                                            <i class="fa-solid fa-moon" style="font-size: 1.5rem; color: #fff;"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-sun" style="font-size: 1.5rem; color: #ffc107;"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?= safe_output($user_avatar) ?>" alt="Avatar" class="user-avatar me-1">
                            <?= safe_output($user['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../src/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>