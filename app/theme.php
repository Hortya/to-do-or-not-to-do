<?php
session_start();
include "./functions.php";
include "./includes/_database.php";
token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>theme</title>
</head>
<body>
    <header>
        <h1 class='main-ttl'>To Do Or Not To Do</h1>
        <h2 class="second-ttl">Créer ton Theme</h2>
    </header>
    <main>
    <form action="action.php" method="post" class="add-task-container">
        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
        <input type="hidden" name="do" value="theme">
        <label for="themecolor">Couleur du thème</label>
        <input type="color" name="themecolor" id="themecolor">
        <label for="themename">Nom du thème</label>
        <input type="text" name="themename" id="themename">
        <button>valider</button>
    </form>
    </main>
    <footer>
        <a href="index.php" class='btn'>
            <img class="btn--close" src="img/close-svgrepo-com.svg" alt="annuler">
        </a>
    </footer>
</body>
</html>