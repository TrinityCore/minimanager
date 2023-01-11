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

define('HEADER_LOADED', true);

require_once("func.php");

// All pulled from MiniManager settings
// to change them, edit /scripts/config.php

if ( !ini_get('session.auto_start') ) 
	session_start();
require_once("../scripts/config.dist.php");
require_once("../scripts/config.php");
require_once '../libs/data_lib.php';

session_regenerate_id();
$realm_id = (isset($_GET['r_id'])) ? (int)$_GET['r_id'] : $_SESSION['realm_id'];
$server_arr = $server;

if (isset($_COOKIE["lang"]))
{
    $lang = $_COOKIE["lang"];
    if (!file_exists("map_".$lang.".php") && !file_exists("zone_names_".$lang.".php"))
        $lang = $language;
}
else
    $lang = $language;

$database_encoding = $site_encoding;

$server = $server_arr[$realm_id]["addr"];
$port = $server_arr[$realm_id]["game_port"];

$host = $characters_db[$realm_id]["addr"];
$user = $characters_db[$realm_id]["user"];
$password = $characters_db[$realm_id]["pass"];
$db = $characters_db[$realm_id]["name"];

$hostr = $realm_db["addr"];
$userr = $realm_db["user"];
$passwordr = $realm_db["pass"];
$dbr = $realm_db["name"];

$sql = new DBLayer($hostr, $userr, $passwordr, $dbr);
$query = $sql->query("SELECT name FROM realmlist WHERE id = ".$realm_id);
$realm_name = $sql->fetch_assoc($query);
$realm_name = htmlentities($realm_name["name"]);

$gm_show_online = $gm_online;
$gm_show_online_only_gmoff = $map_gm_show_online_only_gmoff;
$gm_show_online_only_gmvisible = $map_gm_show_online_only_gmvisible;
$gm_add_suffix = $map_gm_add_suffix;
$gm_include_online = $gm_online_count;
$show_status = $map_show_status;
$time_to_show_uptime = $map_time_to_show_uptime;
$time_to_show_maxonline = $map_time_to_show_maxonline;
$time_to_show_gmonline = $map_time_to_show_gmonline;
$status_gm_include_all = $map_status_gm_include_all;
$time = $map_time;
$show_time = $map_show_time;

// points located on these maps(do not modify it)
$maps_for_points = "0,1,530,571,609";

$img_base = "../img/map/";
$img_base2 = "../img/c_icons/";
?>
