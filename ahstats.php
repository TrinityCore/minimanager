<?php

require_once("header.php");
require_once("scripts/get_lib.php");
require_once("libs/item_lib.php");
valid_login($action_permission['read']);

//#############################################################################
// BROWSE AUCTIONS
//#############################################################################
function browse_auctions(&$sqlr, &$sqlc)
{
    global $lang_auctionhouse, $lang_global, $lang_item, $output, $characters_db, $world_db, $realm_id, $itemperpage, $item_datasite, $server, $user_lvl, $user_id;
    wowhead_tt();

    $red = "\"#DD5047\"";
    $blue = "\"#0097CD\"";
    $sidecolor = [1 => $blue,2 => $red,3 => $blue,4 => $blue,5 => $red,6 => $red,7 => $blue,8 => $red,10 => $red,11 => $blue];
    $hiddencols = [1,8,9,10];

    //$sqlc = new SQL;
    //$sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "time";
    if (!preg_match("/^[_[:lower:]]{1,15}$/", $order_by))
        $order_by="time";

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (!preg_match("/^[01]{1}$/", $dir))
        $dir=1;

    $order_dir = ($dir) ? "ASC" : "DESC";
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    if( !$user_lvl && !$server[$realm_id]['both_factions'])
    {
        $result = $sqlc->query("SELECT `race` FROM `characters` WHERE `account` = $user_id AND `totaltime` = (SELECT MAX(totaltime) FROM `characters` WHERE `account` = $user_id) LIMIT 1");

        if ($sqlc->num_rows($result))
        {
            $order_side = (in_array($sqlc->result($result, 0, 'race'), [2,5,6,8,10])) ? " AND `characters`.`race` IN (2,5,6,8,10) " : " AND `characters`.`race` IN (1,3,4,7,11) ";
        }
        else
            $order_side = "";
    }
    else
        $order_side = "";

    //==========================Browse/Search CHECK (Multi-AND)==============================
    $search_values = isset($_GET['search_value']) ? (array)$_GET['search_value'] : [''];
    $search_bys    = isset($_GET['search_by']) ? (array)$_GET['search_by'] : ['item_name'];
    $search_class   = (isset($_GET['search_class']) && is_numeric($_GET['search_class'])) ? intval($_GET['search_class']) : -1;
    $search_quality = (isset($_GET['search_quality']) && is_numeric($_GET['search_quality'])) ? intval($_GET['search_quality']) : -1;
    $min_req  = (isset($_GET['min_req']) && $_GET['min_req'] !== '' && is_numeric($_GET['min_req'])) ? intval($_GET['min_req']) : '';
    $max_req  = (isset($_GET['max_req']) && $_GET['max_req'] !== '' && is_numeric($_GET['max_req'])) ? intval($_GET['max_req']) : '';
    $min_ilvl = (isset($_GET['min_ilvl']) && $_GET['min_ilvl'] !== '' && is_numeric($_GET['min_ilvl'])) ? intval($_GET['min_ilvl']) : '';
    $max_ilvl = (isset($_GET['max_ilvl']) && $_GET['max_ilvl'] !== '' && is_numeric($_GET['max_ilvl'])) ? intval($_GET['max_ilvl']) : '';

    $search_filter = '';
    $search_url = '';
    $active_filters = [];

    if ($search_class >= 0) {
        $search_filter .= " AND `item_template`.`class` = $search_class";
        $search_url .= "&amp;search_class=$search_class";
    }
    if ($search_quality >= 0) {
        $search_filter .= " AND `item_template`.`Quality` = $search_quality";
        $search_url .= "&amp;search_quality=$search_quality";
    }
    if ($min_req !== '') {
        $search_filter .= " AND `item_template`.`RequiredLevel` >= $min_req";
        $search_url .= "&amp;min_req=$min_req";
    }
    if ($max_req !== '') {
        $search_filter .= " AND `item_template`.`RequiredLevel` <= $max_req";
        $search_url .= "&amp;max_req=$max_req";
    }
    if ($min_ilvl !== '') {
        $search_filter .= " AND `item_template`.`ItemLevel` >= $min_ilvl";
        $search_url .= "&amp;min_ilvl=$min_ilvl";
    }
    if ($max_ilvl !== '') {
        $search_filter .= " AND `item_template`.`ItemLevel` <= $max_ilvl";
        $search_url .= "&amp;max_ilvl=$max_ilvl";
    }

    $allowed_ah_by = ['item_name', 'item_id', 'seller_name', 'buyer_name'];
    for ($idx = 0; $idx < count($search_values); $idx++)
    {
        $val_raw = trim((string)$search_values[$idx]);
        $field = isset($search_bys[$idx]) ? $search_bys[$idx] : 'item_name';
        if (!in_array($field, $allowed_ah_by))
            $field = 'item_name';

        if ($val_raw === '')
            continue;

        $active_filters[] = ['by' => $field, 'val' => $val_raw];
        $search_url .= '&amp;search_by%5B%5D=' . urlencode($field) . '&amp;search_value%5B%5D=' . urlencode($val_raw);
        $val = $sqlc->quote_smart($val_raw);

        if ($field === 'item_name')
            $search_filter .= " AND `item_template`.`name` LIKE '%$val%'";
        elseif ($field === 'item_id')
            $search_filter .= " AND `item_instance`.`itemEntry` = " . intval($val_raw);
        elseif ($field === 'seller_name')
        {
            $res_s = $sqlc->query("SELECT `guid` FROM `characters` WHERE BINARY `name` LIKE '%$val%'");
            $s_ids = [0];
            while ($rs = $sqlc->fetch_row($res_s))
                $s_ids[] = intval($rs[0]);
            $search_filter .= " AND `auctionhouse`.`itemowner` IN (" . implode(',', $s_ids) . ")";
        }
        elseif ($field === 'buyer_name')
        {
            $res_b = $sqlc->query("SELECT `guid` FROM `characters` WHERE BINARY `name` LIKE '%$val%'");
            $b_ids = [-1];
            while ($rb = $sqlc->fetch_row($res_b))
                $b_ids[] = intval($rb[0]);
            $search_filter .= " AND `auctionhouse`.`buyguid` IN (" . implode(',', $b_ids) . ")";
        }
    }

    $has_search = ($search_filter !== '');
    if (empty($active_filters))
        $active_filters[] = ['by' => 'item_name', 'val' => ''];

    $query_1 = $sqlc->query("SELECT count(*) FROM `".$characters_db[$realm_id]['name']."`.`auctionhouse` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` ON `auctionhouse`.`itemowner`=`characters`.`guid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`item_instance` ON `auctionhouse`.`itemguid`=`item_instance`.`guid` LEFT JOIN `".$world_db[$realm_id]['name']."`.`item_template` ON `item_instance`.`itemEntry`=`item_template`.`entry` WHERE `item_instance`.`itemEntry` IS NOT NULL AND `item_instance`.`itemEntry` > 0 $search_filter $order_side");
    $result = $sqlc->query("SELECT BINARY `characters`.`name` AS `seller`, `item_instance`.`itemEntry` AS `itemid`, `item_template`.`name` AS `itemname`, `auctionhouse`.`buyoutprice` AS `buyout`, TIMESTAMPDIFF(SECOND, NOW(), FROM_UNIXTIME(`auctionhouse`.`time`)), BINARY `c2`.`name` AS `encherisseur`, `auctionhouse`.`lastbid`, `auctionhouse`.`startbid`, `item_instance`.`count` AS qty, `characters`.`race` AS seller_race, `c2`.`race` AS buyer_race FROM `".$characters_db[$realm_id]['name']."`.`auctionhouse` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` ON `auctionhouse`.`itemowner`=`characters`.`guid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`item_instance` ON `auctionhouse`.`itemguid`=`item_instance`.`guid` LEFT JOIN `".$world_db[$realm_id]['name']."`.`item_template` ON `item_instance`.`itemEntry`=`item_template`.`entry` WHERE `item_instance`.`itemEntry` IS NOT NULL AND `item_instance`.`itemEntry` > 0 $search_filter $order_side ORDER BY `auctionhouse`.`$order_by` $order_dir LIMIT $start, $itemperpage");
    $all_record = $sqlc->result($query_1,0);

    //=====================top tage navigaion starts here========================
    $output .="
        <center>
            <table class=\"top_hidden\">
                <tr>
                    <td width=\"80%\">
                        <script type=\"text/javascript\">
                            function addAhFilter() {
                                var container = document.getElementById(\"ah_filters_container\");
                                var rows = container.getElementsByClassName(\"ah_filter_row\");
                                var clone = rows[0].cloneNode(true);
                                clone.getElementsByTagName(\"input\")[0].value = \"\";
                                var remBtn = clone.getElementsByClassName(\"rem_ah_filter_btn\")[0];
                                if (remBtn) remBtn.style.display = \"inline-block\";
                                container.appendChild(clone);
                            }
                            function removeAhFilter(btn) {
                                var container = document.getElementById(\"ah_filters_container\");
                                if (container.getElementsByClassName(\"ah_filter_row\").length > 1) {
                                    btn.parentNode.remove();
                                }
                            }
                        </script>
                        <form action=\"ahstats.php\" method=\"get\" name=\"form\">
                        <input type=\"hidden\" name=\"error\" value=\"2\" />
                            <table class=\"hidden\">
                                <tr>
                                    <td valign=\"top\">
                                        <div id=\"ah_filters_container\">";
    foreach ($active_filters as $f_idx => $flt)
    {
        $f_by = $flt['by'];
        $f_val = htmlspecialchars($flt['val']);
        $show_rem = ($f_idx > 0) ? 'inline-block' : 'none';
        $output .= "
                                            <div class=\"ah_filter_row\" style=\"margin-bottom:3px; white-space:nowrap;\">
                                                <input type=\"text\" size=\"20\" name=\"search_value[]\" value=\"$f_val\" placeholder=\"{$lang_global['search_placeholder']}\" />
                                                <select name=\"search_by[]\">
                                                    <option".($f_by == 'item_name' ? " selected=\"selected\"" : "")." value=\"item_name\">{$lang_auctionhouse['item_name']}</option>
                                                    <option".($f_by == 'item_id' ? " selected=\"selected\"" : "")." value=\"item_id\">{$lang_auctionhouse['item_id']}</option>
                                                    <option".($f_by == 'seller_name' ? " selected=\"selected\"" : "")." value=\"seller_name\">{$lang_auctionhouse['seller_name']}</option>
                                                    <option".($f_by == 'buyer_name' ? " selected=\"selected\"" : "")." value=\"buyer_name\">{$lang_auctionhouse['buyer_name']}</option>
                                                </select>
                                                <button type=\"button\" class=\"rem_ah_filter_btn\" onclick=\"removeAhFilter(this)\" style=\"display:$show_rem; cursor:pointer;\" title=\"{$lang_global['remove_filter']}\">-</button>
                                            </div>";
    }
    $output .= "
                                        </div>
                                    </td>
                                    <td valign=\"top\">
                                        <button type=\"button\" onclick=\"addAhFilter()\" style=\"cursor:pointer;\" title=\"{$lang_global['add_and_condition']}\">+</button>
                                    </td>
                                    <td valign=\"top\" style=\"white-space:nowrap;\">
                                        <select name=\"search_class\" title=\"{$lang_global['item_type']}\">
                                            <option".($search_class == -1 ? " selected=\"selected\"" : "")." value=\"-1\">{$lang_global['type']}: {$lang_auctionhouse['all']}</option>
                                            <option".($search_class == 0 ? " selected=\"selected\"" : "")." value=\"0\">{$lang_item['consumable']}</option>
                                            <option".($search_class == 1 ? " selected=\"selected\"" : "")." value=\"1\">{$lang_item['bag']}</option>
                                            <option".($search_class == 2 ? " selected=\"selected\"" : "")." value=\"2\">{$lang_item['weapon']}</option>
                                            <option".($search_class == 3 ? " selected=\"selected\"" : "")." value=\"3\">{$lang_global['gem']}</option>
                                            <option".($search_class == 4 ? " selected=\"selected\"" : "")." value=\"4\">{$lang_item['armor']}</option>
                                            <option".($search_class == 5 ? " selected=\"selected\"" : "")." value=\"5\">{$lang_item['reagent']}</option>
                                            <option".($search_class == 6 ? " selected=\"selected\"" : "")." value=\"6\">{$lang_global['projectile']}</option>
                                            <option".($search_class == 7 ? " selected=\"selected\"" : "")." value=\"7\">{$lang_item['trade_goods']}</option>
                                            <option".($search_class == 9 ? " selected=\"selected\"" : "")." value=\"9\">{$lang_item['recipe']}</option>
                                            <option".($search_class == 11 ? " selected=\"selected\"" : "")." value=\"11\">{$lang_item['quiver']}</option>
                                            <option".($search_class == 13 ? " selected=\"selected\"" : "")." value=\"13\">{$lang_global['key']}</option>
                                            <option".($search_class == 14 ? " selected=\"selected\"" : "")." value=\"14\">{$lang_item['permanent']}</option>
                                            <option".($search_class == 15 ? " selected=\"selected\"" : "")." value=\"15\">{$lang_item['misc_short']}</option>
                                            <option".($search_class == 16 ? " selected=\"selected\"" : "")." value=\"16\">{$lang_global['glyph']}</option>
                                        </select>
                                        <select name=\"search_quality\" title=\"{$lang_global['quality']}\">
                                            <option".($search_quality == -1 ? " selected=\"selected\"" : "")." value=\"-1\">{$lang_global['quality']}: {$lang_auctionhouse['all']}</option>
                                            <option".($search_quality == 0 ? " selected=\"selected\"" : "")." value=\"0\">{$lang_item['poor']}</option>
                                            <option".($search_quality == 1 ? " selected=\"selected\"" : "")." value=\"1\">{$lang_item['common']}</option>
                                            <option".($search_quality == 2 ? " selected=\"selected\"" : "")." value=\"2\">{$lang_item['uncommon']}</option>
                                            <option".($search_quality == 3 ? " selected=\"selected\"" : "")." value=\"3\">{$lang_item['rare']}</option>
                                            <option".($search_quality == 4 ? " selected=\"selected\"" : "")." value=\"4\">{$lang_item['epic']}</option>
                                            <option".($search_quality == 5 ? " selected=\"selected\"" : "")." value=\"5\">{$lang_item['legendary']}</option>
                                            <option".($search_quality == 6 ? " selected=\"selected\"" : "")." value=\"6\">{$lang_item['artifact']}</option>
                                        </select>
                                        <br />
                                        <div style=\"margin-top:4px;\">
                                            {$lang_global['req_level_short']} <input type=\"text\" size=\"3\" maxlength=\"3\" name=\"min_req\" value=\"$min_req\" placeholder=\"Min\" /> - <input type=\"text\" size=\"3\" maxlength=\"3\" name=\"max_req\" value=\"$max_req\" placeholder=\"Max\" />
                                            &nbsp;|&nbsp;
                                            iLvl: <input type=\"text\" size=\"4\" maxlength=\"4\" name=\"min_ilvl\" value=\"$min_ilvl\" placeholder=\"Min\" /> - <input type=\"text\" size=\"4\" maxlength=\"4\" name=\"max_ilvl\" value=\"$max_ilvl\" placeholder=\"Max\" />
                                        </div>
                                    </td>
                                    <td valign=\"top\">";

    makebutton($lang_global['search'], "javascript:do_submit()",80);

    $output .= "
                                    </td>
                                    <td valign=\"top\">";

    ($has_search) ? makebutton($lang_global['clear'], "ahstats.php",80) : $output .= "";

    $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td width=\"25%\" align=\"right\">";

    $output .= generate_pagination("ahstats.php?order_by=$order_by".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
    $output .= "
                    </td>
                </tr>
            </table>
            <table class=\"lined\">
                <tr>
                    <th width=\"10%\"><a href=\"ahstats.php?order_by=itemowner&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='itemowner' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['seller']}</a></th>
                    <th width=\"20%\"><a href=\"ahstats.php?order_by=itemEntry&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='itemEntry' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['item']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=buyoutprice&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='buyoutprice' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['buyoutprice']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=time&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='time' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['timeleft']}</a></th>
                    <th width=\"10%\"><a href=\"ahstats.php?order_by=buyguid&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='buyguid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['buyer']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=lastbid&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='lastbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['lastbid']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=startbid&amp;start=$start".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='startbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['firstbid']}</a></th>
                </tr>";

    global $mmfpm_db, $world_db;
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    while ($rows = $sqlc->fetch_row($result))
    {
        $output .= "
                <tr>";
        foreach($rows as $row => $value)
        {
            switch ($row)
            {
                case 4:
                    $value = ($value >= 0)? (floor($value / 86400).$lang_auctionhouse['dayshortcut']." ". floor(($value % 86400)/3600).$lang_auctionhouse['hourshortcut']." ".floor((($value % 86400)%3600)/60).$lang_auctionhouse['mnshortcut']) : $lang_auctionhouse['auction_over'];
                    break;
                case 5:
                    $value = "<b>".((!empty($rows[10])) ? "<font color=".$sidecolor[$rows[10]].">".htmlentities($value)."</font>" : "N/A")."</b>";
                    break;
                case 7:
                case 6:
                case 3:
                    $g = floor($value/10000);
                    $value -= $g*10000;
                    $s = floor($value/100);
                    $value -= $s*100;
                    $c = $value;
                    $value = $g."<img src=\"./img/gold.gif\" alt=\"\" /> ".$s."<img src=\"./img/silver.gif\" alt=\"\" /> ".$c."<img src=\"./img/copper.gif\" alt=\"\" /> ";
                    break;
                case 2:
                    $value = "<a href=\"$item_datasite$rows[1]\" target=\"_blank\" onmouseover=\"toolTip,'item_tooltip'\"><img src=\"".get_item_icon($rows[1], $sqlm, $sqlw)."\" class=\"".get_item_border($rows[1], $sqlw)."\" alt=\"$value\" /><br/>$value".(($rows[8]>1) ? " (x$rows[8])" : "")."</a>";
                    break;
                case 0:
                    $value = "<b>".((!empty($rows[9])) ? "<font color=".$sidecolor[$rows[9]].">".htmlentities($value)."</font>" : "N/A")."</b>";
                    break;
            }
            if (!in_array($row,$hiddencols))
            $output .= "
                    <td>
                        <center>
                        ".$value."
                        </center>
                    </td>";
        }
        $output .= "
                </tr>";
    }
    $output .= "
                <tr>
                    <td  colspan=\"7\" class=\"hidden\" align=\"right\" width=\"25%\">";
    $output .= generate_pagination("ahstats.php?order_by=$order_by".( $has_search ? "$search_url&amp;error=2" : "" )."&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
    $output .= "
                    </td>
                </tr>
                <tr>
                    <td colspan=\"7\" class=\"hidden\" align=\"right\">{$lang_auctionhouse['total_auctions']} : $all_record
                    </td>
                </tr>
            </table>
        </center>";
}
//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
    <div class=\"top\">";

$lang_auctionhouse = lang_auctionhouse();

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
            <font class=\"error\">{$lang_auctionhouse['search_results']}</font>
        </h1>";
        break;
    default:
        $output .= "
        <h1>{$lang_auctionhouse['auctionhouse']}</h1>";
}

unset($err);

$output .= "
    </div>";
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "unknown":
        break;
    default:
        browse_auctions($sqlr, $sqlc);
}

unset($action);
unset($action_permission);
unset($lang_auctionhouse);
require_once("footer.php");

?>
