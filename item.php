<?php

require_once("header.php");
require_once("libs/item_lib.php");
valid_login($action_permission['read']);

function makeinfocell($text,$tooltip)
{
    return "<a href=\"#\" onmouseover=\"toolTip('".addslashes($tooltip)."','info_tooltip')\" onmouseout=\"toolTip()\">$text</a>";
}


function output_status_options($stat_type_offset)
{
    global $lang_item, $output;

    $stat_type = [0 =>    "", 1 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "",12 => "",
                        13 =>   "",14 => "",15 => "",16 => "",17 => "",18 => "",19 => "",20 => "",21 => "",22 => "",
                        23 =>   "",24 => "",25 => "",26 => "",27 => "",28 => "",29 => "",30 => "",31 => "",32 => "",
                        33 =>   "",34 => "",35 => "",36 => ""];

    if (!$stat_type_offset)
        $stat_type_offset = 0;

    $stat_type[$stat_type_offset] = " selected=\"selected\" ";

    $output .= "
                <option value= \"0\" {$stat_type[0]} > 0: {$lang_item['mana']}</option>
                <option value= \"1\" {$stat_type[1]} > 1: {$lang_item['health']}</option>
                <option value= \"3\" {$stat_type[3]} > 3: {$lang_item['agility']}</option>
                <option value= \"4\" {$stat_type[4]} > 4: {$lang_item['strength']}</option>
                <option value= \"5\" {$stat_type[5]} > 5: {$lang_item['intellect']}</option>
                <option value= \"6\" {$stat_type[6]} > 6: {$lang_item['spirit']}</option>
                <option value= \"7\" {$stat_type[7]} > 7: {$lang_item['stamina']}</option>
                <option value=\"12\" {$stat_type[12]}>12: {$lang_item['DEFENCE_RATING']}</option>
                <option value=\"13\" {$stat_type[13]}>13: {$lang_item['DODGE_RATING']}</option>
                <option value=\"14\" {$stat_type[14]}>14: {$lang_item['PARRY_RATING']}</option>
                <option value=\"15\" {$stat_type[15]}>15: {$lang_item['SHIELD_BLOCK_RATING']}</option>
                <option value=\"16\" {$stat_type[16]}>16: {$lang_item['MELEE_HIT_RATING']}</option>
                <option value=\"17\" {$stat_type[17]}>17: {$lang_item['RANGED_HIT_RATING']}</option>
                <option value=\"18\" {$stat_type[18]}>18: {$lang_item['SPELL_HIT_RATING']}</option>
                <option value=\"19\" {$stat_type[19]}>19: {$lang_item['MELEE_CS_RATING']}</option>
                <option value=\"20\" {$stat_type[20]}>20: {$lang_item['RANGED_CS_RATING']}</option>
                <option value=\"21\" {$stat_type[21]}>21: {$lang_item['SPELL_CS_RATING']}</option>
                <option value=\"22\" {$stat_type[22]}>22: {$lang_item['MELEE_HA_RATING']}</option>
                <option value=\"23\" {$stat_type[23]}>23: {$lang_item['RANGED_HA_RATING']}</option>
                <option value=\"24\" {$stat_type[24]}>24: {$lang_item['SPELL_HA_RATING']}</option>
                <option value=\"25\" {$stat_type[25]}>25: {$lang_item['MELEE_CA_RATING']}</option>
                <option value=\"26\" {$stat_type[26]}>26: {$lang_item['RANGED_CA_RATING']}</option>
                <option value=\"27\" {$stat_type[27]}>27: {$lang_item['SPELL_CA_RATING']}</option>
                <option value=\"28\" {$stat_type[28]}>28: {$lang_item['MELEE_HASTE_RATING']}</option>
                <option value=\"29\" {$stat_type[29]}>29: {$lang_item['RANGED_HASTE_RATING']}</option>
                <option value=\"30\" {$stat_type[30]}>30: {$lang_item['SPELL_HASTE_RATING']}</option>
                <option value=\"31\" {$stat_type[31]}>31: {$lang_item['HIT_RATING']}</option>
                <option value=\"32\" {$stat_type[32]}>32: {$lang_item['CS_RATING']}</option>
                <option value=\"33\" {$stat_type[33]}>33: {$lang_item['HA_RATING']}</option>
                <option value=\"34\" {$stat_type[34]}>34: {$lang_item['CA_RATING']}</option>
                <option value=\"35\" {$stat_type[35]}>35: {$lang_item['RESILIENCE_RATING']}</option>
                <option value=\"36\" {$stat_type[36]}>36: {$lang_item['HASTE_RATING']}</option>";

    return;
}


function output_dmgtype_options($dmg_type_offset)
{
    global $lang_item, $output;

    $dmg_type  = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => ""];

    if (!$dmg_type_offset)
        $dmg_type_offset = 0;

    $dmg_type[$dmg_type_offset] = " selected=\"selected\" ";

    $output .= "
                <option value=\"0\" {$dmg_type[0]}>0: {$lang_item['physical_dmg']}</option>
                <option value=\"1\" {$dmg_type[1]}>1: {$lang_item['holy_dmg']}</option>
                <option value=\"2\" {$dmg_type[2]}>2: {$lang_item['fire_dmg']}</option>
                <option value=\"3\" {$dmg_type[3]}>3: {$lang_item['nature_dmg']}</option>
                <option value=\"4\" {$dmg_type[4]}>4: {$lang_item['frost_dmg']}</option>
                <option value=\"5\" {$dmg_type[5]}>5: {$lang_item['shadow_dmg']}</option>
                <option value=\"6\" {$dmg_type[6]}>6: {$lang_item['arcane_dmg']}</option>";

    return;
}


//########################################################################################################################
//  PRINT  ITEM SEARCH FORM
//########################################################################################################################
function search()
{
    global $lang_global, $lang_item, $lang_item_edit, $output, $mmfpm_db, $world_db, $realm_id, $action_permission, $user_lvl;
    valid_login($action_permission['read']);

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    $result = $sqlw->query("SELECT count(*) FROM item_template");
    $tot_items = $sqlw->result($result, 0);

    $output .= "
        <center>
            <fieldset class=\"full_frame\">
                <legend>{$lang_item_edit['search_item']}</legend><br />
                <form action=\"item.php?action=do_search&amp;error=2\" method=\"post\" name=\"form\">
                    <table class=\"hidden\">
                        <tr>
                            <td align=\"right\">{$lang_item_edit['entry']}:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"entry\" /></td>
                            <td align=\"right\">{$lang_item_edit['item_name']}:</td>
                            <td align=\"right\" colspan=\"3\"><input type=\"text\" size=\"24\" maxlength=\"35\" name=\"name\" /></td>
                            <td align=\"right\">{$lang_item_edit['model_id']}:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"displayid\" /></td>
                        </tr>
                        <tr>
                            <td align=\"right\" width=\"10%\">{$lang_item_edit['class']}:</td>
                            <td align=\"right\" width=\"10%\">
                                <select name=\"class\">
                                    <option value=\"-1\">{$lang_item_edit['all']}</option>
                                    <option value=\"0\">{$lang_item['consumable']}</option>
                                    <option value=\"1\">{$lang_item['bag']}</option>
                                    <option value=\"2\">{$lang_item['weapon']}</option>
                                    <option value=\"4\">{$lang_item['armor']}</option>
                                    <option value=\"5\">{$lang_item['reagent']}</option>
                                    <option value=\"6\">{$lang_item['projectile']}</option>
                                    <option value=\"7\">{$lang_item['trade_goods']}</option>
                                    <option value=\"9\">{$lang_item['recipe']}</option>
                                    <option value=\"11\">{$lang_item['quiver']}</option>
                                    <option value=\"12\">{$lang_item['quest']}</option>
                                    <option value=\"13\">{$lang_item['key']}</option>
                                    <option value=\"14\">{$lang_item['permanent']}</option>
                                    <option value=\"15\">{$lang_item['misc_short']}</option>
                                </select>
                            </td>
                            <td align=\"right\" width=\"10%\">{$lang_item_edit['quality']}:</td>
                            <td align=\"right\" width=\"10%\">
                                <select name=\"Quality\">
                                    <option value=\"-1\">{$lang_item_edit['all']}</option>
                                    <option value=\"0\">{$lang_item['poor']}</option>
                                    <option value=\"1\">{$lang_item['common']}</option>
                                    <option value=\"2\">{$lang_item['uncommon']}</option>
                                    <option value=\"3\">{$lang_item['rare']}</option>
                                    <option value=\"4\">{$lang_item['epic']}</option>
                                    <option value=\"5\">{$lang_item['legendary']}</option>
                                    <option value=\"6\">{$lang_item['artifact']}</option>
                                </select>
                            </td>
                            <td align=\"right\" width=\"10%\">{$lang_item_edit['inv_type']}:</td>
                            <td align=\"right\" width=\"10%\">
                                <select name=\"InventoryType\">
                                    <option value=\"-1\">{$lang_item_edit['all']}</option>
                                    <option value=\"1\">{$lang_item['head']}</option>
                                    <option value=\"2\">{$lang_item['neck']}</option>
                                    <option value=\"3\">{$lang_item['shoulder']}</option>
                                    <option value=\"4\">{$lang_item['shirt']}</option>
                                    <option value=\"5\">{$lang_item['chest']}</option>
                                    <option value=\"6\">{$lang_item['belt']}</option>
                                    <option value=\"7\">{$lang_item['legs']}</option>
                                    <option value=\"8\">{$lang_item['feet']}</option>
                                    <option value=\"9\">{$lang_item['belt']}</option>
                                    <option value=\"10\">{$lang_item['gloves']}</option>
                                    <option value=\"11\">{$lang_item['finger']}</option>
                                    <option value=\"12\">{$lang_item['trinket']}</option>
                                    <option value=\"13\">{$lang_item['one_hand']}</option>
                                    <option value=\"14\">{$lang_item['off_hand']}</option>
                                    <option value=\"15\">{$lang_item['bow']}</option>
                                    <option value=\"16\">{$lang_item['back']}</option>
                                    <option value=\"17\">{$lang_item['two_hand']}</option>
                                    <option value=\"18\">{$lang_item['bag']}</option>
                                    <option value=\"19\">{$lang_item['tabard']}</option>
                                    <option value=\"20\">{$lang_item['robe']}</option>
                                    <option value=\"21\">{$lang_item['main_hand']}</option>
                                    <option value=\"22\">{$lang_item['off_misc']}</option>
                                    <option value=\"23\">{$lang_item['tome']}</option>
                                    <option value=\"24\">{$lang_item['projectile']}</option>
                                    <option value=\"25\">{$lang_item['thrown']}</option>
                                    <option value=\"26\">{$lang_item['rifle']}</option>
                                </select>
                            </td>
                            <td align=\"right\" width=\"10%\">{$lang_item_edit['req_level']}:</td>
                            <td align=\"right\" width=\"10%\"><input type=\"text\" size=\"6\" maxlength=\"3\" name=\"RequiredLevel\" /></td>
                        </tr>
                        <tr>
                            <td align=\"right\">{$lang_item_edit['spell_id']} 1:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"spellid_1\" /></td>
                            <td align=\"right\">{$lang_item_edit['spell_id']} 2:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"spellid_2\" /></td>
                            <td align=\"right\">{$lang_item_edit['spell_id']} 3:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"spellid_3\" /></td>
                            <td align=\"right\">{$lang_item_edit['spell_id']} 4:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"spellid_4\" /></td>
                        </tr>
                        <tr>
                            <td align=\"right\">{$lang_item_edit['item_level']}:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"ItemLevel\" /></td>
                            <td align=\"right\">{$lang_item_edit['item_set']}:</td>
                            <td align=\"right\" colspan=\"3\">
                                <select name=\"itemset\">
                                    <option value=\"\">{$lang_item_edit['all']}</option>";

    $itemset_id = $sqlm->query("SELECT `id`, `field_1` FROM `dbc_itemset`");
    while($set = $sqlm->fetch_row($itemset_id))
        $output .= "
                                    <option value=\"{$set[0]}\">($set[0]) {$set[1]}</option>";
    $output .= "
                                </select>
                            </td>
                            <td align=\"right\">{$lang_item_edit['flags']}:</td>
                            <td align=\"right\"><input type=\"text\" size=\"6\" maxlength=\"6\" name=\"Flags\" /></td>
                        </tr>
                        <tr>
                            <td align=\"right\">{$lang_item_edit['bonding']}:</td>
                            <td align=\"right\" colspan=\"2\">
                                <select name=\"bonding\">
                                    <option value=\"-1\">{$lang_item_edit['all']}</option>
                                    <option value=\"1\">{$lang_item['bop']}</option>
                                    <option value=\"2\">{$lang_item['boe']}</option>
                                    <option value=\"3\">{$lang_item['bou']}</option>
                                    <option value=\"4\">{$lang_item['quest_item']}</option>
                                </select>
                            </td>
                            <td align=\"right\">{$lang_item_edit['custom_search']}:</td>
                            <td align=\"right\" colspan=\"2\"><input type=\"text\" size=\"24\" maxlength=\"512\" name=\"custom_search\" /></td>
                            <td align=\"right\" colspan=\"2\">
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"8\">-----------------------------------------------------------------------------------------------------------------------------------------------</td>
                        </tr>
                        <tr>
                            <td align=\"right\"></td>
                            <td align=\"right\" colspan=\"4\">";

    if($user_lvl >= $action_permission['insert'])
        makebutton($lang_item_edit['add_new_item'], "item.php?action=add_new&error=3",130);

    makebutton($lang_item_edit['search'], "javascript:do_submit()",130);

    $output .= "
                            </td>
                            <td align=\"right\" colspan=\"2\">{$lang_item_edit['tot_items_in_db']}: $tot_items</td>
                            <td align=\"right\"></td>
                        </tr>
                    </table>
                </form>
            </fieldset>
            <br /><br />
        </center>";
}


