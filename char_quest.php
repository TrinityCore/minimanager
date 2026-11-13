<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS QUESTS
//########################################################################################################################
function get_full_quest_chain($qid, &$addon_map)
{
    $chain = array($qid);
    $visited = array($qid => true);
    $curr = $qid;
    $guard = 0;
    while ($guard < 50)
    {
        $p = isset($addon_map[$curr]['prev']) ? intval($addon_map[$curr]['prev']) : 0;
        if ($p <= 0 || isset($visited[$p])) break;
        array_unshift($chain, $p);
        $visited[$p] = true;
        $curr = $p;
        $guard++;
    }
    $curr = $qid;
    $guard = 0;
    while ($guard < 50)
    {
        $n = isset($addon_map[$curr]['next']) ? intval($addon_map[$curr]['next']) : 0;
        if ($n <= 0 || isset($visited[$n])) break;
        array_push($chain, $n);
        $visited[$n] = true;
        $curr = $n;
        $guard++;
    }
    return $chain;
}

function render_quest_chains_tree(&$chains_list, &$meta_map, &$char_states, $quest_datasite)
{
    if (empty($chains_list))
        return '';

    $html = '
                            <table class="lined" style="width: 730px; margin-bottom: 12px;">
                                <tr>
                                    <th colspan="4" align="left">&nbsp;'.$lang_global['quest_chains'].' ('.count($chains_list).' '.$lang_global['chains_detected'].')</th>
                                </tr>
                                <tr>
                                    <th width="10%">'.$lang_global['root_id'].'</th>
                                    <th width="55%">'.$lang_global['quest_chain_hint'].'</th>
                                    <th width="20%">'.$lang_global['progress'].'</th>
                                    <th width="15%">'.$lang_global['current_status'].'</th>
                                </tr>';

    foreach ($chains_list as $root_id => $steps)
    {
        $total = count($steps);
        $done_cnt = 0;
        $active_step_idx = 0;
        $active_title = '';
        $has_active = false;

        foreach ($steps as $idx => $sqid)
        {
            $st = isset($char_states[$sqid]) ? $char_states[$sqid] : 0;
            if ($st == 2) $done_cnt++;
            if ($st == 1 || $st == 3)
            {
                $has_active = true;
                $active_step_idx = $idx + 1;
                $active_title = isset($meta_map[$sqid]['title']) ? $meta_map[$sqid]['title'] : ('Quest #'.$sqid);
            }
        }

        $root_title = isset($meta_map[$root_id]['title']) ? htmlspecialchars($meta_map[$root_id]['title']) : ($lang_global['chain_hash'].$root_id);
        $is_complete = ($done_cnt >= $total);
        $open_attr = $has_active ? ' open="open"' : '';

        if ($is_complete)
            $status_badge = '<span style="color:#00AA00; font-weight:bold;">'.$lang_global['completed'].'</span>';
        elseif ($has_active)
            $status_badge = '<span style="color:#FF9900; font-weight:bold;">Paso '.$active_step_idx.'/'.$total.'</span>';
        else
            $status_badge = '<span style="color:#888888;">'.$lang_global['in_progress'].'</span>';

        $html .= '
                                <tr>
                                    <td valign="top">'.$root_id.'</td>
                                    <td colspan="3" align="left" style="padding: 4px 6px;">
                                        <details'.$open_attr.'>
                                            <summary style="cursor:pointer; font-weight:bold; outline:none;">
                                                <span style="display:inline-block; width:62%;">'.$root_title.'</span>
                                                <span style="display:inline-block; width:20%; text-align:center;">'.$done_cnt.' / '.$total.'</span>
                                                <span style="display:inline-block; width:16%; text-align:center;">'.$status_badge.'</span>
                                            </summary>
                                            <div style="margin-top:6px; padding-left:10px; border-left:2px solid #555;">';

        foreach ($steps as $idx => $sqid)
        {
            $branch = ($idx === $total - 1) ? '&#9492;&#9472;' : '&#9500;&#9472;';
            $st = isset($char_states[$sqid]) ? $char_states[$sqid] : 0;
            $st_title = isset($meta_map[$sqid]['title']) ? htmlspecialchars($meta_map[$sqid]['title']) : ('Quest #'.$sqid);
            $st_lvl = isset($meta_map[$sqid]['lvl']) ? $meta_map[$sqid]['lvl'] : '?';

            if ($st == 2)
                $icon = '<img src="img/aff_tick.png" width="12" height="12" alt="" /> <span style="color:#00AA00;">[Recompensada]</span>';
            elseif ($st == 1)
                $icon = '<img src="img/aff_qst.png" width="12" height="12" alt="" /> <span style="color:#3399FF; font-weight:bold;">[Lista para entregar]</span>';
            elseif ($st == 3)
                $icon = '<img src="img/aff_qst.png" width="12" height="12" alt="" /> <span style="color:#FF9900; font-weight:bold;">[En curso - Paso actual]</span>';
            else
                $icon = '<span style="color:#777777;">&#9675; [Pendiente]</span>';

            $row_bg = ($st == 1 || $st == 3) ? ' background: rgba(255, 153, 0, 0.12); font-weight:bold;' : '';

            $html .= '
                                                <div style="padding:2px 4px;'.$row_bg.'">
                                                    <span style="font-family:monospace; color:#888;">'.$branch.'</span>
                                                    <span style="color:#999;">['.($idx + 1).'/'.$total.']</span>
                                                    ('.$st_lvl.') <a href="'.$quest_datasite.$sqid.'" target="_blank">'.$st_title.'</a>
                                                    <span style="font-size:11px; color:#777;">(#'.$sqid.')</span> &mdash; '.$icon.'
                                                </div>';
        }

        $html .= '
                                            </div>
                                        </details>
                                    </td>
                                </tr>';
    }

    $html .= '
                            </table>';
    return $html;
}

function char_quest(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char,
            $realm_id, $world_db, $characters_db,
            $action_permission, $user_lvl, $user_name,
            $quest_datasite, $itemperpage;
    wowhead_tt();

    require_once 'core/char/char_security.php';

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;
    if (is_numeric($order_by));
    else
        $order_by=1;

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 0;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir=0;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender
                            FROM characters WHERE guid = '.$id.' LIMIT 1');

    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            $output .= '
                    <center>
                        <div id="tab_content">
                            <h1>'.$lang_char['quests'].'</h1>
                            <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                            <br /><br />
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th width="10%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=0&amp;dir='.$dir.'"'.($order_by == 0 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['quest_id'].'</a></th>
                                    <th width="7%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=1&amp;dir='.$dir.'"'.($order_by == 1 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['quest_level'].'</a></th>
                                    <th width="78%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=2&amp;dir='.$dir.'"'.($order_by == 2 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['quest_title'].'</a></th>
                                    <th width="5%"><img src="img/aff_qst.png" width="14" height="14" border="0" alt="" /></th>
                                </tr>';
            $result = $sqlc->query('SELECT quest, status, -1 as rewarded FROM character_queststatus WHERE guid = '.$id.' AND status IN (1, 3) UNION SELECT quest, -1, active AS rewarded FROM character_queststatus_rewarded WHERE guid = ' . $id . ';');

            $quests_1 = [];
            $quests_3 = [];
            $questStatus = [0 => "None (0)", 1 => "Completed but not delivered (1)", 2 => "Not available (2)", 3 => "Incomplete (3)", 4 => "Available (4)", 5 => "Failed (5)"];

            $wdb_name = $world_db[$realmid]['name'];
            $localeStr = get_localestr_by_lang_cookie();
            $addon_map = array();
            $q_addon = $sqlc->query("SELECT ID, ABS(PrevQuestID) AS PrevQuestID, ABS(NextQuestID) AS NextQuestID FROM {$wdb_name}.quest_template_addon WHERE PrevQuestID != 0 OR NextQuestID != 0");
            if ($q_addon)
            {
                while ($ar = $sqlc->fetch_assoc($q_addon))
                {
                    $aid = intval($ar['ID']);
                    $aprev = intval($ar['PrevQuestID']);
                    $anext = intval($ar['NextQuestID']);
                    if (!isset($addon_map[$aid])) $addon_map[$aid] = array('prev' => 0, 'next' => 0);
                    if ($aprev > 0)
                    {
                        $addon_map[$aid]['prev'] = $aprev;
                        if (!isset($addon_map[$aprev])) $addon_map[$aprev] = array('prev' => 0, 'next' => 0);
                        if ($addon_map[$aprev]['next'] == 0) $addon_map[$aprev]['next'] = $aid;
                    }
                    if ($anext > 0)
                    {
                        $addon_map[$aid]['next'] = $anext;
                        if (!isset($addon_map[$anext])) $addon_map[$anext] = array('prev' => 0, 'next' => 0);
                        if ($addon_map[$anext]['prev'] == 0) $addon_map[$anext]['prev'] = $aid;
                    }
                }
            }

            if ($sqlc->num_rows($result))
            {
                $char_states = array();
                $meta_map = array();
                $chains_list = array();
                $all_chain_qids = array();

                while ($quest = $sqlc->fetch_assoc($result))
                {
                    $qid = intval($quest['quest']);
                    $query1 = $sqlc->query("SELECT QuestLevel, IFNULL(quest_template_locale.LogTitle, quest_template.LogTitle) AS Title FROM {$wdb_name}.quest_template LEFT JOIN {$wdb_name}.quest_template_locale ON quest_template.ID = quest_template_locale.ID AND quest_template_locale.locale = '{$localeStr}' WHERE quest_template.ID = {$qid};");
                    $quest_info = $sqlc->fetch_assoc($query1);

                    $qlvl = $quest_info ? $quest_info['QuestLevel'] : '?';
                    $qtitle = $quest_info ? $quest_info['Title'] : ('Quest #'.$qid);
                    $meta_map[$qid] = array('lvl' => $qlvl, 'title' => $qtitle);

                    if ($quest['rewarded'] == 1) $char_states[$qid] = 2;
                    elseif ($quest['status'] == 1) $char_states[$qid] = 1;
                    elseif ($quest['status'] == 3) $char_states[$qid] = 3;

                    $chain_steps = get_full_quest_chain($qid, $addon_map);
                    if (count($chain_steps) > 1)
                    {
                        $root_id = reset($chain_steps);
                        $chains_list[$root_id] = $chain_steps;
                        foreach ($chain_steps as $sq)
                            $all_chain_qids[$sq] = true;
                    }
                    else
                    {
                        if ($quest['rewarded'] == 1 || $quest['status'] == 1)
                            array_push($quests_1, array($qid, $qlvl, $qtitle, $quest['status'], $quest['rewarded']));
                        else
                            array_push($quests_3, array($qid, $qlvl, $qtitle, $quest['status'], $quest['rewarded']));
                    }
                }

                $missing_ids = array();
                foreach (array_keys($all_chain_qids) as $cid)
                {
                    if (!isset($meta_map[$cid]))
                        $missing_ids[] = intval($cid);
                }
                if (!empty($missing_ids))
                {
                    $q_miss = $sqlc->query("SELECT qt.ID, qt.QuestLevel, IFNULL(qtl.LogTitle, qt.LogTitle) AS Title FROM {$wdb_name}.quest_template qt LEFT JOIN {$wdb_name}.quest_template_locale qtl ON qt.ID = qtl.ID AND qtl.locale = '{$localeStr}' WHERE qt.ID IN (" . implode(',', $missing_ids) . ")");
                    if ($q_miss)
                    {
                        while ($mr = $sqlc->fetch_assoc($q_miss))
                            $meta_map[intval($mr['ID'])] = array('lvl' => $mr['QuestLevel'], 'title' => $mr['Title']);
                    }
                }

                uksort($chains_list, function($a, $b) use (&$chains_list, &$char_states) {
                    $act_a = 0;
                    foreach ($chains_list[$a] as $s) { if (isset($char_states[$s]) && ($char_states[$s] == 1 || $char_states[$s] == 3)) $act_a = 1; }
                    $act_b = 0;
                    foreach ($chains_list[$b] as $s) { if (isset($char_states[$s]) && ($char_states[$s] == 1 || $char_states[$s] == 3)) $act_b = 1; }
                    if ($act_a != $act_b) return $act_b - $act_a;
                    return $a - $b;
                });

                unset($quest);
                unset($quest_info);
                aasort($quests_1, $order_by, $dir);
                $orderby = $order_by;
                if (2 < $orderby) $orderby = 1;
                aasort($quests_3, $orderby, $dir);
                $all_record = count($quests_1);

                $output .= '
                            </table>' . render_quest_chains_tree($chains_list, $meta_map, $char_states, $quest_datasite) . '
                            <table class="lined" style="width: 730px;">
                                <tr>
                                    <th colspan="4" align="left">&nbsp;'.$lang_global['active_single_quests'].' ('.count($quests_3).')</th>
                                </tr>
                                <tr>
                                    <th width="10%">'.$lang_char['quest_id'].'</th>
                                    <th width="7%">'.$lang_char['quest_level'].'</th>
                                    <th width="78%">'.$lang_char['quest_title'].'</th>
                                    <th width="5%"><img src="img/aff_qst.png" width="14" height="14" border="0" alt="" /></th>
                                </tr>';

                foreach ($quests_3 as $data)
                {
                    list($q_id, $q_lvl, $q_title, $q_st, $q_rew) = $data;
                    $st_tip = isset($questStatus[$q_st]) ? $questStatus[$q_st] : 'Active';
                    $output .= '
                                <tr>
                                    <td>'.$q_id.'</td>
                                    <td>('.$q_lvl.')</td>
                                    <td align="left"><a href="'.$quest_datasite.$q_id.'" target="_blank">'.htmlentities($q_title).'</a></td>
                                    <td><img src="img/aff_qst.png" width="14" height="14" alt="" onmousemove="toolTip(\'' . $st_tip . '\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                                </tr>';
                }

                if(count($quests_1))
                {
                    $output .= '
                            </table>
                            <table class="hidden" style="width: 730px;">
                                <tr align="right">
                                    <td>';
                    $output .= generate_pagination('char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.($dir ? 0 : 1), $all_record, $itemperpage, $start);
                    $output .= '
                                    </td>
                                </tr>
                            </table>
                            <table class="lined" style="width: 730px;">
                                <tr>
                                    <th colspan="5" align="left">&nbsp;'.$lang_global['completed_single_quests'].' ('.$all_record.')</th>
                                </tr>
                                <tr>
                                    <th width="10%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=0&amp;dir='.$dir.'"'.($order_by == 0 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['quest_id'].'</a></th>
                                    <th width="7%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=1&amp;dir='.$dir.'"'.($order_by == 1 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['quest_level'].'</a></th>
                                    <th width="68%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=2&amp;dir='.$dir.'"'.($order_by == 2 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['quest_title'].'</a></th>
                                    <th width="10%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=3&amp;dir='.$dir.'"'.($order_by == 3 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['rewarded'].'</a></th>
                                    <th width="5%"><img src="img/aff_tick.png" width="14" height="14" border="0" alt="" /></th>
                                </tr>';

                    $i = 0;
                    foreach ($quests_1 as $data)
                    {
                        if($i < ($start+$itemperpage) && $i >= $start)
                        {
                            list($q_id, $q_lvl, $q_title, $q_st, $q_rew) = $data;
                            $st_tip = isset($questStatus[$q_st]) ? $questStatus[$q_st] : 'Rewarded';
                            $output .= '
                                <tr>
                                    <td>'.$q_id.'</td>
                                    <td>('.$q_lvl.')</td>
                                    <td align="left"><a href="'.$quest_datasite.$q_id.'" target="_blank">'.htmlentities($q_title).'</a></td>
                                    <td><img src="img/aff_'.($q_rew == 1 ? 'tick' : 'qst' ).'.png" width="14" height="14" alt="" /></td>
                                    <td><img src="img/aff_tick.png" width="14" height="14" alt="" onmousemove="toolTip(\'' . $st_tip . '\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                                </tr>';
                        }
                        $i++;
                    }

                    $output .= '
                                <tr align="right">
                                    <td colspan="5">';
                    $output .= generate_pagination('char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.($dir ? 0 : 1), $all_record, $itemperpage, $start);
                    $output .= '
                                    </td>
                                </tr>';
                }
            }
            else
                $output .= '
                                <tr>
                                    <td colspan="4"><p>'.$lang_char['no_act_quests'].'</p></td>
                                </tr>';

            //---------------Page Specific Data Ends here----------------------------
            //---------------Character Tabs Footer-----------------------------------
            $output .= '
                            </table>
                        </div>
                        </div>
                        <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                        <br />
                    </center>
                    <!-- end of char_quest.php -->';
        }
        else
            error($lang_char['no_permission']);
    }
    else
        error($lang_char['no_char_found']);
}


//########################################################################################################################
// MAIN
//########################################################################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();

$output .= '
        <div class="top">
            <h1>'.$lang_char['character'].'</h1>
        </div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_quest($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
