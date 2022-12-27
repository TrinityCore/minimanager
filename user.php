<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/srp6_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// BROWSE USERS
//########################################################################################################################
function browse_users(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_user, $mmfpm_db, $action_permission, $user_lvl, $user_name,
           $itemperpage, $expansion_select, $gm_level_arr;

    $online_pq = "online";

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    //-------------------SQL Injection Prevention--------------------------------
    $start = (isset($_GET['start'])) ? $sqlr->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlr->quote_smart($_GET['order_by']) : 'id';
    if (preg_match('/^[_[:lower:]]{1,15}$/', $order_by));
    else
        $order_by='id';

    $dir = (isset($_GET['dir'])) ? $sqlr->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir=1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;

    //-------------------Search--------------------------------------------------
    $search_by = '';
    $search_value = '';

    $order_by2 = $order_by;
    if ($order_by == 'SecurityLevel')
        $order_by = 'account_access.SecurityLevel';
    elseif ($order_by == 'online')
        $order_by = 'account.online';
    else
    {
        $order_by = 'account.'.$order_by2;
        unset($order_by2);
    }

    // if we have a search request, if not we just return everything
    if(isset($_GET['search_value']) && isset($_GET['search_by']))
    {
        // injection prevention
        $search_value = $sqlr->quote_smart($_GET['search_value']);
        $search_by = $sqlr->quote_smart($_GET['search_by']);
        $search_menu = ['username', 'id', 'SecurityLevel', 'greater_gmlevel', 'email', 'joindate', 'last_ip', 'failed_logins', 'greater_failed_logins', 'lesser_failed_logins', 'last_login', 'online', 'banned', 'locked', 'expansion'];
        if (in_array($search_by, $search_menu));
        else
            $search_by = 'username';
        unset($search_menu);

        // special search cases
        // developer note: 'if else' is always faster then 'switch case'
        if ($search_by === 'greater_gmlevel')
        {
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE account_access.SecurityLevel < '.$search_value.' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query('SELECT count(*) FROM account_access WHERE SecurityLevel = "%'.$search_value.'%"');
        }
        elseif ($search_by === 'SecurityLevel')
        {
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE account_access.SecurityLevel = '.$search_value.' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query('SELECT count(*) FROM account_access WHERE SecurityLevel = "%'.$search_value.'%"');
        }
        elseif ($search_by === 'banned')
        {
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE account.id = 0 ';
            $count_query = 'SELECT count(*) FROM account WHERE id = 0 ';
            $que = $sqlr->query('SELECT id FROM account_banned');
            while ($banned = $sqlr->fetch_assoc($que))
            {
                $sql_query .= 'OR id = '.$banned['id'].'';
                $count_query .= 'OR id = '.$banned['id'].'';
            }
            $sql_query .= ' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query($count_query);
            unset($count_query);
        }
        elseif ($search_by === 'failed_logins')
        {
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE failed_logins = '.$search_value.' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query('SELECT count(*) FROM account WHERE failed_logins = '.$search_value.'');
        }
        elseif ($search_by === 'greater_failed_logins')
        {
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE failed_logins > '.$search_value.' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query('SELECT count(*) FROM account WHERE failed_logins > '.$search_value.'');
        }
        elseif ($search_by === 'lesser_failed_logins')
        {
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE failed_logins < '.$search_value.' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query('SELECT count(*) FROM account WHERE failed_logins < '.$search_value.'');
        }
        else
        {
            // default search case
            $sql_query = 'SELECT `account_access`.`SecurityLevel`, `account`.`username`, `account`.`id`, `account`.`expansion`, `account`.`email`, `account`.`joindate`, `account`.`failed_logins`, `account`.`locked`, `account`.`last_login`, `account`.`online`, `account`.`last_ip` FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE `account`.'.$search_by.' LIKE "%'.$search_value.'%" ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'';
            $query_1 = $sqlr->query('SELECT count(*) FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE `account`.'.$search_by.' LIKE "%'.$search_value.'%"');
        }
        $query = $sqlr->query($sql_query);
    }
    else
    {
        // get total number of items
        $query_1 = $sqlr->query('SELECT count(*) FROM account');
        $query = $sqlr->query('SELECT `account_access`.`SecurityLevel`, `account`.* FROM account LEFT JOIN account_access ON account.id=account_access.AccountID ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');
    }
    // this is for multipage support
    $all_record = $sqlr->result($query_1,0);
    unset($query_1);

    //==========================top tage navigaion starts here========================
    // we start with a lead of 10 spaces,
    //  because last line of header is an opening tag with 8 spaces
    //  keep html indent in sync, so debuging from browser source would be easy to read
    $output .='
                <!-- start of user.php -->
                <script type="text/javascript" src="libs/js/check.js"></script>
                <center>
                    <table class="top_hidden">
                        <tr>
                            <td>';
    if ($user_lvl >= $action_permission['insert'])
    {
        makebutton($lang_user['add_acc'], 'user.php?action=add_new', 130);
        // backup is broken
        //              makebutton($lang_user['backup'], 'backup.php', 130);
    }

    // cleanup unknown working condition
    //if($user_lvl >= $action_permission['delete'])
    //              makebutton($lang_user['cleanup'], 'cleanup.php', 130);
    makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
    if ($search_by && $search_value)
        makebutton($lang_user['user_list'], 'user.php', 130);

    $output .= '
                            </td>
                            <td align="right" width="25%" rowspan="2">';

    // multi page links
    $output .=
                                $lang_user['tot_acc'].' : '.$all_record.'<br /><br />'.
                                generate_pagination('user.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'', $all_record, $itemperpage, $start);
    // this part for search
    $output .= '
                            </td>
                        </tr>
                        <tr align="left">
                            <td>
                                <table class="hidden">
                                    <tr>
                                        <td>
                                            <form action="user.php" method="get" name="form">
                                                <input type="hidden" name="error" value="3" />
                                                <input type="text" size="24" maxlength="50" name="search_value" value="'.$search_value.'" placeholder="Search..." />
                                                <select name="search_by" title="Search by">
                                                    <option value="username"'.($search_by === 'username' ? ' selected="selected"' : '').'>'.$lang_user['by_name'].'</option>
                                                    <option value="id"'.($search_by === 'id' ? ' selected="selected"' : '').'>'.$lang_user['by_id'].'</option>
                                                    <option value="SecurityLevel"'.($search_by === 'SecurityLevel' ? ' selected="selected"' : '').'>'.$lang_user['by_gm_level'].'</option>
                                                    <option value="greater_gmlevel"'.($search_by === 'greater_gmlevel' ? ' selected="selected"' : '').'>'.$lang_user['greater_gm_level'].'</option>
                                                    <option value="expansion"'.($search_by === 'expansion' ? ' selected="selected"' : '').'>'.$lang_user['by_expansion'].'</option>
                                                    <option value="email"'.($search_by === 'email' ? ' selected="selected"' : '').'>'.$lang_user['by_email'].'</option>
                                                    <option value="joindate"'.($search_by === 'joindate' ? ' selected="selected"' : '').'>'.$lang_user['by_join_date'].'</option>
                                                    <option value="last_ip"'.($search_by === 'last_ip' ? ' selected="selected"' : '').'>'.$lang_user['by_ip'].'</option>
                                                    <option value="failed_logins"'.($search_by === 'failed_logins' ? ' selected="selected"' : '').'>'.$lang_user['by_failed_loggins'].'</option>
                                                    <option value="greater_failed_logins"'.($search_by === 'greater_failed_logins' ? ' selected="selected"' : '').'>'.$lang_user['by_greater_failed_loggins'].'</option>
                                                    <option value="lesser_failed_logins"'.($search_by === 'lesser_failed_logins' ? ' selected="selected"' : '').'>'.$lang_user['by_lesser_failed_loggins'].'</option>
                                                    <option value="last_login"'.($search_by === 'last_login' ? ' selected="selected"' : '').'>'.$lang_user['by_last_login'].'</option>
                                                    <option value="online"'.($search_by === 'online' ? ' selected="selected"' : '').'>'.$lang_user['by_online'].'</option>
                                                    <option value="locked"'.($search_by === 'locked' ? ' selected="selected"' : '').'>'.$lang_user['by_locked'].'</option>
                                                    <option value="banned"'.($search_by === 'banned' ? ' selected="selected"' : '').'>'.$lang_user['by_banned'].'</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>';
    makebutton($lang_global['search'], 'javascript:do_submit()',80);
    $output .= '
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>';
    //==========================top tage navigaion ENDS here ========================
    $output .= '
                    <form method="get" action="user.php" name="form1">
                        <input type="hidden" name="action" value="del_user" />
                        <input type="hidden" name="start" value="'.$start.'" />
                        <input type="hidden" name="backup_op" value="0"/>
                        <table class="lined">
                            <tr>';
    // column headers, with links for sorting
    // first column is the  selection check box
    if($user_lvl >= $action_permission['insert'])
        $output.= '
                                <th width="1%">
                                    <input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.form1);" title="Check all" />
                                </th>';
    else
        $output .= '
                                <th width="1%"></th>';
    $output .='
                                <th width="1%"><a href="user.php?order_by=id&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='id' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['id'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=username&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='username' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['username'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=SecurityLevel&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='SecurityLevel' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['gm_level'].'</a></th>';
    if ($expansion_select)
        $output .='
                                <th width="1%"><a href="user.php?order_by=expansion&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='expansion' ? ' class="'.$order_dir.'"' : '').'>EXP</a></th>';
    $output .='
                                <th width="1%"><a href="user.php?order_by=email&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='email' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['email'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=joindate&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='joindate' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['join_date'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=last_ip&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='last_ip' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['ip'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=failed_logins&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='failed_logins' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['failed_logins'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=locked&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='locked' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['locked'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=last_login&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='last_login' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['last_login'].'</a></th>
                                <th width="1%"><a href="user.php?order_by=online&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['online'].'</a></th>';

    //---------------Page Specific Data Starts Here--------------------------
    while ($data = $sqlr->fetch_assoc($query))
    {
        $data['SecurityLevel'] = (!is_null($data['SecurityLevel'])) ? $data['SecurityLevel'] : 0;
        if (($user_lvl >= $data['SecurityLevel'])||($user_name === $data['username']))
        {
            $output .= '
                            <tr>';
            if ($user_lvl >= $action_permission['insert'])
                $output .= '
                                <td><input type="checkbox" name="check[]" value="'.$data['id'].'" onclick="CheckCheckAll(document.form1);" title="Check all" /></td>';
            else
                $output .= '
                                <td></td>';
            $output .= '
                                <td>'.$data['id'].'</td>
                                <td>
                                    <a href="user.php?action=edit_user&amp;error=11&amp;id='.$data['id'].'">'.$data['username'].'</a>
                                </td>
                                <td>'.$gm_level_arr[$data['SecurityLevel']][2].'</td>';
            if ($expansion_select)
            {
                $exp_lvl_arr = id_get_exp_lvl();
                $output .= '
                                <td>'.$exp_lvl_arr[$data['expansion']][2].'</td>';
                unset($exp_lvl_arr);
            }
            if ($user_lvl >= $action_permission['update']||($user_name === $data['username']))
                $output .= '
                                <td><a href="mailto:'.$data['email'].'">'.substr($data['email'],0,15).'</a></td>';
            else
                $output .= '
                                <td>***@***.***</td>';
            $output .= '
                                <td class="small">'.$data['joindate'].'</td>';
            if (($user_lvl >= $action_permission['update'])||($user_name === $data['username']))
                $output .= '
                                <td><a href="https://www.ip2location.com/demo/'.$data['last_ip'].'" target="_blank" rel="noopener noreferrer">'.$data['last_ip'].'</a></td>';
            else
                $output .= '
                                <td>*******</td>';
            $output .= '
                                <td>'.(($data['failed_logins']) ? $data['failed_logins'] : '-').'</td>
                                <td>'.(($data['locked']) ? $lang_global['yes_low'] : '-').'</td>
                                <td class="small">'.$data['last_login'].'</td>
                                <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '-').'</td>';

            $output .= '
                            </tr>';
        }
        else
        {
            $output .= '
                            <tr>
                                <td>*</td><td>***</td><td>You</td><td>Have</td><td>No</td>
                                <td class=\"small\">Permission</td><td>to</td><td>View</td><td>this</td><td>Data</td><td>***</td>';
            if ($expansion_select)
                $output .= '
                                <td>*</td>';
            $output .= '
                            </tr>';
        }
    }
    $output .= '
                            <tr>
                                <td  colspan="';
    if ($expansion_select)
    {
        if ($expansion_select)
            $output .= '13';
        else
        $output .= '12';
    }
    else
        $output .= '11';

    $output .= '" class="hidden" align="right" width="25%">';
    $output .= generate_pagination('user.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'', $all_record, $itemperpage, $start);
    $output .= '
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" align="left" class="hidden">';
    if($user_lvl >= $action_permission['delete'])
        makebutton($lang_user['del_selected_users'], 'javascript:do_submit(\'form1\',0)" type="wrn',230);

    // backup is broken
    //if($user_lvl >= $action_permission['insert'])
    //    makebutton($lang_user['backup_selected_users'], 'javascript:do_submit(\'form1\',1)',230);
    $output .= '
                                </td>
                                <td colspan="';
    if ($expansion_select)
    {
        if ($expansion_select)
            $output .= '5';
        else
            $output .= '4';
    }
    else
        $output .= '3';
    $output .= '" align="right" class="hidden">'.$lang_user['tot_acc'].' : '.$all_record.'</td>
                            </tr>
                        </table>
                    </form>
                    <br />
                </center>
                <!-- end of user.php -->';
}


