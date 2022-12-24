<?php

require_once 'header.php';
valid_login($action_permission['read']);

function stats($action, &$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_stat, $lang_id_tab, $lang_index,
           $realm_id, $realm_db, $theme;

    $race = Array
    (
        1  => array(1, $lang_id_tab['human'],'',''),
        2  => array(2, $lang_id_tab['orc'],'',''),
        3  => array(3, $lang_id_tab['dwarf'],'',''),
        4  => array(4, $lang_id_tab['nightelf'],'',''),
        5  => array(5, $lang_id_tab['undead'],'',''),
        6  => array(6, $lang_id_tab['tauren'],'',''),
        7  => array(7, $lang_id_tab['gnome'],'',''),
        8  => array(8, $lang_id_tab['troll'],'',''),
        10 => array(10,$lang_id_tab['bloodelf'],'',''),
        11 => array(11,$lang_id_tab['draenei'],'','')
    );

    $class = Array
    (
        1  => array(1, $lang_id_tab['warrior'],'',''),
        2  => array(2, $lang_id_tab['paladin'],'',''),
        3  => array(3, $lang_id_tab['hunter'],'',''),
        4  => array(4, $lang_id_tab['rogue'],'',''),
        5  => array(5, $lang_id_tab['priest'],'',''),
        6  => array(6, $lang_id_tab['death_knight'],'',''),
        7  => array(7, $lang_id_tab['shaman'],'',''),
        8  => array(8, $lang_id_tab['mage'],'',''),
        9  => array(9, $lang_id_tab['warlock'],'',''),
        11 => array(11,$lang_id_tab['druid'],'','')
    );

    $level = Array
    (
        1 => array(1,1,9,'',''),
        2 => array(2,10,19,'',''),
        3 => array(3,20,29,'',''),
        4 => array(4,30,39,'',''),
        5 => array(5,40,49,'',''),
        6 => array(6,50,59,'',''),
        7 => array(7,60,69,'',''),
        8 => array(8,70,79,'',''),
        9 => array(9,80,80,'','')
    );

    $total_chars = $sqlc->result($sqlc->query('SELECT count(*) FROM characters'.( ($action) ? ' WHERE online= 1' : '' ).''), 0);

    if ($total_chars)
    {
        $output .= '
                <center>
                    <div id="tab">
                        <ul>
                            <li'.(($action) ? '' : ' id="selected"').'>
                                <a href="stat.php">
                                    '.$lang_stat['srv_statistics'].'
                                </a>
                            </li>
                            <li'.(($action) ? ' id="selected"' : '').'>
                                <a href="stat.php?action=true">
                                    '.$lang_stat['on_statistics'].'
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div id="tab_content">
                        <div class="top"><h1>'.(($action) ? $lang_stat['on_statistics'] : $lang_stat['srv_statistics']).'</h1></div>
                            <center>
                                <table class="hidden">
                                    <tr>
                                        <td align="left">
                                            <h1>'.$lang_stat['general_info'].'</h1>
                                        </td>
                                    </tr>
                                    <tr align="left">
                                        <td class="large">';
        //if($action)
        if (1>2) //disable for testing purposes
            $output .= '
                      <font class="bold">'.$lang_index['tot_users_online'].' : '.$total_chars.'</font><br /><br />';
        else
        {
            $query = $sqlr->query('SELECT count(*) FROM account UNION ALL SELECT count(*) FROM account_access WHERE SecurityLevel > 0');

            $total_acc = $sqlr->result($query, 0);
            $total_gms = $sqlr->result($query, 1);
            unset($query);

            $data = date('Y-m-d H:i:s');
            $data_1day = mktime(date('H'), date('i'), date('s'), date('m'), date('d')-1, date('Y'));
            $data_1day = date('Y-m-d H:i:s', $data_1day);
            $uniqueIPs = $sqlr->result($sqlr->query('select distinct count(last_ip) from account where last_login > \''.$data_1day.'\' and last_login < \''.$data.'\''), 0);

            $data_2day = mktime(date('H'), date('i'), date('s'), date('m'), date('d')-2, date('Y'));
            $data_2day = date('Y-m-d H:i:s', $data_2day);
            $uniqueIPs2 = $sqlr->result($sqlr->query('select distinct count(last_ip) from account where last_login > \''.$data_2day.'\' and last_login < \''.$data.'\''), 0);

            $data_1week = mktime(date('H'), date('i'), date('s'), date('m'), date('d')-7, date('Y'));
            $data_1week = date('Y-m-d H:i:s', $data_1week);
            $uniqueIPsWeek = $sqlr->result($sqlr->query('select distinct count(last_ip) from account where last_login > \''.$data_1week.'\' and last_login < \''.$data.'\''), 0);

            $max_ever = $sqlr->result($sqlr->query('SELECT maxplayers FROM uptime WHERE realmid = '.$realm_id.' ORDER BY maxplayers DESC LIMIT 1'), 0);
            $max_restart = $sqlr->result($sqlr->query('SELECT maxplayers FROM uptime WHERE realmid = '.$realm_id.' ORDER BY starttime DESC LIMIT 1'), 0);

            $uptime = $sqlr->fetch_row($sqlr->query('SELECT AVG(uptime)/60, MAX(uptime)/60, ( 100*SUM(uptime)/( UNIX_TIMESTAMP()-MIN(starttime) ) ) FROM uptime WHERE realmid = '.$realm_id.''));

            $output .= '
                                            <table>
                                                <tr valign="top" align="center">
                                                    <td align="left" width="210">
                                                        '.$lang_stat['uptime_prec'].':<br />
                                                        '.$lang_stat['avg_uptime'].':<br />
                                                        '.$lang_stat['max_uptime'].':<br />
                                                        <br />
                                                        '.$lang_stat['tot_accounts'].':<br />
                                                        '.$lang_stat['tot_chars_on_realm'].':<br />
                                                    </td>
                                                    <td align="left">
                                                        '.round($uptime[2],1).'%<br />
                                                        '.(int)($uptime[0]/60).':'.(int)(($uptime[0]%60)).'h<br />
                                                        '.(int)($uptime[1]/60).':'.(int)(($uptime[1]%60)).'h<br />
                                                        <br />
                                                        '.$total_acc.'<br />
                                                        '.$total_chars.'<br />
                                                    </td>
                                                    <td></td>
                                                    <td align="left">
                                                        '.$lang_stat['unique_ip'].':<br />
                                                        '.$lang_stat['unique_ip2'].':<br />
                                                        '.$lang_stat['unique_ipWeek'].':<br />
                                                        <br />
                                                        '.$lang_stat['max_players'].' :<br />
                                                        '.$lang_stat['max_ever'].' :<br />
                                                        '.$lang_stat['max_restart'].' :<br />
                                                    </td>
                                                    <td align="right">
                                                        '.$uniqueIPs.'<br />
                                                        '.$uniqueIPs2.'<br />
                                                        '.$uniqueIPsWeek.'<br />
                                                        <br />
                                                        <br />
                                                        '.$max_ever.'<br />
                                                        '.$max_restart.'<br />
                                                    </td>
                                                </tr>
                                                <tr align="left">
                                                    <td colspan="2">
                                                        '.$lang_stat['average_of'].' '.round($total_chars/$total_acc,1).' '.$lang_stat['chars_per_acc'].'<br />
                                                        '.$lang_stat['total_of'].' '.$total_gms.' '.$lang_stat['gms_one_for'].' '.round($total_acc ?? 1/$total_gms ?? 1,1).' '.$lang_stat['players'].'
                                                    </td>
                                                    <td colspan="2">
                                                    </td>
                                                </tr>
                                            </table>
                                            <br />';
            unset($uptime);
            unset($uniqueIPs);
            unset($max_restart);
            unset($max_ever);
            unset($total_gms);
            unset($total_acc);
        }


        // Total players in 24 Hours
        $horde1day = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(2,5,6,8,10) AND account IN (SELECT account.id FROM '.$realm_db['name'].'.account WHERE last_login > \''.$data_1day.'\')'));
        $allys1day = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(1,3,4,7,11) AND account IN (SELECT account.id FROM '.$realm_db['name'].'.account WHERE last_login > \''.$data_1day.'\')'));
        $day1total = $horde1day + $allys1day;

        if ($day1total == 0)
            $day1total = 1;

        $horde1daytotal = round(($horde1day)*100/$day1total ,1);
        $allys1daytotal = round(($allys1day)*100/$day1total ,1);

        // Total players in 48 Hours
        $horde2day = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(2,5,6,8,10) AND account IN (SELECT account.id FROM '.$realm_db['name'].'.account WHERE last_login > \''.$data_2day.'\')'));
        $allys2day = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(1,3,4,7,11) AND account IN (SELECT account.id FROM '.$realm_db['name'].'.account WHERE last_login > \''.$data_2day.'\')'));
        $day2total = $horde2day + $allys2day;

        if ($day2total == 0)
            $day2total = 1;

        $horde2daytotal = round(($horde1day)*100/$day2total ,1);
        $allys2daytotal = round(($allys1day)*100/$day2total ,1);

        // Total players in 1 Week
        $horde1week = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(2,5,6,8,10) AND account IN (SELECT account.id FROM '.$realm_db['name'].'.account WHERE last_login > \''.$data_1week.'\')'));
        $allys1week = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(1,3,4,7,11) AND account IN (SELECT account.id FROM '.$realm_db['name'].'.account WHERE last_login > \''.$data_1week.'\')'));
        $week1total = $horde1week + $allys1week;

        if ($week1total == 0)
            $week1total = 1;

        $horde1weektotal = round(($horde1week)*100/$week1total ,1);
        $allys1weektotal = round(($allys1week)*100/$week1total ,1);

        // Total players
        $horde_chars  = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race IN(2,5,6,8,10)'.(($action) ? ' AND online= 1' : '')), 0);
        $horde_pros   = round(($horde_chars*100)/$total_chars ,1);
        $allies_chars = $total_chars - $horde_chars;
        $allies_pros  = 100 - $horde_pros;

        $output .= '
                                            <p align="center"><b>'.$lang_stat['acc_24'].'</b></p>
                                            <table class="tot_bar">
                                                <tr>
                                                    <td width="'.$horde1daytotal.'%" background="img/bar_horde.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=h">'.$lang_stat['horde'].': '.$horde1day.' ('.$horde1daytotal.'%)</a></td>
                                                    <td width="'.$allys1daytotal.'%" background="img/bar_allie.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=a">'.$lang_stat['alliance'].': '.$allys1day.' ('.$allys1daytotal.'%)</a></td>
                                                </tr>
                                            </table>

                                            <p align="center"><b>'.$lang_stat['acc_48'].'</b></p>
                                            <table class="tot_bar">
                                                <tr>
                                                    <td width="'.$horde2daytotal.'%" background="img/bar_horde.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=h">'.$lang_stat['horde'].': '.$horde2day.' ('.$horde2daytotal.'%)</a></td>
                                                    <td width="'.$allys2daytotal.'%" background="img/bar_allie.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=a">'.$lang_stat['alliance'].': '.$allys2day.' ('.$allys2daytotal.'%)</a></td>
                                                </tr>
                                            </table>

                                            <p align="center"><b>'.$lang_stat['acc_7'].'</b></p>
                                            <table class="tot_bar">
                                                <tr>
                                                    <td width="'.$horde1weektotal.'%" background="img/bar_horde.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=h">'.$lang_stat['horde'].': '.$horde1week.' ('.$horde1weektotal.'%)</a></td>
                                                    <td width="'.$allys1weektotal.'%" background="img/bar_allie.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=a">'.$lang_stat['alliance'].': '.$allys1week.' ('.$allys1weektotal.'%)</a></td>
                                                </tr>
                                            </table>

                                            <p align="center"><b>'.$lang_stat['acc_total'].'</b></p>
                                            <table class="tot_bar">
                                                <tr>
                                                    <td width="'.$horde_pros.'%" background="img/bar_horde.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=h">'.$lang_stat['horde'].': '.$horde_chars.' ('.$horde_pros.'%)</a></td>
                                                    <td width="'.$allies_pros.'%" background="img/bar_allie.gif" height="30"><a href="stat.php?action='.$action.'&amp;side=a">'.$lang_stat['alliance'].': '.$allies_chars.' ('.$allies_pros.'%)</a></td>
                                                </tr>
                                            </table>';

        unset($horde_chars);
        unset($horde_pros);
        unset($allies_chars);
        unset($allies_pros);

        unset($data_1);
        unset($data_2day);
        unset($data_1week);
        unset($data);

        $order_race = (isset($_GET['race'])) ? 'AND race ='.$sqlc->quote_smart($_GET['race']) : '';
        $order_class = (isset($_GET['class'])) ? 'AND class ='.$sqlc->quote_smart($_GET['class']) : '';

        if(isset($_GET['level']))
        {
            $lvl_min = $sqlc->quote_smart($_GET['level']);
            $lvl_max = $lvl_min + 4;
            $order_level = 'AND level >= '.$lvl_min.' AND level <= '.$lvl_max.'';
        }
        else
            $order_level = '';

        if(isset($_GET['side']))
        {
            if ('h' == $sqlc->quote_smart($_GET['side']))
                $order_side = 'AND race IN(2,5,6,8,10)';
            elseif ('a' == $sqlc->quote_smart($_GET['side']))
                $order_side = 'AND race IN (1,3,4,7,11)';
        }
        else
            $order_side = '';

        // RACE
        foreach ($race as $id)
        {
            $race[$id[0]][2] = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE race = '.$id[0].' '.$order_class.' '.$order_level.' '.$order_side.(($action) ? ' AND online= 1' : '')), 0);
            $race[$id[0]][3] = round((($race[$id[0]][2])*100)/$total_chars,1);
        }

        $output .= '
                                    <tr align="left">
                                        <td>
                                            <h1>'.$lang_stat['chars_by_race'].'</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table class="bargraph">
                                                <tr>';
        foreach ($race as $id)
        {
            $height = ($race[$id[0]][3])*4;
            $output .= '
                                                    <td>
                                                        <a href="stat.php?action='.$action.'&amp;race='.$id[0].'" class="graph_link">'.$race[$id[0]][3].'%<img src="themes/'.$theme.'/column.gif" width="69" height="'.$height.'" alt="'.$race[$id[0]][2].'" /></a>
                                                    </td>';
        }
        $output .= '
                                                </tr>
                                                <tr>';
        foreach ($race as $id)
            $output .= '
                                                    <th>'.$race[$id[0]][1].'<br />'.$race[$id[0]][2].'</th>';

        unset($race);
        $output .= '
                                                </tr>
                                            </table>
                                            <br />
                                        </td>
                                    </tr>';

        // RACE END
        // CLASS
        foreach ($class as $id)
        {
            $class[$id[0]][2] = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE class = '.$id[0].' '.$order_race.' '.$order_level.' '.$order_side.(($action) ? ' AND online= 1' : '')), 0);
            $class[$id[0]][3] = round((($class[$id[0]][2])*100)/$total_chars,1);
        }
        unset($order_level);

        $output .= '
                                    <tr align="left">
                                        <td>
                                            <h1>'.$lang_stat['chars_by_class'].'</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table class="bargraph">
                                                <tr>';
        foreach ($class as $id)
        {
            $height = ($class[$id[0]][3])*4;
            $output .= '
                                                    <td>
                                                        <a href="stat.php?action='.$action.'&amp;class='.$id[0].'" class="graph_link">'.$class[$id[0]][3].'%<img src="themes/'.$theme.'/column.gif" width="69" height="'.$height.'" alt="'.$class[$id[0]][2].'" /></a>
                                                    </td>';
        }
        $output .= '
                                                </tr>
                                                <tr>';
        foreach ($class as $id)
        {
            $output .= '
                                                    <th>'.$class[$id[0]][1].'<br />'.$class[$id[0]][2].'</th>';
        }
    unset($class);
    $output .= '
                                                </tr>
                                            </table>
                                            <br />
                                        </td>
                                    </tr>';

    // CLASS END
    // LEVEL
    foreach ($level as $id)
    {
        $level[$id[0]][3] = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters WHERE level >= '.$id[1].' AND level <= '.$id[2].'
                            '.$order_race.' '.$order_class.' '.$order_side.(($action) ? ' AND online= 1' : '').''), 0);
        $level[$id[0]][4] = round((($level[$id[0]][3])*100)/$total_chars,1);
    }
    unset($order_level);
    unset($order_class);
    unset($order_race);
    unset($total_chars);
    unset($order_side);

    $output .= '
                    <tr align="left">
                        <td>
                            <h1>'.$lang_stat['chars_by_level'].'</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="bargraph">
                                <tr>';
    foreach ($level as $id)
    {
        $height = ($level[$id[0]][4])*3;
        $output .= '
                                    <td><a href="stat.php?action='.$action.'&amp;level='.$id[1].'" class="graph_link">'.$level[$id[0]][4].'%<img src="themes/'.$theme.'/column.gif" width="77" height="'.$height.'" alt="'.$level[$id[0]][3].'" /></a></td>';
    }
    unset($height);

    $output .= '
                                </tr>
                                <tr>';
    foreach ($level as $id)
        $output .= '
                                    <th>'.$level[$id[0]][1].'-'.$level[$id[0]][2].'<br />'.$level[$id[0]][3].'</th>';
    unset($id);
    unset($level);

    $output .= '
                                </tr>
                            </table>
                            <br />
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                        <td>';
    // LEVEL END

    makebutton($lang_stat['reset'], 'stat.php', 720);

    $output .= '
                        </td>
                    </tr>
                </table>
            </center>
            </div>
            <br />
        </center>';
    }
    else
        error($lang_global['err_no_result']);
}


//#############################################################################
// MAIN
//#############################################################################
//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//unset($err);

$lang_index = lang_index();
$lang_stat = lang_stat();

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

stats($action, $sqlr, $sqlc);

unset($action);
unset($action_permission);
unset($lang_index);
unset($lang_stat);

require_once 'footer.php';


?>
