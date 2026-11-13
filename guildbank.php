<?php

require_once 'header.php';
require_once 'libs/item_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// GUILD BANK
//########################################################################################################################
function guild_bank(&$sqlr, &$sqlc)
{
    global  $output, $lang_global, $lang_guildbank, $realm_id, $characters_db, $mmfpm_db, $world_db, $item_datasite, $item_icons;
    wowhead_tt();

    if (empty($_GET['id']))
        error($lang_global['empty_fields']);

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

    $guild_id = $sqlc->quote_smart($_GET['id']);
    if (is_numeric($guild_id));
    else
        $guild_id = 0;

    if (empty($_GET['tab']))
        $current_tab = 0;
    else
        $current_tab = $sqlc->quote_smart($_GET['tab']);

    if (is_numeric($current_tab) || ($current_tab > 6));
    else
        $current_tab = 0;

    $result = $sqlc->query('SELECT BINARY name AS name, BankMoney FROM guild WHERE guildid = '.$guild_id.' LIMIT 1');

    if($sqlc->num_rows($result))
    {
        $guild_name  = $sqlc->result($result, 0, 'name');
        $bank_gold   = $sqlc->result($result, 0, 'BankMoney');
        $result = $sqlc->query('SELECT TabId, TabName, TabIcon FROM guild_bank_tab WHERE guildid = '.$guild_id.' LIMIT 6');
        $tabs = [];

        while ($tab = $sqlc->fetch_assoc($result))
        {
            $tabs[$tab['TabId']] = $tab;
        }

        $output .= '
                <div class="top">
                    <h1>'.$guild_name.' '.$lang_guildbank['guildbank'].'</h1>
                </div>
                <center>
                    <div id="tab">
                        <ul>';
        $is_merged = !empty($_GET['merge']);
        for ($i = 0; $i < 6; ++$i) {
            if (isset($tabs[$i])) {
                $output .= '<li' . ((!$is_merged && $current_tab == $i) ? ' id="selected"' : '') . '><a href="guildbank.php?id=' . $guild_id . '&amp;tab=' . $i . '&amp;realm=' . $realmid . '">';
                $ico = ($tabs[$i]['TabIcon'] != '' && file_exists($item_icons . '/' . $tabs[$i]['TabIcon'] . '.jpg')) ? ($item_icons . '/' . $tabs[$i]['TabIcon'] . '.jpg') : 'img/INV/INV_blank_32.gif';
                $tnm = ($tabs[$i]['TabName'] == '') ? ($lang_guildbank['Tab'] . ($i + 1)) : $tabs[$i]['TabName'];
                $output .= '<img src="' . $ico . '" class="icon_border_0" onmousemove="toolTip(\'' . htmlspecialchars($tnm, ENT_QUOTES) . '\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></a></li>';
            }
        }
        $m_url = $is_merged ? 'guildbank.php?id=' . $guild_id . '&amp;tab=' . $current_tab . '&amp;realm=' . $realmid : 'guildbank.php?id=' . $guild_id . '&amp;merge=1&amp;realm=' . $realmid;
        $m_lbl = $is_merged ? ($lang_global['split_tabs'] ?? 'Split Tabs') : ($lang_global['merge_tabs'] ?? 'Merge Tabs');
        $output .= '</ul></div><div id="tab_content">';
        $sqlm = new SQL; $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $sqlw = new SQL; $sqlw->connect($world_db[$realmid]['addr'], $world_db[$realmid]['user'], $world_db[$realmid]['pass'], $world_db[$realmid]['name']);
        $gb_entries = array(); $gb_slots = array(); $merged_items = array();
        if ($is_merged) {
            $result = $sqlc->query('SELECT gbi.TabId, gbi.SlotId, ii.itemEntry, ii.count AS stack_count FROM guild_bank_item gbi INNER JOIN item_instance ii ON ii.guid = gbi.item_guid WHERE gbi.guildid = ' . $guild_id . ' ORDER BY gbi.TabId ASC, gbi.SlotId ASC');
            while ($row = $sqlc->fetch_assoc($result)) { if (!empty($row['itemEntry'])) { $merged_items[] = $row; $gb_entries[intval($row['itemEntry'])] = true; } }
        } else {
            $result = $sqlc->query('SELECT gbi.SlotId, itemEntry, count as stack_count FROM guild_bank_item gbi INNER JOIN item_instance ii on ii.guid = gbi.item_guid WHERE gbi.guildid = ' . $guild_id . ' AND TabID = ' . $current_tab);
            while ($tab = $sqlc->fetch_assoc($result)) { if (!empty($tab['itemEntry'])) { $gb_slots[$tab['SlotId']] = $tab; $gb_entries[intval($tab['itemEntry'])] = true; } }
        }
$gb_names = array();
if (!empty($gb_entries)) {
    $ids = implode(',', array_keys($gb_entries));
    $qn = $sqlw->query("SELECT entry, name FROM item_template WHERE entry IN ($ids)");
    while ($rn = $sqlw->fetch_assoc($qn)) $gb_names[intval($rn['entry'])] = mb_strtolower($rn['name'], 'UTF-8');
    $ql = @$sqlw->query("SELECT ID, Name FROM item_template_locale WHERE locale IN ('esES','esMX') AND ID IN ($ids)");
    if ($ql) while ($rl = $sqlw->fetch_assoc($ql)) if (!empty($rl['Name'])) $gb_names[intval($rl['ID'])] .= ' ' . mb_strtolower($rl['Name'], 'UTF-8');
}
$gb_json = json_encode($gb_names, JSON_UNESCAPED_UNICODE);
$output .= '<div style="margin:8px 0 8px 0;"><input type="text" id="gb_live_search" placeholder="'.htmlspecialchars($lang_global['search_placeholder'] ?? 'Search...').'" autocomplete="off" style="width:240px;padding:4px 8px;" oninput="filterGuildBank(this.value)" /> <button type="button" id="gb_live_clear" onclick="document.getElementById(\'gb_live_search\').value=\'\';filterGuildBank(\'\');" style="display:none;cursor:pointer;padding:3px 7px;">X</button> <a href="' . $m_url . '" class="button" style="margin-left:8px;padding:4px 10px;">' . $m_lbl . '</a></div><script type="text/javascript">var gbItemNames=' . $gb_json . ';function filterGuildBank(q){q=(q||"").toLowerCase().trim();var c=document.getElementById("gb_live_clear");if(c)c.style.display=q.length?"inline-block":"none";var b=document.getElementById("post_tab_content")||document.getElementById("tab_content");if(!b)return;var l=b.getElementsByTagName("a");for(var i=0;i<l.length;++i){var a=l[i],h=a.getAttribute("href")||"",m=h.match(/(?:item=|\/item\/)(\d+)/);if(!m)continue;var id=m[1],t=(a.parentNode&&a.parentNode.tagName==="DIV")?a.parentNode:a;t.style.transition="opacity 0.15s ease, filter 0.15s ease";if(!q){t.style.opacity="1";t.style.filter="none";a.style.outline="none";continue;}var n=gbItemNames[id]||"";if(n.indexOf(q)!==-1||id===q){t.style.opacity="1";t.style.filter="none";a.style.outline="1px solid #ffd100";}else{t.style.opacity="0.18";t.style.filter="grayscale(85%)";a.style.outline="none";}}}</script><table style="width:510px;"><tr><td class="bag" align="center">';
if ($is_merged) {
    $rows = max(7, ceil(count($merged_items) / 14));
    $output .= '<div style="width:' . (14 * 43) . 'px;height:' . ($rows * 41) . 'px;">';
    foreach ($merged_items as $idx => $mit) {
        $c = $idx % 14; $r = floor($idx / 14); $iid = $mit['itemEntry']; $stk = ($mit['stack_count'] == 1) ? '' : $mit['stack_count'];
        $ti = intval($mit['TabId']); $tt = (!empty($tabs[$ti]['TabName'])) ? $tabs[$ti]['TabName'] : ($lang_guildbank['Tab'] . ($ti + 1));
        $output .= '<div title="' . htmlspecialchars($tt) . '" style="left:' . ($c * 43) . 'px;top:' . ($r * 41) . 'px;"><a style="padding:2px;" href="' . $item_datasite . $iid . '" target="_blank"><img src="' . get_item_icon($iid, $sqlm, $sqlw) . '" alt="" /></a><div style="width:25px;margin:-15px 0px 0px 16px;color:black;font-size:12px;">' . $stk . '</div><div style="width:25px;margin:-16px 0px 0px 15px;color:white;font-size:12px;">' . $stk . '</div></div>';
    }
    $output .= '</div>';
} else {
    $output .= '<div style="width:' . (14 * 43) . 'px;height:' . (7 * 41) . 'px;">';
    for ($i = 0; $i < 7; ++$i) for ($j = 0; $j < 14; ++$j) {
        $p = $j * 7 + $i;
        if (isset($gb_slots[$p])) {
            $iid = $gb_slots[$p]['itemEntry']; $stk = ($gb_slots[$p]['stack_count'] == 1) ? '' : $gb_slots[$p]['stack_count'];
            $output .= '<div style="left:' . ($j * 43) . 'px;top:' . ($i * 41) . 'px;"><a style="padding:2px;" href="' . $item_datasite . $iid . '" target="_blank"><img src="' . get_item_icon($iid, $sqlm, $sqlw) . '" alt="" /></a><div style="width:25px;margin:-15px 0px 0px 16px;color:black;font-size:12px;">' . $stk . '</div><div style="width:25px;margin:-16px 0px 0px 15px;color:white;font-size:12px;">' . $stk . '</div></div>';
        }
    }
    $output .= '</div>';
}
$output .= '</td></tr>
<td class="hidden" align="right">
                                    '.substr($bank_gold,  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
                                    '.substr($bank_gold, -4,  -2).'<img src="img/silver.gif" alt="" align="middle" />
                                    '.substr($bank_gold, -2).'<img src="img/copper.gif" alt="" align="middle" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <table class="hidden">
                        <tr>
                            <td>';

        makebutton($lang_guildbank['guild'], 'guild.php?action=view_guild&amp;realm='.$realmid.'&amp;error=3&amp;id='.$guild_id.'', 130);

        $output .= '
                            </td>
                        </tr>
                    </table>
                    <br />
                </center>';
        unset($bank_gold);
    }
    else
        redirect('error.php?err='.$lang_guildbank['notfound']);
}
//#############################################################################
// MAIN
//#############################################################################
//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;
$lang_guildbank = lang_guildbank();

//unset($err);
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

guild_bank($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_guildbank);

require_once 'footer.php';

?>
