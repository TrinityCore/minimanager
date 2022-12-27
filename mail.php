<?php


require_once("header.php");
valid_login($action_permission['update']);

//###########################################################################
// print mail form
function print_mail_form()
{
    global $lang_mail, $output;

    $to = (isset($_GET['to'])) ? $_GET['to'] : NULL;
    $type = (isset($_GET['type'])) ? $_GET['type'] :"email";

    $output .= "
                <center>
                    <form action=\"mail.php?action=send_mail\" method=\"post\" name=\"form\">
                    <fieldset style=\"width: 770px;\">
                        <legend>{$lang_mail['mail_type']}</legend>
                        <br />
                        <table class=\"top_hidden\" style=\"width: 720px;\">
                            <tr>
                                <td align=\"left\"><label for='to'>{$lang_mail['recipient']}</label>: <input type=\"text\" name=\"to\" id='to' size=\"32\" value=\"$to\" maxlength=\"225\" /></td>
                                <td align=\"left\"><label for='subject'>{$lang_mail['subject']}</label>: <input type=\"text\" name=\"subject\" id='subject' size=\"32\" maxlength=\"50\" /></td>
                                <td width=\"1\" align=\"left\">
                                <label for='type'>{$lang_mail['mail_type']}:</label>
                                    <select name=\"type\" id='type'>";
    if ($type == "email")
        $output .= "
                                        <option value=\"email\">{$lang_mail['email']}</option>
                                        <option value=\"ingame_mail\">{$lang_mail['ingame_mail']}</option>";
    else
        $output .= "
                                        <option value=\"ingame_mail\">{$lang_mail['ingame_mail']}</option>
                                        <option value=\"email\">{$lang_mail['email']}</option>";
    $output .= "
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"3\">
                                    <hr />
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"3\">
                                    {$lang_mail['dont_use_both_groupsend_and_to']}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"3\">{$lang_mail['group_send']}:
                                    <select name=\"group_send\" title='Group send'>
                                        <optgroup label=\"{$lang_mail['both']}\">
                                            <option value=\"gm_level\">{$lang_mail['gm_level']}</option>
                                        </optgroup>
                                        <optgroup label=\"{$lang_mail['email']}\">
                                            <option value=\"locked\">{$lang_mail['locked_accounts']}</option>
                                            <option value=\"banned\">{$lang_mail['banned_accounts']}</option>
                                        </optgroup>
                                        <optgroup label=\"{$lang_mail['ingame_mail']}\">
                                            <option value=\"char_level\">{$lang_mail['char_level']}</option>
                                            <option value=\"online\">{$lang_mail['online']}</option>
                                        </optgroup>
                                    </select>
                                    <select name=\"group_sign\" title='Group operator'>
                                        <option value=\"=\">=</option>
                                        <option value=\"&lt;\">&lt;</option>
                                        <option value=\">\">&gt;</option>
                                        <option value=\"!=\">!=</option>
                                    </select>
                                    <input type=\"text\" name=\"group_value\" size=\"20\" maxlength=\"40\" title='Group parameter' />
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"3\"><hr /></td>
                            </tr>
                            <tr>
                                <td colspan=\"3\" align=\"left\">
                                    {$lang_mail['attachments']}:
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"3\" align=\"right\">
                                    <label for='money'>{$lang_mail['money']}</label> : <input type=\"text\" name=\"money\" id='money' value=\"0\" size=\"10\" maxlength=\"10\" />
                                    <label for='att_item'>{$lang_mail['item']}</label> : <input type=\"text\" name=\"att_item\" id='att_item' value=\"0\" size=\"10\" maxlength=\"10\" />
                                    <label for='att_stack'>{$lang_mail['stack']}</label> : <input type=\"text\" name=\"att_stack\" id='att_item' value=\"0\" size=\"10\" maxlength=\"10\" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"3\">
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset style=\"width: 770px;\">
                        <legend>{$lang_mail['mail_body']}</legend>
                        <br /><textarea name=\"body\" rows=\"14\" cols=\"92\" 
                        placeholder='{$lang_mail['placeholder']}'></textarea><br />
                        <br />
                        <table>
                            <tr>
                                <td>";

    makebutton($lang_mail['send'], "javascript:do_submit()",130);

    $output .= "
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                </form>
            </center>";
}

