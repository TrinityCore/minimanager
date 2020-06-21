<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTER TALENTS
//########################################################################################################################
function char_talent(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char,
            $realm_id, $realm_db, $characters_db, $mmfpm_db, $server,
            $action_permission, $user_lvl, $user_name, $spell_datasite;
    // this page uses wowhead tooltops
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender, (SELECT count(spell) FROM character_talent WHERE guid = '.$id.' AND talentGroup = (SELECT activeTalentGroup FROM characters WHERE guid = '.$id.')) AS talent_points
        FROM characters WHERE guid = '.$id.' LIMIT 1');

    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=AccountID WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            $result = $sqlc->query('SELECT spell FROM character_spell WHERE guid = '.$id.' and active = 1 and disabled = 0 ORDER BY spell DESC');
            $output .= '
                        <center>
                            <div id="tab_content">
                                <h1>'.$lang_char['talents'].'</h1>
                                <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                                <br /><br />
                                <table class="lined" style="width: 550px;">
                                    <tr valign="top" align="center">';
            if ($sqlc->num_rows($result))
            {
                $talent_rate = (isset($server[$realmid]['talent_rate']) ? $server[$realmid]['talent_rate'] : 1);
                $talent_points = ($char['level'] - 9) * $talent_rate;
                $talent_points_left = $char['talent_points'];
                $talent_points_used = $talent_points - $talent_points_left;

                $sqlm = new SQL;
                $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

                $tabs = array();
                $l = 0;

                while (($talent = $sqlc->fetch_assoc($result)) && ($l < $talent_points_used))
                {
                    if ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT field_1, field_2, field_3, field_13, field_16 from dbc_talent where field_8 = '.$talent['spell'].' LIMIT 1')))
                    {
                        if (isset($tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']]))
                            $l -=$tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']][1];
                        $tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']] = array($talent['spell'], '5', '5');
                        $l += 5;

                        if ($tab['field_13'])
                            talent_dependencies($tabs, $tab, $l, $sqlm);
                    }
                    elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT field_1, field_2, field_3, field_13, field_16, field_8 from dbc_talent where field_7 = '.$talent['spell'].' LIMIT 1')))
                    {
                        if (isset($tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']]))
                            $l -=$tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']][1];

                        $tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']] = array($talent['spell'], '4', ($tab['field_8'] ? '2' : '5'));
                        $l += 4;

                        if ($tab['field_13'])
                            talent_dependencies($tabs, $tab, $l, $sqlm);
                    }
                    elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT field_1, field_2, field_3, field_13, field_16, field_7 from dbc_talent where field_6 = '.$talent['spell'].' LIMIT 1')))
                    {
                        if (isset($tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']]))
                            $l -=$tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']][1];

                        $tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']] = array($talent['spell'],'3', ($tab['field_7'] ? '2' : '5'));
                        $l += 3;

                        if ($tab['field_13'])
                            talent_dependencies($tabs, $tab, $l, $sqlm);
                    }
                    elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT field_1, field_2, field_3, field_13, field_16, field_6 from dbc_talent where field_5 = '.$talent['spell'].' LIMIT 1')))
                    {
                        if (isset($tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']]))
                            $l -=$tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']][1];

                        $tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']] = array($talent['spell'],'2', ($tab['field_6'] ? '2' : '5'));
                        $l += 2;

                        if ($tab['field_13'])
                            talent_dependencies($tabs, $tab, $l, $sqlm);
                    }
                    elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT field_1, field_2, field_3, field_13, field_16, field_5 from dbc_talent where field_4 = '.$talent['spell'].' LIMIT 1')))
                    {
                        if (isset($tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']]))
                            $l -=$tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']][1];

                        $tabs[$tab['field_1']][$tab['field_2']][$tab['field_3']] = array($talent['spell'],'1', ($tab['field_5'] ? '2' : '5'));
                        $l += 1;

                        if ($tab['field_13'])
                            talent_dependencies($tabs, $tab, $l, $sqlm);
                    }
                }
                unset($tab);
                unset($talent);
                foreach ($tabs as $k=>$data)
                {
                    $points = 0;
                    $output .= '
                                        <td>
                                            <table class="hidden" style="width: 0px;">
                                                <tr>
                                                    <td colspan="6" style="border-bottom-width: 0px;">
                                                    </td>
                                                </tr>
                                                <tr>';
                    for($i=0;$i<11;++$i)
                    {
                        for($j=0;$j<4;++$j)
                        {
                            if(isset($data[$i][$j]))
                            {
                                $output .= '
                                                    <td valign="bottom" align="center" style="border-top-width: 0px;border-bottom-width: 0px;">
                                                        <a href="'.$spell_datasite.$data[$i][$j][0].'" target="_blank">
                                                            <img src="'.spell_get_icon($data[$i][$j][0], $sqlm).'" width="36" height="36" class="icon_border_'.$data[$i][$j][2].'" alt="" />
                                                        </a>
                                                        <div style="width:0px;margin:-14px 0px 0px 30px;font-size:14px;color:black">'.$data[$i][$j][1].'</div>
                                                        <div style="width:0px;margin:-14px 0px 0px 29px;font-size:14px;color:white">'.$data[$i][$j][1].'</div>
                                                    </td>';
                                $points += $data[$i][$j][1];
                            }
                            else
                                $output .= '
                                                    <td valign="bottom" align="center" style="border-top-width: 0px;border-bottom-width: 0px;">
                                                        <img src="img/blank.gif" width="44" height="44" alt="" />
                                                    </td>';
                        }
                        $output .= '
                                                </tr>
                                                <tr>';
                    }
                    $output .= '
                                                    <td colspan="6" style="border-top-width: 0px;border-bottom-width: 0px;">
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" valign="bottom" align="left">
                                                    '.$sqlm->result($sqlm->query('SELECT field_1 FROM dbc_talenttab WHERE id = '.$k.''), 0, 'field_1').': '.$points.'
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>';
                }
                unset($data);
                unset($k);
                unset($tabs);
                $output .='
                                    </tr>
                                </table>
                                <br />
                                <table>
                                    <tr>
                                        <td align="left">
                                            '.$lang_char['talent_rate'].': <br />
                                            '.$lang_char['talent_points'].': <br />
                                            '.$lang_char['talent_points_used'].': <br />
                                            '.$lang_char['talent_points_shown'].': <br />
                                            '.$lang_char['talent_points_left'].':
                                        </td>
                                        <td align="left">
                                            '.$talent_rate.'<br />
                                            '.$talent_points.'<br />
                                            '.$talent_points_used.'<br />
                                            '.$l.'<br />
                                            '.$talent_points_left.'
                                        </td>
                                        <td width="64">
                                        </td>
                                        <td align="right">';
                unset($l);
                unset($talent_rate);
                unset($talent_points);
                unset($talent_points_used);
                unset($talent_points_left);

                $result = $sqlc->query('SELECT * FROM character_glyphs WHERE guid = '.$id.' AND talentGroup = (SELECT activeTalentGroup FROM characters WHERE guid = '.$id.')');
                if ($sqlc->num_rows($result))
                {
                    $glyphs = $sqlc->fetch_assoc($result);
                    $glyphs = array($glyphs['glyph1'], $glyphs['glyph2'], $glyphs['glyph3'], $glyphs['glyph4'], $glyphs['glyph5'], $glyphs['glyph6']); // didnt want to recode the block down there
                }
                else
                    $glyphs = array(0,0,0,0,0,0,0);

                for($i=0;$i<6;++$i)
                {
                  if ($glyphs[$i] && $glyphs[$i] > 0)
                  {
                    $glyph = $sqlm->result($sqlm->query('select IFNULL(field_1,0) from dbc_glyphproperties where id = '.$glyphs[$i].''), 0);
                    $output .='
                                            <a href="'.$spell_datasite.$glyph.'" target="_blank">
                                                <img src="'.spell_get_icon($glyph, $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                            </a>';
                  }
                }
                unset($glyphs);
                $output .='
                                        </td>';
            }

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
                        <!-- end of char_talent.php -->';
        }
        else
            error($lang_char['no_permission']);
    }
    else
        error($lang_char['no_char_found']);
}


function talent_dependencies(&$tabs, &$tab, &$i, &$sqlm)
{
    if ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT field_1, field_2, field_3, field_'.($tab['field_16'] + 1).', field_13,field_16'.(($tab['field_16'] < 4) ? ', field_'.($tab['field_16'] + 2).'' : '').' from dbc_talent where id = '.$tab['field_13'].' and field_'.($tab['field_16'] + 1).' != 0 LIMIT 1')))
    {
        if(empty($tabs[$dep['field_1']][$dep['field_2']][$dep['field_3']]))
        {
            $tabs[$dep['field_1']][$dep['field_2']][$dep['field_3']] = array($dep['field_'.($tab['field_16'] + 1).''], ''.($tab['field_16'] + 1).'', (($tab['field_16'] < 4) ? ($dep['field_'.($tab['field_16'] + 2).''] ? '2' : '5') : '5'));
            $i += ($tab['field_16'] + 1);

            if ($dep['field_13'])
                talent_dependencies($tabs, $dep, $i, $sqlm);
        }
    }
}


//########################################################################################################################
// MAIN
//########################################################################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

$output .= '
        <div class="top">
            <h1>'.$lang_char['character'].'</h1>
        </div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_talent($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
