<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/srp6_lib.php';
valid_login($action_permission['read']);

//##############################################################################################################
// EDIT USER
//##############################################################################################################
function edit_user(&$sqlr, &$sqlc)
{
    global $output, $lang_edit, $lang_global,
            $mmfpm_db, $characters_db,
            $user_name, $user_id, $expansion_select, $server, $developer_test_mode, $multi_realm_mode;

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    $refguid = $sqlm->result($sqlm->query('SELECT InvitedBy FROM mm_point_system_invites WHERE PlayersAccount = \''.$user_id.'\''), 0, 'InvitedBy');
    $referred_by = $sqlc->result($sqlc->query('SELECT BINARY name AS name FROM characters WHERE guid = \''.$refguid.'\''), 0, 'name');
    unset($refguid);

    if ($acc = $sqlc->fetch_assoc($sqlr->query('SELECT email, SecurityLevel, joindate, expansion, last_ip FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE username = \''.$user_name.'\'')))
    {
        if ($acc['SecurityLevel'] == null)
            $acc['SecurityLevel'] = 0;

        $output .= '
            <center>
                <script type="text/javascript">
                // <![CDATA[
                    function do_submit_data ()
                    {
                        do_submit();
                    }
                // ]]>
                </script>
                <fieldset style="width: 550px;">
                    <legend>'.$lang_edit['edit_acc'].'</legend>
                    <form method="post" action="edit.php?action=doedit_user" name="form">
                            <table class="flat">
                                <tr>
                                    <td>'.$lang_edit['id'].'</td>
                                    <td>'.$user_id.'</td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['username'].'</td>
                                    <td>'.$user_name.'</td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['password'].'</td>
                                    <td><input type="password" name="pass" size="42" maxlength="40" placeholder="(unchanged)" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['mail'].'</td>
                                    <td><input type="text" name="mail" size="42" maxlength="225" value="'.$acc['email'].'" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['invited_by'].':</td>
                                    <td>';
        if ($referred_by == NULL)
          $output .= '
                                        <input type="text" name="referredby" size="42" maxlength="12" value="'.$referred_by.'" />';
        else
            $output .= '
                                        '.$referred_by.'';
        $output .= '
                                    </td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['gm_level'].'</td>
                                    <td>'.id_get_gm_level($acc['SecurityLevel']).' ( '.$acc['SecurityLevel'].' )</td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['join_date'].'</td>
                                    <td>'.$acc['joindate'].'</td>
                                </tr>
                                <tr>
                                    <td>'.$lang_edit['last_ip'].'</td>
                                    <td>'.$acc['last_ip'].'</td>
                                </tr>';
        if ($expansion_select)
        {
            $output .= '
                                <tr>
                                    <td >'.$lang_edit['client_type'].':</td>
                                    <td>
                                        <select name="expansion">
                                            <option value="2" ';
            if($acc['expansion'] == 2)
                $output .= 'selected="selected"';
            $output .= '>'.$lang_edit['wotlk'].'</option>
                            <option value="1" ';
            if($acc['expansion'] == 1)
                $output .= 'selected="selected"';
            $output .= '>'.$lang_edit['tbc'].'</option>
                            <option value="0" ';
            if($acc['expansion'] == 0)
                $output .= 'selected="selected"';
            $output .= '>'.$lang_edit['classic'].'</option>
                                        </select>
                                    </td>
                                </tr>';
        }
        $output .= '
                                <tr>
                                    <td>'.$lang_edit['tot_chars'].'</td>
                                    <td>'.$sqlr->result($sqlr->query('SELECT SUM(numchars) FROM realmcharacters WHERE acctid = '.$user_id.''), 0).'</td>
                                </tr>';
        $realms = $sqlr->query('SELECT id, name FROM realmlist');
        if ( $developer_test_mode && $multi_realm_mode && ( 1 < $sqlr->num_rows($realms) && (1 < count($server)) && (1 < count($characters_db)) ) )
        {
            while ($realm = $sqlr->fetch_assoc($realms))
            {
                $sqlc->connect($characters_db[$realm['id']]['addr'], $characters_db[$realm['id']]['user'], $characters_db[$realm['id']]['pass'], $characters_db[$realm['id']]['name']);
                $result = $sqlc->query('SELECT guid, BINARY name AS name, race, class, level, gender FROM characters WHERE account = '.$user_id.'');
                $result = $sqlc->query('SELECT characters.guid, BINARY characters.name AS name, race, class, level, gender, guild.name AS gname FROM characters LEFT JOIN guild_member
                                        ON characters.guid = guild_member.guid LEFT JOIN guild ON guild_member.guildid = guild.guildid WHERE account = '.$user_id.'');

                $output .= '
                                <tr>
                                    <td>'.$lang_edit['characters'].' '.$realm['name'].'</td>
                                    <td>'.$sqlc->num_rows($result).'</td>
                                </tr>';

                while ($char = $sqlc->fetch_assoc($result))
                {
                    $output .= '
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---></td>
                                    <td>
                                        <a href="char.php?id='.$char['guid'].'&amp;realm='.$realm['id'].'">'.$char['name'].' -
                                            <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                                            <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt=""/> - lvl '.char_get_level_color($char['level']).'
                                        </a> - '.$char['gname'].'
                                    </td>
                                </tr>';
                }
            }
            unset($realm);
        }
        else
        {
            $result = $sqlc->query('SELECT guid, BINARY name AS name, race, class, level, gender FROM characters WHERE account = '.$user_id.'');
            $result = $sqlc->query('SELECT characters.guid, BINARY characters.name AS name, race, class, level, gender, guild.name AS gname FROM characters LEFT JOIN guild_member
                                    ON characters.guid = guild_member.guid LEFT JOIN guild ON guild_member.guildid = guild.guildid WHERE account = '.$user_id.'');

            $output .= '
                                <tr>
                                    <td>'.$lang_edit['characters'].'</td>
                                    <td>'.$sqlc->num_rows($result).'</td>
                                </tr>';
            while ($char = $sqlc->fetch_assoc($result))
            {
//                var_dump($char);die;
                $output .= '
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---></td>
                                    <td>
                                        <a href="char.php?id='.$char['guid'].'">'.$char['name'].' -
                                            <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                                            <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt=""/> - lvl '.char_get_level_color($char['level']).'
                                        </a> - '.$char['gname'].'
                                    </td>
                                </tr>';
            }
        }
        unset($result);
        unset($realms);
        $output .= '
                                <tr>
                                    <td>';

        makebutton($lang_edit['update'], 'javascript:do_submit_data()" type="wrn', 130);

        $output .= '
                                    </td>
                                    <td>';

        makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);

        $output .= '
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br />
                    <fieldset style="width: 550px;">
                        <legend>'.$lang_edit['theme_options'].'</legend>
                            <table class="hidden" style="width: 450px;">
                                <tr>
                                    <td align="left">'.$lang_edit['select_layout_lang'].' :</td>
                                    <td align="right">
                                        <form action="edit.php" method="get" name="form1">
                                            <input type="hidden" name="action" value="lang_set" />
                                            <select name="lang">
                                                <optgroup label="'.$lang_edit['language'].'">';
        if (is_dir('./lang'))
        {
            if ($dh = opendir('./lang'))
            {
                while (($file = readdir($dh)) == true)
                {
                    $lang = explode('.', $file);
                    if(isset($lang[1]) && $lang[1] == 'php')
                    {
                        $output .= '
                                                    <option value="'.$lang[0].'"';
                        if (isset($_COOKIE['lang']) && ($_COOKIE['lang'] == $lang[0]))
                            $output .= ' selected="selected" ';
                        $output .= '>'.$lang[0].'</option>';
                    }
                }
                closedir($dh);
            }
        }
        $output .= '
                                                </optgroup>
                                            </select>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </form>
                                    </td>
                                    <td>';

        makebutton($lang_edit['save'], 'javascript:do_submit(\'form1\',0)', 130);

        $output .= '
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">'.$lang_edit['select_theme'].' :</td>
                                    <td align="right">
                                        <form action="edit.php" method="get" name="form2">
                                            <input type="hidden" name="action" value="theme_set" />
                                            <select name="theme">
                                                <optgroup label="'.$lang_edit['theme'].'">';
        if (is_dir('./themes'))
        {
            if ($dh = opendir('./themes'))
            {
                while (($file = readdir($dh)) == true)
                {
                    if (($file == '.') || ($file == '..') || ($file == '.htaccess') || ($file == 'index.html') || ($file == '.svn'));
                    else
                    {
                        $output .= '
                                                    <option value="'.$file.'"';
                        if (isset($_COOKIE['theme']) && ($_COOKIE['theme'] == $file))
                            $output .= ' selected="selected" ';
                        $output .= '>'.$file.'</option>';
                    }
                }
                closedir($dh);
            }
        }
        $output .= '
                                                </optgroup>
                                            </select>&nbsp;&nbsp;&nbsp;&nbsp;
                                        </form>
                                    </td>
                                    <td>';

        makebutton($lang_edit['save'], 'javascript:do_submit(\'form2\',0)', 130);

        $output .= '
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                        <br />
                    </center>';
    }
    else
        error($lang_global['err_no_records_found']);
}


//#############################################################################################################
//  DO EDIT USER
//#############################################################################################################
function doedit_user(&$sqlr, &$sqlc)
{
    global $output, $user_name;

    if ( (empty($_POST['pass'])||($_POST['pass'] === ''))
        && (empty($_POST['mail'])||($_POST['mail'] === ''))
        && (empty($_POST['expansion'])||($_POST['expansion'] === ''))
        && (empty($_POST['referredby'])||($_POST['referredby'] === '')) )
        redirect('edit.php?error=1');

    $new_pass = ''; // this is spliced into a sql query. yes, this is stupid. i am here to make it work, not to make it better.
    if ($_POST['pass'] !== '')
    {
        list($salt, $verifier) = GetSRP6RegistrationData($user_name, $_POST['pass']);
        $new_pass = ('salt=UNHEX(\'' . bin2hex($salt) . '\'), verifier=UNHEX(\'' . bin2hex($verifier) . '\'), ');
    }
    $new_mail = $sqlr->quote_smart(trim($_POST['mail']));
    $new_expansion = $sqlr->quote_smart(trim($_POST['expansion']));
    $referredby = $sqlr->quote_smart(trim($_POST['referredby']));

    //make sure the mail is valid mail format
    if ((valid_email($new_mail)) && (strlen($new_mail) < 225));
    else
        redirect('edit.php?error=2');

    $sqlr->query('UPDATE account SET email = \''.$new_mail.'\', '.$new_pass.' expansion = \''.$new_expansion.'\' WHERE username = \''.$user_name.'\'');

    if (doupdate_referral($referredby, $sqlr, $sqlc) || $sqlr->affected_rows())
        redirect('edit.php?error=3');
    else
        redirect('edit.php?error=4');

}

function doupdate_referral($referredby, &$sqlr, &$sqlc)
{
    global $mmfpm_db, $user_id;

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    if (NULL == $sqlm->result($sqlm->query('SELECT InvitedBy FROM mm_point_system_invites WHERE PlayersAccount = \''.$user_id.'\''), 0))
    {
        $referred_by = $sqlc->result($sqlc->query('SELECT guid FROM characters WHERE name = \''.$referredby.'\''), 0);

        if ($referred_by == NULL);
        else
        {
            $char = $sqlc->result($sqlc->query('SELECT account FROM characters WHERE guid = \''.$referred_by.'\''), 0, 'account');
            $result = $sqlr->result($sqlr->query('SELECT id FROM account WHERE id = \''.$char.'\''), 0, 'id');
            if ($result == $user_id);
            else
            {
                $sqlm->query('INSERT INTO mm_point_system_invites (PlayersAccount, InvitedBy, InviterAccount) VALUES (\''.$user_id.'\', \''.$referred_by.'\', \''.$result.'\')');
                return true;
            }
        }
    }
    return false;
}


//###############################################################################################################
// SET DEFAULT INTERFACE LANGUAGE
//###############################################################################################################
function lang_set()
{
    if (empty($_GET['lang']))
        redirect('edit.php?error=1');
    else
        $lang = addslashes($_GET['lang']);

    if ($lang)
    {
        setcookie('lang', $lang, time()+60*60*24*30*6); //six month
        redirect('edit.php');
    }
    else
        redirect('edit.php?error=1');
}


//###############################################################################################################
// SET DEFAULT INTERFACE THEME
//###############################################################################################################
function theme_set()
{
    if (empty($_GET['theme']))
        redirect('edit.php?error=1');
    else
        $tmpl = addslashes($_GET['theme']);

    if ($tmpl)
    {
        setcookie('theme', $tmpl, time()+3600*24*30*6); //six month
        redirect('edit.php');
    }
    else
        redirect('edit.php?error=1');
}


//###############################################################################################################
// MAIN
//###############################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

$lang_edit = lang_edit();

if(1 ==  $err)
    $output .= '
            <h1>
                <font class="error\">'.$lang_global['empty_fields'].'</font>
            </h1>';
else if(2 == $err)
    $output .= '
            <h1>
                <font class="error\">'.$lang_edit['use_valid_email'].'</font>
            </h1>';
else if(3 == $err)
    $output .= '
            <h1>
                <font class="error\">'.$lang_edit['data_updated'].'</font>
            </h1>';
else if(4 == $err)
    $output .= '
            <h1>
                <font class="error\">'.$lang_edit['error_updating'].'</font>
            </h1>';
else if(5 == $err)
    $output .= '
            <h1>
                <font class="error\">'.$lang_edit['del_error'].'</font>
            </h1>';
else
    $output .= '
            <h1>'.$lang_edit['edit_your_acc'].'</h1>';

unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('doedit_user' == $action)
    doedit_user($sqlr, $sqlc);
else if('lang_set' == $action)
    lang_set();
else if('theme_set' == $action)
    theme_set();
else
    edit_user($sqlr, $sqlc);

unset($action);
unset($action_permission);
unset($lang_edit);

require_once 'footer.php';


?>