//#############################################################################
// Send the actual mail(s)
function send_mail()
{
    global $lang_global, $output, $realm_db, $characters_db, $realm_id, $user_name, $from_mail, $mailer_type, $smtp_cfg;

    if (empty($_POST['body']) || empty($_POST['subject']) || empty($_POST['type']) || empty($_POST['group_sign']) || empty($_POST['group_send']))
    {
        redirect("mail.php?error=1");
    }

    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $body = explode("\n",$_POST['body']);
    $subject = $sqlc->quote_smart($_POST['subject']);

    if(isset($_POST['to'])&&($_POST['to'] != ''))
        $to = $sqlc->quote_smart($_POST['to']);
    else
    {
        $to = 0;
        if(!isset($_POST['group_value'])||$_POST['group_value'] === '')
        {
            redirect("mail.php?error=1");
        }
        else
        {
            $group_value = $sqlc->quote_smart($_POST['group_value']);
            $group_sign = $sqlc->quote_smart($_POST['group_sign']);
            $group_send = $sqlc->quote_smart($_POST['group_send']);
        }
    }

    $type = addslashes($_POST['type']);
    $att_gold = $sqlc->quote_smart($_POST['money']);
    $att_item = $sqlc->quote_smart($_POST['att_item']);
    $att_stack = $sqlc->quote_smart($_POST['att_stack']);

    switch ($type)
    {
        case "email":
            require_once("libs/mailer/class.phpmailer.php");
            $mail = new PHPMailer();
            $mail->Mailer = $mailer_type;
            if ($mailer_type == "smtp")
            {
                $mail->Host = $smtp_cfg['host'];
                $mail->Port = $smtp_cfg['port'];
                $mail->SMTP_sec = $smtp_cfg['sec'];
                if($smtp_cfg['user'] != '')
                {
                    $mail->SMTPAuth  = true;
                    $mail->Username  = $smtp_cfg['user'];
                    $mail->Password  =  $smtp_cfg['pass'];
                }
            }

            $value = NULL;
            for($i=0;$i<(count($body));$i++)
                $value .= $body[$i]."\r\n";
            $body=$value;

            $mail->From = $from_mail;
            $mail->FromName = $user_name;
            $mail->Subject = $subject;
            $mail->IsHTML(true);

            $body = str_replace("\n", "<br />", $body);
            $body = str_replace("\r", " ", $body);
            $body = str_replace(["\r\n", "\n", "\r"], '<br />', $body);
            $body = preg_replace( "/([^\/=\"\]])((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>",  $body);
            $body = preg_replace('/([^\/=\"\]])(www\.)(\S+)/', '\\1<a href="https://\\2\\3" target="_blank">\\2\\3</a>', $body);

            $mail->Body = $body;
            $mail->WordWrap = 50;

            if($to)
            {
                //single Recipient
                $mail->AddAddress($to);
                if(!$mail->Send())
                {
                    $mail->ClearAddresses();
                    redirect("mail.php?error=3&mail_err=".$mail->ErrorInfo);
                }
                else
                {
                    $mail->ClearAddresses();
                    redirect("mail.php?error=2");
                }
            }
            elseif (isset($group_value))
            {
                //group send
                $email_array = [];
                switch ($group_send)
                {
                    case "gm_level":
                        $result = $sqlr->query("SELECT email FROM account WHERE id IN (SELECT AccountID FROM account_access WHERE SecurityLevel $group_sign '$group_value')");
                        while($user = $sql->fetch_row($result))
                        {
                            if($user[0] != "") array_push($email_array, $user[0]);
                        }
                        break;

                    case "locked":
                        $result = $sql->query("SELECT email FROM account WHERE locked $group_sign '$group_value'");
                        while($user = $sql->fetch_row($result))
                        {
                            if($user[0] != "")
                                array_push($email_array, $user[0]);
                        }
                        break;

                    case "banned":
                        $que = $sqlr->query("SELECT id FROM account_banned");
                        while ($banned = $sql->fetch_row($que))
                        {
                            $result = $sqlr->query("SELECT email FROM account WHERE id = '$banned[0]'");
                            if($sqlr->result($result, 0, 'email'))
                                array_push($email_array, $sql->result($result, 0, 'email'));
                        }
                        break;

                    default:
                        redirect("mail.php?error=5");
                        break;
                }
                foreach ($email_array as $mail_addr)
                {
                    $mail->AddAddress($mail_addr);
                    if(!$mail->Send())
                    {
                        $mail->ClearAddresses();
                        redirect("mail.php?error=3&mail_err=".$mail->ErrorInfo);
                    }
                    else
                    {
                        $mail->ClearAddresses();
                    }
                }
                redirect("mail.php?error=2");
            }
            else
                redirect("mail.php?error=1");
            break;

        case "ingame_mail":
            $value = NULL;
            for($i=0;$i<(count($body));$i++)
            $value .= $body[$i]." ";
            $body=$value;
            $body = str_replace("\r", " ", $body);

            if($to)
            {
                //single Recipient
                $result = $sqlc->query("SELECT BINARY name AS name FROM characters WHERE BINARY name = '$to'");
                if ($sqlc->num_rows($result) == 1)
                {
                    $receiver = $sqlc->result($result, 0, 'name');
                    $mails = [];
                    array_push($mails, [$receiver, $subject, $body, $att_gold, $att_item, $att_stack]);
                    send_ingame_mail($realm_id, $mails);
                }
                else
                {
                    redirect("mail.php?error=4");
                }
                redirect("mail.php?error=2");
                break;
            }
            elseif(isset($group_value))
            {
                //group send
                $char_array = [];
                switch ($group_send)
                {
                    case "gm_level":
                        $result = $sqlr->query("SELECT AccountID FROM account_access WHERE SecurityLevel $group_sign '$group_value'");
                        while($acc = $sqlc->fetch_row($result))
                        {
                            $result_2 = $sqlc->query("SELECT BINARY name AS name FROM `characters` WHERE account = '$acc[0]'");
                            while($char = $sqlc->fetch_row($result_2))
                                array_push($char_array, $char[0]);
                        }
                        break;

                    case "online":
                        $result = $sqlc->query("SELECT BINARY name AS name FROM `characters` WHERE online $group_sign '$group_value'");
                        while($user = $sqlc->fetch_row($result))
                            array_push($char_array, $user[0]);
                        break;

                    case "char_level":
                        $result = $sqlc->query("SELECT BINARY name AS name FROM `characters` WHERE level $group_sign '$group_value'");
                        while($user = $sqlc->fetch_row($result))
                            array_push($char_array, $user[0]);
                        break;

                    default:
                        redirect("mail.php?error=5");
                }
                $mails = [];
                foreach ($char_array as $receiver)
                {
                    array_push($mails, [$receiver, $subject, $body, $att_gold, $att_item, $att_stack]);
                }
                send_ingame_mail($realm_id, $mails);
                redirect("mail.php?error=2");
            }
            break;
        default:
            redirect("mail.php?error=1");
    }
}