//#######################################################################################################
//  DELETE USER
//#######################################################################################################
function del_user()
{
    global $lang_global, $lang_user, $output, $realm_db, $action_permission;
    valid_login($action_permission['delete']);

    if(isset($_GET['check']))
        $check = $_GET['check'];
    else
        redirect("user.php?error=1");

    $pass_array = "";

    //skip to backup
    if (isset($_GET['backup_op'])&&($_GET['backup_op'] == 1))
    {
        for ($i=0; $i<count($check); $i++)
        {
            $pass_array .= "&check%5B%5D=$check[$i]";
        }
        redirect("user.php?action=backup_user$pass_array");
    }

    $output .= "
                <center>
                    <img src=\"img/warn_red.gif\" width=\"48\" height=\"48\" alt=\"\" />
                    <h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1>
                    <br />
                    <font class=\"bold\">{$lang_user['acc_ids']}: ";

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    for ($i=0; $i<count($check); $i++)
    {
        $username = $sqlr->result($sqlr->query("SELECT username FROM `account` WHERE id = {$check[$i]}"),0);
        $output .= "
                        <a href=\"user.php?action=edit_user&amp;id=$check[$i]\" target=\"_blank\">$username, </a>";
        $pass_array .= "&amp;check%5B%5D=$check[$i]";
    }

    $output .= "
                        <br />{$lang_global['will_be_erased']}</font>
                        <br /><br />
                        <table width=\"300\" class=\"hidden\">
                            <tr>
                                <td>";

    makebutton($lang_global['yes'], "user.php?action=dodel_user$pass_array\" type=\"wrn" ,130);
    makebutton($lang_global['no'], "user.php\" type=\"def" ,130);

    $output .= "
                                </td>
                            </tr>
                        </table>
                        <br />
                    </font>
                </center>";
}


