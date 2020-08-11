<?php

require_once 'header.php';
require_once 'libs/srp6_lib.php';

//#############################################################################
// Login
//#############################################################################
function dologin(&$sqlr)
{
    global $mmfpm_db, $require_account_verify;

    if (empty($_POST['user']) || empty($_POST['pass']))
        redirect('login.php?error=2');

    $user_name  = $sqlr->quote_smart($_POST['user']);
    $user_pass  = $sqlr->quote_smart($_POST['pass']);

    if (255 < strlen($user_name) || 255 < strlen($user_pass))
        redirect('login.php?error=1');

    $result = $sqlr->query('SELECT account.id AS id, username, salt, verifier, SecurityLevel FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE username = \''.$user_name.'\'');
    if ($require_account_verify)
    {
        $sql2 = new SQL;
        $sql2->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $query2_result = $sql2->query("SELECT * FROM mm_account WHERE username = '$user_name'");
        if ($sql2->num_rows($query2_result) >= 1)
        {
            $sql2->close;
            redirect('login.php?error=7');
        }
    }

    unset($user_name);

    if (1 == $sqlr->num_rows($result))
    {
        $info = $sqlr->fetch_assoc($result);
        if (!VerifySRP6Login($_POST['user'], $_POST['pass'], $info['salt'], $info['verifier']))
            redirect('login.php?error=1');
        else
        {
            $id = $info['id'];
            if ($sqlr->result($sqlr->query('SELECT count(*) FROM account_banned WHERE id = '.$id.' AND active = \'1\''), 0))
                redirect('login.php?error=3');
            else
            {
                $_SESSION['user_id'] = $id;
                $_SESSION['uname'] = $info['username'];
                
                if ($info['SecurityLevel'] == NULL)
                    $_SESSION['user_lvl'] = 0;
                else
                    $_SESSION['user_lvl'] = $info['SecurityLevel'];
                
                $_SESSION['realm_id'] = $sqlr->quote_smart($_POST['realm']);
                $_SESSION['client_ip'] = (isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
                $_SESSION['logged_in'] = true;
                if (isset($_POST['remember']) && $_POST['remember'] != '')
                    setcookie(session_name(), session_id(), time()+60*60*24*7);
                redirect('index.php');
            }
        }
    }
    else
        redirect('login.php?error=1');
}

//#################################################################################################
// Print login form
//#################################################################################################
function login(&$sqlr)
{
    global $output, $lang_login, $characters_db, $server, $remember_me_checked, $enable_captcha;;

    $output .= '
                <center>
                    <script type="text/javascript">
                    // <![CDATA[
                    function dologin ()
                    {
                        do_submit();
                    }
                    // ]]>
                    </script>
                    <fieldset class="half_frame">
                        <legend>'.$lang_login['login'].'</legend>
                        <form method="post" action="login.php?action=dologin" name="form" onsubmit="return dologin()">
                            <table class="hidden">
                                <tr>
                                    <td>
                                        <hr />
                                    </td>
                                </tr>
                                <tr align="right">
                                    <td>'.$lang_login['username'].' : <input type="text" name="user" size="24" maxlength="16" /></td>
                                </tr>
                                <tr align="right">
                                    <td>'.$lang_login['password'].' : <input type="password" name="pass" size="24" maxlength="40" /></td>
                                </tr>';

    $result = $sqlr->query('SELECT id, name FROM realmlist ORDER BY id ASC LIMIT 10');

    if ($sqlr->num_rows($result) > 1 && (count($server) > 1) && (count($characters_db) > 1))
    {
        $output .= '
                                <tr align="right">
                                    <td>'.$lang_login['select_realm'].' :
                                        <select name="realm">';
        while ($realm = $sqlr->fetch_assoc($result))
            if(isset($server[$realm['id']]))
                $output .= '
                                            <option value="'.$realm['id'].'">'.htmlentities($realm['name']).'</option>';
        $output .= '
                                        </select>
                                    </td>
                                </tr>';
    }
    else
        $output .= '
                                <input type="hidden" name="realm" value="'.$sqlr->result($result, 0, 'id').'" />';
    $output .= '
                                <tr>
                                    <td>
                                    </td>
                                </tr>
                                <tr align="right">
                                    <td>'.$lang_login['remember_me'].' : <input type="checkbox" name="remember" value="1"';
    if ($remember_me_checked)
        $output .= ' checked="checked"';
    $output .= ' />                 </td>';
    if ($enable_captcha == true)
        $output .= '<tr><td><img src="libs/captcha/CaptchaSecurityImages.php"><br><br></td></tr>
                                <tr><td>'.$lang_login['security_code'].':<input type="text" name="security_code" size="45" value="Code Above ^^"><br></td></tr>
                                <tr align="right"><td width="290"><input type="submit" value="" style="display:none" /></tr>';
    $output .= '                </tr>
                                <tr>
                                    <td>
                                    </td>
                                </tr>
                                <tr align="right">
                                    <td width="290">
                                        <input type="submit" value="" style="display:none" />';

    makebutton($lang_login['not_registrated'], 'register.php" type="wrn', 130);
    makebutton($lang_login['login'], 'javascript:dologin()" type="def', 130);

    $output .= '
                                    </td>
                                </tr>
                                <tr align="center">
                                    <td><a href="register.php?action=pass_recovery">'.$lang_login['pass_recovery'].'</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <hr />
                                    </td>
                                </tr>
                            </table>
                            <script type="text/javascript">
                            // <![CDATA[
                                document.form.user.focus();
                            // ]]>
                            </script>
                        </form>
                        <br />
                    </fieldset>
                    <br /><br />
                </center>';
}
//#################################################################################################
// MAIN
//#################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_login = lang_login();

$output .= '
        <div class="top">';

if (1 == $err)
    $output .=  '
            <h1>
                <font class="error">'.$lang_login['bad_pass_user'].'</font>
            </h1>';
elseif (2 == $err)
    $output .=  '
            <h1>
                <font class="error">'.$lang_login['missing_pass_user'].'</font>
            </h1>';
elseif (3 == $err)
    $output .=  '
            <h1>
                <font class="error">'.$lang_login['banned_acc'].'</font>
            </h1>';
elseif (5 == $err)
    $output .=  '
            <h1>
                <font class="error">'.$lang_login['no_permision'].'</font>
            </h1>';
elseif (6 == $err)
    $output .=  '
            <h1>
                <font class="error">'.$lang_login['after_registration'].'</font>
            </h1>';
elseif (7 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_login['verify_required'].'</font>
            </h1>';
elseif (8 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_login['invalid_code'].'</font>
            </h1>';

else
    $output .= '<h1>'.$lang_login['enter_valid_logon'].'</h1>';

unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('dologin' === $action)
{
    if (isset($_POST['security_code']) && isset($_SESSION['security_code']))
    {
        if (($_POST['security_code']) != ($_SESSION['security_code']))
            redirect('login.php?error=8');
    }
    else
    {
        dologin($sqlr);
    }
}
else
    login($sqlr);

unset($action);
unset($action_permission);
unset($lang_login);

require_once 'footer.php';

?>
