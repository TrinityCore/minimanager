<?php

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/item_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['delete']);

//########################################################################################################################
//  PRINT  EDIT FORM
//########################################################################################################################
function edit_char() { //form needs update, uneditable fields have been removed for now but style is fucked up.
    global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $mmfpm_db, $action_permission, $user_lvl,
            $item_datasite;
    wowhead_tt();

    valid_login($action_permission['delete']);
    if (empty($_GET['id']))
        error($lang_global['empty_fields']);

    $sql = new SQL;
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    $id = $sql->quote_smart($_GET['id']);

    $result = $sql->query("SELECT account FROM `characters` WHERE guid = '$id'");

    if ($sql->num_rows($result))
    {
        //resrict by owner's gmlvl
        $owner_acc_id = $sql->result($result, 0, 'account');
        $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
        $query = $sql->query("SELECT SecurityLevel FROM account_access WHERE AccountID ='$owner_acc_id'");
        $query = $sql->query("SELECT username FROM account WHERE id ='$owner_acc_id'");
        $owner_gmlvl = $sql->result($query, 0, 'SecurityLevel');
        $owner_name = $sql->result($query, 0, 'username');
        $owner_check = $sql->result($query, 0, 'username');

        if ($user_lvl >= $owner_gmlvl)
        {
            $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

            $result = $sql->query("SELECT characters.guid,characters.account,BINARY characters.name AS name,characters.race,characters.class,characters.position_x,characters.position_y,characters.map,characters.online,characters.totaltime,characters.position_z,characters.zone,characters.level,characters.gender,
                                COALESCE(guild_member.guildid,0) AS guildid, COALESCE(guild_member.rank,0) AS grank, characters.totalHonorpoints, characters.totalKills, characters.arenaPoints, characters.equipmentCache, characters.money
                                FROM characters LEFT JOIN guild_member ON characters.guid = guild_member.guid WHERE characters.guid = '$id'");
            $char = $sql->fetch_assoc($result);
            $eq_data = explode(' ',$char['equipmentCache']);

            if($char['online'])
                $online = "<font class=\"error\">{$lang_char['online']}</font>{$lang_char['edit_offline_only_char']}";
            else
                $online = $lang_char['offline'];

            if($char['guildid'])
            {
                $query = $sql->query("SELECT BINARY name AS name FROM guild WHERE guildid ='{$char['guildid']}'");
                $guild_name = $sql->result($query, 0, 'name');
                if ($user_lvl > 0 )
                    $guild_name = "<a href=\"guild.php?action=view_guild&amp;error=3&amp;id={$char['guildid']}\" >$guild_name</a>";
                if ($char['grank'])
                {
                    $guild_rank_query = $sql->query("SELECT rname FROM guild_rank WHERE guildid ='{$char['guildid']}' AND rid='{$char['grank']}'");
                    $guild_rank = $sql->result($guild_rank_query, 0, 'rname');
                }
                else
                    $guild_rank = $lang_char['guild_leader'];
            }
            else
            {
                $guild_name = $lang_global['none'];
                $guild_rank = $lang_global['none'];
            }

            $output .= "
                        <center>
                            <form method=\"get\" action=\"char_edit.php\" name=\"form\">
                                <input type=\"hidden\" name=\"action\" value=\"do_edit_char\" />
                                <input type=\"hidden\" name=\"id\" value=\"$id\" />
                                <table class=\"lined\">
                                    <tr>
                                        <td colspan=\"8\"><font class=\"bold\"><input type=\"text\" name=\"name\" size=\"14\" maxlength=\"12\" value=\"".$char['name']."\" /> - <img src='img/c_icons/".$char['race']."-".$char['gender'].".gif' onmousemove='toolTip(\"".char_get_race_name($char['race'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/".$char['class'].".gif' onmousemove='toolTip(\"".char_get_class_name($char['class'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".char_get_level_color($char['level'])."</font><br />".$online."</td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"8\">".get_map_name($char['online'], $sqlm)." - ".get_zone_name($char['zone'], $sqlm)."</td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"8\">{$lang_char['username']}: <input type=\"text\" name=\"owner_name\" size=\"20\" maxlength=\"25\" value=\"$owner_name\" /> | {$lang_char['acc_id']}: $owner_acc_id</td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"8\">{$lang_char['guild']}: $guild_name | {$lang_char['rank']}: $guild_rank</td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"8\">{$lang_char['honor_points']}: <input type=\"text\" name=\"honor_points\" size=\"8\" maxlength=\"6\" value=\"{$char['totalHonorpoints']}\" />/
                                            <input type=\"text\" name=\"arena_points\" size=\"8\" maxlength=\"6\" value=\"{$char['arenaPoints']}\" /> - {$lang_char['honor_kills']}: <input type=\"text\" name=\"total_kills\" size=\"8\" maxlength=\"6\" value=\"{$char['totalKills']}\" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width=\"2%\"><input type=\"checkbox\" name=\"check[]\" value=\"a0\" /></td><td width=\"18%\">{$lang_item['head']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_HEAD]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_HEAD])."</a></td>
                                        <td width=\"18%\">{$lang_item['gloves']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_GLOVES]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_GLOVES])."</a></td><td width=\"2%\"><input type=\"checkbox\" name=\"check[]\" value=\"a9\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a1\" /></td><td>{$lang_item['neck']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_NECK]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_NECK])."</a></td>
                                        <td>{$lang_item['belt']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_BELT]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_BELT])."</a></td> <td><input type=\"checkbox\" name=\"check[]\" value=\"a5\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a2\" /></td><td>{$lang_item['shoulder']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_SHOULDER]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_SHOULDER])."</a></td>
                                        <td>{$lang_item['legs']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_LEGS]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_LEGS])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a6\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a14\" /></td><td>{$lang_item['back']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_BACK]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_BACK])."</a></td>
                                        <td>{$lang_item['feet']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_FEET]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_FEET])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a7\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a4\" /></td><td>{$lang_item['chest']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_CHEST]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_CHEST])."</a></td>
                                        <td>{$lang_item['finger']} 1<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_FINGER1]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_FINGER1])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a10\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a3\" /></td><td>{$lang_item['shirt']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_SHIRT]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_SHIRT])."</a></td>
                                        <td>{$lang_item['finger']} 2<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_FINGER2]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_FINGER2])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a11\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a18\" /></td><td>{$lang_item['tabard']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_TABARD]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_TABARD])."</a></td>
                                        <td>{$lang_item['trinket']} 1<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_TRINKET1]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_TRINKET1])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a12\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a8\" /></td><td>{$lang_item['wrist']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_WRIST]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_WRIST])."</a></td>
                                        <td>{$lang_item['trinket']} 2<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_TRINKET2]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_TRINKET2])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a13\" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a15\" /></td>
                                        <td colspan=\"2\">{$lang_item['main_hand']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_MAIN_HAND]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_MAIN_HAND])."</a></td>
                                        <td colspan=\"2\"><input type=\"checkbox\" name=\"check[]\" value=\"a16\" />&nbsp;{$lang_item['off_hand']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_OFF_HAND]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_OFF_HAND])."</a></td>
                                        <td colspan=\"2\">{$lang_item['ranged']}<br /><a href=\"$item_datasite{$eq_data[EQ_DATA_OFFSET_EQU_RANGED]}\" target=\"_blank\">".get_item_name($eq_data[EQ_DATA_OFFSET_EQU_RANGED])."</a></td>
                                        <td><input type=\"checkbox\" name=\"check[]\" value=\"a17\" /></td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"4\">{$lang_char['gold']}: <input type=\"text\" name=\"money\" size=\"10\" maxlength=\"8\" value=\"{$char['money']}\" /></td>
                                        <td colspan=\"4\">{$lang_char['tot_paly_time']}: <input type=\"text\" name=\"tot_time\" size=\"8\" maxlength=\"14\" value=\"{$char['totaltime']}\" /></td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"5\">{$lang_char['location']}:
                                            X:<input type=\"text\" name=\"x\" size=\"10\" maxlength=\"8\" value=\"{$char['position_x']}\" />
                                            Y:<input type=\"text\" name=\"y\" size=\"8\" maxlength=\"16\" value=\"{$char['position_y']}\" />
                                            Z:<input type=\"text\" name=\"z\" size=\"8\" maxlength=\"16\" value=\"{$char['position_z']}\" />
                                            Map:<input type=\"text\" name=\"map\" size=\"8\" maxlength=\"16\" value=\"{$char['map']}\" />
                                        </td>
                                        <td colspan=\"3\">{$lang_char['move_to']}:<input type=\"text\" name=\"tp_to\" size=\"24\" maxlength=\"64\" value=\"\" /></td>
                                    </tr>
                                </table>
                                <br />";

            //inventory+bank items
            $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
            $query2 = $sql->query("SELECT bag,slot,item,itemEntry FROM character_inventory ci JOIN item_instance ii ON ci.item = ii.guid WHERE ci.guid = '$id' ORDER BY bag,slot");

            $inv = [];
            $count = 0;

            while ($slot = $sql->fetch_row($query2))
            {
                if ($slot[0] == 0)
                {
                    if($slot[1] >= 23 && $slot[1] <= 62)
                    {
                        $count++;
                        $inv[$count][0] = $slot[3];
                        $inv[$count][1] = $slot[2];
                    }
                }
                else
                {
                    $count++;
                    $inv[$count][0] = $slot[3];
                    $inv[$count][1] = $slot[2];
                }
            }

            $output .= "
                                <table class=\"lined\">
                                    <tr>
                                        <td>{$lang_char['inv_bank']}</td>
                                    </tr>
                                    <tr>
                                        <td height=\"100\" align=\"center\">
                                            <table>
                                                <tr align=\"center\">";
            $j = 0;
            for ($i=1; $i<=$count; $i++)
            {
                $j++;
                $output .= "
                                                    <td>
                                                        <a href=\"$item_datasite{$inv[$i][0]}\" target=\"_blank\">{$inv[$i][0]}</a>
                                                        <br />
                                                        <input type=\"checkbox\" name=\"check[]\" value=\"{$inv[$i][1]}\" />
                                                    </td>";
                if ($j == 15)
                {
                    $output .= "</tr><tr align=\"center\">";
                    $j = 0;
                }
            }
            $output .= "
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";
            makebutton($lang_char['update'], "javascript:do_submit()",190);
            makebutton($lang_char['to_char_view'], "char.php?id=$id",160);
            makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",160);
            makebutton($lang_global['back'], "javascript:window.history.back()",160);

            $output .= "
                                        </td>
                                    </tr>
                                </table>
                                <br />
                            </form>
                        </center>";

         //case of non auth request
        }
        else
        {
            $sql->close();
            unset($sql);
            error($lang_char['no_permission']);
            exit();
        }
    }
    else
        error($lang_char['no_char_found']);
}


