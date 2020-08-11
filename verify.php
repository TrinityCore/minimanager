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

$query = $sql->query("SELECT id,username,salt,verifier,email,joindate,last_iplocked,expansion FROM mm_account WHERE username = '$username' AND authkey = '$authkey'");
list($id,$username,$salt,$verifier,$mail,$joindate,$last_ip,$locked,$expansion) = $sql->fetch_array($query);

$lang_verify = lang_verify();

if ($sql->num_rows($query) > 0)
{
    $output .= "<h1><font class=\"error\">{$lang_verify['verify_success']}</font></h1>";
    
    $sql2 = new SQL;
    $sql2->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $sql2->query("INSERT INTO account (id,username,salt,verifier,email, joindate,last_ip,locked,expansion) VALUES ('',UPPER('$username'),UNHEX('".bin2hex($salt)".'),UNHEX('".bin2hex($verifier)."'),'$mail',now(),'$last_ip','$locked','$expansion')");
    $sql->query("DELETE FROM mm_account WHERE id=".$id);
}
else
    $output .= "<h1><font class=\"error\">{$lang_verify['verify_failed']}</font></h1>";

$output .= "</div>";
$output .= "<center><br /><table class=\"hidden\"><tr><td>".makebutton($lang_global['home'], 'index.php', 130)."</td></tr></table></center>";

require_once("footer.php");
?>
