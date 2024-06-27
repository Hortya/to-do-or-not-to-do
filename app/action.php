<?php
session_start();
include "./functions.php";
$dbCo = dbcolink();

if (!isset($_REQUEST['do'])) {
    redirectTo('index.php');
}

preventCSRF();

if ($_REQUEST['do'] === 'done') {
    $update = $dbCo->prepare('UPDATE `task` SET `is_to_do` = 0 WHERE `task`.`Id_task` = :id;');
    $bindvalues = ['id' => intval($_REQUEST['i'])];
    $isupdateOK = $update->execute($bindvalues);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été mise dans "done". Bravo !';
    redirectTo();}
elseif ($_REQUEST['do'] === 'undo') {
    $update = $dbCo->prepare('UPDATE `task` SET `is_to_do` = 1 WHERE `task`.`Id_task` = :id;');
    $bindvalues = ['id' => intval($_REQUEST['i'])];
    $isupdateOK = $update->execute($bindvalues);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été mise dans "To do".';
    redirectTo();
} elseif ($_REQUEST['do'] === 'delete') {
    $insert = $dbCo->prepare('DELETE FROM `task` WHERE `task`.`Id_task` = :id');
    $isinsertOK = $insert->execute(['id' => intval($_REQUEST['i'])]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été supprimé.';
    redirectTo('trash.php');
}

$errorList = [];
if (!isset($_POST['task_tittle'])) {
    $errorList[] = 'Veuillez saissir le texte de votre tâche.';
}
if (strlen($_POST['task_tittle']) < 2) {
    $errorList[] = 'Veuillez saissir un texte de plus de 1 caractère pour votre tâche.';
}
if (strlen($_POST['task_tittle']) > 150) {
    $errorList[] = 'Veuillez saissir un texte de moins de 150 caractère pour votre tâche.';
}

if (!empty($errorList)) {
    $_SESSION['error'] = $errorList;
    redirectTo($_SERVER['HTTP_REFERER']);
}


if ($_POST['do'] === 'modifie') {
    $update = $dbCo->prepare('UPDATE `task` SET `title` = :ttl WHERE `task`.`Id_task` = ' . $_POST['id'] . '; ');
    $isupdateOK = $update->execute(['ttl' => htmlspecialchars($_POST['task_tittle'])]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été modifiée.';
    redirectTo();
} elseif ($_POST['do'] === 'create') {
    $insert = $dbCo->prepare('INSERT INTO `task` (`title`, `creation_date`) VALUES (:ttl, NOW());');

    $isinsertOK = $insert->execute(['ttl' => htmlspecialchars($_POST['task_tittle'])]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été crée.';
    redirectTo();
}