//########################################################################################################################
//  DO EDIT CHARACTER
//########################################################################################################################
function do_edit_char() {
    global $lang_global, $lang_char, $output, $realm_db,
            $characters_db, $realm_id, $action_permission, $user_lvl, $world_db;
    valid_login($action_permission['delete']);
    if (empty($_GET['id']) || empty($_GET['name']))
        error($lang_global['empty_fields']);

    $sql = new SQL;
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $id = $sql->quote_smart($_GET['id']);

    $result = $sql->query("SELECT account, online FROM characters WHERE guid = '$id'");

    if ($sql->num_rows($result))
    {
        //we cannot edit online chars
        if(!$sql->result($result, 0, 'online'))
        {
            //resrict by owner's gmlvl
            $owner_acc_id = $sql->result($result, 0, 'account');
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
            $query = $sql->query("SELECT SecurityLevel FROM account_access WHERE AccountID ='$owner_acc_id' and (`RealmID` = $realm_id or `RealmID` = -1)");
            $owner_gmlvl = $sql->result($query, 0, 'SecurityLevel');
            $new_owner_name = $_GET['owner_name'];
            $query = $sql->query("SELECT id FROM account WHERE username ='$new_owner_name'");
            $new_owner_acc_id = $sql->result($query, 0, 'id');

            if ($owner_acc_id != $new_owner_acc_id)
            {
                $max_players = $sql->query("SELECT numchars FROM realmcharacters WHERE acctid ='$new_owner_acc_id'");
                $max_players = $max_players[0];

                if($max_players <= 9)
                    $result = $sql->query("UPDATE `{$characters_db[$realm_id]['name']}`.`characters` SET account = $new_owner_acc_id WHERE guid = $id"); //there should be a seperate SQL-object for characterdb. what if realmdbuser can't access characterdb?
                else
                    redirect("char_edit.php?action=edit_char&id=$id&error=5");
            }

            if ($user_lvl > $owner_gmlvl)
            {
                if(isset($_GET['check']))
                    $check = $sql->quote_smart($_GET['check']);
                else
                    $check = NULL;

                $new_name = $sql->quote_smart($_GET['name']);

                if (isset($_GET['tot_time']))
                    $new_tot_time = $sql->quote_smart($_GET['tot_time']);
                else
                    $new_tot_time =  0;

                if (isset($_GET['money']))
                    $new_money = $sql->quote_smart($_GET['money']);
                else
                    $new_money =  0;

                if (isset($_GET['arena_points']))
                    $new_arena_points = $sql->quote_smart($_GET['arena_points']);
                else
                    $new_arena_points =  0;

                if (isset($_GET['honor_points']))
                    $new_honor_points = $sql->quote_smart($_GET['honor_points']);
                else
                    $new_honor_points =  0;

                if (isset($_GET['total_kills']))
                    $new_total_kills = $sql->quote_smart($_GET['total_kills']);
                else
                    $new_total_kills =  0;

                if ((!is_numeric($new_tot_time))||(!is_numeric($new_money))||(!is_numeric($new_arena_points))||(!is_numeric($new_honor_points)))
                    error($lang_char['use_numeric']);

                $x = (isset($_GET['x'])) ? $sql->quote_smart($_GET['x']) : 0;
                $y = (isset($_GET['y'])) ? $sql->quote_smart($_GET['y']) : 0;
                $z = (isset($_GET['z'])) ? $sql->quote_smart($_GET['z']) : 0;
                $map = (isset($_GET['map'])) ? $sql->quote_smart($_GET['map']) : 0;
                $tp_to = (isset($_GET['tp_to'])) ? $sql->quote_smart($_GET['tp_to']) : 0;

                $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

                $result = $sql->query("SELECT equipmentCache FROM characters WHERE guid = '$id'");
                $char = $sql->fetch_row($result);
                $eq_data = explode(' ',$char[0]);

                //some items need to be deleted
                if($check)
                {
                    $item_offset = [
                        "a0" => EQ_DATA_OFFSET_EQU_HEAD,
                        "a1" => EQ_DATA_OFFSET_EQU_NECK,
                        "a2" => EQ_DATA_OFFSET_EQU_SHOULDER,
                        "a3" => EQ_DATA_OFFSET_EQU_SHIRT,
                        "a4" => EQ_DATA_OFFSET_EQU_CHEST,
                        "a5" => EQ_DATA_OFFSET_EQU_BELT,
                        "a6" => EQ_DATA_OFFSET_EQU_LEGS,
                        "a7" => EQ_DATA_OFFSET_EQU_FEET,
                        "a8" => EQ_DATA_OFFSET_EQU_WRIST,
                        "a9" => EQ_DATA_OFFSET_EQU_GLOVES,
                        "a10" => EQ_DATA_OFFSET_EQU_FINGER1,
                        "a11" => EQ_DATA_OFFSET_EQU_FINGER2,
                        "a12" => EQ_DATA_OFFSET_EQU_TRINKET1,
                        "a13" => EQ_DATA_OFFSET_EQU_TRINKET2,
                        "a14" => EQ_DATA_OFFSET_EQU_BACK,
                        "a15" => EQ_DATA_OFFSET_EQU_MAIN_HAND,
                        "a16" => EQ_DATA_OFFSET_EQU_OFF_HAND,
                        "a17" => EQ_DATA_OFFSET_EQU_RANGED,
                        "a18" => EQ_DATA_OFFSET_EQU_TABARD
                    ];

                    foreach ($check as $item_num)
                    {
                        //deleting equiped items
                        if ($item_num[0] == "a")
                        {
                            $eq_data[$item_offset[$item_num]] = 0;

                            sscanf($item_num, "a%d",$item_num);
                            $result = $sql->query("SELECT item FROM character_inventory WHERE guid = '$id' AND slot = $item_num AND bag = 0");
                            $item_inst_id = $sql->result($result, 0, 'item');

                            $sql->query("DELETE FROM character_inventory WHERE guid = '$id' AND slot = $item_num AND bag = 0");
                            $sql->query("DELETE FROM item_instance WHERE guid = '$item_inst_id' AND owner_guid = '$id'");
                        }
                        else
                        { //deleting inv/bank items
                            $sql->query("DELETE FROM character_inventory WHERE guid = '$id' AND item = '$item_num'");
                            $sql->query("DELETE FROM item_instance WHERE guid = '$item_num' AND owner_guid = '$id'");
                        }
                    }
                }

                $data = implode(' ',$eq_data);

                if ($tp_to)
                {
                    $query = $sql->query("SELECT map, position_x, position_y, position_z, orientation FROM `".$world_db[$realm_id]['name']."`.`game_tele` WHERE LOWER(name) = '".strtolower($tp_to)."'");
                    $tele = $sql->fetch_row($query);

                    if($tele)
                        $teleport = "map='$tele[0]', position_x='$tele[1]', position_y='$tele[2]', position_z='$tele[3]', orientation='$tele[4]',";
                    else
                        error($lang_char['no_tp_location']);
                }
                else
                    $teleport = "map='$map', position_x='$x', position_y='$y', position_z='$z',";

                $result = $sql->query("UPDATE characters SET equipmentCache = '$data', name = '$new_name', $teleport totaltime = '$new_tot_time', money = '$new_money', arenaPoints = '$new_arena_points', totalHonorPoints = '$new_honor_points', totalKills = '$new_total_kills' WHERE guid = $id");
                $sql->close();
                unset($sql);

                if ($result)
                    redirect("char_edit.php?action=edit_char&id=$id&error=3");
                else
                    redirect("char_edit.php?action=edit_char&id=$id&error=4");
            }
            else
            {
                $sql->close();
                unset($sql);
                error($lang_char['no_permission']);
            }
        }
        else
        {
            $sql->close();
            unset($sql);
            redirect("char_edit.php?action=edit_char&id=$id&error=2");
        }
    }
    else
        error($lang_char['no_char_found']);
    $sql->close();
    unset($sql);
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_char = lang_char();

$output .= "
            <div class=\"top\">";
switch ($err) {
    case 1:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_global['empty_fields']}</font>
                </h1>";
        break;
    case 2:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_char['err_edit_online_char']}</font>
                </h1>";
        break;
    case 3:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_char['updated']}</font>
                </h1>";
        break;
    case 4:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_char['update_err']}</font>
                </h1>";
        break;
    case 5:
        $output .= "
                <h1>
                    <font class=\"error\">{$lang_char['max_acc']}</font>
                </h1>";
        break;
    default: //no error
        $output .= "
                <h1>{$lang_char['edit_char']}</h1>
                <br />{$lang_char['check_to_delete']}";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
    case "edit_char":
        edit_char();
        break;
    case "do_edit_char":
        do_edit_char();
        break;
    default:
            edit_char();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");
?>
