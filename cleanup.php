<?php


require_once("header.php");

// override default security : guild.php (guild leader deletes guild)
if(!isset($_POST['override']) || $_POST['override'] != '1')
    valid_login($action_permission['delete']);


//#################################################################################################################
// print cleanup options
//#################################################################################################################
function cleanup() {
    global $lang_cleanup,$lang_global,$output;

    $output .= "
                            <center>
                                <fieldset style=\"width: 740px;\">
                                    <legend>{$lang_cleanup['cleanup_options']}</legend>
                                    <table class=\"hidden\">
                                    <tr>
                                        <td>
                                            <form action=\"cleanup.php\" method=\"get\" name=\"form\">
                                                <input type=\"hidden\" name=\"action\" value=\"run_cleanup\" />
                                                <select name=\"cleanup_by\" title='{$lang_cleanup['cleanup_by']}'>
                                                    <optgroup label=\"{$lang_cleanup['clean_chars']}\">
                                                        <option value=\"char_lvl\">{$lang_cleanup['char_level']}</option>
                                                        <option value=\"totaltime\">{$lang_cleanup['tot_play_time']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"{$lang_cleanup['clean_acc']}\">
                                                        <option value=\"last_login\">{$lang_cleanup['last_login_time']}</option>
                                                        <option value=\"failed_login\">{$lang_cleanup['failed_logins']}</option>
                                                        <option value=\"banned\">{$lang_cleanup['banned']}</option>
                                                        <option value=\"locked\">{$lang_cleanup['locked']}</option>
                                                        <option value=\"num_of_char_in_acc\">{$lang_cleanup['chars_in_acc']}</option>
                                                    </optgroup>
                                                    <optgroup label=\"{$lang_cleanup['clean_guilds']}\">
                                                        <option value=\"num_of_char_in_guild\">{$lang_cleanup['chars_in_guild']}</option>
                                                    </optgroup>
                                                </select>
                                                <select name=\"cleanup_sign\" title='Cleanup operator'>
                                                    <option value=\"=\">=</option>
                                                    <option value=\"<\"><</option>
                                                    <option value=\"<=\"><=</option>
                                                    <option value=\">\">></option>
                                                    <option value=\">=\">>=</option>
                                                    <option value=\"!=\">!=</option>
                                                </select>
                                                <input type=\"text\" size=\"25\" maxlength=\"40\" name=\"cleanup_value\" title='Cleanup parameter' />
                                        </td>
                                        <td>";

    makebutton($lang_cleanup['run_cleanup'], "javascript:do_submit()",100);
    makebutton($lang_global['back'], "javascript:window.history.back()",100);

    $output .= "                        </td>
                                    </tr>
                                </table>
                                <br />
                            </fieldset>
                            <br /><br />
                        </center>";
}


