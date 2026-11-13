<?php

require_once("header.php");
require_once("libs/char_lib.php");
valid_login($action_permission['read']);

//global $lang_honor, $lang_global, $output, $characters_db, $realm_id, $itemperpage, $realm_db;

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
$order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"honor";

if ($_SESSION['user_lvl'] < 1)
{
	$query = $sql->query("SELECT C.guid, BINARY C.name AS name, C.race, C.class, C.totalHonorPoints AS honor , C.totalKills AS kills, C.level, C.arenaPoints AS arena, COALESCE(guild_member.guildid,0) as GNAME, C.gender, BINARY C.deleteInfos_Name AS deleted_name FROM characters C LEFT JOIN guild_member ON C.guid = guild_member.guid WHERE name<>'' AND race in (1,3,4,7,11) ORDER BY $order_by DESC LIMIT 25;");
}
else
{
	$query = $sql->query("SELECT C.guid, BINARY C.name AS name, C.race, C.class, C.totalHonorPoints AS honor , C.totalKills AS kills, C.level, C.arenaPoints AS arena, COALESCE(guild_member.guildid,0) as GNAME, C.gender, BINARY C.deleteInfos_Name AS deleted_name FROM characters C LEFT JOIN guild_member ON C.guid = guild_member.guid WHERE race in (1,3,4,7,11) ORDER BY $order_by DESC LIMIT 25;");
}

$this_page = $sql->num_rows($query);
$output .= "
                <script type=\"text/javascript\">
                    answerbox.btn_ok='{$lang_global['yes_low']}';
                    answerbox.btn_cancel='{$lang_global['no']}';
                </script>
                <center>
                    <fieldset style=\"width: 776px;\">
                        <legend><img src='img/alliance.gif' /></legend>
                        <table class=\"lined\" style=\"width: 705px;\">
                            <tr class=\"bold\">
                                <td colspan=\"11\">{$lang_honor['allied']} {$lang_honor ['browse_honor']}</td>
                            </tr>
                            <tr>
                                <th width=\"30%\">{$lang_honor['guid']}</th>
                                <th width=\"7%\">{$lang_honor['race']}</th>
                                <th width=\"7%\">{$lang_honor['class']}</th>
                                <th width=\"7%\">{$lang_honor['level']}</th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor']}</a></th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor points']}</a></th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=kills\"".($order_by=='kills' ? " class=DESC" : "").">{$lang_global['kills']}</a></th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=arena\"".($order_by=='arena' ? " class=DESC" : "").">AP</a></th>
                                <th width=\"30%\">{$lang_honor['guild']}</th>
                            </tr>";

while ($char = $sql->fetch_row($query))
{
    $charname = ($char[1] != '') ? htmlentities($char[1]) :  htmlentities($char[10]) . " (Deleted)";
	
	$guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[8].";"));
	
    $output .= "
                            <tr>
                                <td><a href=\"char.php?id=$char[0]\">$charname</a></td>
                                <td><img src='img/c_icons/{$char[2]}-{$char[9]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
                                <td><img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
                                <td>".char_get_level_color($char[6])."</td>
                                <td><span onmouseover='toolTip(\"".char_get_pvp_rank_name($char[4], char_get_side_id($char[2]))."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".char_get_pvp_rank_id($char[4], char_get_side_id($char[2])).".gif'></span></td>
                                <td>$char[4]</td>
                                <td>$char[5]</td>
                                <td>$char[7]</td>
                                <td>".(!empty($guild_name[0]) ? "<a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[8]\">".htmlentities($guild_name[0])."</a>" : "-")."</td>
                            </tr>";
}
$output .= "
                        </table>
                        <br />
                    </fieldset>";

if ($_SESSION['user_lvl'] < 1)
{					
	$query = $sql->query("SELECT C.guid, BINARY C.name AS name, C.race, C.class, C.todayHonorPoints AS honor , C.totalKills AS kills, C.level, C.arenaPoints AS arena, COALESCE(guild_member.guildid,0) as GNAME, C.gender, BINARY C.deleteInfos_Name AS deleted_name FROM characters C LEFT JOIN guild_member ON C.guid = guild_member.guid WHERE name<>'' AND race not in (1,3,4,7,11) ORDER BY $order_by DESC LIMIT 25;");
}
else
{
	$query = $sql->query("SELECT C.guid, BINARY C.name AS name, C.race, C.class, C.todayHonorPoints AS honor , C.totalKills AS kills, C.level, C.arenaPoints AS arena, COALESCE(guild_member.guildid,0) as GNAME, C.gender, BINARY C.deleteInfos_Name AS deleted_name FROM characters C LEFT JOIN guild_member ON C.guid = guild_member.guid WHERE race not in (1,3,4,7,11) ORDER BY $order_by DESC LIMIT 25;");
}
$this_page = $sql->num_rows($query);
$output .= "
                <script type=\"text/javascript\">
                    answerbox.btn_ok='{$lang_global['yes_low']}';
                    answerbox.btn_cancel='{$lang_global['no']}';
                </script>
                <center>
                    <fieldset style=\"width: 776px;\">
                        <legend><img src='img/horde.gif' /></legend>
                        <table class=\"lined\" style=\"width: 705px;\">
                            <tr class=\"bold\">
                                <td colspan=\"11\">{$lang_honor['horde']} {$lang_honor ['browse_honor']}</td>
                            </tr>
                            <tr>
                                <th width=\"30%\">{$lang_honor['guid']}</th>
                                <th width=\"7%\">{$lang_honor['race']}</th>
                                <th width=\"7%\">{$lang_honor['class']}</th>
                                <th width=\"7%\">{$lang_honor['level']}</th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor']}</a></th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor points']}</a></th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=kills\"".($order_by=='kills' ? " class=DESC" : "").">{$lang_global['kills']}</a></th>
                                <th width=\"5%\"><a href=\"honor.php?order_by=arena\"".($order_by=='arena' ? " class=DESC" : "").">AP</a></th>
                                <th width=\"30%\">{$lang_honor['guild']}</th>
                            </tr>";

while ($char = $sql->fetch_row($query))
{
    $charname = ($char[1] != '') ? htmlentities($char[1]) :  htmlentities($char[10]) . " (Deleted)";
	
    $guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[8].";"));
    $output .= "
                            <tr>
                                <td><a href=\"char.php?id=$char[0]\">$charname</a></td>
                                <td><img src='img/c_icons/{$char[2]}-{$char[9]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
                                <td><img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
                                <td>".char_get_level_color($char[6])."</td>
                                <td><span onmouseover='toolTip(\"".char_get_pvp_rank_name($char[4], char_get_side_id($char[2]))."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".char_get_pvp_rank_id($char[4], char_get_side_id($char[2])).".gif'></span></td>
                                <td>$char[4]</td>
                                <td>$char[5]</td>
                                <td>$char[7]</td>
                                <td>".(!empty($guild_name[0]) ? "<a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[8]\">".htmlentities($guild_name[0])."</a>" : "-")."</td>
                            </tr>";
}
$output .= "
                        </table>
                        <br />
                    </fieldset>";

require_once("footer.php");
?>
