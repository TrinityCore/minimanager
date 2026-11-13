<?php

// page header, and any additional required libraries
include 'header.php';

// minimum permission to view page
valid_login($action_permission['read']);

function rbac_get_command_map(&$sqlr)
{
    $perm_by_cmd = [];
    $q_perms = $sqlr->query("SELECT id, SUBSTRING(name, 10) AS cmd FROM rbac_permissions WHERE name LIKE 'Command: %'");
    while ($p = $sqlr->fetch_assoc($q_perms))
        $perm_by_cmd[strtolower(trim($p['cmd']))] = intval($p['id']);

    $links = [];
    $q_links = $sqlr->query("SELECT id, linkedId FROM rbac_linked_permissions");
    while ($l = $sqlr->fetch_assoc($q_links))
        $links[intval($l['id'])][] = intval($l['linkedId']);

    $role_roots = [];
    $q_defs = $sqlr->query("SELECT secId, permissionId FROM rbac_default_permissions WHERE realmId = -1 ORDER BY secId ASC");
    while ($d = $sqlr->fetch_assoc($q_defs))
        $role_roots[intval($d['secId'])][] = intval($d['permissionId']);

    $perm_min_sec = [];
    foreach ([0, 1, 2, 3] as $sec)
    {
        if (!isset($role_roots[$sec])) continue;
        $queue = $role_roots[$sec];
        $visited = [];
        while (!empty($queue))
        {
            $curr = array_shift($queue);
            if (isset($visited[$curr])) continue;
            $visited[$curr] = true;
            if (!isset($perm_min_sec[$curr]))
                $perm_min_sec[$curr] = $sec;
            if (isset($links[$curr]))
                foreach ($links[$curr] as $child)
                    if (!isset($visited[$child])) $queue[] = $child;
        }
    }

    return [$perm_by_cmd, $perm_min_sec, $role_roots];
}

//#############################################################################
// PRINT COMMAND FORM
//#############################################################################
function print_commands_form()
{
    global $output, $lang_command, $realm_id, $world_db, $sqlr, $action_permission, $user_lvl, $gm_level_arr;

    $levels = $gm_level_arr;
    for ($k = 0; $k <= 3; ++$k)
        if (!isset($levels[$k][3])) $levels[$k][3] = '';

    list($perm_by_cmd, $perm_min_sec) = rbac_get_command_map($sqlr);

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
    $query = $sqlw->query('SELECT name, help FROM command ORDER BY name ASC');

    while ($data = $sqlw->fetch_assoc($query))
    {
        $cmd_lower = strtolower(trim($data['name']));
        $sec = 3;
        $parts = explode(' ', $cmd_lower);
        while (!empty($parts))
        {
            $try_cmd = implode(' ', $parts);
            if (isset($perm_by_cmd[$try_cmd]))
            {
                $pid = $perm_by_cmd[$try_cmd];
                if (isset($perm_min_sec[$pid]))
                    $sec = $perm_min_sec[$pid];
                break;
            }
            array_pop($parts);
        }

        if ($sec > $user_lvl || !isset($levels[$sec]))
            continue;

        $comm = explode("\r\n", (string)$data['help'], 2);
        $levels[$sec][3] .= '
                    <tr>
                        '.(($user_lvl >= $action_permission['update']) ? '<td><input type="checkbox" name="check['.$data['name'].']" value="'.$sec.'" /></td>' : '<td></td>').'
                        <td align="left">'.$data['name'].'</td>
                        <td>'.htmlentities(preg_replace("/[a-zA-Z ]+:* *\./", ".", $comm[0])).'</td>
                        <td>'.(isset($comm[1]) ? str_replace("\r\n", "<br />", str_replace("\r\n\r\n", "<br />", htmlentities($comm[1]))): '').'</td>
                    </tr>';
    }
    unset($comm, $data, $query);

    $output .= '
                    <center>
                        <form method="get" action="command.php" name="form">
                            <input type="hidden" name="action" value="update" />';
    for ($i=0; $i<=$user_lvl; ++$i)
    {
        if (!isset($levels[$i])) continue;
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
        if (isset($gm_level_arr[$i]))
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
            if (!isset($gm_level_arr[$j])) continue;
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
    unset($n_commands, $commands, $check);
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
//  DO UPDATE COMMAND LEVEL (RBAC)
//#############################################################################
function doupdate_commands()
{
    global $lang_global;
    global $sqlr, $action_permission;
    valid_login($action_permission['update']);

    if(!isset($_GET['change']) || !is_array($_GET['change']))
        redirect('command.php?error=1');

    list($perm_by_cmd, , $role_roots) = rbac_get_command_map($sqlr);
    $default_group_ids = [0 => 195, 1 => 194, 2 => 193, 3 => 192];

    foreach ($_GET['change'] as $cmd_name => $new_sec_raw)
    {
        $new_sec = intval($new_sec_raw);
        $cmd_lower = strtolower(trim((string)$cmd_name));
        if (!isset($perm_by_cmd[$cmd_lower]) || !isset($default_group_ids[$new_sec]))
            continue;

        $pid = intval($perm_by_cmd[$cmd_lower]);
        $all_roots = implode(',', $default_group_ids);
        $sqlr->query("DELETE FROM rbac_linked_permissions WHERE id IN ($all_roots) AND linkedId = $pid");

        $target_root = isset($role_roots[$new_sec][0]) ? intval($role_roots[$new_sec][0]) : $default_group_ids[$new_sec];
        $sqlr->query("INSERT IGNORE INTO rbac_linked_permissions (id, linkedId) VALUES ($target_root, $pid)");
    }

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

unset($action, $action_permission, $lang_command);

include 'footer.php';

?>
