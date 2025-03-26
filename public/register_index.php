<!DOCTYPE html>
<html lang = "fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content ="width, intinal-scale=1.0">
        <title>MusicWave</title>
        <link rel ="stylesheet" href="assets/stylesheet.css">
    </head>
    <body>
        <header>
            <div>
                <a href="index.php"><h1>MusicWave</h1></a>
                <a href="playlist_index.php">Playlist</a>
            </div>
            <nav>
                <a href="login_index.php">Connexion</a>
                <a href="register_index.php">Inscription</a>
            </nav>
        </header>

        <h2>Inscription</h2>

        <form action="../src/register.php" method="post">

            <label for="username">nom d'utilisateur :</label>
            <input type="text" id="username" name="username" placeholder="" required>
            <br><br>

            <label for="email">email :</label>
            <input type="text" id="email" name="email" placeholder="exemple.adresse@gmail.com" required>
            <br><br>


            <label for="mot_de_passe"> mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="" required></textarea>
            <br><br>

            <br><br>
            <div class="container">
                <button>envoie </button>
                <button>effacer</button>
            </div>
        </form>
    </body> 