//########################################################################################################################
// SHOW SEARCH RESULTS
//########################################################################################################################
function do_search()
{
    global $lang_global, $lang_item, $lang_item_edit, $output, $world_db, $realm_id, $item_datasite, $sql_search_limit, $itemperpage,
        $action_permission, $user_lvl;

    valid_login($action_permission['read']);
    wowhead_tt();

    $deplang = get_lang_id();
    if (($_POST['class'] == "-1")&&($_POST['Quality'] == "-1")&&($_POST['InventoryType'] == "-1")&&($_POST['bonding'] == "-1")
        &&(!isset($_POST['entry'])||$_POST['entry'] === '')&&(!isset($_POST['name'])||$_POST['name'] === '')&&(!isset($_POST['displayid'])||$_POST['displayid'] === '')&&(!isset($_POST['RequiredLevel'])||$_POST['RequiredLevel'] === '')
        &&(!isset($_POST['spellid_1'])||$_POST['spellid_1'] === '')&&(!isset($_POST['spellid_2'])||$_POST['spellid_2'] === '')&&(!isset($_POST['spellid_3'])||$_POST['spellid_3'] === '')&&(!isset($_POST['spellid_4'])||$_POST['spellid_4'] === '')
        &&(!isset($_POST['ItemLevel'])||$_POST['ItemLevel'] === '')&&(!isset($_POST['itemset'])||$_POST['itemset'] === '')&&(!isset($_POST['Flags'])||$_POST['Flags'] === '')
        &&(!isset($_POST['custom_search'])||$_POST['custom_search'] === ''))
        redirect("item.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $class = $sqlw->quote_smart($_POST['class']);
    $Quality = $sqlw->quote_smart($_POST['Quality']);
    $InventoryType = $sqlw->quote_smart($_POST['InventoryType']);
    $bonding = $sqlw->quote_smart($_POST['bonding']);

    if ($_POST['entry'] != '') $entry = $sqlw->quote_smart($_POST['entry']);
    if ($_POST['name'] != '') $name = $sqlw->quote_smart($_POST['name']);
    if ($_POST['displayid'] != '') $displayid = $sqlw->quote_smart($_POST['displayid']);
    if ($_POST['RequiredLevel'] != '') $RequiredLevel = $sqlw->quote_smart($_POST['RequiredLevel']);
    if ($_POST['spellid_1'] != '') $spellid_1 = $sqlw->quote_smart($_POST['spellid_1']);
    if ($_POST['spellid_2'] != '') $spellid_2 = $sqlw->quote_smart($_POST['spellid_2']);
    if ($_POST['spellid_3'] != '') $spellid_3 = $sqlw->quote_smart($_POST['spellid_3']);
    if ($_POST['spellid_4'] != '') $spellid_4 = $sqlw->quote_smart($_POST['spellid_4']);
    if ($_POST['ItemLevel'] != '') $ItemLevel = $sqlw->quote_smart($_POST['ItemLevel']);
    if ($_POST['itemset'] != '') $itemset = $sqlw->quote_smart($_POST['itemset']);
    if ($_POST['Flags'] != '') $Flags = $sqlw->quote_smart($_POST['Flags']);
    if ($_POST['custom_search'] != '') $custom_search = $sqlw->quote_smart($_POST['custom_search']);
    else $custom_search = "";

    $where = "WHERE item_template.entry > 0 ";
    if($custom_search)         $where .= "AND $custom_search ";
    if($class != "-1")         $where .= "AND class = '$class' ";
    if($Quality != "-1")       $where .= "AND Quality = '$Quality' ";
    if($InventoryType != "-1") $where .= "AND InventoryType = '$InventoryType' ";
    if($bonding != "-1")       $where .= "AND bonding = '$bonding' ";

    if(isset($entry))         $where .= "AND item_template.entry = '$entry' ";
    if(isset($name))          $where .= "AND IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) LIKE '%$name%' ";
    if(isset($displayid))     $where .= "AND displayid = '$displayid' ";
    if(isset($RequiredLevel)) $where .= "AND RequiredLevel = '$RequiredLevel' ";

    if(isset($spellid_1)) $where .= "AND (spellid_1 = '$spellid_1' OR spellid_2 = '$spellid_1' OR spellid_3 = '$spellid_1' OR spellid_4 = '$spellid_1' OR spellid_5 = '$spellid_1') ";
    if(isset($spellid_2)) $where .= "AND (spellid_1 = '$spellid_2' OR spellid_2 = '$spellid_2' OR spellid_3 = '$spellid_2' OR spellid_4 = '$spellid_2' OR spellid_5 = '$spellid_2') ";
    if(isset($spellid_3)) $where .= "AND (spellid_1 = '$spellid_3' OR spellid_2 = '$spellid_3' OR spellid_3 = '$spellid_3' OR spellid_4 = '$spellid_3' OR spellid_5 = '$spellid_3') ";
    if(isset($spellid_4)) $where .= "AND (spellid_1 = '$spellid_4' OR spellid_2 = '$spellid_4' OR spellid_3 = '$spellid_4' OR spellid_4 = '$spellid_4' OR spellid_5 = '$spellid_4') ";

    if(isset($ItemLevel)) $where .= "AND ItemLevel = '$ItemLevel' ";
    if(isset($itemset))   $where .= "AND itemset = '$itemset' ";
    if(isset($Flags))     $where .= "AND Flags = '$Flags' ";

    if($where == "WHERE item_template.entry > 0 ") redirect("item.php?error=1");

    $result = $sqlw->query("SELECT item_template.entry,displayid,IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name,RequiredLevel,ItemLevel FROM item_template LEFT JOIN locales_item ON item_template.entry = locales_item.entry $where ORDER BY item_template.entry LIMIT $sql_search_limit");
    $total_items_found = $sqlw->num_rows($result);

    $output .= "
                <center>
                    <table class=\"top_hidden\">
                        <tr>
                            <td>";

    makebutton($lang_item_edit['new_search'], "item.php",130);

    $output .= "
                            </td>
                            <td align=\"right\">{$lang_item_edit['items_found']} : $total_items_found : {$lang_global['limit']} $sql_search_limit</td>
                        </tr>
                    </table>
                    <table class=\"lined\">
                        <tr>
                            <th width=\"10%\">{$lang_item_edit['entry']}</th>
                            <th width=\"10%\">{$lang_item_edit['display_id']}</th>
                            <th width=\"10%\">{$lang_item_edit['item_name']}</th>
                            <th width=\"10%\">{$lang_item_edit['req_level']}</th>
                            <th width=\"10%\">{$lang_item_edit['item_level']}</th>
                        </tr>";

    for ($i=0; $i<$total_items_found; $i++)
    {
        $item = $sqlw->fetch_row($result);

        //$tooltip = get_item_tooltip($item[0]);
        $output .= "
                        <tr>
                            <td><a href=\"$item_datasite$item[0]\" target=\"_blank\">$item[0]</a></td>
                            <td>
                                <a style=\"padding:2px;\" href=\"$item_datasite$item[0]\" target=\"_blank\">
                                    <img src=\"".get_item_icon($item[0])."\" class=\"".get_item_border($item[0], $sqlw)."\" alt=\"\" />
                                </a>
                            </td>
                            <td>
                                <a href=\"item.php?action=edit&amp;entry=$item[0]&amp;error=4\">".htmlentities($item[2])."</a>
                            </td>
                            <td>$item[3]</td>
                            <td>$item[4]</td>
                        </tr>";
    }
    $output .= "
                    </table>
                    <br />
                </center>";
}


//########################################################################################################################
// ADD NEW ITEM
//########################################################################################################################
function add_new() {
    global $lang_global, $lang_item, $lang_id_tab, $lang_item_edit, $output, $item_datasite, $action_permission, $user_lvl;

    valid_login($action_permission['insert']);
    wowhead_tt();

    $output .= "
                <script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
                <center>
                    <br /><br /><br />
                    <form method=\"post\" action=\"item.php?action=do_update\" name=\"form1\">
                        <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
                        <input type=\"hidden\" name=\"type\" value=\"add_new\"/>
                        <div class=\"jtab-container\" id=\"container\">
                            <ul class=\"jtabs\">
                                <li><a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">{$lang_item_edit['general_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane2', this)\">{$lang_item_edit['additional_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane3', this)\">{$lang_item_edit['stats_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane4', this)\">{$lang_item_edit['damage_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane5', this)\">{$lang_item_edit['spell_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane7', this)\">{$lang_item_edit['sock_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane6', this)\">{$lang_item_edit['req_tab']}</a></li>
                            </ul>
                            <div class=\"jtab-panes\">";

    $output .= "
                                <div id=\"pane1\">
                                <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['general']}:</td></tr>
                                        <tr>
                                            <td>".makeinfocell($lang_item_edit['entry'],$lang_item_edit['entry_desc'])."</td>
                                            <td><input type=\"text\" name=\"entry\" size=\"8\" maxlength=\"11\" value=\"\" /></td>

                                            <td>".makeinfocell($lang_item_edit['display_id'],$lang_item_edit['display_id_desc'])."</td>
                                            <td><input type=\"text\" name=\"displayid\" size=\"8\" maxlength=\"11\" value=\"0\" /></td>

                                            <td>".makeinfocell($lang_item_edit['req_level'],$lang_item_edit['req_level_desc'])."</td>
                                            <td><input type=\"text\" name=\"RequiredLevel\" size=\"8\" maxlength=\"4\" value=\"0\" /></td>

                                            <td>".makeinfocell($lang_item_edit['item_level'],$lang_item_edit['item_level_desc'])."</td>
                                            <td><input type=\"text\" name=\"ItemLevel\" size=\"8\" maxlength=\"4\" value=\"1\" /></td>
                                        </tr>

                                        <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['names']}:</td></tr>
                                        <tr>
                                            <td>".makeinfocell($lang_item_edit['item_name'],$lang_item_edit['item_name_desc'])."</td>
                                            <td colspan=\"3\"><input type=\"text\" name=\"name\" size=\"30\" maxlength=\"225\" value=\"item_name\" /></td>

                                            <td>".makeinfocell($lang_item_edit['script_name'],$lang_item_edit['script_name_desc'])."</td>
                                            <td colspan=\"3\"><input type=\"text\" name=\"ScriptName\" size=\"30\" maxlength=\"100\" value=\"internalitemhandler\" /></td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_item_edit['description'],$lang_item_edit['description_desc'])."</td>
                                            <td colspan=\"3\"><input type=\"text\" name=\"description\" size=\"30\" maxlength=\"225\" value=\"\" /></td>
                                            <td colspan=\"4\"></td>
                                        </tr>

                                        <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['type']}:</td></tr>
                                        <tr>
                                            <td>".makeinfocell($lang_item_edit['class'],$lang_item_edit['class_desc'])."</td>
                                            <td colspan=\"3\">
                                                <select name=\"class\">
                                                    <option value=\"0\">0 - {$lang_item['consumable']}</option>
                                                    <option value=\"1\">1 - {$lang_item['bag']}</option>
                                                    <option value=\"2\">2 - {$lang_item['weapon']}</option>
                                                    <option value=\"4\">4 - {$lang_item['armor']}</option>
                                                    <option value=\"5\">5 - {$lang_item['reagent']}</option>
                                                    <option value=\"6\">6 - {$lang_item['projectile']}</option>
                                                    <option value=\"7\">7 - {$lang_item['trade_goods']}</option>
                                                    <option value=\"9\">9 - {$lang_item['recipe']}</option>
                                                    <option value=\"11\">11 - {$lang_item['quiver']}</option>
                                                    <option value=\"12\">12 - {$lang_item['quest']}</option>
                                                    <option value=\"13\">13 - {$lang_item['key']}</option>
                                                    <option value=\"14\">14 - {$lang_item['permanent']}</option>
                                                    <option value=\"15\">15 - {$lang_item['misc_short']}</option>
                                                </select>
                                            </td>

                                            <td>".makeinfocell($lang_item_edit['subclass'],$lang_item_edit['subclass_desc'])."</td>
                                            <td colspan=\"3\">
                                                <select name=\"subclass\">
                                                    <option value=\"0\">0 - {$lang_item['none']}</option>
                                                    <optgroup label=\"Class 0: {$lang_item['consumable']}\">
                                                        <option value=\"0\">0 - {$lang_item['consumable']}</option>
                                                        <option value=\"3\">3 - {$lang_item['potion']}</option>
                                                        <option value=\"4\">4 - {$lang_item['scroll']}</option>
                                                        <option value=\"5\">5 - {$lang_item['bandage']}</option>
                                                        <option value=\"6\">6 - {$lang_item['healthstone']}</option>
                                                        <option value=\"7\">7 - {$lang_item['combat_effect']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 1: {$lang_item['bag']}\">
                                                        <option value=\"0\">0 - {$lang_item['bag']}</option>
                                                        <option value=\"1\">1 - {$lang_item['soul_shards']}</option>
                                                        <option value=\"2\">2 - {$lang_item['herbs']}</option>
                                                        <option value=\"3\">3 - {$lang_item['enchanting']}</option>
                                                        <option value=\"4\">4 - {$lang_item['engineering']}</option>
                                                        <option value=\"5\">5 - {$lang_item['gems']}</option>
                                                        <option value=\"6\">6 - {$lang_item['mining']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 2: {$lang_item['weapon']}\">
                                                        <option value=\"0\">0 - {$lang_item['axe_1h']}</option>
                                                        <option value=\"1\">1 - {$lang_item['axe_2h']}</option>
                                                        <option value=\"2\">2 - {$lang_item['bow']}</option>
                                                        <option value=\"3\">3 - {$lang_item['rifle']}</option>
                                                        <option value=\"4\">4 - {$lang_item['mace_1h']}</option>
                                                        <option value=\"5\">5 - {$lang_item['mace_2h']}</option>
                                                        <option value=\"6\">6 - {$lang_item['polearm']}</option>
                                                        <option value=\"7\">7 - {$lang_item['sword_1h']}</option>
                                                        <option value=\"8\">8 - {$lang_item['sword_2h']}</option>
                                                        <option value=\"10\">10 - {$lang_item['staff']}</option>
                                                        <option value=\"11\">11 - {$lang_item['exotic_1h']}</option>
                                                        <option value=\"12\">12 - {$lang_item['exotic_2h']}</option>
                                                        <option value=\"13\">13 - {$lang_item['fist_weapon']}</option>
                                                        <option value=\"14\">14 - {$lang_item['misc_weapon']}</option>
                                                        <option value=\"15\">15 - {$lang_item['dagger']}</option>
                                                        <option value=\"16\">16 - {$lang_item['thrown']}</option>
                                                        <option value=\"17\">17 - {$lang_item['spear']}</option>
                                                        <option value=\"18\">18 - {$lang_item['crossbow']}</option>
                                                        <option value=\"19\">19 - {$lang_item['wand']}</option>
                                                        <option value=\"20\">20 - {$lang_item['fishing_pole']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 4: {$lang_item['armor']}\">
                                                        <option value=\"0\">0 - {$lang_item['misc']}</option>
                                                        <option value=\"1\">1 - {$lang_item['cloth']}</option>
                                                        <option value=\"2\">2 - {$lang_item['leather']}</option>
                                                        <option value=\"3\">3 - {$lang_item['mail']}</option>
                                                        <option value=\"4\">4 - {$lang_item['plate']}</option>
                                                        <option value=\"5\">5 - {$lang_item['buckler']}</option>
                                                        <option value=\"6\">6 - {$lang_item['shield']}</option>
                                                        <option value=\"7\">7 - {$lang_item['libram']}</option>
                                                        <option value=\"8\">8 - {$lang_item['idol']}</option>
                                                        <option value=\"9\">9 - {$lang_item['totem']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 6: {$lang_item['projectile']}\">
                                                        <option value=\"2\">2 - {$lang_item['arrows']}</option>
                                                        <option value=\"3\">3 - {$lang_item['bullets']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 7: {$lang_item['trade_goods']}\">
                                                        <option value=\"0\">0 - {$lang_item['trade_goods']}</option>
                                                        <option value=\"1\">1 - {$lang_item['parts']}</option>
                                                        <option value=\"2\">2 - {$lang_item['explosives']}</option>
                                                        <option value=\"3\">3 - {$lang_item['devices']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 9: {$lang_item['recipe']}\">
                                                        <option value=\"0\">0 - {$lang_item['book']}</option>
                                                        <option value=\"1\">1 - {$lang_item['LW_pattern']}</option>
                                                        <option value=\"2\">2 - {$lang_item['tailoring_pattern']}</option>
                                                        <option value=\"3\">3 - {$lang_item['ENG_Schematic']}</option>
                                                        <option value=\"4\">4 - {$lang_item['BS_plans']}</option>
                                                        <option value=\"5\">5 - {$lang_item['cooking_recipe']}</option>
                                                        <option value=\"6\">6 - {$lang_item['alchemy_recipe']}</option>
                                                        <option value=\"7\">7 - {$lang_item['FA_manual']}</option>
                                                        <option value=\"8\">8 - {$lang_item['ench_formula']}</option>
                                                        <option value=\"9\">9 - {$lang_item['fishing_manual']}</option>
                                                        <option value=\"10\">10 - {$lang_item['JC_formula']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 11: {$lang_item['quiver']}\">
                                                        <option value=\"2\">2 - {$lang_item['quiver']}</option>
                                                        <option value=\"3\">3 - {$lang_item['ammo_pouch']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"Class 13: {$lang_item['key']}\">
                                                        <option value=\"0\">0 - {$lang_item['key']}</option>
                                                        <option value=\"1\">1 - {$lang_item['lockpick']}</option>
                                                    </optgroup>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_item_edit['quality'],$lang_item_edit['quality_desc'])."</td>
                                            <td colspan=\"2\">
                                                <select name=\"Quality\">
                                                    <option value=\"0\">0 - {$lang_item['poor']}</option>
                                                    <option value=\"1\">1 - {$lang_item['common']}</option>
                                                    <option value=\"2\">2 - {$lang_item['uncommon']}</option>
                                                    <option value=\"3\">3 - {$lang_item['rare']}</option>
                                                    <option value=\"4\">4 - {$lang_item['epic']}</option>
                                                    <option value=\"5\">5 - {$lang_item['legendary']}</option>
                                                    <option value=\"6\">6 - {$lang_item['artifact']}</option>
                                                </select>
                                            </td>
                                            <td>".makeinfocell($lang_item_edit['inv_type'],$lang_item_edit['inv_type_desc'])."</td>
                                            <td colspan=\"2\">
                                                <select name=\"InventoryType\">
                                                    <option value=\"0\">0 - {$lang_item['other']}</option>
                                                    <option value=\"1\">1 - {$lang_item['head']}</option>
                                                    <option value=\"2\">2 - {$lang_item['neck']}</option>
                                                    <option value=\"3\">3 - {$lang_item['shoulder']}</option>
                                                    <option value=\"4\">4 - {$lang_item['shirt']}</option>
                                                    <option value=\"5\">5 - {$lang_item['chest']}</option>
                                                    <option value=\"6\">6 - {$lang_item['belt']}</option>
                                                    <option value=\"7\">7 - {$lang_item['legs']}</option>
                                                    <option value=\"8\">8 - {$lang_item['feet']}</option>
                                                    <option value=\"9\">9 - {$lang_item['belt']}</option>
                                                    <option value=\"10\">10 - {$lang_item['gloves']}</option>
                                                    <option value=\"11\">11 - {$lang_item['finger']}</option>
                                                    <option value=\"12\">12 - {$lang_item['trinket']}</option>
                                                    <option value=\"13\">13 - {$lang_item['one_hand']}</option>
                                                    <option value=\"14\">14 - {$lang_item['off_hand']}</option>
                                                    <option value=\"15\">15 - {$lang_item['bow']}</option>
                                                    <option value=\"16\">16 - {$lang_item['back']}</option>
                                                    <option value=\"17\">17 - {$lang_item['two_hand']}</option>
                                                    <option value=\"18\">18 - {$lang_item['bag']}</option>
                                                    <option value=\"19\">19 - {$lang_item['tabard']}</option>
                                                    <option value=\"20\">20 - {$lang_item['robe']}</option>
                                                    <option value=\"21\">21 - {$lang_item['main_hand']}</option>
                                                    <option value=\"22\">22 - {$lang_item['off_misc']}</option>
                                                    <option value=\"23\">23 - {$lang_item['tome']}</option>
                                                    <option value=\"24\">24 - {$lang_item['projectile']}</option>
                                                    <option value=\"25\">25 - {$lang_item['thrown']}</option>
                                                    <option value=\"26\">26 - {$lang_item['rifle']}</option>
                                                </select>
                                            </td>
                                            <td>".makeinfocell($lang_item_edit['flags'],$lang_item_edit['flags_desc'])."</td>
                                            <td><input type=\"text\" name=\"Flags\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_item_edit['item_set'],$lang_item_edit['item_set_desc'])."</td>
                                            <td><input type=\"text\" name=\"itemset\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                            <td>".makeinfocell($lang_item_edit['bonding'],$lang_item_edit['bonding_desc'])."</td>
                                            <td colspan=\"3\">
                                                <select name=\"bonding\">
                                                    <option value=\"0\">0 - {$lang_item['no_bind']}</option>
                                                    <option value=\"1\">1 - {$lang_item['bop']}</option>
                                                    <option value=\"2\">2 - {$lang_item['boe']}</option>
                                                    <option value=\"3\">3 - {$lang_item['bou']}</option>
                                                    <option value=\"4\">4 - {$lang_item['quest_item']}</option>
                                                    <option value=\"5\">5 - {$lang_item['quest_item']}1</option>
                                                </select>
                                            </td>
                                            <td>".makeinfocell($lang_item_edit['start_quest'],$lang_item_edit['start_quest_desc'])."</td>
                                            <td><input type=\"text\" name=\"startquest\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                        </tr>
                                    </table>
                                    <br />{$lang_item_edit['short_rules_desc']}<br /><br />
                                </div>";

    $output .= "
                                <div id=\"pane2\">
                                <br /><br />
                                <table class=\"lined\" style=\"width: 720px;\">
                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['vendor']}:</td></tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['buy_count'],$lang_item_edit['buy_count_desc'])."</td>
                                        <td><input type=\"text\" name=\"BuyCount\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>

                                        <td>".makeinfocell($lang_item_edit['buy_price'],$lang_item_edit['buy_price_desc'])."</td>
                                        <td><input type=\"text\" name=\"BuyPrice\" size=\"8\" maxlength=\"30\" value=\"0\" /></td>

                                        <td>".makeinfocell($lang_item_edit['sell_price'],$lang_item_edit['sell_price_desc'])."</td>
                                        <td><input type=\"text\" name=\"SellPrice\" size=\"8\" maxlength=\"30\" value=\"0\" /></td>
                                        <td></td><td></td>
                                    </tr>

                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['container']}:</td></tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['max_count'],$lang_item_edit['max_count_desc'])."</td>
                                        <td><input type=\"text\" name=\"maxcount\" size=\"8\" maxlength=\"5\" value=\"0\" /></td>

                                        <td>".makeinfocell($lang_item_edit['stackable'],$lang_item_edit['stackable_desc'])."</td>
                                        <td><input type=\"text\" name=\"stackable\" size=\"8\" maxlength=\"5\" value=\"1\" /></td>

                                        <td>".makeinfocell($lang_item_edit['bag_family'],$lang_item_edit['bag_family_desc'])."</td>
                                        <td>
                                            <select name=\"BagFamily\">
                                                <option value=\"0\">0 - {$lang_item['none']}</option>
                                                <option value=\"1\">1 - {$lang_item['arrows']}</option>
                                                <option value=\"2\">2 - {$lang_item['bullets']}</option>
                                                <option value=\"3\">3 - {$lang_item['soul_shards']}</option>
                                                <option value=\"6\">6 - {$lang_item['herbs']}</option>
                                                <option value=\"7\">7 - {$lang_item['enchanting']}</option>
                                                <option value=\"8\">8 - {$lang_item['engineering']}</option>
                                                <option value=\"9\">9 - {$lang_item['keys']}</option>
                                                <option value=\"10\">10 - {$lang_item['gems']}</option>
                                                <option value=\"12\">12 - {$lang_item['mining']}</option>
                                            </select>
                                        </td>
                                        <td>".makeinfocell($lang_item_edit['bag_slots'],$lang_item_edit['bag_slots_desc'])."</td>
                                        <td><input type=\"text\" name=\"ContainerSlots\" size=\"6\" maxlength=\"3\" value=\"0\" /></td>
                                    </tr>
                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['materials']}:</td></tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['material'],$lang_item_edit['material_desc'])."</td>
                                        <td colspan=\"2\">
                                            <select name=\"Material\">
                                                <option value=\"-1\">-1 - {$lang_item_edit['consumables']}</option>
                                                <option value=\"-1\">0 - {$lang_item_edit['none']}</option>
                                                <option value=\"1\">1 - {$lang_item_edit['metal']}</option>
                                                <option value=\"2\">2 - {$lang_item_edit['wood']}</option>
                                                <option value=\"3\">3 - {$lang_item_edit['liquid']}</option>
                                                <option value=\"4\">4 - {$lang_item_edit['jewelry']}</option>
                                                <option value=\"5\">5 - {$lang_item_edit['chain']}</option>
                                                <option value=\"6\">6 - {$lang_item_edit['plate']}</option>
                                                <option value=\"7\">7 - {$lang_item_edit['cloth']}</option>
                                                <option value=\"8\">8 - {$lang_item_edit['leather']}</option>
                                            </select>
                                        </td>
                                        <td>".makeinfocell($lang_item_edit['page_material'],$lang_item_edit['page_material_desc'])."</td>
                                        <td colspan=\"2\">
                                            <select name=\"PageMaterial\">
                                                <option value=\"0\">0 - {$lang_item_edit['none']}</option>
                                                <option value=\"1\">1 - {$lang_item_edit['parchment']}</option>
                                                <option value=\"2\">2 - {$lang_item_edit['stone']}</option>
                                                <option value=\"3\">3 - {$lang_item_edit['marble']}</option>
                                                <option value=\"4\">4 - {$lang_item_edit['silver']}</option>
                                                <option value=\"5\">5 - {$lang_item_edit['bronze']}</option>
                                            </select>
                                        </td>
                                        <td>".makeinfocell($lang_item_edit['max_durability'],$lang_item_edit['max_durability_desc'])."</td>
                                        <td><input type=\"text\" name=\"MaxDurability\" size=\"8\" maxlength=\"30\" value=\"10\" /></td>
                                    </tr>
                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['RandomProperty']}:</td></tr>
                                    <tr>
                                        <td colspan=\"2\">".makeinfocell($lang_item_edit['RandomProperty'],$lang_item_edit['RandomProperty_desc'])."</td>
                                        <td colspan=\"2\"><input type=\"text\" name=\"RandomProperty\" size=\"8\" maxlength=\"30\" value=\"0\" /></td>
                                        <td colspan=\"2\">".makeinfocell($lang_item_edit['RandomSuffix'],$lang_item_edit['RandomSuffix_desc'])."</td>
                                        <td colspan=\"2\"><input type=\"text\" name=\"RandomSuffix\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
                                    </tr>
                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['other']}:</td></tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['area'],$lang_item_edit['area_desc'])."</td>
                                        <td><input type=\"text\" name=\"area\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
                                        <td>".makeinfocell($lang_item_edit['map'],$lang_item_edit['map_desc'])."</td>
                                        <td><input type=\"text\" name=\"Map\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
                                        <td>".makeinfocell($lang_item_edit['page_text'],$lang_item_edit['page_text_desc'])."</td>
                                        <td><input type=\"text\" name=\"PageText\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                                        <td>".makeinfocell($lang_item_edit['unk0'],$lang_item_edit['unk0_desc'])."</td>
                                        <td><input type=\"text\" name=\"unk0\" size=\"8\" maxlength=\"10\" value=\"-1\" /></td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"2\">".makeinfocell($lang_item_edit['disenchant_id'],$lang_item_edit['disenchant_id_desc'])."</td>
                                        <td><input type=\"text\" name=\"DisenchantID\" size=\"10\" maxlength=\"10\" value=\"0\" /></td>
                                        <td colspan=\"2\">".makeinfocell($lang_item_edit['req_skill_disenchant'],$lang_item_edit['req_skill_disenchant_desc'])."</td>
                                        <td><input type=\"text\" name=\"RequiredDisenchantSkill\" size=\"10\" maxlength=\"10\" value=\"-1\" /></td>
                                        <td>".makeinfocell($lang_item_edit['lock_id'],$lang_item_edit['lock_id_desc'])."</td>
                                        <td><input type=\"text\" name=\"lockid\" size=\"8\" maxlength=\"30\" value=\"0\" /></td>
                                    </tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['lang_id'],$lang_item_edit['lang_id_desc'])."</td>
                                        <td colspan=\"2\">
                                            <select name=\"LanguageID\">
                                                <option value=\"0\">0 - {$lang_item_edit['other']}</option>
                                                <option value=\"1\">1 - Orcish</option>
                                                <option value=\"2\">2 - Darnassian</option>
                                                <option value=\"3\">3 - Taurahe</option>
                                                <option value=\"6\">6 - Dwarvish</option>
                                                <option value=\"7\">7 - Common</option>
                                                <option value=\"8\">8 - Demonic</option>
                                                <option value=\"9\">9 - Titan</option>
                                                <option value=\"10\">10 - Thelassian</option>
                                                <option value=\"11\">11 - Draconic</option>
                                                <option value=\"12\">12 - Kalimag</option>
                                                <option value=\"13\">13 - Gnomish</option>
                                                <option value=\"14\">14 - Troll</option>
                                                <option value=\"33\">33 - Gutterspeak</option>
                                            </select>
                                        </td>
                                        <td>".makeinfocell($lang_item_edit['sheath'],$lang_item_edit['sheath_desc'])."</td>
                                        <td colspan=\"2\">
                                            <select name=\"sheath\">
                                                <option value=\"0\">0 - {$lang_item_edit['other']}</option>
                                                <option value=\"1\">1 - {$lang_item['sword_2h']}</option>
                                                <option value=\"2\">2 - {$lang_item['staff']}</option>
                                                <option value=\"3\">3 - {$lang_item['sword_1h']}</option>
                                                <option value=\"4\">4 - {$lang_item['shield']}</option>
                                                <option value=\"5\">5 - {$lang_item['rod']}</option>
                                                <option value=\"7\">7 - {$lang_item['off_hand']}</option>
                                            </select>
                                        </td>
                                        <td>".makeinfocell($lang_item_edit['totem_category'],$lang_item_edit['totem_category_desc'])."</td>
                                        <td><input type=\"text\" name=\"TotemCategory\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
                                    </tr>
                                </table>
                                <br /><br />
                            </div>";
    $output .= "
                            <div id=\"pane3\">
                            <br /><br />
                            <table class=\"lined\" style=\"width: 720px;\">
                                <tr class=\"large_bold\">
                                    <td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['stats']}:</td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 1",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type1\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value1\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 2",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type2\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value2\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 3",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type3\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value3\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 4",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type4\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value4\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 5",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type5\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value5\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 6",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type6\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value6\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 7",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type7\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value7\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 8",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type8\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value8\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 9",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type9\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value9\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['stat_type']." 10",$lang_item_edit['stat_type_desc'])."</td>
                                    <td>
                                        <select name=\"stat_type10\">";
    output_status_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td><input type=\"text\" name=\"stat_value10\" size=\"10\" maxlength=\"6\" value=\"0\" /></td>
                                </tr>
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['resis_armor']}:</td></tr>
                                <tr>
                                    <td colspan=\"2\">".makeinfocell($lang_item['armor'],$lang_item_edit['armor_desc'])."</td>
                                    <td><input type=\"text\" name=\"armor\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>

                                    <td colspan=\"2\">".makeinfocell($lang_item['block'],$lang_item_edit['block_desc'])."</td>
                                    <td><input type=\"text\" name=\"block\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"2\">".makeinfocell($lang_item['res_holy'],$lang_item_edit['res_holy_desc'])."</td>
                                    <td><input type=\"text\" name=\"holy_res\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>

                                    <td colspan=\"2\">".makeinfocell($lang_item['res_fire'],$lang_item_edit['res_fire_desc'])."</td>
                                    <td><input type=\"text\" name=\"fire_res\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"2\">".makeinfocell($lang_item['res_nature'],$lang_item_edit['res_nature_desc'])."</td>
                                    <td><input type=\"text\" name=\"nature_res\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>

                                    <td colspan=\"2\">".makeinfocell($lang_item['res_frost'],$lang_item_edit['res_frost_desc'])."</td>
                                    <td><input type=\"text\" name=\"frost_res\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"2\">".makeinfocell($lang_item['res_shadow'],$lang_item_edit['res_shadow_desc'])."</td>
                                    <td><input type=\"text\" name=\"shadow_res\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>

                                    <td colspan=\"2\">".makeinfocell($lang_item['res_arcane'],$lang_item_edit['res_arcane_desc'])."</td>
                                    <td><input type=\"text\" name=\"arcane_res\" size=\"10\" maxlength=\"30\" value=\"0\" /></td>
                                </tr>
                            </table>
                            <br /><br />
                        </div>";
    $output .= "
                        <div id=\"pane4\">
                        <br /><br />
                            <table class=\"lined\" style=\"width: 720px;\">
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['weapon_properties']}:</td></tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['delay'],$lang_item_edit['delay_desc'])."</td>
                                    <td colspan=\"2\"><input type=\"text\" name=\"delay\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['ranged_mod'],$lang_item_edit['ranged_mod_desc'])."</td>
                                    <td colspan=\"2\"><input type=\"text\" name=\"RangedModRange\" size=\"10\" maxlength=\"40\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['armor_dmg_mod'],$lang_item_edit['armor_dmg_mod_desc'])."</td>
                                    <td colspan=\"2\"><input type=\"text\" name=\"ArmorDamageModifier\" size=\"10\" maxlength=\"40\" value=\"0\" /></td>
                                    <td>".makeinfocell($lang_item_edit['ammo_type'],$lang_item_edit['ammo_type_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"ammo_type\">
                                            <option value=\"0\">0 - {$lang_item['none']}</option>
                                            <option value=\"2\">2 - {$lang_item['arrows']}</option>
                                            <option value=\"3\">3 - {$lang_item['bullets']}</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['weapon_damage']}:</td></tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['damage_type']." 1",$lang_item_edit['damage_type_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"dmg_type1\">";
    output_dmgtype_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td>".makeinfocell($lang_item_edit['dmg_min_max'],$lang_item_edit['dmg_min_max_desc'])."</td>
                                    <td colspan=\"4\"><input type=\"text\" name=\"dmg_min1\" size=\"8\" maxlength=\"45\" value=\"0\" /> - <input type=\"text\" name=\"dmg_max1\" size=\"8\" maxlength=\"45\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['damage_type']." 2",$lang_item_edit['damage_type_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"dmg_type2\">";
    output_dmgtype_options(NULL);
    $output .= "
                                        </select>
                                    </td>
                                    <td>".makeinfocell($lang_item_edit['dmg_min_max'],$lang_item_edit['dmg_min_max_desc'])."</td>
                                    <td colspan=\"4\"><input type=\"text\" name=\"dmg_min2\" size=\"8\" maxlength=\"45\" value=\"0\" /> - <input type=\"text\" name=\"dmg_max2\" size=\"8\" maxlength=\"45\" value=\"0\" /></td>
                                </tr>
                            </table>
                            <br /><br />
                        </div>";

    $output .= "
                        <div id=\"pane5\">
                        <br /><br />
                        <table class=\"lined\" style=\"width: 720px;\">
                            <tr>
                                <td colspan=\"2\">{$lang_item_edit['item_spell']} 1</td>
                                <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                                <td><input type=\"text\" name=\"spellid_1\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                                <td>
                                    <select name=\"spelltrigger_1\">
                                        <option value=\"0\">0: {$lang_item['spell_use']}</option>
                                        <option value=\"1\">1: {$lang_item['spell_equip']}</option>
                                        <option value=\"2\">2: {$lang_item['spell_coh']}</option>
                                        <option value=\"4\">4: {$lang_item['soul_stone']}</option>
                                    </select>
                                </td>
                                <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcharges_1\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcooldown_1\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategory_1\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategorycooldown_1\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                                <td><input type=\"text\" name=\"spellppmRate_1\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                            <tr>
                                <td colspan=\"2\">{$lang_item_edit['item_spell']} 2</td>
                                <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                                <td><input type=\"text\" name=\"spellid_2\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                                <td>
                                    <select name=\"spelltrigger_2\">
                                        <option value=\"0\">0: {$lang_item['spell_use']}</option>
                                        <option value=\"1\">1: {$lang_item['spell_equip']}</option>
                                        <option value=\"2\">2: {$lang_item['spell_coh']}</option>
                                        <option value=\"4\">4: {$lang_item['soul_stone']}</option>
                                    </select>
                                </td>
                                <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcharges_2\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcooldown_2\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategory_2\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategorycooldown_2\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                                <td><input type=\"text\" name=\"spellppmRate_2\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                            <tr>
                                <td colspan=\"2\">{$lang_item_edit['item_spell']} 3</td>
                                <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                                <td><input type=\"text\" name=\"spellid_3\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                                <td>
                                    <select name=\"spelltrigger_3\">
                                        <option value=\"0\">0: {$lang_item['spell_use']}</option>
                                        <option value=\"1\">1: {$lang_item['spell_equip']}</option>
                                        <option value=\"2\">2: {$lang_item['spell_coh']}</option>
                                        <option value=\"4\">4: {$lang_item['soul_stone']}</option>
                                    </select>
                                </td>
                                <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcharges_3\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcooldown_3\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategory_3\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategorycooldown_3\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                                <td><input type=\"text\" name=\"spellppmRate_3\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                            <tr>
                                <td colspan=\"2\">{$lang_item_edit['item_spell']} 4</td>
                                <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                                <td><input type=\"text\" name=\"spellid_4\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                                <td>
                                    <select name=\"spelltrigger_4\">
                                        <option value=\"0\">0: {$lang_item['spell_use']}</option>
                                        <option value=\"1\">1: {$lang_item['spell_equip']}</option>
                                        <option value=\"2\">2: {$lang_item['spell_coh']}</option>
                                        <option value=\"4\">4: {$lang_item['soul_stone']}</option>
                                    </select>
                                </td>
                                <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcharges_4\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcooldown_4\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategory_4\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategorycooldown_4\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                                <td><input type=\"text\" name=\"spellppmRate_4\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                            <tr>
                                <td colspan=\"2\">{$lang_item_edit['item_spell']} 5</td>
                                <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                                <td><input type=\"text\" name=\"spellid_5\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                                <td>
                                    <select name=\"spelltrigger_5\">
                                        <option value=\"0\">0: {$lang_item['spell_use']}</option>
                                        <option value=\"1\">1: {$lang_item['spell_equip']}</option>
                                        <option value=\"2\">2: {$lang_item['spell_coh']}</option>
                                        <option value=\"4\">4: {$lang_item['soul_stone']}</option>
                                    </select>
                                </td>
                                <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcharges_5\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcooldown_5\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategory_5\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>

                                <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                                <td><input type=\"text\" name=\"spellcategorycooldown_5\" size=\"6\" maxlength=\"30\" value=\"-1\" /></td>

                                <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                                <td><input type=\"text\" name=\"spellppmRate_5\" size=\"6\" maxlength=\"30\" value=\"0\" /></td>
                             </tr>
                        </table>
                        <br /><br />
                    </div>";

    $output .= "
                    <div id=\"pane6\">
                    <br /><br />
                    <table class=\"lined\" style=\"width: 720px;\">
                        <tr>
                            <td>".makeinfocell($lang_item_edit['allow_class'],$lang_item_edit['allow_class_desc'])."</td>
                            <td>
                                <select multiple=\"multiple\" name=\"AllowableClass[]\" size=\"5\">
                                    <option value=\"-1\">-1 - {$lang_item_edit['all']}</option>
                                    <option value=\"1\">1 - {$lang_id_tab['warrior']}</option>
                                    <option value=\"2\">2 - {$lang_id_tab['paladin']}</option>
                                    <option value=\"4\">4 - {$lang_id_tab['hunter']}</option>
                                    <option value=\"8\">8 - {$lang_id_tab['rogue']}</option>
                                    <option value=\"16\">16 - {$lang_id_tab['priest']}</option>
                                    <option value=\"64\">64 - {$lang_id_tab['shaman']}</option>
                                    <option value=\"128\">128 - {$lang_id_tab['mage']}</option>
                                    <option value=\"256\">256 - {$lang_id_tab['warlock']}</option>
                                    <option value=\"1024\">1024 - {$lang_id_tab['druid']}</option>
                                </select>
                            </td>

                            <td>".makeinfocell($lang_item_edit['allow_race'],$lang_item_edit['allow_race_desc'])."</td>
                            <td>
                                <select multiple=\"multiple\" name=\"AllowableRace[]\" size=\"5\">
                                    <option value=\"-1\">-1 - {$lang_item_edit['all']}</option>
                                    <option value=\"1\">1 - {$lang_id_tab['human']}</option>
                                    <option value=\"2\">2 - {$lang_id_tab['orc']}</option>
                                    <option value=\"4\">4 - {$lang_id_tab['dwarf']}</option>
                                    <option value=\"8\">8 - {$lang_id_tab['nightelf']}</option>
                                    <option value=\"16\">16 - {$lang_id_tab['undead']}</option>
                                    <option value=\"32\">32 - {$lang_id_tab['tauren']}</option>
                                    <option value=\"64\">64 - {$lang_id_tab['gnome']}</option>
                                    <option value=\"128\">128 - {$lang_id_tab['troll']}</option>
                                    <option value=\"256\">256 - {$lang_id_tab['draenei']}</option>
                                    <option value=\"512\">512 - {$lang_id_tab['bloodelf']}</option>
                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['req_skill'],$lang_item_edit['req_skill_desc'])."</td>
                            <td><input type=\"text\" name=\"RequiredSkill\" size=\"15\" maxlength=\"30\" value=\"0\" /></td>

                            <td>".makeinfocell($lang_item_edit['req_skill_rank'],$lang_item_edit['req_skill_rank_desc'])."</td>
                            <td><input type=\"text\" name=\"RequiredSkillRank\" size=\"15\" maxlength=\"30\" value=\"0\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['req_spell'],$lang_item_edit['req_spell_desc'])."</td>
                            <td><input type=\"text\" name=\"requiredspell\" size=\"15\" maxlength=\"30\" value=\"0\" /></td>

                            <td>".makeinfocell($lang_item_edit['req_honor_rank'],$lang_item_edit['req_honor_rank_desc'])."</td>
                            <td><input type=\"text\" name=\"requiredhonorrank\" size=\"15\" maxlength=\"30\" value=\"0\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['req_rep_faction'],$lang_item_edit['req_rep_faction_desc'])."</td>
                            <td><input type=\"text\" name=\"RequiredReputationFaction\" size=\"15\" maxlength=\"30\" value=\"0\" /></td>

                            <td>".makeinfocell($lang_item_edit['req_rep_rank'],$lang_item_edit['req_rep_rank_desc'])."</td>
                            <td>
                                <select name=\"RequiredReputationRank\">
                                    <option value=\"0\">0 - {$lang_item_edit['hated']}</option>
                                    <option value=\"1\">1 - {$lang_item_edit['hostile']}</option>
                                    <option value=\"2\">2 - {$lang_item_edit['unfriendly']}</option>
                                    <option value=\"3\">3 - {$lang_item_edit['neutral']}</option>
                                    <option value=\"4\">4 - {$lang_item_edit['friendly']}</option>
                                    <option value=\"5\">5 - {$lang_item_edit['honored']}</option>
                                    <option value=\"6\">6 - {$lang_item_edit['reverted']}</option>
                                    <option value=\"7\">7 - {$lang_item_edit['exalted']}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"2\">".makeinfocell($lang_item_edit['req_city_rank'],$lang_item_edit['req_city_rank_desc'])."</td>
                            <td colspan=\"2\"><input type=\"text\" name=\"RequiredCityRank\" size=\"15\" maxlength=\"30\" value=\"0\" /></td>
                        </tr>
                    </table>
                    <br /><br />
                </div>";

    $output .= "
                <div id=\"pane7\">
                <br /><br />
                <table class=\"lined\" style=\"width: 720px;\">
                    <tr>
                        <td>".makeinfocell($lang_item_edit['socket_color']." 1",$lang_item_edit['socket_color_desc'])."</td>
                        <td>
                            <select name=\"socketColor_1\">
                                <option value=\"0\">0: {$lang_item['none']}</option>
                                <option value=\"1\">1: {$lang_item['socket_meta']}</option>
                                <option value=\"2\">2: {$lang_item['socket_red']}</option>
                                <option value=\"4\">4: {$lang_item['socket_yellow']}</option>
                                <option value=\"8\">8: {$lang_item['socket_blue']}</option>
                            </select>
                        </td>
                        <td>".makeinfocell($lang_item_edit['socket_content']." 1",$lang_item_edit['socket_content_desc'])."</td>
                        <td><input type=\"text\" name=\"socketContent_1\" size=\"15\" maxlength=\"10\" value=\"0\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['socket_color']." 2",$lang_item_edit['socket_color_desc'])."</td>
                        <td>
                            <select name=\"socketColor_2\">
                                <option value=\"0\">0: {$lang_item['none']}</option>
                                <option value=\"1\">1: {$lang_item['socket_meta']}</option>
                                <option value=\"2\">2: {$lang_item['socket_red']}</option>
                                <option value=\"4\">4: {$lang_item['socket_yellow']}</option>
                                <option value=\"8\">8: {$lang_item['socket_blue']}</option>
                            </select>
                        </td>
                        <td>".makeinfocell($lang_item_edit['socket_content']." 2",$lang_item_edit['socket_content_desc'])."</td>
                        <td><input type=\"text\" name=\"socketContent_2\" size=\"15\" maxlength=\"10\" value=\"0\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['socket_color']." 3",$lang_item_edit['socket_color_desc'])."</td>
                        <td>
                            <select name=\"socketColor_3\">
                                <option value=\"0\">0: {$lang_item['none']}</option>
                                <option value=\"1\">1: {$lang_item['socket_meta']}</option>
                                <option value=\"2\">2: {$lang_item['socket_red']}</option>
                                <option value=\"4\">4: {$lang_item['socket_yellow']}</option>
                                <option value=\"8\">8: {$lang_item['socket_blue']}</option>
                            </select>
                        </td>

                        <td>".makeinfocell($lang_item_edit['socket_content']." 3",$lang_item_edit['socket_content_desc'])."</td>
                        <td><input type=\"text\" name=\"socketContent_3\" size=\"15\" maxlength=\"10\" value=\"0\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['socket_bonus'],$lang_item_edit['socket_bonus_desc'])."</td>
                        <td><input type=\"text\" name=\"socketBonus\" size=\"15\" maxlength=\"10\" value=\"0\" /></td>
                        <td>".makeinfocell($lang_item_edit['gem_properties'],$lang_item_edit['gem_properties_desc'])."</td>
                        <td><input type=\"text\" name=\"GemProperties\" size=\"15\" maxlength=\"10\" value=\"0\" /></td>
                    </tr>
                </table>
                <br /><br />
            </div>
            </div>
            </div>
            <br />
        </form>
        <script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>";
    $output .= "
        <table class=\"hidden\">
            <tr>
                <td>";

    makebutton($lang_item_edit['update'], "javascript:do_submit('form1',0)",180);
    makebutton($lang_item_edit['export_sql'], "javascript:do_submit('form1',1)",180);
    makebutton($lang_item_edit['search_items'], "item.php",180);

    $output .= "
                </td>
            </tr>
        </table>
    </center>";
}


