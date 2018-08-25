<?php
// Check if gzip is installed, if yes -> activate gzip compression
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

$time_start = microtime(true);
// resuming login session if available, or start new one
if (ini_get('session.auto_start'));
else
    session_start();

//---------------------Load Default and User Configuration---------------------
if (file_exists('./scripts/config.php'))
{
    if (file_exists('./scripts/config.dist.php'))
        require_once './scripts/config.dist.php';
    else
        exit('<center><br><code>\'scripts/config.dist.php\'</code> not found,<br>
            please restore <code>\'scripts/config.dist.php\'</code></center>');
    require_once './scripts/config.php';
}
else
    exit('<center><br><code>\'scripts/config.php\'</code> not found,<br>
        please copy <code>\'scripts/config.dist.php\'</code> to
        <code>\'scripts/config.php\'</code> and make appropriate changes.');

//---------------------Error reports for Debugging-----------------------------
if ($debug)
    $tot_queries = 0;
if (1 < $debug)
    error_reporting (E_ALL);
else
    error_reporting (E_COMPILE_ERROR);

//---------------------Security Fix + load SQLLib----------------

require_once 'libs/db_lib.php';

// Try to globally fix security vulnerabilities (very dirty way..)
require_once 'libs/valid_lib.php';

$sqlm = new SQL; //mysql_real_escape_string needs a sql connection
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

foreach ($_POST as $key => $value)
    $_POST[$key] = cleanSQL($value);

foreach ($_GET as $key => $value)
    $_GET[$key] = cleanSQL($value);

foreach ($_COOKIE as $key => $value)
    $_COOKIE[$key] = cleanSQL($value);

$sqlm->close();
unset($sqlm);
// End

//---------------------Loading User Theme and Language Settings----------------
if (isset($_COOKIE['theme']))
    if (is_dir('themes/'.$_COOKIE['theme']))
        if (is_file('themes/'.$_COOKIE['theme'].'/'.$_COOKIE['theme'].'_1024.css'))
            $theme = $_COOKIE['theme'];

if (isset($_COOKIE['lang']))
{
    $lang = $_COOKIE['lang'];
    if (file_exists('lang/'.$lang.'.php'));
    else
        $lang = $language;
}
else
    $lang = $language;

//---------------------Loading Libraries---------------------------------------
require_once 'lang/'.$lang.'.php';
require_once 'libs/lang_lib.php';

require_once 'libs/data_lib.php';
require_once 'libs/global_lib.php';
require_once 'scripts/get_lib.php';

//---------------------Initialize Libraries------------------------------------
// $lang_lib = new lang($lang, $lang_global); // Not used yet

//---------------------Headers' header-----------------------------------------
// sets encoding defined in config for language support
header('Content-Type: text/html; charset='.$site_encoding);
header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
$output .= '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>'.$title.'</title>
        <meta http-equiv="Content-Type" content="text/html; charset='.$site_encoding.'" />
        <meta http-equiv="Content-Type" content="text/javascript; charset='.$site_encoding.'" />
        <meta charset="utf8" />
        <link rel="stylesheet" type="text/css" href="themes/'.$theme.'/'.$theme.'_1024.css" title="1024" />
        <link rel="stylesheet" type="text/css" href="themes/'.$theme.'/'.$theme.'_1280.css" title="1280" />
        <link rel="SHORTCUT ICON" href="img/favicon.ico" />
        <script src="https://www.wowhead.com/widgets/power.js"></script>
        <script type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="libs/js/general.js"></script>
        <script type="text/javascript" src="libs/js/layout.js"></script>
    </head>

    <body onload="dynamicLayout();">
        <center>
            <table class="table_top">
                <tr>
                    <td class="table_top_left" valign="top">';
unset($title);

// Add more memory to PHP if needed by MM
    if (ini_get('memory_limit') < 16)
        @ini_set('memory_limit', '16M');

//---------------------Guest login Predefines----------------------------------
if ($allow_anony && empty($_SESSION['logged_in']))
{
    $_SESSION['user_lvl'] = -1;
    $_SESSION['uname'] = $anony_uname;
    $_SESSION['user_id'] = -1;
    $_SESSION['realm_id'] = $anony_realm_id;
    $_SESSION['client_ip'] = ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
}

$sqlr = new SQL;
$sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

