<?php

require_once("header.php");
require_once("scripts/get_lib.php");
valid_login($action_permission['read']);


// return npcflag
function get_npcflag($flag)
{
    global $lang_creature;

    $temp = "";

    if ($flag & 1)
        $temp .= " {$lang_creature['gossip']} ";
    if ($flag & 2)
        $temp .= " {$lang_creature['quest_giver']} ";
    if ($flag & 16)
        $temp .= " {$lang_creature['trainer']} ";
    if ($flag & 128)
        $temp .= " {$lang_creature['vendor']} ";
    if ($flag & 4096)
        $temp .= " {$lang_creature['armorer']} ";
    if ($flag & 8192)
        $temp .= " {$lang_creature['taxi']} ";
    if ($flag & 16384)
        $temp .= " {$lang_creature['spirit_healer']} ";
    if ($flag & 65536)
        $temp .= " {$lang_creature['inn_keeper']} ";
    if ($flag & 131072)
        $temp .= " {$lang_creature['banker']} ";
    if ($flag & 262144)
        $temp .= " {$lang_creature['retitioner']} ";
    if ($flag & 524288)
        $temp .= " {$lang_creature['tabard_vendor']} ";
    if ($flag & 1048576)
        $temp .= " {$lang_creature['battlemaster']} ";
    if ($flag & 2097152)
        $temp .= " {$lang_creature['auctioneer']} ";
    if ($flag & 4194304)
        $temp .= " {$lang_creature['stable_master']} ";
    if ($flag & 268435456)
        $temp .= " {$lang_creature['guard']} ";
    if ($temp != "")
        return $temp;
    else
        return $lang_creature['none'];
}

$creature_type =
    [
    0 => [0,$lang_creature['normal']],
    1 => [1,$lang_creature['elite']],
    2 => [2,$lang_creature['rare_elite']],
    3 => [3,$lang_creature['world_boss']],
    4 => [4,$lang_creature['rare']]
    ];

