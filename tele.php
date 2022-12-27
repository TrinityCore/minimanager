<?php


require_once("header.php");
require_once("libs/map_zone_lib.php");
valid_login($action_permission['read']);

//#############################################################################
// BROWSE TELEPORT LOCATIONS
//#############################################################################
function browse_tele()
{
    global $lang_tele, $lang_global, $output, $world_db, $realm_id, $mmfpm_db, $itemperpage,
            $action_permission, $user_lvl;

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : "id";
    if (!preg_match("/^[_[:lower:]]{1,12}$/", $order_by))
        $order_by = "id";

    $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
    if (!preg_match("/^[01]{1}$/", $dir))
        $dir = 1;

    $order_dir = ($dir) ? "ASC" : "DESC";
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    //==========================Browse/Search CHECK==============================
    $search_by = '';
    $search_value = '';
    if(isset($_GET['search_value']) && isset($_GET['search_by']))
    {
        $search_value = $sqlw->quote_smart($_GET['search_value']);
        $search_by = $sqlw->quote_smart($_GET['search_by']);
        $search_menu = ["name", "id", "map"];

        if (!in_array($search_by, $search_menu))
            $search_by = 'name';
        unset($search_menu);

        if (preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value))
            redirect("tele.php?error=1");

        $query_1 = $sqlw->query("SELECT count(*) FROM game_tele WHERE $search_by LIKE '%$search_value%'");
        $query = $sqlw->query("SELECT id, name, map, position_x, position_y, position_z, orientation
                               FROM game_tele WHERE $search_by LIKE '%$search_value%' ORDER BY $order_by $order_dir LIMIT  $start, $itemperpage");
    }
    else
    {
        $query_1 = $sqlw->query("SELECT count(*) FROM game_tele");
        $query = $sqlw->query("SELECT id, name, map, position_x, position_y, position_z, orientation
                                FROM game_tele ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
    }

    $all_record = $sqlw->result($query_1,0);
    unset($query_1);

    //=====================top tage navigaion starts here========================
    $output .="
                <center>
                    <table class=\"top_hidden\">
                        <tr>
                            <td>";
    if($user_lvl >= $action_permission['insert'])
        makebutton($lang_tele['add_new'], "tele.php?action=add_tele",130);

    makebutton($lang_global['back'], "javascript:window.history.back()", 130);
    ($search_by && $search_value) ? makebutton($lang_tele['teleports'], "tele.php\" type=\"def", 130) : $output .= "";

    $output .= "
                            </td>
                            <td width=\"25%\" align=\"right\" rowspan=\"2\">";
    $output .= generate_pagination("tele.php?order_by=$order_by&amp;dir=".(($dir) ? 0 : 1).( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" ), $all_record, $itemperpage, $start);
    $output .= "
                            </td>
                        </tr>
                        <tr align=\"left\">
                            <td>
                                <table class=\"hidden\">
                                    <tr>
                                        <td>
                                            <form action=\"tele.php\" method=\"get\" name=\"form\">
                                                <input type=\"hidden\" name=\"error\" value=\"4\" />
                                                <input type=\"text\" size=\"24\" name=\"search_value\" value=\"$search_value\" placeholder='{$lang_global['search_by']}' />
                                                <select name=\"search_by\" title='{$lang_global['search_by']}'>
                                                    <option value=\"name\"".($search_by == 'name' ? " selected=\"selected\"" : "").">{$lang_tele['loc_name']}</option>
                                                    <option value=\"id\"".($search_by == 'id' ? " selected=\"selected\"" : "").">{$lang_tele['loc_id']}</option>
                                                    <option value=\"map\"".($search_by == 'map' ? " selected=\"selected\"" : "").">{$lang_tele['on_map']}</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>";

    makebutton($lang_global['search'], "javascript:do_submit()",80);

    $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>";
    //======================top tage navigaion ENDS here ========================

    $output .= "
                    <script type=\"text/javascript\">
                        answerbox.btn_ok='{$lang_global['yes']}';
                        answerbox.btn_cancel='{$lang_global['no']}';
                        var question = '{$lang_global['are_you_sure']}';
                        var del_tele = 'tele.php?action=del_tele&amp;order_by=$order_by&amp;start=$start&amp;dir=$dir&amp;id=';
                    </script>
                    <table class=\"lined\">
                        <tr>";

    if($user_lvl >= $action_permission['delete'])
        $output .= "
                            <th width=\"5%\">{$lang_global['delete_short']}</th>";
    $output .= "
                            <th width=\"5%\"><a href=\"tele.php?order_by=id&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='id' ? " class=\"$order_dir\"" : "").">{$lang_tele['id']}</a></th>
                            <th width=\"28%\"><a href=\"tele.php?order_by=name&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='name' ? " class=\"$order_dir\"" : "").">{$lang_tele['name']}</a></th>
                            <th width=\"22%\"><a href=\"tele.php?order_by=map&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='map' ? " class=\"$order_dir\"" : "").">{$lang_tele['map']}</a></th>
                            <th width=\"9%\"><a href=\"tele.php?order_by=position_x&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='position_x' ? " class=\"$order_dir\"" : "").">{$lang_tele['x']}</a></th>
                            <th width=\"9%\"><a href=\"tele.php?order_by=position_y&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='position_y' ? " class=\"$order_dir\"" : "").">{$lang_tele['y']}</a></th>
                            <th width=\"9%\"><a href=\"tele.php?order_by=position_z&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='position_z' ? " class=\"$order_dir\"" : "").">{$lang_tele['z']}</a></th>
                            <th width=\"10%\"><a href=\"tele.php?order_by=orientation&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='orientation' ? " class=\"$order_dir\"" : "").">{$lang_tele['orientation']}</a></th>
                        </tr>";

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    while ($data = $sqlw->fetch_row($query))
    {
        $output .= "
                        <tr>";
        if($user_lvl >= $action_permission['delete'])
            $output .= "
                            <td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: &lt;font color=white&gt;{$data[1]}&lt;/font&gt;&lt;br /&gt; ' + question, del_tele + $data[0]);\" style=\"cursor:pointer;\" /></td>";
        $output .= "
                            <td>$data[0]</td>
                            <td>";
        if($user_lvl >= $action_permission['update'])
            $output .="
                                <a href=\"tele.php?action=edit_tele&amp;id=$data[0]\">$data[1]</a>";
        else
            $output .="$data[1]";

        $output .="
                            </td>
                            <td>".get_map_name($data[2], $sqlm)." ($data[2])</td>
                            <td>$data[3]</td>
                            <td>$data[4]</td>
                            <td>$data[5]</td>
                            <td>$data[6]</td>
                        </tr>";
    }
    unset($query);
    unset($data);

    $output .= "
                        <tr>
                            <td  colspan=\"7\" class=\"hidden\" align=\"right\" width=\"25%\">";

    $output .= generate_pagination("tele.php?order_by=$order_by&amp;dir=".(($dir) ? 0 : 1).( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" ), $all_record, $itemperpage, $start);

    $output .= "
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"7\" class=\"hidden\" align=\"right\">{$lang_tele['tot_locations']} : $all_record</td>
                        </tr>
                    </table>
                </center>";
}


//#############################################################################
// DO DELETE TELE FROM LIST
//#############################################################################
function del_tele()
{
    global $world_db, $realm_id, $action_permission;
    valid_login($action_permission['delete']);

    if(!isset($_GET['id']))
        redirect("Location: tele.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $id = $sqlw->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect("tele.php?error=1");

    //==========================$_GET and SECURE=================================
    $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : "id";
    if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by))
        $order_by="id";

    $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
    if (!preg_match("/^[01]{1}$/", $dir))
        $dir=1;

    $order_dir = ($dir) ? "ASC" : "DESC";
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $sqlw->query("DELETE FROM game_tele WHERE id = '$id'");
    if ($sqlw->affected_rows() != 0)
        redirect("tele.php?error=3&order_by=$order_by&start=$start&dir=$dir");
    else
        redirect("tele.php?error=5");
}


