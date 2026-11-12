<?php

require_once("header.php");
valid_login($action_permission['read']);

function show_quest_tracker()
{
    global $lang_global, $output, $characters_db, $world_db, $realm_id, $realmid, $itemperpage, $action_permission, $user_lvl, $quest_datasite;
    valid_login($action_permission['read']);

    $r_id = isset($realmid) ? $realmid : (isset($realm_id) ? $realm_id : array_key_first($characters_db));

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$r_id]['addr'], $characters_db[$r_id]['user'], $characters_db[$r_id]['pass'], $characters_db[$r_id]['name']);

    $sqlw = new SQL;
    $sqlw->connect($world_db[$r_id]['addr'], $world_db[$r_id]['user'], $world_db[$r_id]['pass'], $world_db[$r_id]['name']);

    $start = (isset($_GET['start']) && is_numeric($_GET['start'])) ? intval($_GET['start']) : 0;

    $allowed_order = ['id', 'total_accepted', 'abandoned', 'completed', 'gm_completed', 'abandon_rate'];
    $order_by = (isset($_GET['order_by']) && in_array($_GET['order_by'], $allowed_order)) ? $_GET['order_by'] : 'abandoned';

    $dir = (isset($_GET['dir']) && $_GET['dir'] == '1') ? 1 : 0;
    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $next_dir = ($dir) ? 0 : 1;

    $search_val = isset($_GET['search_value']) ? trim((string)$_GET['search_value']) : '';
    $where_sql = '';
    $search_url = '';

    if ($search_val !== '')
    {
        $search_url = '&amp;search_value=' . urlencode($search_val);
        if (is_numeric($search_val))
        {
            $where_sql = ' WHERE qt.id = ' . intval($search_val);
        }
        else
        {
            $q_esc = $sqlw->quote_smart($search_val);
            $q_titles = $sqlw->query("SELECT ID FROM quest_template WHERE LogTitle LIKE '%$q_esc%' LIMIT 200");
            $matched_qids = [0];
            while ($qt_row = $sqlw->fetch_row($q_titles))
                $matched_qids[] = intval($qt_row[0]);
            $where_sql = ' WHERE qt.id IN (' . implode(',', $matched_qids) . ')';
        }
    }

    $q_count = $sqlc->query("SELECT COUNT(DISTINCT qt.id) FROM quest_tracker qt" . $where_sql);
    $all_record = $q_count ? intval($sqlc->result($q_count, 0)) : 0;

    $sql_main = "SELECT
                    qt.id,
                    COUNT(*) AS total_accepted,
                    SUM(CASE WHEN qt.quest_abandon_time IS NOT NULL THEN 1 ELSE 0 END) AS abandoned,
                    SUM(CASE WHEN qt.quest_complete_time IS NOT NULL AND qt.completed_by_gm = 0 THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN qt.completed_by_gm = 1 THEN 1 ELSE 0 END) AS gm_completed,
                    ROUND((SUM(CASE WHEN qt.quest_abandon_time IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) AS abandon_rate,
                    MAX(COALESCE(qt.quest_abandon_time, qt.quest_complete_time, qt.quest_accept_time)) AS last_activity
                 FROM quest_tracker qt
                 $where_sql
                 GROUP BY qt.id
                 ORDER BY $order_by $order_dir
                 LIMIT $start, $itemperpage";

    $result = $sqlc->query($sql_main);

    $output .= "
        <center>
            <table class=\"top_hidden\">
                <tr>
                    <td>";
    if ($search_val !== '')
        makebutton("Mostrar Todas", "quest_tracker.php", 130);
    if ($user_lvl >= $action_permission['delete'])
        makebutton("Limpiar Completadas", "quest_tracker.php?action=purge_completed\" type=\"wrn", 160);
    makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def", 130);

    $output .= "
                    </td>
                    <td align=\"right\" rowspan=\"2\">
                        Total Misiones Rastreadas : <b>$all_record</b><br /><br />
                        " . generate_pagination("quest_tracker.php?order_by=$order_by&amp;dir=$dir$search_url", $all_record, $itemperpage, $start) . "
                    </td>
                </tr>
                <tr align=\"left\">
                    <td>
                        <form action=\"quest_tracker.php\" method=\"get\" name=\"qt_search\" style=\"display:inline;\">
                            <input type=\"text\" size=\"28\" maxlength=\"60\" name=\"search_value\" value=\"" . htmlspecialchars($search_val) . "\" placeholder=\"Buscar por ID o Título de misión...\" />
                            <input type=\"submit\" value=\"{$lang_global['search']}\" class=\"button\" />
                        </form>
                    </td>
                </tr>
            </table>
            <script type=\"text/javascript\">
                answerbox.btn_ok='{$lang_global['yes_low']}';
                answerbox.btn_cancel='{$lang_global['no']}';
                var del_qt = 'quest_tracker.php?action=del_quest&amp;qid=';
            </script>
            <table class=\"lined\">
                <tr>
                    <th width=\"5%\">{$lang_global['delete_short']}</th>
                    <th width=\"8%\"><a href=\"quest_tracker.php?order_by=id&amp;dir=$next_dir$search_url\"" . ($order_by == 'id' ? " class=\"$order_dir\"" : "") . ">Quest ID</a></th>
                    <th width=\"33%\">Título de Misión</th>
                    <th width=\"10%\"><a href=\"quest_tracker.php?order_by=total_accepted&amp;dir=$next_dir$search_url\"" . ($order_by == 'total_accepted' ? " class=\"$order_dir\"" : "") . ">Aceptadas</a></th>
                    <th width=\"10%\"><a href=\"quest_tracker.php?order_by=abandoned&amp;dir=$next_dir$search_url\"" . ($order_by == 'abandoned' ? " class=\"$order_dir\"" : "") . ">Abandonadas</a></th>
                    <th width=\"10%\"><a href=\"quest_tracker.php?order_by=completed&amp;dir=$next_dir$search_url\"" . ($order_by == 'completed' ? " class=\"$order_dir\"" : "") . ">Completadas</a></th>
                    <th width=\"9%\"><a href=\"quest_tracker.php?order_by=gm_completed&amp;dir=$next_dir$search_url\"" . ($order_by == 'gm_completed' ? " class=\"$order_dir\"" : "") . ">Por GM</a></th>
                    <th width=\"15%\"><a href=\"quest_tracker.php?order_by=abandon_rate&amp;dir=$next_dir$search_url\"" . ($order_by == 'abandon_rate' ? " class=\"$order_dir\"" : "") . ">% Abandono</a></th>
                </tr>";

    if ($result && $sqlc->num_rows($result))
    {
        while ($row = $sqlc->fetch_assoc($result))
        {
            $qid = intval($row['id']);
            $q_info = $sqlw->query("SELECT LogTitle AS Title, QuestLevel FROM quest_template WHERE ID = $qid LIMIT 1");
            if ($q_info && $sqlw->num_rows($q_info))
            {
                $q_data = $sqlw->fetch_assoc($q_info);
                $q_title = htmlspecialchars($q_data['Title']) . " <small>(Lvl {$q_data['QuestLevel']})</small>";
            }
            else
                $q_title = "<i>Misión desconocida (#$qid)</i>";

            $rate = floatval($row['abandon_rate']);
            $rate_color = ($rate >= 50) ? '#ff5555' : (($rate >= 25) ? '#ffaa00' : '#55ff55');

            $output .= "
                <tr>
                    <td>";
            if ($user_lvl >= $action_permission['delete'])
                $output .= "
                        <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('Borrar registros de Quest #$qid?<br />{$lang_global['are_you_sure']}', del_qt + '$qid');\" style=\"cursor:pointer;\" />";
            $output .= "
                    </td>
                    <td><a href=\"{$quest_datasite}{$qid}\" target=\"_blank\">$qid</a></td>
                    <td align=\"left\"><a href=\"{$quest_datasite}{$qid}\" target=\"_blank\">$q_title</a></td>
                    <td>{$row['total_accepted']}</td>
                    <td><b>{$row['abandoned']}</b></td>
                    <td>{$row['completed']}</td>
                    <td>{$row['gm_completed']}</td>
                    <td><b style=\"color:$rate_color;\">{$rate}%</b></td>
                </tr>";
        }
    }
    else
    {
        $output .= "
                <tr>
                    <td colspan=\"8\">{$lang_global['err_no_result']}</td>
                </tr>";
    }

    $output .= "
                <tr>
                    <td colspan=\"8\" align=\"right\" class=\"hidden\">Total Misiones Rastreadas : $all_record</td>
                </tr>
            </table>
            <br />
        </center>";
}

function del_quest_logs()
{
    global $characters_db, $realm_id, $realmid, $action_permission;
    valid_login($action_permission['delete']);

    $r_id = isset($realmid) ? $realmid : (isset($realm_id) ? $realm_id : array_key_first($characters_db));
    $sqlc = new SQL;
    $sqlc->connect($characters_db[$r_id]['addr'], $characters_db[$r_id]['user'], $characters_db[$r_id]['pass'], $characters_db[$r_id]['name']);

    if (isset($_GET['qid']) && is_numeric($_GET['qid']))
    {
        $qid = intval($_GET['qid']);
        $sqlc->query("DELETE FROM quest_tracker WHERE id = $qid");
    }
    redirect("quest_tracker.php");
}

function purge_completed_logs()
{
    global $characters_db, $realm_id, $realmid, $action_permission;
    valid_login($action_permission['delete']);

    $r_id = isset($realmid) ? $realmid : (isset($realm_id) ? $realm_id : array_key_first($characters_db));
    $sqlc = new SQL;
    $sqlc->connect($characters_db[$r_id]['addr'], $characters_db[$r_id]['user'], $characters_db[$r_id]['pass'], $characters_db[$r_id]['name']);

    $sqlc->query("DELETE FROM quest_tracker WHERE quest_complete_time IS NOT NULL AND quest_abandon_time IS NULL AND completed_by_gm = 0");
    redirect("quest_tracker.php");
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action === 'del_quest')
    del_quest_logs();
elseif ($action === 'purge_completed')
    purge_completed_logs();
else
    show_quest_tracker();

require_once("footer.php");
?>
