<?php


require_once("header.php");
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW BANNED LIST
//########################################################################################################################
function show_list()
{
    global $lang_global, $lang_banned, $output, $realm_db, $itemperpage, $action_permission, $user_lvl, $showcountryflag, $sqlm;
    valid_login($action_permission['read']);

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $ban_type = (isset($_GET['ban_type'])) ? $sqlr->quote_smart($_GET['ban_type']) : "account_banned";
    $key_field = ($ban_type == 'account_banned') ? 'id' :'ip';

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlr->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlr->quote_smart($_GET['order_by']) : "$key_field";
    if (!preg_match("/^[_[:lower:]]{1,12}$/", $order_by))
        $order_by="$key_field";

    $dir = (isset($_GET['dir'])) ? $sqlr->quote_smart($_GET['dir']) : 1;
    if (!preg_match("/^[01]{1}$/", $dir))
        $dir=1;

    $order_dir = ($dir) ? "ASC" : "DESC";
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $fromwhere = ($ban_type == 'account_banned') ? $ban_type . " WHERE active = '1'" : $ban_type;
    $query_1 = $sqlr->query("SELECT count(*) FROM $fromwhere");
    $all_record = $sqlr->result($query_1,0);

    $result = $sqlr->query("SELECT $key_field, bandate, unbandate, bannedby, SUBSTRING_INDEX(banreason,' ',3) FROM $fromwhere ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
    $this_page = $sqlr->num_rows($result);

    $output .= "
        <center>
            <table class=\"top_hidden\">
                <tr>
                    <td>";
    if($user_lvl >= $action_permission['insert'])
        makebutton($lang_banned['add_to_banned'], "banned.php?action=add_entry\" type=\"wrn",180);
    if ($ban_type === "account_banned")
        makebutton($lang_banned['banned_ips'], "banned.php?ban_type=ip_banned",130);
    else
        makebutton($lang_banned['banned_accounts'], "banned.php?ban_type=account_banned",130);
    makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);

    $output .= "
                    </td>
                    <td align=\"right\">".generate_pagination("banned.php?action=show_list&amp;order_by=$order_by&amp;ban_type=$ban_type&amp;dir=".!$dir, $all_record, $itemperpage, $start)."</td>
                </tr>
            </table>
            <script type=\"text/javascript\">
                answerbox.btn_ok='{$lang_global['yes_low']}';
                answerbox.btn_cancel='{$lang_global['no']}';
                var del_banned = 'banned.php?action=do_delete_entry&amp;ban_type=$ban_type&amp;$key_field=';
            </script>
            <table class=\"lined\">
                <tr>
                    <th width=\"5%\">{$lang_global['delete_short']}</th>
                    <th width=\"15%\"><a href=\"banned.php?order_by=$key_field&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by==$key_field ? " class=\"$order_dir\"" : "").">{$lang_banned['ip_acc']}</a></th>".($ban_type === "account_banned" ? "
                    <th width=\"18%\" style=\"white-space:nowrap;\">{$lang_global['last_ip']}</th>" : "")."
                    <th width=\"16%\"><a href=\"banned.php?order_by=bandate&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='bandate' ? " class=\"$order_dir\"" : "").">{$lang_banned['bandate']}</a></th>
                    <th width=\"16%\"><a href=\"banned.php?order_by=unbandate&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='unbandate' ? " class=\"$order_dir\"" : "").">{$lang_banned['unbandate']}</a></th>
                    <th width=\"15%\"><a href=\"banned.php?order_by=bannedby&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='bannedby' ? " class=\"$order_dir\"" : "").">{$lang_banned['bannedby']}</a></th>
                    <th width=\"25%\"><a href=\"banned.php?order_by=banreason&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='banreason' ? " class=\"$order_dir\"" : "").">{$lang_banned['banreason']}</a></th>
                </tr>";

    while ($ban = $sqlr->fetch_row($result))
    {
        $last_ip_td = "";
        if ($ban_type === "account_banned")
        {
            $result1 = $sqlr->query("SELECT username, last_ip FROM account WHERE id ='$ban[0]'");
            $acc_row = $sqlr->fetch_assoc($result1);
            $owner_acc_name = $acc_row ? $acc_row['username'] : '';
            $last_ip = $acc_row ? $acc_row['last_ip'] : '-';
            $flag_html = "";
            if ($showcountryflag && $last_ip && $last_ip !== '-')
            {
                require_once 'libs/misc_lib.php';
                $country = misc_get_country_by_ip($last_ip, $sqlm);
                if (!empty($country['code']))
                    $flag_html = "<img src=\"img/flags/{$country['code']}.png\" onmousemove=\"toolTip('{$country['country']}', 'item_tooltip')\" onmouseout=\"toolTip()\" alt=\"\" /> ";
            }
            $name_out = "<a href=\"user.php?action=edit_user&amp;error=11&amp;id=$ban[0]\">$owner_acc_name</a>";
            $last_ip_td = "
                    <td style=\"white-space:nowrap;\">$flag_html".($last_ip && $last_ip !== '-' ? "<a href=\"user.php?error=3&amp;search_by%5B%5D=last_ip&amp;search_value%5B%5D=$last_ip\" title=\"Ver cuentas con esta IP\">$last_ip</a>" : $last_ip)."</td>";
        }
        else
        {
            $name_out = $ban[0];
            $owner_acc_name = $ban[0];
        }
        $output .= "
                <tr>
                    <td>";
        if($user_lvl >= $action_permission['delete'])
            $output .= "
                        <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>$owner_acc_name</font><br />{$lang_global['are_you_sure']}', del_banned + '$ban[0]');\" style=\"cursor:pointer;\" alt=\"\" />";
        $output .= "
                    </td>
                    <td>$name_out</td>$last_ip_td
                    <td>".date('d-m-Y G:i', $ban[1])."</td>
                    <td>".date('d-m-Y G:i', $ban[2])."</td>
                    <td>$ban[3]</td>
                    <td>$ban[4]</td>
                </tr>";
    }
    $output .= "
                <tr>
                    <td colspan=\"".($ban_type === "account_banned" ? 7 : 6)."\" align=\"right\" class=\"hidden\">{$lang_banned['tot_banned']} : $all_record</td>
                </tr>
            </table>
            <br/>
        </center>";
}


