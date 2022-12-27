<?php

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/skill_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS SKILLS
//########################################################################################################################
function char_skill(&$sqlr, &$sqlc)
{
    global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name, $skill_datasite;
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir = 1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;

    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender FROM characters WHERE guid = '.$id.' LIMIT 1');

    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        // we get user permissions first
        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            $result = $sqlc->query('SELECT BINARY name AS name, race, class, level, gender FROM characters WHERE guid = '.$id.'');
            $char = $sqlc->fetch_assoc($result);

            $output .= '
                                <center>
                                    <div id="tab_content">
                                        <h1>'.$lang_char['skills'].'</h1>
                                        <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                                        <br /><br />
                                        <table class="lined" style="width: 550px;">
                                            <tr>
                                                <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['skills'].'</th>
                                            </tr>
                                            <tr>
                                                '.($user_lvl ? '<th><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=0&amp;dir='.$dir.'"'.($order_by==0 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['skill_id'].'</a></th>' : '').'
                                                <th align="right"><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=1&amp;dir='.$dir.'"'.($order_by==1 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['skill_name'].'</a></th>
                                                <th><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=2&amp;dir='.$dir.'"'.($order_by==2 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['skill_value'].'</a></th>
                                            </tr>';

            $skill_array = [];
            $class_array = [];
            $prof_1_array = [];
            $prof_2_array = [];
            $weapon_array = [];
            $armor_array = [];
            $language_array = [];

            $skill_rank_array = [
                75  => $lang_char['apprentice'],
                150 => $lang_char['journeyman'],
                225 => $lang_char['expert'],
                300 => $lang_char['artisan'],
                375 => $lang_char['master'],
                450 => $lang_char['inherent'],
                385 => $lang_char['wise']
            ];
            krsort($skill_rank_array, SORT_NUMERIC);

            $result = $sqlc->query('SELECT skill, value, max FROM character_skills WHERE guid = '.$id.'');

            $sqlm = new SQL;
            $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

            while ($char_skill = $sqlc->fetch_assoc($result))
            {
                $temp = $char_skill['value'];
                $skill = $char_skill['skill'];
                $max = $char_skill['max'];

                if (skill_get_type($skill, $sqlm) == 6)
                    array_push($weapon_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
                elseif (skill_get_type($skill, $sqlm) == 7)
                    array_push($class_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
                elseif (skill_get_type($skill, $sqlm) == 8)
                    array_push($armor_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
                elseif (skill_get_type($skill, $sqlm) == 9)
                    array_push($prof_2_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
                elseif (skill_get_type($skill, $sqlm) == 10)
                    array_push($language_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
                elseif (skill_get_type($skill, $sqlm) == 11)
                    array_push($prof_1_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
                else
                    array_push($skill_array , [($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp, $max]);
            }
            unset($char_skill);

            aasort($skill_array, $order_by, $dir);
            aasort($class_array, $order_by, $dir);
            aasort($prof_1_array, $order_by, $dir);
            aasort($prof_2_array, $order_by, $dir);
            aasort($weapon_array, $order_by, $dir);
            aasort($armor_array, $order_by, $dir);
            aasort($language_array, $order_by, $dir);

            foreach ($skill_array as $data)
            {
                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right">'.$data[1].'</td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$data[3])-450).'px;">
                                                        <span>'.$data[2].'/'.$data[3].'</span>
                                                    </td>
                                                </tr>';
            }

            if(count($class_array))
                $output .= '
                                                <tr>
                                                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['classskills'].'</th>
                                                </tr>';

            foreach ($class_array as $data)
            {
                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right"><a href="'.$skill_datasite.'7.'.$char['class'].'.'.$data[0].'" target="_blank">'.$data[1].'</td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: 0px;">
                                                    </td>
                                                </tr>';
            }

            if(count($prof_1_array))
                $output .= '
                                                <tr>
                                                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['professions'].'</th>
                                                </tr>';

            foreach ($prof_1_array as $data)
            {
                $rankName = "";
                foreach ($skill_rank_array as $k => $v)
                {
                    if ($data[3] >= $k)
                    {
                        $rankName = $v;
                        break;
                    }
                }

                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right"><a href="'.$skill_datasite.'11.'.$data[0].'" target="_blank">'.$data[1].'</a></td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$data[3])-450).'px;">
                                                        <span>'.$data[2].'/'.$data[3].' ('.$rankName.')</span>
                                                    </td>
                                                </tr>';
            }

            if(count($prof_2_array))
                $output .= '
                                                <tr>
                                                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['secondaryskills'].'</th>
                                                </tr>';
            foreach ($prof_2_array as $data)
            {
                $rankName = "";
                foreach ($skill_rank_array as $k => $v)
                {
                    if ($data[3] >= $k)
                    {
                        $rankName = $v;
                        break;
                    }
                }

                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right"><a href="'.$skill_datasite.'9.'.$data[0].'" target="_blank">'.$data[1].'</a></td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$data[3])-450).'px;">
                                                        <span>'.$data[2].'/'.$data[3].' ('.$rankName.')</span>
                                                    </td>
                                                </tr>';
            }

            if(count($weapon_array))
                $output .= '
                                                <tr>
                                                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['weaponskills'].'</th>
                                                </tr>';

            foreach ($weapon_array as $data)
                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right">'.$data[1].'</td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$data[3])-450).'px;">
                                                        <span>'.$data[2].'/'.$data[3].'</span>
                                                    </td>
                                                </tr>';

            if(count($armor_array))
                $output .= '
                                                <tr>
                                                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['armorproficiencies'].'</th>
                                                </tr>';

            foreach ($armor_array as $data)
                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right">'.$data[1].'</td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: 0px;">
                                                    </td>
                                                </tr>';

            if(count($language_array))
                $output .= '
                                                <tr>
                                                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['languages'].'</th>
                                                </tr>';

            foreach ($language_array as $data)
                $output .= '
                                                <tr>
                                                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                                                    <td align="right">'.$data[1].'</td>
                                                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$data[3])-450).'px;">
                                                        <span>'.$data[2].'/'.$data[3].'</span>
                                                    </td>
                                                </tr>';

            $output .= '
                                            </table>
                                            <br />
                                        </div>
                                        <br />
                                    </div>
                                    <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                                    <br />
                                </center>
                                <!-- end of char_achieve.php -->';
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
char_skill($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