//#####################################################################################################
//  DO DELETE USER
//#####################################################################################################
function dodel_user()
{
    global $lang_global, $lang_user, $output, $realm_db, $characters_db, $realm_id, $user_lvl,
           $tab_del_user_characters, $tab_del_user_realmd, $action_permission;
    valid_login($action_permission['delete']);

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    if(isset($_GET['check']))
        $check = $sqlr->quote_smart($_GET['check']);
    else
        redirect("user.php?error=1");

    $deleted_acc = 0;
    $deleted_chars = 0;
    require_once("libs/del_lib.php");

    for ($i=0; $i<count($check); $i++)
    {
        if ($check[$i] != "" )
        {
            list($flag,$del_char) = del_acc($check[$i]);
            if ($flag)
            {
                $deleted_acc++;
                $deleted_chars += $del_char;
            }
        }
    }
    $output .= "
                <center>";
    if ($deleted_acc == 0)
        $output .= "
                    <h1><font class=\"error\">{$lang_user['no_acc_deleted']}</font></h1>";
    else
    {
        $output .= "
                    <h1><font class=\"error\">{$lang_user['total']} <font color=blue>$deleted_acc</font> {$lang_user['acc_deleted']}</font><br /></h1>";
        $output .= "
                    <h1><font class=\"error\">{$lang_user['total']} <font color=blue>$deleted_chars</font> {$lang_user['char_deleted']}</font></h1>";
    }
    $output .= "
                    <br /><br />";
    $output .= "
                    <table class=\"hidden\">
                        <tr>
                            <td>";

    makebutton($lang_user['back_browsing'], "user.php", 230);

    $output .= "
                            </td>
                        </tr>
                    </table>
                    <br />
                </center>";
}


