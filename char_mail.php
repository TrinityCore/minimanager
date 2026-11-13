<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/mail_lib.php';
require_once 'libs/item_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS MAILS
//########################################################################################################################
function build_mail_items_cell($items_list, $site, &$sqlm, &$sqlw) {
    if (empty($items_list)) return "-";
    $grouped = array();
    $total_slots = 0;
    foreach (explode(",", $items_list) as $p) {
        $parts = explode(":", $p);
        $entry = intval(reset($parts));
        $cnt   = (count($parts) > 1) ? intval(end($parts)) : 1;
        if ($entry > 0) {
            $total_slots++;
            if (!isset($grouped[$entry])) {
                $grouped[$entry] = array("total" => 0, "stacks" => array());
            }
            $grouped[$entry]["total"] += $cnt;
            $grouped[$entry]["stacks"][] = $cnt;
        }
    }
    if (empty($grouped)) return "-";
    $tips = array();
    foreach ($grouped as $entry => $data) {
        $ico = get_item_icon($entry, $sqlm, $sqlw);
        $nm  = get_item_name($entry, $sqlw);
        if (empty($nm)) $nm = "Item #" . $entry;
        $nm_safe = htmlspecialchars($nm, ENT_QUOTES, "UTF-8");
        $tot = $data["total"];
        $st  = ($tot > 1) ? " x" . $tot : "";
        if (count($data["stacks"]) > 1) {
            $counts = array_count_values($data["stacks"]);
            $parts_str = array();
            foreach ($counts as $val => $times) {
                $parts_str[] = ($times > 1) ? ($times . "x" . $val) : (string)$val;
            }
            $st .= " (" . implode(" + ", $parts_str) . ")";
        }
        $tips[] = "<img src=\"" . $ico . "\" width=\"16\" height=\"16\" align=\"absmiddle\" /> " . $nm_safe . $st . " (#" . $entry . ")";
    }
    $tip_html = implode("<br />", $tips);
    reset($grouped);
    $first_e = key($grouped);
    $first_ico = get_item_icon($first_e, $sqlm, $sqlw);
    $extra = $total_slots - 1;
    $badge = ($extra > 0) ? " <span style=\"font-weight:bold;color:#FFCC00;\">(+" . $extra . ")</span>" : "";
    return "<div style=\"white-space:nowrap;\"><span onmousemove=\"toolTip(this.nextSibling.innerHTML, \x27item_tooltip\x27)\" onmouseout=\"toolTip()\" style=\"cursor:help;\"><a style=\"padding:2px;\" href=\"" . $site . $first_e . "\" target=\"_blank\"><img class=\"bag_icon\" src=\"" . $first_ico . "\" alt=\"\" /></a>" . $badge . "</span><span style=\"display:none;\">" . $tip_html . "</span></div>";
}
function char_mail(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char, $lang_mail,
            $realm_id, $realm_db, $mmfpm_db, $characters_db, $world_db,
            $realmid,
            $action_permission, $user_lvl, $user_name,
            $item_datasite, $itemperpage;

    wowhead_tt();

    require_once 'core/char/char_security.php';

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

    // getting character data from database
    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender
                            FROM characters WHERE guid = '.$id.' LIMIT 1');

    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        // we get user permissions first
        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            //------------------------Character Tabs---------------------------------
            // we start with a lead of 10 spaces,
            //  because last line of header is an opening tag with 8 spaces
            //  keep html indent in sync, so debuging from browser source would be easy to read
            $output .= '
                    <center>
                        <div id="tab_content">
                            <h1>'.$lang_char['mail'].'</h1>
                            <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                            <br /><br />
                            <table class="lined" style="width: 100%">';

            //---------------Page Specific Starts Ends here----------------------------
            $sqlm = new SQL; $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
            $sqlw = new SQL; $sqlw->connect($world_db[$realmid]['addr'], $world_db[$realmid]['user'], $world_db[$realmid]['pass'], $world_db[$realmid]['name']);
            $query = $sqlc->query('SELECT a.id as id, a.messageType as messagetype, a.sender as sender, a.subject as subject, a.body as body, a.has_items as hasitems, a.money as money, a.cod as cod, a.checked as checked, GROUP_CONCAT(CONCAT(c.itemEntry, ":", c.count) ORDER BY b.item_guid ASC SEPARATOR ",") AS items_list FROM mail a LEFT JOIN mail_items b ON a.id = b.mail_id LEFT JOIN item_instance c ON b.item_guid = c.guid WHERE a.receiver = '.$id.' GROUP BY a.id ORDER BY a.id DESC LIMIT '.$start.', '.$itemperpage);

            $total_mail = $sqlc->result($sqlc->query('SELECT count(*) FROM mail WHERE receiver= '.$id .''), 0);


            $output .= '
                                <tr>
                                    <td align="left">
                                        Total Mails: '.$total_mail.'
                                    </td>
                                    <td align="right" width="45%">';
            $output .= generate_pagination('char_mail.php?start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $total_mail, $itemperpage, $start);
            $output .= '
                                    </td>
                                </tr>
                            </table>
                            <table class="lined" style="width: 100%">
                                <tr>
                                    <th width="5%">'.$lang_mail['mail_type'].'</th>
                                    <th width="10%">'.$lang_mail['sender'].'</th>
                                    <th width="15%">'.$lang_mail['subject'].'</th>
                                    <th width="5%">'.$lang_mail['has_items'].'</th>
                                    <th width="25%">'.$lang_mail['text'].'</th>
                                    <th width="20%">'.$lang_mail['money'].'</th>
                                    <th width="5%">'.$lang_mail['checked'].'</th>
                                </tr>';

            while ($mail = $sqlc->fetch_assoc($query))
            {
                $output .= '
                                <tr valign=top>
                                    <td>'.get_mail_source($mail['messagetype']).'</td>
                                    <td><a href="char.php?id='.$mail['sender'].'">'.get_char_name($mail['sender']).'</a></td>
                                    <td>'.htmlspecialchars($mail['subject'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</td>
                                    <td>'.build_mail_items_cell($mail['items_list'], $item_datasite, $sqlm, $sqlw).'</td>
                                    <td>'.htmlspecialchars($mail['body'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</td>
                                    <td>
                                        '.substr($mail['money'],  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
                                        '.substr($mail['money'], -4,  2).'<img src="img/silver.gif" alt="" align="middle" />
                                        '.substr($mail['money'], -2).'<img src="img/copper.gif" alt="" align="middle" />
                                    </td>
                                    <td>'.get_check_state($mail['checked']).'</td>
                                </tr>';
            }
            //---------------Page Specific Data Ends here----------------------------
            //---------------Character Tabs Footer-----------------------------------
            $output .= '
                            </table>
                        </div>
                        <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                        <br />
                    </center>
                    <!-- end of char_mail.php -->';
        }
        else
            error($lang_char['no_permission']);
    }
    else
        error($lang_char['no_char_found']);
}


//########################################################################################################################
// MAIN
//########################################################################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();
$lang_mail = lang_mail();

$output .= '
        <div class="top">
            <h1>'.$lang_char['character'].'</h1>
        </div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_mail($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);
unset($lang_mail);

require_once 'footer.php';


?>
