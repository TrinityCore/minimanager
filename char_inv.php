<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/item_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// SHOW INV. AND BANK ITEMS
//#############################################################################
function char_inv(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char, $lang_item,
            $realm_id, $characters_db, $world_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name,
            $item_datasite;

    // this page uses wowhead tooltops
    wowhead_tt();

    require_once 'core/char/char_security.php';

    // getting character data from database
    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender, money
                            FROM characters WHERE guid = '.$id.' LIMIT 1');

    // no point going further if character does not exist
    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        // we get user permissions first
        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=AccountID WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        // check user permission
        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            // main data that we need for this page, character inventory
            $result = $sqlc->query('SELECT ci.bag, ci.slot, ci.item, ii.itemEntry,
                                    count as stack_count
                                    FROM character_inventory ci INNER JOIN item_instance ii on ii.guid = ci.item
                                    WHERE ci.guid = '.$id.' ORDER BY ci.bag,ci.slot');

            //---------------Page Specific Data Starts Here--------------------------
            // lets start processing first before we display anything
            //  we have lots to do for inventory

            // character bags, 1 main + 4 additional
            $bag = array
            (
                0=>array(),
                1=>array(),
                2=>array(),
                3=>array(),
                4=>array()
            );

            // character bang, 1 main + 7 additional
            $bank = array
            (
                0=>array(),
                1=>array(),
                2=>array(),
                3=>array(),
                4=>array(),
                5=>array(),
                6=>array(),
                7=>array()
            );

            // this is where we will put items that are in main bag
            $bag_id = array();
            // this is where we will put items that are in main bank
            $bank_bag_id = array();
            // this is where we will put items that are in character bags, 4 arrays, 1 for each
            $equiped_bag_id = array(0,0,0,0,0);
            // this is where we will put items that are in bank bangs, 7 arrays, 1 for each
            $equip_bnk_bag_id = array(0,0,0,0,0,0,0,0);

            $sqlw = new SQL;
            $sqlw->connect($world_db[$realmid]['addr'], $world_db[$realmid]['user'], $world_db[$realmid]['pass'], $world_db[$realmid]['name']);

            // we load the things in each bag slot
            while ($slot = $sqlc->fetch_assoc($result))
            {
                if ($slot['bag'] == 0 && $slot['slot'] > 18)
                {
                    if($slot['slot'] < 23) // SLOT 19 TO 22 (Bags)
                    {
                        $bag_id[$slot['item']] = ($slot['slot']-18);
                        $equiped_bag_id[$slot['slot']-18] = array($slot['itemEntry'],
                        $sqlw->result($sqlw->query('SELECT ContainerSlots FROM item_template WHERE entry = '.$slot['itemEntry'].''), 0, 'ContainerSlots'), $slot['stack_count']);
                    }
                    elseif($slot['slot'] < 39) // SLOT 23 TO 38 (BackPack)
                    {
                        if(isset($bag[0][$slot['slot']-23]))
                            $bag[0][$slot['slot']-23][0]++;
                        else
                            $bag[0][$slot['slot']-23] = array($slot['itemEntry'],0,$slot['stack_count']);
                    }
                    elseif($slot['slot'] < 67) // SLOT 39 TO 66 (Bank)
                        $bank[0][$slot['slot']-39] = array($slot['itemEntry'],0,$slot['stack_count']);
                    elseif($slot['slot'] < 74) // SLOT 67 TO 73 (Bank Bags)
                    {
                        $bank_bag_id[$slot['item']] = ($slot['slot']-66);
                        $equip_bnk_bag_id[$slot['slot']-66] = array($slot['itemEntry'],
                        $sqlw->result($sqlw->query('SELECT ContainerSlots FROM item_template WHERE entry = '.$slot['itemEntry'].''), 0, 'ContainerSlots'), $slot['stack_count']);
                    }
                }
                else
                {
                    // Bags
                    if (isset($bag_id[$slot['bag']]))
                    {
                        if(isset($bag[$bag_id[$slot['bag']]][$slot['slot']]))
                            $bag[$bag_id[$slot['bag']]][$slot['slot']][1]++;
                        else
                            $bag[$bag_id[$slot['bag']]][$slot['slot']] = array($slot['itemEntry'],0,$slot['stack_count']);
                    }
                    // Bank Bags
                    elseif (isset($bank_bag_id[$slot['bag']]))
                        $bank[$bank_bag_id[$slot['bag']]][$slot['slot']] = array($slot['itemEntry'],0,$slot['stack_count']);
                }
            }
            unset($slot);
            unset($bag_id);
            unset($bank_bag_id);
            unset($result);

            //------------------------Character Tabs---------------------------------
            // we start with a lead of 10 spaces,
            //  because last line of header is an opening tag with 8 spaces
            //  keep html indent in sync, so debuging from browser source would be easy to read
            $output .= '
                    <center>
                        <div id="tab_content">
                            <h1>'.$lang_char['inventory'].'</h1>
                            <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                            <br /><br />
                            <table class="lined" style="width: 700px;">
                                <tr>';

            //---------------Page Specific Data Starts Here--------------------------
            $sqlm = new SQL;
            $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

            // equipped bags
            for($i=1; $i < 5; ++$i)
            {
                $output .= '
                                    <th>';
                if($equiped_bag_id[$i])
                    $output .='
                                        <a style="padding:2px;" href="'.$item_datasite.$equiped_bag_id[$i][0].'" target="_blank">
                                            <img class="bag_icon" src="'.get_item_icon($equiped_bag_id[$i][0], $sqlm, $sqlw).'" alt="" />
                                        </a>
                                        '.$lang_item['bag'].' '.$i.'<br />
                                        <font class="small">'.$equiped_bag_id[$i][1].' '.$lang_item['slots'].'</font>';
                $output .= '
                                    </th>';
            }
            $output .= '
                                </tr>
                                <tr>';

            // equipped bag slots
            for($t = 1; $t < 5; ++$t)
            {
                $output .= '
                                    <td class="bag" valign="bottom" align="center">
                                        <div style="width:'.(4*43).'px;height:'.(ceil($equiped_bag_id[$t][1]/4)*41).'px;">';
                $dsp = $equiped_bag_id[$t][1]%4;
                if ($dsp)
                    $output .= '
                                            <div class="no_slot"></div>';
                foreach ($bag[$t] as $pos => $item)
                {
                    $item[2] = $item[2] == 1 ? '' : $item[2];
                    $output .= '
                                            <div style="left:'.(($pos+$dsp)%4*42).'px;top:'.(floor(($pos+$dsp)/4)*41).'px;">
                                                <a style="padding:2px;" href="'.$item_datasite.$item[0].'" target="_blank">
                                                    <img src="'.get_item_icon($item[0], $sqlm, $sqlw).'" alt="" />
                                                </a>
                                                <div style="width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px">'.$item[2].'</div>
                                                <div style="width:25px;margin:-21px 0px 0px 17px;font-size:14px">'.$item[2].'</div>
                                            </div>';
                }
                $output .= '
                                        </div>
                                    </td>';
            }
            unset($equiped_bag_id);
            $output .= '
                                </tr>
                                <tr>
                                    <th colspan="2" align="left">
                                        <img class="bag_icon" src="'.get_item_icon(3960, $sqlm, $sqlw).'" alt="" align="middle" style="margin-left:100px;" />
                                        <font style="margin-left:30px;">'.$lang_char['backpack'].'</font>
                                    </th>
                                    <th colspan="2">
                                        '.$lang_char['bank_items'].'
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bag" align="center" height="220px">
                                        <div style="width:'.(4*43).'px;height:'.(ceil(16/4)*41).'px;">';

            // inventory items
            foreach ($bag[0] as $pos => $item)
            {
                $item[2] = $item[2] == 1 ? '' : $item[2];
                $output .= '
                                            <div style="left:'.($pos%4*42).'px;top:'.(floor($pos/4)*41).'px;">
                                                <a style="padding:2px;" href="'.$item_datasite.$item[0].'" target="_blank">
                                                    <img src="'.get_item_icon($item[0], $sqlm, $sqlw).'" alt="" />
                                                </a>
                                                <div style="width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">'.$item[2].'</div>
                                                <div style="width:25px;margin:-21px 0px 0px 17px;font-size:14px">'.$item[2].'</div>
                                            </div>';
            }
            unset($bag);
            $output .= '
                                        </div>
                                        <div style="text-align:right;width:168px;background-image:none;background-color:#393936;padding:2px;">
                                            <b>
                                                '.substr($char['money'],  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
                                                '.substr($char['money'], -4, -2).'<img src="img/silver.gif" alt="" align="middle" />
                                                '.substr($char['money'], -2).'<img src="img/copper.gif" alt="" align="middle" />
                                            </b>
                                        </div>
                                    </td>
                                    <td colspan="2" class="bank" align="center">
                                        <div style="width:'.(7*43).'px;height:'.(ceil(24/7)*41).'px;">';

            // bank items
            foreach ($bank[0] as $pos => $item)
            {
                $item[2] = $item[2] == 1 ? '' : $item[2];
                $output .= '
                                            <div style="left:'.($pos%7*43).'px;top:'.(floor($pos/7)*41).'px;">
                                                <a style="padding:2px;" href="'.$item_datasite.$item[0].'" target="_blank">
                                                    <img src="'.get_item_icon($item[0], $sqlm, $sqlw).'" class="inv_icon" alt="" />
                                                </a>
                                                <div style="width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px">'.$item[2].'</div>
                                                <div style="width:25px;margin:-21px 0px 0px 17px;font-size:14px">'.$item[2].'</div>
                                            </div>';
            }
            $output .= '
                                        </div>
                                    </td>
                                </tr>
                                <tr>';

            // equipped bank bags, first 4
            for($i=1; $i < 5; ++$i)
            {
                $output .= '
                                    <th>';
                if($equip_bnk_bag_id[$i])
                {
                    $output .= '
                                        <a style="padding:2px;" href="'.$item_datasite.$equip_bnk_bag_id[$i][0].'" target="_blank">
                                            <img class="bag_icon" src="'.get_item_icon($equip_bnk_bag_id[$i][0], $sqlm, $sqlw).'" alt="" />
                                        </a>
                                        '.$lang_item['bag'].' '.$i.'<br />
                                        <font class="small">'.$equip_bnk_bag_id[$i][1].' '.$lang_item['slots'].'</font>';
                }
                $output .= '
                                    </th>';
            }
            $output .= '
                                </tr>
                                <tr>';

            // equipped bank bag slots
            for($t=1; $t < 8; ++$t)
            {
                // equipped bank bags, last 3
                if($t===5)
                {
                    $output .= '
                                </tr>
                                <tr>';
                    for($i=5; $i < 8; ++$i)
                    {
                        $output .= '
                                        <th>';
                        if($equip_bnk_bag_id[$i])
                        {
                            $output .= '
                                            <a style="padding:2px;" href="'.$item_datasite.$equip_bnk_bag_id[$i][0].'" target="_blank">
                                                <img class="bag_icon" src="'.get_item_icon($equip_bnk_bag_id[$i][0], $sqlm, $sqlw).'" alt="" />
                                            </a>
                                            '.$lang_item['bag'].' '.$i.'<br />
                                            <font class="small">'.$equip_bnk_bag_id[$i][1].' '.$lang_item['slots'].'</font>';
                        }
                        $output .= '
                                        </th>';
                    }
                    $output .= '
                                        <th>
                                        </th>
                                    </tr>
                                    <tr>';
                }
                $output .= '
                                        <td class="bank" align="center">
                                            <div style="width:'.(4*43).'px;height:'.(ceil($equip_bnk_bag_id[$t][1]/4)*41).'px;">';
                $dsp=$equip_bnk_bag_id[$t][1]%4;

                if ($dsp)
                    $output .= '
                                                <div class="no_slot"></div>';
                foreach ($bank[$t] as $pos => $item)
                {
                    $item[2] = $item[2] == 1 ? '' : $item[2];
                    $output .= '
                                                    <div style="left:'.(($pos+$dsp)%4*43).'px;top:'.(floor(($pos+$dsp)/4)*41).'px;">
                                                        <a style="padding:2px;" href="'.$item_datasite.$item[0].'" target="_blank">
                                                            <img src="'.get_item_icon($item[0], $sqlm, $sqlw).'" alt="" />
                                                        </a>
                                                    <div style="width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px">'.$item[2].'</div>
                                                    <div style="width:25px;margin:-21px 0px 0px 17px;font-size:14px">'.$item[2].'</div>
                                                </div>';
                }

                $output .= '
                                            </div>
                                        </td>';
            }
            unset($equip_bnk_bag_id);
            unset($bank);
            $output .= '
                                        <td class="bank"></td>';

            //---------------Page Specific Data Ends here----------------------------
            //---------------Character Tabs Footer-----------------------------------
            $output .= '
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                        <br />
                    </center>
                    <!-- end of char_inv.php -->';
        }
        else
            error($lang_char['no_permission']);
    }
    else
        error($lang_char['no_char_found']);
}


//#############################################################################
// MAIN
//#############################################################################

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
char_inv($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
