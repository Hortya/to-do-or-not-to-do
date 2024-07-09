<?php 
include './includes/_config.php';

function getArrayAsHTMLp (array $array, string $pstyle = ''): string
{
    return '<p class = "' . $pstyle .'">' . implode($array) . '</p>';
};


/**
 * Create a html task list depending on the location (index or trash)
 *
 * @param array $array the database array
 * @param boolean $trash if trash set true / if index set false
 * @return string the html task list
 */
function getTaskList (array $array, bool $trash = false): string
{
    if(!$trash){
    $li = [];
    foreach($array as $value){
        $li[] = '<li class="list__task">
        <form action="action.php" method="post">
            <input type="hidden" name="token" value="' . $_SESSION['token'] . '">
            <input type="hidden" name="i" value="' . $value['Id_task'] . '">
            <input type="hidden" name="order" value="' . $value['order_'] - 1 . '">
            <input type="hidden" name="do" value="up">
            <button class="btn">
            <img class="btn--up" src="img/up.svg" alt="augmenter la priorité de la tâche">
            </button>
        </form>
        <p class="task__order">' . $value['order_'] . '</p>
        <form action="action.php" method="post">
            <input type="hidden" name="token" value="' . $_SESSION['token'] . '">
            <input type="hidden" name="i" value="' . $value['Id_task'] . '">
            <input type="hidden" name="order" value="' . $value['order_'] + 1 . '">
            <input type="hidden" name="do" value="down">
            <button class="btn">
                <img class="btn--down" src="img/down.svg" alt="diminuer la priorité de la tâche">
            </button>
        </form>
        <p class = "task">' . $value['title'] . '</p>'
        . '<p class = date>' . displayNotNull($value['deadline'])  . '</p>'
        . '<a href="task.php?do=modifie&i=' . $value['Id_task'] . '" class="btn"><img class="btn--modifie" src="/img/pen-svgrepo-com.svg" alt="modifier la tâche"></a>
        <a href="action.php?do=done&i=' . $value['Id_task'] . '&token=' . $_SESSION['token'] . '&order=' . $value['order_'] . '" class="btn"><img class="btn--itsdone" src="/img/done-svgrepo-com.svg" alt="valider la tâche"></a></li>';
    };
    return implode($li);}
    else{
    $li = [];
    foreach($array as $value){
        $li[] = '<li class="list__task"><p class = "task">' . $value['title'] . '</p>'
        . '<button type="button" data-id-delete="' . $value['Id_task'] . '" class="btn js-del"><img class="btn--trash" src="/img/trash-circle-svgrepo-com.svg"></a>
        <a href="action.php?do=undo&i=' . $value['Id_task'] . '&token=' . $_SESSION['token'] . '" class="btn"><img class="btn--undo" src="/img/undo-svgrepo-com.svg" alt=""></a></li>';
    };
    return implode($li);}

}



function displayNotNull(string | null $value): string{
    if(is_null($value)){
        return '';
    }
    else return $value;
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

/**
 * Obtain the max order from database
 *
 * @param object $db The database connection
 * @return int | NULL The max order value (or NULL if there is not order value)
 */
function haveMax(object $db): int | NULL
{
    $query = $db->prepare('SELECT MAX(order_) FROM task');
    $query->execute();
    return $query->fetchColumn();
}