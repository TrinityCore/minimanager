<?php

if (ini_get('session.auto_start'));
else
    session_start();

session_destroy();
unset($_SESSION['user_id']);
unset($_SESSION['uname']);
unset($_SESSION['user_lvl']);
unset($_SESSION['realm_id']);
unset($_SESSION['client_ip']);
unset($_SESSION['logged_in']);

if (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false)
{
    header('Location: //'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/login.php');
    exit();
}
else
    die('<meta http-equiv="refresh" content="0;URL=login.php" />');

?>