//#####################################################################################################
//  DO BACKUP USER
//#####################################################################################################
function backup_user()
{
    global $lang_global, $lang_user, $output, $realm_db, $characters_db, $realm_id, $user_lvl, $backup_dir, $action_permission;
    valid_login($action_permission['insert']);

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    if(isset($_GET['check']))
        $check = $sql->quote_smart($_GET['check']);
    else
        redirect("user.php?error=1");

    require_once("libs/tab_lib.php");

    $subdir = "$backup_dir/accounts/".date("m_d_y_H_i_s")."_partial";
    mkdir($subdir, 0750);

    for ($t=0; $t<count($check); $t++)
    {
        if ($check[$t] != "" )
        {
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
            $query = $sql->query("SELECT id FROM account WHERE id = $check[$t]");
            $acc = $sql->fetch_array($query);
            $file_name_new = $acc[0]."_{$realm_db['name']}.sql";
            $fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
            fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$realm_db['name']};\n")or die (error($lang_backup['file_write_err']));
            fwrite($fp, "USE {$realm_db['name']};\n\n")or die (error($lang_backup['file_write_err']));

            foreach ($tab_backup_user_realmd as $value)
            {
                $acc_query = $sql->query("SELECT * FROM $value[0] WHERE $value[1] = $acc[0]");
                $num_fields = $sql->num_fields($acc_query);
                $numrow = $sql->num_rows($acc_query);
                $result = "-- Dumping data for $value[0] ".date("m.d.y_H.i.s")."\n";
                $result .= "LOCK TABLES $value[0] WRITE;\n";
                $result .= "DELETE FROM $value[0] WHERE $value[1] = $acc[0];\n";

                if ($numrow)
                {
                    $result .= "INSERT INTO $value[0] (";
                    for($count = 0; $count < $num_fields; $count++)
                    {
                        $result .= "`".$sql->field_name($acc_query,$count)."`";
                        if ($count < ($num_fields-1))
                            $result .= ",";
                    }
                    $result .= ") VALUES \n";

                    for ($i =0; $i<$numrow; $i++)
                    {
                        $result .= "\t(";
                        $row = $sql->fetch_row($acc_query);

                        for($j=0; $j<$num_fields; $j++)
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace("\n","\\n",$row[$j]);

                            if (isset($row[$j]))
                            {
                                if ($sql->field_type($j,$acc_query) == "int")
                                    $result .= "$row[$j]";
                                else
                                    $result .= "'$row[$j]'" ;
                            }
                            else
                                $result .= "''";

                            if ($j<($num_fields-1))
                                $result .= ",";
                        }
                        if ($i < ($numrow-1))
                            $result .= "),\n";
                    }
                    $result .= ");\n";
                }
                $result .= "UNLOCK TABLES;\n";
                $result .= "\n";
                fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
            }
            fclose($fp);

            foreach ($characters_db as $db)
            {
                $file_name_new = $acc[0]."_{$db['name']}.sql";
                $fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
                fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$db['name']};\n")or die (error($lang_backup['file_write_err']));
                fwrite($fp, "USE {$db['name']};\n\n")or die (error($lang_backup['file_write_err']));

                $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
                $all_char_query = $sql->query("SELECT guid, BINARY name AS name FROM `characters` WHERE account = $acc[0]");

                while ($char = $sql->fetch_array($all_char_query))
                {
                    fwrite($fp, "-- Dumping data for character $char[1]\n")or die (error($lang_backup['file_write_err']));
                    foreach ($tab_backup_user_characters as $value)
                    {
                        $char_query = $sql->query("SELECT * FROM $value[0] WHERE $value[1] = $char[0]");
                        $num_fields = $sql->num_fields($char_query);
                        $numrow = $sql->num_rows($char_query);
                        $result = "LOCK TABLES $value[0] WRITE;\n";
                        $result .= "DELETE FROM $value[0] WHERE $value[1] = $char[0];\n";

                        if ($numrow)
                        {
                            $result .= "INSERT INTO $value[0] (";

                            for($count = 0; $count < $num_fields; $count++)
                            {
                                $result .= "`".$sql->field_name($char_query,$count)."`";
                                if ($count < ($num_fields-1))
                                    $result .= ",";
                            }
                            $result .= ") VALUES \n";
                            for ($i =0; $i<$numrow; $i++)
                            {
                                $result .= "\t(";
                                $row = $sql->fetch_row($char_query);
                                for($j=0; $j<$num_fields; $j++)
                                {
                                    $row[$j] = addslashes($row[$j]);
                                    $row[$j] = preg_replace("\n","\\n",$row[$j]);

                                    if (isset($row[$j]))
                                    {
                                        if ($sql->field_type($j,$char_query) == "int")
                                            $result .= "$row[$j]";
                                        else
                                            $result .= "'$row[$j]'" ;
                                    }
                                    else
                                        $result .= "''";
                                    if ($j<($num_fields-1))
                                        $result .= ",";
                                }
                                if ($i < ($numrow-1))
                                    $result .= "),\n";
                            }
                            $result .= ");\n";
                        }
                        $result .= "UNLOCK TABLES;\n";
                        $result .= "\n";
                        fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
                    }
                }
                fclose($fp);
            }
        }
    }
    redirect("user.php?error=15");
}


//#######################################################################################################
//  ADD NEW USER
//#######################################################################################################
function add_new()
{
    global $lang_global, $lang_user, $output, $action_permission, $expansion_select;
    valid_login($action_permission['insert']);
    $output .= "
                <center>
                    <script type=\"text/javascript\">
                        // <![CDATA[
                          function do_submit_data ()
                          {
                            if (document.form.pass.value != document.form.pass2.value)
                            {
                              alert('{$lang_user['nonidentical_passes']}');
                              return;
                            }
                            else
                            {
                              do_submit();
                            }
                          }
                        // ]]>
                    </script>
                    <fieldset style=\"width: 550px;\">
                        <legend>{$lang_user['create_new_acc']}</legend>
                        <form method=\"get\" action=\"user.php\" name=\"form\">
                            <input type=\"hidden\" name=\"action\" value=\"doadd_new\" />
                            <table class=\"flat\">
                                <tr>
                                    <td>{$lang_user['username']}</td>
                                    <td><input type=\"text\" name=\"new_user\" size=\"24\" maxlength=\"15\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_user['password']}</td>
                                    <td><input type=\"password\" name=\"pass\" size=\"24\" maxlength=\"25\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_user['confirm']}</td>
                                    <td><input type=\"password\" name=\"pass2\" size=\"24\" maxlength=\"25\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_user['email']}</td>
                                    <td><input type=\"text\" name=\"new_mail\" size=\"24\" maxlength=\"225\" value=\"none@mail.com\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_user['locked']}</td>
                                    <td><input type=\"checkbox\" name=\"new_locked\" value=\"1\" /></td>
                                </tr>";
    if ( $expansion_select )
        $output .= "
                                <tr>
                                    <td>{$lang_user['expansion_account']}</td>
                                    <td>
                                        <select name=\"new_expansion\">
                                            <option value=\"2\">{$lang_user['wotlk']}</option>
                                            <option value=\"1\">{$lang_user['tbc']}</option>
                                            <option value=\"0\">{$lang_user['classic']}</option>
                                        </select>
                                    </td>
                                </tr>";
    $output .="
                                <tr>
                                    <td>";

    makebutton($lang_user['create_acc'], "javascript:do_submit_data()\" type=\"wrn",130);

    $output .= "
                                    </td>
                                    <td>";

    makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);

    $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br /><br />
                </center>";
}


