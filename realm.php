<?php


require_once 'header.php';
valid_login($action_permission['read']);

//####################################################################################################
// SHOW REALMS
//####################################################################################################
function show_realm(&$sqlr)
{
    global $output, $lang_global, $lang_realm,
        $server, $action_permission, $user_lvl;
    valid_login($action_permission['read']);

    //==========================$_GET and SECURE=================================
    $order_by = (isset($_GET['order_by'])) ? $sqlr->quote_smart($_GET['order_by']) : 'rid';
    if (preg_match('/^[_[:lower:]]{1,8}$/', $order_by));
    else
        $order_by='rid';

    $dir = (isset($_GET['dir'])) ? $sqlr->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir=1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $result = $sqlr->query('SELECT realmlist.id AS rid, name, address, port, icon, flag, timezone,
                            (SELECT SUM(numchars) FROM realmcharacters WHERE realmid = rid) as sum
                            FROM realmlist ORDER BY '.$order_by.' '.$order_dir.'');
    $total_realms = $sqlr->num_rows($result);

    $output .= '
                <center>
                    <table class="top_hidden">
                        <tr>
                            <td>';
    if($user_lvl >= $action_permission['insert'])
        makebutton($lang_realm['add_realm'], 'realm.php?action=add_realm', 130);
    makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
    $output .= '
                            </td>
                            <td align="right">'.$lang_realm['tot_realms'].' : '.$total_realms.'</td>
                        </tr>
                    </table>
                    <table class="lined">
                        <tr>';
    if($user_lvl >= $action_permission['delete'])
        $output .= '
                            <th width="1%">'.$lang_global['delete_short'].'</th>';
    $output .= '
                            <th width="1%"><a href="realm.php?order_by=rid&amp;dir='.$dir.'"'.($order_by==='rid' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['id'].'</a></th>
                            <th width="40%"><a href="realm.php?order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['name'].'</a></th>
                            <th width="1%">'.$lang_realm['online'].'</th>
                            <th width="10%"><a href="realm.php?order_by=sum&amp;dir='.$dir.'"'.($order_by==='sum' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['tot_char'].'</a></th>
                            <th width="10%"><a href="realm.php?order_by=address&amp;dir='.$dir.'"'.($order_by==='address' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['address'].'</a></th>
                            <th width="1%"><a href="realm.php?order_by=port&amp;dir='.$dir.'"'.($order_by==='port' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['port'].'</a></th>
                            <th width="1%"><a href="realm.php?order_by=icon&amp;dir='.$dir.'"'.($order_by==='icon' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['icon'].'</a></th>
                            <th width="1%"><a href="realm.php?order_by=flag&amp;dir='.$dir.'"'.($order_by==='flag' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['flag'].'</a></th>
                            <th width="7%"><a href="realm.php?order_by=timezone&amp;dir='.$dir.'"'.($order_by==='timezone' ? ' class="'.$order_dir.'"' : '').'>'.$lang_realm['timezone'].'</a></th>
                        </tr>';
    unset($dir);
    unset($order_dir);
    unset($order_by);
    $icon_type = get_icon_type();
    $timezone_type = get_timezone_type();

    while ($realm = $sqlr->fetch_assoc($result))
    {
        $output .= '
                        <tr>';
        if($user_lvl >= $action_permission['delete'])
            $output .= '
                            <td><a href="realm.php?action=del_realm&amp;id='.$realm['rid'].'"><img src="img/aff_cross.png" alt="" /></a></td>';
        $output .= '
                            <td>'.$realm['rid'].'</td>';
        if (isset($server[$realm['rid']]['game_port']))
        {
            if($user_lvl >= $action_permission['update'])
                $output .= '
                            <td><a href="realm.php?action=edit_realm&amp;id='.$realm['rid'].'">'.$realm['name'].'</a></td>';
            else
                $output .= '
                            <td>'.$realm['name'].'</td>';
            if (test_port($server[$realm['rid']]['addr'],$server[$realm['rid']]['game_port']))
                $output .= '
                            <td><img src="img/up.gif" alt="" /></td>';
            else
                $output .= '
                            <td><img src="img/down.gif" alt="" /></td>';
        }
        else
        {
            $output .= '
                            <td>';
            if($user_lvl >= $action_permission['update'])
                $output .= '
                                <a href="realm.php?action=edit_realm&amp;id='.$realm['rid'].'">'.$realm['name'].' ('.$lang_realm['notconfigured'].')</a>';
            else
                $output .= ''.
                                $realm['name'].' ('.$lang_realm['notconfigured'].')';
            $output .= '
                            </td>
                            <td>***</td>';
        }
        $output .= '
                            <td>'.$realm['sum'].'</td>
                            <td>'.$realm['address'].'</td>
                            <td>'.$realm['port'].'</td>
                            <td>'.$icon_type[$realm['icon']][1].'</td>
                            <td>'.$realm['flag'].'</td>
                            <td>'.$timezone_type[$realm['timezone']][1].'</td>
                        </tr>';
    }
    unset($realm);
    unset($icon_type);
    unset($timezone_type);
    unset($result);
    $output .= '
                    </table>
                    <br />
                </center>';
}


//####################################################################################################
//  EDIT REALM
//####################################################################################################
function edit_realm(&$sqlr)
{
    global $output, $lang_global, $lang_realm,
            $server, $action_permission, $user_lvl;
    valid_login($action_permission['update']);

    if(empty($_GET['id']))
        redirect('realm.php?error=1');

    $id = $sqlr->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect('realm.php?error=1');

    if ($realm = $sqlr->fetch_assoc($sqlr->query('SELECT realmlist.id AS rid, name, address, port, icon, flag, timezone,
                                                (SELECT SUM(numchars) FROM realmcharacters WHERE realmid = rid) as sum
                                                FROM realmlist WHERE id ='.$id.'')))
    {
        $output .= '
                <center>
                    <fieldset class="half_frame">
                        <legend>'.$lang_realm['edit_realm'].'</legend>
                        <form method="get" action="realm.php" name="form">
                            <input type="hidden" name="action" value="doedit_realm" />
                            <input type="hidden" name="id" value="'.$id.'" />
                            <table class="flat">
                                <tr>
                                    <td>'.$lang_realm['id'].'</td>
                                    <td>'.$realm['rid'].'</td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['name'].'</td>
                                    <td><input type="text" name="new_name" size="40" maxlength="32" value="'.$realm['name'].'" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['address'].'</td>
                                    <td><input type="text" name="new_address" size="40" maxlength="32" value="'.$realm['address'].'" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['port'].'</td>
                                    <td><input type="text" name="new_port" size="40" maxlength="5" value="'.$realm['port'].'" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['icon'].'</td>
                                    <td>
                                        <select name="new_icon">';
        unset($id);
        foreach (get_icon_type() as $icon)
        {
            $output .= '
                                            <option value="'.$icon[0].'" ';
            if ($realm['icon']==$icon[0])
                $output .= 'selected="selected" ';
            $output .= '>'.$icon[1].'</option>';
        }
        unset($icon);
        $output .= '
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['flag'].'</td>
                                    <td><input type="text" name="new_flag" size="40" maxlength="3" value="'.$realm['flag'].'" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['timezone'].'</td>
                                    <td>
                                        <select name="new_timezone">';
        foreach (get_timezone_type() as $zone)
        {
            $output .= '
                                            <option value="'.$zone[0].'" ';
            if ($realm['timezone']==$zone[0])
                $output .= 'selected="selected" ';
            $output .= '>'.$zone[1].'</option>';
        }
        unset($zone);
        $output .= '
                                        </select>
                                    </td>
                                </tr>';
        if (isset($server[$realm['rid']]['game_port']))
        {
            $output .= '
                                <tr>
                                    <td>'.$lang_realm['status'].'</td>
                                    <td>'.(test_port($server[$realm['rid']]['addr'],$server[$realm['rid']]['game_port']) ? $lang_realm['online'] : $lang_realm['offline']).'</td>
                                </tr>
                                <tr>
                                    <td>'.$lang_realm['tot_char'].'</td>
                                    <td>'.$realm['sum'].'</td>
                                </tr>';
        }
        else
            $output .= '
                                <tr>
                                    <td colspan="2">'.$lang_realm['conf_from_file'].'</td>
                                </tr>';
        $output .= '
                                <tr>
                                    <td>';
        if($user_lvl >= $action_permission['delete'])
            makebutton($lang_realm['delete'], 'realm.php?action=del_realm&amp;id='.$realm['rid'].'" type="wrn', 130);
        unset($realm);
        $output .= '
                                    </td>
                                    <td>';

            makebutton($lang_realm['update'], 'javascript:do_submit()', 130);
            makebutton($lang_global['back'], 'realm.php" type="def', 130);

        $output .= '
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br /><br />
                </center>';
    }
    else
        error($lang_global['err_no_result']);
}


//####################################################################################################
//  DO EDIT REALM
//####################################################################################################
function doedit_realm(&$sqlr)
{
    global $action_permission;
    valid_login($action_permission['update']);

    if (empty($_GET['id']) ||
        empty($_GET['new_name']) ||
        empty($_GET['new_address']) ||
        empty($_GET['new_port']) ||
        empty($_GET['new_icon']) ||
        empty($_GET['new_timezone'])
        )
        redirect('realm.php?error=1');

    $id = $sqlr->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect('realm.php?error=1');
    $new_name     = $sqlr->quote_smart($_GET['new_name']);
    $new_address  = $sqlr->quote_smart($_GET['new_address']);
    $new_port     = $sqlr->quote_smart($_GET['new_port']);
    $new_icon     = $sqlr->quote_smart($_GET['new_icon']);
    $new_flag     = $sqlr->quote_smart($_GET['new_flag']);
    $new_timezone = $sqlr->quote_smart($_GET['new_timezone']);

    $query = $sqlr->query('UPDATE realmlist SET name=\''.$new_name.'\', address =\''.$new_address.'\' , port =\''.$new_port.'\', icon =\''.$new_icon.'\', flag =\''.$new_flag.'\', timezone =\''.$new_timezone.'\' WHERE id = '.$id.'');

    unset($new_name);
    unset($new_address);
    unset($new_port);
    unset($new_icon);
    unset($new_flag);
    unset($new_timezone);

    if ($sqlr->affected_rows())
        redirect('realm.php?error=3');
    else
        redirect('realm.php?action=edit_realm&id='.$id.'&error=4');
}


//####################################################################################################
// DELETE REALM
//####################################################################################################
function del_realm(&$sqlr)
{
    global $output, $lang_realm, $lang_global, $action_permission;
    valid_login($action_permission['delete']);

    if(empty($_GET['id']))
        redirect('realm.php?error=1');
    $id = $sqlr->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect('realm.php?error=1');

    $output .= '
                <center>
                    <h1><font class="error">'.$lang_global['are_you_sure'].'</font></h1>
                    <br />
                    <font class="bold">'.$lang_realm['realm_id'].': '.$id.'<br />'.$lang_global['will_be_erased'].'</font>
                    <br /><br />
                    <table width="300" class="hidden">
                        <tr>
                            <td>';

    makebutton($lang_global['yes'], 'realm.php?action=dodel_realm&amp;id='.$id.'" type ="wrn', 130);
    makebutton($lang_global['no'], 'realm.php" type="def', 130);

    unset($id);
    $output .= '
                            </td>
                        </tr>
                    </table>
                </center>';
}


//####################################################################################################
// DO DELETE REALM
//####################################################################################################
function dodel_realm(&$sqlr)
{
    global $action_permission;
    valid_login($action_permission['delete']);

    if(empty($_GET['id']))
        redirect('realm.php?error=1');
    $id = $sqlr->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect('realm.php?error=1');

    $sqlr->query('DELETE FROM realmlist WHERE id = '.$id.'');
    unset($id);

    if ($sqlr->affected_rows())
        redirect('realm.php');
    else
        redirect('realm.php?error=2');
}


//####################################################################################################
//  ADD NEW REALM
//####################################################################################################
function add_realm(&$sqlr)
{
    global $action_permission;
    valid_login($action_permission['insert']);

    if ($sqlr->query('INSERT INTO realmlist (id, name, address, port, icon, flag, timezone)
                        VALUES (NULL,"'.TRINITY.'", "127.0.0.1", 8085 ,0 ,0 ,1)'))
        redirect('realm.php');
    else
        redirect('realm.php?error=4');
}


//####################################################################################################
// SET REALM TO DEFAULT
//####################################################################################################
function set_def_realm(&$sqlr)
{
    global $action_permission;
    valid_login($action_permission['read']);

    $id = (isset($_GET['id'])) ? $sqlr->quote_smart($_GET['id']) : 1;
    if(is_numeric($id));
    else
        $id = 1;

    if ($sqlr->num_rows($sqlr->query('SELECT id FROM realmlist WHERE id = '.$id.'')))
        $_SESSION['realm_id'] = $id;
    unset($id);

    $url = (isset($_GET['url'])) ? $_GET['url'] : 'index.php';
    redirect($url);
}


function get_icon_type()
{
    global $lang_realm;
    return
        [
        0 => [0,$lang_realm['normal']],
        1 => [1,$lang_realm['pvp']],
        4 => [4,$lang_realm['normal']],
        6 => [6,$lang_realm['rp']],
        8 => [8,$lang_realm['rppvp']],
        16 => [16,$lang_realm['ffapvp']],
        ];
}


function get_timezone_type()
{
    global $lang_realm;
    return
        [
        1 => [1,$lang_realm['development']],
        2 => [2,$lang_realm['united_states']],
        3 => [3,$lang_realm['oceanic']],
        4 => [4,$lang_realm['latin_america']],
        5 => [5,$lang_realm['tournament']],
        6 => [6,$lang_realm['korea']],
        8 => [8,$lang_realm['english']],
        9 => [9,$lang_realm['german']],
       10 => [10,$lang_realm['french']],
       11 => [11,$lang_realm['spanish']],
       12 => [12,$lang_realm['russian']],
       14 => [14,$lang_realm['taiwan']],
       16 => [16,$lang_realm['china']],
       26 => [26,$lang_realm['test_server']],
       28 => [28,$lang_realm['qa_server']],
        ];
}


//####################################################################################################
// MAIN
//####################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

$lang_realm = lang_realm();

if (1 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['empty_fields'].'</font>
            </h1>';
elseif (2 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_realm['err_deleting'].'</font>
            </h1>';
elseif (3 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_realm['update_executed'].'</font>
            </h1>';
elseif (4 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_realm['update_err'].'</font>
            </h1>';
else
    $output .= '
            <h1>'.$lang_realm['realm_data'].'</h1>';

unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('edit_realm' === $action)
    edit_realm($sqlr);
elseif ('doedit_realm' === $action)
    doedit_realm($sqlr);
elseif ('del_realm' === $action)
    del_realm($sqlr);
elseif ('dodel_realm' === $action)
    dodel_realm($sqlr);
elseif ('add_realm' === $action)
    add_realm($sqlr);
elseif ('set_def_realm' === $action)
    set_def_realm($sqlr);
else
    show_realm($sqlr);


unset($action);
unset($action_permission);
unset($lang_realm);

require_once 'footer.php';


?>