//----Check if a user has login, if Guest mode is enabled, code above will login as Guest
if (isset($_SESSION['user_lvl']) && isset($_SESSION['uname']) && isset($_SESSION['realm_id']) && empty($_GET['err']))
{
    // check for host php script max memory allowed,
    // setting it higher if it is not enough for MiniManager to run
    if (ini_get('memory_limit') < 16)
        @ini_set('memory_limit', '16M');

    // resuming logged in user settings
    session_regenerate_id();
    $user_lvl = $_SESSION['user_lvl'];
    $user_name = $_SESSION['uname'];
    $user_id = $_SESSION['user_id'];
    $realm_id = ( isset($_GET['r_id']) ) ? (int)$_GET['r_id'] : $_SESSION['realm_id'];
    // for MiniManager security system, getting the users' account group name
    $user_lvl_name = id_get_gm_level($user_lvl);

    // get the file name that called this header
    $array = explode ( '/', $_SERVER['PHP_SELF']);
    $lookup_file = $array[sizeof($array)-1];
    unset($array);

    //---------------------Top Menu----------------------------------------------
    $output .= '
                    <div id="menuwrapper">
                        <ul id="menubar">';
    $lang_header = lang_header();
    $action_permission = array();
    foreach ($menu_array as $trunk)
    {
        // ignore "invisible array" this is for setting security read/write values
        // for not accessible elements not in the navbar!
        if ('invisible' === $trunk[1])
        {
            foreach ($trunk[2] as $branch)
            {
                if($branch[0] === $lookup_file)
                {
                    $action_permission['read']   = $branch[2];
                    $action_permission['insert'] = $branch[3];
                    $action_permission['update'] = $branch[4];
                    $action_permission['delete'] = $branch[5];
                }
            }
        }
        else
        {
            $output .= '
                            <li><a href="'.$trunk[0].'">'.(isset($lang_header[$trunk[1]]) ? $lang_header[$trunk[1]] : $trunk[1]).'</a>';
            if(isset($trunk[2][0]))
                $output .= '
                    <ul>';
            foreach ($trunk[2] as $branch)
            {
                if($branch[0] === $lookup_file)
                {
                    $action_permission['read']   = $branch[2];
                    $action_permission['insert'] = $branch[3];
                    $action_permission['update'] = $branch[4];
                    $action_permission['delete'] = $branch[5];
                }
                if ($user_lvl >= $branch[2])
                    $output .= '
                            <li><a href="'.$branch[0].'">'.(isset($lang_header[$branch[1]]) ? $lang_header[$branch[1]] : $branch[1]).'</a></li>';
            }
            if(isset($trunk[2][0]))
                $output .= '
                        </ul>';

            $output .= '
                    </li>';
        }
    }
    unset($branch);
    unset($trunk);
    unset($lookup_file);
    unset($menu_array);

    $output .= '
                            <li><a href="edit.php">'.$lang_header['my_acc'].'</a>
                        <ul>';

    $result = $sqlr->query('SELECT id, name FROM `realmlist` LIMIT 10');

    // we check how many realms are configured, this does not check if config is valid
    if ((1 < $sqlr->num_rows($result)) && (1 < count($server)) && (1 < count($characters_db)))
    {
        $output .= '
                            <li><a href="#">'.$lang_header['realms'].'</a></li>';
        while ($realm = $sqlr->fetch_assoc($result))
        {
            if(isset($server[$realm['id']]))
            {
                $set = ($realm_id === $realm['id']) ? '>' : '';
                $output .= '
                            <li><a href="realm.php?action=set_def_realm&amp;id='.$realm['id'].'&amp;url='.$_SERVER['PHP_SELF'].'">'.htmlentities($set.' '.$realm['name']).'</a></li>';
            }
        }
        unset($set);
        unset($realm);
    }

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    // we have a different menu for guest account
    if($allow_anony && empty($_SESSION['logged_in']))
    {
        $lang_login = lang_login();
        $output .= '
                            <li><a href="#">'.$lang_header['account'].'</a></li>
                            <li><a href="register.php">'.$lang_login['not_registrated'].'</a></li>
                            <li><a href="login.php">'.$lang_login['login'].'</a></li>';
        unset($lang_login);
    }
    else
    {
        $result = $sqlc->query('SELECT guid, name, race, class, level, gender
                            FROM characters
                            WHERE account = '.$user_id.'');

        // this puts links to user characters of active realm in "My Account" menu
        if($sqlc->num_rows($result))
        {
            $output .= '
                            <li><a href="#">'.$lang_header['my_characters'].'</a></li>';
            while ($char = $sqlc->fetch_assoc($result))
            {
                $output .= '
                            <li>
                                <a href="char.php?id='.$char['guid'].'">
                                <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" alt="" /><img src="img/c_icons/'.$char['class'].'.gif" alt="" />'.
                                $char['name'].'
                                </a>
                            </li>';
            }
            unset($char);
        }
        $output .= '
                            <li><a href="#">'.$lang_header['account'].'</a></li>
                            <li><a href="edit.php">'.$lang_header['edit_my_acc'].'</a></li>
                            <li><a href="logout.php">'.$lang_header['logout'].'</a></li>';
    }
    unset($result);

    $output .= '
                        </ul>
                    </li>
                    </ul>
                    <br class="clearit" />
                    </div>
                    </td>
                    <td class="table_top_middle">
                        <div id="username">'.$user_name.' .:'.$user_lvl_name.'\'s '.$lang_header['menu'].':.</div>
                    </td>
                    <td class="table_top_right"></td>
                </tr>
            </table>';
    unset($lang_header);

    //---------------------Version Information-------------------------------------
    if ( $show_version['show'] && $user_lvl >= $show_version['version_lvl'] )
    {
        if ((1 < $show_version['show']) && $user_lvl >= $show_version['svnrev_lvl'])
        {
            $show_version['svnrev'] = '';
            // if file exists and readable
            if (is_readable('.svn/entries'))
            {
                $file_obj = new SplFileObject('.svn/entries');
                // line 4 is where svn revision is stored
                $file_obj->seek(3);
                $show_version['svnrev'] = $file_obj->current();
                unset($file_obj);
            }
            $output .= '
        <div id="version">
            '.$show_version['version'].' r'.$show_version['svnrev'].'
        </div>';
        }
        else
        {
            $output .= '
        <div id="version">
            '.$show_version['version'].'
        </div>';
        }
    }
}
else
{
    $output .= '
                    </td>
                    <td class="table_top_middle"></td>
                    <td class="table_top_right"></td>
                </tr>
            </table>';
}

//---------------------Start of Body-------------------------------------------
    $output .= '
        <div id="body_main">
            <div class="bubble">';

?>