//##########################################################################################
//SEND INGAME MAIL BY TELNET
//
// Xiong Guoy
// 2009-08-08
function send_ingame_mail($realm_id, $massmails)
{
    require_once 'libs/telnet_lib.php';
    global $server, $lang_telnet;

    $telnet = new telnet_lib();

    //$massmails array format
    //($to, $subject, $body, $gold = 0, $item = 0, $stack = 1)

    $result = $telnet->Connect($server[$realm_id]['addr'], $server[$realm_id]['telnet_port'], $server[$realm_id]['telnet_user'], $server[$realm_id]['telnet_pass']);

    if (0 == $result)
    {
        $mess_str = '';
        $result = '';
        foreach($massmails as $mails)
        {
            if ($mails[3] && $mails[4])
            {
                $mess_str1 = "send money ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[3]."";
                $telnet->DoCommand($mess_str1, $result1);

                $mess_str .= $mess_str1."<br >";
                $result .= $result1."";

                $mess_str1 = "send items ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[4].(($mails[5] > 1) ? "[:".$mails[5]."]" : " ");
                $telnet->DoCommand($mess_str1, $result1);

                $mess_str .= $mess_str1."<br >";
                $result .= $result1."";
            }
            elseif ($mails[3])
            {
                $mess_str1 = "send money ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[3]."";
                $telnet->DoCommand($mess_str1, $result1);

                $mess_str .= $mess_str1."<br >";
                $result .= $result1."";
            }
            elseif ($mails[4])
            {
                $mess_str1 = "send items ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[4].(($mails[5] > 1) ? "[:".$mails[5]."]" : " ");
                $telnet->DoCommand($mess_str1, $result1);

                $mess_str .= $mess_str1."<br >";
                $result .= $result1."";
            }
            else
            {
                $mess_str1 = "send mail ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\"";
                $telnet->DoCommand($mess_str1, $result1);

                $mess_str .= $mess_str1."<br >";
                $result .= $result1."";
            }
        }
        $result = str_replace("TC>","",$result);
        $result = str_replace(["\r\n", "\n", "\r"], '<br />', $result);
        $mess_str .= "<br /><br />".$result;
        $telnet->Disconnect();
    }
    elseif (1 == $result)
        $mess_str = $lang_telnet['unable'];
    elseif (2 == $result)
        $mess_str = $lang_telnet['unknown_host'];
    elseif (3 == $result)
        $mess_str = $lang_telnet['login_failed'];
    elseif (4 == $result)
        $mess_str = $lang_telnet['not_supported'];

    redirect("mail.php?action=result&error=6&mess=$mess_str");
}