function makeinfocell($text,$tooltip)
{
    return "<a href=\"#\" onmouseover=\"toolTip('".addslashes($tooltip)."','info_tooltip')\" onmouseout=\"toolTip()\">$text</a>";
}
//########################################################################################################################
//  PRINT  ITEM SEARCH FORM
//########################################################################################################################
function search() {
    global $locales_search_option, $lang_global, $lang_creature, $output, $world_db, $realm_id, $creature_type;

    include_once("./scripts/language_select.php");

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $result = $sql->query("SELECT count(*) FROM creature_template");
    $tot_items = $sql->result($result, 0);
    $sql->close();
    unset($sql);

    $output .= "
                    <center>
                <fieldset class=\"full_frame\">
                    <legend>{$lang_creature['search_template']}</legend><br />
                    <form action=\"creature.php?action=do_search&amp;error=2\" method=\"post\" name=\"form\">
                        <table class=\"hidden\">
                            <tr>
                                <td>{$lang_creature['entry']}:</td>
                                <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"entry\" /></td>
                                <td>{$lang_creature['name']}:</td>
                                <td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"name\" /></td>
                            </tr>
                            <tr>
                                <td>{$lang_creature['level']}:</td>
                                <td><input type=\"text\" size=\"10\" maxlength=\"3\" name=\"level\" /></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>{$lang_creature['faction_A']}:</td>
                                <td><input type=\"text\" size=\"10\" maxlength=\"4\" name=\"faction_A\" /></td>
                                <td>{$lang_creature['faction_H']}:</td>
                                <td><input type=\"text\" size=\"10\" maxlength=\"4\" name=\"faction_H\" /></td>
                            </tr>
                            <tr>
                                <td>{$lang_creature['heroic']}:</td>
                                <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"heroic\" /></td>
                                <td>{$lang_creature['rank']}:</td>
                                <td><select name=\"rank\">
                                        <option value=\"\">- {$lang_creature['select']} -</option>";

    foreach ($creature_type as $flag)
        $output .= "
                                        <option value=\"{$flag[0]}\">{$flag[1]}</option>";

    $output .= "
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>{$lang_creature['type']}:</td>
                                <td>
                                    <select name=\"type\">
                                        <option value=\"\">- {$lang_creature['select']} -</option>
                                        <option value=\"0\">0 - {$lang_creature['other']}</option>
                                        <option value=\"1\">1 - {$lang_creature['beast']}</option>
                                        <option value=\"2\">2 - {$lang_creature['dragonkin']}</option>
                                        <option value=\"3\">3 - {$lang_creature['demon']}</option>
                                        <option value=\"4\">4 - {$lang_creature['elemental']}</option>
                                        <option value=\"5\">5 - {$lang_creature['giant']}</option>
                                        <option value=\"6\">6 - {$lang_creature['undead']}</option>
                                        <option value=\"7\">7 - {$lang_creature['humanoid']}</option>
                                        <option value=\"8\">8 - {$lang_creature['critter']}</option>
                                        <option value=\"9\">9 - {$lang_creature['mechanical']}</option>
                                        <option value=\"10\">10 - {$lang_creature['not_specified']}</option>
                                    </select>
                                </td>
                                    <td>{$lang_creature['npc_flag']}:</td>
                                    <td>
                                        <select name=\"npcflag\">
                                            <option value=\"\">- {$lang_creature['select']} -</option>
                                            <option value=\"1\">{$lang_creature['gossip']}</option>
                                            <option value=\"2\">{$lang_creature['quest_giver']}</option>
                                            <option value=\"16\">{$lang_creature['trainer']}</option>
                                            <option value=\"128\">{$lang_creature['vendor']}</option>
                                            <option value=\"4096\">{$lang_creature['armorer']}</option>
                                            <option value=\"8192\">{$lang_creature['taxi']}</option>
                                            <option value=\"16384\">{$lang_creature['spirit_healer']}</option>
                                            <option value=\"65536\">{$lang_creature['inn_keeper']}</option>
                                            <option value=\"131072\">{$lang_creature['banker']}</option>
                                            <option value=\"262144\">{$lang_creature['retitioner']}</option>
                                            <option value=\"524288\">{$lang_creature['tabard_vendor']}</option>
                                            <option value=\"1048576\">{$lang_creature['battlemaster']}</option>
                                            <option value=\"2097152\">{$lang_creature['auctioneer']}</option>
                                            <option value=\"4194304\">{$lang_creature['stable_master']}</option>
                                            <option value=\"268435456\">{$lang_creature['guard']}</option>
                                        </select>
                                    </td>

                                </tr>
                                <tr>
                                    <td>{$lang_creature['family']}:</td>
                                    <td>
                                        <select name=\"family\">
                                            <option value=\"\">- {$lang_creature['select']} -</option>
                                            <option value=\"0\">0 - {$lang_creature['other']}</option>
                                            <option value=\"1\">1 - {$lang_creature['wolf']}</option>
                                            <option value=\"2\">2 - {$lang_creature['cat']}</option>
                                            <option value=\"3\">3 - {$lang_creature['spider']}</option>
                                            <option value=\"4\">4 - {$lang_creature['bear']}</option>
                                            <option value=\"5\">5 - {$lang_creature['boar']}</option>
                                            <option value=\"6\">6 - {$lang_creature['crocolisk']}</option>
                                            <option value=\"7\">7 - {$lang_creature['carrion_bird']}</option>
                                            <option value=\"8\">8 - {$lang_creature['crab']}</option>
                                            <option value=\"9\">9 - {$lang_creature['gorilla']}</option>
                                            <option value=\"11\">11 - {$lang_creature['raptor']}</option>
                                            <option value=\"12\">12 - {$lang_creature['tallstrider']}</option>
                                            <option value=\"13\">13 - {$lang_creature['other']}</option>
                                            <option value=\"14\">14 - {$lang_creature['other']}</option>
                                            <option value=\"15\">15 - {$lang_creature['felhunter']}</option>
                                            <option value=\"16\">16 - {$lang_creature['voidwalker']}</option>
                                            <option value=\"17\">17 - {$lang_creature['succubus']}</option>
                                            <option value=\"18\">18 - {$lang_creature['other']}</option>
                                            <option value=\"19\">19 - {$lang_creature['doomguard']}</option>
                                            <option value=\"20\">20 - {$lang_creature['scorpid']}</option>
                                            <option value=\"21\">21 - {$lang_creature['turtle']}</option>
                                            <option value=\"22\">22 - {$lang_creature['scorpid']}</option>
                                            <option value=\"23\">23 - {$lang_creature['imp']}</option>
                                            <option value=\"24\">24 - {$lang_creature['bat']}</option>
                                            <option value=\"25\">25 - {$lang_creature['hyena']}</option>
                                            <option value=\"26\">26 - {$lang_creature['owl']}</option>
                                            <option value=\"27\">27 - {$lang_creature['wind_serpent']}</option>
                                        </select>
                                    </td>
                                    <td>{$lang_creature['loot_id']}</td>
                                    <td>
                                        <input type=\"text\" size=\"10\" maxlength=\"10\" name=\"lootid\" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$lang_creature['spell']}:</td>
                                    <td>
                                        <input type=\"text\" size=\"10\" maxlength=\"11\" name=\"spell\" />
                                    </td>
                                    <td>{$lang_creature['script_name']}</td>
                                    <td>
                                        <input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ScriptName\" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$lang_creature['custom_search']}:</td>
                                    <td colspan=\"2\">
                                        <input type=\"text\" size=\"25\" maxlength=\"50\" name=\"custom_search\" />
                                    </td>
                                    <td>&nbsp</td>
                                </tr>
                                <tr>
                                    <td>{$lang_global['language_select']}:</td>
                                    <td>".generate_language_selectbox()."</td>
                                    <td>&nbsp;</td>
                                    <td>";

    makebutton($lang_creature['search'], "javascript:do_submit()",150);
    $output .= "
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=\"4\"><hr></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan=\"2\">";
                                        makebutton($lang_creature['add_new'], "creature.php?action=add_new&error=3",200);
                                        $output .= "
                                    </td>
                                    <td colspan=\"2\">{$lang_creature['tot_creature_templ']}: $tot_items</td>
                                </tr>
                            </table>
                        </form>
                </fieldset>
                <br />
                <br />
                </center>";
}
//########################################################################################################################
// SHOW SEARCH RESULTS
//########################################################################################################################
function do_search() {
    global $lang_global, $lang_creature, $output, $world_db, $realm_id, $creature_datasite, $sql_search_limit,
            $creature_type, $creature_npcflag, $language;
    wowhead_tt();

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $where = '';

    // language // if $_POST['language'] > 0 also search locales_XXX
    // prepare sql_query
    if ($_POST['language'] != '0')
        $loc_language  = (is_numeric($_POST['language']))  ? $sql->quote_smart($_POST['language'])  : redirect("creature.php?error=8");
    else
        $loc_language = '0';

    // check input and prepare sql query

    if ($_POST['npcflag'] != '')
    {
        $npcflag = (is_numeric($_POST['npcflag'])) ? $sql->quote_smart($_POST['npcflag']) : redirect("creature.php?error=8");
        $where .= "ct.npcflag = '$npcflag' ";
    }
    else if ($_POST['type'] != '')
    {
        $type    = (is_numeric($_POST['type']))    ? $sql->quote_smart($_POST['type'])    : redirect("creature.php?error=8");
        $where .= "ct.type = '$type' ";
    }
    else if ($_POST['rank'] != '')
    {
        $rank    = (is_numeric($_POST['rank']))    ? $sql->quote_smart($_POST['rank'])    : redirect("creature.php?error=8");
        $where .= "ct.rank = '$rank' ";
    }
    else if  ($_POST['family'] != '')
    {
        $family  = (is_numeric($_POST['family']))  ? $sql->quote_smart($_POST['family'])  : redirect("creature.php?error=8");
        $where .= "ct.family = '$family' ";
    }
    else if ($_POST['entry'] != '')
    {
        $entry   = (is_numeric($_POST['entry']))   ? $sql->quote_smart($_POST['entry'])   : redirect("creature.php?error=8");
        $where .= "ct.entry = '$entry' ";
    }
    else if ($_POST['name'] != '')
    {
        $name    = (preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['name']))  ?  "test" : $sql->quote_smart($_POST['name']);
        if ($loc_language)
            $where .= "lc.name_loc{$loc_language} LIKE '%$name%' ";
        else
            $where .= "ct.`name`LIKE '%$name%' ";
    }
    else if ($_POST['level'] != '')
    {
        $level   = (is_numeric($_POST['level']))   ? $sql->quote_smart($_POST['level'])   : redirect("creature.php?error=8");
        $where .= "ct.minlevel <= $level AND ct.maxlevel >= $level ";
    }
    else if ($_POST['faction_A'] != '')
    {
        $faction_A = (is_numeric($_POST['faction_A'])) ? $sql->quote_smart($_POST['faction_A']) : redirect("creature.php?error=8");
        $where .= "ct.faction_A = '$faction_A' ";
    }
    else if ($_POST['faction_H'] != '')
    {
        $faction_H = (is_numeric($_POST['faction_H'])) ? $sql->quote_smart($_POST['faction_H']) : redirect("creature.php?error=8");
        $where .= "ct.faction_H = '$faction_H' ";
    }
    else if ($_POST['spell'] != '')
    {
        $spell   = (is_numeric($_POST['spell']))   ? $sql->quote_smart($_POST['spell'])   : redirect("creature.php?error=8");
        $where .= "(ct.spell1 = '$spell' OR ct.spell2 = '$spell' OR ct.spell3 = '$spell' OR ct.spell4 = '$spell') ";
    }
    else if ($_POST['lootid'] != '')
    {
        $lootid  = (is_numeric($_POST['lootid']))  ? $sql->quote_smart($_POST['lootid'])  : redirect("creature.php?error=8");
        $where .= "ct.lootid = '$lootid' ";
    }
    else if ($_POST['ScriptName'] != '')
    {
        $ScriptName = (preg_match("/^[_[:alpha:]]{1,32}$/", $_POST['ScriptName'])) ? $sql->quote_smart($_POST['ScriptName']) : "mob_generic";
        $where .= "ct.ScriptName LIKE '%$ScriptName%' ";
    }
    else if ($_POST['heroic'] != '')
    {
        $heroic  = (is_numeric($_POST['heroic']))  ? $sql->quote_smart($_POST['heroic'])  : redirect("creature.php?error=8");
        $where .= "ct.heroic_entry = '$heroic'";
    }

    // additional search query
    if ($_POST['custom_search'] != '')
    {
        $custom_search  = (preg_match('/^[\t\v\b\f\a\n\r\\\"\?[](){}=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['$custom_search']))  ? 0 : $sql->quote_smart($_POST['$custom_search']);
        $where .= ($where == '') ? "ct.{$custom_search}" : "AND ct.{$custom_search}";
    }

    /* no search value, go home! */
    if ($where == '')
        redirect("creature.php?error=1");

    if ($loc_language)
        $db_query = "SELECT ct.entry, ct.name, ct.maxlevel, ct.rank, ct.npcflag, lc.name_loc{$loc_language} FROM creature_template ct LEFT OUTER JOIN locales_creature lc on lc.entry = ct.entry WHERE {$where} ORDER BY ct.entry LIMIT 100";
    else
        $db_query = "SELECT ct.entry, ct.name, ct.maxlevel, ct.rank, ct.npcflag FROM creature_template ct WHERE {$where} ORDER BY ct.entry LIMIT 100";

    $result = $sql->query($db_query);
    $total_found = $sql->num_rows($result);
    $output .= "
                <center>
                    <table class=\"top_hidden\"></td>
                        <tr>
                            <td>";
                            makebutton($lang_creature['new_search'], "creature.php",160);
                            $output .= "
                            </td>
                            <td align=\"right\">{$lang_creature['tot_found']} : $total_found : {$lang_global['limit']} $sql_search_limit</td>
                        </tr>
                    </table>
                    <table class=\"lined\">
                        <tr>
                            <th>{$lang_creature['entry']}</th>
                            <th>{$lang_creature['name']}</th>
                            <th>{$lang_creature['level']}</th>
                            <th>{$lang_creature['health']}</th>
                            <th>{$lang_creature['rank']}</th>
                            <th>{$lang_creature['npc_flag']}</th>
                        </tr>";

    for ($i=1; $i<=$total_found; $i++)
    {
        $creature = $sql->fetch_row($result);
        $output .= "
                        <tr>
                            <td><a href=\"$creature_datasite$creature[0]\" target=\"_blank\">$creature[0]</a></td>";
        if ($loc_language)
            $output .= "
                            <td><a href=\"creature.php?action=edit&amp;entry=$creature[0]&amp;error=4\">".htmlentities($creature[5])." ( {$creature[1]} )</a></td>";
        else
            $output .= "
                            <td><a href=\"creature.php?action=edit&amp;entry=$creature[0]&amp;error=4\">$creature[1]</a></td>";
        $output .= "
                            <td>$creature[2]</td>
                            <td>".round(get_additional_data($creature[0], 1),0)."</td>
                            <td>{$creature_type[$creature[3]][1]}</td>
                            <td>".get_npcflag($creature[4])."</td>
                        </tr>";
    }
    $output .= "
                    </table>
                </center><br />";
    $sql->close();
    unset($sql);
}

//########################################################################################################################
// EDIT CREATURE FORM
//########################################################################################################################
function do_insert_update($do_insert)
{
    global $lang_global, $lang_creature, $output, $world_db, $realm_id, $creature_datasite,$item_datasite, $quest_datasite, $lang_id_tab, $spell_datasite, $lang_item,$language, $action_permission, $user_lvl, $locales_search_option;

    wowhead_tt();

    require_once("./scripts/get_lib.php");
    require_once 'libs/item_lib.php';

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    // entry only needed on update
    if (!$do_insert)
    {
        if (!isset($_GET['entry']) )
            redirect("creature.php?error=1");
        $entry   = (is_numeric($_GET['entry']))   ? $sql->quote_smart($_GET['entry'])   : redirect("creature.php?error=8");
        $result = $sql->query("SELECT `entry`, `difficulty_entry_1`, `KillCredit1`, `KillCredit2`, `modelid1`, `modelid2`, `modelid3`, `modelid4`, `name`,`subname`, `IconName`, `minlevel`, `maxlevel`, `faction_A`, `faction_H`, `npcflag`, `speed_walk`, `speed_run`, `scale`, `rank`, `mindmg`, `maxdmg`, `dmgschool`, `attackpower`, `baseattacktime`, `rangeattacktime`, `unit_flags`,`dynamicflags`, `family`, `trainer_type`, `trainer_spell`, `trainer_class`,`trainer_race`,`minrangedmg`, `maxrangedmg`, `rangedattackpower`, `type`,`type_flags`,`lootid`, `pickpocketloot`, `skinloot`, `resistance1`, `resistance2`, `resistance3`, `resistance4`, `resistance5`, `resistance6`, `spell1`, `spell2`, `spell3`, `spell4`, `PetSpellDataId`, `mingold`, `maxgold`, `AIName`, `MovementType`, `InhabitType`, `RacialLeader`, `RegenHealth`, `equipment_id`, `mechanic_immune_mask`, `flags_extra`, `ScriptName` FROM creature_template WHERE entry = '$entry'");
    }
    else
    {
        // get new free id
        $result = $sql->query("SELECT max(entry)+1 as newentry from creature_template");
        $entry  = $sql->result($result, 0, 'newentry');
        $result = $sql->query("SELECT $entry as `entry`, 0 as `difficulty_entry_1`, 0 as `KillCredit1`, 0 as `KillCredit2`, 0 as `modelid1`, 0 as `modelid2`, 0 as `modelid3`, 0 as `modelid4`, 'new creature' as `name`,'' as `subname`, '' as `IconName`, 1 as `minlevel`, 1 as `maxlevel`, 0 as `faction_A`, 0 as `faction_H`, 0 as `npcflag`, 1 as `speed`, 1 as `scale`,0 as `rank`, 1 as `mindmg`, 1 as `maxdmg`, 0 as `dmgschool`, 0 as `attackpower`, 2000 as `baseattacktime`, 0 as `rangeattacktime`, 0 as `unit_flags`,0 as `dynamicflags`, 0 as `family`, 0 as `trainer_type`, 0 as `trainer_spell`, 0 as `trainer_class`,0 as `trainer_race`,0 as `minrangedmg`, 0 as `maxrangedmg`, 0 as `rangedattackpower`, 0 as `type`,0 as `type_flags`,0 as `lootid`, 0 as `pickpocketloot`, 0 as `skinloot`, 0 as `resistance1`, 0 as `resistance2`, 0 as `resistance3`, 0 as `resistance4`, 0 as `resistance5`, 0 as `resistance6`, 0 as`spell1`, 0 as`spell2`, 0 as `spell3`, 0 as `spell4`, 0 as `PetSpellDataId`, 100 as `mingold`, 250 as `maxgold`, '' as `AIName`, 0 as `MovementType`, 1 as `InhabitType`, 0 as `RacialLeader`, 1 as `RegenHealth`, 0 as `equipment_id`, 0 as `mechanic_immune_mask`, 0 as `flags_extra`, '' as `ScriptName`");
        // use id for new creature_template
    }

    if ($mob = $sql->fetch_assoc($result))
    {
        $output .= "
                <script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
                <center>
                <br /><br /><br />
                    <form method=\"post\" action=\"creature.php?action=do_update\" name=\"form1\">
                        <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
                        <input type=\"hidden\" name=\"entry\" value=\"$entry\"/>
                        <input type=\"hidden\" name=\"insert\" value=\"$do_insert\"/>
                        <div class=\"jtab-container\" id=\"container\">
                            <ul class=\"jtabs\">
                                <li><a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">{$lang_creature['general']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane3', this)\">{$lang_creature['stats']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane4', this)\">{$lang_creature['models']}</a></li>
                                <li><a href=\"#\" onclick=\"return showPane('pane2', this)\">{$lang_creature['additional']}</a></li>";
        $quest_flag = 0;
        $vendor_flag = 0;
        $trainer_flag = 0;
        if (!$mob['npcflag'])
            $output .= "";
        else
        {
            if ($mob['npcflag'] & 1)
                $output .= ""; //gossip
            if ($mob['npcflag'] & 2)
            {
                $quest_flag = 1;
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane6', this)\">{$lang_creature['quests']}</a></li>";
            }
            if ($mob['npcflag'] & 4)
            {
                $vendor_flag = 1;
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane7', this)\">{$lang_creature['vendor']}</a></li>";
            }
            if ($mob['npcflag'] & 16)
            {
                $trainer_flag = 1;
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane8', this)\">{$lang_creature['trainer']}</a></li>";
            }
        }
            if ($mob['npcflag'] & 128)
            {
                $vendor_flag = 1;
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane7', this)\">{$lang_creature['vendor']}</a></li>";
            }
            if ($mob['npcflag'] & 16384)
            {
                $vendor_flag = 1;
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane7', this)\">{$lang_creature['vendor']}</a></li>";
            }
            if ($mob['lootid'])
            {
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane5', this)\">{$lang_creature['loot']}</a></li>";
            }
            if ($mob['skinloot'])
            {
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane9', this)\">{$lang_creature['skin_loot']}</a></li>";
            }
            if ($mob['pickpocketloot'])
            {
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane10', this)\">{$lang_creature['pickpocket_loot']}</a></li>";
            }
            if ($locales_search_option != 0)
                $output .= "
                                <li><a href=\"#\" onclick=\"return showPane('pane11', this)\">{$lang_creature['locales']}</a></li>";

        $output .= "
                            </ul>
                            <div class=\"jtab-panes\">
                                <div id=\"pane1\">
                                    <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['general']}:</td></tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['entry'],$lang_creature['entry_desc'])."</td>
                                            <td>
                                                <a href=\"$creature_datasite$entry\" target=\"_blank\">$entry</a>
                                            </td>
                                            <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                            <td colspan=\"3\">
                                                <input type=\"text\" name=\"name\" size=\"50\" maxlength=\"100\" value=\"{$mob['name']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"subname\" size=\"25\" maxlength=\"100\" value=\"{$mob['subname']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['script_name'],$lang_creature['script_name_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"ScriptName\" size=\"25\" maxlength=\"128\" value=\"{$mob['ScriptName']}\" />
                                            </td>
                                        </tr>
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['basic_status']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['heroic'],$lang_creature['heroic_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"difficulty_entry_1\" size=\"8\" maxlength=\"3\" value=\"{$mob['difficulty_entry_1']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['min_level'],$lang_creature['min_level_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"minlevel\" size=\"8\" maxlength=\"3\" value=\"{$mob['minlevel']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['max_level'],$lang_creature['max_level_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"maxlevel\" size=\"8\" maxlength=\"3\" value=\"{$mob['maxlevel']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>
                                                &nbsp;
                                            </td>";
        if ($mob['RegenHealth'])
            $RegenHealth = "checked";
        else
            $RegenHealth = "";

        $output .= "
                                            <td>".makeinfocell($lang_creature['RegenHealth'],$lang_creature['RegenHealth'])."</td>
                                            <td>
                                                <input type=\"checkbox\" name=\"RegenHealth\" value=\"1\" $RegenHealth />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['faction_A'],$lang_creature['faction_A_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"faction_A\" size=\"14\" maxlength=\"10\" value=\"{$mob['faction_A']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['faction_H'],$lang_creature['faction_H_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"faction_H\" size=\"14\" maxlength=\"10\" value=\"{$mob['faction_H']}\" />
                                            </td>
                                        </tr>
                                        <tr>";
        $rank = [0 => "", 1 => "", 3 => "", 2 => "", 4 => ""];
        $rank[$mob['rank']] = " selected=\"selected\" ";
        $output .= "
                                            <td >".makeinfocell($lang_creature['rank'],$lang_creature['rank_desc'])."</td>
                                            <td>
                                                <select name=\"rank\">
                                                    <option value=\"0\" {$rank[0]}>0 - {$lang_creature['normal']}</option>
                                                    <option value=\"1\" {$rank[1]}>1 - {$lang_creature['elite']}</option>
                                                    <option value=\"2\" {$rank[2]}>2 - {$lang_creature['rare_elite']}</option>
                                                    <option value=\"3\" {$rank[3]}>3 - {$lang_creature['world_boss']}</option>
                                                    <option value=\"4\" {$rank[4]}>4 - {$lang_creature['rare']}</option>
                                                </select>
                                            </td>";
        unset($rank);
        $type = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "", 10 => ""];
        $type[$mob['type']] = " selected=\"selected\" ";
        $output .= "
                                            <td colspan=\"2\">".makeinfocell($lang_creature['type'],$lang_creature['type_desc'])."</td>
                                            <td colspan=\"2\">
                                                <select name=\"type\">
                                                    <option value=\"0\" {$type[0]}>0 - {$lang_creature['other']}</option>
                                                    <option value=\"1\" {$type[1]}>1 - {$lang_creature['beast']}</option>
                                                    <option value=\"2\" {$type[2]}>2 - {$lang_creature['dragonkin']}</option>
                                                    <option value=\"3\" {$type[3]}>3 - {$lang_creature['demon']}</option>
                                                    <option value=\"4\" {$type[4]}>4 - {$lang_creature['elemental']}</option>
                                                    <option value=\"5\" {$type[5]}>5 - {$lang_creature['giant']}</option>
                                                    <option value=\"6\" {$type[6]}>6 - {$lang_creature['undead']}</option>
                                                    <option value=\"7\" {$type[7]}>7 - {$lang_creature['humanoid']}</option>
                                                    <option value=\"8\" {$type[8]}>8 - {$lang_creature['critter']}</option>
                                                    <option value=\"9\" {$type[9]}>9 - {$lang_creature['mechanical']}</option>
                                                    <option value=\"10\" {$type[10]}>10 - {$lang_creature['not_specified']}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>";
        unset($type);
        $npcflag = [0 => "", 1 => "", 2 => "", 4 => "", 8 => "", 16 => "", 32 => "", 64 => "", 128 => "", 256 => "", 512 => "", 1024 => "", 2048 => "", 4096 => "", 8192 => "", 16384 => "", 65536 => "", 131072 => "", 262144 => "", 524288 => "", 1048576 => "", 2097152 => "", 4194304 => "", 268435456 => ""];
        if($mob['npcflag'] == 0)
            $npcflag[0] = " selected=\"selected\" ";
        else
        {
            if ($mob['npcflag'] & 1)
                $npcflag[1] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 2)
                $npcflag[2] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 4)
                $npcflag[4] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 8)
                $npcflag[8] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 16)
                $npcflag[16] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 32)
                $npcflag[32] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 64)
                $npcflag[64] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 128)
                $npcflag[128] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 256)
                $npcflag[256] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 512)
                $npcflag[512] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 1024)
                $npcflag[1024] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 2048)
                $npcflag[2048] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 4096)
                $npcflag[4096] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 8192)
                $npcflag[8192] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 16384)
                $npcflag[16384] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 65536)
                $npcflag[65536] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 131072)
                $npcflag[131072] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 262144)
                $npcflag[262144] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 524288)
               $npcflag[524288] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 1048576)
                $npcflag[1048576] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 2097152)
                $npcflag[2097152] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 4194304)
                $npcflag[4194304] = " selected=\"selected\" ";
            if ($mob['npcflag'] & 268435456)
                $npcflag[268435456] = " selected=\"selected\" ";
        }

        $output .= "
                                            <td rowspan=\"2\">".makeinfocell($lang_creature['npc_flag'],$lang_creature['npc_flag_desc'])."</td>
                                            <td colspan=\"2\" rowspan=\"2\">
                                                <select multiple=\"multiple\" name=\"npcflag[]\" size=\"3\">
                                                    <option value=\"0\" {$npcflag[0]}>{$lang_creature['none']}</option>
                                                    <option value=\"1\" {$npcflag[1]}>{$lang_creature['gossip']}</option>
                                                    <option value=\"2\" {$npcflag[2]}>{$lang_creature['quest_giver']}</option>
                                                    <option value=\"4\" {$npcflag[4]}>{$lang_creature['vendor']}</option>
                                                    <option value=\"8\" {$npcflag[8]}>{$lang_creature['taxi']}</option>
                                                    <option value=\"16\" {$npcflag[16]}>{$lang_creature['trainer']}</option>
                                                    <option value=\"32\" {$npcflag[32]}>{$lang_creature['spirit_healer']}</option>
                                                    <option value=\"64\" {$npcflag[64]}>{$lang_creature['guard']}</option>
                                                    <option value=\"128\" {$npcflag[128]}>{$lang_creature['inn_keeper']}</option>
                                                    <option value=\"256\" {$npcflag[256]}>{$lang_creature['banker']}</option>
                                                    <option value=\"512\" {$npcflag[512]}>{$lang_creature['retitioner']}</option>
                                                    <option value=\"1024\" {$npcflag[1024]}>{$lang_creature['tabard_vendor']}</option>
                                                    <option value=\"2048\" {$npcflag[2048]}>{$lang_creature['battlemaster']}</option>
                                                    <option value=\"4096\" {$npcflag[4096]}>{$lang_creature['auctioneer']}</option>
                                                    <option value=\"8192\" {$npcflag[8192]}>{$lang_creature['stable_master']}</option>
                                                    <option value=\"16384\" {$npcflag[16384]}>{$lang_creature['armorer']}</option>
                                                </select>
                                            </td>";

        unset($npcflag);
        $trainer_type = [0 => "", 1 => "", 2 => "", 3 => ""];
        $trainer_type[$mob['trainer_type']] = " selected=\"selected\" ";
        $output .= "
                                            <td>".makeinfocell($lang_creature['trainer_type'],$lang_creature['trainer_type_desc'])."</td>
                                            <td colspan=\"2\">
                                                <select name=\"trainer_type\">
                                                    <option value=\"0\" {$trainer_type[0]}>0 - {$lang_creature['class']}</option>
                                                    <option value=\"1\" {$trainer_type[1]}>1 - {$lang_creature['mounts']}</option>
                                                    <option value=\"2\" {$trainer_type[2]}>2 - {$lang_creature['trade_skill']}</option>
                                                    <option value=\"3\" {$trainer_type[3]}>3 - {$lang_creature['pets']}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>";
        unset($trainer_type);
        $family = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "", 11 => "", 12 => "", 13 => "", 14 => "", 15 => "", 16 => "", 17 => "", 18 => "", 19 => "", 20 => "", 21 => "", 22 => "", 23 => "", 24 => "", 25 => "", 26 => "", 27 => ""];
        $family[$mob['family']] = " selected=\"selected\" ";
        $output .= "
                                            <td>".makeinfocell($lang_creature['family'],$lang_creature['family_desc'])."</td>
                                            <td colspan=\"2\">
                                                <select name=\"family\">
                                                    <option value=\"0\" {$family[0]}>0 - {$lang_creature['other']}</option>
                                                    <option value=\"1\" {$family[1]}>1 - {$lang_creature['wolf']}</option>
                                                    <option value=\"2\" {$family[2]}>2 - {$lang_creature['cat']}</option>
                                                    <option value=\"3\" {$family[3]}>3 - {$lang_creature['spider']}</option>
                                                    <option value=\"4\" {$family[4]}>4 - {$lang_creature['bear']}</option>
                                                    <option value=\"5\" {$family[5]}>5 - {$lang_creature['boar']}</option>
                                                    <option value=\"6\" {$family[6]}>6 - {$lang_creature['crocolisk']}</option>
                                                    <option value=\"7\" {$family[7]}>7 - {$lang_creature['carrion_bird']}</option>
                                                    <option value=\"8\" {$family[8]}>8 - {$lang_creature['crab']}</option>
                                                    <option value=\"9\" {$family[9]}>9 - {$lang_creature['gorilla']}</option>
                                                    <option value=\"11\" {$family[11]}>11 - {$lang_creature['raptor']}</option>
                                                    <option value=\"12\" {$family[12]}>12 - {$lang_creature['tallstrider']}</option>
                                                    <option value=\"13\" {$family[13]}>13 - {$lang_creature['other']}</option>
                                                    <option value=\"14\" {$family[14]}>14 - {$lang_creature['other']}</option>
                                                    <option value=\"15\" {$family[15]}>15 - {$lang_creature['felhunter']}</option>
                                                    <option value=\"16\" {$family[16]}>16 - {$lang_creature['voidwalker']}</option>
                                                    <option value=\"17\" {$family[17]}>17 - {$lang_creature['succubus']}</option>
                                                    <option value=\"18\" {$family[18]}>18 - {$lang_creature['other']}</option>
                                                    <option value=\"19\" {$family[19]}>19 - {$lang_creature['doomguard']}</option>
                                                    <option value=\"20\" {$family[20]}>20 - {$lang_creature['scorpid']}</option>
                                                    <option value=\"21\" {$family[21]}>21 - {$lang_creature['turtle']}</option>
                                                    <option value=\"22\" {$family[22]}>22 - {$lang_creature['scorpid']}</option>
                                                    <option value=\"23\" {$family[23]}>23 - {$lang_creature['imp']}</option>
                                                    <option value=\"24\" {$family[24]}>24 - {$lang_creature['bat']}</option>
                                                    <option value=\"25\" {$family[25]}>25 - {$lang_creature['hyena']}</option>
                                                    <option value=\"26\" {$family[26]}>26 - {$lang_creature['owl']}</option>
                                                    <option value=\"27\" {$family[27]}>27 - {$lang_creature['wind_serpent']}</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['loot']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['loot_id'],$lang_creature['loot_id_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"lootid\" size=\"10\" maxlength=\"10\" value=\"{$mob['lootid']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['skin_loot'],$lang_creature['skin_loot_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"skinloot\" size=\"10\" maxlength=\"10\" value=\"{$mob['skinloot']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['pickpocket_loot'],$lang_creature['pickpocket_loot_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"pickpocketloot\" size=\"10\" maxlength=\"10\" value=\"{$mob['pickpocketloot']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['min_gold'],$lang_creature['min_gold_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"mingold\" size=\"14\" maxlength=\"30\" value=\"{$mob['mingold']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['max_gold'],$lang_creature['max_gold_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"maxgold\" size=\"14\" maxlength=\"30\" value=\"{$mob['maxgold']}\" />
                                            </td>
                                        </tr>";
        unset($family);
        $result1 = $sql->query("SELECT COUNT(*) FROM creature WHERE id = '{$mob['entry']}'");
        $output .= "
                                        <tr>
                                            <td colspan=\"6\">{$lang_creature['creature_swapned']} : ".$sql->result($result1, 0)." {$lang_creature['times']}.</td>
                                        </tr>
                                    </table>
                                    <br /><br />
                                </div>
                                <div id=\"pane3\">
                                    <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['basic_status']}:</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td colspan=\"2\">
                                                &nbsp;
                                            </td>
                                            <td>".makeinfocell($lang_creature['speed'],$lang_creature['speed_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"speed_walk\" size=\"8\" maxlength=\"45\" value=\"{$mob['speed_walk']}\" />
                                            </td>
                                        </tr>
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['damage']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['min_damage'],$lang_creature['min_damage_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"mindmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['mindmg']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['max_damage'],$lang_creature['max_damage_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"maxdmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['maxdmg']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['attack_power'],$lang_creature['attack_power_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"attackpower\" size=\"8\" maxlength=\"10\" value=\"{$mob['attackpower']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['min_range_dmg'],$lang_creature['min_range_dmg_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"minrangedmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['minrangedmg']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['max_range_dmg'],$lang_creature['max_range_dmg_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"maxrangedmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['maxrangedmg']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['ranged_attack_power'],$lang_creature['ranged_attack_power_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"rangedattackpower\" size=\"8\" maxlength=\"10\" value=\"{$mob['rangedattackpower']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['attack_time'],$lang_creature['attack_time_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"baseattacktime\" size=\"8\" maxlength=\"4\" value=\"{$mob['baseattacktime']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['range_attack_time'],$lang_creature['range_attack_time_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"rangeattacktime\" size=\"8\" maxlength=\"4\" value=\"{$mob['rangeattacktime']}\" />
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan=\"2\"></td>";

        $dmgschool = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => ""];
        $dmgschool[$mob['dmgschool']] = " selected=\"selected\" ";
        $output .= "
                                            <td>".makeinfocell($lang_creature['dmgschool'],$lang_creature['dmgschool_desc'])."</td>
                                            <td colspan=\"2\">
                                                <select name=\"dmgschool\">
                                                    <option value=\"0\" {$dmgschool[0]}>0: {$lang_item['physical_dmg']}</option>
                                                    <option value=\"1\" {$dmgschool[1]}>1: {$lang_item['holy_dmg']}</option>
                                                    <option value=\"2\" {$dmgschool[2]}>2: {$lang_item['fire_dmg']}</option>
                                                    <option value=\"3\" {$dmgschool[3]}>3: {$lang_item['nature_dmg']}</option>
                                                    <option value=\"4\" {$dmgschool[4]}>4: {$lang_item['frost_dmg']}</option>
                                                    <option value=\"5\" {$dmgschool[5]}>5: {$lang_item['shadow_dmg']}</option>
                                                    <option value=\"6\" {$dmgschool[6]}>6: {$lang_item['arcane_dmg']}</option>
                                                </select>
                                            </td>";
        unset($dmgschool);
        $output .= "
                                        </tr>
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['spells']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['spell']." 1",$lang_creature['spell_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"spell1\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell1']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['spell']." 2",$lang_creature['spell_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"spell2\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell2']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['spell']." 3",$lang_creature['spell_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"spell3\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell3']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['spell']." 4",$lang_creature['spell_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"spell4\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell4']}\" />
                                            </td>
                                        </tr>
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['resistances']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['resis_holy'],$lang_creature['resis_holy_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"resistance1\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance1']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['resis_fire'],$lang_creature['resis_fire_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"resistance2\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance2']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['resis_nature'],$lang_creature['resis_nature_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"resistance3\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance3']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['resis_frost'],$lang_creature['resis_frost_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"resistance4\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance4']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['resis_shadow'],$lang_creature['resis_shadow_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"resistance5\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance5']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['resis_arcane'],$lang_creature['resis_arcane_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"resistance6\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance6']}\" />
                                            </td>
                                        </tr>
                                    </table>
                                    <br /><br />
                                </div>
                                <div id=\"pane4\">
                                    <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['models']}:</td>
                                        </tr>
                                        <tr>
                                            <td colspan=\"2\">".makeinfocell($lang_creature['modelid_A'],$lang_creature['modelid_A_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"modelid1\" size=\"8\" maxlength=\"11\" value=\"{$mob['modelid1']}\" />
                                            </td>
                                            <td colspan=\"2\">".makeinfocell($lang_creature['modelid_A2'],$lang_creature['modelid_A2_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"modelid2\" size=\"8\" maxlength=\"11\" value=\"{$mob['modelid2']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=\"2\">".makeinfocell($lang_creature['modelid_H'],$lang_creature['modelid_H_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"modelid3\" size=\"8\" maxlength=\"11\" value=\"{$mob['modelid3']}\" />
                                            </td>
                                            <td colspan=\"2\">".makeinfocell($lang_creature['modelid_H2'],$lang_creature['modelid_H2_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"modelid4\" size=\"8\" maxlength=\"11\" value=\"{$mob['modelid4']}\" />
                                            </td>
                                        </tr>
                                    </table>
                                    <br /><br />";

        $result1 = $sql->query("SELECT * FROM creature_equip_template WHERE entry = '{$mob['equipment_id']}'");
        if ($mobequip = $sql->fetch_assoc($result1))
        {
            $output .= "
                                    <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['equipment']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['equip_slot']." 1",$lang_creature['equip_slot1_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"equipslot1\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipentry1']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['equip_slot']." 2",$lang_creature['equip_slot2_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"equipslot2\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipentry2']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['equip_slot']." 3",$lang_creature['equip_slot3_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"equipslot3\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipentry3']}\" />
                                            </td>
                                        </tr>
                                    </table>
                                    <br /><br />
                                </div>";
        }
        else
        {
            $output .= "
                                    <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['equipment']}:</td></tr>
                                    </table>
                                    <br /><br />
                                </div>";
        }

        $output .= "
                                <div id=\"pane2\">
                                <br /><br />
                                    <table class=\"lined\" style=\"width: 720px;\">
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['scripts']}:</td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['ai_name'],$lang_creature['ai_name_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"AIName\" size=\"14\" maxlength=\"128\" value=\"{$mob['AIName']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['movement_type'],$lang_creature['movement_type_desc'])."</td>
                                            <td colspan=\"2\">
                                                <input type=\"text\" name=\"MovementType\" size=\"14\" maxlength=\"24\" value=\"{$mob['MovementType']}\" />
                                            </td>
                                        </tr>
                                        <tr class=\"large_bold\">
                                            <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['other']}:</td>
                                        </tr>";
        $trainer_class = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "",5 => "",7 => "",8 => "",9 => "",11 => ""];
        $trainer_class[$mob['trainer_class']] = " selected=\"selected\" ";
        $output .= "
                                            <td>".makeinfocell($lang_creature['class'],$lang_creature['class_desc'])."</td>
                                            <td>
                                                <select name=\"class\">
                                                    <option value=\"0\" {$trainer_class[0]}>0 - {$lang_creature['none']}</option>
                                                    <option value=\"1\" {$trainer_class[1]}>1 - {$lang_id_tab['warrior']}</option>
                                                    <option value=\"2\" {$trainer_class[2]}>2 - {$lang_id_tab['paladin']}</option>
                                                    <option value=\"3\" {$trainer_class[3]}>3 - {$lang_id_tab['hunter']}</option>
                                                    <option value=\"4\" {$trainer_class[4]}>4 - {$lang_id_tab['rogue']}</option>
                                                    <option value=\"5\" {$trainer_class[5]}>5 - {$lang_id_tab['priest']}</option>
                                                    <option value=\"7\" {$trainer_class[7]}>7 - {$lang_id_tab['shaman']}</option>
                                                    <option value=\"8\" {$trainer_class[8]}>8 - {$lang_id_tab['mage']}</option>
                                                    <option value=\"9\" {$trainer_class[9]}>9 - {$lang_id_tab['warlock']}</option>
                                                    <option value=\"11\" {$trainer_class[11]}>11 - {$lang_id_tab['druid']}</option>
                                                </select>
                                            </td>";
        unset($trainer_class);

        $trainer_race = [0 => "", 1 => "", 2 => "", 3 => "", 4 => "",5 => "",6 => "", 7 => "",8 => "",10 => "",11 => ""];
        $trainer_race[$mob['trainer_race']] = " selected=\"selected\" ";
        $output .= "
                                            <td>".makeinfocell($lang_creature['race'],$lang_creature['race_desc'])."</td>
                                            <td>
                                                <select name=\"race\">
                                                    <option value=\"0\" {$trainer_race[0]}>0 - {$lang_creature['none']}</option>
                                                    <option value=\"1\" {$trainer_race[1]}>1 - {$lang_id_tab['human']}</option>
                                                    <option value=\"2\" {$trainer_race[2]}>2 - {$lang_id_tab['orc']}</option>
                                                    <option value=\"3\" {$trainer_race[3]}>3 - {$lang_id_tab['dwarf']}</option>
                                                    <option value=\"4\" {$trainer_race[4]}>4 - {$lang_id_tab['nightelf']}</option>
                                                    <option value=\"5\" {$trainer_race[5]}>5 - {$lang_id_tab['undead']}</option>
                                                    <option value=\"6\" {$trainer_race[6]}>6 - {$lang_id_tab['tauren']}</option>
                                                    <option value=\"7\" {$trainer_race[7]}>7 - {$lang_id_tab['gnome']}</option>
                                                    <option value=\"8\" {$trainer_race[8]}>8 - {$lang_id_tab['troll']}</option>
                                                    <option value=\"10\" {$trainer_race[10]}>10 - {$lang_id_tab['bloodelf']}</option>
                                                    <option value=\"11\" {$trainer_race[11]}>11 - {$lang_id_tab['draenei']}</option>
                                                </select>
                                            </td>";

        if ($mob['RacialLeader'])
            $RacialLeader = "checked";
        else
            $RacialLeader = "";

        $output .= "
                                            <td>".makeinfocell($lang_creature['RacialLeader'],$lang_creature['RacialLeader_desc'])."</td>
                                            <td>
                                                <input type=\"checkbox\" name=\"RacialLeader\" value=\"1\" $RacialLeader />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['trainer_spell'],$lang_creature['trainer_spell_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"trainer_spell\" size=\"14\" maxlength=\"11\" value=\"{$mob['trainer_spell']}\" />
                                            </td>";
        unset($trainer_race);

        $InhabitType = [0 => "", 1 => "", 2 => "", 3 => ""];
        $InhabitType[$mob['InhabitType']] = " selected=\"selected\" ";
        $output .= "
                                            <td>".makeinfocell($lang_creature['inhabit_type'],$lang_creature['inhabit_type_desc'])."</td>
                                            <td>
                                                <select name=\"InhabitType\">
                                                    <option value=\"0\" {$InhabitType[0]}>0 - {$lang_creature['none']}</option>
                                                    <option value=\"1\" {$InhabitType[1]}>1 - {$lang_creature['walk']}</option>
                                                    <option value=\"2\" {$InhabitType[2]}>2 - {$lang_creature['swim']}</option>
                                                    <option value=\"3\" {$InhabitType[3]}>3 - {$lang_creature['both']}</option>
                                                </select>
                                            </td>";
        unset($InhabitType);

        $output .= "
                                            <td>".makeinfocell($lang_creature['flags_extra'],$lang_creature['flags_extra_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"flags_extra\" size=\"8\" maxlength=\"11\" value=\"{$mob['flags_extra']}\" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".makeinfocell($lang_creature['unit_flags'],$lang_creature['flags_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"unit_flags\" size=\"8\" maxlength=\"11\" value=\"{$mob['unit_flags']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['dynamic_flags'],$lang_creature['dynamic_flags_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"dynamicflags\" size=\"8\" maxlength=\"11\" value=\"{$mob['dynamicflags']}\" />
                                            </td>
                                            <td>".makeinfocell($lang_creature['flag_1'],$lang_creature['flag_1_desc'])."</td>
                                            <td>
                                                <input type=\"text\" name=\"type_flags\" size=\"8\" maxlength=\"11\" value=\"{$mob['type_flags']}\" />
                                            </td>
                                        </tr>
                                        </table>
                                        <br /><br />
                                    </div>";

        /*****************
        /  LOCALES
        *****************/
        if ($locales_search_option != 0)
        {

            if ($do_insert)
                $result_loc = $sql->query("SELECT '' as `name_loc1`, '' as `name_loc2`, '' as `name_loc3`, '' as `name_loc4`, '' as `name_loc5`, '' as `name_loc6`, '' as `name_loc7`, '' as `name_loc8`, '' as `subname_loc1`, '' as `subname_loc2`, '' as `subname_loc3`, '' as `subname_loc4`, '' as `subname_loc5`, '' as `subname_loc6`, '' as `subname_loc7`, '' as `subname_loc8`");
            else  // update
                $result_loc = $sql->query("SELECT `name_loc1`, `name_loc2`, `name_loc3`, `name_loc4`, `name_loc5`, `name_loc6`, `name_loc7`, `name_loc8`, `subname_loc1`, `subname_loc2`, `subname_loc3`, `subname_loc4`, `subname_loc5`, `subname_loc6`, `subname_loc7`, `subname_loc8` FROM `locales_creature` WHERE `entry` = '$entry'");
                $loc = $sql->fetch_assoc($result_loc);
                $output .= "
                                    <div id=\"pane11\">
                                        <br /><br />
                                        <table class=\"lined\" style=\"width: 720px;\">
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_1']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc1\" size=\"24\" maxlength=\"128\" value=\"{$loc['name_loc1']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc1\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc1']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_2']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc2\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc2']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc2\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc2']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_3']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc3\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc3']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc3\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc3']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_4']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc4\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc4']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc4\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc4']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_5']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc5\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc5']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc5\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc5']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_6']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc6\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc6']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc6\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc6']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_7']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc7\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc7']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc7\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc7']}\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_global['language_8']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['name'],$lang_creature['name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"name_loc8\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc8']}\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['sub_name'],$lang_creature['sub_name_desc'])."</td>
                                                <td colspan=\"2\">
                                                    <input type=\"text\" name=\"subname_loc8\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc8']}\" />
                                                </td>
                                            </tr>
                                        </table>
                                        <br /><br />
                                    </div>";
        }

        if($mob['lootid'])
        {
            $output .= "
                                    <div id=\"pane5\">
                                        <br /><br />
                                        <table class=\"lined\" style=\"width: 720px;\">
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['loot_tmpl_id']}: {$mob['lootid']}</td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"6\">";
            $cel_counter = 0;
            $row_flag = 0;
            $output .= "
                                                    <table class=\"hidden\" align=\"center\">
                                                        <tr>";
            $result1 = $sql->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount, lootcondition, condition_value1,condition_value2 FROM creature_loot_template WHERE entry = {$mob['lootid']} ORDER BY ChanceOrQuestChance DESC");
            while ($item = $sql->fetch_row($result1))
            {
                $cel_counter++;
                $tooltip = get_item_name($item[0])." ($item[0])<br />{$lang_creature['drop_chance']}: $item[1]%<br />{$lang_creature['quest_drop_chance']}: $item[2]%<br />{$lang_creature['drop_chance']}: $item[3]-$item[4]<br />{$lang_creature['lootcondition']}: $item[5]<br />{$lang_creature['condition_value1']}: $item[6]<br />{$lang_creature['condition_value2']}: $item[7]";
                $output .= "
                                                            <td>";
                $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
                $output .= "
                                                                <br /><input type=\"checkbox\" name=\"del_loot_items[]\" value=\"$item[0]\" />
                                                            </td>";
                if ($cel_counter >= 14)
                {
                    $cel_counter = 0;
                    $output .= "
                                                        </tr>
                                                        <tr>";
                    $row_flag++;
                }
            };

            if ($row_flag)
                $output .= "
                                                            <td colspan=\"".(16 - $cel_counter)."\"></td>";
            $output .= "
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['add_items_to_templ']}:</td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['loot_item_id'],$lang_creature['loot_item_id_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"item\" size=\"8\" maxlength=\"10\" value=\"\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['loot_drop_chance'],$lang_creature['loot_drop_chance_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['loot_quest_drop_chance'],$lang_creature['loot_quest_drop_chance_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"groupid\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['min_count'],$lang_creature['min_count_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['max_count'],$lang_creature['max_count_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>".makeinfocell($lang_creature['lootcondition'],$lang_creature['lootcondition_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['condition_value1'],$lang_creature['condition_value1_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                </td>
                                                <td>".makeinfocell($lang_creature['condition_value2'],$lang_creature['condition_value2_desc'])."</td>
                                                <td>
                                                    <input type=\"text\" name=\"condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                </td>
                                            </tr>
                                        </table>
                                        <br />{$lang_creature['check_to_delete']}<br /><br />
                                    </div>";
        }

        if ($quest_flag)
        {
            $output .= "
                                    <div id=\"pane6\">
                                        <br /><br />
                                        <table class=\"lined\" style=\"width: 720px;\">
                                            <tr class=\"large_bold\">
                                                <td colspan=\"2\" class=\"hidden\" align=\"left\">{$lang_creature['start_quests']}:</td>
                                            </tr>";

            $deplang = get_lang_id();

            $result1 = $sql->query("SELECT quest FROM creature_questrelation WHERE id = {$mob['entry']}");
            while ($quest = $sql->fetch_row($result1))
            {
                $query1 = $sql->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as title FROM quest_template LEFT JOIN locales_quest ON quest_template.entry = locales_quest.entry WHERE quest_template.entry ='$quest[0]'");
                $quest_templ = $sql->fetch_row($query1);
                $output .= "
                                            <tr>
                                                <td width=\"5%\">
                                                    <input type=\"checkbox\" name=\"del_questrelation[]\" value=\"$quest[0]\" />
                                                </td>
                                                <td width=\"95%\" align=\"left\">
                                                    <a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">({$quest_templ[0]}) $quest_templ[1]</a>
                                                </td>
                                            </tr>";
            };

            $output .= "
                                            <tr class=\"large_bold\" align=\"left\">
                                                <td colspan=\"2\" class=\"hidden\">{$lang_creature['add_starts_quests']}:</td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"2\" align=\"left\">".makeinfocell($lang_creature['quest_id'],$lang_creature['quest_id_desc'])." :
                                                    <input type=\"text\" name=\"questrelation\" size=\"8\" maxlength=\"8\" value=\"\" />
                                                </td>
                                            </tr>
                                            <tr class=\"large_bold\">
                                                <td colspan=\"2\" class=\"hidden\" align=\"left\">{$lang_creature['ends_quests']}:</td>
                                            </tr>";

            $result1 = $sql->query("SELECT quest FROM creature_involvedrelation WHERE id = {$mob['entry']}");
            while ($quest = $sql->fetch_row($result1))
            {
                $query1 = $sql->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as title FROM quest_template LEFT JOIN locales_quest ON quest_template.entry = locales_quest.entry WHERE quest_template.entry ='$quest[0]'");
                $quest_templ = $sql->fetch_row($query1);
                $output .= "
                                            <tr>
                                                <td width=\"5%\">
                                                    <input type=\"checkbox\" name=\"del_involvedrelation[]\" value=\"$quest[0]\" />
                                                </td>
                                                <td width=\"95%\" align=\"left\">
                                                    <a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">({$quest_templ[0]}) $quest_templ[1]</a>
                                                </td>
                                            </tr>";
            };

            $output .= "
                                            <tr class=\"large_bold\" align=\"left\">
                                                <td colspan=\"2\" class=\"hidden\">{$lang_creature['add_ends_quests']}:</td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"2\" align=\"left\">".makeinfocell($lang_creature['quest_id'],$lang_creature['quest_id_desc'])." :
                                                    <input type=\"text\" name=\"involvedrelation\" size=\"8\" maxlength=\"8\" value=\"\" />
                                                </td>
                                            </tr>
                                            </table>
                                            <br />{$lang_creature['check_to_delete']}<br /><br />
                                        </div>";
        }

        if ($vendor_flag)
        {
            $output .= "
                                        <div id=\"pane7\">
                                            <br /><br />
                                            <table class=\"lined\" style=\"width: 720px;\">
                                                <tr class=\"large_bold\">
                                                    <td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_creature['sells']}:</td>
                                                </tr>
                                                <tr>
                                                </tr>
                                                    <td colspan=\"8\">";
            $cel_counter = 0;
            $row_flag = 0;
            $output .= "
                                                        <table class=\"hidden\" align=\"center\">
                                                            <tr>";
            $result1 = $sql->query("SELECT item, maxcount, incrtime, ExtendedCost FROM npc_vendor WHERE entry = {$mob['entry']}");
            while ($item = $sql->fetch_row($result1))
            {
                $cel_counter++;

                if (!$item[1])
                    $count = "{$lang_creature['unlimited']}";
                else
                    $count = $item[1];

                $tooltip = get_item_name($item[0])."<br />{$lang_creature['count']} : $count<br />{$lang_creature['vendor_incrtime']} : $item[2]";
                $output .= "
                                                                <td>";
                $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
                $output .= "
                                                                    <br />
                                                                    <input type=\"checkbox\" name=\"del_vendor_item[]\" value=\"$item[0]\" />
                                                                </td>";
                if ($cel_counter >= 14)
                {
                    $cel_counter = 0;
                    $output .= "
                                                            </tr>
                                                            <tr>";
                    $row_flag++;
                }
            };

            if ($row_flag)
                $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";

            $output .= "
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">{$lang_creature['add_items_to_vendor']}:</td></tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['vendor_item_id'],$lang_creature['vendor_item_id_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"vendor_item\" size=\"8\" maxlength=\"10\" value=\"\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['vendor_max_count'],$lang_creature['vendor_max_count_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"vendor_maxcount\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['vendor_incrtime'],$lang_creature['vendor_incrtime_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"vendor_incrtime\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['vendor_extended_cost'],$lang_creature['vendor_extended_cost_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"vendor_extended_cost\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                </tr>
                                            </table><br />{$lang_creature['check_to_delete']}<br /><br />
                                        </div>";
        }

        if ($trainer_flag)
        {
            $output .= "
                                        <div id=\"pane8\">
                                            <br /><br />
                                            <table class=\"lined\" style=\"width: 720px;\">
                                                <tr class=\"large_bold\">
                                                    <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['trains']}:</td>
                                                </tr>
                                                <tr>
                                                    <td colspan=\"6\">";
            $cel_counter = 0;
            $row_flag = 0;
            $output .= "
                                                        <table class=\"hidden\" align=\"center\">
                                                            <tr>";
            $result1 = $sql->query("SELECT spell, spellcost, reqskill, reqskillvalue, reqlevel FROM npc_trainer WHERE entry = {$mob['entry']} ORDER BY reqlevel");
            while ($spell = $sql->fetch_row($result1))
            {
                $cel_counter++;
                $tooltip = "{$lang_creature['spell_id']} : $spell[0]<br />{$lang_creature['cost']} :  $spell[1](c)<br />{$lang_creature['req_skill']} : $spell[2]<br />{$lang_creature['req_skill_lvl']} :  $spell[3]<br />{$lang_creature['req_level']} $spell[4]";
                $output .= "
                                                                <td>";
                $output .= maketooltip($spell[0], "$spell_datasite$spell[0]", $tooltip, "info_tooltip");
                $output .= "
                                                                    <br /><input type=\"checkbox\" name=\"del_trainer_spell[]\" value=\"$spell[0]\" />
                                                                </td>";
                if ($cel_counter >= 16)
                {
                    $cel_counter = 0;
                    $output .= "
                                                            </tr>
                                                            <tr>";
                    $row_flag++;
                }
            };

            if ($row_flag)
                $output .= "
                                                                <td colspan=\"".(16 - $cel_counter)."\"></td>";
            $output .= "
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['add_spell_to_trainer']}:</td></tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['train_spell_id'],$lang_creature['train_spell_id_desc'])."</td>
                                                    <td colspan=\"3\">
                                                        <input type=\"text\" name=\"trainer_spell\" size=\"40\" maxlength=\"10\" value=\"\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['train_cost'],$lang_creature['train_cost_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"spellcost\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['req_skill'],$lang_creature['req_skill_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"reqskill\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['req_skill_value'],$lang_creature['req_skill_value_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"reqskillvalue\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['req_level'],$lang_creature['req_level_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"reqlevel\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                </tr>
                                            </table>
                                            <br />{$lang_creature['check_to_delete']}<br /><br />
                                        </div>";
        }

        if ($mob['skinloot'])
        {
            $output .= "
                                        <div id=\"pane9\">
                                            <br /><br />
                                            <table class=\"lined\" style=\"width: 720px;\">
                                                <tr class=\"large_bold\">
                                                    <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['skinning_loot_tmpl_id']}: {$mob['skinloot']}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan=\"6\">";
            $cel_counter = 0;
            $row_flag = 0;
            $output .= "
                                                        <table class=\"hidden\" align=\"center\">
                                                            <tr>";
            $result1 = $sql->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount, lootcondition, condition_value1, condition_value2 FROM skinning_loot_template WHERE entry = {$mob['skinloot']} ORDER BY ChanceOrQuestChance DESC");
            while ($item = $sql->fetch_row($result1))
            {
                $cel_counter++;
                $tooltip = get_item_name($item[0])." ($item[0])<br />{$lang_creature['drop_chance']}: $item[1]%<br />{$lang_creature['quest_drop_chance']}: $item[2]%<br />{$lang_creature['drop_chance']}: $item[3]-$item[4]<br />{$lang_creature['lootcondition']}: $item[5]<br />{$lang_creature['condition_value1']}: $item[6]<br />{$lang_creature['condition_value2']}: $item[7]";
                $output .= "
                                                                <td>";
                $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
                $output .= "
                                                                    <br /><input type=\"checkbox\" name=\"del_skin_items[]\" value=\"$item[0]\" />
                                                                </td>";

                if ($cel_counter >= 16)
                {
                    $cel_counter = 0;
                    $output .= "
                                                            </tr>
                                                            <tr>";
                    $row_flag++;
                }
            };

            if ($row_flag)
                $output .= "
                                                                <td colspan=\"".(16 - $cel_counter)."\"></td>";
            $output .= "
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['add_items_to_templ']}:</td></tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['loot_item_id'],$lang_creature['loot_item_id_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_item\" size=\"8\" maxlength=\"10\" value=\"\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['loot_drop_chance'],$lang_creature['loot_drop_chance_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['loot_quest_drop_chance'],$lang_creature['loot_quest_drop_chance_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_groupid\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['min_count'],$lang_creature['min_count_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['max_count'],$lang_creature['max_count_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['lootcondition'],$lang_creature['lootcondition_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['condition_value1'],$lang_creature['condition_value1_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['condition_value2'],$lang_creature['condition_value2_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"skin_condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                </tr>
                                            </table>
                                            <br />{$lang_creature['check_to_delete']}<br /><br />
                                        </div>";
        }

        if ($mob['pickpocketloot'])
        {
            $output .= "
                                        <div id=\"pane10\">
                                            <br /><br />
                                            <table class=\"lined\" style=\"width: 720px;\">
                                                <tr class=\"large_bold\">
                                                    <td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['pickpocketloot_tmpl_id']}: {$mob['pickpocketloot']}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan=\"6\">";
            $cel_counter = 0;
            $row_flag = 0;
            $output .= "
                                                        <table class=\"hidden\" align=\"center\">
                                                            <tr>";
            $result1 = $sql->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount, lootcondition, condition_value1, condition_value2 FROM pickpocketing_loot_template WHERE entry = {$mob['pickpocketloot']} ORDER BY ChanceOrQuestChance DESC");
            while ($item = $sql->fetch_row($result1))
            {
                $cel_counter++;
                $tooltip = get_item_name($item[0])." ($item[0])<br />{$lang_creature['drop_chance']}: $item[1]%<br />{$lang_creature['quest_drop_chance']}: $item[2]%<br />{$lang_creature['drop_chance']}: $item[3]-$item[4]<br />{$lang_creature['lootcondition']}: $item[5]<br />{$lang_creature['condition_value1']}: $item[6]<br />{$lang_creature['condition_value2']}: $item[7]";
                $output .= "
                                                                <td>";
                $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
                $output .= "
                                                                    <br /><input type=\"checkbox\" name=\"del_pp_items[]\" value=\"$item[0]\" />
                                                                </td>";

                if ($cel_counter >= 16)
                {
                    $cel_counter = 0;
                    $output .= "
                                                            </tr>
                                                            <tr>";
                    $row_flag++;
                }
            };

            if ($row_flag)
                $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";

            $output .= "
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">{$lang_creature['add_items_to_templ']}:</td></tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['loot_item_id'],$lang_creature['loot_item_id_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_item\" size=\"8\" maxlength=\"10\" value=\"\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['loot_drop_chance'],$lang_creature['loot_drop_chance_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['loot_quest_drop_chance'],$lang_creature['loot_quest_drop_chance_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_groupid\" size=\"8\" maxlength=\"10\" value=\"0\" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['min_count'],$lang_creature['min_count_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['max_count'],$lang_creature['max_count_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>".makeinfocell($lang_creature['lootcondition'],$lang_creature['lootcondition_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['condition_value1'],$lang_creature['condition_value1_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                    <td>".makeinfocell($lang_creature['condition_value2'],$lang_creature['condition_value2_desc'])."</td>
                                                    <td>
                                                        <input type=\"text\" name=\"pp_condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" />
                                                    </td>
                                                </tr>
                                            </table>
                                            <br />{$lang_creature['check_to_delete']}<br /><br />
                                        </div>";
        }

        $output .= "
                                    </div>
                                    </div>
                                    <br />
                                </form>
                                <script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";
        if($do_insert)
        {
            if ($user_lvl >= $action_permission['insert'] && $do_insert)
                makebutton($lang_creature['save_to_db'], "javascript:do_submit('form1',0)",180);
        }
        else
        {
            if ($user_lvl >= $action_permission['insert'])
                makebutton($lang_creature['save_to_db'], "javascript:do_submit('form1',0)",180);
            if ($user_lvl >= $action_permission['delete'])
                makebutton($lang_creature['del_creature'], "creature.php?action=delete&amp;entry=$entry",180);
            if ($user_lvl >= $action_permission['delete'])
                makebutton($lang_creature['del_spawns'], "creature.php?action=delete_spwn&amp;entry=$entry",180);
        }

        // scripts/export should be okay without permission check
        makebutton($lang_creature['save_to_script'], "javascript:do_submit('form1',1)",180);
        $output .= "
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>";
        makebutton($lang_creature['lookup_creature'], "creature.php",760);
        $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </center>";

        $sql->close();
        unset($sql);
    }
    else
    {
        $sql->close();
        unset($sql);
        error($lang_creature['item_not_found']);
        exit();
    }
}

//########################################################################################################################
//DO UPDATE CREATURE TEMPLATE
//########################################################################################################################
function do_update() {
    global $world_db, $realm_id, $action_permission, $user_lvl, $locales_search_option ;

    // on update, use replace.. and else insert
    if ($_POST['insert'] == "1") {
        if ($user_lvl < $action_permission['insert'])
            redirect("creature.php?error=9");
        $db_action_creature = "INSERT";
    }
    else
    {
        if ($user_lvl < $action_permission['update'])
            redirect("creature.php?error=9");
        $db_action_creature = "REPLACE";
    }
    //($del_trainer_spell || $del_loot_items || $del_skin_items || $del_pp_items || $del_questrelation || $del_involvedrelation || $del_vendor_item ) &&
    if ( $user_lvl < $action_permission['delete'] )
        redirect("creature.php?error=9");
    $deplang = get_lang_id();

    if (!isset($_POST['entry']) || $_POST['entry'] === '')
        redirect("creature.php?error=1");

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
    $entry = $sql->quote_smart($_POST['entry']);

    if (isset($_POST['difficulty_entry_1']) && $_POST['difficulty_entry_1'] != '')
        $difficulty_entry_1 = $sql->quote_smart($_POST['difficulty_entry_1']);
    else
        $difficulty_entry_1 = 0;

    if (isset($_POST['modelid1']) && $_POST['modelid2'] != '')
        $modelid1 = $sql->quote_smart($_POST['modelid1']);
    else
        $modelid1 = 0;

    if (isset($_POST['modelid3']) && $_POST['modelid3'] != '')
        $modelid3 = $sql->quote_smart($_POST['modelid3']);
    else
        $modelid3 = 0;

    if (isset($_POST['name']) && $_POST['name'] != '')
        $name = $sql->quote_smart($_POST['name']);
    else
        $name = "";

    if (isset($_POST['subname']) && $_POST['subname'] != '')
        $subname = $sql->quote_smart($_POST['subname']);
    else
        $subname = "";

    if (isset($_POST['minlevel']) && $_POST['minlevel'] != '')
        $minlevel = $sql->quote_smart($_POST['minlevel']);
    else
        $minlevel = 0;

    if (isset($_POST['maxlevel']) && $_POST['maxlevel'] != '')
        $maxlevel = $sql->quote_smart($_POST['maxlevel']);
    else
        $maxlevel = 0;

    if (isset($_POST['faction_A']) && $_POST['faction_A'] != '')
        $faction_A = $sql->quote_smart($_POST['faction_A']);
    else
        $faction_A = 0;

    if (isset($_POST['faction_H']) && $_POST['faction_H'] != '')
        $faction_H = $sql->quote_smart($_POST['faction_H']);
    else
        $faction_H = 0;

    if (isset($_POST['npcflag']))
        $npcflag = $sql->quote_smart($_POST['npcflag']);
    else
        $npcflag = 0;

    if (isset($_POST['speed_walk']) && $_POST['speed_walk'] != '')
        $speed_walk = $sql->quote_smart($_POST['speed_walk']);
    else
        $speed_walk = 0;

    if (isset($_POST['rank']) && $_POST['rank'] != '')
        $rank = $sql->quote_smart($_POST['rank']);
    else
        $rank = 0;

    if (isset($_POST['mindmg']) && $_POST['mindmg'] != '')
        $mindmg = $sql->quote_smart($_POST['mindmg']);
    else
        $mindmg = 0;

    if (isset($_POST['maxdmg']) && $_POST['maxdmg'] != '')
        $maxdmg = $sql->quote_smart($_POST['maxdmg']);
    else
        $maxdmg = 0;

    if (isset($_POST['dmgschool']) && $_POST['dmgschool'] != '')
        $dmgschool = $sql->quote_smart($_POST['dmgschool']);
    else
        $dmgschool = 0;

    if (isset($_POST['attackpower']) && $_POST['attackpower'] != '')
        $attackpower = $sql->quote_smart($_POST['attackpower']);
    else
        $attackpower = 0;

    if (isset($_POST['baseattacktime']) && $_POST['baseattacktime'] != '')
        $baseattacktime = $sql->quote_smart($_POST['baseattacktime']);
    else
        $baseattacktime = 0;

    if (isset($_POST['rangeattacktime']) && $_POST['rangeattacktime'] != '')
        $rangeattacktime = $sql->quote_smart($_POST['rangeattacktime']);
    else
        $rangeattacktime = 0;

    if (isset($_POST['unit_flags']) && $_POST['unit_flags'] != '')
        $unit_flags = $sql->quote_smart($_POST['unit_flags']);
    else
        $unit_flags = 0;

    if (isset($_POST['dynamicflags']) && $_POST['dynamicflags'] != '')
        $dynamicflags = $sql->quote_smart($_POST['dynamicflags']);
    else
        $dynamicflags = 0;

    if (isset($_POST['family']) && $_POST['family'] != '')
        $family = $sql->quote_smart($_POST['family']);
    else
        $family = 0;

    if (isset($_POST['trainer_type']) && $_POST['trainer_type'] != '')
        $trainer_type = $sql->quote_smart($_POST['trainer_type']);
    else
        $trainer_type = 0;

    if (isset($_POST['trainer_spell']) && $_POST['trainer_spell'] != '')
        $trainer_spell = $sql->quote_smart($_POST['trainer_spell']);
    else
        $trainer_spell = 0;

    if (isset($_POST['trainer_class']) && $_POST['trainer_class'] != '')
        $trainer_class = $sql->quote_smart($_POST['trainer_class']);
    else
        $trainer_class = 0;

    if (isset($_POST['trainer_race']) && $_POST['trainer_race'] != '')
        $trainer_race = $sql->quote_smart($_POST['trainer_race']);
    else
        $trainer_race = 0;

    if (isset($_POST['minrangedmg']) && $_POST['minrangedmg'] != '')
        $minrangedmg = $sql->quote_smart($_POST['minrangedmg']);
    else
        $minrangedmg = 0;

    if (isset($_POST['maxrangedmg']) && $_POST['maxrangedmg'] != '')
        $maxrangedmg = $sql->quote_smart($_POST['maxrangedmg']);
    else
        $maxrangedmg = 0;

    if (isset($_POST['rangedattackpower']) && $_POST['rangedattackpower'] != '')
        $rangedattackpower = $sql->quote_smart($_POST['rangedattackpower']);
    else
        $rangedattackpower = 0;

    if (isset($_POST['combat_reach']) && $_POST['combat_reach'] != '')
        $combat_reach = $sql->quote_smart($_POST['combat_reach']);
    else
        $combat_reach = 0;

    if (isset($_POST['type']) && $_POST['type'] != '')
        $type = $sql->quote_smart($_POST['type']);
    else
        $type = 0;

    if (isset($_POST['flags_extra']) && $_POST['flags_extra'] != '')
        $flags_extra = $sql->quote_smart($_POST['flags_extra']);
    else
        $flags_extra = 0;

    if (isset($_POST['type_flags']) && $_POST['type_flags'] != '')
        $type_flags = $sql->quote_smart($_POST['type_flags']);
    else
        $type_flags = 0;

    if (isset($_POST['lootid']) && $_POST['lootid'] != '')
        $lootid = $sql->quote_smart($_POST['lootid']);
    else
        $lootid = 0;

    if (isset($_POST['pickpocketloot']) && $_POST['pickpocketloot'] != '')
        $pickpocketloot = $sql->quote_smart($_POST['pickpocketloot']);
    else
        $pickpocketloot = 0;

    if (isset($_POST['skinloot']) && $_POST['skinloot'] != '')
        $skinloot = $sql->quote_smart($_POST['skinloot']);
    else
        $skinloot = 0;

    if (isset($_POST['resistance1']) && $_POST['resistance1'] != '')
        $resistance1 = $sql->quote_smart($_POST['resistance1']);
    else
        $resistance1 = 0;

    if (isset($_POST['resistance2']) && $_POST['resistance2'] != '')
        $resistance2 = $sql->quote_smart($_POST['resistance2']);
    else
        $resistance2 = 0;

    if (isset($_POST['resistance3']) && $_POST['resistance3'] != '')
        $resistance3 = $sql->quote_smart($_POST['resistance3']);
    else
        $resistance3 = 0;

    if (isset($_POST['resistance4']) && $_POST['resistance4'] != '')
        $resistance4 = $sql->quote_smart($_POST['resistance4']);
    else
        $resistance4 = 0;

    if (isset($_POST['resistance5']) && $_POST['resistance5'] != '')
        $resistance5 = $sql->quote_smart($_POST['resistance5']);
    else
        $resistance5 = 0;

    if (isset($_POST['resistance6']) && $_POST['resistance6'] != '')
        $resistance6 = $sql->quote_smart($_POST['resistance6']);
    else
        $resistance6 = 0;

    if (isset($_POST['spell1']) && $_POST['spell1'] != '')
        $spell1 = $sql->quote_smart($_POST['spell1']);
    else
        $spell1 = 0;

    if (isset($_POST['spell2']) && $_POST['spell2'] != '')
        $spell2 = $sql->quote_smart($_POST['spell2']);
    else
        $spell2 = 0;

    if (isset($_POST['spell3']) && $_POST['spell3'] != '')
        $spell3 = $sql->quote_smart($_POST['spell3']);
    else
        $spell3 = 0;

    if (isset($_POST['spell4']) && $_POST['spell4'] != '')
        $spell4 = $sql->quote_smart($_POST['spell4']);
    else
        $spell4 = 0;

    if (isset($_POST['mingold']) && $_POST['mingold'] != '')
        $mingold = $sql->quote_smart($_POST['mingold']);
    else
        $mingold = 0;

    if (isset($_POST['maxgold']) && $_POST['maxgold'] != '')
        $maxgold = $sql->quote_smart($_POST['maxgold']);
    else
        $maxgold = 0;

    if (isset($_POST['AIName']) && $_POST['AIName'] != '')
        $AIName = $sql->quote_smart($_POST['AIName']);
    else
        $AIName = "";

    if (isset($_POST['MovementType']) && $_POST['MovementType'] != '')
        $MovementType = $sql->quote_smart($_POST['MovementType']);
    else
        $MovementType = 0;

    if (isset($_POST['InhabitType']) && $_POST['InhabitType'] != '')
        $InhabitType = $sql->quote_smart($_POST['InhabitType']);
    else
        $InhabitType = 0;

    if (isset($_POST['ScriptName']) && $_POST['ScriptName'] != '')
        $ScriptName = $sql->quote_smart($_POST['ScriptName']);
    else
        $ScriptName = "";

    if (isset($_POST['RacialLeader']) && $_POST['RacialLeader'] != '')
        $RacialLeader = $sql->quote_smart($_POST['RacialLeader']);
    else
        $RacialLeader = 0;

    if (isset($_POST['ChanceOrQuestChance']) && $_POST['ChanceOrQuestChance'] != '')
        $ChanceOrQuestChance = $sql->quote_smart($_POST['ChanceOrQuestChance']);
    else
        $ChanceOrQuestChance = 0;

    if (isset($_POST['groupid']) && $_POST['groupid'] != '')
        $groupid = $sql->quote_smart($_POST['groupid']);
    else
        $groupid = 0;

    if (isset($_POST['mincountOrRef']) && $_POST['mincountOrRef'] != '')
        $mincountOrRef = $sql->quote_smart($_POST['mincountOrRef']);
    else
        $mincountOrRef = 0;

    if (isset($_POST['maxcount']) && $_POST['maxcount'] != '')
        $maxcount = $sql->quote_smart($_POST['maxcount']);
    else
        $maxcount = 0;

    if (isset($_POST['lootcondition']) && $_POST['lootcondition'] != '')
        $lootcondition = $sql->quote_smart($_POST['lootcondition']);
    else
        $lootcondition = 0;

    if (isset($_POST['condition_value1']) && $_POST['condition_value1'] != '')
        $condition_value1 = $sql->quote_smart($_POST['condition_value1']);
    else
        $condition_value1 = 0;

    if (isset($_POST['condition_value2']) && $_POST['condition_value2'] != '')
        $condition_value2 = $sql->quote_smart($_POST['condition_value2']);
    else
        $condition_value2 = 0;

    if (isset($_POST['item']) && $_POST['item'] != '')
        $item = $sql->quote_smart($_POST['item']);
    else
        $item = 0;

    if (isset($_POST['del_loot_items']) && $_POST['del_loot_items'] != '')
        $del_loot_items = $sql->quote_smart($_POST['del_loot_items']);
    else
        $del_loot_items = NULL;

    if (isset($_POST['involvedrelation']) && $_POST['involvedrelation'] != '')
        $involvedrelation = $sql->quote_smart($_POST['involvedrelation']);
    else
        $involvedrelation = 0;

    if (isset($_POST['del_involvedrelation']) && $_POST['del_involvedrelation'] != '')
        $del_involvedrelation = $sql->quote_smart($_POST['del_involvedrelation']);
    else
        $del_involvedrelation = NULL;

    if (isset($_POST['questrelation']) && $_POST['questrelation'] != '')
        $questrelation = $sql->quote_smart($_POST['questrelation']);
    else
        $questrelation = 0;

    if (isset($_POST['del_questrelation']) && $_POST['del_questrelation'] != '')
        $del_questrelation = $sql->quote_smart($_POST['del_questrelation']);
    else
        $del_questrelation = NULL;

    if (isset($_POST['del_vendor_item']) && $_POST['del_vendor_item'] != '')
        $del_vendor_item = $sql->quote_smart($_POST['del_vendor_item']);
    else
        $del_vendor_item = NULL;

    if (isset($_POST['vendor_item']) && $_POST['vendor_item'] != '')
        $vendor_item = $sql->quote_smart($_POST['vendor_item']);
    else
        $vendor_item = 0;

    if (isset($_POST['vendor_maxcount']) && $_POST['vendor_maxcount'] != '')
        $vendor_maxcount = $sql->quote_smart($_POST['vendor_maxcount']);
    else
        $vendor_maxcount = 0;

    if (isset($_POST['vendor_incrtime']) && $_POST['vendor_incrtime'] != '')
        $vendor_incrtime = $sql->quote_smart($_POST['vendor_incrtime']);
    else
    $vendor_incrtime = 0;

    if (isset($_POST['vendor_extended_cost']) && $_POST['vendor_extended_cost'] != '')
        $vendor_extended_cost = $sql->quote_smart($_POST['vendor_extended_cost']);
    else
        $vendor_extended_cost = 0;

    if (isset($_POST['skin_ChanceOrQuestChance']) && $_POST['skin_ChanceOrQuestChance'] != '')
        $skin_ChanceOrQuestChance = $sql->quote_smart($_POST['skin_ChanceOrQuestChance']);
    else
        $skin_ChanceOrQuestChance = 0;

    if (isset($_POST['skin_groupid']) && $_POST['skin_groupid'] != '')
        $skin_groupid = $sql->quote_smart($_POST['skin_groupid']);
    else
        $skin_groupid = 0;

    if (isset($_POST['skin_mincountOrRef']) && $_POST['skin_mincountOrRef'] != '')
        $skin_mincountOrRef = $sql->quote_smart($_POST['skin_mincountOrRef']);
    else
        $skin_mincountOrRef = 0;

    if (isset($_POST['skin_maxcount']) && $_POST['skin_maxcount'] != '')
        $skin_maxcount = $sql->quote_smart($_POST['skin_maxcount']);
    else
        $skin_maxcount = 0;

    if (isset($_POST['skin_lootcondition']) && $_POST['skin_lootcondition'] != '')
        $skin_lootcondition = $sql->quote_smart($_POST['skin_lootcondition']);
    else
        $skin_lootcondition = 0;

    if (isset($_POST['skin_condition_value1']) && $_POST['skin_condition_value1'] != '')
        $skin_condition_value1 = $sql->quote_smart($_POST['skin_condition_value1']);
    else
        $skin_condition_value1 = 0;

    if (isset($_POST['skin_condition_value2']) && $_POST['skin_condition_value2'] != '')
        $skin_condition_value2 = $sql->quote_smart($_POST['skin_condition_value2']);
    else
        $skin_condition_value2 = 0;

    if (isset($_POST['skin_item']) && $_POST['skin_item'] != '')
        $skin_item = $sql->quote_smart($_POST['skin_item']);
    else
        $skin_item = 0;

    if (isset($_POST['del_skin_items']) && $_POST['del_skin_items'] != '')
        $del_skin_items = $sql->quote_smart($_POST['del_skin_items']);
    else
        $del_skin_items = NULL;

    if (isset($_POST['pp_ChanceOrQuestChance']) && $_POST['pp_ChanceOrQuestChance'] != '')
        $pp_ChanceOrQuestChance = $sql->quote_smart($_POST['pp_ChanceOrQuestChance']);
    else
        $pp_ChanceOrQuestChance = 0;

    if (isset($_POST['pp_groupid']) && $_POST['pp_groupid'] != '')
        $pp_groupid = $sql->quote_smart($_POST['pp_groupid']);
    else
        $pp_groupid = 0;

    if (isset($_POST['pp_mincountOrRef']) && $_POST['pp_mincountOrRef'] != '')
        $pp_mincountOrRef = $sql->quote_smart($_POST['pp_mincountOrRef']);
    else
        $pp_mincountOrRef = 0;

    if (isset($_POST['pp_maxcount']) && $_POST['pp_maxcount'] != '')
        $pp_maxcount = $sql->quote_smart($_POST['pp_maxcount']);
    else
        $pp_maxcount = 0;

    if (isset($_POST['pp_lootcondition']) && $_POST['pp_lootcondition'] != '')
        $pp_lootcondition = $sql->quote_smart($_POST['pp_lootcondition']);
    else
        $pp_lootcondition = 0;

    if (isset($_POST['pp_condition_value1']) && $_POST['pp_condition_value1'] != '')
        $pp_condition_value1 = $sql->quote_smart($_POST['pp_condition_value1']);
    else
        $pp_condition_value1 = 0;

    if (isset($_POST['pp_condition_value2']) && $_POST['pp_condition_value2'] != '')
        $pp_condition_value2 = $sql->quote_smart($_POST['pp_condition_value2']);
    else
        $pp_condition_value2 = 0;

    if (isset($_POST['pp_item']) && $_POST['pp_item'] != '')
        $pp_item = $sql->quote_smart($_POST['pp_item']);
    else
        $pp_item = 0;

    if (isset($_POST['del_pp_items']) && $_POST['del_pp_items'] != '')
        $del_pp_items = $sql->quote_smart($_POST['del_pp_items']);
    else
        $del_pp_items = NULL;

    if (isset($_POST['trainer_spell']) && $_POST['trainer_spell'] != '')
        $trainer_spell = $sql->quote_smart($_POST['trainer_spell']);
    else
        $trainer_spell = 0;

    if (isset($_POST['spellcost']) && $_POST['spellcost'] != '')
        $spellcost = $sql->quote_smart($_POST['spellcost']);
    else
        $spellcost = 0;

    if (isset($_POST['reqskill']) && $_POST['reqskill'] != '')
        $reqskill = $sql->quote_smart($_POST['reqskill']);
    else
        $reqskill = 0;

    if (isset($_POST['reqskillvalue']) && $_POST['reqskillvalue'] != '')
        $reqskillvalue = $sql->quote_smart($_POST['reqskillvalue']);
    else
        $reqskillvalue = 0;

    if (isset($_POST['reqlevel']) && $_POST['reqlevel'] != '')
        $reqlevel = $sql->quote_smart($_POST['reqlevel']);
    else
        $reqlevel = 0;

    if (isset($_POST['del_trainer_spell']) && $_POST['del_trainer_spell'] != '')
        $del_trainer_spell = $sql->quote_smart($_POST['del_trainer_spell']);
    else
        $del_trainer_spell = NULL;

    if ($locales_search_option != 0)
    {
        // locales
        for ($lc = 1; $lc<9; $lc++)
        {
            if (isset($_POST['name_loc'.$lc]) && $_POST['name_loc'.$lc] != '' && !preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['name_loc'.$lc]))
            {
                $name_loc[$lc] = $sql->quote_smart($_POST['name_loc'.$lc]);
            }
            else
                $name_loc[$lc] = '';
            if (isset($_POST['subname_loc'.$lc]) && $_POST['subname_loc'.$lc] != '' && !preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['subname_loc'.$lc]))
            {
                $subname_loc[$lc] = $sql->quote_smart($_POST['subname_loc'.$lc]);
            }
            else
                $subname_loc[$lc] = '';
        }
    }
    $tmp = 0;
    for ($t = 0; $t < count($npcflag); $t++)
    {
        if ($npcflag[$t] & 1)
            $tmp = $tmp + 1;
        if ($npcflag[$t] & 2)
            $tmp = $tmp + 2;
        if ($npcflag[$t] & 16)
            $tmp = $tmp + 16;
        if ($npcflag[$t] & 128)
            $tmp = $tmp + 128;
        if ($npcflag[$t] & 4096)
            $tmp = $tmp + 4096;
        if ($npcflag[$t] & 8192)
            $tmp = $tmp + 8192;
        if ($npcflag[$t] & 16384)
            $tmp = $tmp + 16384;
        if ($npcflag[$t] & 65536)
            $tmp = $tmp + 65536;
        if ($npcflag[$t] & 131072)
            $tmp = $tmp + 131072;
        if ($npcflag[$t] & 262144)
            $tmp = $tmp + 262144;
        if ($npcflag[$t] & 524288)
            $tmp = $tmp + 524288;
        if ($npcflag[$t] & 1048576)
            $tmp = $tmp + 1048576;
        if ($npcflag[$t] & 2097152)
            $tmp = $tmp + 2097152;
        if ($npcflag[$t] & 4194304)
            $tmp = $tmp + 4194304;
        if ($npcflag[$t] & 268435456)
            $tmp = $tmp + 268435456;
    }
    $npcflag = ($tmp) ? $tmp : 0;
    // insert or update creature
    $sql_query = "{$db_action_creature} INTO creature_template ( entry, difficulty_entry_1, modelid1, modelid3, name, subname, minlevel, maxlevel, faction_A, faction_H, npcflag, speed_walk, rank, mindmg, maxdmg, dmgschool, attackpower, baseattacktime, rangeattacktime, unit_flags, dynamicflags, family, trainer_type, trainer_spell, trainer_class, trainer_race, minrangedmg, maxrangedmg, rangedattackpower, type, flags_extra, type_flags, lootid, pickpocketloot, skinloot, resistance1, resistance2, resistance3, resistance4, resistance5, resistance6, spell1, spell2, spell3, spell4, mingold, maxgold, AIName, MovementType, InhabitType, RacialLeader, ScriptName) VALUES ( '$entry', '$difficulty_entry_1', '$modelid1', '$modelid3', '$name', '$subname', '$minlevel', '$maxlevel', '$faction_A', '$faction_A',  '$npcflag', '$speed_walk', '$rank', '$mindmg', '$maxdmg', '$dmgschool', '$attackpower', '$baseattacktime', '$rangeattacktime', '$unit_flags', '$dynamicflags', '$family', '$trainer_type', '$trainer_spell', '$trainer_class', '$trainer_race', '$minrangedmg', '$maxrangedmg', '$rangedattackpower', '$type', '$flags_extra', '$type_flags', '$lootid', '$pickpocketloot', '$skinloot', '$resistance1', '$resistance2', '$resistance3', '$resistance4', '$resistance5', '$resistance6', '$spell1', '$spell2', '$spell3', '$spell4', '$mingold', '$maxgold', '$AIName', '$MovementType', '$InhabitType', '$RacialLeader', '$ScriptName' );\n";

    if ($trainer_spell)
        $sql_query .= "{$db_action_creature} INTO npc_trainer (entry, spell, spellcost, reqskill, reqskillvalue, reqlevel) VALUES ($entry,$trainer_spell,$spellcost,$reqskill ,$reqskillvalue ,$reqlevel);\n";

    if ($del_trainer_spell)
        foreach($del_trainer_spell as $spell_id)
            $sql_query .= "DELETE FROM npc_trainer WHERE entry = $entry AND spell = $spell_id;\n";

    if ($item)
        $sql_query .= "{$db_action_creature} INTO creature_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2) VALUES ($lootid,$item,'$ChanceOrQuestChance', '$groupid' ,$mincountOrRef ,$maxcount ,$lootcondition ,$condition_value1 ,$condition_value2);\n";

    if ($del_loot_items)
        foreach($del_loot_items as $item_id)
            $sql_query .= "DELETE FROM creature_loot_template WHERE entry = $lootid AND item = $item_id;\n";

    if ($skin_item)
        $sql_query .= "{$db_action_creature} INTO skinning_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2) VALUES ($skinloot,$skin_item,'$skin_ChanceOrQuestChance', '$skin_groupid' ,$skin_mincountOrRef ,$skin_maxcount ,$skin_lootcondition ,$skin_condition_value1 ,$skin_condition_value2);\n";

    if ($del_skin_items)
        foreach($del_skin_items as $item_id)
            $sql_query .= "DELETE FROM skinning_loot_template WHERE entry = $skinloot AND item = $item_id;\n";

    if ($pp_item)
        $sql_query .= "{$db_action_creature} INTO pickpocketing_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2) VALUES ($pickpocketloot,$pp_item,'$pp_ChanceOrQuestChance', '$pp_groupid' ,$pp_mincountOrRef ,$pp_maxcount ,$pp_lootcondition ,$pp_condition_value1 ,$pp_condition_value2);\n";

    if ($del_pp_items)
        foreach($del_pp_items as $item_id)
            $sql_query .= "DELETE FROM pickpocketing_loot_template WHERE entry = $pickpocketloot AND item = $item_id;\n";

    if ($questrelation)
        $sql_query .= "{$db_action_creature} INTO creature_questrelation (id, quest) VALUES ($entry,$questrelation);\n";

    if ($involvedrelation)
        $sql_query .= "{$db_action_creature} INTO creature_involvedrelation (id, quest) VALUES ($entry,$involvedrelation);\n";

    if ($del_questrelation)
        foreach($del_questrelation as $quest_id)
            $sql_query .= "DELETE FROM creature_questrelation WHERE id = $entry AND quest = $quest_id;\n";

    if ($del_involvedrelation)
        foreach($del_involvedrelation as $quest_id)
            $sql_query .= "DELETE FROM creature_involvedrelation WHERE id = $entry AND quest = $quest_id;\n";

    if ($del_vendor_item)
        foreach($del_vendor_item as $item_id)
            $sql_query .= "DELETE FROM npc_vendor WHERE entry = $entry AND item = $item_id;\n";

    if ($vendor_item)
        $sql_query .= "{$db_action_creature} INTO npc_vendor (entry, item, maxcount, incrtime, ExtendedCost) VALUES ($entry,$vendor_item,$vendor_maxcount,$vendor_incrtime,$vendor_extended_cost);\n";

    if ($locales_search_option != 0)
        if ($name_loc)
            $sql_query .= "{$db_action_creature} INTO locales_creature (`entry`,  `name_loc1`, `name_loc2`, `name_loc3`, `name_loc4`, `name_loc5`, `name_loc6`, `name_loc7`, `name_loc8`, `subname_loc1`, `subname_loc2`, `subname_loc3`, `subname_loc4`, `subname_loc5`, `subname_loc6`, `subname_loc7`, `subname_loc8`) VALUES ('$entry', '$name_loc[1]', '$name_loc[2]', '$name_loc[3]', '$name_loc[4]', '$name_loc[5]', '$name_loc[6]', '$name_loc[7]', '$name_loc[8]', '$subname_loc[1]', '$subname_loc[2]', '$subname_loc[3]', '$subname_loc[4]', '$subname_loc[5]', '$subname_loc[6]', '$subname_loc[7]', '$subname_loc[8]');\n";

    if ( isset($_POST['backup_op']) && ($_POST['backup_op'] == 1) )
    {
        $sql->close();
        Header("Content-type: application/octet-stream");
        Header("Content-Disposition: attachment; filename=creatureid_$entry.sql");
        echo $sql_query;
        exit();
        redirect("creature.php?action=edit&entry=$entry&error=4");
    }
    else
    {
        $sql_query = explode(';',$sql_query);
        foreach($sql_query as $tmp_query) if(($tmp_query)&&($tmp_query != "\n")) $result = $sql->query($tmp_query);
        $sql->close();
    }
    if ($result)
        redirect("creature.php?action=edit&entry=$entry&error=4");
    else
        redirect("creature.php");
}