//########################################################################################################################
// DO DELETE ENTRY FROM LIST
//########################################################################################################################
function do_delete_entry()
{
    global $realm_db, $action_permission, $user_lvl;
    valid_login($action_permission['delete']);

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    if(isset($_GET['ban_type']))
        $ban_type = $sqlr->quote_smart($_GET['ban_type']);
    else
        redirect("banned.php?error=1");

    $key_field = ($ban_type == "account_banned") ? "id" : "ip";

    if(isset($_GET[$key_field]))
        $entry = $sqlr->quote_smart($_GET[$key_field]);
    else
        redirect("banned.php?error=1");

    if($ban_type == 'account_banned')
        $sqlr->query("UPDATE account_banned SET active = '0' WHERE $key_field = '$entry'");
    else
        $sqlr->query("DELETE FROM $ban_type WHERE $key_field = '$entry'");

    if ($sqlr->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
    else
        redirect("banned.php?error=2&ban_type=$ban_type");
}


//########################################################################################################################
//  BAN NEW IP
//########################################################################################################################
function add_entry()
{
    global $lang_global, $lang_banned, $output, $action_permission, $user_lvl;
    valid_login($action_permission['insert']);

    $output .= "
        <center>
            <fieldset class=\"half_frame\">
                <legend>{$lang_banned['ban_entry']}</legend>
                <form method=\"get\" action=\"banned.php\" name=\"form\">
                    <input type=\"hidden\" name=\"action\" value=\"do_add_entry\" />
                    <table class=\"flat\">
                        <tr>
                            <td>{$lang_banned['ban_type']}</td>
                            <td>
                                <select name=\"ban_type\">
                                    <option value=\"ip_banned\" >{$lang_banned['ip']}</option>
                                    <option value=\"account_banned\" >{$lang_banned['account']}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{$lang_banned['entry']}</td>
                            <td>
                                <input type=\"text\" name=\"entry\" size=\"24\" maxlength=\"20\" value=\"\" />
                            </td>
                        </tr>
                        <tr>
                            <td>{$lang_banned['ban_time']}</td>
                            <td>
                                <input type=\"text\" name=\"bantime\" size=\"24\" maxlength=\"40\" value=\"1\" />
                            </td>
                        </tr>
                        <tr>
                            <td>{$lang_banned['banreason']}</td>
                            <td>
                                <input type=\"text\" name=\"banreason\" size=\"24\" maxlength=\"255\" value=\"\" />
                            </td>
                        </tr>
                        <tr>
                            <td>";

    makebutton($lang_banned['ban_entry'], "javascript:do_submit()\" type=\"wrn",180);

    $output .= "
                            </td>
                            <td>";

    makebutton($lang_global['back'], "banned.php\" type=\"def",130);

    $output .= "
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
            <br/><br/>
        </center>";
}


//########################################################################################################################
//DO BAN NEW IP/ACC
//########################################################################################################################
function do_add_entry()
{
    global $realm_db, $user_name, $output, $action_permission, $user_lvl;
    valid_login($action_permission['insert']);

    if((empty($_GET['ban_type']))||(empty($_GET['entry'])) ||(empty($_GET['bantime'])))
        redirect("banned.php?error=1&action=add_entry");

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $ban_type = $sqlr->quote_smart($_GET['ban_type']);

    $entry = $sqlr->quote_smart($_GET['entry']);
    if ($ban_type == "account_banned")
    {
        $result1 = $sqlr->query("SELECT id FROM account WHERE username ='$entry'");
        if (!$sqlr->num_rows($result1))
            redirect("banned.php?error=4&action=add_entry");
        else
            $entry = $sqlr->result($result1, 0, 'id');
    }

    $bantime = time() + (3600 * $sqlr->quote_smart($_GET['bantime']));
    $banreason = (isset($_GET['banreason']) && ($_GET['banreason'] != '')) ? $sqlr->quote_smart($_GET['banreason']) : "none";

    if ($ban_type === "account_banned")
    {
        $result = $sqlr->query("SELECT count(*) FROM account_banned WHERE id = '$entry'");
        if(!$sqlr->result($result, 0))
            $sqlr->query("INSERT INTO account_banned (id, bandate, unbandate, bannedby, banreason, active)
                            VALUES ('$entry',".time().",$bantime,'$user_name','$banreason', 1)");
    }
    else
        $sqlr->query("INSERT INTO ip_banned (ip, bandate, unbandate, bannedby, banreason)
                        VALUES ('$entry',".time().",$bantime,'$user_name','$banreason')");

    if ($sqlr->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
    else
        redirect("banned.php?error=2&ban_type=$ban_type");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

$lang_banned = lang_banned();

switch ($err)
{
    case 1:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_global['empty_fields']}</font>
          </h1>";
        break;

    case 2:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_banned['err_del_entry']}</font>
          </h1>";
        break;

    case 3:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_banned['updated']}</font>
          </h1>";
        break;

    case 4:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_banned['acc_not_found']}</font>
          </h1>";
        break;

    default: //no error
        $output .= "
          <h1>{$lang_banned['banned_list']}</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "do_delete_entry":
        do_delete_entry();
        break;

    case "add_entry":
        add_entry();
        break;

    case "do_add_entry":
        do_add_entry();
        break;

    default:
        show_list();
}

unset($action);
unset($action_permission);
unset($lang_banned);

require_once("footer.php");

?>