//#############################################################################
//  EDIT   TELE
//#############################################################################
function edit_tele()
{
    global  $lang_tele, $lang_global, $output, $world_db, $realm_id, $mmfpm_db, $action_permission, $user_lvl;
    valid_login($action_permission['update']);

    if(!isset($_GET['id']))
        redirect("Location: tele.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    $id = $sqlw->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect("tele.php?error=1");

    $query = $sqlw->query("SELECT id, name, map, position_x, position_y, position_z, orientation FROM game_tele WHERE id = '$id'");

    if ($sqlw->num_rows($query) == 1)
    {
        $tele = $sqlw->fetch_row($query);
        $output .= "
                <script type=\"text/javascript\">
                    answerbox.btn_ok='{$lang_global['yes']}';
                    answerbox.btn_cancel='{$lang_global['no']}';
                </script>
                <center>
                    <fieldset class=\"half_frame\">
                        <legend>{$lang_tele['edit_tele']}</legend>
                        <form method=\"get\" action=\"tele.php\" name=\"form\">
                            <input type=\"hidden\" name=\"action\" value=\"do_edit_tele\" />
                            <input type=\"hidden\" name=\"id\" value=\"$id\" />
                            <table class=\"flat\">
                                <tr>
                                    <td>{$lang_tele['loc_id']}</td>
                                    <td>$tele[0]</td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['loc_name']}</td>
                                    <td><input type=\"text\" name=\"new_name\" size=\"42\" maxlength=\"98\" value=\"$tele[1]\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['on_map']}</td>
                                    <td>
                                        <select name=\"new_map\">";

        $map_query = $sqlm->query("SELECT id, name01 from dbc_map order by id");
        while ($map = $sqlm->fetch_row($map_query))
        {
            $output .= "
                                            <option value=\"{$map[0]}\" ";
            if ($tele[2] == $map[0]) $output .= "selected=\"selected\" ";
                $output .= ">{$map[0]} : {$map[1]}</option>";
        }
        unset($map);
        unset($map_query);
        $output .= "
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['position_x']}</td>
                                    <td><input type=\"text\" name=\"new_x\" size=\"42\" maxlength=\"36\" value=\"$tele[3]\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['position_y']}</td>
                                    <td><input type=\"text\" name=\"new_y\" size=\"42\" maxlength=\"36\" value=\"$tele[4]\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['position_z']}</td>
                                    <td><input type=\"text\" name=\"new_z\" size=\"42\" maxlength=\"36\" value=\"$tele[5]\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['orientation']}</td>
                                    <td><input type=\"text\" name=\"new_orientation\" size=\"42\" maxlength=\"36\" value=\"$tele[6]\" /></td>
                                </tr>
                                <tr>
                                    <td>";

        if($user_lvl >= $action_permission['delete'])
            makebutton($lang_tele['delete_tele'], "#\" onclick=\"answerBox('{$lang_global['delete']}: &lt;font color=white&gt;{$tele[1]}&lt;/font&gt; &lt;br /&gt; {$lang_global['are_you_sure']}', 'tele.php?action=del_tele&amp;id=$id');\" type=\"wrn",130);

        $output .= "
                                    </td>
                                    <td>";

        makebutton($lang_tele['update_tele'], "javascript:do_submit()",130);
        makebutton($lang_global['back'], "tele.php\" type=\"def",130);

        $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br /><br />
                </center>";
    }
    else
        error($lang_global['err_no_records_found']);
}