//#######################################################################################################
//  DELETE CREATURE TEMPLATE
//#######################################################################################################
function delete() {
    global $lang_global, $lang_creature, $output, $user_lvl, $action_permission;

    if ($user_lvl < $action_permission['delete'] )
        redirect("creature.php?error=9");

    if(isset($_GET['entry']))
        $entry = $_GET['entry'];
    else
        redirect("creature.php?error=1");

    $output .= "
                <center>
                    <h1>
                        <font class=\"error\">{$lang_global['are_you_sure']}</font>
                    </h1>
                    <br />
                    <font class=\"bold\">
                        {$lang_creature['creature_template']}: <a href=\"creature.php?action=edit&amp;entry=$entry\" target=\"_blank\">$entry</a>
                        {$lang_global['will_be_erased']}<br />{$lang_creature['all_related_data']}
                    </font>
                    <br /><br />
                    <table class=\"hidden\">
                        <tr>
                            <td>";
                                makebutton($lang_global['yes'], "creature.php?action=do_delete&amp;entry=$entry",120);
                                makebutton($lang_global['no'], "creature.php",120);
                                $output .= "
                            </td>
                        </tr>
                    </table>
                </center>
                <br />";
}

//########################################################################################################################
//  DO DELETE CREATURE TEMPLATE
//########################################################################################################################
function do_delete()
{
    global $world_db, $realm_id, $user_lvl, $action_permission;

    if ($user_lvl < $action_permission['delete'] )
        redirect("creature.php?error=9");

    if(isset($_GET['entry']))
        $entry = $_GET['entry'];
    else
        redirect("creature.php?error=1");

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $result = $sql->query("SELECT guid FROM creature WHERE id = '$entry'");
    while ($guid = $sql->fetch_row($result))
        $sql->query("DELETE FROM creature_movement WHERE id = '$guid'");

    $sql->query("DELETE FROM creature WHERE id = '$entry'");
    $sql->query("DELETE FROM creature_template WHERE entry = '$entry'");
    $sql->query("DELETE FROM creature_onkill_reputation WHERE creature_id = '$entry'");
    $sql->query("DELETE FROM creature_involvedrelation WHERE id = '$entry'");
    $sql->query("DELETE FROM creature_questrelation WHERE id = '$entry'");
    $sql->query("DELETE FROM npc_vendor WHERE entry = '$entry'");
    $sql->query("DELETE FROM npc_trainer WHERE entry = '$entry'");
    $sql->query("DELETE FROM npc_gossip WHERE npc_guid = '$entry'");
    $sql->close();
    redirect("creature.php");
}

