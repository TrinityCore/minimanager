<?php

// page header, and any additional required libraries
include 'header.php';
// minimum permission to view page
valid_login($action_permission['read']);
//#############################################################################
// PRINT COMMAND FORM
//#############################################################################
function print_commands_form()
{
    global $output, $lang_command, $realm_id, $world_db, $action_permission, $user_lvl, $gm_level_arr;

    $levels = $gm_level_arr;
    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
    $query = $sqlw->query('SELECT name, help, security FROM command WHERE security <= '.$user_lvl.'');

    while ($data = $sqlw->fetch_assoc($query))
    {
        $comm = explode("\r\n", $data['help'], 2);
        $levels[$data['security']][3] .= '
                    <tr>
                        '.(($user_lvl >= $action_permission['update']) ? '<td><input type="checkbox" name="check['.$data['name'].']" value="'.$data['security'].'" /></td>' : '<td></td>').'
                        <td align="left">'.$data['name'].'</td>
                        <td>'.htmlentities(preg_replace("/[a-zA-Z ]+:* *\./", ".", $comm[0])).'</td>
                        <td>'.(isset($comm[1]) ? str_replace("\r\n", "<br />", str_replace("\r\n\r\n", "<br />", htmlentities($comm[1]))): '').'</td>
                    </tr>';
    }
    unset($comm);
    unset($data);
    unset($query);

    $output .= '
                    <center>
                        <form method="get" action="command.php" name="form">
                            <input type="hidden" name="action" value="update" />';
    for ($i=0; $i<=$user_lvl; ++$i)
    {
        $output .= '
                            <table style="width: 720px; text-align: left;" class="lined">
                                <tr>
                                    <th>
                                        <div id="div'.$levels[$i][1].'" onclick="expand(\''.$levels[$i][1].'\', this, \''.$levels[$i][1].'\');">[+] '.$levels[$i][1].' :</div>
                                    </th>
                                </tr>
                            </table>
                            <table id="'.$levels[$i][1].'" class="lined" style="width: 720px; text-align: left; display: none">
                                <tr style="text-align: center;">
                                    <th width="2%"></th>
                                    <th width="13%">'.$lang_command['command'].'</th>
                                    <th width="20%">'.$lang_command['syntax'].'</th>
                                    <th width="65%">'.$lang_command['description'].'</th>
                                </tr>'.$levels[$i][3];

        if($user_lvl >= $action_permission['update'])
        {
            $output .= '
                            </table>
                            <br />
                            <table class="hidden" style="width: 720px;">
                                <tr>
                                    <td>';
            makebutton($lang_command['change_level'], 'javascript:do_submit()',280);
            $output .= '
                                    </td>
                                </tr>';
        }
        $output .= '
                            </table>
                            <br />';
    }
    $output .= '
                        </form>
                    </center>';
}
//#############################################################################
//  UPDATE COMMAND LEVEL
//#############################################################################
function update_commands()
{
    global $output, $lang_global, $lang_command, $action_permission, $user_lvl, $gm_level_arr;
    valid_login($action_permission['update']);

    if(isset($_GET['check']))
        $check = $_GET['check'];
    else
        redirect('command.php?error=1');

    $output .= '
                    <center>
                        <form method="get" action="command.php" name="form">
                            <input type="hidden" name="action" value="doupdate" />
                            <table class="lined" style="width: 700px;">
                                <tr>
                                    <th width="1%"></th>';
    for ($i=0; $i<=$user_lvl; ++$i)
    $output .= '
                                    <th width="1%">'.$gm_level_arr[$i][1].'</th>';

    $output .= '
                                </tr>';

    $commands = array_keys($check);
    $n_commands = count($check);
    for ($i=0; $i<$n_commands; ++$i)
    {
        $output .= '
                                <tr>
                                    <td>'.$commands[$i].'</td>';
        for ($j=0; $j<=$user_lvl; ++$j)
        {
            $output .= '
                                    <td>
                                        <input type="radio" name="change['.$commands[$i].']" value="'.$j.'"';

            if ($j==$check[$commands[$i]])
                $output .= ' checked="checked"';

            $output .= ' />
                                    </td>';
        }
        $output .= '
                                </tr>';
    }
    unset($n_commands);
    unset($commands);
    unset($check);
    $output .= '
                            </table>
                        </form>
                        <table width="300" class="hidden">
                        <tr>
                            <td>';

    makebutton($lang_command['save'], 'javascript:do_submit()" type="wrn', 130);
    makebutton($lang_global['back'], 'command.php" type="def', 130);

    $output .= '
                            </td>
                        </tr>
                    </table>
                </center>';
}
//#############################################################################
//  DO UPDATE COMMAND LEVEL
//#############################################################################
function doupdate_commands()
{
    global $output, $realm_id, $world_db, $action_permission;
    valid_login($action_permission['update']);

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    if(isset($_GET['change']))
        $change = $sqlw->quote_smart($_GET['change']);
    else
        redirect('command.php?error=1');

    $commands = array_keys($change);
    $n_commands = count($change);

    for ($i=0; $i<$n_commands; ++$i)
    {
        $query = $sqlw->query('UPDATE command SET security = '.$change[$commands[$i]].' WHERE name= \''.$commands[$i].'\'');
    }

    unset($n_commands);
    unset($commands);
    unset($change);
    redirect('command.php');
}
//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

$lang_command = lang_command();

if(1 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['empty_fields'].'</font>
            </h1>';
else
    $output .= '
            <h1>'.$lang_command['command_list'].'</h1>';

unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('update' == $action)
    update_commands();
elseif ('doupdate' == $action)
    doupdate_commands();
else
    print_commands_form();

unset($action);
unset($action_permission);
unset($lang_command);

include 'footer.php';

?>
