<?php 
include './includes/_config.php';

function getArrayAsHTMLp (array $array, string $pstyle = ''): string
{
    return '<p class = "' . $pstyle .'">' . implode($array) . '</p>';
};



function getTaskList (array $array, bool $trash = false): string
{
    if(!$trash){
    $li = [];
    foreach($array as $value){
        $li[] = '<li class="list__task"><p class = "task">' . $value['title'] . '</p>'
        . '<a href="task.php?do=modifie&i=' . $value['Id_task'] . '" class="btn"><img class="btn--modifie" src="/img/pen-svgrepo-com.svg" alt="modifier la tâche"></a>
        <a href="action.php?do=done&i=' . $value['Id_task'] . '&token=' . $_SESSION['token'] . '" class="btn"><img class="btn--itsdone" src="/img/done-svgrepo-com.svg" alt="valider la tâche"></a></li>';
    };
    return implode($li);}
    else{
    $li = [];
    foreach($array as $value){
        $li[] = '<li class="list__task"><p class = "task">' . $value['title'] . '</p>'
        . '<a href="action.php?do=delete&i=' . $value['Id_task'] . '&token=' . $_SESSION['token'] . '" class="btn js-del"><img class="btn--trash" src="/img/trash-circle-svgrepo-com.svg"></a>
        <a href="action.php?do=undo&i=' . $value['Id_task'] . '&token=' . $_SESSION['token'] . '" class="btn"><img class="btn--undo" src="/img/undo-svgrepo-com.svg" alt=""></a></li>';
    };
    return implode($li);}

}


function dbcolink ()
{
    try {
        $dbCo = new PDO(
            'mysql:host=172.21.0.2;dbname=to_do;charset=utf8',
            'user',
            'password'
        );
        $dbCo->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );
    } catch (Exception $e) {
        die('Unable to connect to the database.
        ' . $e->getMessage());
    }
    return $dbCo;
}

/**
 * Generate a unique token and add it to the user session. 
 *
 * @return void
 */
function token()
{
    if (
        !isset($_SESSION['token'])
        || !isset($_SESSION['tokenExpire'])
        || $_SESSION['tokenExpire'] < time()
    ) {
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        $_SESSION['tokenExpire'] = time() + 60 * 15;
    }
}



/**
 * Verify HTTP referer and token. Redirect with error message.
 *
 * @return void
 */
function preventCSRF(string $redirectUrl = 'index.php'): void
{
    global $globalUrl;

    if (!isset($_SERVER['HTTP_REFERER']) || !(str_contains($_SERVER['HTTP_REFERER'], $globalUrl) || str_contains($_SERVER['HTTP_REFERER'],'task.php'))) {
        $_SESSION['error'] = 'referer';
        redirectTo($redirectUrl);
    }

    if (!isset($_SESSION['token']) || !isset($_REQUEST['token']) || $_SESSION['token'] !== $_REQUEST['token']) {
        $_SESSION['error'] = 'csrf';
        redirectTo($redirectUrl);
    }
}

/**
 * Redirect to the given URL.
 *
 * @param string $url
 * @return void
 */
function redirectTo(string $url = 'index.php'): void
{
    // var_dump('REDIRECT ' . $url);
    header('Location: ' . $url);
    exit;
}