//#########################################################################################################
// DO ADD NEW USER
//#########################################################################################################
function doadd_new()
{
    global $lang_global, $realm_db, $action_permission;
    valid_login($action_permission['insert']);

    if ( empty($_GET['new_user']) || empty($_GET['pass']) )
        redirect("user.php?action=add_new&error=4");

    $sqlc = new SQL;
    $sqlc->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $new_user = $sqlc->quote_smart(trim($_GET['new_user']));

    //make sure username/pass at least 4 chars long and less than max
    if ((strlen($new_user) < 4) || (strlen($new_user) > 15))
        redirect("user.php?action=add_new&error=8");

    //make sure it doesnt contain non english chars.
    if (!valid_alphabetic($new_user))
        redirect("user.php?action=add_new&error=9");

    $result = $sqlc->query("SELECT username FROM account WHERE username = '$new_user'");
    //there is already someone with same username
    if ($sqlc->num_rows($result))
        redirect("user.php?action=add_new&error=7");
    else
        $last_ip = "0.0.0.0";
    
    list($salt,$verifier) = GetSRP6RegistrationData(trim($_GET['new_user']), $_GET['pass']);

    $new_mail = (isset($_GET['new_mail'])) ? $sqlc->quote_smart(trim($_GET['new_mail'])) : NULL;
    $locked = (isset($_GET['new_locked'])) ? $sqlc->quote_smart($_GET['new_locked']) : 0;
    $expansion = (isset($_GET['new_expansion'])) ? $sqlc->quote_smart($_GET['new_expansion']) : 0;
    $result = $sqlc->query("INSERT INTO account (username,salt,verifier,email, joindate,last_ip,failed_logins,locked,last_login,expansion)
                            VALUES ('$new_user',UNHEX('".bin2hex($salt)."'),UNHEX('".bin2hex($verifier)."'),'$new_mail',now() ,'$last_ip',0, $locked ,NULL, $expansion)");
    if ($result)
        redirect("user.php?error=5");
}


//###########################################################################################################
//  EDIT USER
//###########################################################################################################
function edit_user()
{
    global $lang_global, $lang_user, $output, $realm_db, $characters_db, $realm_id, $mmfpm_db, $user_lvl, $user_name,
           $gm_level_arr, $action_permission, $expansion_select, $developer_test_mode, $multi_realm_mode, $server, $showcountryflag, $enable_soap;

    $online_pq = "online";

    if ($showcountryflag)
    {
        require_once 'libs/misc_lib.php';
    }

    if (empty($_GET['id']))
        redirect("user.php?error=10");

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $id = $sqlr->quote_smart($_GET['id']);

    $result = $sqlr->query("SELECT IFNULL(`account_access`.`SecurityLevel`,0) as `SecurityLevel`, `account`.* FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE account.id = '$id'");
    $data = $sqlr->fetch_assoc($result);

    $refguid = $sqlm->fetch_assoc($sqlm->query('SELECT InvitedBy FROM mm_point_system_invites WHERE PlayersAccount = '.$data['id'].''));
    $refguid = $refguid['InvitedBy'];
    $referred_by = $sqlc->fetch_assoc($sqlc->query("SELECT BINARY name AS name FROM characters WHERE guid = '$refguid'"));
    unset($refguid);
    $referred_by = $referred_by['name'];

    if ($sqlr->num_rows($result))
    {
        $output .= '
                <center>
                    <script type="text/javascript">
                        // <![CDATA[
                          function do_submit_data ()
                          {
                            if ((document.form.username.value != "'.$data['username'].'") && (document.form.pass.value == ""))
                            {
                              alert("If you are changing Username, The password must be changed too.");
                              return;
                            }
                            else
                            {
                              do_submit();
                            }
                          }
                        // ]]>
                    </script>
                        <fieldset style="width: 550px;">
                        <legend>'.$lang_user['edit_acc'].'</legend>
                            <form method="post" action="user.php?action=doedit_user" name="form">
                                <input type="hidden" name="id" value="'.$id.'" />
                                <table class="flat">
                                    <tr>
                                        <td>'.$lang_user['id'].'</td>
                                        <td>'.$data['id'].'</td>
                                    </tr>
                                    <tr>
                                        <td>'.$lang_user['username'].'</td>';
        if($user_lvl >= $action_permission['update'])
            $output .='
                                        <td><input type="text" name="username" size="42" maxlength="15" value="'.$data['username'].'" /></td>';
        else
            $output.='
                                        <td>'.$data['username'].'</td>';
        $output .= '
                                    </tr>
                                    <tr>
                                        <td>'.$lang_user['password'].'</td>';
        if($user_lvl >= $action_permission['update'])
            $output .="
                                        <td><input type=\"password\" name=\"pass\" size=\"42\" maxlength=\"40\" placeholder=\"(unchanged)\" /></td>";
        else
            $output.="
                                        <td>********</td>";
        $output .= "
                                    </tr>
                                    <tr>
                                        <td>{$lang_user['email']}</td>";
        if($user_lvl >= $action_permission['update'])
            $output .= '
                                        <td><input type="text" name="mail" size="42" maxlength="225" value="'.$data['email'].'" /></td>';
        else
            $output.="
                                        <td>***@***.***</td>";
        $output .= "
                                    </tr>
                                    <tr>
                                        <td>{$lang_user['invited_by']}:</td>
                                        <td>";
        if($user_lvl >= $action_permission['update'] && !$referred_by !=NULL)
            $output .="
                                            <input type=\"text\" name=\"referredby\" size=\"42\" maxlength=\"12\" value=\"$referred_by\" />";
        else
            $output .="
                                            $referred_by";
        $output .="
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{$lang_user['gm_level_long']}</td>";
        if($user_lvl >= $action_permission['update'])
        {
            $output .="
                                        <td>
                                            <select name=\"SecurityLevel\">";
            foreach ($gm_level_arr as $level)
            {
                if (($level[0] > -1) && ($level[0] < $user_lvl))
                {
                    $output .= "
                                                <option value=\"{$level[0]}\" ";
                    if ($data['SecurityLevel'] == $level[0])
                        $output .= "selected=\"selected\" ";
                    $output .= ">{$level[1]}</option>";
                }
            }
            $output .= "
                                            </select>
                                        </td>";
        }
        else
            $output .= '
                                        <td>'.id_get_gm_level($data['SecurityLevel']).' ( '.$data['SecurityLevel'].' )</td>';
        $output .= '
                                    </tr>
                                    <tr>
                                        <td>'.$lang_user['join_date'].'</td>
                                        <td>'.$data['joindate'].'</td>
                                    </tr>
                                    <tr>
                                        <td>'.$lang_user['last_ip'].'</td>';
         if($user_lvl >= $action_permission['update'])
         {
             $output .= '
                                        <td><a href="https://www.ip2location.com/demo/'.$data['last_ip'].'" target="_blank" rel="noopener noreferrer">'.$data['last_ip'].'</a>';

                                             if ($showcountryflag)
                                             {
                                                 $country = misc_get_country_by_ip($data['last_ip'], $sqlm);
                                                 $output .= '
                                                            '.(($country['code']) ? '<img src="img/flags/'.$country['code'].'.png" onmousemove="toolTip(\''.($country['country']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />' : '-').'';
                                             }
              $output .= '             <a href="banned.php?action=do_add_entry&amp;entry='.$data['last_ip'].'&amp;bantime=3600&amp;ban_type=ip_banned"> &lt;- '.$lang_user['ban_this_ip'].'</a> | <a href="user.php?error=3&search_value='.$data['last_ip'].'&search_by=last_ip">'.$lang_user['search_this_ip'].'</a></td>';
        }
        else
        {
            $output .= "
                                        <td>***.***.***.***";
                                            if ($showcountryflag)
                                            {
                                                $country = misc_get_country_by_ip($data['last_ip'], $sqlm);
                                                $output .= '
                                                           '.(($country['code']) ? '<img src="img/flags/'.$country['code'].'.png" onmousemove="toolTip(\''.($country['country']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />' : '-').'';
                                            }
            $output .= "          </td>";
        }
        $output .= "
                                    </tr>
                                    <tr>
                                        <td>{$lang_user['banned']}</td>";

        $que = $sqlr->query("SELECT bandate, unbandate, bannedby, banreason FROM account_banned WHERE id = $id");
        if ($sqlr->num_rows($que))
        {
            $banned = $sqlr->fetch_row($que);
            $ban_info = " From:".date('d-m-Y G:i', $banned[0])." till:".date('d-m-Y G:i', $banned[1])."<br />by $banned[2]";
            $ban_checked = " checked=\"checked\"";
        }
        else
        {
            $ban_checked = "";
            $ban_info    = "";
            $banned[3]   = "";
        }
        if($user_lvl >= $action_permission['update'])
            $output .= "
                                        <td><input type=\"checkbox\" name=\"banned\" value=\"1\" $ban_checked/>$ban_info</td>";
        else
            $output .= "
                                        <td>$ban_info</td>";
        $output .="
                                    </tr>
                                    <tr>
                                        <td>{$lang_user['banned_reason']}</td>";
        if($user_lvl >= $action_permission['update'])
            $output .="
                                        <td><input type=\"text\" name=\"banreason\" size=\"42\" maxlength=\"255\" value=\"$banned[3]\" /></td>";
        else
            $output .= "
                                        <td>$banned[3]</td>";
        if ($expansion_select)
        {
            $output .="
                                    </tr>
                                    <tr>";
            if($user_lvl >= $action_permission['update'])
            {
                $output .="
                                        <td>{$lang_user['client_type']}</td>";
                $output .="
                                        <td>
                                            <select name=\"expansion\">";
                $output .= "
                                                <option value=\"0\">{$lang_user['classic']}</option>
                                                <option value=\"1\" ";
                if ($data['expansion'] == 1)
                    $output .= "selected=\"selected\" ";
                $output .= ">{$lang_user['tbc']}</option>
                                                <option value=\"2\" ";
                if ($data['expansion'] ==2)
                    $output .= "selected=\"selected\" ";
                $output .= ">{$lang_user['wotlk']}</option>
                                            </select>
                                        </td>";
            }
            else
                $output .= "
                                            <td>{$lang_user['classic']}</td>";
        }
        $output .="
                                        </tr>
                                        <tr>
                                            <td>{$lang_user['failed_logins_long']}</td>";
        if($user_lvl >= $action_permission['update'])
            $output .='
                                            <td><input type="text" name="failed" size="42" maxlength="3" value="'.$data['failed_logins'].'" /></td>';
        else
            $output .= '
                                            <td>'.$data['failed_logins'].'</td>';
        $output .="
                                        </tr>
                                        <tr>
                                            <td>{$lang_user['locked']}</td>";
        $lock_checked = ($data['locked']) ? " checked=\"checked\"" : "";
        if($user_lvl >= $action_permission['update'])
            $output .= "
                                            <td><input type=\"checkbox\" name=\"locked\" value=\"1\" $lock_checked/></td>";
        else
            $output .="
                                            <td></td>";
        $output.= '
                                        </tr>
                                        <tr>
                                            <td>'.$lang_user['last_login'].'</td>
                                            <td>'.$data['last_login'].'</td>
                                        </tr>
                                        <tr>
                                            <td>'.$lang_user['online'].'</td>';
        $output .= "
                                            <td>".(( $data['online'] ) ? $lang_global['yes'] : $lang_global['no'])."</td>
                                        </tr>";
        $query = $sqlr->query("SELECT SUM(numchars) FROM realmcharacters WHERE acctid = '$id'");
        $tot_chars = $sqlr->result($query, 0);
        $query = $sqlc->query("SELECT count(*) FROM `characters` WHERE account = $id");
        $chars_on_realm = $sqlc->result($query, 0);
        $output .= "
                                        <tr>
                                            <td>{$lang_user['tot_chars']}</td>
                                            <td>$tot_chars</td>
                                        </tr>";
        $realms = $sqlr->query("SELECT id, name FROM realmlist");
        if ($developer_test_mode && $multi_realm_mode && ($sqlr->num_rows($realms) > 1 && (count($server) > 1) && (count($characters_db) > 1)))
        {
            require_once("scripts/get_lib.php");
            while ($realm = $sqlr->fetch_array($realms))
            {
                $sqlc->connect($characters_db[$realm[0]]['addr'], $characters_db[$realm[0]]['user'], $characters_db[$realm[0]]['pass'], $characters_db[$realm[0]]['name']);
                $query = $sqlc->query("SELECT count(*) FROM `characters` WHERE account = $id");
                $chars_on_realm = $sqlc->result($query, 0);
                $output .= "
                                        <tr>
                                            <td>{$lang_user['chars_on_realm']} ".get_realm_name($realm[0])."</td>
                                            <td>$chars_on_realm</td>
                                        </tr>";
                if ($chars_on_realm)
                {
                    $char_array = $sqlc->query("SELECT guid, BINARY name AS name, race, class, level, gender FROM `characters` WHERE account = $id");
                    while ($char = $sqlc->fetch_array($char_array))
                    {
                        $output .= "
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---></td>
                                            <td>
                                                <a href=\"char.php?id=$char[0]&amp;realm=$realm[0]\">$char[1]  - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />
                                                <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\"/> - lvl ".char_get_level_color($char[4])."</a>";
                    if ($enable_soap == 1)
                    {
                        $output .= "            <br /> <br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=rename&char=$char[1]&id=$id\">Rename</a><br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=changefaction&char=$char[1]&id=$id\">Change Faction</a><br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=changerace&char=$char[1]&id=$id\">Change Race</a><br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=customize&char=$char[1]&id=$id\">Character Customize</a><br />";
                    }
                        $output .= "        </td>
                                        </tr>";
                    }
                }
            }
        }
        else
        {
            $query = $sqlc->query("SELECT count(*) FROM `characters` WHERE account = $id");
            $chars_on_realm = $sqlc->result($query, 0);
            $output .= "
                                        <tr>
                                            <td>{$lang_user['chars_on_realm']}</td>
                                            <td>$chars_on_realm</td>
                                        </tr>";
            if ($chars_on_realm)
            {
                $char_array = $sqlc->query("SELECT guid,BINARY name AS name,race,class, level, gender FROM `characters` WHERE account = $id");
                while ($char = $sqlc->fetch_array($char_array))
                {
                    $output .= "
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---></td>
                                            <td>
                                                <a href=\"char.php?id=$char[0]\">$char[1]  - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />
                                                <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\"/> - lvl ".char_get_level_color($char[4])."</a>";
                    if ($enable_soap == 1)
                    {
                        $output .= "            <br /> <br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=rename&char=$char[1]&id=$id\">Rename</a><br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=changefaction&char=$char[1]&id=$id\">Change Faction</a><br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=changerace&char=$char[1]&id=$id\">Change Race</a><br />
                                                &nbsp;&nbsp;&nbsp;&nbsp;---><a href=\"user.php?action=soap_command&cmd=customize&char=$char[1]&id=$id\">Character Customize</a><br />";
                    }
                        $output .= "        </td>
                                        </tr>";
                }
            }
        }
        $output .= "
                                        <tr>
                                            <td>";
        if($user_lvl >= $action_permission['delete'])
            makebutton($lang_user['del_acc'], "user.php?action=del_user&amp;check%5B%5D=$id\" type=\"wrn",130);

        $output .= "
                                            </td>
                                            <td>";
        if($user_lvl >= $action_permission['update'])
            makebutton($lang_user['update_data'], "javascript:do_submit_data()",130);

        makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);

        $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </fieldset>
                        <br /><br />
                    </center>";
    }
    else
        error($lang_global['err_no_user']);
}


