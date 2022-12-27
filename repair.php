<?php

require_once 'header.php';
valid_login($action_permission['read']);

//##############################################################################################
// PRINT REPAIR/OPTIMIZE FORM
//##############################################################################################
function repair_form()
{
    global $output, $lang_global, $lang_repair, $realm_db, $world_db, $characters_db, $mmfpm_db, $action_permission, $user_lvl;

    $output .= '
                <center>
                    <fieldset class="tquarter_frame">
                        <legend>'.$lang_repair['repair_optimize'].'</legend>
                        <form action="repair.php?action=do_repair" method="post" name="form">';
    if($user_lvl >= $action_permission['update'])
    {
        $output .= '
                            <table class="hidden">
                                <tr>
                                    <td>
                                        <select name="repair_action">
                                            <option value="REPAIR">'.$lang_repair['repair'].'</option>
                                            <option value="OPTIMIZE">'.$lang_repair['optimize'].'</option>
                                        </select>
                                    </td>
                                    <td>';

        makebutton($lang_repair['start'], 'javascript:do_submit()" type="wrn', 130);

        $output .= '
                                    </td>
                                    <td>';

        makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);

        $output .= '
                                    </td>
                                </tr>
                            </table>
                            <p>'.$lang_repair['select_tables'].'</p>';
    }
    $output .= '
                            <script type="text/javascript" src="libs/js/check.js"></script>
                            <table style="width: 550px;" class="lined">
                                <tr>';
    if($user_lvl >= $action_permission['update'])
        $output .= '
                                    <th width="5%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.form);" /></th>';
    $output .= '
                                    <th width="25%">'.$lang_repair['table_name'].'</th>
                                    <th width="35%">'.$lang_repair['status'].'</th>
                                    <th width="15%">'.$lang_repair['num_records'].'</th>
                                </tr>
                            </table>';
    $sql = new SQL;
    $mm_dbs= [$realm_db, $mmfpm_db];

    foreach ($mm_dbs as $db)
    {
        $output.= '
                            <table style="width: 550px;" class="lined">
                                <tr class="large_bold">
                                    <td colspan="3" class="hidden" align="left">
                                        <div id="div'.$db['name'].'" onclick="expand(\''.$db['name'].'\', this, \''.$db['name'].' '.$lang_repair['tables'].' :\');">[+] '.$db['name'].' '.$lang_repair['tables'].' :</div>
                                    </td>
                                </tr>
                            </table>
                            <table id="'.$db['name'].'" style="width: 550px; display: none;" class="lined">';
        $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
        $result = $sql->query('SHOW TABLES FROM '.$db['name'].'');

        while ($table = $sql->fetch_row($result))
        {
            $result1 = $sql->query('SELECT count(*) FROM '.$table[0].'');
            $result2 = $sql->query('CHECK TABLE '.$table[0].' CHANGED');
            $output .= '
                                <tr>';
            if($user_lvl >= $action_permission['update'])
                $output .= '
                                    <td>
                                        <input type="checkbox" name="check[]" value="db~0~'.$db['name'].'~'.$table[0].'" onclick="CheckCheckAll(document.form);" />
                                    </td>';
            $output .= '
                                    <td>'.$table[0].'</td>
                                    <td>'.$sql->result($result2, 0, 'Msg_type').' : '.$sql->result($result2, 0, 'Msg_text').'</td>
                                    <td>'.$sql->result($result1, 0).'</td>
                                </tr>';
        }
        $output .= '
                            </table>';
    }
    $mm_dbs= [$world_db, $characters_db];

    foreach ($mm_dbs as $dbs)
    {
        foreach ($dbs as $dbr => $db)
        {
            $output .= '
                            <table style="width: 550px;" class="lined">
                                <tr class="large_bold">
                                    <td colspan="3" class="hidden" align="left">
                                        <div id="div'.$db['name'].$dbr.'" onclick="expand(\''.$db['name'].$dbr.'\', this, \''.$db['name'].' Realm '.$dbr.' Tables :\');">[+] '.$db['name'].' Realm '.$dbr.' Tables :</div>
                                    </td>
                                </tr>
                            </table>
                            <table id="'.$db['name'].$dbr.'" style="width: 550px; display: none;" class="lined">';
            $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
            $result = $sql->query('SHOW TABLES FROM '.$db['name'].'');

            while ($table = $sql->fetch_row($result))
            {
                $result1 = $sql->query('SELECT count(*) FROM '.$table[0].'');
                $result2 = $sql->query('CHECK TABLE '.$table[0].' CHANGED');
                $output .= '
                                <tr>';
                    if($user_lvl >= $action_permission['update'])
                        $output .= '
                                    <td>
                                        <input type="checkbox" name="check[]" value="db~'.$dbr.'~'.$db['name'].'~'.$table[0].'" onclick="CheckCheckAll(document.form);" />
                                    </td>';
                    $output .= '
                                    <td>'.$table[0].'</td>
                                    <td>'.$sql->result($result2, 0, 'Msg_type').' : '.$sql->result($result2, 0, 'Msg_text').'</td>
                                    <td>'.$sql->result($result1, 0).'</td>
                                </tr>';
            }
            $output .= '
                            </table>';
        }
    }
    unset($dbs);
    unset($db);
    unset($result);
    unset($result2);
    unset($result1);
    unset($table);
    unset($mm_dbs);

    $output .= '
                        </form>
                    </fieldset>
                    <br /><br />
                </center>';
}
//##############################################################################################
// EXECUTE TABLE REPAIR OR OPTIMIZATION
//##############################################################################################
function do_repair()
{
    global $output, $realm_db, $mmfpm_db, $world_db, $characters_db, $action_permission;
    valid_login($action_permission['update']);

    if ((empty($_POST['repair_action']) && '' === $_POST['repair_action']) || (empty($_POST['check'])) )
        redirect('repair.php?error=1');
    else
    {
        $table_list = $_POST['check'];
        $table_action = addslashes($_POST['repair_action']);
    }

    $sql = new SQL;
    $counter = 0;

    foreach($table_list as $table)
    {
        $table_data = explode('~', $table);
        if ($table_data[2] == $realm_db['name'])
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
        elseif ($table_data[2] == $mmfpm_db['name'])
            $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        elseif ($table_data[2] == $world_db[$table_data[1]]['name'])
            $sql->connect($world_db[$table_data[1]]['addr'], $world_db[$table_data[1]]['user'], $world_db[$table_data[1]]['pass']);
        elseif ($table_data[2] == $characters_db[$table_data[1]]['name'])
            $sql->connect($characters_db[$table_data[1]]['addr'], $characters_db[$table_data[1]]['user'], $characters_db[$table_data[1]]['pass']);
        $action_result = $sql->fetch_row($sql->query(''.$table_action.' TABLE '.$table_data[2].'.'.$table_data[3].''));

        if ($action_result[3] === 'OK')
            ++$counter;
        else
            $err = $action_result[3];
    }
    unset($action_result);
    unset($table_data);
    unset($table);
    unset($table_action);
    unset($table_list);

    if ($counter)
        redirect('repair.php?error=2&num='.$counter.'');
    else
        redirect('repair.php?error=4&rep_err='.$err.'');
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;
$num = (isset($_GET['num'])) ? $_GET['num'] : NULL;
$rep_err = (isset($_GET['rep_err'])) ? $_GET['rep_err'] : NULL;

$output .= '
        <div class="top">';

$lang_repair = lang_repair();

if (1 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['empty_fields'].'</font>
            </h1>';
elseif (2 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_repair['repair_finished'].' : '.$num.' '.$lang_repair['tables'].'</font>
            </h1>';
elseif (3 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_repair['no_table_selected'].'</font>
            </h1>';
elseif (4 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_repair['repair_error'].' : '.$rep_err.'</font>
            </h1>';
else
    $output .= '
            <h1>'.$lang_repair['repair_optimize'].'</h1>';

$output .= '
        </div>';

unset($err);
unset($num);
unset($rep_err);

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('repair_form' == $action)
    repair_form();
elseif ('do_repair' == $action)
    do_repair();
else
    repair_form();

unset($action);
unset($action_permission);
unset($lang_repair);

include_once 'footer.php';

?>
