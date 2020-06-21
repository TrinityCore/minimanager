<?php

require_once("header.php");

//##############################################################################################
// MAIN
//##############################################################################################

$username = (isset($_GET['username'])) ? $_GET['username'] : NULL;
$authkey = (isset($_GET['authkey'])) ? $_GET['authkey'] : NULL;

$output .= "<div class=\"top\">";

$sql = new SQL;
$sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$query = $sql->query("SELECT * FROM mm_account WHERE username = '$username' AND authkey = '$authkey'");

$lang_verify = lang_verify();

if ($sql->num_rows($query) < 1)
    $output .= "<h1><font class=\"error\">{$lang_verify['verify_failed']}</font></h1>";
else
{
    $output .= "<h1><font class=\"error\">{$lang_verify['verify_success']}</font></h1>";
    $sql2 = new SQL;
    $sql2->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $data = mysql_fetch_array($query);
    list($id,$username,$pass,$mail,$joindate,$last_ip,$failed_logins,$locked,$last_login,$expansion) = $data;
    $sql2->query("INSERT INTO account (id,username,sha_pass_hash,email, joindate,last_ip,failed_logins,locked,last_login,expansion) VALUES ('',UPPER('$username'),'$pass','$mail',now(),'$last_ip','0','$locked',NULL,'$expansion')");
    $result = $sql2->query("SELECT * FROM account WHERE username='$username'");
    $data = mysql_fetch_assoc($result);
    $sql2->query("INSERT INTO account_access (`id`,`SecurityLevel`) VALUES ('{$data['id']}','0')");

}

$sql->query("DELETE FROM mm_account WHERE username='$username'");

$output .= "</div>";
$output .= "<center><br /><table class=\"hidden\"><tr><td>".makebutton($lang_global['home'], 'index.php', 130)."</td></tr></table></center>";

require_once("footer.php");
?>
