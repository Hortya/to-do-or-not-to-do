<?php
session_start();

include 'includes/_config.php';
include 'includes/_functions.php';
include 'includes/_database.php';

header('Content-type:application/json');

if (!isset($_REQUEST['action'])) {
    var_dump('NO ACTION');
    exit;
}

// Delelte task from database
if ($_REQUEST['action'] === 'delete' && isset($_REQUEST['i']) && is_numeric($_REQUEST['i']) && isset($_REQUEST['token'])) {
 
    $delete = $dbCo->prepare('
        DELETE FROM `task` WHERE `task`.`Id_task` = :id;
        ');
    $isDeleteOK = $delete->execute(['id' => intval($_REQUEST['i'])]);
    $_SESSION['success'] = 'Votre tâche a bien été supprimé.';

    $result = [
        'isOk' => $isUpdateOk,
    ];


    if ($isUpdateOk) {
        $result['id'] =  intval($_REQUEST['i']);
    }

    echo json_encode($result);
}