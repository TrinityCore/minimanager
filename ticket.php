<?php


require_once 'header.php';
valid_login($action_permission['read']);

class tickets
{
//#############################################################################
//  BROWSE  TICKETS
//#############################################################################
	function browse_tickets(&$sqlc)
	{
		global $output, $lang_global, $lang_ticket, $action_permission, $user_lvl, $itemperpage;

		//==========================$_GET and SECURE=================================
		$start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
		$start = (is_numeric($start)) ? $start : 0;
	
        $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'id';
        $order_by = (preg_match('/^[_[:lower:]]{1,10}$/', $order_by)) ? $order_by : 'id';
			
        $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
		$dir = (preg_match('/^[01]{1}$/', $dir)) ? $dir : 1;

		$order_dir = ($dir) ? 'ASC' : 'DESC';
		$dir = ($dir) ? 0 : 1;
		//==========================$_GET and SECURE end=============================

		//get total number of items
		$query_1 = $sqlc->query('SELECT count(*) FROM gm_ticket');
		$all_record = $sqlc->result($query_1,0);
		unset($query_1);

		$query = $sqlc->query("SELECT gm_ticket.id, gm_ticket.playerGuid, gm_ticket.description, BINARY characters.name AS name, characters.online
								FROM gm_ticket,characters
									WHERE gm_ticket.playerGuid = characters.guid
									  ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");

		$output .="
					<script type=\"text/javascript\" src=\"libs/js/check.js\"></script>
					<center>
						<table class=\"top_hidden\">
							<tr>
								<td width=\"25%\" align=\"right\">";
		$output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
		$output .= "
								</td>
							</tr>
						</table>";
		$output .= "
					<form method=\"get\" action=\"ticket.php\" name=\"form\">
						<input type=\"hidden\" name=\"action\" value=\"delete_tickets\" />
						<input type=\"hidden\" name=\"start\" value=\"$start\" />
						<table class=\"lined\">
							<tr>";
		if($user_lvl >= $action_permission['delete'])
			$output .="
								<th width=\"7%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form);\" title='Check all' /></th>";
		if($user_lvl >= $action_permission['update'])
			$output .="
								<th width=\"7%\">{$lang_global['edit']}</th>";
		$output .="
								<th width=\"10%\"><a href=\"ticket.php?order_by=id&amp;start=$start&amp;dir=$dir\">".($order_by=='id' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_ticket['id']}</a></th>
								<th width=\"16%\"><a href=\"ticket.php?order_by=online&amp;start=$start&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."Online?</a></th>
								<th width=\"16%\"><a href=\"ticket.php?order_by=playerGuid&amp;start=$start&amp;dir=$dir\">".($order_by=='playerGuid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_ticket['sender']}</a></th>";
		$output .="
								<th width=\"60%\">{$lang_ticket['ticket_text']}</th>
							</tr>";
		while ($ticket = $sqlc->fetch_row($query))
		{
			$output .= "
							<tr>";
			if($user_lvl >= $action_permission['delete'])
			$output .="
								<td><input type=\"checkbox\" name=\"check[]\" value=\"$ticket[0]\" onclick=\"CheckCheckAll(document.form);\" title='Check all' /></td>";
			if($user_lvl >= $action_permission['update'])
				$output .="
								<td><a href=\"ticket.php?action=edit_ticket&amp;id=$ticket[0]\">{$lang_global['edit']}</a></td>";
			$output .="
								<td>$ticket[0]</td>
								<td>".($ticket[4] ? "<img src=\"img/up.gif\" alt=\"online\">" : "<img src=\"img/down.gif\" alt=\"offline\">")."</td>
								<td><a href=\"char.php?id=$ticket[1]\">".htmlentities($ticket[3])."</a></td>
								<td>".htmlentities($ticket[2])."</td>
							</tr>";
		}
		unset($query);
		unset($ticket);

		$output .= "
							<tr>
								<td colspan=\"5\" align=\"right\" class=\"hidden\" width=\"25%\">";
		$output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
		$output .= "
								</td>
							</tr>
							<tr>
								<td colspan=\"3\" align=\"left\" class=\"hidden\">";
		if($user_lvl >= $action_permission['delete'])
			makebutton($lang_ticket['del_selected_tickets'], "javascript:do_submit()\" type=\"wrn",230);
		$output .= "
								</td>
								<td colspan=\"2\" align=\"right\" class=\"hidden\">{$lang_ticket['tot_tickets']}: $all_record</td>
							</tr>
						</table>
					</form>
				</center>
				<ul>
				<li>'Delete Checked Ticket(s)' option needs .reload gm_ticket to apply changes ingame.</li>
				<li>After reloading the table, If the deleted ticket was a open one and the sender was online, he/she will still see his/her ticket as pending and will cannot edit it or open a new ticket till reloging.</li>
				</ul>";
	}
}


//########################################################################################################################
//  DELETE TICKETS
//########################################################################################################################
function delete_tickets()
{
    global $lang_global, $characters_db, $realm_id, $action_permission;
    valid_login($action_permission['delete']);

    if(!isset($_GET['check']))
        redirect("ticket.php?error=1");

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $check = $sqlc->quote_smart($_GET['check']);

    $deleted_tickets = 0;
	if (is_array($check) || is_object($check))
    {
		foreach($check as $id)
		{
        $query = $sqlc->query("DELETE FROM gm_ticket WHERE id = '$id'");
        $deleted_tickets++;
		}
	}

    if (0 == $deleted_tickets)
        redirect('ticket.php?error=3');
    else
        redirect('ticket.php?error=2');
}


//########################################################################################################################
//  EDIT TICKET
//########################################################################################################################
function edit_ticket()
{
    global  $lang_global, $lang_ticket, $output, $characters_db, $realm_id, $action_permission;
    valid_login($action_permission['update']);

    if(!isset($_GET['id']))
        redirect("Location: ticket.php?error=1");

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $id = $sqlc->quote_smart($_GET['id']);
    if(!is_numeric($id))
        redirect("ticket.php?error=1");

    $query = $sqlc->query("SELECT gm_ticket.playerGuid, gm_ticket.description, BINARY `characters`.name AS name
                            FROM `characters`
                            LEFT JOIN gm_ticket ON gm_ticket.playerGuid =`characters`.`guid`
                            WHERE gm_ticket.playerGuid = `characters`.`guid` AND gm_ticket.id = '$id'");

    if ($ticket = $sqlc->fetch_row($query))
    {
        $output .= "
                <center>
                    <fieldset style=\"width: 550px;\">
                        <legend>{$lang_ticket['edit_reply']}</legend>
                        <form method=\"post\" action=\"ticket.php?action=do_edit_ticket\" name=\"form\">
                            <input type=\"hidden\" name=\"id\" value=\"$id\" />
                            <table class=\"flat\">
                                <tr>
                                    <td>{$lang_ticket['ticket_id']}</td>
                                    <td>$id</td>
                                </tr>
                                <tr>
                                    <td>{$lang_ticket['submitted_by']}:</td>
                                    <td><a href=\"char.php?id=$ticket[0]\">".htmlentities($ticket[2])."</a></td>
                                </tr>
                                <tr>
                                    <td valign=\"top\">{$lang_ticket['ticket_text']}</td>
                                    <td><textarea name=\"new_text\" rows=\"5\" cols=\"40\" maxlength=\"500\">".htmlentities($ticket[1])."</textarea></td>
                                </tr>
                                <tr>
                                    <td>";
        makebutton($lang_ticket['update'], "javascript:do_submit()\" type=\"wrn",130);

        $output .= "
                                    </td>
                                    <td>
                                    <table class=\"hidden\">
                                        <tr>
                                            <td>";
        makebutton($lang_ticket['send_ingame_mail'], "mail.php?type=ingame_mail&amp;to=$ticket[2]",130);
        $output .= "
                                            </td>
                                            <td>";
        makebutton($lang_global['back'], "ticket.php\" type=\"def",130);
        $output .= "
                                            </td>
                                        </tr>
                                    </table>";
        $output .= "
                                </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br />
                </center>
				<ul>
					<li>'Update Ticket' option needs .reload gm_ticket to apply changes ingame.</li>
				</ul>";
    }
    else
        error($lang_global['err_no_records_found']);
}


//########################################################################################################################
//  DO EDIT  TICKET
//########################################################################################################################
function do_edit_ticket()
{
    global $characters_db, $realm_id, $action_permission;
    valid_login($action_permission['update']);

    if(empty($_POST['new_text']) || empty($_POST['id']) )
        redirect("ticket.php?error=1");

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $new_text = $sqlc->quote_smart($_POST['new_text']);
	$new_text = str_replace('\\\\r\\', '', $new_text);
	
    $id = $sqlc->quote_smart($_POST['id']);
    if(is_numeric($id))
		$query = $sqlc->query("UPDATE gm_ticket SET description='$new_text' WHERE id = '$id'");
    else
        redirect("ticket.php?error=1");

    if ($sqlc->affected_rows())
        redirect("ticket.php?error=5");
    else
        redirect("ticket.php?error=6");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

$lang_ticket = lang_ticket();

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
                <font class=\"error\">{$lang_ticket['ticked_deleted']}</font>
            </h1>";
        break;
    case 3:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_ticket['ticket_not_deleted']}</font>
            </h1>";
        break;
    case 4:
        $output .= "
            <h1>{$lang_ticket['edit_ticked']}</h1>";
        break;
    case 5:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_ticket['ticket_updated']}</font>
            </h1>";
        break;
    case 6:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_ticket['ticket_update_err']}</font>
            </h1>";
        break;
    default: //no error
        $output .= "
            <h1>{$lang_ticket['browse_tickets']}</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$tickets = new tickets;

switch ($action)
{
    case "browse_tickets":
        $tickets->browse_tickets($sqlc);
        break;
    case "delete_tickets":
        delete_tickets();
        break;
    case "edit_ticket":
        edit_ticket();
        break;
    case "do_edit_ticket":
        do_edit_ticket();
        break;
    default:
        $tickets->browse_tickets($sqlc);
}

unset($action);
unset($action_permission);
unset($lang_tikcet);

require_once 'footer.php';

?>