//#############################################################################
//  DO EDIT TELE LOCATION
//#############################################################################
function do_edit_tele()
{
    global $world_db, $realm_id, $action_permission;
    valid_login($action_permission['update']);

    if( empty($_GET['id']) || !isset($_GET['new_name']) || !isset($_GET['new_map']) || !isset($_GET['new_x'])
        || !isset($_GET['new_y'])|| !isset($_GET['new_z'])|| !isset($_GET['new_orientation']))
        redirect("tele.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $id = $sqlw->quote_smart($_GET['id']);
    if(is_numeric($id));
    else
        redirect("tele.php?error=1");

    $new_name = $sqlw->quote_smart($_GET['new_name']);
    $new_map = $sqlw->quote_smart($_GET['new_map']);
    $new_x = $sqlw->quote_smart($_GET['new_x']);
    $new_y = $sqlw->quote_smart($_GET['new_y']);
    $new_z = $sqlw->quote_smart($_GET['new_z']);
    $new_orientation = $sqlw->quote_smart($_GET['new_orientation']);

    $sqlw->query("UPDATE game_tele SET position_x='$new_x', position_y ='$new_y', position_z ='$new_z', orientation ='$new_orientation', map ='$new_map', name ='$new_name' WHERE id = '$id'");

    if ($sqlw->affected_rows())
        redirect("tele.php?error=3");
    else
        redirect("tele.php?error=5");
}


//#############################################################################
//  ADD NEW TELE
//#############################################################################
function add_tele()
{
    global  $output, $lang_tele, $lang_global, $mmfpm_db, $action_permission;
    valid_login($action_permission['insert']);

    $sqlw = new SQL;
    $output .= "
                <center>
                    <fieldset class=\"half_frame\">
                        <legend>{$lang_tele['add_new_tele']}</legend>
                        <form method=\"get\" action=\"tele.php\" name=\"form\">
                            <input type=\"hidden\" name=\"action\" value=\"do_add_tele\" />
                            <table class=\"flat\">
                                <tr>
                                    <td>{$lang_tele['loc_name']}</td>
                                    <td><input type=\"text\" name=\"name\" size=\"42\" maxlength=\"98\" value=\"{$lang_tele['name']}\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['on_map']}</td>
                                    <td>
                                        <select name=\"map\">";

    $sqlw->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
    $map_query = $sqlw->query("SELECT id, name01 from dbc_map order by id");
    while ($map = $sqlw->fetch_row($map_query))
        $output .= "
                                            <option value=\"{$map[0]}\">{$map[0]} : {$map[1]}</option>";
    unset($map);
    unset($map_query);

    $output .= "
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['position_x']}</td>
                                    <td><input type=\"text\" name=\"x\" size=\"42\" maxlength=\"36\" value=\"0.0000\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['position_y']}</td>
                                    <td><input type=\"text\" name=\"y\" size=\"42\" maxlength=\"36\" value=\"0.0000\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['position_z']}</td>
                                    <td><input type=\"text\" name=\"z\" size=\"42\" maxlength=\"36\" value=\"0.0000\" /></td>
                                </tr>
                                <tr>
                                    <td>{$lang_tele['orientation']}</td>
                                    <td><input type=\"text\" name=\"orientation\" size=\"42\" maxlength=\"36\" value=\"0\" /></td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>";

    makebutton($lang_tele['add_new'], "javascript:do_submit()",130);
    makebutton($lang_global['back'], "tele.php\" type=\"def",130);

    $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br /><br />
                </center>";
}