//########################################################################################################################
// InGame Mail Result
//########################################################################################################################
//
// Xiong Guoy
// 2009-08-08
// report page for send_ingame_mail
function result()
{
    global $lang_global, $output;
    $mess = (isset($_GET['mess'])) ? $_GET['mess'] : NULL;
    $output .= '
        <center>
            <br />
            <table width="400" class="flat">
                <tr>
                    <td align="left">
                        <br />'.$mess.'<br />';
    unset($mess);
    $output .= '
                    </td>
                </tr>
            </table>
            <br />
            <table width="400" class="hidden">
                <tr>
                    <td align="center">';

    makebutton($lang_global['back'], 'javascript:window.history.back()', 130);

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

$output .= "
        <div class=\"top\">";

$lang_mail = lang_mail();
$lang_telnet = lang_telnet();

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
                <font class=\"error\">{$lang_mail['mail_sent']}</font>
            </h1>";
        break;
    case 3:
        $mail_err = (isset($_GET['mail_err'])) ? $_GET['mail_err'] : NULL;
        $output .= "
            <h1>
                <font class=\"error\">{$lang_mail['mail_err']}: $mail_err</font>
            </h1>";
        break;
    case 4:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_mail['no_recipient_found']}</font>
            </h1>
            {$lang_mail['use_name_or_email']}";
        break;
    case 5:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_mail['option_unavailable']}</font>
            </h1>
            {$lang_mail['use_currect_option']}";
        break;
    case 6:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_mail['result']}</font>
            </h1>";
        break;
    default: //no error
        $output .= "
            <h1>{$lang_mail['send_mail']}</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "send_mail":
        send_mail();
        break;

    case "result":
        result();
        break;

    default:
        print_mail_form();
}

unset($action);
unset($action_permission);
unset($lang_telnet);
unset($lang_mail);

require_once("footer.php");

?>
