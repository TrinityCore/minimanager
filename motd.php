<?php

require_once 'header.php';
require_once 'libs/bbcode_lib.php';
valid_login($action_permission['insert']);

//#############################################################################
// ADD MOTD
//#############################################################################
function add_motd(&$sqlm)
{
    global $output, $lang_motd, $lang_global, $action_permission;
    valid_login($action_permission['insert']);

    $output .= '
                <center>
                    <form action="motd.php?action=do_add_motd" method="post" name="form">
                        <table class="top_hidden">
                            <tr>
                                <td colspan="3">';
    bbcode_add_editor();
    $output .= '
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <textarea id="msg" name="msg" rows="26" cols="97"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>'.$lang_motd['post_rules'].'</td>
                                <td>';
    makebutton($lang_motd['post_motd'], 'javascript:do_submit()" type="wrn', 230);
    $output .= '
                                </td>
                                <td>';
    makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
    $output .= '
                                </td>
                            </tr>
                        </table>
                    </form>
                <br />
                </center>';
}
//#############################################################################
// EDIT MOTD
//#############################################################################
function edit_motd(&$sqlm)
{
    global $output, $lang_motd, $lang_global, $mmfpm_db, $action_permission;
    valid_login($action_permission['update']);

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    if(empty($_GET['id']))
        redirect('motd.php?error=1');
    $id = $sqlm->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect('motd.php?error=1');

    $msg = $sqlm->result($sqlm->query('SELECT content FROM mm_motd WHERE id = '.$id.''), 0);

    $output .= '
                <center>
                    <form action="motd.php?action=do_edit_motd" method="post" name="form">
                        <input type="hidden" name="id" value="'.$id.'" />
                        <table class="top_hidden">
                            <tr>
                                <td colspan="3">';
    unset($id);
    bbcode_add_editor();
    $output .= '
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <textarea id="msg" name="msg" rows="26" cols="97">'.$msg.'</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>'.$lang_motd['post_rules'].'</td>
                                <td>';
    unset($msg);
    makebutton($lang_motd['post_motd'], 'javascript:do_submit()" type="wrn', 230);
    $output .= '
                                </td>
                                <td>';
    makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
    $output .= '
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br />
                </center>';
}
//#####################################################################################################
// DO ADD MOTD
//#####################################################################################################
function do_add_motd(&$sqlm)
{
    global $action_permission, $user_name, $realm_id, $mmfpm_db;
    valid_login($action_permission['insert']);

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    if (empty($_POST['msg']))
        redirect('motd.php?error=1');
    $msg = $sqlm->quote_smart($_POST['msg']);
    if (4096 < strlen($msg))
        redirect('motd.php?error=2');

    $posted_by = date('m/d/y H:i:s').' Posted by: '.$user_name;

    $sqlm->query('INSERT INTO mm_motd (realmid, type, content) VALUES (\''.$realm_id.'\', \''.$posted_by.'\', \''.$msg.'\')');
    unset($posted_by);
    unset($msg);
    redirect('index.php');
}
//#####################################################################################################
// DO EDIT MOTD
//#####################################################################################################
function do_edit_motd(&$sqlm)
{
    global $action_permission, $user_name, $realm_id, $mmfpm_db;
    valid_login($action_permission['update']);

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    if (empty($_POST['msg']) || empty($_POST['id']))
        redirect('motd.php?error=1');
    $id = $sqlm->quote_smart($_POST['id']);
    if(is_numeric($id));
    else
        redirect('motd.php?error=1');

    $msg = $sqlm->quote_smart($_POST['msg']);
    if (4096 < strlen($msg))
        redirect('motd.php?error=2');

    $by = $sqlm->result($sqlm->query('SELECT type FROM mm_motd WHERE id = '.$id.''), 0);
    $by = split('<br />', $by, 2);
    $by = $by[0].'<br />'.date('m/d/y H:i:s').' Edited by: '.$user_name;

    $sqlm->query('UPDATE mm_motd SET realmid = \''.$realm_id.'\', type = \''.$by.'\', content = \''.$msg.'\' WHERE id = '.$id.'');
    unset($by);
    unset($msg);
    unset($id);
    redirect('index.php');
}
//#####################################################################################################
// DELETE MOTD
//#####################################################################################################
function delete_motd(&$sqlm)
{
    global $lang_global;
    global $action_permission, $mmfpm_db;
    valid_login($action_permission['delete']);

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    if (empty($_GET['id']))
        redirect('index.php');
    $id = $sqlm->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect('motd.php?error=1');

    $sqlm->query('DELETE FROM mm_motd WHERE id ='.$id.'');
    unset($id);
    redirect('index.php');
}
//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_motd = lang_motd();

$output .= '
        <div class="top">';

if (1 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['empty_fields'].'</font>
            </h1>';
elseif (2 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_motd['err_max_len'].'</font>
            </h1>';
elseif (3 == $err)
    $output .= '
            <h1>'.$lang_motd['edit_motd'].'</h1>';
else
    $output .= '
            <h1>'.$lang_motd['add_motd'].'</h1>';

unset($err);

$output .= '</div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('delete_motd' == $action)
    delete_motd($sqlm);
elseif ('add_motd' == $action)
    add_motd($sqlm);
elseif ('do_add_motd' == $action)
    do_add_motd($sqlm);
elseif ('edit_motd' == $action)
    edit_motd($sqlm);
elseif ('do_edit_motd' == $action)
    do_edit_motd($sqlm);
else
    add_motd($sqlm);

unset($action);
unset($action_permission);
unset($lang_motd);

require_once 'footer.php';

?>
