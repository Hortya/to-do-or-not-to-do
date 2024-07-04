<?php
session_start();
include "./functions.php";
$dbCo = dbcolink();

if (!isset($_REQUEST['do'])) {
    redirectTo('index.php');
}

preventCSRF();


if ($_REQUEST['do'] === 'done') {
    $update = $dbCo->prepare('
        UPDATE `task` SET `is_to_do` = 0, order_ = NULL WHERE `task`.`Id_task` = :id;
        UPDATE `task` SET order_ = order_ -1 WHERE order_ > :order');
    $bindvalues = [
        'id' => intval($_REQUEST['i']),
        'order'=> intval($_REQUEST['order'])
        ];
    $isupdateOK = $update->execute($bindvalues);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été mise dans "done". Bravo !';
    redirectTo();
}
elseif ($_REQUEST['do'] === 'undo') {
    $max = haveMax($dbCo);
    if(is_null($max)){
        $max = 0;
    }
    $update = $dbCo->prepare("UPDATE `task` SET `is_to_do` = 1, order_ = $max + 1 WHERE `task`.`Id_task` = :id;");
    $bindvalues = ['id' => intval($_REQUEST['i'])];
    $isupdateOK = $update->execute($bindvalues);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été mise dans "To do".';
    redirectTo();
}
elseif ($_REQUEST['do'] === 'delete') {
    $query = $dbCo->prepare('SELECT order_ FROM `task` WHERE `task`.`Id_task` = :id;');
    $isqueryOK = $query->execute(['id' => intval($_REQUEST['i'])]);
    $order = $query->fetch();
    $delete = $dbCo->prepare('
        DELETE FROM `task` WHERE `task`.`Id_task` = :id;
        UPDATE `task` SET order_ = _order -1 WHERE order_ > ' . $delete . ';');
    $isinsertOK = $delete->execute(['id' => intval($_REQUEST['i'])]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été supprimé.';
    redirectTo('trash.php');
}
elseif ($_REQUEST['do'] === 'up'){
    if($_REQUEST['order'] == 0){
        $errorList[] = 'Votre tâche est déjà la première.';
    }
    else{   
        $update = $dbCo->prepare('
        UPDATE `task` SET `order_` = order_ + 1 WHERE task.order_ = :order;
        UPDATE `task` SET `order_` = order_ - 1 WHERE `task`.`Id_task` = :id;
        ');
        $isupdateOK = $update->execute(['id' => intval($_REQUEST['i']), 'order' => intval($_REQUEST['order'])]);
        $_SESSION['success'] = 'Votre tâche a bien augmenté en priorité.';
        redirectTo();
    }
}
elseif ($_REQUEST['do'] === 'down'){
    $max = haveMax($dbCo);
    if($_REQUEST['order'] > $max){
        $errorList[] = 'Votre tâche est déjà la dernière.';
    }
    else{
        $update = $dbCo->prepare('
            UPDATE `task` SET `order_` = order_ - 1 WHERE task.order_ = :order;
            UPDATE `task` SET `order_` = order_ + 1 WHERE `task`.`Id_task` = :id;
            ');
        $isupdateOK = $update->execute(['id' => intval($_REQUEST['i']), 'order' => intval($_REQUEST['order'])]);
        $newTask = $dbCo->lastInsertId();
        $_SESSION['success'] = 'Votre tâche a bien diminuer en priorité.';
        redirectTo();
    }

}
if (!empty($errorList)) {
    $_SESSION['error'] = $errorList;
    redirectTo($_SERVER['HTTP_REFERER']);
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
    $max = haveMax($dbCo);
    $insert = $dbCo->prepare('INSERT INTO `task` (`title`, `creation_date`, `order_`) VALUES (:ttl, NOW(), ' . $max + 1 . ');');

    $isinsertOK = $insert->execute(['ttl' => htmlspecialchars($_POST['task_tittle'])]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été crée.';
    redirectTo();
}
