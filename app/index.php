<?php
session_start();
include "./functions.php";
token();



$dbCo = dbcolink();

$query = $dbCo->prepare("SELECT Id_task, title, order_ FROM task WHERE is_to_do = 1 ORDER BY order_ ASC;");
$query->execute();
$result = $query->fetchAll();
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
        <?php
            if(!empty($_SESSION['success'])){
            echo '<p class="success">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);}
            elseif(!empty($_SESSION['error'])){
                echo '<p class="error">Erreur : ' . implode($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }
            else echo '<h2 class="second-ttl">To Do</h2>';

        ?>
    </header>
    <main>
        <ul class="list">
            <?php
            echo getTaskList($result)
            ?>
        </ul>
    </main>
    <footer>
        <a href="trash.php" class='btn'>
            <img class="btn--trash" src="img/trash-circle-svgrepo-com.svg" alt="voir les tâches déjà faite">
        </a>
        <a href="task.php?do=create" class='btn'>
            <img class="btn--add" src="img/create-svgrepo-com.svg" alt="ajouter une tâche">
        </a>
    </footer>
</body>

</html>