//########################################################################################################################
// EDIT ITEM FORM
//########################################################################################################################
function edit() {
    global $lang_global, $lang_item_templ, $lang_item, $lang_item_edit, $output, $world_db, $realm_id,
            $item_datasite, $lang_id_tab, $quest_datasite, $action_permission, $user_lvl, $creature_datasite;
    wowhead_tt();

    valid_login($action_permission['read']);

    if (!isset($_GET['entry']))
        redirect("item.php?error=1");

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $entry = $sql->quote_smart($_GET['entry']);
    $deplang = get_lang_id();
    $result = $sql->query("SELECT `item_template`.`entry`,`class`,`subclass`,`unk0`,IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name,`displayid`,`Quality`,`Flags`,`BuyCount`,`BuyPrice`,`SellPrice`,`InventoryType`,`AllowableClass`,`AllowableRace`,`ItemLevel`,`RequiredLevel`,`RequiredSkill`,`RequiredSkillRank`,`requiredspell`,`requiredhonorrank`,`RequiredCityRank`,`RequiredReputationFaction`,`RequiredReputationRank`,`maxcount`,`stackable`,`ContainerSlots`,`stat_type1`,`stat_value1`,`stat_type2`,`stat_value2`,`stat_type3`,`stat_value3`,`stat_type4`,`stat_value4`,`stat_type5`,`stat_value5`,`stat_type6`,`stat_value6`,`stat_type7`,`stat_value7`,`stat_type8`,`stat_value8`,`stat_type9`,`stat_value9`,`stat_type10`,`stat_value10`,`dmg_min1`,`dmg_max1`,`dmg_type1`,`dmg_min2`,`dmg_max2`,`dmg_type2`,`armor`,`holy_res`,`fire_res`,`nature_res`,`frost_res`,`shadow_res`,`arcane_res`,`delay`,`ammo_type`,`RangedModRange`,`spellid_1`,`spelltrigger_1`,`spellcharges_1`,`spellppmRate_1`,`spellcooldown_1`,`spellcategory_1`,`spellcategorycooldown_1`,`spellid_2`,`spelltrigger_2`,`spellcharges_2`,`spellppmRate_2`,`spellcooldown_2`,`spellcategory_2`,`spellcategorycooldown_2`,`spellid_3`,`spelltrigger_3`,`spellcharges_3`,`spellppmRate_3`,`spellcooldown_3`,`spellcategory_3`,`spellcategorycooldown_3`,`spellid_4`,`spelltrigger_4`,`spellcharges_4`,`spellppmRate_4`,`spellcooldown_4`,`spellcategory_4`,`spellcategorycooldown_4`,`spellid_5`,`spelltrigger_5`,`spellcharges_5`,`spellppmRate_5`,`spellcooldown_5`,`spellcategory_5`,`spellcategorycooldown_5`,`bonding`,`description`,`PageText`,`LanguageID`,`PageMaterial`,`startquest`,`lockid`,`Material`,`sheath`,`RandomProperty`,`RandomSuffix`,`block`,`itemset`,`MaxDurability`,`area`,`Map`,`BagFamily`,`TotemCategory`,`socketColor_1`,`socketContent_1`,`socketColor_2`,`socketContent_2`,`socketColor_3`,`socketContent_3`,`socketBonus`,`GemProperties`,`RequiredDisenchantSkill`,`ArmorDamageModifier`,`ScriptName`,`DisenchantID`,`FoodType`,`minMoneyLoot`,`maxMoneyLoot` FROM item_template LEFT JOIN locales_item ON item_template.entry = locales_item.entry WHERE item_template.entry = '$entry'");

    if ($result)
    {
        $item = $sql->fetch_assoc($result);
        require_once("scripts/get_lib.php");
        //$tooltip = get_item_tooltip($entry);

        $output .= "
                <script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
                <center>
                <br /><br /><br />
                    <form method=\"post\" action=\"item.php?action=do_update\" name=\"form1\">
                        <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
                        <input type=\"hidden\" name=\"type\" value=\"edit\"/>
                        <input type=\"hidden\" name=\"entry\" value=\"$entry\"/>

                        <div class=\"jtab-container\" id=\"container\">
                            <ul class=\"jtabs\">
                                <li><a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">{$lang_item_edit['general_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane2', this)\">{$lang_item_edit['additional_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane3', this)\">{$lang_item_edit['stats_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane4', this)\">{$lang_item_edit['damage_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane5', this)\">{$lang_item_edit['spell_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane7', this)\">{$lang_item_edit['sock_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane6', this)\">{$lang_item_edit['req_tab']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane8', this)\">{$lang_item_edit['info']}</a></li>";
        if ($item['DisenchantID'])
            $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane9', this)\">{$lang_item_edit['disenchant_tab']}</a></li>";
        $output .= "
                            </ul>
                            <div class=\"jtab-panes\">";

        $output .= "
                                <div id=\"pane1\">
                                <br /><br />
                                <table class=\"lined\" style=\"width: 720px;\">
                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['general']}:</td></tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['entry'],$lang_item_edit['entry_desc'])."</td>
                                        <td>";
        //$output .= maketooltip($entry, "$item_datasite$entry", $tooltip, "item_tooltip");
        $output .= "
                                        </td>
                                        <td>".makeinfocell($lang_item_edit['display_id'],$lang_item_edit['display_id_desc'])."</td>
                                        <td><input type=\"text\" name=\"displayid\" size=\"8\" maxlength=\"11\" value=\"{$item['displayid']}\" /></td>

                                        <td>".makeinfocell($lang_item_edit['req_level'],$lang_item_edit['req_level_desc'])."</td>
                                        <td><input type=\"text\" name=\"RequiredLevel\" size=\"8\" maxlength=\"4\" value=\"{$item['RequiredLevel']}\" /></td>

                                        <td>".makeinfocell($lang_item_edit['item_level'],$lang_item_edit['item_level_desc'])."</td>
                                        <td><input type=\"text\" name=\"ItemLevel\" size=\"8\" maxlength=\"4\" value=\"{$item['ItemLevel']}\" /></td>
                                    </tr>

                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['names']}:</td></tr>
                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['item_name'],$lang_item_edit['item_name_desc'])."</td>
                                        <td colspan=\"3\"><input type=\"text\" name=\"name\" size=\"30\" maxlength=\"225\" value=\"{$item['name']}\" /></td>

                                        <td>".makeinfocell($lang_item_edit['script_name'],$lang_item_edit['script_name_desc'])."</td>
                                        <td colspan=\"3\"><input type=\"text\" name=\"ScriptName\" size=\"30\" maxlength=\"100\" value=\"{$item['ScriptName']}\" /></td>
                                    </tr>

                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['description'],$lang_item_edit['description_desc'])."</td>
                                        <td colspan=\"3\"><input type=\"text\" name=\"description\" size=\"30\" maxlength=\"225\" value=\"{$item['description']}\" /></td>
                                        <td colspan=\"4\"></td>
                                    </tr>

                                    <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['type']}:</td></tr>
                                    <tr>";

        $class = [0 =>    "", 1 => "", 2 => "", 4 => "", 5 => "", 6 => "", 7 => "", 9 => "", 11 => "", 12 => "", 13 => "",
                        14 =>   "",15 => ""];
        $class[$item['class']] = " selected=\"selected\" ";

        $output .= "
                                        <td>".makeinfocell($lang_item_edit['class'],$lang_item_edit['class_desc'])."</td>
                                        <td colspan=\"3\">
                                            <select name=\"class\">
                                                <option value=\"0\" {$class[0]}>0 - {$lang_item['consumable']}</option>
                                                <option value=\"1\" {$class[1]}>1 - {$lang_item['bag']}</option>
                                                <option value=\"2\" {$class[2]}>2 - {$lang_item['weapon']}</option>
                                                <option value=\"4\" {$class[4]}>4 - {$lang_item['armor']}</option>
                                                <option value=\"5\" {$class[5]}>5 - {$lang_item['reagent']}</option>
                                                <option value=\"6\" {$class[6]}>6 - {$lang_item['projectile']}</option>
                                                <option value=\"7\" {$class[7]}>7 - {$lang_item['trade_goods']}s</option>
                                                <option value=\"9\" {$class[9]}>9 - {$lang_item['recipe']}</option>
                                                <option value=\"11\" {$class[11]}>11 - {$lang_item['quiver']}</option>
                                                <option value=\"12\" {$class[12]}>12 - {$lang_item['quest']}</option>
                                                <option value=\"13\" {$class[13]}>13 - {$lang_item['key']}</option>
                                                <option value=\"14\" {$class[14]}>14 - {$lang_item['permanent']}</option>
                                                <option value=\"15\" {$class[15]}>15 - {$lang_item['misc_short']}</option>
                                            </select>
                                        </td>";
        unset($class);

        $subclass = [
          0 => [0 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => ""],
          1 => [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => ""],
          2 => [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 10 => "",
              11 => "", 12 => "", 13 => "", 14 => "", 15 => "", 16 => "", 17 => "", 18 => "", 19 => "", 20 => ""],
          4 => [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => ""],
          6 => [2 => "", 3 => ""],
          7 => [0 => "", 1 => "", 2 => "", 3 => ""],
          9 => [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "",10 => ""],
          11 => [2 => "", 3 => ""],
          13 => [0 => "", 1 => ""]
        ];

        $subclass[$item['class']][$item['subclass']] = " selected=\"selected\" ";

        $output .= "
                                        <td>".makeinfocell($lang_item_edit['subclass'],$lang_item_edit['subclass_desc'])."</td>
                                        <td colspan=\"3\">
                                            <select name=\"subclass\">
                                                <option value=\"0\" {$subclass[0][0]}>0 - {$lang_item['none']}</option>
                                                <optgroup label=\"Class 0: {$lang_item['consumable']}\">
                                                    <option value=\"0\" {$subclass[0][0]}>0 - {$lang_item['consumable']}</option>
                                                    <option value=\"3\" {$subclass[0][3]}>3 - {$lang_item['potion']}</option>
                                                    <option value=\"4\" {$subclass[0][4]}>4 - {$lang_item['scroll']}</option>
                                                    <option value=\"5\" {$subclass[0][5]}>5 - {$lang_item['bandage']}</option>
                                                    <option value=\"6\" {$subclass[0][6]}>6 - {$lang_item['healthstone']}</option>
                                                    <option value=\"7\" {$subclass[0][7]}>7 - {$lang_item['combat_effect']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 1: {$lang_item['bag']}\">
                                                    <option value=\"0\" {$subclass[1][0]}>0 - {$lang_item['bag']}</option>
                                                    <option value=\"1\" {$subclass[1][1]}>1 - {$lang_item['soul_shards']}</option>
                                                    <option value=\"2\" {$subclass[1][2]}>2 - {$lang_item['herbs']}</option>
                                                    <option value=\"3\" {$subclass[1][3]}>3 - {$lang_item['enchanting']}</option>
                                                    <option value=\"4\" {$subclass[1][4]}>4 - {$lang_item['engineering']}</option>
                                                    <option value=\"5\" {$subclass[1][5]}>5 - {$lang_item['gems']}</option>
                                                    <option value=\"6\" {$subclass[1][6]}>6 - {$lang_item['mining']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 2: {$lang_item['weapon']}\">
                                                    <option value=\"0\" {$subclass[2][0]}>0 - {$lang_item['axe_1h']}</option>
                                                    <option value=\"1\" {$subclass[2][2]}>1 - {$lang_item['axe_2h']}</option>
                                                    <option value=\"2\" {$subclass[2][2]}>2 - {$lang_item['bow']}</option>
                                                    <option value=\"3\" {$subclass[2][2]}>3 - {$lang_item['rifle']}</option>
                                                    <option value=\"4\" {$subclass[2][4]}>4 - {$lang_item['mace_1h']}</option>
                                                    <option value=\"5\" {$subclass[2][5]}>5 - {$lang_item['mace_2h']}</option>
                                                    <option value=\"6\" {$subclass[2][6]}>6 - {$lang_item['polearm']}</option>
                                                    <option value=\"7\" {$subclass[2][7]}>7 - {$lang_item['sword_1h']}</option>
                                                    <option value=\"8\" {$subclass[2][8]}>8 - {$lang_item['sword_2h']}</option>
                                                    <option value=\"10\" {$subclass[2][10]}>10 - {$lang_item['staff']}</option>
                                                    <option value=\"11\" {$subclass[2][11]}>11 - {$lang_item['exotic_1h']}</option>
                                                    <option value=\"12\" {$subclass[2][12]}>12 - {$lang_item['exotic_2h']}</option>
                                                    <option value=\"13\" {$subclass[2][13]}>13 - {$lang_item['fist_weapon']}</option>
                                                    <option value=\"14\" {$subclass[2][14]}>14 - {$lang_item['misc_weapon']}</option>
                                                    <option value=\"15\" {$subclass[2][15]}>15 - {$lang_item['dagger']}</option>
                                                    <option value=\"16\" {$subclass[2][16]}>16 - {$lang_item['thrown']}</option>
                                                    <option value=\"17\" {$subclass[2][17]}>17 - {$lang_item['spear']}</option>
                                                    <option value=\"18\" {$subclass[2][18]}>18 - {$lang_item['crossbow']}</option>
                                                    <option value=\"19\" {$subclass[2][19]}>19 - {$lang_item['wand']}</option>
                                                    <option value=\"20\" {$subclass[2][20]}>20 - {$lang_item['fishing_pole']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 4: {$lang_item['armor']}\">
                                                    <option value=\"0\" {$subclass[4][0]}>0 - {$lang_item['misc']}</option>
                                                    <option value=\"1\" {$subclass[4][1]}>1 - {$lang_item['cloth']}</option>
                                                    <option value=\"2\" {$subclass[4][2]}>2 - {$lang_item['leather']}</option>
                                                    <option value=\"3\" {$subclass[4][3]}>3 - {$lang_item['mail']}</option>
                                                    <option value=\"4\" {$subclass[4][4]}>4 - {$lang_item['plate']}</option>
                                                    <option value=\"5\" {$subclass[4][5]}>5 - {$lang_item['buckler']}</option>
                                                    <option value=\"6\" {$subclass[4][6]}>6 - {$lang_item['shield']}</option>
                                                    <option value=\"7\" {$subclass[4][7]}>7 - {$lang_item['libram']}</option>
                                                    <option value=\"8\" {$subclass[4][8]}>8 - {$lang_item['idol']}</option>
                                                    <option value=\"9\" {$subclass[4][9]}>9 - {$lang_item['totem']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 6: {$lang_item['projectile']}\">
                                                    <option value=\"2\" {$subclass[6][2]}>2 - {$lang_item['arrows']}</option>
                                                    <option value=\"3\" {$subclass[6][3]}>3 - {$lang_item['bullets']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 7: {$lang_item['trade_goods']}\">
                                                    <option value=\"0\" {$subclass[7][0]}>0 - {$lang_item['trade_goods']}</option>
                                                    <option value=\"1\" {$subclass[7][1]}>1 - {$lang_item['parts']}</option>
                                                    <option value=\"2\" {$subclass[7][2]}>2 - {$lang_item['explosives']}</option>
                                                    <option value=\"3\" {$subclass[7][3]}>3 - {$lang_item['devices']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 9: {$lang_item['recipe']}\">
                                                    <option value=\"0\" {$subclass[9][0]}>0 - {$lang_item['book']}</option>
                                                    <option value=\"1\" {$subclass[9][1]}>1 - {$lang_item['LW_pattern']}</option>
                                                    <option value=\"2\" {$subclass[9][2]}>2 - {$lang_item['tailoring_pattern']}</option>
                                                    <option value=\"3\" {$subclass[9][3]}>3 - {$lang_item['ENG_Schematic']}</option>
                                                    <option value=\"4\" {$subclass[9][4]}>4 - {$lang_item['BS_plans']}</option>
                                                    <option value=\"5\" {$subclass[9][5]}>5 - {$lang_item['cooking_recipe']}</option>
                                                    <option value=\"6\" {$subclass[9][6]}>6 - {$lang_item['alchemy_recipe']}</option>
                                                    <option value=\"7\" {$subclass[9][7]}>7 - {$lang_item['FA_manual']}</option>
                                                    <option value=\"8\" {$subclass[9][8]}>8 - {$lang_item['ench_formula']}</option>
                                                    <option value=\"9\" {$subclass[9][9]}>9 - {$lang_item['fishing_manual']}</option>
                                                    <option value=\"10\" {$subclass[9][10]}>10 - {$lang_item['JC_formula']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 11: {$lang_item['quiver']}\">
                                                    <option value=\"2\" {$subclass[11][2]}>2 - {$lang_item['quiver']}</option>
                                                    <option value=\"3\" {$subclass[11][3]}>3 - {$lang_item['ammo_pouch']}</option>
                                                </optgroup>
                                                <optgroup label=\"Class 13: {$lang_item['key']}\">
                                                    <option value=\"0\" {$subclass[13][0]}>0 - {$lang_item['key']}</option>
                                                    <option value=\"1\" {$subclass[13][1]}>1 - {$lang_item['lockpick']}</option>
                                                </optgroup>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>";
        unset($subclass);

        $quality = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => ""];
        $quality[$item['Quality']] = " selected=\"selected\" ";

        $output .= "
                                        <td>".makeinfocell($lang_item_edit['quality'],$lang_item_edit['quality_desc'])."</td>
                                        <td colspan=\"2\">
                                            <select name=\"Quality\">
                                                <option value=\"0\" {$quality[0]}>0 - {$lang_item['poor']}</option>
                                                <option value=\"1\" {$quality[1]}>1 - {$lang_item['common']}</option>
                                                <option value=\"2\" {$quality[2]}>2 - {$lang_item['uncommon']}</option>
                                                <option value=\"3\" {$quality[3]}>3 - {$lang_item['rare']}</option>
                                                <option value=\"4\" {$quality[4]}>4 - {$lang_item['epic']}</option>
                                                <option value=\"5\" {$quality[5]}>5 - {$lang_item['legendary']}</option>
                                                <option value=\"6\" {$quality[6]}>6 - {$lang_item['artifact']}</option>
                                            </select>
                                        </td>";
        unset($quality);

        $inv_type = [0 =>     "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "", 10 => "", 11 => "", 12 => "",
                           13 =>    "", 14 => "", 15 => "", 16 => "", 17 => "", 18 => "", 19 => "", 20 => "", 21 => "", 22 => "", 23 => "",
                           24 =>    "", 25 => "", 26 => ""];
        $inv_type[$item['InventoryType']] = " selected=\"selected\" ";

        $output .= "
                                        <td>".makeinfocell($lang_item_edit['inv_type'],$lang_item_edit['inv_type_desc'])."</td>
                                        <td colspan=\"2\">
                                            <select name=\"InventoryType\">
                                                <option value=\"0\" {$inv_type[0]}>0 - {$lang_item['other']}</option>
                                                <option value=\"1\" {$inv_type[1]}>1 - {$lang_item['head']}</option>
                                                <option value=\"2\" {$inv_type[2]}>2 - {$lang_item['neck']}</option>
                                                <option value=\"3\" {$inv_type[3]}>3 - {$lang_item['shoulder']}</option>
                                                <option value=\"4\" {$inv_type[4]}>4 - {$lang_item['shirt']}</option>
                                                <option value=\"5\" {$inv_type[5]}>5 - {$lang_item['chest']}</option>
                                                <option value=\"6\" {$inv_type[6]}>6 - {$lang_item['belt']}</option>
                                                <option value=\"7\" {$inv_type[7]}>7 - {$lang_item['legs']}</option>
                                                <option value=\"8\" {$inv_type[8]}>8 - {$lang_item['feet']}</option>
                                                <option value=\"9\" {$inv_type[9]}>9 - {$lang_item['belt']}</option>
                                                <option value=\"10\" {$inv_type[10]}>10 - {$lang_item['gloves']}</option>
                                                <option value=\"11\" {$inv_type[11]}>11 - {$lang_item['finger']}</option>
                                                <option value=\"12\" {$inv_type[12]}>12 - {$lang_item['trinket']}</option>
                                                <option value=\"13\" {$inv_type[13]}>13 - {$lang_item['one_hand']}</option>
                                                <option value=\"14\" {$inv_type[14]}>14 - {$lang_item['off_hand']}</option>
                                                <option value=\"15\" {$inv_type[15]}>15 - {$lang_item['bow']}</option>
                                                <option value=\"16\" {$inv_type[16]}>16 - {$lang_item['back']}</option>
                                                <option value=\"17\" {$inv_type[17]}>17 - {$lang_item['two_hand']}</option>
                                                <option value=\"18\" {$inv_type[18]}>18 - {$lang_item['bag']}</option>
                                                <option value=\"19\" {$inv_type[19]}>19 - {$lang_item['tabard']}</option>
                                                <option value=\"20\" {$inv_type[20]}>20 - {$lang_item['robe']}</option>
                                                <option value=\"21\" {$inv_type[21]}>21 - {$lang_item['main_hand']}</option>
                                                <option value=\"22\" {$inv_type[22]}>22 - {$lang_item['off_misc']}</option>
                                                <option value=\"23\" {$inv_type[23]}>23 - {$lang_item['tome']}</option>
                                                <option value=\"24\" {$inv_type[24]}>24 - {$lang_item['projectile']}</option>
                                                <option value=\"25\" {$inv_type[25]}>25 - {$lang_item['thrown']}</option>
                                                <option value=\"26\" {$inv_type[26]}>26 - {$lang_item['rifle']}</option>
                                            </select>
                                        </td>

                                        <td>".makeinfocell($lang_item_edit['flags'],$lang_item_edit['flags_desc'])."</td>
                                        <td><input type=\"text\" name=\"Flags\" size=\"10\" maxlength=\"30\" value=\"{$item['Flags']}\" /></td>
                                    </tr>

                                    <tr>
                                        <td>".makeinfocell($lang_item_edit['item_set'],$lang_item_edit['item_set_desc'])."</td>
                                        <td><input type=\"text\" name=\"itemset\" size=\"10\" maxlength=\"30\" value=\"{$item['itemset']}\" /></td>";
        unset($inv_type);

        $bonding = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => ""];
        $bonding[$item['bonding']] = " selected=\"selected\" ";

        $output .= "
                                        <td>".makeinfocell($lang_item_edit['bonding'],$lang_item_edit['bonding_desc'])."</td>
                                        <td colspan=\"3\">
                                            <select name=\"bonding\">
                                                <option value=\"0\" {$bonding[0]}>0 - {$lang_item['no_bind']}</option>
                                                <option value=\"1\" {$bonding[1]}>1 - {$lang_item['bop']}</option>
                                                <option value=\"2\" {$bonding[2]}>2 - {$lang_item['boe']}</option>
                                                <option value=\"3\" {$bonding[3]}>3 - {$lang_item['bou']}</option>
                                                <option value=\"4\" {$bonding[4]}>4 - {$lang_item['quest_item']}</option>
                                                <option value=\"5\" {$bonding[5]}>5 - {$lang_item['quest_item']}1</option>
                                            </select>
                                        </td>

                                        <td>".makeinfocell($lang_item_edit['start_quest'],$lang_item_edit['start_quest_desc'])."</td>
                                        <td><input type=\"text\" name=\"startquest\" size=\"10\" maxlength=\"30\" value=\"{$item['startquest']}\" /></td>

                                    </tr>
                            </table>
                            <br />{$lang_item_edit['short_rules_desc']}<br /><br />
                        </div>";
        unset($bonding);

        $output .= "
                        <div id=\"pane2\">
                        <br /><br />
                            <table class=\"lined\" style=\"width: 720px;\">
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['vendor']}:</td></tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['buy_count'],$lang_item_edit['buy_count_desc'])."</td>
                                    <td><input type=\"text\" name=\"BuyCount\" size=\"8\" maxlength=\"3\" value=\"{$item['BuyCount']}\" /></td>

                                    <td>".makeinfocell($lang_item_edit['buy_price'],$lang_item_edit['buy_price_desc'])."</td>
                                    <td><input type=\"text\" name=\"BuyPrice\" size=\"8\" maxlength=\"30\" value=\"{$item['BuyPrice']}\" /></td>

                                    <td>".makeinfocell($lang_item_edit['sell_price'],$lang_item_edit['sell_price_desc'])."</td>
                                    <td><input type=\"text\" name=\"SellPrice\" size=\"8\" maxlength=\"30\" value=\"{$item['SellPrice']}\" /></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['container']}:</td></tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['max_count'],$lang_item_edit['max_count_desc'])."</td>
                                    <td><input type=\"text\" name=\"maxcount\" size=\"6\" maxlength=\"5\" value=\"{$item['maxcount']}\" /></td>
                                    <td>".makeinfocell($lang_item_edit['stackable'],$lang_item_edit['stackable_desc'])."</td>
                                    <td><input type=\"text\" name=\"stackable\" size=\"6\" maxlength=\"5\" value=\"{$item['stackable']}\" /></td>
                                    <td>".makeinfocell($lang_item_edit['bag_family'],$lang_item_edit['bag_family_desc'])."</td>";
        $bagfamily = [0 => "", 1 => "", 2 => "", 3 => "", 6 => "", 7 => "", 8 => "", 9 => "", 10 => "", 12 => ""];
        $bagfamily[$item['BagFamily']] = " selected=\"selected\" ";

        $output .= "
                                    <td>
                                        <select name=\"BagFamily\">
                                            <option value=\"0\" {$bagfamily[0]}>0 - {$lang_item['none']}</option>
                                            <option value=\"1\" {$bagfamily[1]}>1 - {$lang_item['arrows']}</option>
                                            <option value=\"2\" {$bagfamily[2]}>2 - {$lang_item['bullets']}</option>
                                            <option value=\"3\" {$bagfamily[3]}>3 - {$lang_item['soul_shards']}</option>
                                            <option value=\"6\" {$bagfamily[6]}>6 - {$lang_item['herbs']}</option>
                                            <option value=\"7\" {$bagfamily[7]}>7 - {$lang_item['enchanting']}</option>
                                            <option value=\"8\" {$bagfamily[8]}>8 - {$lang_item['engineering']}</option>
                                            <option value=\"9\" {$bagfamily[9]}>9 - {$lang_item['keys']}</option>
                                            <option value=\"10\" {$bagfamily[10]}>10 - {$lang_item['gems']}</option>
                                            <option value=\"12\" {$bagfamily[12]}>12 - {$lang_item['mining']}</option>
                                        </select>
                                    </td>
                                    <td>".makeinfocell($lang_item_edit['bag_slots'],$lang_item_edit['bag_slots_desc'])."</td>
                                    <td><input type=\"text\" name=\"ContainerSlots\" size=\"10\" maxlength=\"3\" value=\"{$item['ContainerSlots']}\" /></td>
                                </tr>
                                <tr>
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['materials']}:</td></tr>";

        unset($bagfamily);

        $Material = [-1 => "", 0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => ""];
        $Material[$item['Material']] = " selected=\"selected\" ";

        $output .= "
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['material'],$lang_item_edit['material_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"Material\">
                                            <option value=\"-1\" {$Material[-1]}>-1 - {$lang_item_edit['consumables']}</option>
                                            <option value=\"0\" {$Material[0]}>0 - {$lang_item_edit['none']}</option>
                                            <option value=\"1\" {$Material[1]}>1 - {$lang_item_edit['metal']}</option>
                                            <option value=\"2\" {$Material[2]}>2 - {$lang_item_edit['wood']}</option>
                                            <option value=\"3\" {$Material[3]}>3 - {$lang_item_edit['liquid']}</option>
                                            <option value=\"4\" {$Material[4]}>4 - {$lang_item_edit['jewelry']}</option>
                                            <option value=\"5\" {$Material[5]}>5 - {$lang_item_edit['chain']}</option>
                                            <option value=\"6\" {$Material[6]}>6 - {$lang_item_edit['plate']}</option>
                                            <option value=\"7\" {$Material[7]}>7 - {$lang_item_edit['cloth']}</option>
                                            <option value=\"8\" {$Material[8]}>8 - {$lang_item_edit['leather']}</option>
                                        </select>
                                    </td>";
        unset($Material);

        $PageMaterial = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => ""];
        $PageMaterial[$item['PageMaterial']] = " selected=\"selected\" ";

        $output .= "
                                    <td>".makeinfocell($lang_item_edit['page_material'],$lang_item_edit['page_material_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"PageMaterial\">
                                            <option value=\"0\" {$PageMaterial[0]}>0 - {$lang_item_edit['none']}</option>
                                            <option value=\"1\" {$PageMaterial[1]}>1 - {$lang_item_edit['parchment']}</option>
                                            <option value=\"2\" {$PageMaterial[2]}>2 - {$lang_item_edit['stone']}</option>
                                            <option value=\"3\" {$PageMaterial[3]}>3 - {$lang_item_edit['marble']}</option>
                                            <option value=\"4\" {$PageMaterial[4]}>4 - {$lang_item_edit['silver']}</option>
                                            <option value=\"5\" {$PageMaterial[5]}>5 - {$lang_item_edit['bronze']}</option>
                                        </select>
                                    </td>";
        unset($PageMaterial);

        $output .= "
                                    <td>".makeinfocell($lang_item_edit['max_durability'],$lang_item_edit['max_durability_desc'])."</td>
                                    <td><input type=\"text\" name=\"MaxDurability\" size=\"8\" maxlength=\"30\" value=\"{$item['MaxDurability']}\" /></td>
                                </tr>
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['RandomProperty']}:</td></tr>
                                <tr>
                                    <td colspan=\"2\">".makeinfocell($lang_item_edit['RandomProperty'],$lang_item_edit['RandomProperty_desc'])."</td>
                                    <td colspan=\"2\"><input type=\"text\" name=\"RandomProperty\" size=\"8\" maxlength=\"30\" value=\"{$item['RandomProperty']}\" /></td>
                                    <td colspan=\"2\">".makeinfocell($lang_item_edit['RandomSuffix'],$lang_item_edit['RandomSuffix_desc'])."</td>
                                    <td colspan=\"2\"><input type=\"text\" name=\"RandomSuffix\" size=\"8\" maxlength=\"10\" value=\"{$item['RandomSuffix']}\" /></td>
                                </tr>
                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['other']}:</td></tr>
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['area'],$lang_item_edit['area_desc'])."</td>
                                    <td><input type=\"text\" name=\"area\" size=\"8\" maxlength=\"10\" value=\"{$item['area']}\" /></td>
                                    <td>".makeinfocell($lang_item_edit['map'],$lang_item_edit['map_desc'])."</td>
                                    <td><input type=\"text\" name=\"Map\" size=\"8\" maxlength=\"10\" value=\"{$item['Map']}\" /></td>
                                    <td>".makeinfocell($lang_item_edit['page_text'],$lang_item_edit['page_text_desc'])."</td>
                                    <td><input type=\"text\" name=\"PageText\" size=\"6\" maxlength=\"30\" value=\"{$item['PageText']}\" /></td>
                                    <td>".makeinfocell($lang_item_edit['unk0'],$lang_item_edit['unk0_desc'])."</td>
                                    <td><input type=\"text\" name=\"unk0\" size=\"8\" maxlength=\"10\" value=\"{$item['unk0']}\" /></td>
                                </tr>
                                <tr>
                                <tr>
                                    <td colspan=\"2\">".makeinfocell($lang_item_edit['disenchant_id'],$lang_item_edit['disenchant_id_desc'])."</td>
                                    <td><input type=\"text\" name=\"DisenchantID\" size=\"10\" maxlength=\"10\" value=\"{$item['DisenchantID']}\" /></td>
                                    <td colspan=\"2\">".makeinfocell($lang_item_edit['req_skill_disenchant'],$lang_item_edit['req_skill_disenchant_desc'])."</td>
                                    <td><input type=\"text\" name=\"RequiredDisenchantSkill\" size=\"10\" maxlength=\"10\" value=\"{$item['RequiredDisenchantSkill']}\" /></td>
                                    <td>".makeinfocell($lang_item_edit['lock_id'],$lang_item_edit['lock_id_desc'])."</td>
                                    <td><input type=\"text\" name=\"lockid\" size=\"8\" maxlength=\"30\" value=\"{$item['lockid']}\" /></td>
                                </tr>";


        $LanguageID = [0 => "", 1 => "", 2 => "", 3 => "", 6 => "", 7 => "", 8 => "", 9 => "", 10 => "", 11 => "",
                   12 => "", 13 => "", 14 => "", 33 => ""];
        $LanguageID[$item['LanguageID']] = " selected=\"selected\" ";

        $output .= "
                                <tr>
                                    <td>".makeinfocell($lang_item_edit['lang_id'],$lang_item_edit['lang_id_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"LanguageID\">
                                            <option value=\"0\" {$LanguageID[0]}>0 - {$lang_item_edit['other']}</option>
                                            <option value=\"1\" {$LanguageID[1]}>1 - Orcish</option>
                                            <option value=\"2\" {$LanguageID[2]}>2 - Darnassian</option>
                                            <option value=\"3\" {$LanguageID[3]}>3 - Taurahe</option>
                                            <option value=\"6\" {$LanguageID[6]}>6 - Dwarvish</option>
                                            <option value=\"7\" {$LanguageID[7]}>7 - Common</option>
                                            <option value=\"8\" {$LanguageID[8]}>8 - Demonic</option>
                                            <option value=\"9\" {$LanguageID[9]}>9 - Titan</option>
                                            <option value=\"10\" {$LanguageID[10]}>10 - Thelassian</option>
                                            <option value=\"11\" {$LanguageID[11]}>11 - Draconic</option>
                                            <option value=\"12\" {$LanguageID[12]}>12 - Kalimag</option>
                                            <option value=\"13\" {$LanguageID[13]}>13 - Gnomish</option>
                                            <option value=\"14\" {$LanguageID[14]}>14 - Troll</option>
                                            <option value=\"33\" {$LanguageID[33]}>33 - Gutterspeak</option>
                                        </select>
                                    </td>";
        unset($LanguageID);

        $sheath = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 7 => ""];
        $sheath[$item['sheath']] = " selected=\"selected\" ";

        $output .= "
                                    <td>".makeinfocell($lang_item_edit['sheath'],$lang_item_edit['sheath_desc'])."</td>
                                    <td colspan=\"2\">
                                        <select name=\"sheath\">
                                            <option value=\"0\" {$sheath[0]}>0 - {$lang_item_edit['other']}</option>
                                            <option value=\"1\" {$sheath[1]}>1 - {$lang_item['sword_2h']}</option>
                                            <option value=\"2\" {$sheath[2]}>2 - {$lang_item['staff']}</option>
                                            <option value=\"3\" {$sheath[3]}>3 - {$lang_item['sword_1h']}</option>
                                            <option value=\"4\" {$sheath[4]}>4 - {$lang_item['shield']}</option>
                                            <option value=\"5\" {$sheath[5]}>5 - {$lang_item['rod']}</option>
                                            <option value=\"7\" {$sheath[7]}>7 - {$lang_item['off_hand']}</option>
                                        </select>
                                    </td>
                                    <td>".makeinfocell($lang_item_edit['totem_category'],$lang_item_edit['totem_category_desc'])."</td>
                                    <td><input type=\"text\" name=\"TotemCategory\" size=\"8\" maxlength=\"10\" value=\"{$item['TotemCategory']}\" /></td>
                                </tr>
                            </table>
                            <br /><br />
                        </div>";
        unset($sheath);

        $output .= "
                        <div id=\"pane3\">
                        <br /><br />
                        <table class=\"lined\" style=\"width: 720px;\">
                            <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['stats']}:</td></tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 1",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type1\">";
        output_status_options($item['stat_type1']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value1\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value1']}\" /></td>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 2",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type2\">";
        output_status_options($item['stat_type2']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value2\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value2']}\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 3",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type3\">";
        output_status_options($item['stat_type3']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value3\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value3']}\" /></td>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 4",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type4\">";
        output_status_options($item['stat_type4']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value4\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value4']}\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 5",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type5\">";
        output_status_options($item['stat_type5']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value5\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value5']}\" /></td>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 6",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type6\">";
        output_status_options($item['stat_type1']);
        $output .= "
                                    </select>
                                </td>

                                <td><input type=\"text\" name=\"stat_value6\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value6']}\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 7",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type7\">";
        output_status_options($item['stat_type7']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value7\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value7']}\" /></td>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 8",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type8\">";
        output_status_options($item['stat_type8']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value8\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value8']}\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 9",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type9\">";
        output_status_options($item['stat_type9']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value9\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value9']}\" /></td>
                                <td>".makeinfocell($lang_item_edit['stat_type']." 10",$lang_item_edit['stat_type_desc'])."</td>
                                <td>
                                    <select name=\"stat_type10\">";
        output_status_options($item['stat_type10']);
        $output .= "
                                    </select>
                                </td>
                                <td><input type=\"text\" name=\"stat_value10\" size=\"10\" maxlength=\"6\" value=\"{$item['stat_value10']}\" /></td>
                            </tr>
                            <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['resis_armor']}:</td></tr>
                            <tr>
                                <td colspan=\"2\">".makeinfocell($lang_item['armor'],$lang_item_edit['armor_desc'])."</td>
                                <td><input type=\"text\" name=\"armor\" size=\"10\" maxlength=\"30\" value=\"{$item['armor']}\" /></td>
                                <td colspan=\"2\">".makeinfocell($lang_item['block'],$lang_item_edit['block_desc'])."</td>
                                <td><input type=\"text\" name=\"block\" size=\"10\" maxlength=\"30\" value=\"{$item['block']}\" /></td>
                            </tr>
                            <tr>
                                <td colspan=\"2\">".makeinfocell($lang_item['res_holy'],$lang_item_edit['res_holy_desc'])."</td>
                                <td><input type=\"text\" name=\"holy_res\" size=\"10\" maxlength=\"30\" value=\"{$item['holy_res']}\" /></td>
                                <td colspan=\"2\">".makeinfocell($lang_item['res_fire'],$lang_item_edit['res_fire_desc'])."</td>
                                <td><input type=\"text\" name=\"fire_res\" size=\"10\" maxlength=\"30\" value=\"{$item['fire_res']}\" /></td>
                            </tr>
                            <tr>
                                <td colspan=\"2\">".makeinfocell($lang_item['res_nature'],$lang_item_edit['res_nature_desc'])."</td>
                                <td><input type=\"text\" name=\"nature_res\" size=\"10\" maxlength=\"30\" value=\"{$item['nature_res']}\" /></td>
                                <td colspan=\"2\">".makeinfocell($lang_item['res_frost'],$lang_item_edit['res_frost_desc'])."</td>
                                <td><input type=\"text\" name=\"frost_res\" size=\"10\" maxlength=\"30\" value=\"{$item['frost_res']}\" /></td>
                            </tr>
                            <tr>
                                <td colspan=\"2\">".makeinfocell($lang_item['res_shadow'],$lang_item_edit['res_shadow_desc'])."</td>
                                <td><input type=\"text\" name=\"shadow_res\" size=\"10\" maxlength=\"30\" value=\"{$item['shadow_res']}\" /></td>
                               <td colspan=\"2\">".makeinfocell($lang_item['res_arcane'],$lang_item_edit['res_arcane_desc'])."</td>
                               <td><input type=\"text\" name=\"arcane_res\" size=\"10\" maxlength=\"30\" value=\"{$item['arcane_res']}\" /></td>
                            </tr>
                        </table>
                        <br /><br />
                    </div>";

        $output .= "
                    <div id=\"pane4\">
                    <br /><br />
                        <table class=\"lined\" style=\"width: 720px;\">
                            <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['weapon_properties']}:</td></tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['delay'],$lang_item_edit['delay_desc'])."</td>
                                <td colspan=\"2\"><input type=\"text\" name=\"delay\" size=\"10\" maxlength=\"11\" value=\"{$item['delay']}\" /></td>

                                <td>".makeinfocell($lang_item_edit['ranged_mod'],$lang_item_edit['ranged_mod_desc'])."</td>
                                <td colspan=\"2\"><input type=\"text\" name=\"RangedModRange\" size=\"10\" maxlength=\"40\" value=\"{$item['RangedModRange']}\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['armor_dmg_mod'],$lang_item_edit['armor_dmg_mod_desc'])."</td>
                                <td colspan=\"2\"><input type=\"text\" name=\"ArmorDamageModifier\" size=\"10\" maxlength=\"40\" value=\"{$item['ArmorDamageModifier']}\" /></td>";

        $ammo_type  = [0 => "", 2 => "", 3 => ""];
        $ammo_type [$item['ammo_type']] = " selected=\"selected\" ";

        $output .= "
                                <td>".makeinfocell($lang_item_edit['ammo_type'],$lang_item_edit['ammo_type_desc'])."</td>
                                <td colspan=\"2\">
                                    <select name=\"ammo_type\">
                                        <option value=\"0\" {$ammo_type[0]}>0 - {$lang_item['none']}</option>
                                        <option value=\"2\" {$ammo_type[2]}>2 - {$lang_item['arrows']}</option>
                                        <option value=\"3\" {$ammo_type[3]}>3 - {$lang_item['bullets']}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_item_edit['weapon_damage']}:</td></tr>
                            <tr>";
        unset($ammo_type);

        $output .= "
                                <td>".makeinfocell($lang_item_edit['damage_type']." 1",$lang_item_edit['damage_type_desc'])."</td>
                                <td colspan=\"2\">
                                    <select name=\"dmg_type1\">";
        output_dmgtype_options($item['dmg_type1']);
        $output .= "
                                    </select>
                                </td>

                                <td>".makeinfocell($lang_item_edit['dmg_min_max'],$lang_item_edit['dmg_min_max_desc'])."</td>
                                <td colspan=\"4\"><input type=\"text\" name=\"dmg_min1\" size=\"8\" maxlength=\"45\" value=\"{$item['dmg_min1']}\" /> - <input type=\"text\" name=\"dmg_max1\" size=\"8\" maxlength=\"45\" value=\"{$item['dmg_max1']}\" /></td>
                            </tr>
                            <tr>
                                <td>".makeinfocell($lang_item_edit['damage_type']." 2",$lang_item_edit['damage_type_desc'])."</td>
                                <td colspan=\"2\">
                                    <select name=\"dmg_type2\">";
        output_dmgtype_options($item['dmg_type2']);
        $output .= "
                                    </select>
                                </td>
                                <td>".makeinfocell($lang_item_edit['dmg_min_max'],$lang_item_edit['dmg_min_max_desc'])."</td>
                                <td colspan=\"4\"><input type=\"text\" name=\"dmg_min2\" size=\"8\" maxlength=\"45\" value=\"{$item['dmg_min2']}\" /> - <input type=\"text\" name=\"dmg_max2\" size=\"8\" maxlength=\"45\" value=\"{$item['dmg_max2']}\" /></td>
                            </tr>
                        </table>
                        <br /><br />
                    </div>";

        $output .= "
                    <div id=\"pane5\">
                    <br /><br />
                    <table class=\"lined\" style=\"width: 720px;\">
                        <tr>
                            <td colspan=\"2\">{$lang_item_edit['item_spell']} 1</td>
                            <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                            <td><input type=\"text\" name=\"spellid_1\" size=\"6\" maxlength=\"30\" value=\"{$item['spellid_1']}\" /></td>";

        $spelltrigger_1  = [0 => "", 1 => "", 2 => "", 4 => ""];
        $spelltrigger_1 [$item['spelltrigger_1']] = " selected=\"selected\" ";

        $output .= "
                            <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                            <td>
                                <select name=\"spelltrigger_1\">
                                    <option value=\"0\" {$spelltrigger_1[0]}>0: {$lang_item['spell_use']}</option>
                                    <option value=\"1\" {$spelltrigger_1[1]}>1: {$lang_item['spell_equip']}</option>
                                    <option value=\"2\" {$spelltrigger_1[2]}>2: {$lang_item['spell_coh']}</option>
                                    <option value=\"4\" {$spelltrigger_1[4]}>4: {$lang_item['soul_stone']}</option>
                                </select>
                            </td>
                            <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcharges_1\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcharges_1']}\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcooldown_1\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcooldown_1']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategory_1\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategory_1']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategorycooldown_1\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategorycooldown_1']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                            <td><input type=\"text\" name=\"spellppmRate_1\" size=\"6\" maxlength=\"30\" value=\"{$item['spellppmRate_1']}\" /></td>
                        </tr>
                        <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                        <tr>
                            <td colspan=\"2\">{$lang_item_edit['item_spell']} 2</td>
                            <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                            <td><input type=\"text\" name=\"spellid_2\" size=\"6\" maxlength=\"30\" value=\"{$item['spellid_2']}\" /></td>";
        unset($spelltrigger_1);

        $spelltrigger_2  = [0 => "", 1 => "", 2 => "", 4 => ""];
        $spelltrigger_2 [$item['spelltrigger_2']] = " selected=\"selected\" ";

        $output .= "
                            <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                            <td>
                                <select name=\"spelltrigger_2\">
                                    <option value=\"0\" {$spelltrigger_2[0]}>0: {$lang_item['spell_use']}</option>
                                    <option value=\"1\" {$spelltrigger_2[1]}>1: {$lang_item['spell_equip']}</option>
                                    <option value=\"2\" {$spelltrigger_2[2]}>2: {$lang_item['spell_coh']}</option>
                                    <option value=\"4\" {$spelltrigger_2[4]}>4: {$lang_item['soul_stone']}</option>
                                </select>
                            </td>
                            <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcharges_2\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcharges_2']}\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcooldown_2\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcooldown_2']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategory_2\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategory_2']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategorycooldown_2\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategorycooldown_2']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                            <td><input type=\"text\" name=\"spellppmRate_2\" size=\"6\" maxlength=\"30\" value=\"{$item['spellppmRate_2']}\" /></td>
                        </tr>
                        <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                        <tr>
                            <td colspan=\"2\">{$lang_item_edit['item_spell']} 3</td>
                            <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                            <td><input type=\"text\" name=\"spellid_3\" size=\"6\" maxlength=\"30\" value=\"{$item['spellid_3']}\" /></td>";
        unset($spelltrigger_2);

        $spelltrigger_3  = [0 => "", 1 => "", 2 => "", 4 => ""];
        $spelltrigger_3 [$item['spelltrigger_3']] = " selected=\"selected\" ";

        $output .= "
                            <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                            <td>
                                <select name=\"spelltrigger_3\">
                                    <option value=\"0\" {$spelltrigger_3[0]}>0: {$lang_item['spell_use']}</option>
                                    <option value=\"1\" {$spelltrigger_3[1]}>1: {$lang_item['spell_equip']}</option>
                                    <option value=\"2\" {$spelltrigger_3[2]}>2: {$lang_item['spell_coh']}</option>
                                    <option value=\"4\" {$spelltrigger_3[4]}>4: {$lang_item['soul_stone']}</option>
                                </select>
                            </td>
                            <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcharges_3\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcharges_3']}\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcooldown_3\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcooldown_3']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategory_3\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategory_3']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategorycooldown_3\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategorycooldown_3']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                            <td><input type=\"text\" name=\"spellppmRate_3\" size=\"6\" maxlength=\"30\" value=\"{$item['spellppmRate_3']}\" /></td>
                        </tr>
                        <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                        <tr>
                            <td colspan=\"2\">{$lang_item_edit['item_spell']} 4</td>
                            <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                            <td><input type=\"text\" name=\"spellid_4\" size=\"6\" maxlength=\"30\" value=\"{$item['spellid_4']}\" /></td>";
        unset($spelltrigger_3);

        $spelltrigger_4  = [0 => "", 1 => "", 2 => "", 4 => ""];
        $spelltrigger_4 [$item['spelltrigger_4']] = " selected=\"selected\" ";

        $output .= "
                            <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                            <td>
                                <select name=\"spelltrigger_4\">
                                    <option value=\"0\" {$spelltrigger_4[0]}>0: {$lang_item['spell_use']}</option>
                                    <option value=\"1\" {$spelltrigger_4[1]}>1: {$lang_item['spell_equip']}</option>
                                    <option value=\"2\" {$spelltrigger_4[2]}>2: {$lang_item['spell_coh']}</option>
                                    <option value=\"4\" {$spelltrigger_4[4]}>4: {$lang_item['soul_stone']}</option>
                                </select>
                            </td>
                            <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcharges_4\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcharges_4']}\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcooldown_4\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcooldown_4']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategory_4\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategory_4']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategorycooldown_4\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategorycooldown_4']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                            <td><input type=\"text\" name=\"spellppmRate_4\" size=\"6\" maxlength=\"30\" value=\"{$item['spellppmRate_4']}\" /></td>
                        </tr>
                        <tr><td colspan=\"6\" class=\"hidden\"></td></tr>
                        <tr>
                            <td colspan=\"2\">{$lang_item_edit['item_spell']} 5</td>
                            <td>".makeinfocell($lang_item_edit['spell_id'],$lang_item_edit['spell_id_desc'])."</td>
                            <td><input type=\"text\" name=\"spellid_5\" size=\"6\" maxlength=\"30\" value=\"{$item['spellid_5']}\" /></td>";
        unset($spelltrigger_4);

        $spelltrigger_5  = [0 => "", 1 => "", 2 => "", 4 => ""];
        $spelltrigger_5 [$item['spelltrigger_5']] = " selected=\"selected\" ";

        $output .= "
                            <td>".makeinfocell($lang_item_edit['spell_trigger'],$lang_item_edit['spell_trigger_desc'])."</td>
                            <td>
                                <select name=\"spelltrigger_5\">
                                    <option value=\"0\" {$spelltrigger_5[0]}>0: {$lang_item['spell_use']}</option>
                                    <option value=\"1\" {$spelltrigger_5[1]}>1: {$lang_item['spell_equip']}</option>
                                    <option value=\"2\" {$spelltrigger_5[2]}>2: {$lang_item['spell_coh']}</option>
                                    <option value=\"4\" {$spelltrigger_5[4]}>4: {$lang_item['soul_stone']}</option>
                                </select>
                            </td>
                            <td>".makeinfocell($lang_item_edit['spell_charges'],$lang_item_edit['spell_charges_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcharges_5\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcharges_5']}\" /></td>
                        </tr>
                        <tr>
                            <td>".makeinfocell($lang_item_edit['spell_cooldown'],$lang_item_edit['spell_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcooldown_5\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcooldown_5']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category'],$lang_item_edit['spell_category_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategory_5\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategory_5']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['spell_category_cooldown'],$lang_item_edit['spell_category_cooldown_desc'])."</td>
                            <td><input type=\"text\" name=\"spellcategorycooldown_5\" size=\"6\" maxlength=\"30\" value=\"{$item['spellcategorycooldown_5']}\" /></td>

                            <td>".makeinfocell($lang_item_edit['ppm_rate'],$lang_item_edit['ppm_rate_desc'])."</td>
                            <td><input type=\"text\" name=\"spellppmRate_5\" size=\"6\" maxlength=\"30\" value=\"{$item['spellppmRate_5']}\" /></td>
                        </tr>

                    </table>
                </div>";
        unset($spelltrigger_5);

        $output .= "
                <div id=\"pane6\">
                <br /><br />
                <table class=\"lined\" style=\"width: 720px;\">
                    <tr>";

        $AllowableClass  = [-1 => "", 1 => "", 2 => "", 4 => "", 8 => "", 16 => "", 32 => "", 64 => "", 128 => "",
                                256 => "", 512 => "", 1024 => ""];

        if($item['AllowableClass'] == -1) $AllowableClass[-1] = " selected=\"selected\" ";
        else
        {
            if ($item['AllowableClass'] & 1) $AllowableClass[1] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 2) $AllowableClass[2] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 4) $AllowableClass[4] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 8) $AllowableClass[8] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 16) $AllowableClass[16] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 32) $AllowableClass[32] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 64) $AllowableClass[64] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 128) $AllowableClass[128] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 256) $AllowableClass[256] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 512) $AllowableClass[512] = " selected=\"selected\" ";
            if ($item['AllowableClass'] & 1024) $AllowableClass[1024] = " selected=\"selected\" ";
        }

        $output .= "
                        <td>".makeinfocell($lang_item_edit['allow_class'],$lang_item_edit['allow_class_desc'])."</td>
                        <td>
                            <select multiple=\"multiple\" name=\"AllowableClass[]\" size=\"5\">
                                <option value=\"-1\" {$AllowableClass[-1]}>-1 - {$lang_item_edit['all']}</option>
                                <option value=\"1\" {$AllowableClass[1]}>1 - {$lang_id_tab['warrior']}</option>
                                <option value=\"2\" {$AllowableClass[2]}>2 - {$lang_id_tab['paladin']}</option>
                                <option value=\"4\" {$AllowableClass[4]}>4 - {$lang_id_tab['hunter']}</option>
                                <option value=\"8\" {$AllowableClass[8]}>8 - {$lang_id_tab['rogue']}</option>
                                <option value=\"16\" {$AllowableClass[16]}>16 - {$lang_id_tab['priest']}</option>
                                <option value=\"32\" {$AllowableClass[32]}>32 - FUTURE_1</option>
                                <option value=\"64\" {$AllowableClass[64]}>64 - {$lang_id_tab['shaman']}</option>
                                <option value=\"128\" {$AllowableClass[128]}>128 - {$lang_id_tab['mage']}</option>
                                <option value=\"256\" {$AllowableClass[256]}>256 - {$lang_id_tab['warlock']}</option>
                                <option value=\"512\" {$AllowableClass[512]}>512 - FUTURE_2</option>
                                <option value=\"1024\" {$AllowableClass[1024]}>1024 - {$lang_id_tab['druid']}</option>
                            </select>
                        </td>";
        unset($AllowableClass);

        $AllowableRace  = [-1 => "", 1 => "", 2 => "", 4 => "", 8 => "", 16 => "", 32 => "", 64 => "", 128 => "",
                     256 => "", 512 => ""];

        if($item['AllowableRace'] == -1) $AllowableRace[-1] = " selected=\"selected\" ";
        else
        {
            if ($item['AllowableRace'] & 1) $AllowableRace[1] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 2) $AllowableRace[2] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 4) $AllowableRace[4] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 8) $AllowableRace[8] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 16) $AllowableRace[16] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 32) $AllowableRace[32] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 64) $AllowableRace[64] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 128) $AllowableRace[128] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 256) $AllowableRace[256] = " selected=\"selected\" ";
            if ($item['AllowableRace'] & 512) $AllowableRace[512] = " selected=\"selected\" ";
        }

        $output .= "
                        <td>".makeinfocell($lang_item_edit['allow_race'],$lang_item_edit['allow_race_desc'])."</td>
                        <td>
                            <select multiple=\"multiple\" name=\"AllowableRace[]\" size=\"5\">
                                <option value=\"-1\" {$AllowableRace[-1]}>-1 - {$lang_item_edit['all']}</option>
                                <option value=\"1\" {$AllowableRace[1]}>1 - {$lang_id_tab['human']}</option>
                                <option value=\"2\" {$AllowableRace[2]}>2 - {$lang_id_tab['orc']}</option>
                                <option value=\"4\" {$AllowableRace[4]}>4 - {$lang_id_tab['dwarf']}</option>
                                <option value=\"8\" {$AllowableRace[8]}>8 - {$lang_id_tab['nightelf']}</option>
                                <option value=\"16\" {$AllowableRace[16]}>16 - {$lang_id_tab['undead']}</option>
                                <option value=\"32\" {$AllowableRace[32]}>32 - {$lang_id_tab['tauren']}</option>
                                <option value=\"64\" {$AllowableRace[64]}>64 - {$lang_id_tab['gnome']}</option>
                                <option value=\"128\" {$AllowableRace[128]}>128 - {$lang_id_tab['troll']}</option>
                                <option value=\"256\" {$AllowableRace[256]}>256 - {$lang_id_tab['draenei']}</option>
                                <option value=\"512\" {$AllowableRace[512]}>512 - {$lang_id_tab['bloodelf']}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['req_skill'],$lang_item_edit['req_skill_desc'])."</td>
                        <td><input type=\"text\" name=\"RequiredSkill\" size=\"15\" maxlength=\"30\" value=\"{$item['RequiredSkill']}\" /></td>

                        <td>".makeinfocell($lang_item_edit['req_skill_rank'],$lang_item_edit['req_skill_rank_desc'])."</td>
                        <td><input type=\"text\" name=\"RequiredSkillRank\" size=\"15\" maxlength=\"30\" value=\"{$item['RequiredSkillRank']}\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['req_spell'],$lang_item_edit['req_spell_desc'])."</td>
                        <td><input type=\"text\" name=\"requiredspell\" size=\"15\" maxlength=\"30\" value=\"{$item['requiredspell']}\" /></td>

                        <td>".makeinfocell($lang_item_edit['req_honor_rank'],$lang_item_edit['req_honor_rank_desc'])."</td>
                        <td><input type=\"text\" name=\"requiredhonorrank\" size=\"15\" maxlength=\"30\" value=\"{$item['requiredhonorrank']}\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['req_rep_faction'],$lang_item_edit['req_rep_faction_desc'])."</td>
                        <td><input type=\"text\" name=\"RequiredReputationFaction\" size=\"15\" maxlength=\"30\" value=\"{$item['RequiredReputationFaction']}\" /></td>";
        unset($AllowableRace);

        $RequiredReputationRank  = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5=> "", 6 => "", 7 => ""];
        $RequiredReputationRank [$item['RequiredReputationRank']] = " selected=\"selected\" ";

        $output .= "
                        <td>".makeinfocell($lang_item_edit['req_rep_rank'],$lang_item_edit['req_rep_rank_desc'])."</td>
                        <td>
                            <select name=\"RequiredReputationRank\">
                                <option value=\"0\" {$RequiredReputationRank[0]}>0 - {$lang_item_edit['hated']}</option>
                                <option value=\"1\" {$RequiredReputationRank[1]}>1 - {$lang_item_edit['hostile']}</option>
                                <option value=\"2\" {$RequiredReputationRank[2]}>2 - {$lang_item_edit['unfriendly']}</option>
                                <option value=\"3\" {$RequiredReputationRank[3]}>3 - {$lang_item_edit['neutral']}</option>
                                <option value=\"4\" {$RequiredReputationRank[4]}>4 - {$lang_item_edit['friendly']}</option>
                                <option value=\"5\" {$RequiredReputationRank[5]}>5 - {$lang_item_edit['honored']}</option>
                                <option value=\"6\" {$RequiredReputationRank[6]}>6 - {$lang_item_edit['reverted']}</option>
                                <option value=\"7\" {$RequiredReputationRank[7]}>7 - {$lang_item_edit['exalted']}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"2\">".makeinfocell($lang_item_edit['req_city_rank'],$lang_item_edit['req_city_rank_desc'])."</td>
                        <td colspan=\"2\"><input type=\"text\" name=\"RequiredCityRank\" size=\"15\" maxlength=\"30\" value=\"{$item['RequiredCityRank']}\" /></td>
                    </tr>
                </table>
                <br /><br />
            </div>";
        unset($RequiredReputationRank);

        $output .= "
            <div id=\"pane7\">
            <br /><br />
                <table class=\"lined\" style=\"width: 720px;\">
                    <tr>";

        $socketColor_1  = [0 => "", 1 => "", 2 => "", 4 => "", 8=> ""];
        $socketColor_1 [$item['socketColor_1']] = " selected=\"selected\" ";

        $output .= "
                        <td>".makeinfocell($lang_item_edit['socket_color']." 1",$lang_item_edit['socket_color_desc'])."</td>
                        <td>
                            <select name=\"socketColor_1\">
                                <option value=\"0\" {$socketColor_1[0]}>0: {$lang_item['none']}</option>
                                <option value=\"1\" {$socketColor_1[1]}>1: {$lang_item['socket_meta']}</option>
                                <option value=\"2\" {$socketColor_1[2]}>2: {$lang_item['socket_red']}</option>
                                <option value=\"4\" {$socketColor_1[4]}>4: {$lang_item['socket_yellow']}</option>
                                <option value=\"8\" {$socketColor_1[8]}>8: {$lang_item['socket_blue']}</option>
                            </select>
                        </td>
                        <td>".makeinfocell($lang_item_edit['socket_content']." 1",$lang_item_edit['socket_content_desc'])."</td>
                        <td><input type=\"text\" name=\"socketContent_1\" size=\"15\" maxlength=\"10\" value=\"{$item['socketContent_1']}\" /></td>
                    </tr>
                    <tr>";
        unset($socketColor_1);

        $socketColor_2  = [0 => "", 1 => "", 2 => "", 4 => "", 8=> ""];
        $socketColor_2 [$item['socketColor_2']] = " selected=\"selected\" ";

        $output .= "
                        <td>".makeinfocell($lang_item_edit['socket_color']." 2",$lang_item_edit['socket_color_desc'])."</td>
                        <td>
                            <select name=\"socketColor_2\">
                                <option value=\"0\" {$socketColor_2[0]}>0: {$lang_item['none']}</option>
                                <option value=\"1\" {$socketColor_2[1]}>1: {$lang_item['socket_meta']}</option>
                                <option value=\"2\" {$socketColor_2[2]}>2: {$lang_item['socket_red']}</option>
                                <option value=\"4\" {$socketColor_2[4]}>4: {$lang_item['socket_yellow']}</option>
                                <option value=\"8\" {$socketColor_2[8]}>8: {$lang_item['socket_blue']}</option>
                            </select>
                        </td>
                        <td>".makeinfocell($lang_item_edit['socket_content']." 2",$lang_item_edit['socket_content_desc'])."</td>
                        <td><input type=\"text\" name=\"socketContent_2\" size=\"15\" maxlength=\"10\" value=\"{$item['socketContent_2']}\" /></td>
                    </tr>
                    <tr>";
        unset($socketColor_2);

        $socketColor_3  = [0 => "", 1 => "", 2 => "", 4 => "", 8=> ""];
        $socketColor_3 [$item['socketColor_3']] = " selected=\"selected\" ";

        $output .= "
                        <td>".makeinfocell($lang_item_edit['socket_color']." 3",$lang_item_edit['socket_color_desc'])."</td>
                        <td>
                            <select name=\"socketColor_3\">
                                <option value=\"0\" {$socketColor_3[0]}>0: {$lang_item['none']}</option>
                                <option value=\"1\" {$socketColor_3[1]}>1: {$lang_item['socket_meta']}</option>
                                <option value=\"2\" {$socketColor_3[2]}>2: {$lang_item['socket_red']}</option>
                                <option value=\"4\" {$socketColor_3[4]}>4: {$lang_item['socket_yellow']}</option>
                                <option value=\"8\" {$socketColor_3[8]}>8: {$lang_item['socket_blue']}</option>
                            </select>
                        </td>
                        <td>".makeinfocell($lang_item_edit['socket_content']." 3",$lang_item_edit['socket_content_desc'])."</td>
                        <td><input type=\"text\" name=\"socketContent_3\" size=\"15\" maxlength=\"10\" value=\"{$item['socketContent_3']}\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['socket_bonus'],$lang_item_edit['socket_bonus_desc'])."</td>
                        <td><input type=\"text\" name=\"socketBonus\" size=\"15\" maxlength=\"10\" value=\"{$item['socketBonus']}\" /></td>
                        <td>".makeinfocell($lang_item_edit['gem_properties'],$lang_item_edit['gem_properties_desc'])."</td>
                        <td><input type=\"text\" name=\"GemProperties\" size=\"15\" maxlength=\"10\" value=\"{$item['GemProperties']}\" /></td>
                    </tr>
                </table>
                <br /><br />
            </div>";

        $output .= "
            <div id=\"pane8\">
            <br /><br />
                <table class=\"lined\" style=\"width: 720px;\">
                    <tr class=\"large_bold\"><td colspan=\"5\" class=\"hidden\" align=\"left\">{$lang_item_edit['dropped_by']}: {$lang_item_edit['top_x']}</td></tr>
                    <tr>
                        <th width=\"1%\">ID</th>
                        <th width=\"35%\">{$lang_item_edit['mob_name']}</th>
                        <th width=\"10%\">{$lang_item_edit['mob_level']}</th>
                        <th width=\"25%\">{$lang_item_edit['mob_drop_chance']}</th>
                        <th width=\"25%\">{$lang_item_edit['mob_quest_drop_chance']}</th>
                    </tr>";

        $result2 = $sql->query("SELECT entry,ChanceOrQuestChance,`groupid` FROM creature_loot_template WHERE item = {$item['entry']} ORDER BY `groupid`,ChanceOrQuestChance DESC LIMIT 5");
        while ($info = $sql->fetch_row($result2))
        {
            $result3 = $sql->query("SELECT creature_template.entry,IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name,maxlevel FROM creature_template LEFT JOIN locales_creature ON creature_template.entry = locales_creature.entry WHERE lootid = {$info[0]} LIMIT 1");
            while ($mob = $sql->fetch_row($result3))
            {
                $output .= "
                    <tr>
                        <td>";
                if($user_lvl >= $action_permission['delete'])
                    $output .="
                            <a class=\"tooltip\" href=\"creature.php?action=edit&amp;entry=$mob[0]&amp;error=4\" target=\"_blank\">$mob[0]</a>";
                else
                    $output .="
                            $mob[0]";
                $output .="
                        </td>
                        <td><a class=\"tooltip\" href=\"$creature_datasite$mob[0]\" target=\"_blank\">$mob[1]</a></td>
                        <td>$mob[2]</td>
                        <td>$info[1]%</td>
                        <td>$info[2]%</td></tr>";
            }
        }

        $result2 = $sql->query("SELECT creature_template.entry,IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name,maxlevel FROM creature_template LEFT JOIN locales_creature ON creature_template.entry = locales_creature.entry WHERE creature_template.entry IN (SELECT entry FROM npc_vendor WHERE item = {$item['entry']}) ORDER BY maxlevel DESC LIMIT 5");
        if ($sql->num_rows($result2))
        {
            $output .= "<tr class=\"large_bold\"><td colspan=\"4\" class=\"hidden\" align=\"left\">{$lang_item_edit['sold_by']}: {$lang_item_edit['limit_x']}</td></tr>";
            while ($mob = $sql->fetch_row($result2))
            {
                $output .= "
                    <tr>
                        <td width=\"10%\">";
                if($user_lvl >= $action_permission['delete'])
                    $output .="
                            <a class=\"tooltip\" href=\"creature.php?action=edit&amp;entry=$mob[0]&amp;error=4\" target=\"_blank\">$mob[0]</a>";
                else
                    $output .="
                            $mob[0]";
                $output .="
                        <td width=\"10%\">$mob[2]</td>
                        <td width=\"80%\" colspan=\"2\" align=\"left\">
                            <a class=\"tooltip\" href=\"$creature_datasite$mob[0]\" target=\"_blank\">$mob[1]</a></td>
                        </td>
                    </tr>";
            }
        }

        $result2 = $sql->query("SELECT quest_template.Id,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as title,Level FROM quest_template LEFT JOIN locales_quest ON quest_template.Id = locales_quest.entry WHERE ( SourceItemId = {$item['entry']} OR RequiredItemId1 = {$item['entry']} OR
                            RequiredItemId2 = {$item['entry']} OR RequiredItemId3 = {$item['entry']} OR RequiredItemId4 = {$item['entry']} OR RewardItemId1 = {$item['entry']} OR
                            RewardItemId2 = {$item['entry']} OR RewardItemId3 = {$item['entry']} OR RewardItemId4 = {$item['entry']} ) ORDER BY Level DESC");

        if ($sql->num_rows($result2))
        {
            $output .= "
                    <tr class=\"large_bold\"><td colspan=\"4\" class=\"hidden\" align=\"left\">{$lang_item_edit['involved_in_quests']}:</td></tr>";
            while ($quest = $sql->fetch_row($result2))
            {
                    $output .= "
                    <tr>
                        <td width=\"20%\">id: $quest[0]</td>
                        <td width=\"80%\" colspan=\"3\" align=\"left\"><a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">($quest[2]) $quest[1]</a></td>
                    </tr>";
            }
        }

        $result2 = $sql->query("SELECT quest_template.Id,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as title,Level FROM quest_template LEFT JOIN locales_quest ON quest_template.Id = locales_quest.entry WHERE ( RewardChoiceItemId1 = {$item['entry']} OR RewardChoiceItemId2 = {$item['entry']} OR
                                RewardChoiceItemId3 = {$item['entry']} OR RewardChoiceItemId4 = {$item['entry']} OR RewardChoiceItemId5 = {$item['entry']} OR RewardChoiceItemId6 = {$item['entry']} )
                                ORDER BY Level DESC");
        if ($sql->num_rows($result2))
        {
            $output .= "
                    <tr class=\"large_bold\"><td colspan=\"4\" class=\"hidden\" align=\"left\">{$lang_item_edit['reward_from_quest']}:</td></tr>";
            while ($quest = $sql->fetch_row($result2))
            {
                $output .= "
                    <tr>
                        <td width=\"20%\">id: $quest[0]</td>
                        <td width=\"80%\" colspan=\"3\" align=\"left\"><a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">($quest[2]) $quest[1]</a></td>
                    </tr>";
            }
        }

        $output .= "
                    </tr>
                </table>
                <br /><br />
            </div>";

        if ($item['DisenchantID'])
        {
            $output .= "
            <div id=\"pane9\">
            <br /><br />
            <table class=\"lined\" style=\"width: 720px;\">
                <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_item_edit['disenchant_templ']}: {$item['DisenchantID']}</td></tr>
                <tr>
                    <td colspan=\"6\">";

            $cel_counter = 0;
            $row_flag = 0;
            $output .= "
                        <table class=\"hidden\" align=\"center\">
                            <tr>";
            $result1 = $sql->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount,lootcondition,condition_value1, condition_value2 FROM disenchant_loot_template WHERE entry = {$item['DisenchantID']} ORDER BY ChanceOrQuestChance DESC");

            while ($item = $sql->fetch_row($result1))
            {
                $cel_counter++;
                $tooltip = get_item_name($item[0])." ($item[0])<br />{$lang_item_edit['drop_chance']}: $item[1]%<br />{$lang_item_edit['quest_drop_chance']}: $item[2]%<br />{$lang_item_edit['drop_chance']}: $item[3]-$item[4]<br />{$lang_item_edit['lootcondition']}: $item[5]<br />{$lang_item_edit['condition_value1']}: $item[6]<br />{$lang_item_edit['condition_value2']}: $item[7]";
                $output .= "
                                <td>";
                $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
                $output .= "
                                    <br /><input type=\"checkbox\" name=\"del_de_items[]\" value=\"$item[0]\" />
                                </td>";

                if ($cel_counter >= 16) {
                    $cel_counter = 0;
                    $output .= "
                                </tr>
                                <tr>";
                    $row_flag++;
                }
            };

            if ($row_flag) $output .= "
                                    <td colspan=\"".(16 - $cel_counter)."\"></td>";
            $output .= "
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_item_edit['add_items_to_templ']}:</td></tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['loot_item_id'],$lang_item_edit['loot_item_id_desc'])."</td>
                        <td><input type=\"text\" name=\"de_item\" size=\"8\" maxlength=\"10\" value=\"\" /></td>
                        <td>".makeinfocell($lang_item_edit['loot_drop_chance'],$lang_item_edit['loot_drop_chance_desc'])."</td>
                        <td><input type=\"text\" name=\"de_ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" /></td>
                        <td>".makeinfocell($lang_item_edit['loot_quest_drop_chance'],$lang_item_edit['loot_quest_drop_chance_desc'])."</td>
                        <td><input type=\"text\" name=\"de_groupid\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['min_count'],$lang_item_edit['min_count_desc'])."</td>
                        <td><input type=\"text\" name=\"de_mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
                        <td>".makeinfocell($lang_item_edit['max_count'],$lang_item_edit['max_count_desc'])."</td>
                        <td><input type=\"text\" name=\"de_maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
                    </tr>
                    <tr>
                        <td>".makeinfocell($lang_item_edit['lootcondition'],$lang_item_edit['lootcondition_desc'])."</td>
                        <td><input type=\"text\" name=\"de_lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
                        <td>".makeinfocell($lang_item_edit['condition_value1'],$lang_item_edit['condition_value1_desc'])."</td>
                        <td><input type=\"text\" name=\"de_condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
                        <td>".makeinfocell($lang_item_edit['condition_value2'],$lang_item_edit['condition_value2_desc'])."</td>
                        <td><input type=\"text\" name=\"de_condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
                    </tr>
                </table>
            </div>";
        }

        $output .= "
            </div>
            </div>
            <br />
        </form>

        <script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>";
        unset($socketColor_3);

        $output .= "
        <table class=\"hidden\">
            <tr>
                <td>";
        if($user_lvl >= $action_permission['delete'])
            makebutton($lang_item_edit['del_item'], "item.php?action=delete&amp;entry=$entry\" type=\"wrn",180);
        $output .= "
                </td>
                <td>";
        if($user_lvl >= $action_permission['update'])
            makebutton($lang_item_edit['update'], "javascript:do_submit('form1',0)\" type=\"wrn",180);
        $output .= "
                </td>
                <td>";
        if($user_lvl >= $action_permission['insert'])
            makebutton($lang_item_edit['export_sql'], "javascript:do_submit('form1',1)",180);
        $output .= "
                </td>
                <td>";
        makebutton($lang_global['back'], "javascript:window.history.back()", 130);
        $output .= "
                </td>
                <td>";
        makebutton($lang_item_edit['new_search'], "item.php",130);
        $output .= "
                </td>
            </tr>
        </table>
    </center>";
    }
    else
    {
        error($lang_item_edit['item_not_found']);
        exit();
    }
}