//############################################################################################################
//  DO   EDIT   USER
//############################################################################################################
function doedit_user()
{
    global $lang_global, $realm_db, $mmfpm_db, $user_lvl, $user_name, $action_permission;
    valid_login($action_permission['update']);

    if ( (!isset($_POST['pass'])||($_POST['pass'] === ''))
            && (!isset($_POST['mail'])||($_POST['mail'] === ''))
            && (!isset($_POST['expansion'])||($_POST['expansion'] === ''))
            && (!isset($_POST['referredby'])||($_POST['referredby'] === '')) )
        redirect("user.php?action=edit_user&&id={$_POST['id']}&error=1");

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $id = $sqlr->quote_smart($_POST['id']);
    $username = $sqlr->quote_smart($_POST['username']);
    $banreason = $sqlr->quote_smart($_POST['banreason']);
    
    $user_pass_change = '';
    if ($_POST['pass'] !== '')
    {
        list($salt, $verifier) = GetSRP6RegistrationData($username, $_POST['pass']);
        $user_pass_change = ('username=\'' . $username . '\', salt=UNHEX(\'' . bin2hex($salt) . '\'), verifier=UNHEX(\'' . bin2hex($verifier) . '\'), ');
    }

    $mail = (isset($_POST['mail']) && $_POST['mail'] != '') ? $sqlr->quote_smart($_POST['mail']) : "";
    $failed = (isset($_POST['failed'])) ? $sqlr->quote_smart($_POST['failed']) : 0;
    $SecurityLevel = (isset($_POST['SecurityLevel'])) ? $sqlr->quote_smart($_POST['SecurityLevel']) : 0;
    $expansion = (isset($_POST['expansion'])) ? $sqlr->quote_smart($_POST['expansion']) : 1;
    $banned = (isset($_POST['banned'])) ? $sqlr->quote_smart($_POST['banned']) : 0;
    $locked = (isset($_POST['locked'])) ? $sqlr->quote_smart($_POST['locked']) : 0;
    $referredby = $sqlr->quote_smart(trim($_POST['referredby']));

    //make sure username/pass at least 4 chars long and less than max
    if ((strlen($username) < 4) || (strlen($username) > 15))
        redirect("user.php?action=edit_user&id=$id&error=8");

    if ($SecurityLevel >= $user_lvl)
        redirect("user.php?action=edit_user&&id={$_POST['id']}&error=16");
    if (!valid_alphabetic($username))
        redirect("user.php?action=edit_user&error=9&id=$id");
    //restricting accsess to lower gmlvl
    $result = $sqlr->query("SELECT account.username, IFNULL(account_access.SecurityLevel,0) as SecurityLevel FROM account LEFT JOIN account_access ON account.id=account_access.AccountID WHERE account.id = '$id'");
    $accgmlevel = $sqlr->result($result, 0, 'SecurityLevel');
    if (($user_lvl <= $accgmlevel) && ($user_name != $sqlr->result($result, 0, 'username')))
        redirect("user.php?error=14");


    if (!$banned)
        $sqlr->query("DELETE FROM account_banned WHERE id='$id'");
    else
    {
        $result = $sqlr->query("SELECT count(*) FROM account_banned WHERE id = '$id'");
        if(!$sqlr->result($result, 0))
            $sqlr->query("INSERT INTO account_banned (id, bandate, unbandate, bannedby, banreason, active)
                          VALUES ($id, ".time().",".(time()+(365*24*3600)).",'$user_name','$banreason', 1)");
    }
    $error = false;

    $sqlr->query("UPDATE account SET email='$mail', $user_pass_change failed_logins='$failed',locked='$locked',expansion='$expansion' WHERE id='$id'");
    if (!$sqlr->affected_rows())
        $error = true;

    if ($SecurityLevel != $accgmlevel) //SecurityLevel changed..
    {
        if ($SecurityLevel == 0 && $accgmlevel > 0)
            $sqlr->query("DELETE FROM account_access WHERE AccountID='$id'"); //0 has no entry in account_access
        elseif ($SecurityLevel > 0 && $accgmlevel == 0) //0 has no entry in account_access, add one; sometimes there's a bug so there's indeed a SecurityLevel 0 entry in the table -> replace
            $sqlr->query("REPLACE INTO account_access (`id`,`SecurityLevel`,`RealmID`) VALUES ('$id','$SecurityLevel','-1')"); //RealmID needs to be fixed!!
        else
            $sqlr->query("UPDATE account_access SET SecurityLevel='$SecurityLevel' WHERE id='$id'");

        $sqlr->query("SELECT IFNULL((SELECT SecurityLevel FROM account_access WHERE AccountID='$id'),0)");
        if (!$sqlr->affected_rows() || $sqlr->result($result, 0) != $accgmlevel) //temporary errorhandling
            $error = true;
    }

    if (doupdate_referral($referredby, $id) || $error)
        redirect("user.php?action=edit_user&error=13&id=$id");
    else
        redirect("user.php?action=edit_user&error=12&id=$id");
}

function doupdate_referral($referredby, $user_id)
{
    global $realm_db, $mmfpm_db, $characters_db, $realm_id;
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $result = $sqlm->fetch_row($sqlm->query("SELECT InvitedBy FROM mm_point_system_invites WHERE PlayersAccount = '$user_id'"));
    $result = $result[0];

    if ($result == NULL)
    {
        $referred_by = $sqlc->fetch_row($sqlc->query("SELECT guid FROM characters WHERE BINARY name = '$referredby'"));
        $referred_by = $referred_by[0];

        if ($referred_by != NULL)
        {
            $char = $sqlc->fetch_row($sqlc->query("SELECT account FROM characters WHERE guid = '$referred_by'"));
            $result = $sqlr->fetch_row($sqlr->query("SELECT id FROM account WHERE id = '$char'"));
            $result = $result[0];
            if ($result != $user_id)
            {
                $sqlm->query("INSERT INTO mm_point_system_invites (PlayersAccount, InvitedBy, InviterAccount) VALUES ('$user_id', '$referred_by', '$result')");
                return true;
            }
            else
                return false;
        }
    }
}

//########################################################################################################################
// SOAP FUNCTION
//########################################################################################################################

function soap_command()
{
global $soapaddr, $soap_port, $soap_user, $soap_pass, $cmd, $char, $docmd;

require_once 'scripts/config.php';

   $cmd = $_GET['cmd'];
   $char = $_GET['char'];
   $id = $_GET['id'];

$command = "error";

   $client = new SoapClient(NULL, [
       'location' => "https://$soapaddr:$soap_port/",
       'uri'      => 'urn:TC',
       'login'    => $soap_user,
       'password' => $soap_pass,
   ]);

   switch ($cmd)
   {
      case "rename":
           $command = "character rename $char";
      break;
      case "changefaction":
           $command = "character changefaction $char";
      break;
      case "changerace":
           $command = "character changerace $char";
      break;
      case "cuztomize":
           $command = "character cuztomize $char";
      break;
      default:
           $command = "error";
   }

   if($command != "error")
      $docmd = $client->executeCommand(new SoapParam($command, "command"));

   return($docmd);
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

// load language
$lang_user = lang_user();

// defines the title header in error cases
switch ($err)
{
    case 1:
        $output .= "
            <h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
        break;
    case 2:
        $output .= "
            <h1><font class=\"error\">{$lang_global['err_no_search_passed']}</font></h1>";
        break;
    case 3:
        $output .= "
            <h1><font class=\"error\">{$lang_user['search_results']}</font></h1>";
        break;
    case 4:
        $output .= "
            <h1><font class=\"error\">{$lang_user['acc_creation_failed']}</font></h1>";
        break;
    case 5:
        $output .= "
            <h1>{$lang_user['acc_created']}</h1>";
        break;
    case 6:
        $output .= "
            <h1><font class=\"error\">{$lang_user['nonidentical_passes']}</font></h1>";
        break;
    case 7:
        $output .= "
            <h1><font class=\"error\">{$lang_user['user_already_exist']}</font></h1>";
        break;
    case 8:
        $output .= "
            <h1><font class=\"error\">{$lang_user['username_pass_too_long']}</font></h1>";
        break;
    case 9:
        $output .= "
            <h1><font class=\"error\">{$lang_user['use_only_eng_charset']}</font></h1>";
        break;
    case 10:
        $output .= "
            <h1><font class=\"error\">{$lang_user['no_value_passed']}</font></h1>";
        break;
    case 11:
        $output .= "
            <h1>{$lang_user['edit_acc']}</h1>";
        break;
    case 12:
        $output .= "
            <h1><font class=\"error\">{$lang_user['update_failed']}</font></h1>";
        break;
    case 13:
        $output .= "
            <h1>{$lang_user['data_updated']}</h1>";
        break;
    case 14:
        $output .= "
            <h1><font class=\"error\">{$lang_user['you_have_no_permission']}</font></h1>";
        break;
    case 15:
        $output .= "
            <h1><font class=\"error\">{$lang_user['acc_backedup']}</font></h1>";
        break;
    case 16:
        $output .= "
            <h1><font class=\"error\">{$lang_user['you_have_no_permission_to_set_gmlvl']}</font></h1>";
        break;
    default: //no error
        $output .= "
            <h1>{$lang_user['browse_acc']}</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "add_new":
        add_new();
        break;
    case "doadd_new":
        doadd_new();
        break;
    case "edit_user":
        edit_user();
        break;
    case "doedit_user":
        doedit_user();
        break;
    case "del_user":
        del_user();
        break;
    case "dodel_user":
        dodel_user();
        break;
    case "backup_user":
        backup_user();
        break;
    case "soap_command":
        soap_command();
        $output .= "
            <h1><font class=\"error\">$docmd</font></h1>";
        edit_user();
        break;

    default:
        browse_users($sqlr, $sqlc);
}

unset($action);
unset($action_permission);
unset($lang_user);

require_once("footer.php");

?>
