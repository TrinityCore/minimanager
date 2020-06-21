<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS QUESTS
//########################################################################################################################
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

            $quests_1 = array();
            $quests_3 = array();
            $questStatus = array(0 => "None (0)", 1 => "Completed but not delivered (1)", 2 => "Not available (2)", 3 => "Incomplete (3)", 4 => "Available (4)", 5 => "Failed (5)");

            if ($sqlc->num_rows($result))
            {
                while ($quest = $sqlc->fetch_assoc($result))
                {
                    $localeStr = get_localestr_by_lang_cookie();
                    $query1 = $sqlc->query('SELECT QuestLevel, IFNULL(quest_template_locale.Title, quest_template.LogTitle) AS Title FROM ' . $world_db[$realmid]['name'] . '.quest_template LEFT JOIN ' . $world_db[$realmid]['name'] . '.quest_template_locale ON quest_template.ID = quest_template_locale.ID AND quest_template_locale.locale = \'' . $localeStr . '\' WHERE quest_template.ID = ' . $quest['quest'] . ';');
                    $quest_info = $sqlc->fetch_assoc($query1);

                    if ($quest['rewarded'] == 1 || $quest['status'] == 1)
                        array_push($quests_1, array($quest['quest'], $quest_info['QuestLevel'], $quest_info['Title'], $quest['status'], $quest['rewarded']));
                    else
                        array_push($quests_3, array($quest['quest'], $quest_info['QuestLevel'], $quest_info['Title'], $quest['status'], $quest['rewarded']));
                }

                unset($quest);
                unset($quest_info);
                aasort($quests_1, $order_by, $dir);
                $orderby = $order_by;

                if (2 < $orderby)
                    $orderby = 1;

                aasort($quests_3, $orderby, $dir);
                $all_record = count($quests_1);

                foreach ($quests_3 as $data)
                {
                    $output .= '
                                <tr>
                                    <td>'.$data[0].'</td>
                                    <td>('.$data[1].')</td>
                                    <td align="left"><a href="'.$quest_datasite.$data[0].'" target="_blank">'.htmlentities($data[2]).'</a></td>
                                    <td><img src="img/aff_qst.png" width="14" height="14" alt="" onmousemove="toolTip(\'' . $questStatus[$data[3]] . '\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                                </tr>';
                }

                unset($quest_3);

                if(count($quests_1))
                {
                    $output .= '
                            </table>
                            <table class="hidden" style="width: 550px;">
                                <tr align="right">
                                    <td>';
                    $output .= generate_pagination('char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.($dir ? 0 : 1), $all_record, $itemperpage, $start);
                    $output .= '
                                    </td>
                                </tr>
                            </table>
                            <table class="lined" style="width: 550px;">
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
                            $output .= '
                                <tr>
                                    <td>'.$data[0].'</td>
                                    <td>('.$data[1].')</td>
                                    <td align="left"><a href="'.$quest_datasite.$data[0].'" target="_blank">'.htmlentities($data[2]).'</a></td>
                                    <td><img src="img/aff_'.($data[4] == 1 ? 'tick' : 'qst' ).'.png" width="14" height="14" alt="" /></td>
                                    <td><img src="img/aff_tick.png" width="14" height="14" alt="" onmousemove="toolTip(\'' . $questStatus[$data[3]] . '\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                                </tr>';
                        $i++;
                    }

                    unset($data);
                    unset($quest_1);
                    unset($questStatus);
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