//########################################################################################################################
//DO UPDATE ITEM
//########################################################################################################################
function do_update() {
    global $world_db, $realm_id, $action_permission, $user_lvl;

    valid_login($action_permission['update']);

    if (!isset($_POST['type']) || $_POST['type'] === '')
        redirect("item.php?error=1");
    if (!isset($_POST['entry']) || $_POST['entry'] === '')
        redirect("item.php?error=1");

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $entry = $sql->quote_smart($_POST['entry']);
    if (isset($_POST['class']) && $_POST['class'] != '') $class = $sql->quote_smart($_POST['class']);
        else $class = 0;
    if (isset($_POST['subclass']) && $_POST['subclass'] != '') $subclass = $sql->quote_smart($_POST['subclass']);
        else $subclass = 0;
    if (isset($_POST['name']) && $_POST['name'] != '') $name = $sql->quote_smart($_POST['name']);
        else $name = 0;
    if (isset($_POST['displayid']) && $_POST['displayid'] != '') $displayid = $sql->quote_smart($_POST['displayid']);
        else $displayid = 0;
    if (isset($_POST['Quality']) && $_POST['Quality'] != '') $Quality = $sql->quote_smart($_POST['Quality']);
        else $Quality = 0;
    if (isset($_POST['Flags']) && $_POST['Flags'] != '') $Flags = $sql->quote_smart($_POST['Flags']);
        else $Flags = 0;
    if (isset($_POST['BuyCount']) && $_POST['BuyCount'] != '') $BuyCount = $sql->quote_smart($_POST['BuyCount']);
        else $BuyCount = 0;
    if (isset($_POST['BuyPrice']) && $_POST['BuyPrice'] != '') $BuyPrice = $sql->quote_smart($_POST['BuyPrice']);
        else $BuyPrice = 0;
    if (isset($_POST['SellPrice']) && $_POST['SellPrice'] != '') $SellPrice = $sql->quote_smart($_POST['SellPrice']);
        else $SellPrice = 0;
    if (isset($_POST['InventoryType']) && $_POST['InventoryType'] != '') $InventoryType = $sql->quote_smart($_POST['InventoryType']);
        else $AllowableClass = 0;
    if (isset($_POST['AllowableClass'])) $AllowableClass = $sql->quote_smart($_POST['AllowableClass']);
        else $AllowableClass = -1;
    if (isset($_POST['AllowableRace'])) $AllowableRace = $sql->quote_smart($_POST['AllowableRace']);
        else $AllowableRace = -1;
    if (isset($_POST['ItemLevel']) && $_POST['ItemLevel'] != '') $ItemLevel = $sql->quote_smart($_POST['ItemLevel']);
        else $ItemLevel = 1;
    if (isset($_POST['RequiredLevel']) && $_POST['RequiredLevel'] != '') $RequiredLevel = $sql->quote_smart($_POST['RequiredLevel']);
        else $RequiredLevel = 0;
    if (isset($_POST['RequiredSkill']) && $_POST['RequiredSkill'] != '') $RequiredSkill = $sql->quote_smart($_POST['RequiredSkill']);
        else $RequiredSkill = 0;
    if (isset($_POST['RequiredSkillRank']) && $_POST['RequiredSkillRank'] != '') $RequiredSkillRank = $sql->quote_smart($_POST['RequiredSkillRank']);
        else $RequiredSkillRank = 0;
    if (isset($_POST['requiredspell']) && $_POST['requiredspell'] != '') $requiredspell = $sql->quote_smart($_POST['requiredspell']);
        else $requiredspell = 0;
    if (isset($_POST['requiredhonorrank']) && $_POST['requiredhonorrank'] != '') $requiredhonorrank = $sql->quote_smart($_POST['requiredhonorrank']);
        else $requiredhonorrank = 0;
    if (isset($_POST['RequiredCityRank']) && $_POST['RequiredCityRank'] != '') $RequiredCityRank = $sql->quote_smart($_POST['RequiredCityRank']);
        else $RequiredCityRank = 0;
    if (isset($_POST['RequiredReputationFaction']) && $_POST['RequiredReputationFaction'] != '') $RequiredReputationFaction = $sql->quote_smart($_POST['RequiredReputationFaction']);
        else $RequiredReputationFaction = 0;
    if (isset($_POST['RequiredReputationRank']) && $_POST['RequiredReputationRank'] != '') $RequiredReputationRank = $sql->quote_smart($_POST['RequiredReputationRank']);
        else $RequiredReputationRank = 0;
    if (isset($_POST['maxcount']) && $_POST['maxcount'] != '') $maxcount = $sql->quote_smart($_POST['maxcount']);
        else $maxcount = 0;
    if (isset($_POST['stackable']) && $_POST['stackable'] != '') $stackable = $sql->quote_smart($_POST['stackable']);
        else $description = 0;
    if (isset($_POST['ContainerSlots']) && $_POST['ContainerSlots'] != '') $ContainerSlots = $sql->quote_smart($_POST['ContainerSlots']);
        else $ContainerSlots = 0;
    if (isset($_POST['stat_type1']) && $_POST['stat_type1'] != '') $stat_type1 = $sql->quote_smart($_POST['stat_type1']);
        else $stat_type1 = 0;
    if (isset($_POST['stat_value1']) && $_POST['stat_value1'] != '') $stat_value1 = $sql->quote_smart($_POST['stat_value1']);
        else $stat_value1 = 0;
    if (isset($_POST['stat_type2']) && $_POST['stat_type2'] != '') $stat_type2 = $sql->quote_smart($_POST['stat_type2']);
        else $stat_type2 = 0;
    if (isset($_POST['stat_value2']) && $_POST['stat_value2'] != '') $stat_value2 = $sql->quote_smart($_POST['stat_value2']);
        else $stat_value2 = 0;
    if (isset($_POST['stat_type3']) && $_POST['stat_type3'] != '') $stat_type3 = $sql->quote_smart($_POST['stat_type3']);
        else $stat_type3 = 0;
    if (isset($_POST['stat_value3']) && $_POST['stat_value3'] != '') $stat_value3 = $sql->quote_smart($_POST['stat_value3']);
        else $stat_value3 = 0;
    if (isset($_POST['stat_type4']) && $_POST['stat_type4'] != '') $stat_type4 = $sql->quote_smart($_POST['stat_type4']);
        else $stat_type4 = 0;
    if (isset($_POST['stat_value4']) && $_POST['stat_value4'] != '') $stat_value4 = $sql->quote_smart($_POST['stat_value4']);
        else $stat_value4 = 0;
    if (isset($_POST['stat_type5']) && $_POST['stat_type5'] != '') $stat_type5 = $sql->quote_smart($_POST['stat_type5']);
        else $stat_type5 = 0;
    if (isset($_POST['stat_value5']) && $_POST['stat_value5'] != '') $stat_value5 = $sql->quote_smart($_POST['stat_value5']);
        else $stat_value5 = 0;
    if (isset($_POST['stat_type6']) && $_POST['stat_type6'] != '') $stat_type6 = $sql->quote_smart($_POST['stat_type6']);
        else $stat_type6 = 0;
    if (isset($_POST['stat_value6']) && $_POST['stat_value6'] != '') $stat_value6 = $sql->quote_smart($_POST['stat_value6']);
        else $stat_value6 = 0;
    if (isset($_POST['stat_type7']) && $_POST['stat_type7'] != '') $stat_type7 = $sql->quote_smart($_POST['stat_type7']);
        else $stat_type7 = 0;
    if (isset($_POST['stat_value7']) && $_POST['stat_value7'] != '') $stat_value7 = $sql->quote_smart($_POST['stat_value7']);
        else $stat_value7 = 0;
    if (isset($_POST['stat_type8']) && $_POST['stat_type8'] != '') $stat_type8 = $sql->quote_smart($_POST['stat_type8']);
        else $stat_type8 = 0;
    if (isset($_POST['stat_value8']) && $_POST['stat_value8'] != '') $stat_value8 = $sql->quote_smart($_POST['stat_value8']);
        else $stat_value8 = 0;
    if (isset($_POST['stat_type9']) && $_POST['stat_type9'] != '') $stat_type9 = $sql->quote_smart($_POST['stat_type9']);
        else $stat_type9 = 0;
    if (isset($_POST['stat_value9']) && $_POST['stat_value9'] != '') $stat_value9 = $sql->quote_smart($_POST['stat_value9']);
        else $stat_value9 = 0;
    if (isset($_POST['stat_type10']) && $_POST['stat_type10'] != '') $stat_type10 = $sql->quote_smart($_POST['stat_type10']);
        else $stat_type10 = 0;
    if (isset($_POST['stat_value10']) && $_POST['stat_value10'] != '') $stat_value10 = $sql->quote_smart($_POST['stat_value10']);
        else $stat_value10 = 0;
    if (isset($_POST['dmg_min1']) && $_POST['dmg_min1'] != '') $dmg_min1 = $sql->quote_smart($_POST['dmg_min1']);
        else $dmg_min1 = 0;
    if (isset($_POST['dmg_max1']) && $_POST['dmg_max1'] != '') $dmg_max1 = $sql->quote_smart($_POST['dmg_max1']);
        else $dmg_max1 = 0;
    if (isset($_POST['dmg_type1']) && $_POST['dmg_type1'] != '') $dmg_type1 = $sql->quote_smart($_POST['dmg_type1']);
        else $dmg_type1 = 0;
    if (isset($_POST['dmg_min2']) && $_POST['dmg_min2'] != '') $dmg_min2 = $sql->quote_smart($_POST['dmg_min2']);
        else $dmg_min2 = 0;
    if (isset($_POST['dmg_max2']) && $_POST['dmg_max2'] != '') $dmg_max2 = $sql->quote_smart($_POST['dmg_max2']);
        else $dmg_max2 = 0;
    if (isset($_POST['dmg_type2']) && $_POST['dmg_type2'] != '') $dmg_type2 = $sql->quote_smart($_POST['dmg_type2']);
        else $dmg_type2 = 0;
    if (isset($_POST['armor']) && $_POST['armor'] != '') $armor = $sql->quote_smart($_POST['armor']);
        else $armor = 0;
    if (isset($_POST['holy_res']) && $_POST['holy_res'] != '') $holy_res = $sql->quote_smart($_POST['holy_res']);
        else $holy_res = 0;
    if (isset($_POST['fire_res']) && $_POST['fire_res'] != '') $fire_res = $sql->quote_smart($_POST['fire_res']);
        else $fire_res = 0;
    if (isset($_POST['nature_res']) && $_POST['nature_res'] != '') $nature_res = $sql->quote_smart($_POST['nature_res']);
        else $nature_res = 0;
    if (isset($_POST['frost_res']) && $_POST['frost_res'] != '') $frost_res = $sql->quote_smart($_POST['frost_res']);
        else $frost_res = 0;
    if (isset($_POST['shadow_res']) && $_POST['shadow_res'] != '') $shadow_res = $sql->quote_smart($_POST['shadow_res']);
        else $shadow_res = 0;
    if (isset($_POST['arcane_res']) && $_POST['arcane_res'] != '') $arcane_res = $sql->quote_smart($_POST['arcane_res']);
        else $arcane_res = 0;
    if (isset($_POST['delay']) && $_POST['delay'] != '') $delay = $sql->quote_smart($_POST['delay']);
        else $delay = 0;
    if (isset($_POST['ammo_type']) && $_POST['ammo_type'] != '') $ammo_type = $sql->quote_smart($_POST['ammo_type']);
        else $ammo_type = 0;
    if (isset($_POST['RangedModRange']) && $_POST['RangedModRange'] != '') $RangedModRange = $sql->quote_smart($_POST['RangedModRange']);
        else $RangedModRange = 0;
    if (isset($_POST['spellid_1']) && $_POST['spellid_1'] != '') $spellid_1 = $sql->quote_smart($_POST['spellid_1']);
        else $spellid_1 = 0;
    if (isset($_POST['spelltrigger_1']) && $_POST['spelltrigger_1'] != '') $spelltrigger_1 = $sql->quote_smart($_POST['spelltrigger_1']);
        else $spelltrigger_1 = 0;
    if (isset($_POST['spellcharges_1']) && $_POST['spellcharges_1'] != '') $spellcharges_1 = $sql->quote_smart($_POST['spellcharges_1']);
        else $spellcharges_1 = 0;
    if (isset($_POST['spellcooldown_1']) && $_POST['spellcooldown_1'] != '') $spellcooldown_1 = $sql->quote_smart($_POST['spellcooldown_1']);
        else $spellcooldown_1 = -1;
    if (isset($_POST['spellcategory_1']) && $_POST['spellcategory_1'] != '') $spellcategory_1 = $sql->quote_smart($_POST['spellcategory_1']);
        else $spellcategory_1 = 0;
    if (isset($_POST['spellcategorycooldown_1']) && $_POST['spellcategorycooldown_1'] != '') $spellcategorycooldown_1 = $sql->quote_smart($_POST['spellcategorycooldown_1']);
        else $spellcategorycooldown_1 = -1;
    if (isset($_POST['spellppmRate_1']) && $_POST['spellppmRate_1'] != '') $spellppmRate_1 = $sql->quote_smart($_POST['spellppmRate_1']);
        else $spellppmRate_1 = 0;
    if (isset($_POST['spellid_2']) && $_POST['spellid_2'] != '') $spellid_2 = $sql->quote_smart($_POST['spellid_2']);
        else $spellid_2 = 0;
    if (isset($_POST['spelltrigger_2']) && $_POST['spelltrigger_2'] != '') $spelltrigger_2 = $sql->quote_smart($_POST['spelltrigger_2']);
        else $spelltrigger_2 = 0;
    if (isset($_POST['spellcharges_2']) && $_POST['spellcharges_2'] != '') $spellcharges_2 = $sql->quote_smart($_POST['spellcharges_2']);
        else $spellcharges_2 = 0;
    if (isset($_POST['spellcooldown_2']) && $_POST['spellcooldown_2'] != '') $spellcooldown_2 = $sql->quote_smart($_POST['spellcooldown_2']);
        else $spellcooldown_2 = -1;
    if (isset($_POST['spellcategory_2']) && $_POST['spellcategory_2'] != '') $spellcategory_2 = $sql->quote_smart($_POST['spellcategory_2']);
        else $spellcategory_2 = 0;
    if (isset($_POST['spellcategorycooldown_2']) && $_POST['spellcategorycooldown_2'] != '') $spellcategorycooldown_2 = $sql->quote_smart($_POST['spellcategorycooldown_2']);
        else $spellcategorycooldown_2 = -1;
    if (isset($_POST['spellppmRate_2']) && $_POST['spellppmRate_2'] != '') $spellppmRate_2 = $sql->quote_smart($_POST['spellppmRate_2']);
        else $spellppmRate_2 = 0;
    if (isset($_POST['spellid_3']) && $_POST['spellid_3'] != '') $spellid_3 = $sql->quote_smart($_POST['spellid_3']);
        else $spellid_3 = 0;
    if (isset($_POST['spelltrigger_3']) && $_POST['spelltrigger_3'] != '') $spelltrigger_3 = $sql->quote_smart($_POST['spelltrigger_3']);
        else $spelltrigger_3 = 0;
    if (isset($_POST['spellcharges_3']) && $_POST['spellcharges_3'] != '') $spellcharges_3 = $sql->quote_smart($_POST['spellcharges_3']);
        else $spellcharges_3 = 0;
    if (isset($_POST['spellcooldown_3']) && $_POST['spellcooldown_3'] != '') $spellcooldown_3 = $sql->quote_smart($_POST['spellcooldown_3']);
        else $spellcooldown_3 = -1;
    if (isset($_POST['spellcategory_3']) && $_POST['spellcategory_3'] != '') $spellcategory_3 = $sql->quote_smart($_POST['spellcategory_3']);
        else $description = 0;
    if (isset($_POST['spellcategorycooldown_3']) && $_POST['spellcategorycooldown_3'] != '') $spellcategorycooldown_3 = $sql->quote_smart($_POST['spellcategorycooldown_3']);
        else $spellcategorycooldown_3 = -1;
    if (isset($_POST['spellppmRate_3']) && $_POST['spellppmRate_3'] != '') $spellppmRate_3 = $sql->quote_smart($_POST['spellppmRate_3']);
        else $spellppmRate_3 = 0;
    if (isset($_POST['spellid_4']) && $_POST['spellid_4'] != '') $spellid_4 = $sql->quote_smart($_POST['spellid_4']);
        else $spellid_4 = 0;
    if (isset($_POST['spelltrigger_4']) && $_POST['spelltrigger_4'] != '') $spelltrigger_4 = $sql->quote_smart($_POST['spelltrigger_4']);
        else $spelltrigger_4 = 0;
    if (isset($_POST['spellcharges_4']) && $_POST['spellcharges_4'] != '') $spellcharges_4 = $sql->quote_smart($_POST['spellcharges_4']);
        else $spellcharges_4 = 0;
    if (isset($_POST['spellcooldown_4']) && $_POST['spellcooldown_4'] != '') $spellcooldown_4 = $sql->quote_smart($_POST['spellcooldown_4']);
        else $spellcooldown_4 = -1;
    if (isset($_POST['spellcategory_4']) && $_POST['spellcategory_4'] != '') $spellcategory_4 = $sql->quote_smart($_POST['spellcategory_4']);
        else $spellcategory_4 = 0;
    if (isset($_POST['spellcategorycooldown_4']) && $_POST['spellcategorycooldown_4'] != '') $spellcategorycooldown_4 = $sql->quote_smart($_POST['spellcategorycooldown_4']);
        else $spellcategorycooldown_4 = -1;
    if (isset($_POST['spellppmRate_4']) && $_POST['spellppmRate_4'] != '') $spellppmRate_4 = $sql->quote_smart($_POST['spellppmRate_4']);
        else $spellppmRate_4 = 0;
    if (isset($_POST['spellid_5']) && $_POST['spellid_5'] != '') $spellid_5 = $sql->quote_smart($_POST['spellid_5']);
        else $spellid_5 = 0;
    if (isset($_POST['spelltrigger_5']) && $_POST['spelltrigger_5'] != '') $spelltrigger_5 = $sql->quote_smart($_POST['spelltrigger_5']);
        else $spelltrigger_5 = 0;
    if (isset($_POST['spellcharges_5']) && $_POST['spellcharges_5'] != '') $spellcharges_5 = $sql->quote_smart($_POST['spellcharges_5']);
        else $spellcharges_5 = 0;
    if (isset($_POST['spellcooldown_5']) && $_POST['spellcooldown_5'] != '') $spellcooldown_5 = $sql->quote_smart($_POST['spellcooldown_5']);
        else $spellcooldown_5 = -1;
    if (isset($_POST['spellcategory_5']) && $_POST['spellcategory_5'] != '') $spellcategory_5 = $sql->quote_smart($_POST['spellcategory_5']);
        else $spellcategory_5 = 0;
    if (isset($_POST['spellcategorycooldown_5']) && $_POST['spellcategorycooldown_5'] != '') $spellcategorycooldown_5 = $sql->quote_smart($_POST['spellcategorycooldown_5']);
        else $spellcategorycooldown_5 = -1;
    if (isset($_POST['spellppmRate_5']) && $_POST['spellppmRate_5'] != '') $spellppmRate_5 = $sql->quote_smart($_POST['spellppmRate_5']);
        else $spellppmRate_5 = 0;
    if (isset($_POST['bonding']) && $_POST['bonding'] != '') $bonding = $sql->quote_smart($_POST['bonding']);
        else $bonding = 0;
    if (isset($_POST['description']) && $_POST['description'] != '') $description = $sql->quote_smart($_POST['description']);
        else $description = "";
    if (isset($_POST['PageText']) && $_POST['PageText'] != '') $PageText = $sql->quote_smart($_POST['PageText']);
        else $PageText = 0;
    if (isset($_POST['LanguageID']) && $_POST['LanguageID'] != '') $LanguageID = $sql->quote_smart($_POST['LanguageID']);
        else $LanguageID = 0;
    if (isset($_POST['PageMaterial']) && $_POST['PageMaterial'] != '') $PageMaterial = $sql->quote_smart($_POST['PageMaterial']);
        else $PageMaterial = 0;
    if (isset($_POST['startquest']) && $_POST['startquest'] != '') $startquest = $sql->quote_smart($_POST['startquest']);
        else $startquest = 0;
    if (isset($_POST['lockid']) && $_POST['lockid'] != '') $lockid = $sql->quote_smart($_POST['lockid']);
        else $lockid = 0;
    if (isset($_POST['Material']) && $_POST['Material'] != '') $Material = $sql->quote_smart($_POST['Material']);
        else $Material = 0;
    if (isset($_POST['sheath']) && $_POST['sheath'] != '') $sheath = $sql->quote_smart($_POST['sheath']);
        else $sheath = 0;
    if (isset($_POST['RandomProperty']) && $_POST['RandomProperty'] != '') $RandomProperty = $sql->quote_smart($_POST['RandomProperty']);
        else $RandomProperty = 0;
    if (isset($_POST['block ']) && $_POST['block '] != '') $block = $sql->quote_smart($_POST['block']);
        else $block  = 0;
    if (isset($_POST['itemset']) && $_POST['itemset'] != '') $itemset = $sql->quote_smart($_POST['itemset']);
        else $itemset = 0;
    if (isset($_POST['MaxDurability']) && $_POST['MaxDurability'] != '') $MaxDurability = $sql->quote_smart($_POST['MaxDurability']);
        else $MaxDurability = 0;
    if (isset($_POST['area']) && $_POST['area'] != '') $area = $sql->quote_smart($_POST['area']);
        else $area = 0;
    if (isset($_POST['BagFamily']) && $_POST['BagFamily'] != '') $BagFamily = $sql->quote_smart($_POST['BagFamily']);
        else $BagFamily = 0;
    if (isset($_POST['Map']) && $_POST['Map'] != '') $Map = $sql->quote_smart($_POST['Map']);
        else $Map = 0;
    if (isset($_POST['ScriptName']) && $_POST['ScriptName'] != '') $ScriptName = $sql->quote_smart($_POST['ScriptName']);
        else $ScriptName = 0;
    if (isset($_POST['DisenchantID']) && $_POST['DisenchantID'] != '') $DisenchantID = $sql->quote_smart($_POST['DisenchantID']);
        else $DisenchantID = 0;
    if (isset($_POST['RequiredDisenchantSkill']) && $_POST['RequiredDisenchantSkill'] != '') $RequiredDisenchantSkill = $sql->quote_smart($_POST['RequiredDisenchantSkill']);
        else $RequiredDisenchantSkill = -1;
    if (isset($_POST['unk0']) && $_POST['unk0'] != '') $unk0 = $sql->quote_smart($_POST['unk0']);
        else $unk0 = -1;
    if (isset($_POST['RandomSuffix']) && $_POST['RandomSuffix'] != '') $RandomSuffix = $sql->quote_smart($_POST['RandomSuffix']);
        else $RandomSuffix = 0;
    if (isset($_POST['TotemCategory']) && $_POST['TotemCategory'] != '') $TotemCategory = $sql->quote_smart($_POST['TotemCategory']);
        else $TotemCategory = 0;
    if (isset($_POST['socketColor_1']) && $_POST['socketColor_1'] != '') $socketColor_1 = $sql->quote_smart($_POST['socketColor_1']);
        else $socketColor_1 = 0;
    if (isset($_POST['socketContent_1']) && $_POST['socketContent_1'] != '') $socketContent_1 = $sql->quote_smart($_POST['socketContent_1']);
        else $socketContent_1 = 0;
    if (isset($_POST['socketColor_2']) && $_POST['socketColor_2'] != '') $socketColor_2 = $sql->quote_smart($_POST['socketColor_2']);
        else $socketColor_2 = 0;
    if (isset($_POST['socketContent_2']) && $_POST['socketContent_2'] != '') $socketContent_2 = $sql->quote_smart($_POST['socketContent_2']);
        else $socketContent_2 = 0;
    if (isset($_POST['socketColor_3']) && $_POST['socketColor_3'] != '') $socketColor_3 = $sql->quote_smart($_POST['socketColor_3']);
        else $socketColor_3 = 0;
    if (isset($_POST['socketContent_3']) && $_POST['socketContent_3'] != '') $socketContent_3 = $sql->quote_smart($_POST['socketContent_3']);
        else $socketContent_3 = 0;
    if (isset($_POST['socketBonus']) && $_POST['socketBonus'] != '') $socketBonus = $sql->quote_smart($_POST['socketBonus']);
        else $socketBonus = 0;
    if (isset($_POST['GemProperties']) && $_POST['GemProperties'] != '') $GemProperties = $sql->quote_smart($_POST['GemProperties']);
        else $GemProperties = 0;
    if (isset($_POST['ArmorDamageModifier']) && $_POST['ArmorDamageModifier'] != '') $ArmorDamageModifier = $sql->quote_smart($_POST['ArmorDamageModifier']);
        else $ArmorDamageModifier = 0;

    if (isset($_POST['de_ChanceOrQuestChance']) && $_POST['de_ChanceOrQuestChance'] != '') $de_ChanceOrQuestChance = $sql->quote_smart($_POST['de_ChanceOrQuestChance']);
        else $de_ChanceOrQuestChance = 0;
    if (isset($_POST['de_groupid']) && $_POST['de_groupid'] != '') $de_groupid = $sql->quote_smart($_POST['de_groupid']);
        else $de_groupid = 0;
    if (isset($_POST['de_mincountOrRef']) && $_POST['de_mincountOrRef'] != '') $de_mincountOrRef = $sql->quote_smart($_POST['de_mincountOrRef']);
        else $de_mincountOrRef = 0;
    if (isset($_POST['de_maxcount']) && $_POST['de_maxcount'] != '') $de_maxcount = $sql->quote_smart($_POST['de_maxcount']);
        else $de_maxcount = 0;
    if (isset($_POST['de_lootcondition']) && $_POST['de_lootcondition'] != '') $de_lootcondition = $sql->quote_smart($_POST['de_lootcondition']);
        else $de_lootcondition = 0;
    if (isset($_POST['de_condition_value1']) && $_POST['de_condition_value1'] != '') $de_condition_value1 = $sql->quote_smart($_POST['de_condition_value1']);
        else $de_condition_value1 = 0;
    if (isset($_POST['de_condition_value2']) && $_POST['de_condition_value2'] != '') $de_condition_value2 = $sql->quote_smart($_POST['de_condition_value2']);
        else $de_condition_value2 = 0;
    if (isset($_POST['de_item']) && $_POST['de_item'] != '') $de_item = $sql->quote_smart($_POST['de_item']);
        else $de_item = 0;
    if (isset($_POST['del_de_items']) && $_POST['del_de_items'] != '') $del_de_items = $sql->quote_smart($_POST['del_de_items']);
        else $del_de_items = NULL;


    $tmp = 0;
    if ($AllowableClass[0] != -1)
    {
        for ($t=0; $t<count($AllowableClass); $t++)
        {
            if ($AllowableClass[$t] & 1) $tmp = $tmp +1;
            if ($AllowableClass[$t] & 2) $tmp = $tmp +2;
            if ($AllowableClass[$t] & 4) $tmp = $tmp +4;
            if ($AllowableClass[$t] & 8) $tmp = $tmp +8;
            if ($AllowableClass[$t] & 16) $tmp = $tmp +16;
            if ($AllowableClass[$t] & 32) $tmp = $tmp +32;
            if ($AllowableClass[$t] & 64) $tmp = $tmp +64;
            if ($AllowableClass[$t] & 128) $tmp = $tmp +128;
            if ($AllowableClass[$t] & 256) $tmp = $tmp +256;
            if ($AllowableClass[$t] & 512) $tmp = $tmp +512;
            if ($AllowableClass[$t] & 1024) $tmp = $tmp +1024;
        }
    }
    if ($tmp) $AllowableClass = $tmp;
    else $AllowableClass = -1;

    $tmp = 0;
    if ($AllowableRace[0] != -1)
    {
        for ($t=0; $t<count($AllowableRace); $t++)
        {
            if ($AllowableRace[$t] & 1) $tmp = $tmp +1;
            if ($AllowableRace[$t] & 2) $tmp = $tmp +2;
            if ($AllowableRace[$t] & 4) $tmp = $tmp +4;
            if ($AllowableRace[$t] & 8) $tmp = $tmp +8;
            if ($AllowableRace[$t] & 16) $tmp = $tmp +16;
            if ($AllowableRace[$t] & 32) $tmp = $tmp +32;
            if ($AllowableRace[$t] & 64) $tmp = $tmp +64;
            if ($AllowableRace[$t] & 128) $tmp = $tmp +128;
            if ($AllowableRace[$t] & 256) $tmp = $tmp +256;
            if ($AllowableRace[$t] & 512) $tmp = $tmp +512;
        }
    }
    if ($tmp) $AllowableRace = $tmp;
    else $AllowableRace = -1;

    if ($_POST['type'] == "add_new")
    {
        $sql_query = "INSERT INTO item_template (entry, class, subclass, name,displayid, Quality, Flags, BuyCount, BuyPrice, SellPrice, InventoryType, AllowableClass, AllowableRace, ItemLevel,
                      RequiredLevel, RequiredSkill, RequiredSkillRank, requiredspell, requiredhonorrank, RequiredCityRank, RequiredReputationFaction, RequiredReputationRank, maxcount, stackable, ContainerSlots, stat_type1,
                      stat_value1, stat_type2, stat_value2, stat_type3, stat_value3, stat_type4, stat_value4, stat_type5, stat_value5, stat_type6, stat_value6, stat_type7, stat_value7, stat_type8, stat_value8, stat_type9,
                      stat_value9, stat_type10, stat_value10, dmg_min1, dmg_max1, dmg_type1, dmg_min2, dmg_max2, dmg_type2, armor, holy_res, fire_res, nature_res, frost_res, shadow_res, arcane_res, delay, ammo_type,
                      RangedModRange, spellid_1, spelltrigger_1, spellcharges_1, spellppmRate_1, spellcooldown_1, spellcategory_1, spellcategorycooldown_1,
                      spellid_2, spelltrigger_2, spellcharges_2, spellppmRate_2, spellcooldown_2, spellcategory_2, spellcategorycooldown_2, spellid_3, spelltrigger_3, spellcharges_3, spellppmRate_3, spellcooldown_3, spellcategory_3, spellcategorycooldown_3,
                      spellid_4, spelltrigger_4, spellcharges_4, spellppmRate_4, spellcooldown_4, spellcategory_4, spellcategorycooldown_4, spellid_5, spelltrigger_5, spellcharges_5, spellppmRate_5, spellcooldown_5, spellcategory_5, spellcategorycooldown_5,
                      bonding, description, PageText, LanguageID, PageMaterial, startquest, lockid, Material, sheath, RandomProperty, block, itemset, MaxDurability, area, BagFamily, Map, ScriptName, DisenchantID,RequiredDisenchantSkill,
                      ArmorDamageModifier,unk0,RandomSuffix,TotemCategory, socketColor_1, socketContent_1, socketColor_2, socketContent_2, socketColor_3, socketContent_3, socketBonus, GemProperties)
                      VALUES ('$entry', '$class', '$subclass', '$name','$displayid', '$Quality', '$Flags', '$BuyCount', '$BuyPrice', '$SellPrice', '$InventoryType', '$AllowableClass', '$AllowableRace', '$ItemLevel', '$RequiredLevel',
                      '$RequiredSkill', '$RequiredSkillRank', '$requiredspell', '$requiredhonorrank', '$RequiredCityRank', '$RequiredReputationFaction', '$RequiredReputationRank', '$maxcount', '$stackable', '$ContainerSlots', '$stat_type1',
                      '$stat_value1', '$stat_type2', '$stat_value2', '$stat_type3', '$stat_value3', '$stat_type4', '$stat_value4', '$stat_type5', '$stat_value5', '$stat_type6', '$stat_value6', '$stat_type7', '$stat_value7', '$stat_type8', '$stat_value8',
                      '$stat_type9', '$stat_value9', '$stat_type10', '$stat_value10', '$dmg_min1', '$dmg_max1', '$dmg_type1', '$dmg_min2', '$dmg_max2', '$dmg_type2', '$armor', '$holy_res', '$fire_res', '$nature_res', '$frost_res', '$shadow_res', '$arcane_res', '$delay', '$ammo_type', '$RangedModRange', '$spellid_1', '$spelltrigger_1', '$spellcharges_1', '$spellppmRate_1', '$spellcooldown_1',
                      '$spellcategory_1', '$spellcategorycooldown_1', '$spellid_2', '$spelltrigger_2', '$spellcharges_2', '$spellppmRate_2', '$spellcooldown_2', '$spellcategory_2', '$spellcategorycooldown_2', '$spellid_3', '$spelltrigger_3', '$spellcharges_3', '$spellppmRate_3',
                      '$spellcooldown_3', '$spellcategory_3', '$spellcategorycooldown_3', '$spellid_4', '$spelltrigger_4', '$spellcharges_4', '$spellppmRate_4', '$spellcooldown_4', '$spellcategory_4', '$spellcategorycooldown_4', '$spellid_5', '$spelltrigger_5',
                      '$spellcharges_5', '$spellppmRate_5', '$spellcooldown_5', '$spellcategory_5', '$spellcategorycooldown_5', '$bonding', '$description', '$PageText', '$LanguageID', '$PageMaterial', '$startquest', '$lockid', '$Material', '$sheath', '$RandomProperty', '$block',
                      '$itemset', '$MaxDurability', '$area', '$BagFamily', '$Map', '$ScriptName', '$DisenchantID', '$RequiredDisenchantSkill','$ArmorDamageModifier','$unk0','$RandomSuffix', '$TotemCategory', '$socketColor_1', '$socketContent_1', '$socketColor_2',
                      '$socketContent_2', '$socketColor_3', '$socketContent_3', '$socketBonus', '$GemProperties')";

    }
    elseif ($_POST['type'] == "edit")
    {
        $sql_query = "UPDATE item_template SET  ";

        $result = $sql->query("SELECT `item_template`.`entry`,`class`,`subclass`,`unk0`,IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name,`displayid`,`Quality`,`Flags`,`BuyCount`,`BuyPrice`,`SellPrice`,`InventoryType`,`AllowableClass`,`AllowableRace`,`ItemLevel`,`RequiredLevel`,`RequiredSkill`,`RequiredSkillRank`,`requiredspell`,`requiredhonorrank`,`RequiredCityRank`,`RequiredReputationFaction`,`RequiredReputationRank`,`maxcount`,`stackable`,`ContainerSlots`,`stat_type1`,`stat_value1`,`stat_type2`,`stat_value2`,`stat_type3`,`stat_value3`,`stat_type4`,`stat_value4`,`stat_type5`,`stat_value5`,`stat_type6`,`stat_value6`,`stat_type7`,`stat_value7`,`stat_type8`,`stat_value8`,`stat_type9`,`stat_value9`,`stat_type10`,`stat_value10`,`dmg_min1`,`dmg_max1`,`dmg_type1`,`dmg_min2`,`dmg_max2`,`dmg_type2`,`armor`,`holy_res`,`fire_res`,`nature_res`,`frost_res`,`shadow_res`,`arcane_res`,`delay`,`ammo_type`,`RangedModRange`,`spellid_1`,`spelltrigger_1`,`spellcharges_1`,`spellppmRate_1`,`spellcooldown_1`,`spellcategory_1`,`spellcategorycooldown_1`,`spellid_2`,`spelltrigger_2`,`spellcharges_2`,`spellppmRate_2`,`spellcooldown_2`,`spellcategory_2`,`spellcategorycooldown_2`,`spellid_3`,`spelltrigger_3`,`spellcharges_3`,`spellppmRate_3`,`spellcooldown_3`,`spellcategory_3`,`spellcategorycooldown_3`,`spellid_4`,`spelltrigger_4`,`spellcharges_4`,`spellppmRate_4`,`spellcooldown_4`,`spellcategory_4`,`spellcategorycooldown_4`,`spellid_5`,`spelltrigger_5`,`spellcharges_5`,`spellppmRate_5`,`spellcooldown_5`,`spellcategory_5`,`spellcategorycooldown_5`,`bonding`,`description`,`PageText`,`LanguageID`,`PageMaterial`,`startquest`,`lockid`,`Material`,`sheath`,`RandomProperty`,`RandomSuffix`,`block`,`itemset`,`MaxDurability`,`area`,`Map`,`BagFamily`,`TotemCategory`,`socketColor_1`,`socketContent_1`,`socketColor_2`,`socketContent_2`,`socketColor_3`,`socketContent_3`,`socketBonus`,`GemProperties`,`RequiredDisenchantSkill`,`ArmorDamageModifier`,`ScriptName`,`DisenchantID`,`FoodType`,`minMoneyLoot`,`maxMoneyLoot` FROM item_template LEFT JOIN locales_item ON item_template.entry = locales_item.entry WHERE item_template.entry = '$entry'");
        if ($item_templ = $sql->fetch_assoc($result))
        {
            if ($item_templ['class'] != $class) $sql_query .= "class='$class',";
            if ($item_templ['subclass'] != $subclass) $sql_query .= "subclass='$subclass',";
            if ($item_templ['name'] != $name) $sql_query .= "name='$name',";
            if ($item_templ['displayid'] != $displayid) $sql_query .= "displayid='$displayid',";
            if ($item_templ['Quality'] != $Quality) $sql_query .= "Quality='$Quality',";
            if ($item_templ['Flags'] != $Flags) $sql_query .= "Flags='$Flags',";
            if ($item_templ['BuyCount'] != $BuyCount) $sql_query .= "BuyCount='$BuyCount',";
            if ($item_templ['BuyPrice'] != $BuyPrice) $sql_query .= "BuyPrice='$BuyPrice',";
            if ($item_templ['SellPrice'] != $SellPrice) $sql_query .= "SellPrice='$SellPrice',";
            if ($item_templ['InventoryType'] != $InventoryType) $sql_query .= "InventoryType='$InventoryType',";
            if ($item_templ['AllowableClass'] != $AllowableClass) $sql_query .= "AllowableClass='$AllowableClass',";
            if ($item_templ['AllowableRace'] != $AllowableRace) $sql_query .= "AllowableRace='$AllowableRace',";
            if ($item_templ['ItemLevel'] != $ItemLevel) $sql_query .= "ItemLevel='$ItemLevel',";
            if ($item_templ['RequiredLevel'] != $RequiredLevel) $sql_query .= "RequiredLevel='$RequiredLevel',";
            if ($item_templ['RequiredSkill'] != $RequiredSkill) $sql_query .= "RequiredSkill='$RequiredSkill',";
            if ($item_templ['RequiredSkillRank'] != $RequiredSkillRank) $sql_query .= "RequiredSkillRank='$RequiredSkillRank',";
            if ($item_templ['requiredspell'] != $requiredspell) $sql_query .= "requiredspell='$requiredspell',";
            if ($item_templ['requiredhonorrank'] != $requiredhonorrank) $sql_query .= "requiredhonorrank='$requiredhonorrank',";
            if ($item_templ['RequiredCityRank'] != $RequiredCityRank) $sql_query .= "RequiredCityRank='$RequiredCityRank',";
            if ($item_templ['RequiredReputationFaction'] != $RequiredReputationFaction) $sql_query .= "RequiredReputationFaction='$RequiredReputationFaction',";
            if ($item_templ['RequiredReputationRank'] != $RequiredReputationRank) $sql_query .= "RequiredReputationRank='$RequiredReputationRank',";
            if ($item_templ['maxcount'] != $maxcount) $sql_query .= "maxcount='$maxcount',";
            if ($item_templ['stackable'] != $stackable) $sql_query .= "stackable='$stackable',";
            if ($item_templ['ContainerSlots'] != $ContainerSlots) $sql_query .= "ContainerSlots='$ContainerSlots',";
            if ($item_templ['stat_type1'] != $stat_type1) $sql_query .= "stat_type1='$stat_type1',";
            if ($item_templ['stat_value1'] != $stat_value1) $sql_query .= "stat_value1='$stat_value1',";
            if ($item_templ['stat_type2'] != $stat_type2) $sql_query .= "stat_type2='$stat_type2',";
            if ($item_templ['stat_value2'] != $stat_value2) $sql_query .= "stat_value2='$stat_value2',";
            if ($item_templ['stat_type3'] != $stat_type3) $sql_query .= "stat_type3='$stat_type3',";
            if ($item_templ['stat_value3'] != $stat_value3) $sql_query .= "stat_value3='$stat_value3',";
            if ($item_templ['stat_type4'] != $stat_type4) $sql_query .= "stat_type4='$stat_type4',";
            if ($item_templ['stat_value4'] != $stat_value4) $sql_query .= "stat_value4='$stat_value4',";
            if ($item_templ['stat_type5'] != $stat_type5) $sql_query .= "stat_type5='$stat_type5',";
            if ($item_templ['stat_value5'] != $stat_value5) $sql_query .= "stat_value5='$stat_value5',";
            if ($item_templ['stat_type6'] != $stat_type6) $sql_query .= "stat_type6='$stat_type6',";
            if ($item_templ['stat_value6'] != $stat_value6) $sql_query .= "stat_value6='$stat_value6',";
            if ($item_templ['stat_type7'] != $stat_type7) $sql_query .= "stat_type7='$stat_type7',";
            if ($item_templ['stat_value7'] != $stat_value7) $sql_query .= "stat_value7='$stat_value7',";
            if ($item_templ['stat_type8'] != $stat_type8) $sql_query .= "stat_type8='$stat_type8',";
            if ($item_templ['stat_value8'] != $stat_value8) $sql_query .= "stat_value8='$stat_value8',";
            if ($item_templ['stat_type9'] != $stat_type9) $sql_query .= "stat_type9='$stat_type9',";
            if ($item_templ['stat_value9'] != $stat_value9) $sql_query .= "stat_value9='$stat_value9',";
            if ($item_templ['stat_type10'] != $stat_type10) $sql_query .= "stat_type10='$stat_type10',";
            if ($item_templ['stat_value10'] != $stat_value10) $sql_query .= "stat_value10='$stat_value10',";
            if ($item_templ['dmg_min1'] != $dmg_min1) $sql_query .= "dmg_min1='$dmg_min1',";
            if ($item_templ['dmg_max1'] != $dmg_max1) $sql_query .= "dmg_max1='$dmg_max1',";
            if ($item_templ['dmg_type1'] != $dmg_type1) $sql_query .= "dmg_type1='$dmg_type1',";
            if ($item_templ['dmg_min2'] != $dmg_min2) $sql_query .= "dmg_min2='$dmg_min2',";
            if ($item_templ['dmg_max2'] != $dmg_max2) $sql_query .= "dmg_max2='$dmg_max2',";
            if ($item_templ['dmg_type2'] != $dmg_type2) $sql_query .= "dmg_type2='$dmg_type2',";
            if ($item_templ['armor'] != $armor) $sql_query .= "armor='$armor',";
            if ($item_templ['holy_res'] != $holy_res) $sql_query .= "holy_res='$holy_res',";
            if ($item_templ['fire_res'] != $fire_res) $sql_query .= "fire_res='$fire_res',";
            if ($item_templ['nature_res'] != $nature_res) $sql_query .= "nature_res='$nature_res',";
            if ($item_templ['frost_res'] != $frost_res) $sql_query .= "frost_res='$frost_res',";
            if ($item_templ['shadow_res'] != $shadow_res) $sql_query .= "shadow_res='$shadow_res',";
            if ($item_templ['arcane_res'] != $arcane_res) $sql_query .= "arcane_res='$arcane_res',";
            if ($item_templ['delay'] != $delay) $sql_query .= "delay='$delay',";
            if ($item_templ['ammo_type'] != $ammo_type) $sql_query .= "ammo_type='$ammo_type',";
            if ($item_templ['RangedModRange'] != $RangedModRange) $sql_query .= "RangedModRange='$RangedModRange',";
            if ($item_templ['spellid_1'] != $spellid_1) $sql_query .= "spellid_1='$spellid_1',";
            if ($item_templ['spelltrigger_1'] != $spelltrigger_1) $sql_query .= "spelltrigger_1='$spelltrigger_1',";
            if ($item_templ['spellcharges_1'] != $spellcharges_1) $sql_query .= "spellcharges_1='$spellcharges_1',";
            if ($item_templ['spellppmRate_1'] != $spellppmRate_1) $sql_query .= "spellppmRate_1='$spellppmRate_1',";
            if ($item_templ['spellcooldown_1'] != $spellcooldown_1) $sql_query .= "spellcooldown_1='$spellcooldown_1',";
            if ($item_templ['spellcategory_1'] != $spellcategory_1) $sql_query .= "spellcategory_1='$spellcategory_1',";
            if ($item_templ['spellcategorycooldown_1'] != $spellcategorycooldown_1) $sql_query .= "spellcategorycooldown_1='$spellcategorycooldown_1',";
            if ($item_templ['spellid_2'] != $spellid_2) $sql_query .= "spellid_2='$spellid_2',";
            if ($item_templ['spelltrigger_2'] != $spelltrigger_2) $sql_query .= "spelltrigger_2='$spelltrigger_2',";
            if ($item_templ['spellcharges_2'] != $spellcharges_2) $sql_query .= "spellcharges_2='$spellcharges_2',";
            if ($item_templ['spellppmRate_2'] != $spellppmRate_2) $sql_query .= "spellppmRate_2='$spellppmRate_2',";
            if ($item_templ['spellcooldown_2'] != $spellcooldown_2) $sql_query .= "spellcooldown_2='$spellcooldown_2',";
            if ($item_templ['spellcategory_2'] != $spellcategory_2) $sql_query .= "spellcategory_2='$spellcategory_2',";
            if ($item_templ['spellcategorycooldown_2'] != $spellcategorycooldown_2) $sql_query .= "spellcategorycooldown_2='$spellcategorycooldown_2',";
            if ($item_templ['spellid_3'] != $spellid_3) $sql_query .= "spellid_3='$spellid_3',";
            if ($item_templ['spelltrigger_3'] != $spelltrigger_3) $sql_query .= "spelltrigger_3='$spelltrigger_3',";
            if ($item_templ['spellcharges_3'] != $spellcharges_3) $sql_query .= "spellcharges_3='$spellcharges_3',";
            if ($item_templ['spellppmRate_3'] != $spellppmRate_3) $sql_query .= "spellppmRate_3='$spellppmRate_3',";
            if ($item_templ['spellcooldown_3'] != $spellcooldown_3) $sql_query .= "spellcooldown_3='$spellcooldown_3',";
            if ($item_templ['spellcategory_3'] != $spellcategory_3) $sql_query .= "spellcategory_3='$spellcategory_3',";
            if ($item_templ['spellcategorycooldown_3'] != $spellcategorycooldown_3) $sql_query .= "spellcategorycooldown_3='$spellcategorycooldown_3',";
            if ($item_templ['spellid_4'] != $spellid_4) $sql_query .= "spellid_4='$spellid_4',";
            if ($item_templ['spelltrigger_4'] != $spelltrigger_4) $sql_query .= "spelltrigger_4='$spelltrigger_4',";
            if ($item_templ['spellcharges_4'] != $spellcharges_4) $sql_query .= "spellcharges_4='$spellcharges_4',";
            if ($item_templ['spellppmRate_4'] != $spellppmRate_4) $sql_query .= "spellppmRate_4='$spellppmRate_4',";
            if ($item_templ['spellcooldown_4'] != $spellcooldown_4) $sql_query .= "spellcooldown_4='$spellcooldown_4',";
            if ($item_templ['spellcategory_4'] != $spellcategory_4) $sql_query .= "spellcategory_4='$spellcategory_4',";
            if ($item_templ['spellcategorycooldown_4'] != $spellcategorycooldown_4) $sql_query .= "spellcategorycooldown_4='$spellcategorycooldown_4', ";
            if ($item_templ['spellid_5'] != $spellid_5) $sql_query .= "spellid_5='$spellid_5',";
            if ($item_templ['spelltrigger_5'] != $spelltrigger_5) $sql_query .= "spelltrigger_5='$spelltrigger_5',";
            if ($item_templ['spellcharges_5'] != $spellcharges_5) $sql_query .= "spellcharges_5='$spellcharges_5',";
            if ($item_templ['spellppmRate_5'] != $spellppmRate_5) $sql_query .= "spellppmRate_5='$spellppmRate_5',";
            if ($item_templ['spellcooldown_5'] != $spellcooldown_5) $sql_query .= "spellcooldown_5='$spellcooldown_5',";
            if ($item_templ['spellcategory_5'] != $spellcategory_5) $sql_query .= "spellcategory_5='$spellcategory_5',";
            if ($item_templ['spellcategorycooldown_5'] != $spellcategorycooldown_5) $sql_query .= "spellcategorycooldown_5='$spellcategorycooldown_5',";
            if ($item_templ['bonding'] != $bonding) $sql_query .= "bonding='$bonding',";
            if ($item_templ['description'] != $description) $sql_query .= "description='$description',";
            if ($item_templ['PageText'] != $PageText) $sql_query .= "PageText='$PageText',";
            if ($item_templ['LanguageID'] != $LanguageID) $sql_query .= "LanguageID='$LanguageID',";
            if ($item_templ['PageMaterial'] != $PageMaterial) $sql_query .= "PageMaterial='$PageMaterial',";
            if ($item_templ['startquest'] != $startquest) $sql_query .= "startquest='$startquest',";
            if ($item_templ['lockid'] != $lockid) $sql_query .= "lockid='$lockid',";
            if ($item_templ['Material'] != $Material) $sql_query .= "Material='$Material',";
            if ($item_templ['sheath'] != $sheath) $sql_query .= "sheath='$sheath',";
            if ($item_templ['RandomProperty'] != $RandomProperty) $sql_query .= "RandomProperty='$RandomProperty',";
            if ($item_templ['block'] != $block) $sql_query .= "block='$block',";
            if ($item_templ['itemset'] != $itemset) $sql_query .= "itemset='$itemset',";
            if ($item_templ['MaxDurability'] != $MaxDurability) $sql_query .= "MaxDurability='$MaxDurability',";
            if ($item_templ['area'] != $area) $sql_query .= "area='$area',";
            if ($item_templ['BagFamily'] != $BagFamily) $sql_query .= "BagFamily='$BagFamily',";
            if ($item_templ['Map'] != $Map) $sql_query .= "Map='$Map',";
            if ($item_templ['ScriptName'] != $ScriptName) $sql_query .= "ScriptName='$ScriptName',";
            if ($item_templ['DisenchantID'] != $DisenchantID) $sql_query .= "DisenchantID='$DisenchantID',";
            if ($item_templ['RequiredDisenchantSkill'] != $RequiredDisenchantSkill) $sql_query .= "RequiredDisenchantSkill='$RequiredDisenchantSkill',";
            if ($item_templ['ArmorDamageModifier'] != $ArmorDamageModifier) $sql_query .= "ArmorDamageModifier='$ArmorDamageModifier',";
            if ($item_templ['unk0'] != $unk0) $sql_query .= "unk0='$unk0',";
            if ($item_templ['RandomSuffix'] != $RandomSuffix) $sql_query .= "RandomSuffix='$RandomSuffix',";
            if ($item_templ['TotemCategory'] != $TotemCategory) $sql_query .= "TotemCategory='$TotemCategory',";
            if ($item_templ['socketColor_1'] != $socketColor_1) $sql_query .= "socketColor_1='$socketColor_1',";
            if ($item_templ['socketContent_1'] != $socketContent_1) $sql_query .= "socketContent_1='$socketContent_1',";
            if ($item_templ['socketColor_2'] != $socketColor_2) $sql_query .= "socketColor_2='$socketColor_2',";
            if ($item_templ['socketContent_2'] != $socketContent_2) $sql_query .= "socketContent_2='$socketContent_2',";
            if ($item_templ['socketColor_3'] != $socketColor_3) $sql_query .= "socketColor_3='$socketColor_3',";
            if ($item_templ['socketContent_3'] != $socketContent_3) $sql_query .= "socketContent_3='$socketContent_3',";
            if ($item_templ['socketBonus'] != $socketBonus) $sql_query .= "socketBonus='$socketBonus',";
            if ($item_templ['GemProperties'] != $GemProperties) $sql_query .= "GemProperties='$GemProperties',";

            $sql->free_result($result);
            unset($item_templ);

            if (($sql_query == "UPDATE item_template SET  ")&&(!$de_item)&&(!$del_de_items))
            {
                $sql->close();
                redirect("item.php?action=edit&entry=$entry&error=6");
            }
            else
            {
                if ($sql_query != "UPDATE item_template SET  ")
                {
                    $sql_query[strlen($sql_query)-1] = " ";
                    $sql_query .= " WHERE entry = '$entry';\n";
                }
                else
                    $sql_query = "";
            }

            if ($de_item)
            {
                $sql_query .= "INSERT INTO disenchant_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2)
                                VALUES ($DisenchantID,$de_item,'$de_ChanceOrQuestChance', '$de_groupid' ,$de_mincountOrRef ,$de_maxcount ,$de_lootcondition ,$de_condition_value1 ,$de_condition_value2);\n";
            }

            if ($del_de_items)
            {
                foreach($del_de_items as $item_id)
                    $sql_query .= "DELETE FROM disenchant_loot_template WHERE entry = $DisenchantID AND item = $item_id;\n";
            }
        }
        else
        {
            $sql->close();
            redirect("item.php?error=5");
        }
    }
    else
    {
        $sql->close();
        redirect("item.php?error=5");
    }

    if ( isset($_POST['backup_op']) && ($_POST['backup_op'] == 1) )
    {
        $sql->close();
        Header("Content-type: application/octet-stream");
        Header("Content-Disposition: attachment; filename=itemid_$entry.sql");
        echo $sql_query;
        exit();
    }
    else
    {
        $sql_query = explode(';',$sql_query);
        foreach($sql_query as $tmp_query)
            if(($tmp_query)&&($tmp_query != "\n")) $result = $sql->query($tmp_query);
        $sql->close();
    }

    if ($result) redirect("item.php?action=edit&entry=$entry&error=4");
    else redirect("item.php");
}


