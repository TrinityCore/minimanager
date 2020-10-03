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
    $sidecolor = array(1 => $blue,2 => $red,3 => $blue,4 => $blue,5 => $red,6 => $red,7 => $blue,8 => $red,10 => $red,11 => $blue);
    $hiddencols = array(1,8,9,10);

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
            $order_side = (in_array($sqlc->result($result, 0, 'race'),array(2,5,6,8,10))) ? " AND `characters`.`race` IN (2,5,6,8,10) " : " AND `characters`.`race` IN (1,3,4,7,11) ";
        }
        else
            $order_side = "";
    }
    else
        $order_side = "";

    //==========================Browse/Search CHECK==============================
    $search_by = '';
    $search_value = '';
    $search_filter = '';
    $search_class = -1;
    $search_quality = -1;

    if((isset($_GET['search_value']) && isset($_GET['search_by'])) || (isset($_GET['search_class'])) || (isset($_GET['search_quality'])) )
    {
        $search_value = $sqlc->quote_smart($_GET['search_value']);
        $search_by = $sqlc->quote_smart($_GET['search_by']);
        $search_class = $sqlc->quote_smart($_GET['search_class']);
        $search_quality = $sqlc->quote_smart($_GET['search_quality']);

        switch ($search_by)
        {
            case "item_name":
                if(( ($search_class >= 0) || ($search_quality >= 0)) && (!isset($search_value) ))
                {
                    if ($search_class >= 0)
                        $search_filter = "AND item_template.class = '$search_class'";
                    if ($search_quality >= 0)
                        $search_filter = "AND item_template.Quality = '$search_quality'";
                }
                else
                {
                    $item_prefix = "";
                    if ($search_class >= 0)
                        $item_prefix .= "AND item_template.class = '$search_class' ";
                    if ($search_quality >= 0)
                        $item_prefix .= "AND item_template.Quality = '$search_quality' ";

                    $result = $sqlc->query("SELECT `entry` FROM `".$world_db[$realm_id]['name']."`.`item_template` WHERE `name` LIKE '%$search_value%' $item_prefix");
                    $search_filter = "AND item_instance.itemEntry IN(0";

                    while ($item = $sqlc->fetch_row($result))
                        $search_filter .= ", $item[0]";

                    $search_filter .= ")";
                }
                break;

            case "item_id":
                $search_filter = "AND item_instance.itemEntry = '$search_value'";
                break;

            case "seller_name":
                if(( ($search_class >= 0) || ($search_quality >= 0)) && (!isset($search_value) ))
                {
                    if ($search_class >= 0)
                        $search_filter = "AND item_template.class = '$search_class'";
                    if ($search_quality >= 0)
                        $search_filter = "AND item_template.Quality = '$search_quality'";
                }
                else
                {
                    $item_prefix = "";
                    if ($search_class >= 0)
                        $item_prefix .= "AND item_template.class = '$search_class' ";
                    if ($search_quality >= 0)
                        $item_prefix .= "AND item_template.Quality = '$search_quality' ";

                    $result = $sqlc->query("SELECT `guid` FROM `characters` WHERE BINARY `name` LIKE '%$search_value%'");
                    $search_filter = $item_prefix;
                    $search_filter .= "AND auctionhouse.itemowner IN(0";

                    while ($char = $sqlc->fetch_row($result))
                        $search_filter .= ", $char[0]";

                    $search_filter .= ")";
                    $search_filter .= $item_prefix;
                }
                break;

            case "buyer_name":
                if(( ($search_class >= 0) || ($search_quality >= 0)) && (!isset($search_value) ))
                {
                    if ($search_class >= 0) $search_filter = "AND item_template.class = '$search_class'";
                    if ($search_quality >= 0) $search_filter = "AND item_template.Quality = '$search_quality'";
                }
                else
                {
                    $item_prefix = "";
                    if ($search_class >= 0)
                        $item_prefix .= "AND item_template.class = '$search_class' ";
                    if ($search_quality >= 0)
                        $item_prefix .= "AND item_template.Quality = '$search_quality' ";

                    $result = $sqlc->query("SELECT guid FROM `characters` WHERE BINARY name LIKE '%$search_value%'");
                    $search_filter = $item_prefix;
                    $search_filter .= "AND auctionhouse.buyguid IN(-1";

                    while ($char = $sqlc->fetch_row($result))
                        $search_filter .= ", $char[0]";

                    $search_filter .= ")";
                }
                break;

            default:
                redirect("ahstats.php?error=1");
        }
        $query_1 = $sqlc->query("SELECT count(*) FROM `".$characters_db[$realm_id]['name']."`.`auctionhouse` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` ON `auctionhouse`.`itemowner`=`characters`.`guid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`item_instance` ON `auctionhouse`.`itemguid`=`item_instance`.`guid` LEFT JOIN `".$world_db[$realm_id]['name']."`.`item_template` ON `item_instance`.`itemEntry`=`item_template`.`entry` WHERE 1 = 1 $search_filter $order_side");
    }
    else
    {
        $query_1 = $sqlc->query("SELECT count(*) FROM auctionhouse");
    }

    $result = $sqlc->query("SELECT BINARY `characters`.`name` AS `seller`, `item_instance`.`itemEntry` AS `itemid`, `item_template`.`name` AS `itemname`, `auctionhouse`.`buyoutprice` AS `buyout`, `auctionhouse`.`time`-unix_timestamp(), BINARY `c2`.`name` AS `encherisseur`, `auctionhouse`.`lastbid`, `auctionhouse`.`startbid`, `item_instance`.`count` AS qty, `characters`.`race` AS seller_race, `c2`.`race` AS buyer_race FROM `".$characters_db[$realm_id]['name']."`.`auctionhouse` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` ON `auctionhouse`.`itemowner`=`characters`.`guid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`item_instance` ON `auctionhouse`.`itemguid`=`item_instance`.`guid` LEFT JOIN `".$world_db[$realm_id]['name']."`.`item_template` ON `item_instance`.`itemEntry`=`item_template`.`entry` WHERE 1 = 1 $search_filter $order_side ORDER BY `auctionhouse`.`$order_by` $order_dir LIMIT $start, $itemperpage");
    $all_record = $sqlc->result($query_1,0);

    //=====================top tage navigaion starts here========================
    $output .="
        <center>
            <table class=\"top_hidden\">
                <tr>
                    <td width=\"80%\">
                        <form action=\"ahstats.php\" method=\"get\" name=\"form\">
                        <input type=\"hidden\" name=\"error\" value=\"2\" />
                            <table class=\"hidden\">
                                <tr>
                                    <td>
                                        <input type=\"text\" size=\"24\" name=\"search_value\" value=\"$search_value\" />
                                    </td>
                                    <td>
                                        <select name=\"search_by\">
                                            <option".($search_by == 'item_name' ? " selected=\"selected\"" : "")." value=\"item_name\">{$lang_auctionhouse['item_name']}</option>
                                            <option".($search_by == 'item_id' ? " selected=\"selected\"" : "")." value=\"item_id\">{$lang_auctionhouse['item_id']}</option>
                                            <option".($search_by == 'seller_name' ? " selected=\"selected\"" : "")." value=\"seller_name\">{$lang_auctionhouse['seller_name']}</option>
                                            <option".($search_by == 'buyer_name' ? " selected=\"selected\"" : "")." value=\"buyer_name\">{$lang_auctionhouse['buyer_name']}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name=\"search_class\">
                                            <option".($search_class == -1 ? " selected=\"selected\"" : "")." value=\"-1\">{$lang_auctionhouse['all']}</option>
                                            <option".($search_class == 0 ? " selected=\"selected\"" : "")." value=\"0\">{$lang_item['consumable']}</option>
                                            <option".($search_class == 1 ? " selected=\"selected\"" : "")." value=\"1\">{$lang_item['bag']}</option>
                                            <option".($search_class == 2 ? " selected=\"selected\"" : "")." value=\"2\">{$lang_item['weapon']}</option>
                                            <option".($search_class == 4 ? " selected=\"selected\"" : "")." value=\"4\">{$lang_item['armor']}</option>
                                            <option".($search_class == 5 ? " selected=\"selected\"" : "")." value=\"5\">{$lang_item['reagent']}</option>
                                            <option".($search_class == 7 ? " selected=\"selected\"" : "")." value=\"7\">{$lang_item['trade_goods']}</option>
                                            <option".($search_class == 9 ? " selected=\"selected\"" : "")." value=\"9\">{$lang_item['recipe']}</option>
                                            <option".($search_class == 11 ? " selected=\"selected\"" : "")." value=\"11\">{$lang_item['quiver']}</option>
                                            <option".($search_class == 14 ? " selected=\"selected\"" : "")." value=\"14\">{$lang_item['permanent']}</option>
                                            <option".($search_class == 15 ? " selected=\"selected\"" : "")." value=\"15\">{$lang_item['misc_short']}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name=\"search_quality\">
                                            <option".($search_quality == -1 ? " selected=\"selected\"" : "")." value=\"-1\">{$lang_auctionhouse['all']}</option>
                                            <option".($search_quality == 0 ? " selected=\"selected\"" : "")." value=\"0\">{$lang_item['poor']}</option>
                                            <option".($search_quality == 1 ? " selected=\"selected\"" : "")." value=\"1\">{$lang_item['common']}</option>
                                            <option".($search_quality == 2 ? " selected=\"selected\"" : "")." value=\"2\">{$lang_item['uncommon']}</option>
                                            <option".($search_quality == 3 ? " selected=\"selected\"" : "")." value=\"3\">{$lang_item['rare']}</option>
                                            <option".($search_quality == 4 ? " selected=\"selected\"" : "")." value=\"4\">{$lang_item['epic']}</option>
                                            <option".($search_quality == 5 ? " selected=\"selected\"" : "")." value=\"5\">{$lang_item['legendary']}</option>
                                            <option".($search_quality == 6 ? " selected=\"selected\"" : "")." value=\"6\">{$lang_item['artifact']}</option>
                                        </select>
                                    </td>
                                    <td>";

    makebutton($lang_global['search'], "javascript:do_submit()",80);

    $output .= "
                                    </td>
                                    <td>";

    (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? makebutton($lang_global['back'], "javascript:window.history.back()",80) : $output .= "";

    $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td width=\"25%\" align=\"right\">";

    $output .= generate_pagination("ahstats.php?order_by=$order_by".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
    $output .= "
                    </td>
                </tr>
            </table>
            <table class=\"lined\">
                <tr>
                    <th width=\"10%\"><a href=\"ahstats.php?order_by=itemowner&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='itemowner' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['seller']}</a></th>
                    <th width=\"20%\"><a href=\"ahstats.php?order_by=itemEntry&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='itemEntry' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['item']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=buyoutprice&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='buyoutprice' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['buyoutprice']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=time&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='time' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['timeleft']}</a></th>
                    <th width=\"10%\"><a href=\"ahstats.php?order_by=buyguid&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='buyguid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['buyer']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=lastbid&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='lastbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['lastbid']}</a></th>
                    <th width=\"15%\"><a href=\"ahstats.php?order_by=startbid&amp;start=$start".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=$dir\">".($order_by=='startbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_auctionhouse['firstbid']}</a></th>
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
    $output .= generate_pagination("ahstats.php?order_by=$order_by".( (($search_by && $search_value) || ($search_class != -1) || ($search_quality != -1)) ? "&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;error=2" : "" )."&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
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