//########################################################################################################################
//   DELETE ALL CREATURE SPAWNS
//########################################################################################################################
function delete_spwn()
{
    global $world_db, $realm_id, $user_lvl, $action_permission;

    if ($user_lvl < $action_permission['delete'] )
        redirect("creature.php?error=9");

    if(isset($_GET['entry']))
        $entry = $_GET['entry'];
    else
        redirect("creature.php?error=1");

    $sql = new SQL;
    $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $result = $sql->query("SELECT guid FROM creature WHERE id = '$entry'");
    while ($guid = $sql->fetch_row($result))
        $sql->query("DELETE FROM creature_movement WHERE id = '$guid'");

    $sql->query("DELETE FROM creature WHERE id = '$entry'");
    $sql->close();
    redirect("creature.php?action=edit&entry=$entry&error=4");
}
//########################################################################################################################
// MAIN
//########################################################################################################################
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
                    <font class=\"error\">{$lang_creature['search_results']}</font>
                </h1>";
        break;
    case 3:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_creature['add_new_mob_templ']}</font>
                </h1>";
        break;
    case 4:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_creature['edit_mob_templ']}</font>
                </h1>";
        break;
    case 5:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_creature['err_adding_new']}</font>
                </h1>";
        break;
    case 6:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_creature['err_no_fields_updated']}</font>
                </h1>";
        break;
    case 7:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_creature['add_new_success']}</font>
                </h1>";
        break;
    case 8:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_global['err_invalid_input']}</font>
                </h1>";
        break;
    case 9:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_global['err_no_permission']}</font>
                </h1>";
        break;
    default: //no error
    $output .= "
                <h1>{$lang_creature['search_creatures']}</h1>";
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
        do_insert_update(1);
        break;

    case "do_update":
        do_update();
        break;

    case "edit":
        do_insert_update(0);
        break;

    case "delete":
        delete();
        break;

    case "delete_spwn":
        delete_spwn();
        break;

    case "do_delete":
        do_delete();
        break;

    default:
        search();
}

require_once("footer.php");
?>