//#######################################################################################################
//  DELETE ITEM
//#######################################################################################################
function delete()
{
    global $lang_global, $lang_item_edit, $output, $action_permission, $user_lvl;
    valid_login($action_permission['delete']);

    if(isset($_GET['entry']))
        $entry = $_GET['entry'];
    else
        redirect("item.php?error=1");

    $output .= "
        <center>
            <h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1>
            <br />
            <font class=\"bold\">
                {$lang_item_edit['item_id']}: <a href=\"item.php?action=edit&amp;entry=$entry\" target=\"_blank\">$entry</a>
                {$lang_global['will_be_erased']}
            </font>
            <br /><br />
            <table class=\"hidden\">
                <tr>
                    <td>";
    makebutton($lang_global['yes'], "item.php?action=do_delete&amp;entry=$entry\" type=\"warn",130);
    makebutton($lang_global['no'], "item.php",130);
    $output .= "
                    </td>
                </tr>
            </table>
            <br />
        </center>";
}


//########################################################################################################################
//  DO DELETE ITEM
//########################################################################################################################
function do_delete()
{
    global $world_db, $realm_id, $action_permission, $user_lvl;
    valid_login($action_permission['delete']);

    if(isset($_GET['entry']))
        $entry = $_GET['entry'];
    else
        redirect("item.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $result = $sqlw->query("DELETE FROM item_template WHERE entry = '$entry'");

    redirect("item.php");
}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

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
                <font class=\"error\">{$lang_item_edit['search_results']}</font>
            </h1>";
        break;
    case 3:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_item_edit['add_new_item']}</font>
            </h1>";
        break;
    case 4:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_item_edit['edit_item']}</font>
            </h1>";
        break;
    case 5:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_item_edit['err_adding_item']}</font>
            </h1>";
        break;
    case 6:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_item_edit['err_no_field_updated']}</font>
            </h1>";
        break;
    default: //no error
        $output .= "
            <h1>{$lang_item_edit['search_items']}</h1>";
}
$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "search":
        search();
        break;
    case "do_search":
        do_search();
        break;
    case "add_new":
        add_new();
        break;
    case "do_update":
        do_update();
        break;
    case "edit":
        edit();
        break;
    case "delete":
        delete();
        break;
    case "do_delete":
        do_delete();
        break;
    default:
        search();
}

require_once("footer.php");

?>
