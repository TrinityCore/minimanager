<?php

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/archieve_lib.php';

valid_login($action_permission['read']);

function browse_achievements(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char, $mmfpm_db, $characters_db, $realm_id,
           $action_permission, $user_lvl, $itemperpage, $achievement_datasite, $gm_online;

    wowhead_tt();

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    $realmid = (!empty($_GET['realm']) && is_numeric($_GET['realm'])) ? intval($_GET['realm']) : $realm_id;
    if (!isset($characters_db[$realmid]))
        $realmid = $realm_id;

    $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

    $start = (isset($_GET['start']) && is_numeric($_GET['start'])) ? intval($_GET['start']) : 0;
    if ($start < 0) $start = 0;

    $search_by = isset($_GET['search_by']) ? $_GET['search_by'] : 'char';
    $search_value = isset($_GET['search_value']) ? trim((string)$_GET['search_value']) : '';
    $search_url = '';

    $where_clauses = array("c.name <> ''");
    if (isset($gm_online) && $gm_online == 0)
    {
        $gm_accs = array(0);
        $q_gm = $sqlr->query("SELECT AccountID FROM account_access WHERE SecurityLevel > 0");
        if ($q_gm)
        {
            while ($gr = $sqlr->fetch_row($q_gm))
            {
                list($gacc) = $gr;
                $gm_accs[] = intval($gacc);
            }
        }
        $where_clauses[] = "c.account NOT IN (" . implode(',', $gm_accs) . ")";
    }

    if ($search_value !== '')
    {
        $search_url = '&amp;search_by=' . urlencode($search_by) . '&amp;search_value=' . urlencode($search_value);
        if ($search_by === 'char')
        {
            $val_safe = $sqlc->quote_smart($search_value);
            $where_clauses[] = "c.name LIKE '%" . $val_safe . "%'";
        }
        elseif ($search_by === 'guild')
        {
            $val_safe = $sqlc->quote_smart($search_value);
            $where_clauses[] = "g.name LIKE '%" . $val_safe . "%'";
        }
        elseif ($search_by === 'achieve')
        {
            if (is_numeric($search_value))
                $where_clauses[] = "ca.achievement = " . intval($search_value);
            else
            {
                $val_m = $sqlm->quote_smart($search_value);
                $q_aids = $sqlm->query("SELECT id FROM dbc_achievement WHERE name01 LIKE '%" . $val_m . "%'");
                $aids = array(0);
                if ($q_aids)
                {
                    while ($ar = $sqlm->fetch_row($q_aids))
                    {
                        list($aid) = $ar;
                        $aids[] = intval($aid);
                    }
                }
                $where_clauses[] = "ca.achievement IN (" . implode(',', $aids) . ")";
            }
        }
    }

    $where_sql = ' WHERE ' . implode(' AND ', $where_clauses);

    $q_cnt = $sqlc->query("SELECT COUNT(*) FROM character_achievement ca
                           INNER JOIN characters c ON ca.guid = c.guid
                           LEFT JOIN guild_member gm ON c.guid = gm.guid
                           LEFT JOIN guild g ON gm.guildid = g.guildid" . $where_sql);
    $all_record = $sqlc->result($q_cnt, 0);

    $q_list = $sqlc->query("SELECT ca.guid, ca.achievement, ca.date,
                            BINARY c.name AS char_name, c.race, c.class, c.level, c.gender,
                            g.guildid, BINARY g.name AS guild_name
                            FROM character_achievement ca
                            INNER JOIN characters c ON ca.guid = c.guid
                            LEFT JOIN guild_member gm ON c.guid = gm.guid
                            LEFT JOIN guild g ON gm.guildid = g.guildid" . $where_sql . "
                            ORDER BY ca.date DESC, ca.achievement DESC
                            LIMIT " . $start . ", " . $itemperpage);

    $output .= '
                <center>
                    <table class="top_hidden" style="width: 90%;">
                        <tr>
                            <td align="left">
                                <form action="achievements.php" method="get" name="form">
                                    <input type="hidden" name="realm" value="'.$realmid.'" />
                                    <input type="text" size="24" maxlength="50" name="search_value" value="'.htmlspecialchars($search_value).'" placeholder="'.$lang_global['search_by'].'" />
                                    <select name="search_by" id="ach_search_by">
                                        <option value="char"'.($search_by === 'char' ? ' selected="selected"' : '').'>'.$lang_global['character'].'</option>
                                        <option value="guild"'.($search_by === 'guild' ? ' selected="selected"' : '').'>'.$lang_global['guild'].'</option>
                                        <option value="achieve"'.($search_by === 'achieve' ? ' selected="selected"' : '').'>Logro (Nombre/ID)</option>
                                    </select>
                                </form>
                            </td>
                            <td align="left">';
    makebutton($lang_global['search'], 'javascript:do_submit()', 80);
    if ($search_value !== '')
        makebutton($lang_global['back'], 'achievements.php?realm='.$realmid, 80);
    $output .= '
                            </td>
                            <td align="right" width="40%">
                                Total : '.$all_record.'<br /><br />
                                '.generate_pagination('achievements.php?realm='.$realmid.$search_url, $all_record, $itemperpage, $start).'
                            </td>
                        </tr>
                    </table>
                    <table class="lined" style="width: 90%;">
                        <tr>
                            <th width="4%">'.$lang_global['icon'].'</th>
                            <th width="36%" align="left">'.$lang_global['achievement'].'</th>
                            <th width="4%">'.$lang_global['pts'].'</th>
                            <th width="22%">'.$lang_global['character'].'</th>
                            <th width="20%">'.$lang_global['guild'].'</th>
                            <th width="14%">'.$lang_global['date'].'</th>
                        </tr>';

    if ($q_list && $sqlc->num_rows($q_list) > 0)
    {
        while ($row = $sqlc->fetch_assoc($q_list))
        {
            $ach_id = intval($row['achievement']);
            $details = achieve_get_details($ach_id, $sqlm);
            $ach_name = !empty($details['name01']) ? str_replace('&', '&amp;', $details['name01']) : ('Achievement #'.$ach_id);
            $ach_desc = !empty($details['description01']) ? str_replace('&', '&amp;', $details['description01']) : '';
            $ach_pts  = isset($details['rewpoints']) ? intval($details['rewpoints']) : 0;
            $ach_icon = achieve_get_icon($ach_id, $sqlm);

            $char_link = '<a href="char.php?id='.$row['guid'].'&amp;realm='.$realmid.'">'.htmlentities($row['char_name']).'</a>';
            $icons_html = '<img src="img/c_icons/'.$row['race'].'-'.$row['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($row['race']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" /> '.
                          '<img src="img/c_icons/'.$row['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($row['class']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" /> '.
                          '<span class="small">('.$row['level'].')</span>';

            if (!empty($row['guildid']) && !empty($row['guild_name']))
                $guild_html = '&lt;<a href="guild.php?action=view_guild&amp;error=3&amp;realm='.$realmid.'&amp;id='.$row['guildid'].'">'.htmlentities($row['guild_name']).'</a>&gt;';
            else
                $guild_html = '-';

            $date_str = ($row['date'] > 0) ? date('Y-m-d H:i', $row['date']) : '-';

            $output .= '
                        <tr>
                            <td>
                                <a href="'.$achievement_datasite.$ach_id.'" target="_blank">
                                    <img src="'.$ach_icon.'" width="32" height="32" class="icon_border_0" alt="" />
                                </a>
                            </td>
                            <td align="left">
                                <a href="'.$achievement_datasite.$ach_id.'" target="_blank"><b>'.$ach_name.'</b></a>
                                '.($ach_desc !== '' ? '<br /><span class="small">'.$ach_desc.'</span>' : '').'
                            </td>
                            <td>'.$ach_pts.' <img src="img/money_achievement.gif" alt="" /></td>
                            <td>'.$icons_html.' '.$char_link.'</td>
                            <td>'.$guild_html.'</td>
                            <td class="small">'.$date_str.'</td>
                        </tr>';
        }
    }
    else
    {
        $output .= '
                        <tr>
                            <td colspan="6">'.$lang_global['no_recent_achieves'].'</td>
                        </tr>';
    }

    $output .= '
                        <tr>
                            <td colspan="6" class="hidden" align="right">
                                '.generate_pagination('achievements.php?realm='.$realmid.$search_url, $all_record, $itemperpage, $start).'
                            </td>
                        </tr>
                    </table>
                    <br />
                </center>';
}

$lang_char = lang_char();

$output .= '
        <div class="top">
            <h1>'.$lang_char['achievements'].'</h1>
        </div>';

browse_achievements($sqlr, $sqlc);

unset($action_permission);
unset($lang_char);

require_once 'footer.php';

?>