//#############################################################################
//  DO ADD  TELE LOCATION
//#############################################################################
function do_add_tele()
{
    global $world_db, $realm_id, $action_permission;
    valid_login($action_permission['insert']);

    if( !isset($_GET['name']) || !isset($_GET['map']) || !isset($_GET['x'])
        || !isset($_GET['y'])|| !isset($_GET['z'])|| !isset($_GET['orientation']))
        redirect("tele.php?error=1");

    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $name = $sqlw->quote_smart($_GET['name']);
    $map = $sqlw->quote_smart($_GET['map']);
    $x = $sqlw->quote_smart($_GET['x']);
    $y = $sqlw->quote_smart($_GET['y']);
    $z = $sqlw->quote_smart($_GET['z']);
    $orientation = $sqlw->quote_smart($_GET['orientation']);

    $sqlw->query("INSERT INTO game_tele VALUES (NULL,'$x','$y', '$z' ,'$orientation' ,'$map' ,'$name')");

    if ($sqlw->affected_rows())
        redirect("tele.php?error=3");
    else
        redirect("tele.php?error=5");
}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

$lang_tele = lang_tele();

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
        $output .= "
            <h1>
                <font class=\"error\">{$lang_tele['tele_updated']}</font>
            </h1>";
        break;
    case 4:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_tele['search_results']}</font>
            </h1>";
        break;
    case 5:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_tele['error_updating']}</font>
            </h1>";
        break;
    default: //no error
        $output .= "
            <h1>{$lang_tele['tele_locations']}</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "edit_tele":
        edit_tele();
        break;
    case "do_edit_tele":
        do_edit_tele();
        break;
    case "add_tele":
        add_tele();
        break;
    case "do_add_tele":
        do_add_tele();
        break;
    case "del_tele":
        del_tele();
        break;
    default:
        browse_tele();
}

unset($action);
unset($action_permission);
unset($lang_tele);

require_once("footer.php");

?>
