<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['read']);

//#############################################################################
// BROWSE GUILDS
//#############################################################################
function browse_guilds(&$sqlr, &$sqlc)
{
    global $output, $lang_guild, $lang_global,
            $realm_db, $characters_db, $realm_id,
            $action_permission, $user_lvl, $user_id,
            $itemperpage;

    // this is multi realm support, as of writing still under development
    //  this page is already implementing it
    if (empty($_GET['realm']))
        $realmid = $realm_id;
    else
    {
        $realmid = $sqlr->quote_smart($_GET['realm']);
        if (is_numeric($realmid))
            $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
        else
            $realmid = $realm_id;
    }

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'gid';
    if (preg_match('/^[_[:lower:]]{1,10}$/', $order_by));
    else
        $order_by='gid';

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir=1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================
    //==========================MyGuild==========================================

    $query_myGuild = $sqlc->query("SELECT g.guildid as gid, BINARY g.name, g.leaderguid AS lguid,
                                    (SELECT BINARY name from characters where guid = lguid), (SELECT race in (2,5,6,8,10) from characters where guid = lguid) as faction,
                                    (select count(*) from characters where guid in (select guid from guild_member where guildid = lguid) and online = 1) as gonline,
                                    (select count(*) from guild_member where guildid = gid), SUBSTRING_INDEX(g.MOTD,' ',6), g.createdate,
                                    (select account from characters where guid = lguid) FROM guild as g
                                    left outer join guild_member as gm on gm.guildid = g.guildid left outer join characters as c on c.guid = gm.guid
                                    where c.account = $user_id group by g.guildid order by gid");

    if ($query_myGuild)
    {
        $output .= '
                <center>
                    <fieldset>
                        <legend>'.$lang_guild['my_guilds'].'</legend>
                        <table class="lined" align="center">
                            <tr>
                                <th width="1%">'.$lang_guild['id'].'</th>
                                <th width="20%">'.$lang_guild['guild_name'].'</th>
                                <th width="10%">'.$lang_guild['guild_leader'].'</th>
                                <th width="1%">'.$lang_guild['guild_faction'].'</th>
                                <th width="10%">'.$lang_guild['tot_m_online'].'</th>
                                <th width="20%">'.$lang_guild['guild_motd'].'</th>
                                <th width="20%">'.$lang_guild['create_date'].'</th>
                            </tr>';
        while ($data = $sqlr->fetch_row($query_myGuild))
        {
            $result = $sqlr->query("SELECT gmlevel FROM account_access WHERE id ='$data[9]'");
            $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
            $output .= "
                            <tr>
                                <td>$data[0]</td>
                                <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$data[0]\">$data[1]</a></td>";
            $output .= ($user_lvl < $owner_gmlvl ) ? "<td>".htmlentities($data[3])."</td>" : "<td><a href=\"char.php?id=$data[2]&amp;realm=$realmid\">".htmlentities($data[3])."</a></td>";
            $output .= "
                                <td><img src=\"img/".($data[4]==0 ? "alliance" : "horde")."_small.gif\" alt=\"\" /></td>
                                <td>$data[5]/$data[6]</td>
                                <td>".htmlentities($data[7])." ...</td>
                                <td class=\"small\">".date('o-m-d', $data[8])."</td>
                            </tr>";
        }
        unset($data);
        unset($result);
        $output .= '
                        </table>
                    </fieldset>
                    <br />
                </center>';
    }
    //==========================MyGuild end======================================
    //==========================Browse/Search Guilds CHECK=======================
    $search_by ='';
    $search_value = '';
    if(isset($_GET['search_value']) && isset($_GET['search_by']))
    {
        $search_by = $sqlc->quote_smart($_GET['search_by']);
        $search_value = $sqlc->quote_smart($_GET['search_value']);

        $search_menu = array('name', 'leadername', 'guildid');
        if (in_array($search_by, $search_menu));
        else
            $search_by = 'name';

        switch($search_by)
        {
            case "name":
                if (preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value))
                    redirect("guild.php?error=5&amp;realm=$realmid");
                $query = $sqlc->query("SELECT g.guildid as gid, BINARY g.name,g.leaderguid as lguid,
                                      (SELECT BINARY name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction,
                                      (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount
                                      FROM guild as g left outer join characters as c on c.guid = g.leaderguid
                                      where BINARY g.name like '%$search_value%' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
                $query_count = $sqlc->query("SELECT 1 from guild where BINARY name like '%$search_value%'");
                break;

            case "leadername" :
                if (preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value))
                    redirect("guild.php?error=5&amp;realm=$realmid");
                $query = $sqlc->query("SELECT g.guildid as gid, BINARY g.name,g.leaderguid as lguid,
                                      (SELECT BINARY name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction,
                                      (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount
                                      FROM guild as g left outer join characters as c on c.guid = g.leaderguid where g.leaderguid in
                                      (SELECT guid from characters where BINARY name like '%$search_value%') ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
                $query_count = $sqlc->query("SELECT 1 from guild where leaderguid in (select guid from characters where BINARY name like '%$search_value%')");
                break;

            case "guildid" :
                if (is_numeric($search_value));
                else
                    redirect("guild.php?error=5&amp;realm=$realmid");
                $query = $sqlc->query("SELECT g.guildid as gid, BINARY g.name,g.leaderguid as lguid,
                                      (SELECT BINARY name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction,
                                      (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount
                                      FROM guild as g left outer join characters as c on c.guid = g.leaderguid
                                      where g.guildid = '$search_value' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
                $query_count = $sqlc->query("SELECT 1 from guild where guildid = '$search_value'");
                break;

            default :
                    redirect("guild.php?error=2&amp;realm=$realmid");
        }
    }
    else
    {
        $query = $sqlc->query("SELECT g.guildid as gid, BINARY g.name,g.leaderguid as lguid, (SELECT BINARY name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction, (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount FROM guild as g left outer join characters as c on c.guid = g.leaderguid ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
        $query_count = $sqlc->query("SELECT 1 from guild");
    }
    $all_record = $sqlc->num_rows($query_count);
    //==========================Browse/Search Guilds CHECK end===================
    //==========================Browse/Search Guilds=============================

    $output .="
                <center>
                    <table class=\"top_hidden\">
                        <tr align=\"left\">
                            <td>
                                <table class=\"hidden\">
                                    <tr>
                                        <td>
                                            <form action=\"guild.php?realm=$realmid\" method=\"get\" name=\"form\">
                                                <input type=\"hidden\" name=\"error\" value=\"4\" />
                                                <input type=\"text\" size=\"24\" name=\"search_value\" value=\"{$search_value}\" />
                                                <select name=\"search_by\">
                                                    <option value=\"name\"".($search_by == 'name' ? " selected=\"selected\"" : "").">{$lang_guild['by_name']}</option>
                                                    <option value=\"leadername\"".($search_by == 'leadername' ? " selected=\"selected\"" : "").">{$lang_guild['by_guild_leader']}</option>
                                                    <option value=\"guildid\"".($search_by == 'guildid' ? " selected=\"selected\"" : "").">{$lang_guild['by_id']}</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td width=\"300\">";

    makebutton($lang_global['search'], "javascript:do_submit()",80);

    ($search_by &&  $search_value) ? makebutton($lang_guild['show_guilds'], "guild.php?realm=$realmid\" type=\"def", 130) : $output .= "";

    $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </center>";
    //==========================top tage navigaion ENDS here ====================
    $output .= "
                <center>
                    <fieldset>
                        <legend>{$lang_guild['browse_guilds']}</legend>
                        <table class=\"lined\" align=\"center\">
                            <tr class=\"hidden\">
                                <td colspan=\"6\" class=\"hidden\" align=\"right\" width=\"25%\">";
    $output .= generate_pagination("guild.php?action=brows_guilds&amp;realm=$realmid&amp;order_by=$order_by&amp;".($search_value && $search_by ? "search_by=$search_by&amp;search_value=$search_value&amp" : "")."dir=".(($dir) ? 0 : 1)."", $all_record, $itemperpage, $start);
    $output .= "
                                </td>
                            </tr>
                            <tr>
                                <th width=\"5%\"><a href=\"guild.php?order_by=gid&amp;realm=$realmid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='gid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['id']}</a></th>
                                <th width=\"30%\"><a href=\"guild.php?order_by=name&amp;realm=$realmid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['guild_name']}</a></th>
                                <th width=\"20%\"><a href=\"guild.php?order_by=lname&amp;realm=$realmid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='lname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['guild_leader']}</a></th>
                                <th width=\"10%\"><a href=\"guild.php?order_by=lfaction&amp;realm=$realmid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='lfaction' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['guild_faction']}</a></th>
                                <th width=\"15%\"><a href=\"guild.php?order_by=tot_chars&amp;realm=$realmid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='tot_chars' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['tot_members']}</a></th>
                                <th width=\"20%\"><a href=\"guild.php?order_by=createdate&amp;realm=$realmid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='createdate' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['create_date']}</a></th>
                            </tr>";
    while ($data = $sqlr->fetch_row($query))
    {
        $result = $sqlr->query("SELECT gmlevel FROM account_access WHERE id ='$data[7]'");
        $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
        $output .= "
                            <tr>
                                <td>$data[0]</td>";
        $output .= ($user_lvl >= $action_permission['update']) ? "<td><a href=\"guild.php?action=view_guild&amp;realm=$realmid&amp;error=3&amp;id=$data[0]\">".htmlentities($data[1])."</a></td>" : "<td>".htmlentities($data[1])."</td>";
        $output .= ($user_lvl < $owner_gmlvl ) ? "<td>".htmlentities($data[3])."</td>" : "<td><a href=\"char.php?id=$data[2]&amp;realm=$realmid\">".htmlentities($data[3])."</a></td>";
        $output .= "
                                <td><img src=\"img/".($data[4]==0 ? "alliance" : "horde")."_small.gif\" alt=\"\" /></td>
                                <td>$data[5]</td>
                                <td class=\"small\">".date('o-m-d', $data[6])."</td>
                            </tr>";
    }
    $output .= "
                            <tr>
                                <td colspan=\"6\" class=\"hidden\" align=\"right\" width=\"25%\">".generate_pagination("guild.php?action=brows_guilds&amp;realm=$realmid&amp;order_by=$order_by&amp;".($search_value && $search_by ? "search_by=$search_by&amp;search_value=$search_value&amp" : "")."dir=".(($dir) ? 0 : 1)."", $all_record, $itemperpage, $start)."</td>
                            </tr>
                            <tr>
                                <td colspan=\"6\" class=\"hidden\" align=\"right\">{$lang_guild['tot_guilds']} : $all_record</td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                </center>";

}
//==========================Browse/Search Guilds end=========================

function count_days( $a, $b )
{
    $gd_a = getdate( $a );
    $gd_b = getdate( $b );
    $a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
    $b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );

    return round( abs( $a_new - $b_new ) / 86400 );
}


//#############################################################################
// VIEW GUILD
//#############################################################################
function view_guild()
{
    global $lang_guild, $lang_global, $output, $realm_db, $characters_db, $mmfpm_db, $realm_id, $itemperpage,
            $action_permission, $user_lvl, $user_id, $showcountryflag;

    if(!isset($_GET['id']))
        redirect("guild.php?error=1&amp;realm=$realmid");

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    if (empty($_GET['realm']))
        $realmid = $realm_id;
    else
    {
        $realmid = $sqlr->quote_smart($_GET['realm']);
        if (!is_numeric($realmid))
            $realmid = $realm_id;
    }

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

    $guild_id = $sqlc->quote_smart($_GET['id']);
    if(is_numeric($guild_id));
    else
        redirect("guild.php?error=6&amp;realm=$realmid");

    //==========================SQL INGUILD and GUILDLEADER======================
    $q_inguild = $sqlc->query("select 1 from guild_member where guildid = '$guild_id' and guid in (select guid from characters where account = '$user_id')");
    $inguild = $sqlc->result($q_inguild, 0, '1');
    if ( $user_lvl < $action_permission['update'] && !$inguild )
        redirect("guild.php?error=6&amp;realm=$realmid");

    $q_amIguildleader = $sqlc->query("select 1 from guild where guildid = '$guild_id' and leaderguid in (select guid from characters where account = '$user_id')");
    $amIguildleader = $sqlc->result($q_amIguildleader, 0, '1');

    $q_guildmemberCount = $sqlc->query("SELECT 1 from guild_member where guildid = '$guild_id'");
    $guildmemberCount = $sqlc->num_rows($q_guildmemberCount);
    //====================SQL INGUILD and GUILDLEADER end========================

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "mrank";
    if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by))
        $order_by="mrank";

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (!preg_match("/^[01]{1}$/", $dir))
        $dir=1;

    $order_dir = ($dir) ? "ASC" : "DESC";
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $query = $sqlc->query("SELECT guildid, BINARY name, BINARY info, BINARY MOTD, createdate,
                          (select count(*) from guild_member where guildid = '$guild_id') as mtotal,
                          (select count(*) from guild_member where guildid = '$guild_id' and guid in
                          (select guid from characters where online = 1)) as monline
                          FROM guild WHERE guildid = '$guild_id'");
    $guild_data = $sqlc->fetch_row($query);

    $output .= "
                <center>
                    <div id=\"tab_content\">
                        <div id=\"tab\">
                            <ul>
                                <li id=\"selected\"><a href=\"guild.php?action=view_guild&amp;realm=$realmid&amp;error=3&amp;id=$guild_data[0]\">".$lang_guild['members']."</a></li>
                                <li><a href=\"guildbank.php?id=$guild_id&amp;realm=$realmid\">".$lang_guild['guildbank']."</a></li>
                            </ul>
                        </div>
                        <script type=\"text/javascript\">
                            answerbox.btn_ok='{$lang_global['yes']}';
                            answerbox.btn_cancel='{$lang_global['no']}';
                        </script>
                        <div id=\"tab_content2\">
                            <table class=\"hidden\" style=\"width: 100%;\">
                                <tr>
                                    <td>
                                        <table class=\"lined\"style=\"width: 100%;\">
                                            <tr>
                                                <td width=\"25%\"><b>{$lang_guild['create_date']}:</b><br />".date('o-m-d', $guild_data[4])."</td>
                                                <td width=\"50%\" class=\"bold\">$guild_data[1]</td>
                                                <td width=\"25%\"><b>{$lang_guild['tot_m_online']}:</b><br />$guild_data[6] / $guild_data[5]</td>
                                            </tr>";
    if ($guild_data[2] != '')
        $output .= "
                                            <tr>
                                                <td colspan=\"3\"><b>{$lang_guild['info']}:</b><br />$guild_data[2]</td>
                                            </tr>";
    if ($guild_data[3] != '')
        $output .= "
                                            <tr>
                                                <td colspan=\"3\"><b>{$lang_guild['motd']}:</b><br />$guild_data[3]</td>
                                            </tr>";
    $output .="
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"right\">".generate_pagination("guild.php?action=view_guild&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=$order_by&amp;dir=".(($dir) ? 0 : 1)."", $guildmemberCount, $itemperpage, $start)."</td>
                                </tr>
                                <tr>
                                    <td>
                                        <table class=\"lined\" style=\"width: 100%;\">
                                            <tr>
                                                <th width=\"1%\">{$lang_guild['remove']}</th>
                                                <th width=\"20%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=cname&amp;start=$start&amp;dir=$dir\">".($order_by=='cname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['name']}</a></th>
                                                <th width=\"1%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=crace&amp;start=$start&amp;dir=$dir\">".($order_by=='crace' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['race']}</a></th>
                                                <th width=\"1%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=class&amp;start=$start&amp;dir=$dir\">".($order_by=='cclass' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['class']}</a></th>
                                                <th width=\"1%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=clevel&amp;start=$start&amp;dir=$dir\">".($order_by=='clevel' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['level']}</a></th>
                                                <th width=\"23%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=mrank&amp;start=$start&amp;dir=$dir\">".($order_by=='mrank' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['rank']}</a></th>
                                                <th width=\"25%\">{$lang_guild['pnote']}</th>
                                                <th width=\"25%\">{$lang_guild['offnote']}</th>
                                                <th width=\"1%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=clogout&amp;start=$start&amp;dir=$dir\">".($order_by=='clogout' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['llogin']}</a></th>
                                                <th width=\"1%\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=conline&amp;start=$start&amp;dir=$dir\">".($order_by=='conline' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_guild['online']}</a></th>";
    if ($showcountryflag)
    {
        require_once 'libs/misc_lib.php';

        $sqlm = new SQL;
        $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

        $output .="
                                                <th width=\"1%\">{$lang_global['country']}</th>";
    }

    $output .="
                                            </tr>";
    $members = $sqlc->query("SELECT gm.guid as cguid, BINARY c.name as cname, c.`race` as crace ,c.`class` as cclass,
                            c.`level` AS clevel,
                            gm.rank AS mrank, (SELECT BINARY rname FROM guild_rank WHERE guildid ='$guild_id' AND rid = mrank) AS rname,
                            BINARY gm.Pnote, BINARY gm.OFFnote, gender,
                            c.`online` as conline, c.`account`, c.`logout_time` as clogout
                            FROM guild_member as gm left outer join characters as c on c.guid = gm.guid
                            WHERE gm.guildid = '$guild_id' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");

    while ($member = $sqlr->fetch_row($members))
    {
        $result = $sqlr->query("SELECT gmlevel FROM account_access WHERE id ='$member[11]'");
        $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
        $output .= "
                                            <tr>";
        // gm, gildleader or own account! are allowed to remove from guild
        $output .= ($user_lvl >= $action_permission['delete'] || $amIguildleader || $member[11] == $user_id) ? "
                                                <td>
                                                    <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: &lt;font color=white&gt;{$member[1]}&lt;/font&gt;&lt;br /&gt;{$lang_global['are_you_sure']}', 'guild.php?action=rem_char_from_guild&amp;realm=$realmid&amp;id=$member[0]&amp;guld_id=$guild_id');\" style=\"cursor:pointer;\" />
                                                </td>" : "
                                                <td>
                                                </td>";
        $output .= ($user_lvl < $owner_gmlvl ) ? "
                                                <td>".htmlentities($member[1])."</td>" : "
                                                <td><a href=\"char.php?id=$member[0]&amp;realm=$realmid\">".htmlentities($member[1])."</a></td>";
        $output .= "
                                                <td><img src='img/c_icons/{$member[2]}-{$member[9]}.gif' onmousemove='toolTip(\"".char_get_race_name($member[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                                                <td><img src='img/c_icons/{$member[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($member[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                                                <td>".char_get_level_color($member[4])."</td>
                                                <td>".htmlentities($member[6])." (".$member[5].")</td>
                                                <td>".htmlentities($member[7])."</td>
                                                <td>".htmlentities($member[8])."</td>
                                                <td>".get_days_with_color($member[12])."</td>
                                                <td>".(($member[10]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>";

        if ($showcountryflag)
        {
            $country = misc_get_country_by_account($member[11], $sqlr, $sqlm);
            $output .="
                                                <td>".(($country['code']) ? "<img src='img/flags/".$country['code'].".png' onmousemove='toolTip(\"".($country['country'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />" : "-")."</td>";
        }

        $output .="
                                            </tr>";
    }
    unset($member);
    $output .= "
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"right\">".generate_pagination("guild.php?action=view_guild&amp;error=3&amp;realm=$realmid&amp;id=$guild_id&amp;order_by=$order_by&amp;dir=".!$dir, $guildmemberCount, $itemperpage, $start)."</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br />
                    <table class=\"hidden\">
                        <tr>
                            <td>";
    if ($user_lvl >= $action_permission['delete'] || $amIguildleader)
    {
        makebutton($lang_guild['del_guild'], "guild.php?action=del_guild&amp;realm=$realmid&amp;id=$guild_id\" type=\"wrn", 130);

        $output .= "
                            </td>
                            <td>";
    }
    makebutton($lang_guild['show_guilds'], "guild.php?realm=$realmid\" type=\"def", 130);

    $output .= "
                            </td>
                        </tr>
                    </table>
                </center>";
}


//#############################################################################
// ARE YOU SURE  YOU WOULD LIKE TO OPEN YOUR AIRBAG?
//#############################################################################
function del_guild()
{
    global $lang_guild, $lang_global, $output, $characters_db, $realm_id,
            $action_permission, $user_lvl, $user_id;

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    if (empty($_GET['realm']))
        $realmid = $realm_id;
    else
    {
        $realmid = $sqlr->quote_smart($_GET['realm']);
        if (!is_numeric($realmid))
            $realmid = $realm_id;
    }

    if(isset($_GET['id']))
        $id = $_GET['id'];
    else
        redirect("guild.php?error=1&amp;realm=$realmid");
    if (is_numeric($id));
    else
        redirect("guild.php?error=5&amp;realm=$realmid");

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    $q_amIguildleader = $sqlc->query("select 1 from guild where guildid = '$id' and leaderguid in (select guid from characters where account = '$user_id')");
    $amIguildleader = $sqlc->result($q_amIguildleader, 0, '1');

    if ($user_lvl < $action_permission['delete'] && !$amIguildleader)
        redirect("guild.php?error=6&amp;realm=$realmid");

    $output .= "
                <center>
                    <h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1>
                    <br />
                    <font class=\"bold\">{$lang_guild['guild_id']}: $id {$lang_global['will_be_erased']}</font>
                    <br /><br />
                    <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                        <input type=\"hidden\" name=\"type\" value=\"guild\" />
                        <input type=\"hidden\" name=\"check\" value=\"-$id\" />
                        <input type=\"hidden\" name=\"override\" value=\"1\" />
                        <table class=\"hidden\">
                            <tr>
                                <td>";
    makebutton($lang_global['yes'], "javascript:do_submit()\" type=\"wrn",130);
    $output .= "
                                </td>
                                <td>";
    makebutton($lang_global['no'], "guild.php?action=view_guild&amp;realm=$realmid&amp;id=$id\" type=\"def",130);
    $output .= '
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>
                <br />';
}


//#############################################################################
//REMOVE CHAR FROM GUILD
//#############################################################################
function rem_char_from_guild(&$sqlr, &$sqlc)
{
    global $characters_db, $realm_id, $user_lvl, $user_id, $action_permission;

    // this is multi realm support, as of writing still under development
    //  this page is already implementing it
    if (empty($_GET['realm']))
        $realmid = $realm_id;
    else
    {
        $realmid = $sqlr->quote_smart($_GET['realm']);
        if (is_numeric($realmid))
            $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
        else
            $realmid = $realm_id;
    }

    if(isset($_GET['id']))
        $guid = $_GET['id'];
    else
        redirect("guild.php?error=1&amp;realm=$realmid");

    if (is_numeric($guid));
    else
        redirect("guild.php?error=5&amp;realm=$realmid");

    if(isset($_GET['guld_id']))
        $guld_id = $_GET['guld_id'];
    else
        redirect("guild.php?error=1&amp;realm=$realmid");

    if (is_numeric($guld_id));
    else
        redirect("guild.php?error=5&amp;realm=$realmid");

    $q_amIguildleaderOrSelfRemoval = $sqlc->query("select 1 from guild as g left outer join guild_member as gm on gm.guildid = g.guildid
                                                   where g.guildid = '$guld_id' and
                                                   (g.leaderguid in (select guid from characters where account = '$user_id')
                                                   or gm.guid in (select guid from characters where account = '$user_id' and guid = '$guid'))");

    $amIguildleaderOrSelfRemoval = $sqlc->result($q_amIguildleaderOrSelfRemoval, 0, '1');
    if ($user_lvl < $action_permission['delete'] && !$amIguildleaderOrSelfRemoval )
        redirect("guild.php?error=6&amp;realm=$realmid");
    $sqlc->query("DELETE FROM guild_member WHERE guid = '$guid'");
    redirect("guild.php?action=view_guild&amp;realm=$realmid&amp;id=$guld_id");
}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

$lang_guild = lang_guild();

switch ($err)
{
    case 1:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_global['err_empty_fields']}</font>
          </h1>";
        break;
    case 2:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_global['err_no_search_passed']}</font>
          </h1>";
        break;
    case 3: //keep blank
        break;
    case 4:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_guild['guild_search_result']}:</font>
          </h1>";
        break;
    case 5:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_global['err_invalid_input']}:</h1>";
        break;
    case 6:
        $output .= "
          <h1>
            <font class=\"error\">{$lang_global['err_no_permission']}:</font>
          </h1>";
        break;
    default: //no error
}

$output .= "
          <h1>{$lang_guild['browse_guilds']}</h1>";


unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('view_guild' == $action)
    view_guild();
elseif ('del_guild' == $action)
    del_guild();
elseif ('rem_char_from_guild' == $action)
    rem_char_from_guild($sqlr, $sqlc);
else
    browse_guilds($sqlr, $sqlc);

unset($action);
unset($action_permission);
unset($lang_guild);

require_once 'footer.php';


?>
