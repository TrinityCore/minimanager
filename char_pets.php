<?php

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################^M
// SHOW CHARACTER PETS
//########################################################################################################################^M
function char_pets(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char, $realm_id, $characters_db, $mmfpm_db, $action_permission, $user_lvl, $user_name, $spell_datasite, $pet_ability;
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender FROM characters WHERE guid = '.$id.' LIMIT 1');

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
                        <h1>'.$lang_char['pets'].'</h1>
                        <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                        <br /><br />';
            $result = $sqlc->query('SELECT id, level, exp, name, curhappiness FROM character_pet WHERE owner = '.$id.'');

            if ($sqlc->num_rows($result))
            {
                $sqlm = new SQL;
                $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
                while($pet = $sqlc->fetch_assoc($result))
                {
                    $happiness = floor($pet['curhappiness']/333000);
                    if (1 == $happiness)
                    {
                        $hap_text = 'Content';
                        $hap_val = 1;
                    }
                    elseif (2 == $happiness)
                    {
                        $hap_text = 'Happy';
                        $hap_val = 2;
                    }
                    else
                    {
                        $hap_text = 'Unhappy';
                        $hap_val = 0;
                    }
                    $pet_next_lvl_xp = floor(char_get_xp_to_level($pet['level'])/4);
                    $output .= '
                        <font class="bold">'.$pet['name'].' - lvl '.char_get_level_color($pet['level']).'
                            <a style="padding:2px;" onmouseover="toolTip(\''.$hap_text.'\', \'item_tooltip\')" onmouseout="toolTip()"><img src="img/pet/happiness_'.$hap_val.'.jpg" alt="" /></a>
                            <br /><br />
                        </font>
                        <table class="lined" style="width: 550px;">
                            <tr>
                                <td align="right">Exp:</td>
                                <td valign="top" class="bar skill_bar" style="background-position: '.(round(385*$pet['exp']/$pet_next_lvl_xp)-385).'px;">
                                    <span>'.$pet['exp'].'/'.$pet_next_lvl_xp.'</span>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Pet Abilities:</td>
                                <td align="left">';
                    $ability_results = $sqlc->query('SELECT spell FROM pet_spell WHERE guid = '.$pet['id'].' and active > 1'); // active = 0 is unused and active = 1 probably some passive auras, i dont know diference between values 129 and 193, need to check mangos source
                    if ($sqlc->num_rows($ability_results))
                    {
                        while ($ability = $sqlc->fetch_assoc($ability_results))
                        {
                            $output .= '
                                    <a style="padding:2px;" href="'.$spell_datasite.$ability['spell'].'" target="_blank">
                                        <img src="'.spell_get_icon($ability['spell'], $sqlm).'" alt="'.$ability['spell'].'" class="icon_border_0" />
                                    </a>';
                        }
                    }
                    $output .= '
                                </td>
                            </tr>
                        </table>
                        <br /><br />';
                }
                unset($ability_results);
                unset($pet_next_lvl_xp);
                unset($happiness);
                unset($pet);
            }

            $output .= '
                    </div>
                    </div>
                    <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                    <br />
                    </center>
                    <!-- end of char_pets.php -->';
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
char_pets($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';

?>
