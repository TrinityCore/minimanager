<?php
require_once("header.php");

//#####################################################################################################
// DO EMAIL VERIFICATION
//#####################################################################################################
function do_verify_email() {
    global $user_name, $authkey, $mail, $from_mail, $title, $mailer_type, $smtp_cfg;
    
    require_once("libs/mailer/class.phpmailer.php");
    
    $mail2 = new PHPMailer();
    $mail2->Mailer = $mailer_type;
    
    if ($mailer_type == "smtp")
    {
        $mail2->Host = $smtp_cfg['host'];
        $mail2->Port = $smtp_cfg['port'];
        $mail2->SMTP_sec = $smtp_cfg['sec'];
        
        if($smtp_cfg['user'] != '') {
            $mail2->SMTPAuth  = true;
            $mail2->Username  = $smtp_cfg['user'];
            $mail2->Password  =  $smtp_cfg['pass'];
        }
    }

    $body = "Hello, {$user_name}!<br /><br />Thank you for registering on our account management system. Unfortunately, all new accounts are required to verify their email addresses for security. Please follow the link below to verify your account now!<br /><br />
            <a href=\"{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?username={$user_name}&amp;authkey={$authkey}\">{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?username={$user_name}&amp;authkey={$authkey}</a>
            If you are unable to see the link above, please copy and paste the following URL into your browsers address bar.<br />{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?username={$user_name}&amp;authkey={$authkey}<br /><br />Thank you, <br />{$title} Manager";

    $mail2->WordWrap = 50;
    $mail2->From = $from_mail;
    $mail2->FromName = "{$title} Admin";
    $mail2->Subject = "Account Verfication Needed";
    $mail2->IsHTML(true);
    $mail2->Body = $body;
    $mail2->AddAddress($mail);

    if(!$mail2->Send()) 
    {
        $mail2->ClearAddresses();
        redirect("register.php?&err=11&usr=".$mail2->ErrorInfo);
    } 
    else
        return "Excellent job!";
}
//#####################################################################################################
// DO REGISTER
//#####################################################################################################
function doregister(){
    global $lang_global, $characters_db, $realm_db, $realm_id, $mmfpm_db, $disable_acc_creation, $limit_acc_per_ip, $valid_ip_mask, $expansion_select,
           $send_mail_on_creation, $create_acc_locked, $from_mail, $mailer_type, $smtp_cfg, $title, $defaultoption, $require_account_verify;

    if (($_POST['security_code']) != ($_SESSION['security_code']))
        redirect("register.php?err=13");

    if (empty($_POST['pass']) || empty($_POST['email']) || empty($_POST['username']))
        redirect("register.php?err=1");

    if ($disable_acc_creation) 
        redirect("register.php?err=4");

    $last_ip =  (getenv('HTTP_X_FORWARDED_FOR')) ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR');

    if (sizeof($valid_ip_mask))
    {
        $qFlag = 0;
        $user_ip_mask = explode('.', $last_ip);

        foreach($valid_ip_mask as $mask)
        {
            $vmask = explode('.', $mask);
            $v_count = 4;
            $i = 0;
            
            foreach($vmask as $range)
            {
                $vmask_h = explode('-', $range);
                
                if (isset($vmask_h[1]))
                {
                    if (($vmask_h[0]>=$user_ip_mask[$i]) && ($vmask_h[1]<=$user_ip_mask[$i])) 
                        $v_count--;
                }
                else
                {
                    if ($vmask_h[0] == $user_ip_mask[$i]) 
                        $v_count--;
                }
                $i++;
            }
            
            if (!$v_count)
            {
                $qFlag++;
                break;
            }
        }
        if (!$qFlag) 
            redirect("register.php?err=9&usr=$last_ip");
    }

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $user_name = $sql->quote_smart(trim($_POST['username']));
    $pass = $sql->quote_smart($_POST['pass']);
    $pass1 = $sql->quote_smart($_POST['pass1']);

    //make sure username/pass at least 4 chars long and less than max
    if ((strlen($user_name) < 4) || (strlen($user_name) > 15))
    {
        $sql->close();
        redirect("register.php?err=5");
    }

    //make sure it doesnt contain non english chars.
    if (!ctype_alnum($user_name)) 
    {
        $sql->close();
        redirect("register.php?err=6");
    }

    //make sure the mail is valid mail format
    $mail = $sql->quote_smart(trim($_POST['email']));
    if ((!valid_email($mail))||(strlen($mail) > 224)) 
    {
        $sql->close();
        redirect("register.php?err=7");
    }

    $per_ip = ($limit_acc_per_ip) ? "OR last_ip='$last_ip'" : "";

    $result = $sql->query("SELECT ip FROM ip_banned WHERE ip = '$last_ip'");
    //IP is in ban list
    if ($sql->num_rows($result))
    {
        $sql->close();
        redirect("register.php?err=8&usr=$last_ip");
    }

    //Email check
    $result = $sql->query("SELECT email FROM account WHERE email='$mail' $per_ip");
    if ($sql->num_rows($result))
    {
        $sql->close();
        redirect("register.php?err=14");
    }
  
    //Username check
    $result = $sql->query("SELECT username FROM account WHERE username='$user_name' $per_ip");
    if ($sql->num_rows($result))
    {
        $sql->close();
        redirect("register.php?err=3");
    }

    //there is already someone with same account name
    if ($sql->num_rows($result))
    {
        $sql->close();
        redirect("register.php?err=3&usr=$user_name");
    }
    else 
    {
        if ($expansion_select)
            $expansion = (isset($_POST['expansion'])) ? $sql->quote_smart($_POST['expansion']) : 0;
        else 
            $expansion = $defaultoption;

        if ($require_account_verify) 
        {
            $sql2 = new SQL;
            $sql2->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
            $query2_result = $sql2->query("SELECT * FROM mm_account WHERE username = '$user_name' OR email = '$mail'");
            
            if ($sql2->num_rows($query2_result) > 0) 
              redirect("register.php?err=15");
            else 
            {
                $client_ip = $_SERVER['REMOTE_ADDR'];
                $authkey = sha1($client_ip . time());
                $result = $sql2->query("INSERT INTO mm_account (username,sha_pass_hash,email, joindate,last_ip,failed_logins,locked,last_login,expansion,authkey)
                                        VALUES (UPPER('$user_name'),'$pass','$mail',now(),'$last_ip','0','$create_acc_locked',NULL,'$expansion','$authkey')");
                do_verify_email();
                redirect("login.php?error=7");
            }
            $sql2->close();
        }
        else 
        {
            $result = $sql->query("INSERT INTO account (username,sha_pass_hash,email, joindate,last_ip,failed_logins,locked,last_login,expansion)
                                   VALUES (UPPER('$user_name'),'$pass','$mail',now(),'$last_ip',0,$create_acc_locked,NULL,$expansion)");
            $query_result = mysql_fetch_assoc($sql->query("SELECT id FROM account WHERE username = '$user_name'"));
        }

        $sql->close();

        setcookie ("terms", "", time() - 3600);

        if ($send_mail_on_creation)
        {
            require_once("libs/mailer/class.phpmailer.php");
            
            $mailer = new PHPMailer();
            $mailer->Mailer = $mailer_type;
            
            if ($mailer_type == "smtp")
            {
                $mailer->Host = $smtp_cfg['host'];
                $mailer->Port = $smtp_cfg['port'];
                $mailer->SMTP_sec = $smtp_cfg['sec'];
                
                if($smtp_cfg['user'] != '') 
                {
                    $mailer->SMTPAuth  = true;
                    $mailer->Username  = $smtp_cfg['user'];
                    $mailer->Password  =  $smtp_cfg['pass'];
                }
            }

            $file_name = "mail_templates/mail_welcome.tpl";
            $fh = fopen($file_name, 'r');
            $subject = fgets($fh, 4096);
            $body = fread($fh, filesize($file_name));
            fclose($fh);

            $subject = str_replace("<title>", $title, $subject);
            $body = str_replace("\n", "<br />", $body);
            $body = str_replace("\r", " ", $body);
            $body = str_replace("<username>", $user_name, $body);
            $body = str_replace("<password>", $pass1, $body);
            $body = str_replace("<base_url>", $_SERVER['SERVER_NAME'], $body);

            $mailer->WordWrap = 50;
            $mailer->From = $from_mail;
            $mailer->FromName = "$title Admin";
            $mailer->Subject = $subject;
            $mailer->IsHTML(true);
            $mailer->Body = $body;
            $mailer->AddAddress($mail);
            $mailer->Send();
            $mailer->ClearAddresses();
        }

        if ($result) 
            redirect("login.php?error=6");
    }
}

//#####################################################################################################
// PRINT FORM
//#####################################################################################################
function register(){
    global $lang_register, $lang_global, $output, $expansion_select, $lang_captcha ,$lang_command, $enable_captcha;

    $output .= "
                <center>
                    <script type=\"text/javascript\" src=\"libs/js/sha1.js\"></script>
                    <script type=\"text/javascript\">
                        function do_submit_data () {
                            if (document.form.pass1.value != document.form.pass2.value){
                                alert('{$lang_register['diff_pass_entered']}');
                                return;
                            } else if (document.form.pass1.value.length > 225){
                                alert('{$lang_register['pass_too_long']}');
                                return;
                            } else {
                                document.form.pass.value = hex_sha1(document.form.username.value.toUpperCase()+':'+document.form.pass1.value.toUpperCase());
                                document.form.pass2.value = '0';
                                do_submit();
                            }
                        }
                        answerbox.btn_ok='{$lang_register['i_agree']}';
                        answerbox.btn_cancel='{$lang_register['i_dont_agree']}';
                        answerbox.btn_icon='';
                    </script>
                    <fieldset class=\"half_frame\">
                        <legend>{$lang_register['create_acc']}</legend>
                        <form method=\"post\" action=\"register.php?action=doregister\" name=\"form\">
                            <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
                            <table class=\"flat\">
                                <tr>
                                    <td valign=\"top\">{$lang_register['username']}:</td>
                                    <td>
                                        <input type=\"text\" name=\"username\" size=\"45\" maxlength=\"14\" /><br />
                                        {$lang_register['use_eng_chars_limited_len']}<br />
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=\"top\">{$lang_register['password']}:</td>
                                    <td><input type=\"password\" name=\"pass1\" size=\"45\" maxlength=\"25\" /></td>
                                </tr>
                                <tr>
                                    <td valign=\"top\">{$lang_register['confirm_password']}:</td>
                                    <td>
                                        <input type=\"password\" name=\"pass2\" size=\"45\"  maxlength=\"25\" /><br />
                                        {$lang_register['min_pass_len']}<br />
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=\"top\">{$lang_register['email']}:</td>
                                    <td>
                                        <input type=\"text\" name=\"email\" size=\"45\" maxlength=\"225\" /><br />
                                        {$lang_register['use_valid_mail']}</td>
                                </tr>";
                                
    if ( $enable_captcha ) 
        $output .= "
                                <tr>
                                    <td></td>
                                    <td><img src=\"libs/captcha/CaptchaSecurityImages.php?width=300&height=80&characters=6\" /><br /><br /></td>
                                </tr>
                                <tr>
                                    <td valign=\"top\">{$lang_captcha['security_code']}:</td>
                                    <td>
                                        <input type=\"text\" name=\"security_code\" autocomplete=\"off\" size=\"45\" /><br />
                                    </td>
                                </tr>";
    
    if ( $expansion_select ) 
        $output .= "
                                <tr>
                                    <td valign=\"top\">{$lang_register['acc_type']}:</td>
                                    <td>
                                        <select name=\"expansion\">
                                            <option value=\"2\">{$lang_register['wotlk']}</option>
                                            <option value=\"1\">{$lang_register['tbc']}</option>
                                            <option value=\"0\">{$lang_register['classic']}</option>
                                        </select>
                                        - {$lang_register['acc_type_desc']}
                                    </td>
                                </tr>";

    $output .= "
                                <tr>
                                    <td colspan=\"2\"><hr /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"2\">{$lang_register['read_terms']}.</td>
                                </tr>
                                <tr>
                                    <td colspan=\"2\"><hr /></td>
                                </tr>
                                <tr>
                                    <td>";

    $terms = "
                                        <textarea rows=\'18\' cols=\'80\' readonly=\'readonly\'>";
    $fp = fopen("mail_templates/terms.tpl", 'r') or die (error("Couldn't Open terms.tpl File!"));
    while (!feof($fp)) 
        $terms .= fgets($fp, 1024);
    fclose($fp);
    $terms .= "
                                        </textarea>";

    makebutton($lang_register['create_acc_button'], "javascript:answerBox('{$lang_register['terms']}<br />$terms', 'javascript:do_submit_data()')",150);
    
    $output .= "
                                    </td>
                                    <td>";
                                    
    makebutton($lang_global['back'], "login.php", 328);
    
    $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br /><br />
                </center>";
}


//#####################################################################################################
// PRINT PASSWORD RECOVERY FORM
//#####################################################################################################
function pass_recovery(){
    global $lang_register, $lang_global, $output;
    
    $output .= "
                <center>
                    <fieldset class=\"half_frame\">
                        <legend>{$lang_register['recover_acc_password']}</legend>
                        <form method=\"post\" action=\"register.php?action=do_pass_recovery\" name=\"form\">
                            <table class=\"flat\">
                                <tr>
                                    <td valign=\"top\">{$lang_register['username']} :</td>
                                    <td>
                                        <input type=\"text\" name=\"username\" size=\"45\" maxlength=\"14\" /><br />
                                        {$lang_register['user_pass_rec_desc']}<br />
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=\"top\">{$lang_register['email']} :</td>
                                    <td>
                                        <input type=\"text\" name=\"email\" size=\"45\" maxlength=\"225\" /><br />
                                        {$lang_register['mail_pass_rec_desc']}
                                    </td>
                                </tr>
                                <tr>
                                    <td>";
                                    
    makebutton($lang_register['recover_pass'], "javascript:do_submit()",150);
    
    $output .= "
                                    </td>
                                    <td>";
                                    
    makebutton($lang_global['back'], "javascript:window.history.back()", 328);
    
    $output .= "
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <br /><br />
                </center>";
}

//#####################################################################################################
// DO RECOVER PASSWORD
//#####################################################################################################
function do_pass_recovery(){
    global $lang_global, $realm_db, $from_mail, $mailer_type, $smtp_cfg, $title;

    if ( empty($_POST['username']) || empty($_POST['email']) ) 
        redirect("register.php?action=pass_recovery&err=1");

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $user_name = $sql->quote_smart(trim($_POST['username']));
    $email_addr = $sql->quote_smart($_POST['email']);

    $result = $sql->query("SELECT sha_pass_hash FROM account WHERE username = '$user_name' AND email = '$email_addr'");

    if ($sql->num_rows($result) == 1)
    {
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

        $file_name = "mail_templates/recover_password.tpl";
        $fh = fopen($file_name, 'r');
        $subject = fgets($fh, 4096);
        $body = fread($fh, filesize($file_name));
        fclose($fh);

        $body = str_replace("\n", "<br />", $body);
        $body = str_replace("\r", " ", $body);
        $body = str_replace("<username>", $user_name, $body);
        $body = str_replace("<password>", substr(sha1(strtoupper($user_name)),0,7), $body);
        $body = str_replace("<activate_link>",
        $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?action=do_pass_activate&amp;h=".$sql->result($result, 0, 'sha_pass_hash')."&amp;p=".substr(sha1(strtoupper($user_name)),0,7), $body);
        $body = str_replace("<base_url>", $_SERVER['HTTP_HOST'], $body);

        $mail->WordWrap = 50;
        $mail->From = $from_mail;
        $mail->FromName = "$title Admin";
        $mail->Subject = $subject;
        $mail->IsHTML(true);
        $mail->Body = $body;
        $mail->AddAddress($email_addr);

        if(!$mail->Send()) 
        {
            $mail->ClearAddresses();
            redirect("register.php?action=pass_recovery&err=11&usr=".$mail->ErrorInfo);
        } 
        else 
        {
            $mail->ClearAddresses();
            redirect("register.php?action=pass_recovery&err=12");
        }
    } 
    else 
        redirect("register.php?action=pass_recovery&err=10");
}


//#####################################################################################################
// DO ACTIVATE RECOVERED PASSWORD
//#####################################################################################################
function do_pass_activate(){
    global $lang_global, $realm_db;

    if (empty($_GET['h']) || empty($_GET['p'])) 
        redirect("register.php?action=pass_recovery&err=1");

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $pass = $sql->quote_smart(trim($_GET['p']));
    $hash = $sql->quote_smart($_GET['h']);

    $result = $sql->query("SELECT id,username FROM account WHERE sha_pass_hash = '$hash'");

    if ($sql->num_rows($result) == 1)
    {
        $username = $sql->result($result, 0, 'username');
        $id = $sql->result($result, 0, 'id');
        
        if (substr(sha1(strtoupper($sql->result($result, 0, 'username'))),0,7) == $pass)
        {
            $sql->query("UPDATE account SET sha_pass_hash=SHA1(CONCAT(UPPER('$username'),':',UPPER('$pass'))), v=0, s=0 WHERE id = '$id'");
            redirect("login.php");
        }

    } 
    else 
        redirect("register.php?action=pass_recovery&err=1");

    redirect("register.php?action=pass_recovery&err=1");
}


//#####################################################################################################
// MAIN
//#####################################################################################################
$err = (isset($_GET['err'])) ? $_GET['err'] : NULL;

if (isset($_GET['usr'])) 
    $usr = $_GET['usr'];
else
    $usr = NULL;

$lang_captcha = lang_captcha();

$output .=  "
        <div class=\"top\">";

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
                <font class=\"error\">{$lang_register['diff_pass_entered']}</font>
            </h1>";
        break;
    case 3:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['username']} $usr {$lang_register['already_exist']}</font>
            </h1>";
        break;
    case 4:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['acc_reg_closed']}</font>
            </h1>";
        break;
    case 5:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['wrong_pass_username_size']}</font>
            </h1>";
        break;
    case 6:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['bad_chars_used']}</font>
            </h1>";
        break;
    case 7:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['invalid_email']}</font>
            </h1>";
        break;
    case 8:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['banned_ip']} ($usr)<br />{$lang_register['contact_serv_admin']}</font>
            </h1>";
        break;
    case 9:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['users_ip_range']}: $usr {$lang_register['cannot_create_acc']}</font>
            </h1>";
        break;
    case 10:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['user_mail_not_found']}</font>
            </h1>";
        break;
    case 11:
        $output .= "
            <h1>
                <font class=\"error\">Mailer Error: $usr</font>
            </h1>";
        break;
    case 12:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['recovery_mail_sent']}</font>
            </h1>";
        break;
    case 13:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_captcha['invalid_code']}</font>
            </h1>";
        break;
    case 14:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['email_address_used']}</font>
            </h1>";
        break;
    case 15:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['account_needs_verified']}</font>
            </h1>";
        break;
    default:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_register['fill_all_fields']}</font>
            </h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action){
    case "doregister":
        doregister();
        break;
    case "pass_recovery":
        pass_recovery();
        break;
    case "do_pass_recovery":
        do_pass_recovery();
        break;
    case "do_pass_activate":
        do_pass_activate();
        break;
    default:
        register();
}

unset($action);
unset($action_permission);
unset($lang_captcha);

require_once("footer.php");
?>
