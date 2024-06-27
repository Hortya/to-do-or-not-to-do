<?php
session_start();

if(!isset($_SESSION['token'])){
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
}

include "./functions.php";

$dbCo = dbcolink();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>To Do Or Not To Do</title>
</head>

<body>
    <header>
        <h1 class='main-ttl'>To Do Or Not To Do</h1>
        <h2 class='second-ttl'>Ajouter une tâche</h2>
    </header>
    <main>
        <form action="" method="post" class="add-task-container">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <label class="input-ttl-label" for="tittle">Titre</label>
            <input class="input-ttl" type="text" id="tittle" name="task_tittle" required value="
            <?php echo $_GET['v'] ?>">

           

            <button class='btn'>
                <img class="btn--done" src="img/done-svgrepo-com.svg" alt="créer la tâche ">
            </button>
        </form>
    </main>
    <footer>
        <a href="index.php" class='btn'>
            <img class="btn--close" src="img/close-svgrepo-com.svg" alt="annuler">
        </a>
    </footer>
</body>

</html>