<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/archieve_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//#############################################################################
function char_achievements(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char,
            $realm_id, $characters_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name,
            $achievement_datasite;

    // this page uses wowhead tooltops
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $show_type = (isset($_POST['show_type'])) ? $sqlc->quote_smart($_POST['show_type']) : 0;
    if (is_numeric($show_type));
        else $show_type = 0;

    // getting character data from database
    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender
                            FROM characters WHERE guid = '.$id.' LIMIT 1');

    // no point going further if character does not exist
    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        // we get user permissions first
        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `AccountID` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `AccountID` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'AccountID');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        // check user permission
        if ( ($user_lvl > $owner_gmlvl) || ($owner_name === $user_name) )
        {
            //------------------------Character Tabs---------------------------------
            // we start with a lead of 10 spaces,
            //  because last line of header is an opening tag with 8 spaces
            //  keep html indent in sync, so debuging from browser source would be easy to read
            $output .= '
            <center>
                <script type="text/javascript">
                    function expand(thistag)
                    {
                        var i = 0;
                        %%REPLACE%%

                        if (thistag == \'tsummary\')
                        {
                            document.getElementById(\'tsummary\').style.display="table";
                            document.getElementById(\'divsummary\').innerHTML = \'[-] '.$lang_char['summary'].'\' ;
                            for(x in main_cats)
                            {
                                if(document.getElementById(main_cats[x]).style.display=="table")
                                {
                                    document.getElementById(main_cats[x]).style.display="none";
                                    document.getElementById(main_cats_achieve[x]).style.display="none";
                                    document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                                }
                            }
                            for(x in main_sub_cats)
                            {
                                if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                {
                                    document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                    document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                }
                            }
                        }
                        else
                        {
                            if (document.getElementById(\'tsummary\').style.display="table")
                            {
                                document.getElementById(\'tsummary\').style.display="none";
                                document.getElementById(\'divsummary\').innerHTML = \'[+] '.$lang_char['summary'].'\' ;
                            }
                            for(x in main_cats)
                            {
                                if (main_cats[x] == thistag)
                                {
                                    i = 1;
                                }
                            }

                            if (i == 1)
                            {
                                for(x in main_cats)
                                {
                                    if (main_cats[x] == thistag)
                                    {
                                        if(document.getElementById(main_cats[x]).style.display=="table")
                                        {
                                            document.getElementById(main_cats[x]).style.display="none";
                                            document.getElementById(main_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                                            document.getElementById(\'tsummary\').style.display="table";
                                            document.getElementById(\'divsummary\').innerHTML = \'[-] '.$lang_char['summary'].'\' ;
                                        }
                                        else
                                        {
                                            document.getElementById(main_cats[x]).style.display="table";
                                            document.getElementById(main_cats_achieve[x]).style.display="table";
                                            document.getElementById(main_cats_div[x]).innerHTML = \'[-] \' + main_cats_name[x];
                                        }
                                    }
                                    else
                                    {
                                        if(document.getElementById(main_cats[x]).style.display=="table")
                                        {
                                            document.getElementById(main_cats[x]).style.display="none";
                                            document.getElementById(main_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                                        }
                                    }
                                }
                                for(x in main_sub_cats)
                                {
                                    if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                    {
                                        document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                        document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                    }
                                }
                            }
                            else if (i == 0)
                            {
                                for(x in main_sub_cats)
                                {
                                    if (main_sub_cats[x] == thistag)
                                    {
                                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                        {
                                            document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                        }
                                        else
                                        {
                                            document.getElementById(main_sub_cats_achieve[x]).style.display="table";
                                            document.getElementById(main_sub_cats_div[x]).innerHTML = \'[-] \' + main_sub_cats_name[x];
                                        }
                                    }
                                    else
                                    {
                                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                        {
                                            document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                        }
                                    }
                                }
                                for(x in main_cats)
                                {
                                    if(document.getElementById(main_cats_achieve[x]).style.display=="table")
                                    {
                                        document.getElementById(main_cats_achieve[x]).style.display="none";
                                    }
                                }
                            }
                        }
                    }
                </script>';
            $output .= '
                <center>
                    <div id="tab_content">
                        <h1>'.$lang_char['achievements'].'</h1>
                        <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                        <br /><br />';
            //---------------Page Specific Data Starts Here--------------------------

            $sqlm = new SQL;
            $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

            $output .= '
                        <table class="top_hidden" style="width: 90%;">
                            <tr>
                                <td width="30%">
                                </td>
                                %%REPLACE_POINTS%%
                                <td align="right">
                                    <form action="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'" method="post" name="form">
                                        '.$lang_char['show'].' :
                                        <select name="show_type">
                                            <option value="1"';
            if (1 == $show_type)
                $output .= ' selected="selected"';

            $output .= '>'.$lang_char['all'].'</option>
                                            <option value="0"';
            if (0 == $show_type)
                $output .= ' selected="selected"';

            $output .= '>'.$lang_char['earned'].'</option>
                                            <option value="2"';
            if (2 == $show_type)
                $output .= ' selected="selected"';

            $output .= '>'.$lang_char['incomplete'].'</option>
                                        </select>
                                    </form>
                                </td>
                                <td align="right">';

            makebutton('View', 'javascript:do_submit()', 130);

            $output .= '
                                </td>
                            </tr>
                        </table>
                        <table class="lined" style="width: 90%;">
                            <tr valign="top">
                                <td width="30%">
                                    <table class="hidden" style="width: 100%">
                                        <tr>
                                            <th align="left">
                                                <div id="divsummary" onclick="expand(\'tsummary\')">[-] '.$lang_char['summary'].'</div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                            </td>
                                        </tr>';
            $result = $sqlc->query('SELECT achievement, date FROM character_achievement WHERE guid = '.$id.'');
            $char_achieve = [];
            while ($temp = $sqlc->fetch_assoc($result))
                $char_achieve[$temp['achievement']] = $temp['date'];
            $result = $sqlc->query('SELECT achievement, date FROM character_achievement WHERE guid = \''.$id.'\' order by date DESC limit 4');

            $points = 0;

            $main_cats = achieve_get_main_category($sqlm);
            $sub_cats  = achieve_get_sub_category($sqlm);

            $output_achieve_main_cat = [];
            $output_u_achieve_main_cat = [];
            $output_achieve_sub_cat = [];
            $output_u_achieve_sub_cat = [];

            $js_main_cats = '
                    var main_cats = new Array();
                    var main_cats_div = new Array();
                    var main_cats_name = new Array();
                    var main_cats_achieve = new Array();
                    var main_sub_cats = new Array();
                    var main_sub_cats_div = new Array();
                    var main_sub_cats_name = new Array();
                    var main_sub_cats_achieve = new Array();';

            foreach($main_cats as $cat_id => $cat)
            {
                if (isset($cat['name01']))
                {
                    $i=0;
                    $output_achieve_main_cat[$cat_id] = '';
                    $output_u_achieve_main_cat[$cat_id] = '';
                    $achieve_main_cat = achieve_get_id_category($cat['id'], $sqlm);
                    foreach($achieve_main_cat as $achieve_id => $cid)
                    {
                        if (isset($achieve_id) && isset($cid['id']))
                        {
                            if (isset($char_achieve[$cid['id']]))
                            {
                                if (2 > $show_type)
                                {
                                    $cid['name01'] = str_replace('&', '&amp;', $cid['name01']);
                                    $cid['description01'] = str_replace('&', '&amp;', $cid['description01']);
                                    $cid['rewarddesc01'] = str_replace('&', '&amp;', $cid['rewarddesc01']);
                                    $output_achieve_main_cat[$cat_id] .= '
                                        <tr>
                                            <td width="1%" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">
                                                    <img src="'.achieve_get_icon($cid['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                                </a>
                                            </td>
                                            <td colspan="2" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">'.$cid['name01'].'</a><br />
                                                '.$cid['description01'].'<br />
                                                '.$cid['rewarddesc01'].'
                                            </td>
                                            <td width="5%" align="right">'.$cid['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                                            <td width="15%" align="right">'.date('o-m-d', $char_achieve[$cid['id']]).'</td>
                                        </tr>';
                                    ++$i;
                                }
                                $points += $cid['rewpoints'];
                            }
                            elseif ($show_type && isset($achieve_id))
                            {
                                $cid['name01'] = str_replace('&', '&amp;', $cid['name01']);
                                $cid['description01'] = str_replace('&', '&amp;', $cid['description01']);
                                $cid['rewarddesc01'] = str_replace('&', '&amp;', $cid['rewarddesc01']);
                                $output_u_achieve_main_cat[$cat_id] .= '
                                        <tr>
                                            <td width="1%" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">
                                                    <span style="opacity:0.2;">
                                                        <img src="'.achieve_get_icon($cid['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                                    </span>
                                                </a>
                                            </td>
                                            <td colspan="2" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">'.$cid['name01'].'</a><br />
                                                '.$cid['description01'].'<br />
                                                '.$cid['rewarddesc01'].'
                                            </td>
                                            <td width="5%" align="right">'.$cid['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                                            <td width="15%" align="right">'.$lang_char['incomplete'].'</td>
                                        </tr>';
                                ++$i;
                            }
                        }
                    }
                    unset($achieve_main_cat);
                    $output_achieve_main_cat[$cat_id] = '
                                        <table class="hidden" id="ta'.$cat_id.'" style="width: 100%; display: none;">
                                            <tr>
                                                <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                                                <th width="5%">'.$lang_char['achievement_points'].'</th>
                                                <th width="15%">'.$lang_char['achievement_date'].'</th>
                                            </tr>'.$output_achieve_main_cat[$cat_id].$output_u_achieve_main_cat[$cat_id].'
                                        </table>';
                    unset($output_u_achieve_main_cat);
                    $js_main_cats .='
                        main_cats_achieve['.$cat_id.'] = "ta'.$cat_id.'";';

                    $output_sub_cat = '';
                    $total_sub_cat = 0;
                    if (isset($sub_cats[$cat['id']]))
                    {
                        $main_sub_cats = $sub_cats[$cat['id']];
                        foreach($main_sub_cats as $sub_cat_id => $sub_cat)
                        {
                            if (isset($sub_cat))
                            {
                                $j=0;
                                $output_achieve_sub_cat[$sub_cat_id] = '';
                                $output_u_achieve_sub_cat[$sub_cat_id] = '';
                                $achieve_sub_cat = achieve_get_id_category($sub_cat_id, $sqlm);
                                foreach($achieve_sub_cat as $achieve_id => $cid)
                                {
                                    if (isset($achieve_id) && isset($cid['id']))
                                    {
                                        if (isset($char_achieve[$cid['id']]))
                                        {
                                            if (2 > $show_type)
                                            {
                                                $cid['name01'] = str_replace('&', '&amp;', $cid['name01']);
                                                $cid['description01'] = str_replace('&', '&amp;', $cid['description01']);
                                                $cid['rewarddesc01'] = str_replace('&', '&amp;', $cid['rewarddesc01']);
                                                $output_achieve_sub_cat[$sub_cat_id] .= '
                                        <tr>
                                            <td width="1%" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">
                                                    <img src="'.achieve_get_icon($cid['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                                </a>
                                            </td>
                                            <td colspan="2" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">'.$cid['name01'].'</a><br />
                                                '.$cid['description01'].'<br />
                                                '.$cid['rewarddesc01'].'
                                            </td>
                                            <td width="5%" align="right">'.$cid['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                                            <td width="15%" align="right">'.date('o-m-d', $char_achieve[$cid['id']]).'</td>
                                        </tr>';
                                                ++$j;
                                            }
                                            $points += $cid['rewpoints'];
                                        }
                                        elseif ($show_type && isset($achieve_id))
                                        {
                                            $cid['name01'] = str_replace('&', '&amp;', $cid['name01']);
                                            $cid['description01'] = str_replace('&', '&amp;', $cid['description01']);
                                            $cid['rewarddesc01'] = str_replace('&', '&amp;', $cid['rewarddesc01']);
                                            $output_u_achieve_sub_cat[$sub_cat_id] .= '
                                        <tr>
                                            <td width="1%" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">
                                                    <span style="opacity:0.2;">
                                                        <img src="'.achieve_get_icon($cid['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                                    </span>
                                                </a>
                                            </td>
                                            <td colspan="2" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">'.$cid['name01'].'</a><br />
                                                '.$cid['description01'].'<br />
                                                '.$cid['rewarddesc01'].'
                                            </td>
                                            <td width="5%" align="right">'.$cid['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                                            <td width="15%" align="right">'.$lang_char['incomplete'].'</td>
                                        </tr>';
                                            ++$j;
                                        }
                                    }
                                }
                                unset($achieve_sub_cat);
                                $total_sub_cat = $total_sub_cat + $j;
                                if($j)
                                {
                                    $sub_cat = str_replace('&', '&amp;', $sub_cat);
                                    $output_sub_cat .='
                                        <tr>
                                            <th align="left">
                                                <div id="divs'.$sub_cat_id.'" onclick="expand(\'tsa'.$sub_cat_id.'\');">[+] '.$sub_cat.' ('.$j.')</div>
                                            </th>
                                        </tr>';
                                    $js_main_cats .='
                                        main_sub_cats['.$sub_cat_id.']      = "tsa'.$sub_cat_id.'";
                                        main_sub_cats_div['.$sub_cat_id.']  = "divs'.$sub_cat_id.'";
                                        main_sub_cats_name['.$sub_cat_id.'] = "'.$sub_cat.' ('.$j.')";';
                                    $output_achieve_sub_cat[$sub_cat_id] = '
                                        <table class="hidden" id="tsa'.$sub_cat_id.'" style="width: 100%; display: none;">
                                            <tr>
                                                <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                                                <th width="5%">'.$lang_char['achievement_points'].'</th>
                                                <th width="15%">'.$lang_char['achievement_date'].'</th>
                                            </tr>'.$output_achieve_sub_cat[$sub_cat_id].$output_u_achieve_sub_cat[$sub_cat_id].'
                                        </table>';
                                    unset($output_u_achieve_sub_cat);
                                    $js_main_cats .='
                                        main_sub_cats_achieve['.$sub_cat_id.'] = "tsa'.$sub_cat_id.'";';
                                }
                            }
                        }
                        unset($main_sub_cats);
                    }
                    if($total_sub_cat || $i)
                    {
                        $cat['name01'] = str_replace('&', '&amp;', $cat['name01']);
                        $output .='
                                        <tr>
                                            <th align="left">
                                                <div id="div'.$cat_id.'" onclick="expand(\'t'.$cat_id.'\');">[+] '.$cat['name01'].' ('.($i+$total_sub_cat).')</div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table class="hidden" id="t'.$cat_id.'" style="width: 100%; display: none;">'.$output_sub_cat.'
                                                </table>
                                            </td>
                                        </tr>';
                        $js_main_cats .='
                                main_cats['.$cat_id.']      = "t'.$cat_id.'";
                                main_cats_div['.$cat_id.']  = "div'.$cat_id.'";
                                main_cats_name['.$cat_id.'] = "'.$cat['name01'].' ('.($i+$total_sub_cat).')";';
                    }
                    unset($output_sub_cat);
                }
            }
            unset($sub_cats);
            unset($main_cats);
            unset($char_achieve);

            $output = str_replace('%%REPLACE%%', $js_main_cats, $output);
            unset($js_main_cats);
            $output = str_replace('%%REPLACE_POINTS%%', '
                                            <td align="right">
                                                '.$lang_char['achievements'].' '.$lang_char['achievement_points'].': '.$points.'
                                            </td>', $output);
            unset($point);
            $output .= '
                                    </table>
                                </td>
                                <td>';

            foreach($output_achieve_main_cat as $temp)
                $output .= $temp;
            foreach($output_achieve_sub_cat as $temp)
                $output .= $temp;

            unset($temp);
            unset($output_achieve_main_cat);
            unset($output_achieve_sub_cat);

            $output .= '
                                    <table class="hidden" id="tsummary" style="width: 100%; display: table;">
                                        <tr>
                                            <th colspan="5">
                                                '.$lang_char['recent'].' '.$lang_char['achievements'].'
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                                            <th width="5%">'.$lang_char['achievement_points'].'</th>
                                            <th width="15%">'.$lang_char['achievement_date'].'</th>
                                        </tr>';
            while ($temp = $sqlc->fetch_assoc($result))
            {
                $cid = achieve_get_details($temp['achievement'], $sqlm);
                $cid['name01'] = str_replace('&', '&amp;', $cid['name01']);
                $cid['description01'] = str_replace('&', '&amp;', $cid['description01']);
                $cid['rewarddesc01'] = str_replace('&', '&amp;', $cid['rewarddesc01']);
                $output .= '
                                        <tr>
                                            <td width="1%" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">
                                                    <img src="'.achieve_get_icon($cid['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                                </a>
                                            </td>
                                            <td colspan="2" align="left">
                                                <a href="'.$achievement_datasite.$cid['id'].'" target="_blank">'.$cid['name01'].'</a><br />
                                                '.$cid['description01'].'<br />
                                                '.$cid['rewarddesc01'].'
                                            </td>
                                            <td width="5%" align="right">'.$cid['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                                            <td width="15%" align="right">'.date('o-m-d', $temp['date']).'</td>
                                        </tr>';
            }
            unset($cid);
            unset($temp);
            unset($result);
            $output .= '
                                    </table>
                                </td>';
            //---------------Page Specific Data Ends here----------------------------
            //---------------Character Tabs Footer-----------------------------------
            $output .= '
                            </tr>
                        </table>
                    </div>
                </div>
                <br />';

            require_once 'core/char/char_footer.php';

            $output .= '
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
char_achievements($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
