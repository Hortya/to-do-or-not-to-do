<?php
session_start();
include "./functions.php";
include "./includes/_database.php";
token();



if(!isset($_GET['do'])){
    header('Location: index.php');
    exit;
}
elseif ($_GET['do'] === 'create'){
    $h2 = 'Créer';
}
elseif ($_GET['do'] === 'modifie'){
    $h2 = 'Modifier';
    $query = $dbCo->prepare("SELECT Id_task, title FROM task WHERE Id_task = :id ORDER BY creation_date ASC;");
    $isqueryOK = $query->execute(['id' => ROUND($_GET['i'])]);
    $result = $query->fetch();
    if(!$result){
        header('Location: index.php');
        exit;
    }
}
$queryTheme = $dbCo->prepare("SELECT name, id_theme FROM theme");
    $isqueryOK = $queryTheme->execute();
    $resultTheme = $queryTheme->fetchAll();
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
            echo '<h2 class="second-ttl">' . $h2 . ' sa tâche</h2>';
        ?>
        
    </header>
    <main>
        <form action="action.php" method="post" class="add-task-container">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="do" value="<?= $_GET['do'] ?>">
            <?php if($_GET['do'] === 'modifie') echo '<input type="hidden" name="i" value="' . $result['Id_task'] . '">';
            ?>
            <label class="input-ttl-label" for="tittle">Titre</label>
            <input class="input-ttl" type="text" id="tittle" name="task_tittle" required placeholder="voter NFP"
            <?php 
            if($_GET['do'] === 'modifie'){
                echo 'value ="' . $result['title'] . '"';
            }
            ?>
            >
            <input type="date" name="date" class="input-date">
            <?php
            if(isset($_GET['error'])) echo '<p>ERREUR : ' . $_GET['error'];
            ?>
            <fieldset>
                <legend>Sélectionnez vos thèmes</legend>
                <?php
                if(is_array($resultTheme)){
                    foreach($resultTheme as $theme){
                        echo '<div class="input-checkbox">
                        <input type="checkbox" id="' . $theme['id_theme']  . '" name="' . $theme['name'] . '" value="' . $theme['id_theme'] . '">
                        <label class="input-ttl-label" for="' . $theme['id_theme'] . '">' . $theme['name'] . '</label>
                        </div>';
                    }
                }
                ?>
                <a href="theme.php" class="btn js-theme">Créer un thème</a>
            </fieldset>
            
            <button class='btn'>
                <img class="btn--done" src="img/done-svgrepo-com.svg" alt="<?= $h2;?> la tâche ">
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