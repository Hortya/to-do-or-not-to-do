<?php
session_start();
include "./functions.php";
include "./includes/_database.php";

if (!isset($_REQUEST['do'])) {
    redirectTo('index.php');
}

preventCSRF();

//When the task is done
if ($_REQUEST['do'] === 'done') {
    try {
        $dbCo->beginTransaction();
        $update = $dbCo->prepare('
            UPDATE `task` SET `is_to_do` = 0, order_ = NULL WHERE `task`.`Id_task` = :id;');
        $bindvalues = [
            'id' => intval($_REQUEST['i'])
        ];
        $isUpdateOk = $update->execute($bindvalues);
        $update = $dbCo->prepare('
            UPDATE `task` SET order_ = order_ -1 WHERE order_ > :order');
        $bindvalues = [
            'order' => intval($_REQUEST['order'])
        ];
        $isUpdateOk = $update->execute($bindvalues);
        $dbCo->commit();
        if ($isUpdateOk) {
            $_SESSION['success'] = 'Votre tâche a bien été mise dans "done". Bravo !';
        } else {
            $errorList[] = 'update KO';
        }
    } catch (Exception $e) {
        $dbCo->rollBack();
        $errorList[] = 'update KO';
    }
    redirectTo();
}
// When the user want to reuse a task
elseif ($_REQUEST['do'] === 'undo') {
    $max = haveMax($dbCo);
    if (is_null($max)) {
        $max = 0;
    }
    $update = $dbCo->prepare("UPDATE `task` SET `is_to_do` = 1, order_ = :max WHERE `task`.`Id_task` = :id;");
    $bindvalues = [
        'id' => intval($_REQUEST['i']),
        'max' => $max + 1
    ];
    $isupdateOK = $update->execute($bindvalues);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été mise dans "To do".';
    redirectTo();
}

// When the user want to rise the task priority
elseif ($_REQUEST['do'] === 'up') {
    if ($_REQUEST['order'] == 0) {
        $errorList[] = 'Votre tâche est déjà la première.';
    }
    else {
        try{
        $dbCo->beginTransaction();
        $update = $dbCo->prepare('
        UPDATE `task` SET `order_` = order_ + 1 WHERE task.order_ = :order;
        ');
        $isUpdateOk = $update->execute(['order' => intval($_REQUEST['order'])]);
        $update = $dbCo->prepare('
        UPDATE `task` SET `order_` = order_ - 1 WHERE `task`.`Id_task` = :id;
        ');
        $isUpdateOk = $update->execute(['id' => intval($_REQUEST['i'])]);
        $dbCo->commit();
        if ($isUpdateOk) {
            $_SESSION['success'] = 'Votre tâche a bien augmenté en priorité.';
        } else {
            $errorList[] = 'update KO';
        }
        }
        catch (Exception $e) {
            $dbCo->rollBack();
            $errorList[] = 'update KO';
        }
        redirectTo();
    }
}
// When the user want to lower the task priority
elseif ($_REQUEST['do'] === 'down') {
    $max = haveMax($dbCo);
    if ($_REQUEST['order'] > $max) {
        $errorList[] = 'Votre tâche est déjà la dernière.';
    } else {
        try{
            $dbCo->beginTransaction();
            $update = $dbCo->prepare('
                UPDATE `task` SET `order_` = order_ - 1 WHERE task.order_ = :order;
                ');
                $isUpdateOk = $update->execute(['order' => intval($_REQUEST['order'])]);
            $update = $dbCo->prepare('
                UPDATE `task` SET `order_` = order_ + 1 WHERE `task`.`Id_task` = :id;
                ');
                $isUpdateOk = $update->execute(['id' => intval($_REQUEST['i'])]);
            $dbCo->commit();
        if ($isUpdateOk) {
            $_SESSION['success'] = 'Votre tâche a bien diminuer en priorité.';
        } else {
            $errorList[] = 'update KO';
        }
        }
        catch (Exception $e) {
            $dbCo->rollBack();
            $errorList[] = 'update KO';
        }
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

// When the user want to modifie a task
if ($_POST['do'] === 'modifie') {
    $update = $dbCo->prepare('UPDATE `task` SET `title` = :ttl WHERE `task`.`Id_task` = :id;');
    $isupdateOK = $update->execute(['ttl' => htmlspecialchars($_POST['task_tittle']),
        'id' => intval($_REQUEST['i']) ]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été modifiée.';
    redirectTo();
}
// When the user want to create a task
elseif ($_POST['do'] === 'create') {
    $max = haveMax($dbCo);
    $insert = $dbCo->prepare('INSERT INTO `task` (`title`, `creation_date`, `order_`, `deadline`) VALUES (:ttl, NOW(), :max, :deadline);');
    $isinsertOK = $insert->execute([
        'ttl' => htmlspecialchars($_POST['task_tittle']),
        'max' => $max + 1,
        'deadline' => $_POST['deadline']
    ]);
    $newTask = $dbCo->lastInsertId();
    $_SESSION['success'] = 'Votre tâche a bien été crée.';
    redirectTo();
}
