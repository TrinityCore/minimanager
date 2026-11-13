<?php

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once("libs/map_zone_lib.php");
valid_login($action_permission['read']);

//########################################################################################################################
//  BROWSE CHARS
//########################################################################################################################
function browse_chars(&$sqlr, &$sqlc)
{
    global $output, $lang_char_list, $lang_global,
            $realm_db, $mmfpm_db, $characters_db, $realm_id,
            $action_permission, $user_lvl, $user_name,
            $showcountryflag, $itemperpage;

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    //==========================$_GET and SECURE========================
    $start = (isset($_GET['start'])) ? $sqlr->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlr->quote_smart($_GET['order_by']) : 'guid';
    if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by));
    else
        $order_by = 'guid';

    $dir = (isset($_GET['dir'])) ? $sqlr->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir=1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end========================

    if ($order_by == 'map')
        $order_by = 'map '.$order_dir.', zone';
    elseif ($order_by == 'zone')
        $order_by = 'zone '.$order_dir.', map';

    // Multi-condition search support (AND)
    $search_values = isset($_GET['search_value']) ? (array)$_GET['search_value'] : [''];
    $search_bys = isset($_GET['search_by']) ? (array)$_GET['search_by'] : ['name'];
    $search_menu = ['name', 'guid', 'account', 'level', 'greater_level', 'lesser_level', 'guild', 'race', 'class', 'map', 'greater_rank', 'online', 'gold', 'item', 'gender', 'logout_time'];

    $where_clauses = [];
    $search_url = '';
    $active_filters = [];
    $has_item_search = false;
    $item_search_val = 0;

    for ($idx = 0; $idx < count($search_values); $idx++)
    {
        $val_raw = trim((string)$search_values[$idx]);
        $field = isset($search_bys[$idx]) ? $search_bys[$idx] : 'name';
        if (!in_array($field, $search_menu))
            $field = 'name';

        if ($val_raw === '')
            continue;

        $active_filters[] = ['by' => $field, 'val' => $val_raw];
        $search_url .= '&amp;search_by%5B%5D=' . urlencode($field) . '&amp;search_value%5B%5D=' . urlencode($val_raw);
        $val = $sqlc->quote_smart($val_raw);

        if ($field === 'account')
        {
            $val_r = $sqlr->quote_smart($val_raw);
            $res_acc = $sqlr->query("SELECT id FROM account WHERE username LIKE '%$val_r%'");
            $acc_ids = [0];
            while ($r_acc = $sqlr->fetch_row($res_acc))
                $acc_ids[] = intval($r_acc[0]);
            $where_clauses[] = 'characters.account IN (' . implode(',', $acc_ids) . ')';
        }
        elseif ($field === 'level')
            $where_clauses[] = 'characters.level = ' . intval($val_raw);
        elseif ($field === 'greater_level')
            $where_clauses[] = 'characters.level > ' . intval($val_raw);
        elseif ($field === 'lesser_level')
            $where_clauses[] = 'characters.level < ' . intval($val_raw);
        elseif ($field === 'gold')
            $where_clauses[] = 'characters.money > ' . intval($val_raw);
        elseif ($field === 'greater_rank')
            $where_clauses[] = 'characters.totalKills > ' . intval($val_raw);
        elseif ($field === 'guild')
        {
            $res_g = $sqlc->query("SELECT guildid FROM guild WHERE name LIKE '%$val%'");
            $g_ids = [0];
            while ($r_g = $sqlc->fetch_row($res_g))
                $g_ids[] = intval($r_g[0]);
            $where_clauses[] = 'characters.guid IN (SELECT guid FROM guild_member WHERE guildid IN (' . implode(',', $g_ids) . '))';
        }
        elseif ($field === 'item')
        {
            $has_item_search = true;
            $item_search_val = intval($val_raw);
            $where_clauses[] = 'characters.guid IN (SELECT owner_guid FROM item_instance WHERE itemEntry = ' . $item_search_val . ')';
        }
        elseif ($field === 'race')
        {
            if (is_numeric($val_raw))
                $where_clauses[] = 'characters.race = ' . intval($val_raw);
            else
            {
                $r_ids = [0];
                for ($ri = 1; $ri <= 25; $ri++)
                    if (stripos((string)char_get_race_name($ri), $val_raw) !== false) $r_ids[] = $ri;
                $where_clauses[] = 'characters.race IN (' . implode(',', $r_ids) . ')';
            }
        }
        elseif ($field === 'class')
        {
            if (is_numeric($val_raw))
                $where_clauses[] = 'characters.class = ' . intval($val_raw);
            else
            {
                $c_ids = [0];
                for ($ci = 1; $ci <= 15; $ci++)
                    if (stripos((string)char_get_class_name($ci), $val_raw) !== false) $c_ids[] = $ci;
                $where_clauses[] = 'characters.class IN (' . implode(',', $c_ids) . ')';
            }
        }
        elseif ($field === 'gender')
        {
            $gl = strtolower($val_raw);
            if ($val_raw === '0' || in_array($gl, ['m', 'male', 'masculino', 'hombre']))
                $where_clauses[] = 'characters.gender = 0';
            elseif ($val_raw === '1' || in_array($gl, ['f', 'female', 'femenino', 'mujer']))
                $where_clauses[] = 'characters.gender = 1';
            else
                $where_clauses[] = '1=0';
        }
        elseif ($field === 'logout_time')
        {
            if (preg_match('/^([<>=])?\s*(\d{4}-\d{2}-\d{2})$/', $val_raw, $dm))
            {
                $op = !empty($dm[1]) ? $dm[1] : '=';
                $ts_start = strtotime($dm[2] . ' 00:00:00');
                $ts_end   = strtotime($dm[2] . ' 23:59:59');
                if ($op === '<')
                    $where_clauses[] = 'characters.logout_time > 0 AND characters.logout_time < ' . $ts_start;
                elseif ($op === '>')
                    $where_clauses[] = 'characters.logout_time > ' . $ts_end;
                else
                    $where_clauses[] = 'characters.logout_time BETWEEN ' . $ts_start . ' AND ' . $ts_end;
            }
            else
                $where_clauses[] = '1=0';
        }
        else
            $where_clauses[] = "characters.$field LIKE '%$val%'";
    }

    $has_search = !empty($where_clauses);
    if (!$has_search)
        $active_filters[] = ['by' => 'name', 'val' => ''];

    $where_sql = $has_search ? implode(' AND ', $where_clauses) . ' AND ' : '';
    if ($has_item_search)
        $sql_query = "SELECT characters.guid, BINARY characters.name AS name, characters.account, characters.race, characters.class, characters.zone, characters.map, characters.online, characters.level, characters.gender, characters.logout_time, COALESCE(guild_member.guildid,0) AS gname, BINARY guild.name AS guild_name, SUM(item_instance.count) as amount FROM characters LEFT JOIN guild_member ON characters.guid = guild_member.guid LEFT JOIN guild ON guild_member.guildid = guild.guildid LEFT JOIN item_instance on characters.guid = item_instance.owner_guid WHERE $where_sql itemEntry = $item_search_val AND characters.name != '' GROUP BY guid ORDER BY $order_by $order_dir LIMIT $start, $itemperpage";
    else
        $sql_query = "SELECT characters.guid, BINARY characters.name AS name, characters.account, characters.race, characters.class, characters.zone, characters.map, characters.online, characters.level, characters.gender, characters.logout_time, COALESCE(guild_member.guildid,0) AS gname, BINARY guild.name AS guild_name FROM characters LEFT JOIN guild_member ON characters.guid = guild_member.guid LEFT JOIN guild ON guild_member.guildid = guild.guildid WHERE $where_sql characters.name != '' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage";

    $query_1 = $sqlc->query("SELECT count(*) FROM `characters` WHERE $where_sql name != ''");
    $query = $sqlc->query($sql_query);

    $all_record = $sqlc->result($query_1,0);
    unset($query_1);

    $this_page = $sqlc->num_rows($query) or die(error($lang_global['err_no_result']));

    //==========================top tage navigaion starts here========================
    $output .= '
                    <script type="text/javascript" src="libs/js/check.js"></script>
                    <center>
                        <table class="top_hidden">
                            <tr>
                                <td>';
    // cleanup unknown working condition
    //if($user_lvl >= $action_permission['delete'])
    //              makebutton($lang_char_list['cleanup'], 'cleanup.php', 130);
    makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
    ($has_search) ? makebutton($lang_char_list['characters'], 'char_list.php" type="def', 130) : $output .= '';
    $output .= '
                                </td>
                                <td align="right" width="25%" rowspan="2">';
    $output .= generate_pagination('char_list.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).$search_url, $all_record, $itemperpage, $start);
    $output .= "
                                </td>
                            </tr>
                            <tr align=\"left\">
                                <td>
                                    <table class=\"hidden\">
                                        <tr>
                                            <td>
                                                <script type=\"text/javascript\">
                                                    function addCharFilter() {
                                                        var container = document.getElementById(\"char_filters_container\");
                                                        var rows = container.getElementsByClassName(\"char_filter_row\");
                                                        var clone = rows[0].cloneNode(true);
                                                        clone.getElementsByTagName(\"input\")[0].value = \"\";
                                                        var remBtn = clone.getElementsByClassName(\"rem_char_filter_btn\")[0];
                                                        if (remBtn) remBtn.style.display = \"inline-block\";
                                                        container.appendChild(clone);
                                                    }
                                                    function removeCharFilter(btn) {
                                                        var container = document.getElementById(\"char_filters_container\");
                                                        if (container.getElementsByClassName(\"char_filter_row\").length > 1) {
                                                            btn.parentNode.remove();
                                                        }
                                                    }
                                                </script>
                                                <form action=\"char_list.php\" method=\"get\" name=\"form\">
                                                    <input type=\"hidden\" name=\"error\" value=\"3\" />
                                                    <div id=\"char_filters_container\" style=\"display:inline-block; vertical-align:middle;\">";
    foreach ($active_filters as $f_idx => $flt)
    {
        $f_by = $flt['by'];
        $f_val = htmlspecialchars($flt['val']);
        $show_rem = ($f_idx > 0) ? 'inline-block' : 'none';
        $output .= "
                                                        <div class=\"char_filter_row\" style=\"margin-bottom:3px;\">
                                                            <input type=\"text\" size=\"24\" maxlength=\"50\" name=\"search_value[]\" value=\"{$f_val}\" placeholder='{$lang_global['search_by']}' />
                                                            <select name=\"search_by[]\" title='{$lang_global['search_by']}'>
                                                                <option value=\"name\"".($f_by == 'name' ? " selected=\"selected\"" : "").">{$lang_char_list['by_name']}</option>
                                                                <option value=\"guid\"".($f_by == 'guid' ? " selected=\"selected\"" : "").">{$lang_char_list['by_id']}</option>
                                                                <option value=\"account\"".($f_by == 'account' ? " selected=\"selected\"" : "").">{$lang_char_list['by_account']}</option>
                                                                <option value=\"level\"".($f_by == 'level' ? " selected=\"selected\"" : "").">{$lang_char_list['by_level']}</option>
                                                                <option value=\"greater_level\"".($f_by == 'greater_level' ? " selected=\"selected\"" : "").">{$lang_char_list['greater_level']}</option>
                                                                <option value=\"lesser_level\"".($f_by == 'lesser_level' ? " selected=\"selected\"" : "").">{$lang_char_list['lesser_level']}</option>
                                                                <option value=\"guild\"".($f_by == 'guild' ? " selected=\"selected\"" : "").">{$lang_char_list['by_guild']}</option>
                                                                <option value=\"race\"".($f_by == 'race' ? " selected=\"selected\"" : "").">{$lang_char_list['by_race_id']}</option>
                                                                <option value=\"class\"".($f_by == 'class' ? " selected=\"selected\"" : "").">{$lang_char_list['by_class_id']}</option>
                                                                <option value=\"gender\"".($f_by == 'gender' ? " selected=\"selected\"" : "").">Género</option>
                                                                <option value=\"logout_time\"".($f_by == 'logout_time' ? " selected=\"selected\"" : "").">Last Online (&lt; = &gt; YYYY-MM-DD)</option>
                                                                <option value=\"map\"".($f_by == 'map' ? " selected=\"selected\"" : "").">{$lang_char_list['by_map_id']}</option>
                                                                <option value=\"online\"".($f_by == 'online' ? " selected=\"selected\"" : "").">{$lang_char_list['by_online']}</option>
                                                                <option value=\"gold\"".($f_by == 'gold' ? " selected=\"selected\"" : "").">{$lang_char_list['chars_gold']}</option>
                                                                <option value=\"item\"".($f_by == 'item' ? " selected=\"selected\"" : "").">{$lang_char_list['by_item']}</option>
                                                            </select>
                                                            <button type=\"button\" class=\"rem_char_filter_btn\" onclick=\"removeCharFilter(this)\" style=\"display:$show_rem; cursor:pointer;\" title=\"Quitar filtro\">-</button>
                                                        </div>";
    }
    $output .= "
                                                    </div>
                                                    <div style=\"display:inline-block; vertical-align:top;\">
                                                        <button type=\"button\" onclick=\"addCharFilter()\" style=\"cursor:pointer;\" title=\"Añadir otra condición AND\">+</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>";
    makebutton($lang_global['search'], 'javascript:do_submit()', 80);
    $output .= '
                                            </td>
                                        </tr>
                                </table>
                            </td>
                        </tr>
                    </table>';
    //==========================top tage navigaion ENDS here ========================
    $output .= "
                    <form method=\"get\" action=\"char_list.php\" name=\"form1\">
                        <input type=\"hidden\" name=\"action\" value=\"del_char_form\" />
                        <input type=\"hidden\" name=\"start\" value=\"$start\" />
                        <table class=\"lined\">
                            <tr>
                                <th width=\"1%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form1);\" title=\"{$lang_global['check_all']}\" /></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=guid&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='guid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['id']}</a></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=name&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['char_name']}</a></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=account&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='account' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['account']}</a></th>";

    if ($has_item_search)
    {
        $output .= "
                                <th width=\"1%\"><a href=\"char_list.php?order_by=amount&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='amount' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['amount']}</a></th>";
    }
    $output .= "
                                <th width=\"1%\"><a href=\"char_list.php?order_by=race&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='race' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['race']}</a></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=class&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='class' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['class']}</a></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=level&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='level' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['level']}</a></th>
                                <th width=\"10%\"><a href=\"char_list.php?order_by=map&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='map '.$order_dir.', zone' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['map']}</a></th>
                                <th width=\"10%\"><a href=\"char_list.php?order_by=zone&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='zone '.$order_dir.', map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['zone']}</a></th>
                                <th width=\"10%\"><a href=\"char_list.php?order_by=gname&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='gname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['guild']}</a></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=logout_time&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='logout_time' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['lastseen']}</a></th>
                                <th width=\"1%\"><a href=\"char_list.php?order_by=online&amp;start=$start".$search_url."&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char_list['online']}</a></th>";

    if ($showcountryflag)
    {
        require_once 'libs/misc_lib.php';
        $output .= '
                                <th width="1%">'.$lang_global['country'].'</th>';
    }

    $output .='
                            </tr>';

    $chars = [];
    while ($char = $sqlc->fetch_row($query))
        $chars[] = $char;

    // owner accounts for the whole page in one query, to disalow lower lvl gm to view accounts of other gms
    $accounts = [];
    $account_ids = implode(',', array_map('intval', array_unique(array_column($chars, 2))));
    $result = $sqlr->query('SELECT account.id, account.username, MAX(account_access.SecurityLevel) AS gmlevel FROM account LEFT JOIN account_access ON account.id = account_access.AccountID AND (account_access.RealmID = '.(int)$realm_id.' OR account_access.RealmID = -1) WHERE account.id IN ('.$account_ids.') GROUP BY account.id, account.username');
    while ($acc = $sqlr->fetch_assoc($result))
        $accounts[$acc['id']] = $acc;

    foreach ($chars as $char)
    {
        $acc = $accounts[$char[2]] ?? null;
        $owner_gmlvl = (int)($acc['gmlevel'] ?? 0);
        $owner_acc_name = $acc['username'] ?? '';
        $lastseen = date('Y-m-d G:i:s', $char[10]);

        if (($user_lvl >= $owner_gmlvl)||($owner_acc_name == $user_name))
        {
            $output .= '
                            <tr>
                                <td>';
            if (($user_lvl >= $action_permission['delete'])||($owner_acc_name == $user_name))
                $output .= '
                                    <input type="checkbox" name="check[]" value="'.$char[0].'" onclick="CheckCheckAll(document.form1);" title="' . $lang_global['check_all'] . '" />';
            $output .= "
                                </td>
                                <td>$char[0]</td>
                                <td><a href=\"char.php?id=$char[0]\">".htmlentities($char[1])."</a></td>
                                <td><a href=\"user.php?action=edit_user&amp;error=11&amp;id=$char[2]\">".htmlentities($owner_acc_name)."</a></td>";

            if ($has_item_search)
            {
                $output .= "
                                <td>$char[13]</td>";
            }
            $output .= "
                                <td><img src='img/c_icons/{$char[3]}-{$char[9]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                                <td><img src='img/c_icons/{$char[4]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[4])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                                <td>".char_get_level_color($char[8])."</td>
                                <td class=\"small\"><span onmousemove='toolTip(\"MapID:".$char[6]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_map_name($char[6], $sqlm)."</span></td>
                                <td class=\"small\"><span onmousemove='toolTip(\"ZoneID:".$char[5]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_zone_name($char[5], $sqlm)."</span></td>
                                <td class=\"small\"><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[11]\">".htmlentities($char[12] ?? '')."</a></td>
                                <td class=\"small\">$lastseen</td>
                                <td>".(($char[7]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>";
            if ($showcountryflag)
            {
                $country = misc_get_country_by_account($char[2], $sqlr, $sqlm);
                $output .= "
                                <td>".((ctype_alpha($country['code'])) ? "<img src='img/flags/".$country['code'].".png' onmousemove='toolTip(\"".($country['country'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />" : "-")."</td>";
            }
            $output .= '
                            </tr>';
        }
        else
        {
            $output .= '
                            <tr>
                                <td>*</td>
                                <td>***</td>
                                <td>***</td>
                                <td>You</td>
                                <td>Have</td>
                                <td>No</td>
                                <td class=\"small\">Permission</td>
                                <td>to</td>
                                <td>View</td>
                                <td>this</td>
                                <td>Data</td>
                                <td>***</td>
                                <td>*</td>';
            if ($showcountryflag)
                $output .= '
                                <td>*</td>';
            $output .= '
                            </tr>';
        }
    }
    unset($char);
    unset($result);

    $output .= '
                            <tr>
                                <td colspan="13" align="right" class="hidden" width="25%">';
    $output .= generate_pagination('char_list.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).$search_url, $all_record, $itemperpage, $start);
    $output .= '
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" align="left" class="hidden">';
    if (($user_lvl >= $action_permission['delete']) || ($owner_acc_name == $user_name))
        makebutton($lang_char_list['del_selected_chars'], 'javascript:do_submit(\'form1\',0)" type="wrn', 220);

    $output .= '
                                </td>
                                <td colspan="7" align="right" class="hidden">'.$lang_char_list['tot_chars'].' : '.$all_record.'</td>
                            </tr>
                        </table>
                    </form>
                </center>';
}


//########################################################################################################################
//  DELETE CHAR
//########################################################################################################################
function del_char_form(&$sqlc)
{
    global $output, $lang_char_list, $lang_global,
            $characters_db, $realm_id,
            $action_permission;
    valid_login($action_permission['delete']);

    if(isset($_GET['check']))
        $check = $_GET['check'];
    else
        redirect('char_list.php?error=1');

    $output .= '
                <center>
                    <img src="img/warn_red.gif" width="48" height="48" alt="" />
                    <h1>
                        <font class="error">'.$lang_global['are_you_sure'].'</font>
                    </h1>
                    <br />
                    <font class="bold">'.$lang_char_list['char_ids'].': ';

    $pass_array = '';
    $n_check = count($check);
    for ($i=0; $i<$n_check; ++$i)
    {
        $name = $sqlc->result($sqlc->query('SELECT BINARY name AS name FROM characters WHERE guid = '.$check[$i].''), 0);
        $output .= '
                        <a href="char.php?id='.$check[$i].'" target="_blank">'.$name.', </a>';
        $pass_array .= '&amp;check%5B%5D='.$check[$i].'';
    }
    unset($name);
    unset($n_check);
    unset($check);

    $output .= '
                        <br />'.$lang_global['will_be_erased'].'
                    </font>
                    <br /><br />
                    <table width="300" class="hidden">
                        <tr>
                            <td>';
    makebutton($lang_global['yes'], 'char_list.php?action=dodel_char'.$pass_array.'" type="wrn', 130);
    makebutton($lang_global['no'], 'char_list.php" type="def', 130);

    unset($pass_array);
    $output .= '
                            </td>
                        </tr>
                    </table>
                </center>';
}


//########################################################################################################################
//  DO DELETE CHARS
//########################################################################################################################
function dodel_char(&$sqlc)
{
    global $output, $lang_global, $lang_char_list,
            $characters_db, $realm_id,
            $action_permission,
            $tab_del_user_characters;
    valid_login($action_permission['delete']);

    if(isset($_GET['check']))
        $check = $sqlc->quote_smart($_GET['check']);
    else
        redirect('char_list.php?error=1');

    $deleted_chars = 0;
    require_once 'libs/del_lib.php';

    $n_check = count($check);
    for ($i=0; $i<$n_check; ++$i)
    {
        if ($check[$i] == '');
        else
            if (del_char($check[$i], $realm_id))
                $deleted_chars++;
    }
    unset($n_check);
    unset($check);

    $output .= '
                <center>';

    if ($deleted_chars)
        $output .= '
                    <h1>
                        <font class="error">'.$lang_char_list['total'].' <font color=blue>'.$deleted_chars.'</font> '.$lang_char_list['chars_deleted'].'</font>
                    </h1>';
    else
        $output .= '
                    <h1>
                        <font class="error">'.$lang_char_list['no_chars_del'].'</font>
                    </h1>';

    unset($deleted_chars);
    $output .= '
                    <br /><br />';
    $output .= '
                    <table class="hidden">
                        <tr>
                            <td>';
    makebutton($lang_char_list['back_browse_chars'], 'char_list.php', 220);
    $output .= '
                            </td>
                        </tr>
                    </table>
                    <br />
                </center>';
}


//########################################################################################################################
// MAIN
//########################################################################################################################

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

$lang_char_list = lang_char_list();

switch ($err)
{
    case 1:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_global['empty_fields']}</font>
            </h1>";
        break;

    case 2:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_global['err_no_search_passed']}</font>
            </h1>";
        break;

    case 3:
        $output .="
            <h1>
                <font class=\"error\">{$lang_char_list['search_results']}:</font>
            </h1>";
        break;

    default:
        $output .= "
            <h1>{$lang_char_list['browse_chars']}</h1>";
}

unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "del_char_form":
        del_char_form($sqlc);
        break;

    case "dodel_char":
        dodel_char($sqlc);
        break;

    default:
        browse_chars($sqlr, $sqlc);
}

unset($action);
unset($action_permission);
unset($lang_char_list);

require_once("footer.php");

?>
