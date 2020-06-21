<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS SPELL
//########################################################################################################################
function char_spell(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char,
            $realm_id, $characters_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name,
            $spell_datasite, $itemperpage;
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start)); else $start=0;

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
            $all_record = $sqlc->result($sqlc->query('SELECT count(spell) FROM character_spell WHERE guid = '.$id.' and active = 1'), 0);
            $result = $sqlc->query('SELECT spell FROM character_spell WHERE guid = '.$id.' and active = 1 order by spell ASC LIMIT '.$start.', '.$itemperpage.'');

            $output .= '
                        <center>
                            <div id="tab_content">
                                <h1>'.$lang_char['spells'].'</h1>
                                <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                                <br /><br />';

            if ($sqlc->num_rows($result))
            {
                $output .= '
                                <table class="lined" style="width: 550px;">
                                    <tr align="right">
                                        <td colspan="4">';
                $output .= generate_pagination('char_spell.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'', $all_record, $itemperpage, $start);
                $output .= '
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>'.$lang_char['icon'].'</th>
                                        <th>'.$lang_char['name'].'</th>
                                        <th>'.$lang_char['icon'].'</th>
                                        <th>'.$lang_char['name'].'</th>
                                    </tr>';

                $sqlm = new SQL;
                $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

                while ($spell = $sqlc->fetch_assoc($result))
                {
                    $output .= '
                                    <tr>
                                        <td><a href="'.$spell_datasite.$spell['spell'].'"><img src="'.spell_get_icon($spell['spell'], $sqlm).'" class="icon_border_0" /></a></td>
                                        <td align="left"><a href="'.$spell_datasite.$spell['spell'].'">'.spell_get_name($spell['spell'], $sqlm).'</a></td>';
                    if($spell = $sqlc->fetch_assoc($result))
                        $output .='
                                        <td><a href="'.$spell_datasite.$spell['spell'].'"><img src="'.spell_get_icon($spell['spell'], $sqlm).'" class="icon_border_0" /></a></td>
                                        <td align="left"><a href="'.$spell_datasite.$spell['spell'].'">'.spell_get_name($spell['spell'], $sqlm).'</a></td>
                                    </tr>';
                    else
                        $output .='
                                        <td></td>
                                        <td></td>
                                    </tr>';
                }

                $output .= '
                                    <tr align="right">
                                        <td colspan="4">';
                $output .= generate_pagination('char_spell.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'', $all_record, $itemperpage, $start);
                $output .= '
                                        </td>
                                    </tr>
                                </table>';
            }

            //---------------Page Specific Data Ends here----------------------------
            //---------------Character Tabs Footer-----------------------------------
            $output .= '
                                </div>
                                </div>
                                <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                                <br />
                            </center>
                            <!-- end of char_spell.php -->';
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

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

$output .= '
        <div class="top">
            <h1>'.$lang_char['character'].'</h1>
        </div>';

char_spell($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
