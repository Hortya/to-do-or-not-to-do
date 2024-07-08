<?php
session_start();
include "./functions.php";
include "./includes/_database.php";
include "./includes/_config.php";

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
    $_SESSION['success'] = $success['done'];
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
    $_SESSION['success'] = $success['toDo'];
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
    $_SESSION['success'] = $success['delete'];
    redirectTo('trash.php');
}
elseif ($_REQUEST['do'] === 'up'){
    if($_REQUEST['order'] == 0){
        $errorList[] = $errors['first'];
    }
    else{   
        $update = $dbCo->prepare('
        UPDATE `task` SET `order_` = order_ + 1 WHERE task.order_ = :order;
        UPDATE `task` SET `order_` = order_ - 1 WHERE `task`.`Id_task` = :id;
        ');
        $isupdateOK = $update->execute(['id' => intval($_REQUEST['i']), 'order' => intval($_REQUEST['order'])]);
        $_SESSION['success'] = $success['upPriority'];
        redirectTo();
    }
}
elseif ($_REQUEST['do'] === 'down'){
    $max = haveMax($dbCo);
    if($_REQUEST['order'] > $max){
        $errorList[] = $errors['last'];
    }
    else{
        $update = $dbCo->prepare('
            UPDATE `task` SET `order_` = order_ - 1 WHERE task.order_ = :order;
            UPDATE `task` SET `order_` = order_ + 1 WHERE `task`.`Id_task` = :id;
            ');
        $isupdateOK = $update->execute(['id' => intval($_REQUEST['i']), 'order' => intval($_REQUEST['order'])]);
        $newTask = $dbCo->lastInsertId();
        $_SESSION['success'] = $success['downPriority'];
        redirectTo();
    }

}
elseif ($_POST['do'] === 'modifie') {
    $errorList = userInputError($_POST['task_tittle'], 2, 150);
    if($errorList.length < 1 ){
        $update = $dbCo->prepare('UPDATE `task` SET `title` = :ttl WHERE `task`.`Id_task` = ' . $_POST['id'] . '; ');
        $isupdateOK = $update->execute(['ttl' => htmlspecialchars($_POST['task_tittle'])]);
        $newTask = $dbCo->lastInsertId();
        $_SESSION['success'] = $success['modifie'];
        redirectTo();
    }
} elseif ($_POST['do'] === 'create') {
    $errorList = userInputError($_POST['task_tittle'], 2, 150);
    if($errorList.length < 1 ){
        $max = haveMax($dbCo);
        $insert = $dbCo->prepare('INSERT INTO `task` (`title`, `creation_date`, `order_`) VALUES (:ttl, NOW(), ' . $max + 1 . ');');
        $isinsertOK = $insert->execute(['ttl' => htmlspecialchars($_POST['task_tittle'])]);
        $newTask = $dbCo->lastInsertId();
        $_SESSION['success'] = $success['create'];
        redirectTo();}
    }
elseif ($_REQUEST['do'] === 'theme'){
    $errorList = userInputError($_POST['name'], 2, 150);
    if($errorList.length < 1 ){
        if(!isHexColor($_POST['themecolor'])){
            $errorList[] = $errors['color'];
        }
        else {
            $insert = $dbCo->prepare('INSERT INTO `theme` (`name`, `color`) VALUES (:name, :color);');
            $isinsertOK = $insert->execute([
                'name' => htmlspecialchars($_POST['themename']),
                'color' => $_POST['themecolor']]);
                redirectTo();
            }}
}

if (!empty($errorList)) {
    $_SESSION['error'] = $errorList;
    redirectTo($_SERVER['HTTP_REFERER']);
}