//########################################################################################################
// make and list list of all acc/chars
//########################################################################################################
function run_cleanup() {
    global $lang_cleanup, $lang_global, $output, $realm_db, $characters_db, $realm_id, $user_lvl;

    if( empty($_GET['cleanup_by']) || empty($_GET['cleanup_sign']) )
        redirect("cleanup.php?error=1");

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $cleanup_by = $sql->quote_smart($_GET['cleanup_by']);
    $cleanup_sign = $sql->quote_smart($_GET['cleanup_sign']);
    $cleanup_value = $sql->quote_smart($_GET['cleanup_value']);

    switch ($cleanup_by)
    {
        // clean by lvl
        case "char_lvl":
            $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

            $result = $sql->query("SELECT guid FROM `characters` WHERE level $cleanup_sign $cleanup_value");
            $total_chars = $sql->num_rows($result);

            $output .= "
                            <center>";
            if ($total_chars)
            {
                $output .= "
                                <h1>
                                    <font class=\"error\">{$lang_global['are_you_sure']}</font>
                                </h1>
                                <br />";
            $output .= "
                                <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                    <input type=\"hidden\" name=\"type\" value=\"char\" />
                                    <font class=\"bold\">{$lang_cleanup['chars_id']}: ";

            $pass_array = "";

            while($char = $sql->fetch_row($result))
            {
                $output .= "
                                        <a href=\"char.php?id=$char[0]\" target=\"_blank\">$char[0], </a>";
                $pass_array .= "-$char[0]";
            }

            $output .= "
                                        <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                        <br />{$lang_cleanup['tot_of']} $total_chars {$lang_global['will_be_erased']}
                                    </font>
                                    <br /><br />
                                    <table class=\"hidden\">
                                        <tr>
                                            <td>";

            makebutton($lang_global['yes'], "javascript:do_submit()",120);
            makebutton($lang_global['no'], "cleanup.php",120);

            $output .= "
                                            </td>
                                        </tr>
                                    </table>
                                </form>";
            }
            else
            {
                $output .= "
                                <h1>
                                    <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                                </h1>
                                <br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>";
            }
            $output .= "
                            </center><br />";
            break;


            //last loggin
        case "last_login":
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

            $result = $sql->query("SELECT account.id FROM account left join account_access on account.id = account_access.AccountID  WHERE account.last_login $cleanup_sign '$cleanup_value' AND account_access.SecurityLevel < $user_lvl OR account.last_login $cleanup_sign '$cleanup_value' AND account_access.SecurityLevel IS NULL");
            $total_accounts = $sql->num_rows($result);

            $output .= "
                            <center>";
            if ($total_accounts)
            {
                $output .= "
                                <h1>
                                    <font class=\"error\">{$lang_global['are_you_sure']}</font>
                                </h1>
                                <br />
                                <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                    <input type=\"hidden\" name=\"type\" value=\"acc\" />
                                    <font class=\"bold\">{$lang_cleanup['acc_ids']}: ";

                $pass_array = "";

                while($acc = $sql->fetch_row($result))
                {
                    $output .= "
                                        <a href=\"user.php?action=edit_user&amp;id=$acc[0]\" target=\"_blank\">$acc[0], </a>";
                    $pass_array .= "-$acc[0]";
                }

                $output .= "
                                        <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                        <br />{$lang_cleanup['tot_of']} $total_accounts {$lang_global['will_be_erased']}
                                    </font>
                                    <br /><br />
                                    <table class=\"hidden\">
                                        <tr>
                                            <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                            </td>
                                        </tr>
                                    </table>
                                </form>";
            }
            else
            {
                $output .= "
                                <h1>
                                    <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                                </h1>
                                <br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>";
            }
            $output .= "
                            </center>
                            <br />";
            break;

        //failed loggin attempts
        case "failed_login":
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

            $result = $sql->query("SELECT account.id FROM account left join account_access on account.id = account_access.AccountID  WHERE account.failed_logins $cleanup_sign '$cleanup_value' AND account_access.SecurityLevel < $user_lvl OR account.failed_logins $cleanup_sign '$cleanup_value' AND account_access.SecurityLevel IS NULL");
            $total_accounts = $sql->num_rows($result);

            $output .= "
                        <center>";
            if ($total_accounts)
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['are_you_sure']}</font>
                            </h1>
                            <br />
                            <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"type\" value=\"acc\" />
                                <font class=\"bold\">{$lang_cleanup['acc_ids']}: ";

                $pass_array = "";

                while($acc = $sql->fetch_row($result))
                {
                    $output .= "
                                    <a href=\"user.php?action=edit_user&amp;id=$acc[0]\" target=\"_blank\">$acc[0], </a>";
                    $pass_array .= "-$acc[0]";
                }

                $output .= "
                                    <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                    <br />{$lang_cleanup['tot_of']} $total_accounts {$lang_global['will_be_erased']}
                                </font>
                                <br /><br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>";
            }
            else
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                            </h1>
                            <br />
                            <table class=\"hidden\">
                                <tr>
                                    <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                    </td>
                                </tr>
                            </table>";
            }
            $output .= "
                        </center>
                        <br />";
            break;

        //clean banned accounts
        case "banned":
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

            $result = $sql->query("SELECT id FROM account_banned");
            $total_accounts = $sql->num_rows($result);

            $output .= "
                        <center>";
            if ($total_accounts)
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['are_you_sure']}</font>
                            </h1>
                            <br />
                            <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"type\" value=\"acc\" />
                                <font class=\"bold\">{$lang_cleanup['acc_ids']}: ";

                $pass_array = "";

                while($acc = $sql->fetch_row($result))
                {
                    $output .= "
                                    <a href=\"user.php?action=edit_user&amp;id=$acc[0]\" target=\"_blank\">$acc[0], </a>";
                    $pass_array .= "-$acc[0]";
                }

                $output .= "
                                    <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                    <br />{$lang_cleanup['tot_of']} $total_accounts {$lang_global['will_be_erased']}
                                </font>
                                <br /><br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>";
            }
            else
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                            </h1>
                            <br />
                            <table class=\"hidden\">
                                <tr>
                                    <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                    </td>
                                </tr>
                            </table>";
            }
            $output .= "
                        </center>
                        <br />";
            break;

        //clean chars with given total time played
        case "totaltime":
            $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

            $result = $sql->query("SELECT guid FROM `characters` WHERE totaltime $cleanup_sign $cleanup_value");
            $total_chars = $sql->num_rows($result);

            $output .= "
                        <center>";
            if ($total_chars)
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['are_you_sure']}</font>
                            </h1>
                            <br />
                            <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"type\" value=\"char\" />
                                <font class=\"bold\">{$lang_cleanup['chars_id']}: ";

                $pass_array = "";

                while($char = $sql->fetch_row($result))
                {
                    $output .= "
                                    <a href=\"char.php?id=$char[0]\" target=\"_blank\">$char[0], </a>";
                    $pass_array .= "-$char[0]";
                }

                $output .= "
                                    <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                    <br />{$lang_cleanup['tot_of']} $total_chars {$lang_global['will_be_erased']}
                                </font>
                                <br /><br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>";
            }
            else
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                            </h1>
                            <br />
                            <table class=\"hidden\">
                                <tr>
                                    <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                    </td>
                                </tr>
                            </table>";
            }
            $output .= "
                        </center>
                        <br />";
            break;


        //clean locked acc
        case "locked":
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

            $result = $sql->query("SELECT account.id FROM account left join account_access on account.id = account_access.AccountID  WHERE account.locked $cleanup_sign '$cleanup_value' AND account_access.SecurityLevel < $user_lvl OR account.locked $cleanup_sign '$cleanup_value' AND account_access.SecurityLevel IS NULL");
            $total_accounts = $sql->num_rows($result);

            $output .= "
                        <center>";
            if ($total_accounts)
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['are_you_sure']}</font>
                            </h1>
                            <br />
                            <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"type\" value=\"acc\" />
                                <font class=\"bold\">{$lang_cleanup['acc_ids']}: ";

                $pass_array = "";

                while($acc = $sql->fetch_row($result))
                {
                    $output .= "
                                    <a href=\"user.php?action=edit_user&amp;id=$acc[0]\" target=\"_blank\">$acc[0], </a>";
                    $pass_array .= "-$acc[0]";
                }

                $output .= "
                                    <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                    <br />{$lang_cleanup['tot_of']} $total_accounts {$lang_global['will_be_erased']}
                                </font>
                                <br /><br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>";
            }
            else
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                            </h1>
                            <br />
                            <table class=\"hidden\">
                                <tr>
                                    <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                    </td>
                                </tr>
                            </table>";
            }
            $output .= "
                        </center>
                        <br />";
            break;


        //accounts without chars or specified number of chars
        case "num_of_char_in_acc":
            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

            $result = $sql->query("SELECT account.id FROM account left join account_access on account.id = account_access.AccountID  WHERE account_access.SecurityLevel < $user_lvl OR account_access.SecurityLevel IS NULL");
            $acc_output_array = [];

            while($acc = $sql->fetch_row($result))
            {
                $total_chars_in_acc = 0;
                foreach ($characters_db as $db)
                {
                    $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);

                    $query = $sql->query("SELECT count(*) FROM `characters` WHERE account = '$acc[0]'");
                    $total_chars_in_acc = $total_chars_in_acc + $sql->result($query, 0);
                }

                switch ($cleanup_sign)
                {
                    case "=":
                        if($total_chars_in_acc == $cleanup_value)
                            array_push($acc_output_array, $acc[0]);
                        break;
                    case "<":
                        if($total_chars_in_acc < $cleanup_value)
                            array_push($acc_output_array, $acc[0]);
                        break;
                    case "<=":
                        if($total_chars_in_acc <= $cleanup_value)
                            array_push($acc_output_array, $acc[0]);
                        break;
                    case ">":
                        if($total_chars_in_acc > $cleanup_value)
                            array_push($acc_output_array, $acc[0]);
                        break;
                    case ">=":
                        if($total_chars_in_acc >= $cleanup_value)
                            array_push($acc_output_array, $acc[0]);
                        break;
                    case "!=":
                        if($total_chars_in_acc <> $cleanup_value)
                            array_push($acc_output_array, $acc[0]);
                        break;
                    default:
                        redirect("cleanup.php?error=1");
                }
            }

            $output .= "
                        <center>";
            if ($acc_output_array)
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['are_you_sure']}</font>
                            </h1>
                            <br />
                            <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"type\" value=\"acc\" />
                                <font class=\"bold\">{$lang_cleanup['acc_ids']}: ";

                $pass_array = "";

                for ($i = 0; $i < count($acc_output_array); $i++)
                {
                    $output .= "
                                    <a href=\"user.php?action=edit_user&amp;id=$acc_output_array[$i]\" target=\"_blank\">$acc_output_array[$i], </a>";
                    $pass_array .= "-$acc_output_array[$i]";
                }

                $output .= "
                                    <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                    <br />{$lang_cleanup['tot_of']} ".count($acc_output_array)." {$lang_global['will_be_erased']}
                                </font>
                                <br /><br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>";
            }
            else
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                            </h1>
                            <br />
                            <table class=\"hidden\">
                                <tr>
                                    <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                    </td>
                                </tr>
                            </table>";
            }
            $output .= "
                        </center>
                        <br />";
            break;


        //guild  without chars or specified number of chars
        case "num_of_char_in_guild":
            $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

            $result = $sql->query("SELECT guildid FROM guild");

            $guild_output_array = [];

            while($guild = $sql->fetch_row($result))
            {
                $query = $sql->query("SELECT count(*) FROM guild_member WHERE guildid = '$guild[0]'");
                $total_chars_in_guild = $sql->result($query, 0);

                switch ($cleanup_sign)
                {
                    case "=":
                        if($total_chars_in_guild == $cleanup_value)
                            array_push($guild_output_array, $guild[0]);
                        break;
                    case "<":
                        if($total_chars_in_guild < $cleanup_value)
                            array_push($guild_output_array, $guild[0]);
                        break;
                    case "<=":
                        if($total_chars_in_guild <= $cleanup_value)
                            array_push($guild_output_array, $guild[0]);
                        break;
                    case ">":
                        if($total_chars_in_guild > $cleanup_value)
                            array_push($guild_output_array, $guild[0]);
                        break;
                    case ">=":
                        if($total_chars_in_guild >= $cleanup_value)
                            array_push($guild_output_array, $guild[0]);
                        break;
                    case "!=":
                        if($total_chars_in_guild <> $cleanup_value)
                            array_push($guild_output_array, $guild[0]);
                        break;
                    default:
                        redirect("cleanup.php?error=1");
                }
            }

            $output .= "
                        <center>";
            if ($guild_output_array)
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['are_you_sure']}</font>
                            </h1>
                            <br />
                            <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"type\" value=\"guild\" />
                                <font class=\"bold\">{$lang_cleanup['guilds_id']}: ";

                $pass_array = "";

                for ($i=0; $i < count($guild_output_array); $i++)
                {
                    $output .= "
                                    <a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$guild_output_array[$i]\" target=\"_blank\">$guild_output_array[$i], </a>";
                    $pass_array .= "-$guild_output_array[$i]";
                }

                $output .= "
                                    <input type=\"hidden\" name=\"check\" value=\"$pass_array\" />
                                    <br />{$lang_cleanup['tot_of']} ".count($guild_output_array)." {$lang_global['will_be_erased']}
                                </font>
                                <br /><br />
                                <table class=\"hidden\">
                                    <tr>
                                        <td>";

                makebutton($lang_global['yes'], "javascript:do_submit()",120);
                makebutton($lang_global['no'], "cleanup.php",120);

                $output .= "
                                        </td>
                                    </tr>
                                </table>
                            </form>";
            }
            else
            {
                $output .= "
                            <h1>
                                <font class=\"error\">{$lang_global['err_no_records_found']}</font>
                            </h1>
                            <br />
                            <table class=\"hidden\">
                                <tr>
                                    <td>";

                makebutton($lang_global['go_back'], "cleanup.php",120);

                $output .= "
                                    </td>
                                </tr>
                            </table>";
            }
            $output .= "
                        </center>
                        <br />";
            break;

        default:
            redirect("Location: cleanup.php?error=1");
    }
    $sql->close();
    unset($sql);
}


