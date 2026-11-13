<?php

define("HEADER_LOADED", true);

// Check if gzip is installed, if yes -> activate gzip compression
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

$time_start = microtime(true);
// resuming login session if available, or start new one
if (ini_get('session.auto_start'));
else
{
    // SameSite keeps the session cookie off cross-site POSTs; JS never needs it
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_httponly', '1');
    session_start();
}

//---------------------Load Default and User Configuration---------------------
if (file_exists('./scripts/config.php'))
    require_once './scripts/config.php';
else
    exit('<center><br><code>Configuration file \'scripts/config.php\'</code> not found,<br>
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
    $_POST[$key] = $sqlm->quote_smart($value);

foreach ($_GET as $key => $value)
    $_GET[$key] = $sqlm->quote_smart($value);

foreach ($_COOKIE as $key => $value)
    $_COOKIE[$key] = $sqlm->quote_smart($value);

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
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$output .= '
<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>'.$title.'</title>
        <meta charset='.$site_encoding.' />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" type="text/css" href="themes/'.$theme.'/'.$theme.'_1024.css" />
        <link rel="stylesheet" type="text/css" media="screen and (min-width: 1025px)" href="themes/'.$theme.'/'.$theme.'_1280.css" />
        <link rel="stylesheet" type="text/css" media="screen and (min-width: 1400px)" href="themes/'.$theme.'/'.$theme.'_1920.css" />
        <link rel="icon" href="img/favicon.ico" />
        <script type="text/javascript"></script>
        <script type="text/javascript" src="libs/js/general.js"></script>
        <script type="text/javascript" src="libs/js/layout.js?v=2"></script>
    </head>

    <body onload="dynamicLayout();">
        <center>
            <table class="table_top">
                <tr>
                    <td class="table_top_left" valign="top">';
unset($title);

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

//---------------------Page permissions----------------------------------------
// resolved for every request, so the err display flag cannot skip it.
// the first included file is the script PHP actually runs; PHP_SELF can carry path-info.
$lookup_file = basename(get_included_files()[0]);
$action_permission = [];
foreach ($menu_array as $trunk)
    foreach ($trunk[2] as $branch)
        if ($branch[0] === $lookup_file)
            $action_permission = ['read' => $branch[2], 'insert' => $branch[3], 'update' => $branch[4], 'delete' => $branch[5]];
unset($branch, $trunk, $lookup_file);

//---------------------Logged in user re-check---------------------------------
// GM level depends on the realm, re-read it when the realm changes.
// level and ban state are also re-read at most once a minute.
// runs before the err check below, so valid_login() never sees a stale level.
if (!empty($_SESSION['logged_in']))
{
    $lvl_realm = ( isset($_GET['r_id']) ) ? (int)$_GET['r_id'] : $_SESSION['realm_id'];
    if (!isset($_SESSION['lvl_realm'], $_SESSION['lvl_time'])
        || $_SESSION['lvl_realm'] != $lvl_realm || 60 < time() - $_SESSION['lvl_time'])
    {
        if (!$sqlr->num_rows($sqlr->query('SELECT id FROM account WHERE id = '.(int)$_SESSION['user_id'].' AND id NOT IN (SELECT id FROM account_banned WHERE active = 1)')))
        {
            session_destroy();
            redirect('login.php');
        }
        $_SESSION['user_lvl'] = get_user_lvl($sqlr, $_SESSION['user_id'], $lvl_realm);
        $_SESSION['lvl_realm'] = $lvl_realm;
        $_SESSION['lvl_time'] = time();
    }
    unset($lvl_realm);
}

//----Check if a user has login, if Guest mode is enabled, code above will login as Guest
if (isset($_SESSION['user_lvl']) && isset($_SESSION['uname']) && isset($_SESSION['realm_id']) && empty($_GET['err']))
{
    // resuming logged in user settings
    session_regenerate_id();
    $user_lvl = $_SESSION['user_lvl'];
    $user_name = $_SESSION['uname'];
    $user_id = $_SESSION['user_id'];
    $realm_id = ( isset($_GET['r_id']) ) ? (int)$_GET['r_id'] : $_SESSION['realm_id'];
    // for MiniManager security system, getting the users' account group name
    $user_lvl_name = id_get_gm_level($user_lvl);

    //---------------------Top Menu----------------------------------------------
    $output .= '
                    <div id="menuwrapper">
                        <ul id="menubar">';
    $lang_header = lang_header();
    foreach ($menu_array as $trunk)
    {
        // skip the "invisible" array, it only sets permissions for pages not in the navbar
        if ('invisible' !== $trunk[1])
        {
            $output .= '
                            <li><a href="'.$trunk[0].'">'.(isset($lang_header[$trunk[1]]) ? $lang_header[$trunk[1]] : $trunk[1]).'</a>';
            if(isset($trunk[2][0]))
                $output .= '
                    <ul>';
            foreach ($trunk[2] as $branch)
            {
                if ($user_lvl >= $branch[2])
                    {
                    list($b_url, $b_lang) = $branch;
                    $b_target = (strpos($b_url, 'map') !== false) ? ' target="_blank" rel="noopener noreferrer" onclick="window.open(this.href, \'wow_player_map\', \'width=1050,height=750,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no\'); return false;"' : '';
                    $output .= '
                            <li><a href="'.$b_url.'"'.$b_target.'>'.(isset($lang_header[$b_lang]) ? $lang_header[$b_lang] : $b_lang).'</a></li>';
                }
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
                            <li><a href="realm.php?action=set_def_realm&amp;id='.$realm['id'].'&amp;url='.urlencode($_SERVER['SCRIPT_NAME']).'">'.htmlentities($set.' '.$realm['name']).'</a></li>';
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
        $result = $sqlc->query('SELECT guid, BINARY name AS name, race, class, level, gender
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
