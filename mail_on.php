<?php


require_once 'header.php';
require_once 'libs/mail_lib.php';
require_once 'libs/item_lib.php';
require_once 'scripts/get_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
//  INGAME MAIL
//########################################################################################################################
function do_search(&$sqlc)
{
    global $lang_global, $lang_mail,
            $output, $itemperpage, $item_datasite,
            $world_db, $characters_db, $realm_id;
    wowhead_tt();

    $sql = new SQL;
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start = 0;

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'id';
    if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by));
    else
        $order_by = 'id';

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir = 1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $query = $sql->query("SELECT a.id, a.messageType, a.sender, a.receiver, a.subject, a.body, a.has_items, a.money, a.cod, a.checked, b.item_guid, c.itemEntry
                          FROM mail a INNER JOIN mail_items b ON a.id = b.mail_id LEFT JOIN item_instance c ON b.item_guid = c.guid ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
    $total_found = $sql->num_rows($query);
    $this_page = $sql->num_rows($query);
    $query_1 = $sql->query("SELECT count(*) FROM `mail`");
    $all_record = $sql->result($query_1,0);

    //==========================top page navigation starts here========================
    $output .="
        <center>
            <table class=\"top_hidden\">
                <tr>
                    <td>
                        <table class=\"hidden\">
                        <tr>
                            <td>
                                <form action=\"mail_on.php\" method=\"get\" name=\"form\">
                                    <input type=\"hidden\" name=\"action\" value=\"search\" />
                                    <input type=\"hidden\" name=\"error\" value=\"4\" />
                                    <input type=\"text\" size=\"45\" name=\"search_value\" />
                                    <select name=\"search_by\">
                                        <option value=\"sender\">Sender</option>
                                        <option value=\"receiver\">Receiver</option>
                                    </select>
                                </form>
                            </td>
                            <td>";
    makebutton($lang_global['search'], "javascript:do_submit()",80);
    $output .= "
                            </td>
                        </tr>
                    </table>
                <td align=\"right\">";
    $output .=  generate_pagination("mail_on.php?action=do_search&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
    $output .= "
                </td>
            </tr>
        </table>";
    //==========================top page navigation ENDS here ========================
    $output .= "
        <table class=\"lined\">
            <tr>
                <th width=\"5%\">".$lang_mail['id']."</th>
                <th width=\"5%\">".$lang_mail['mail_type']."</th>
                <th width=\"10%\">".$lang_mail['sender']."</th>
                <th width=\"10%\">".$lang_mail['receiver']."</th>
                <th width=\"15%\">".$lang_mail['subject']."</th>
                <th width=\"5%\">".$lang_mail['has_items']."</th>
                <th width=\"25%\">".$lang_mail['text']."</th>
                <th width=\"20%\">".$lang_mail['money']."</th>
                <th width=\"5%\">".$lang_mail['checked']."</th>
            </tr>";

    while ($mail = $sql->fetch_array($query))
    {
        $g = floor($mail[7]/10000);
        $mail[7] -= $g*10000;
        $s = floor($mail[7]/100);
        $mail[7] -= $s*100;
        $c = $mail[7];
        $money = "";
        if ($mail[7] > 0)
            $money = $g."<img src=\"./img/gold.gif\" /> ".$s."<img src=\"./img/silver.gif\" /> ".$c."<img src=\"./img/copper.gif\" /> ";

        $output .= "
            <tr valign=top>
                <td>$mail[0]</td>
                <td>".get_mail_source($mail[1])."</td>
                <td><a href=\"char.php?id=$mail[2]\">".get_char_name($mail[2])."</a></td>
                <td><a href=\"char.php?id=$mail[3]\">".get_char_name($mail[3])."</a></td>
                <td>$mail[4]</td>";

        $output .= "
                <td>
                    <a style=\"padding:2px;\" href=\"$item_datasite{$mail[11]}\" target=\"_blank\">
                        <img class=\"bag_icon\" src=\"".get_item_icon($mail[11])."\" alt=\"\" />
                    </a>";
        //$output .= maketooltip("<img src=\"./img/up.gif\" alt= />", $item_datasite{$mail[11]}, $mail[11], "item_tooltip", "target=_blank");
        $output .= "
                </td>
                <td>".$mail[5]."</td>
                <td>$money</td>
                <td>".get_check_state($mail[9])."</td>
            </tr>";
    }
    /*--------------------------------------------------*/

    $output .= "
            <tr>
                <td colspan=\"6\" class=\"hidden\" align=\"right\">All Mails: $all_record</td>
            </tr>
        </table>
    </center>";

    $sql->close();
}

//########################################################################################################################
//  SEARCH
//########################################################################################################################
function search() {
    global $lang_global, $lang_mail,
            $output, $itemperpage, $item_datasite,
            $mangos_db, $characters_db, $realm_id, $sql_search_limit;
    wowhead_tt();

    if(!isset($_GET['search_value']) || !isset($_GET['search_by']))
        redirect("mail_on.php?error=2");

    $sql = new SQL;
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $search_value = $sql->quote_smart($_GET['search_value']);
    $search_by = $sql->quote_smart($_GET['search_by']);
    $search_menu = ['sender', 'receiver'];
    // if (!array_key_exists($search_by, $search_menu)) $search_by = 'sender';

    $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
    $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"id";
    $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
    $order_dir = ($dir) ? "ASC" : "DESC";
    $dir = ($dir) ? 0 : 1;

    $temp = $sql->query("SELECT guid FROM `characters` WHERE BINARY name = '$search_value'");
    $search_value = $sql->result($temp, 0, 'guid');

    $query_1 = $sql->query("SELECT count(*) FROM `mail`");

    $query = $sql->query("SELECT a.id, a.messageType, a.sender, a.receiver, a.subject, a.body, a.has_items, a.money, a.cod, a.checked, b.item_guid, c.itemEntry
                            FROM mail a
                            INNER JOIN mail_items b ON a.id = b.mail_id
                            LEFT JOIN item_instance c ON b.item_guid = c.guid
                            WHERE a.$search_by = $search_value
                            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");

    $this_page = $sql->num_rows($query);
    $all_record = $sql->result($query_1,0);

    $total_found = $sql->num_rows($query);
    //==========================top page navigation starts here========================
    $output .="
            <center>
                <table class=\"top_hidden\">
                    <tr>
                        <td>
                            <table class=\"hidden\">
                                <tr>
                                    <td>
                                        <form action=\"mail_on.php\" method=\"get\" name=\"form\">
                                            <input type=\"hidden\" name=\"action\" value=\"search\" />
                                            <input type=\"hidden\" name=\"error\" value=\"4\" />
                                            <input type=\"text\" size=\"45\" name=\"search_value\" />
                                            <select name=\"search_by\">
                                                <option value=\"sender\">Sender</option>
                                                <option value=\"receiver\">Receiver</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>";

    makebutton($lang_global['search'], "javascript:do_submit()",80);

    $output .= "
                                    </td>
                                </tr>
                            </table>
                    <td align=\"right\">";

    $output .= generate_pagination("mail_on.php?action=search&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
    $output .= "
                    </td>
                </tr>
            </table>";
    //==========================top page navigation ENDS here ========================

    $output .= "
            <table class=\"lined\">
                <tr>
                    <th width=\"5%\">".$lang_mail['id']."</th>
                    <th width=\"5%\">".$lang_mail['mail_type']."</th>
                    <th width=\"10%\">".$lang_mail['sender']."</th>
                    <th width=\"10%\">".$lang_mail['receiver']."</th>
                    <th width=\"15%\">".$lang_mail['subject']."</th>
                    <th width=\"5%\">".$lang_mail['has_items']."</th>
                    <th width=\"25%\">".$lang_mail['text']."</th>
                    <th width=\"20%\">".$lang_mail['money']."</th>
                    <th width=\"5%\">".$lang_mail['checked']."</th>
                </tr>";

    while ($mail = $sql->fetch_array($query))
    {
        $g = floor($mail[7]/10000);
        $mail[7] -= $g*10000;
        $s = floor($mail[7]/100);
        $mail[7] -= $s*100;
        $c = $mail[7];
        $money = "";

        if ($mail[7] > 0)
            $money = $g."<img src=\"./img/gold.gif\" /> ".$s."<img src=\"./img/silver.gif\" /> ".$c."<img src=\"./img/copper.gif\" /> ";

        $output .= "
                <tr valign=top>
                    <td>$mail[0]</td>
                    <td>".get_mail_source($mail[1])."</td>
                    <td><a href=\"char.php?id=$mail[2]\">".get_char_name($mail[2])."</a></td>
                    <td><a href=\"char.php?id=$mail[3]\">".get_char_name($mail[3])."</a></td>
                    <td>$mail[4]</td>
                    <td>";
        $output .= "
                        <a style=\"padding:2px;\" href=\"$item_datasite{$mail[11]}\" target=\"_blank\">
                            <img class=\"bag_icon\" src=\"".get_item_icon($mail[11])."\" alt=\"\" />
                        </a>";

        //maketooltip("<img src=\"./img/up.gif\" alt=\"\">", $item_datasite{$mail[11]}, $mail[11], "item_tooltip", "target=\"_blank\"");
        $output .= "</td>
                    <td>".$mail[5]."</td>
                    <td>$money</td>
                    <td>".get_check_state($mail[9])."</td>
                    </tr>";
    }
    /*--------------------------------------------------*/

    $output .= "
                    <tr>
                        <td colspan=\"6\" class=\"hidden\" align=\"right\">All Mails: $all_record</td>
                    </tr>
                </table>
            </center>";
    $sql->close();
}
/*--------------------------------------------------*/

//########################################################################################################################
// MAIN
//########################################################################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_mail = lang_mail();

$output .= '
        <div class="top">
            <h1>'.$lang_mail['mail_on'].'</h1>
        </div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
    case "do_search":
        do_search($sqlc);
        break;
    case "search":
        search();
        break;
    default:
        do_search($sqlc);
}

//unset($action);
unset($action_permission);
unset($lang_mail);

require_once 'footer.php';
?>
