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
        for($i=0;$i<6;++$i)
        {
            if (isset($tabs[$i]))
            {
                $output .= '
                            <li'.(($current_tab == $i) ? ' id="selected"' : '').'>
                                <a href="guildbank.php?id='.$guild_id.'&amp;tab='.$i.'&amp;realm='.$realmid.'">';
                if ($tabs[$i]['TabIcon'] == '')
                    $output .= '
                                    <img src="img/INV/INV_blank_32.gif" class="icon_border_0"';
                else
                {
                    if (file_exists(''.$item_icons.'/'.$tabs[$i]['TabIcon'].'.jpg'))
                        $output .= '
                                    <img src="'.$item_icons.'/'.$tabs[$i]['TabIcon'].'.jpg" class="icon_border_0"';
                    else
                        $output .= '
                                    <img src="img/INV/INV_blank_32.gif" class="icon_border_0"';
                }
                if ($tabs[$i]['TabName'] == '')
                    $output .= ' onmousemove="toolTip(\''.$lang_guildbank['tab'].($i+1).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />';
                else
                    $output .= ' onmousemove="toolTip(\''.$tabs[$i]['TabName'].'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />';
                $output .= '
                                </a>
                            </li>';
            }
        }
        $output .= '
                        </ul>
                    </div>
                    <div id="tab_content">';

        $result = $sqlc->query('SELECT gbi.SlotId, itemEntry, count as stack_count FROM guild_bank_item gbi INNER JOIN item_instance ii on ii.guid = gbi.item_guid WHERE gbi.guildid = '.$guild_id.' AND TabID = '.$current_tab.'');
        $gb_slots = [];

        while ($tab = $sqlc->fetch_assoc($result))
            if ($tab['itemEntry'])
                $gb_slots[$tab['SlotId']] = $tab;

        $output .= '
                        <table style="width: 510px;">
                            <tr>
                                <td class="bag" align="center">
                                    <div style="width:'.(14*43).'px;height:'.(7*41).'px;">';
        $sqlm = new SQL;
        $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $sqlw = new SQL;
        $sqlw->connect($world_db[$realmid]['addr'], $world_db[$realmid]['user'], $world_db[$realmid]['pass'], $world_db[$realmid]['name']);
        $item_position = 0;
        for ($i=0;$i<7;++$i)
        {
            for ($j=0;$j<14;++$j)
            {
                $item_position = $j*7+$i;
                if (isset($gb_slots[$item_position]))
                {
                    $gb_item_id = $gb_slots[$item_position]['itemEntry'];
                    $stack = $gb_slots[$item_position]['stack_count'] == 1 ? '' : $gb_slots[$item_position]['stack_count'];
                    $output .= '
                                        <div style="left:'.($j*43).'px;top:'.($i*41).'px;">
                                            <a style="padding:2px;" href="'.$item_datasite.$gb_item_id.'">
                                                <img src="'.get_item_icon($gb_item_id, $sqlm, $sqlw).'" alt="" />
                                            </a>
                                            <div style="width:25px;margin:-15px 0px 0px 16px;color:black;font-size:12px">'.$stack.'</div>
                                            <div style="width:25px;margin:-16px 0px 0px 15px;color:white;font-size:12px">'.$stack.'</div>
                                        </div>';
                }
            }
        }
        $output .= '
                                    </div>
                                </td>
                            </tr>
                            <tr>
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
