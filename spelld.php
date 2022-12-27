<?php


require_once 'header.php';
valid_login($action_permission['read']);

//#############################################################################
// BROWSE SPELLS
//#############################################################################
function browse_spells()
{
    global $output, $lang_spelld, $lang_global,
            $world_db, $realm_id,
            $action_permission, $user_lvl,
            $itemperpage;
    valid_login($action_permission['read']);

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : 'entry';
    if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by));
    else
        $order_by = 'entry';

    $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir = 1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    //==========================Browse/Search CHECK==============================
    $search_by = '';
    $search_value = '';
    if(isset($_GET['search_value']) && isset($_GET['search_by']))
    {
        $search_value = $sqlw->quote_smart($_GET['search_value']);
        $search_by = $sqlw->quote_smart($_GET['search_by']);
        $search_menu = ['entry', 'disable_mask', 'comment'];
        if (in_array($search_by, $search_menu));
        else
            $search_by = 'entry';

        $query_1 = $sqlw->query('SELECT count(*) FROM disables WHERE sourceType=0 AND '.$search_by.' LIKE \'%'.$search_value.'%\'');
        $result = $sqlw->query('SELECT entry, flags, comment FROM disables
                                WHERE sourceType=0 AND '.$search_by.' LIKE \'%'.$search_value.'%\' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');
    }
    else
    {
        $query_1 = $sqlw->query('SELECT count(*) FROM disables WHERE sourceType=0');
        $result = $sqlw->query('SELECT entry, flags, comment FROM disables WHERE sourceType=0
                                ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');
    }
    //get total number of items
    $all_record = $sqlw->result($query_1,0);
    unset($query_1);

    //==========================top tage navigaion starts here========================
    $output .= '
                <script type="text/javascript" src="libs/js/check.js"></script>
                <center>
                    <table class="top_hidden">
                        <tr>
                            <td>';
    if ($user_lvl >= $action_permission['insert'])
        makebutton($lang_spelld['add_spell'], 'spelld.php?action=add_new" type="wrn', 130);

    makebutton($lang_global['back'], 'javascript:window.history.back()', 130);

    ($search_by && $search_value) ? makebutton($lang_spelld['spell_list'], 'spelld.php', 130) : $output .= '';
    $output .= '
                            </td>
                            <td align="right" width="25%">';
    $output .= generate_pagination('spelld.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ), $all_record, $itemperpage, $start);
    $output .= '
                            </td>
                        </tr>
                        <tr align="left">
                            <td rowspan="2">
                                <table class="hidden">
                                    <tr>
                                        <td>
                                            <form action="spelld.php" method="get" name="form">
                                                <input type="hidden" name="error" value="3" />
                                                <input type="text" size="24\" maxlength="64" name="search_value" value="'.$search_value.'" placeholder="Search..." />
                                                <select name="search_by" title="Search by">
                                                    <option value="entry"'.($search_by == 'entry' ? ' selected="selected"' : '').'>'.$lang_spelld['by_id'].'</option>
                                                    <option value="disable_mask"'.($search_by == 'disable_mask' ? ' selected="selected"' : '').'>'.$lang_spelld['by_disable'].'</option>
                                                    <option value="comment"'.($search_by == 'comment' ? ' selected="selected"' : '').'>'.$lang_spelld['by_comment'].'</option>
                                                </select>
                                            </form>
                                        </td>
                                    <td>';
    makebutton($lang_global['search'], 'javascript:do_submit()', 80);
    $output .= '
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';
    //==========================top tage navigaion ENDS here ========================

    $output .= '
                <form method="get" action="spelld.php" name="form1">
                    <input type="hidden" name="action" value="del_spell" />
                    <input type="hidden" name="start" value="'.$start.'" />
                    <table class="lined">
                        <tr>';
    if($user_lvl >= $action_permission['delete'])
        $output .= '
                            <th width="1%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.form1);" /></th>';
    else
        $output .= '
                            <th width="1%"></th>';
    $output .= '
                            <th width="10%"><a href="spelld.php?order_by=entry&amp;start='.$start.( $search_value && $search_by ? '&amp;error=3&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='entry' ? ' class="'.$order_dir.'"' : '').'>'.$lang_spelld['entry'].'</a></th>
                            <th width="10%"><a href="spelld.php?order_by=flags&amp;start='.$start.( $search_value && $search_by ? '&amp;error=3&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='flags' ? ' class="'.$order_dir.'"' : '').'>'.$lang_spelld['disable_mask'].'</a></th>
                            <th width="70%"><a href="spelld.php?order_by=comment&amp;start='.$start.( $search_value && $search_by ? '&amp;error=3&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='comment' ? ' class="'.$order_dir.'"' : '').'>'.$lang_spelld['comment'].'</a></th>
                        </tr>
                        <tr>';

    while($spelld = $sqlw->fetch_assoc($result))
    {
        if($user_lvl >= $action_permission['delete'])
            $output .= '
                            <td><input type="checkbox" name="check[]" value="'.$spelld['entry'].'" onclick="CheckCheckAll(document.form1);" /></td>';
        else
            $output .= '
                            <td></td>';
        $output .= '
                            <td>'.$spelld['entry'].'</td>
                            <td>'.$spelld['flags'].'</td>
                            <td>'.$spelld['comment'].'</td>
                        </tr>
                        <tr>';
    }
    $output .= '
                            <td colspan="4" class="hidden" align="right" width="25%">';
    $output .= generate_pagination('spelld.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ), $all_record, $itemperpage, $start);
    $output .= '
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hidden" align="left">';

    if($user_lvl >= $action_permission['delete'])
        makebutton($lang_spelld['del_selected_spells'], 'javascript:do_submit(\'form1\',0)" type="wrn', 180);

    $output .= '
                            </td>
                            <td colspan="2" class="hidden" align="right">'.$lang_spelld['tot_spell'].' : '.$all_record.'</td>
                        </tr>
                    </table>
                </form>
                <br />
            </center>';
}


//#####################################################################################################
//  ADD NEW SPELL
//#######################################################################################################
function add_new()
{
    global $lang_global, $lang_spelld, $output, $action_permission;
    valid_login($action_permission['insert']);

    $output .= '
                <center>
                    <fieldset style="width: 550px;">
                        <legend>'.$lang_spelld['add_new_spell'].'</legend>
                        <form method="get" action="spelld.php" name="form">
                            <input type="hidden" name="action" value="doadd_new" />
                            <table class="flat">
                                <tr>
                                    <td>'.$lang_spelld['entry2'].'</td>
                                    <td><input type="text" name="entry" size="24" maxlength="11" value="" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_spelld['disable_mask2'].'</td>
                                    <td><input type="text" name="disable_mask" size="24" maxlength="8" value="" /></td>
                                </tr>
                                <tr>
                                    <td>'.$lang_spelld['comment2'].'</td>
                                    <td><input type="text" name="comment" size="24" maxlength="64" value="" /></td>
                                </tr>
                                <tr>
                                    <td>';
    makebutton($lang_spelld['add_spell'], 'javascript:do_submit()" type="wrn', 130);

    $output .= '
                                    </td>
                                    <td>';
    makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);

    $output .= '
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <fieldset style="width: 440px;">
                        <table class="hidden">
                            <tr>
                                <td>
                                    '.$lang_spelld['dm_exp'].'
                                </td>
                            </tr>
                        </table>
                        <br />
                        <table class="flat" border="2" cellpadding="4" cellspacing="2">
                            <tr>
                                <th>'.$lang_spelld['value'].'</th>
                                <th>'.$lang_spelld['type'].'</th>
                            </tr>
                            <tr>
                                <td align="center">0</td>
                                <td>'.$lang_spelld['enabled'].'</td>
                            </tr>
                            <tr>
                                <td align="center">1</td>
                                <td>'.$lang_spelld['disabled_p'].'</td>
                            </tr>
                            <tr>
                                <td align="center">2</td>
                                <td>'.$lang_spelld['disabled_crea_npc'].'</td>
                            </tr>
                            <tr>
                                <td align="center">4</td>
                                <td>'.$lang_spelld['disabled_pets'].'</td>
                            </tr>
                        </table>
                        <table class="hidden">
                            <tr>
                                <td>
                                <br />
                                    '.$lang_spelld['combinations_hint'].'
                                </td>
                            </tr>
                        </table>
                        <table class="flat" border="2" cellpadding="4" cellspacing="2">
                            <tr>
                                <th>'.$lang_spelld['value'].'</th>
                                <th>'.$lang_spelld['type'].'</th>
                            </tr>
                            <tr>
                                <td align="center">3</td>
                                <td>'.$lang_spelld['disabled_p_crea_npc'].'</td>
                            </tr>
                            <tr>
                                <td align="center">5</td>
                                <td>'.$lang_spelld['disabled_p_pets'].'</td>
                            </tr>
                            <tr>
                                <td align="center">6</td>
                                <td>'.$lang_spelld['disabled_crea_npc_pets'].'</td>
                            </tr>
                            <tr>
                                <td align="center">7</td>
                                <td>'.$lang_spelld['disabled_p_crea_npc_pets'].'</td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                </center>';
}

//#########################################################################################################
// DO ADD NEW SPELL
//#########################################################################################################
function doadd_new()
{
    global $world_db, $realm_id, $action_permission;
    valid_login($action_permission['insert']);

    if ( empty($_GET['entry']) && empty($_GET['flags']) && empty($_GET['comment']) )
        redirect('spelld.php?error=1');

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $entry = $sqlw->quote_smart($_GET['entry']);
    if (is_numeric($entry));
    else
        redirect('spelld.php?error=6');

    $flags = $sqlw->quote_smart($_GET['flags']);

    if (is_numeric($flags));
    else
        redirect('spelld.php?error=6');

    $comment = $sqlw->quote_smart($_GET['comment']);

    $sqlw->query('INSERT INTO spell_disabled (sourceType, entry, flags, comment) VALUES (0, \''.$entry.'\', \''.$flags.'\', \''.$comment.'\')');

    if ($sqlw->affected_rows())
        redirect('spelld.php?error=8');
    else
        redirect('spelld.php?error=7');
}


//#####################################################################################################
//  DELETE SPELL
//#####################################################################################################
function del_spell()
{
    global $world_db, $realm_id, $action_permission;
    valid_login($action_permission['delete']);

    if(isset($_GET['check']));
    else
        redirect("spelld.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $check = $sqlw->quote_smart($_GET['check']);

    $n_check=count($check);

    for ($i=0; $i<$n_check; ++$i)
        if ($check[$i] == '' );
        else
            $sqlw->query('DELETE FROM spell_disabled WHERE entry = '.$check[$i].'');

    unset($n_check);
    unset($check);

    if ($sqlw->affected_rows())
        redirect('spelld.php?error=4');
    else
        redirect('spelld.php?error=5');
}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

$lang_spelld = lang_spelld();

if (1 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['empty_fields'].'</font>
            </h1>';
elseif (2 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['err_no_search_passed'].'</font>
            </h1>';
elseif (3 == $err)
    $output .= '
            <h1>'.$lang_spelld['search_results'].'</h1>';
elseif (4 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_spelld['spell_deleted'].'</font>
            </h1>';
elseif (5 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_spelld['spell_not_deleted'].'</font>
            </h1>';
elseif (6 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_spelld['wrong_fields'].'</font>
            </h1>';
elseif (7 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_spelld['err_add_entry'].'</font>
            </h1>';
elseif (8 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_spelld['spell_added'].'</font>
            </h1>';
else
    $output .= '
            <h1>'.$lang_spelld['spells'].'</h1>';

unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('add_new' === $action)
    add_new();
elseif ('doadd_new' === $action)
    doadd_new();
elseif ('del_spell' === $action)
    del_spell();
else
    browse_spells();

unset($action);
unset($action_permission);
unset($lang_spelld);

require_once 'footer.php';

?>