//################################################################################################
// DO CLEANUP
//################################################################################################
function docleanup(){
    global $lang_cleanup, $lang_global, $output, $realm_db, $characters_db, $realm_id, $user_lvl,
            $tab_del_user_characters, $tab_del_user_realmd;

    if (!isset($_POST['type']) || $_POST['type'] === '')
        redirect("cleanup.php?error=1");

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $type = $sql->quote_smart($_POST['type']);
    if(isset($_POST['check']) && $_POST['check'] != '')
    {
        $check = $sql->quote_smart($_POST['check']);
        $check = explode('-',$check);
    }
    else
        redirect("cleanup.php?error=1");

    $deleted_acc = 0;
    $deleted_chars = 0;
    $deleted_gulds = 0;

    require_once("./libs/del_lib.php");

    switch ($type)
    {
        //we deleting account array
        case "acc":
            for ($i = 1; $i < count($check); $i++)
            {
                if ($check[$i] != "" )
                {
                    list($flag,$del_char) = del_acc($check[$i]);
                    if ($flag)
                    {
                        $deleted_acc++;
                        $deleted_chars += $del_char;
                    }
                }
            }
        break;

        //we deleting character array
        case "char":
            for ($i = 1; $i < count($check); $i++)
            {
                if ($check[$i] != "" )
                {
                    if (del_char($check[$i], $realm_id))
                        $deleted_chars++;
                }
            }
        break;

        //cleaning guilds
        case "guild":
            for ($i = 1; $i < count($check); $i++)
            {
                if ($check[$i] != "" )
                {
                    if (del_guild($check[$i], $realm_id))
                        $deleted_gulds++;
                }
            }
        break;

        //cleaning arena teams
        case "arenateam":
            for ($i = 1; $i < count($check); $i++)
            {
                if ($check[$i] != "" )
                {
                    if (del_arenateam($check[$i], $realm_id))
                        $deleted_arenateams++;
                }
            }
        break;

        default:
            redirect("cleanup.php?error=1");
    }

    $sql->close();
    unset($sql);

    $output .= "
                <center>";
    if ($type == "guild")
    {
        if (!$deleted_gulds)
            $output .= "
                    <h1>
                        <font class=\"error\">{$lang_cleanup['no_guilds_del']}</font>
                    </h1>";
        else
            $output .= "
                    <h1>
                        <font class=\"error\">
                            {$lang_cleanup['total']} <font color=blue>$deleted_gulds</font> {$lang_cleanup['guilds_deleted']}
                        </font>
                    </h1>";
    }
    else
    {
        if ($type == "arenateam")
        {
            if (!$deleted_arenateams)
                $output .= "
                    <h1><font class=\"error\">{$lang_cleanup['no_arenateams_del']}</font>
                    </h1>";
            else
                $output .= "
                    <h1>
                        <font class=\"error\">
                            {$lang_cleanup['total']} <font color=blue>$deleted_arenateams</font> {$lang_cleanup['arenateams_deleted']}
                        </font>
                    </h1>";
        }
        else
        {
            if (($deleted_acc+$deleted_chars) == 0)
                $output .= "
                    <h1>
                        <font class=\"error\">{$lang_cleanup['no_acc_chars_deleted']}</font>
                    </h1>";
            else
            {
                $output .= "
                    <h1>
                        <font class=\"error\">
                            {$lang_cleanup['total']} <font color=blue>$deleted_acc</font> {$lang_cleanup['accs_deleted']}
                        </font>
                    </h1>
                    <br />
                    <h1>
                        <font class=\"error\">
                            {$lang_cleanup['total']} <font color=blue>$deleted_chars</font> {$lang_cleanup['chars_deleted']}
                        </font>
                    </h1>";
            }
        }
    }
    $output .= "
                    <br /><br />
                    <table class=\"hidden\">
                        <tr>
                            <td>";

    makebutton($lang_cleanup['back_cleaning'], "cleanup.php", 200);

    $output .= "
                            </td>
                        </tr>
                    </table>
                    <br />
                </center>";
}



//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";
switch ($err) {
    case 1:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_global['empty_fields']}</font>
            </h1>";
        break;
    default: //no error
        $output .= "
            <h1>{$lang_cleanup['clean_db']}</h1>";
}
$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
    case "run_cleanup":
        run_cleanup();
        break;
    case "docleanup":
        docleanup();
        break;
    default:
        cleanup();
}

require_once("footer.php");
?>
