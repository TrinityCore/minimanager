<?php
/*
    POMM  v1.3
    Player Online Map for Trinity

    Show online players position on map. Update without refresh.
    Show tooltip with location, race, class and level of player.
    Show realm status.

    16.09.2006      https://pomm.da.ru/

    Created by mirage666 (c) (mailto:mirage666@pisem.net icq# 152263154)
    2006-2009 Modified by killdozer.
*/

require_once("pomm_conf.php");
require_once("func.php");

if (file_exists("map_".$lang.".php") && file_exists("zone_names_".$lang.".php"))
  require_once("map_".$lang.".php");
else
  require_once("map_english.php");

$_RESULT = NULL;

$maps_count = count($lang_defs['maps_names']);

$Horde_races = 0x2B2;
$Alliance_races = 0x44D;
$outland_inst   = [540,542,543,544,545,546,547,548,550,552,553,554,555,556,557,558,559,562,564,565];
$northrend_inst = [533,574,575,576,578,599,600,601,602,603,604,608,615,616,617,619,624];

require_once "../libs/js/JsHttpRequest/Php.php";
$JsHttpRequest = new Subsys_JsHttpRequest_Php("utf-8");

$realm_db = new DBLayer($hostr, $userr, $passwordr, $dbr);
if(!$realm_db->isValid())
{
    $_RESULT['status']['online'] = 2;
    exit();
}
$realm_db->query("SET NAMES $database_encoding");

$gm_online = 0;
$gm_accounts = [];
$query = $realm_db->query("SELECT GROUP_CONCAT(`AccountID` SEPARATOR ' ') FROM `account_access` WHERE `SecurityLevel`>'0'");
if($query)
    if($result = $realm_db->fetch_row($query))
        $gm_accounts = explode(' ', $result[0]);
$groups = [];
$characters_db = new DBLayer($host, $user, $password, $db);
if(!$characters_db->isValid())
{
    $_RESULT['status']['online'] = 2;
    exit();
}
$characters_db->query("SET NAMES $database_encoding");
$query = $characters_db->query("SELECT `groups`.`leaderGuid`, `group_member`.`memberGuid` FROM `groups` LEFT JOIN `group_member` ON `groups.`guid` = `group_member`.`guid` WHERE `memberGuid` IN(SELECT `guid` FROM `characters` WHERE `online`='1')");
if($query)
    while($result = $characters_db->fetch_assoc($query))
        $groups[$result['memberGuid']] = $result['leaderGuid'];

$Count = [];
for($i = 0; $i < $maps_count; $i++) {
    $Count[$i] = [0,0];
    }
$arr = [];
$i=$maps_count;
$query = $characters_db->query("SELECT `guid`,`account`,`playerFlags`,`name`,`class`,`race`, `level`, `gender`, `position_x`,`position_y`,`map`,`zone`,`extra_flags` FROM `characters` WHERE `online`='1' ORDER BY `name`");
while($result = $characters_db->fetch_assoc($query))
{
    if($result['map'] == 530 && $result['position_y'] > -1000 || in_array($result['map'], $outland_inst))
        $Extention = 1;
    else if($result['map'] == 571 || in_array($result['map'], $northrend_inst))
        $Extention = 2;
    else
        $Extention = 0;

    $gm_player = false;
    $show_player = true;
    if(in_array($result['account'], $gm_accounts))
    {
        $gm_player = true;
        $show_player = false;
        if($gm_show_online == 1)
        {
            $show_player = true;
            if(($result['extra_flags'] & 0x1) != 0 && $gm_show_online_only_gmoff == 1)
                $show_player = false;
            if(($result['extra_flags'] & 0x10) != 0 && $gm_show_online_only_gmvisible == 1)
                $show_player = false;
            if($gm_add_suffix && $show_player)
                $result['name'] = $result['name'].' <small style="color: #EABA28;">{GM}</small>';
        }
    }

    if($gm_player == false || ($gm_player == true && $gm_include_online == 1))
    {
        if($Horde_races & (0x1 << ($result['race']-1)))
            $Count[$Extention][1]++;
        else if($Alliance_races & (0x1 << ($result['race']-1)))
            $Count[$Extention][0]++;
    }

    if(($gm_player && $show_player) || ($gm_player && !$show_player && $status_gm_include_all))
        $gm_online++;
    if($gm_player && $show_player == false)
        continue;

    $char_dead = ($result['playerFlags'] & 0x11)?1:0;
    $arr[$i]['x'] = $result['position_x'];
    $arr[$i]['y'] = $result['position_y'];
    $arr[$i]['dead'] = $char_dead;
    $arr[$i]['name']=$result['name'];
    $arr[$i]['map']=$result['map'];
    $arr[$i]['zone']=get_zone_name($result['zone']);
    $arr[$i]['cl'] = $result['class'];
    $arr[$i]['race'] = $result['race'];
    $arr[$i]['level']=$result['level'];
    $arr[$i]['gender'] = $result['gender'];
    $arr[$i]['Extention'] = $Extention;
    $arr[$i]['leaderGuid'] = isset($groups[$result['guid']]) ? $groups[$result['guid']] : 0;
    $i++;
}
$characters_db->close();
unset($characters_db);

if(!count($arr) && !test_realm())
    $res['online'] = NULL;
else
{
    usort($arr, "sort_players");
    $arr = array_merge($Count, $arr);
    $res['online'] = $arr;
}

if($show_status) {
    $query = $realm_db->query("SELECT UNIX_TIMESTAMP(),`starttime`,`maxplayers` FROM `uptime` WHERE `starttime`=(SELECT MAX(`starttime`) FROM `uptime`)");
    if($result = $realm_db->fetch_row($query)) {
        $status['online'] = test_realm() ? 1 : 0;
        $status['uptime'] = $result[0] - $result[1];
        $status['maxplayers'] = $result[2];
        $status['gmonline'] = $gm_online;
        }
    else
        $status = NULL;
    }
else
    $status = NULL;

$realm_db->close();
unset($realm_db);

$res['status'] = $status;

$_RESULT = $res;
?>
