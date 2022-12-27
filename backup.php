<?php


require_once("header.php");
require_once("libs/tab_lib.php");
valid_login($action_permission['read']);

//##############################################################################################
// print backup options step one
//##############################################################################################
function backup_step1()
{
    global $lang_backup, $lang_global, $output;

    $output .= "
        <center>
            <br />
            <fieldset class=\"tquarter_frame\">
                <legend>{$lang_backup['backup_options']}</legend>
                <table class=\"hidden\">
                    <form action=\"backup.php\" method=\"get\" name=\"form\">
                        <tr>
                            <td colspan=\"2\">
                                {$lang_backup['select_option']}:
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type=\"hidden\" name=\"action\" value=\"backup_step2\" />
                                <input type=\"hidden\" name=\"error\" value=\"3\" />
                                <select name=\"backup_action\">
                                    <option value=\"save\">{$lang_backup['save']}</option>
                                    <option value=\"load\">{$lang_backup['load']}</option>
                                </select> - {$lang_backup['to_from']} -
                                <select name=\"backup_from_to\">
                                    <option value=\"web\">{$lang_backup['web_backup']}</option>
                                    <option value=\"file\">{$lang_backup['local_file']}</option>
                                    <option value=\"acc_on_file\">{$lang_backup['acc_on_file']}</option>
                                </select>
                            </td>
                            <td>";

    makebutton($lang_backup['go'], "javascript:do_submit()",130);
    makebutton($lang_global['back'], "javascript:window.history.back()",130);

    $output .= "
                        </tr>
                        <tr>
                            <td colspan=\"2\" align=\"left\"><input type=\"checkbox\" name=\"struc_backup\" value=\"1\" />
                                {$lang_backup['save_table_struc_backup']}.
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"2\" align=\"left\">
                                <input type=\"checkbox\" name=\"save_all_realms\" value=\"1\" checked=\"checked\" />
                                {$lang_backup['save_all_realms']}.
                            </td>
                        </tr>
                    </form>
                </table>
                <br />
            </fieldset>
            <br /><br />
        </center>";

}


//##############################################################################################
// print backup options step two
//##############################################################################################
function backup_step2()
{
    global $lang_backup, $lang_global, $output, $backup_dir, $realm_db, $characters_db;

    if ( empty($_GET['backup_action']) || empty($_GET['backup_from_to'] ))
        redirect("backup.php?error=1");
    else
    {
        $backup_action   = addslashes($_GET['backup_action']);
        $backup_from_to  = addslashes($_GET['backup_from_to']);
        $struc_backup    = (isset($_GET['struc_backup'])) ? addslashes($_GET['struc_backup']) :  0;
        $save_all_realms = (isset($_GET['save_all_realms'])) ? addslashes($_GET['save_all_realms']) : 0;
    }

    $upload_max_filesize=ini_get("upload_max_filesize");
    if (preg_match("/([0-9]+)K/i",$upload_max_filesize,$tempregs))
        $upload_max_filesize=$tempregs[1]*1024;
    if (preg_match("/([0-9]+)M/i",$upload_max_filesize,$tempregs))
        $upload_max_filesize=$tempregs[1]*1024*1024;

    switch ($backup_action)
    {
        case "load":
            $output .= "
        <center>
            <fieldset class=\"tquarter_frame\">
                <legend>{$lang_backup['select_file']}</legend>
                <br />
                <table class=\"hidden\">";

            switch ($backup_from_to)
            {
                case "file":
                    $output .= "
                    <tr>
                        <td colspan=\"2\">
                            {$lang_backup['max_file_size']} : $upload_max_filesize bytes (".round ($upload_max_filesize/1024/1024)." Mbytes)
                            <br />
                            {$lang_backup['use_ftp_for_large_files']}.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form enctype=\"multipart/form-data\" action=\"backup.php?action=dobackup&amp;backup_action=$backup_action&amp;backup_from_to=$backup_from_to\" method=\"post\" name=\"form\">
                                <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$upload_max_filesize\" />
                                <select name=\"use_db\">";
                    foreach ($characters_db as $db)
                        $output .= "
                                    <option value=\"{$db['name']}\">{$db['name']}</option>";

                    $output .= "
                                    <option value=\"{$realm_db['name']}\">{$realm_db['name']}</option>
                                </select>
                                <input type=\"file\" name=\"uploaded_file\" />
                            </form>
                        </td>
                        <td>";
                    makebutton($lang_backup['upload'], "javascript:do_submit()",130);
                    break;

                case "web":
                    $output .= "
                    <tr>
                        <td>
                            <form action=\"backup.php?action=dobackup&amp;backup_action=$backup_action&amp;backup_from_to=$backup_from_to\" method=\"post\" name=\"form\">
                                <select name=\"use_db\">";

                    foreach ($characters_db as $db)
                        $output .= "
                                    <option value=\"{$db['name']}\">{$db['name']}</option>";

                    $output .= "
                                    <option value=\"{$realm_db['name']}\">{$realm_db['name']}</option>
                                </select>
                                <select name=\"selected_file_name\">";
                    if (is_dir($backup_dir))
                    {
                        if ($dh = opendir($backup_dir))
                        {
                            while (($file = readdir($dh)) != false)
                            {
                                if (($file != '.')&&($file != '..')&&($file != '.htaccess')&&($file != 'accounts')&&($file != 'index.html'))
                                    $output .= "
                                    <option value=\"$file\">$file</option>";
                            }
                            closedir($dh);
                        }
                    }

                    $output .= "
                                </select>
                            </form>
                        </td>
                        <td>";

                    makebutton($lang_backup['go'], "javascript:do_submit()",130);
                    break;
                case "acc_on_file":
                    $output .= "
                    <tr>
                        <td colspan=\"2\">
                            {$lang_backup['enter_acc_name']}:
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action=\"backup.php?action=dobackup&amp;backup_action=$backup_action&amp;backup_from_to=$backup_from_to\" method=\"post\" name=\"form\">
                                <select name=\"use_db\">";
                    foreach ($characters_db as $db)
                        $output .= "
                                    <option value=\"{$db['name']}\">{$db['name']}</option>";

                    $output .= "
                                    <option value=\"{$realm_db['name']}\">{$realm_db['name']}</option>
                                </select>
                                <select name=\"file_dir\">";
                    if (is_dir($backup_dir."/accounts"))
                    {
                        if ($dh = opendir($backup_dir."/accounts"))
                        {
                            while (($file = readdir($dh)) != false)
                            {
                                if (($file != '.')&&($file != '..')&&($file != '.htaccess')&&($file != 'index.html'))
                                    $output .= "
                                    <option value=\"$file\">$file</option>";
                            }
                            closedir($dh);
                        }
                    }

                    $output .= "
                                </select>
                                <input type=\"text\" name=\"selected_file_name\" size=\"20\" maxlength=\"35\" />
                            </form>
                        </td>
                        <td>";

                    makebutton($lang_backup['go'], "javascript:do_submit()",80);
                    break;

                default:
            }

            makebutton($lang_global['back'], "javascript:window.history.back()",80);
            $output .= "
                        </td>
                    </tr>
                </table>
                <br /><br />
            </fieldset>
            <br /><br />
        </center>";
            break;

        case "save":
            redirect("backup.php?action=dobackup&backup_action=$backup_action&backup_from_to=$backup_from_to&struc_backup=$struc_backup&save_all_realms=$save_all_realms");
            break;

        default:
            redirect("backup.php?error=1");
    }
}


//##############################################################################################
// DO Backup
//##############################################################################################
function dobackup()
{
    global $lang_backup,$backup_dir, $tables_backup_realmd, $tables_backup_characters, $output, $realm_db, $characters_db, $realm_id, $tab_backup_user_realmd, $tab_backup_user_characters;

    if ( empty($_GET['backup_action']) || empty($_GET['backup_from_to']) )
        redirect("backup.php?error=1");
    else
    {
        $backup_action = addslashes($_GET['backup_action']);
        $backup_from_to = addslashes($_GET['backup_from_to']);
    }

    if (("load" == $backup_action)&&("file" == $backup_from_to))
    {
        if (!preg_match("/(\.(sql|qbquery))$/i",$_FILES["uploaded_file"]["name"]))
            error($lang_backup['upload_sql_file_only']);

        $uploaded_filename=str_replace(" ","_",$_FILES["uploaded_file"]["name"]);
        $uploaded_filename=preg_replace("/[^_A-Za-z0-9-\.]/i",'',$uploaded_filename);
        $file_name_new = $uploaded_filename."_".date("m.d.y_H.i.s").".sql";

        move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], "$backup_dir/$file_name_new") or die (error("{$lang_backup['upload_err_write_permission']} $backup_dir"));
        if (file_exists("$backup_dir/$file_name_new"))
        {
            require_once("libs/db_lib/sql_lib.php");
            $use_db = addslashes($_POST['use_db']);

            if ($use_db == $realm_db['name'])
                $queries = run_sql_script($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name'], "$backup_dir/$file_name_new",true);
            else
            {
                foreach ($characters_db as $db)
                    if ($use_db == $db['name'])
                        $queries = run_sql_script($db['addr'], $db['user'], $db['pass'], $db['name'], "$backup_dir/$file_name_new",true);
            }
            redirect("backup.php?error=4&tot=$queries");
        }
        else
            error($lang_backup['file_not_found']);
    }
    elseif (("load" == $backup_action)&&("web" == $backup_from_to))
    {
        if (empty($_POST['selected_file_name']))
            redirect("backup.php?error=1");
        else
            $file_name = addslashes($_POST['selected_file_name']);

        if (file_exists("$backup_dir/$file_name"))
        {
            require_once("libs/db_lib/sql_lib.php");
            $use_db = addslashes($_POST['use_db']);

            if ($use_db == $realm_db['name'])
                $queries = run_sql_script($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name'], "$backup_dir/$file_name",false);
            else
            {
                foreach ($characters_db as $db)
                if ($use_db == $db['name'])
                    $queries = run_sql_script($db['addr'], $db['user'], $db['pass'], $db['name'], "$backup_dir/$file_name",false);
            }
            redirect("backup.php?error=4&tot=$queries");
        }
        else
            error($lang_backup['file_not_found']);

    }
    elseif (("save" == $backup_action)&&("file" == $backup_from_to))
    {
        //save and send to user
        $struc_backup = addslashes($_GET['struc_backup']);
        $save_all_realms = addslashes($_GET['save_all_realms']);

        if($save_all_realms)
            $temp_id = "all_realms";
        else
            $temp_id = "realmid_".$realm_id;

        $file_name_new = $temp_id."_backup_".date("m.d.y_H.i.s").".sql";

        $fp = fopen("$backup_dir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));

        fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$realm_db['name']};\n")or die (error($lang_backup['file_write_err']));
        fwrite($fp, "USE {$realm_db['name']};\n\n")or die (error($lang_backup['file_write_err']));
        fclose($fp);

        require_once("libs/db_lib/sql_lib.php");

        foreach ($tables_backup_realmd as $value)
        {
            sql_table_dump ($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name'],$value,$struc_backup,"$backup_dir/$file_name_new");
        }

        if($save_all_realms)
        {
            foreach ($characters_db as $db)
            {
                $fp = fopen("$backup_dir/$file_name_new", 'r+') or die (error($lang_backup['file_write_err']));
                fseek($fp,0,SEEK_END);
                fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$db['name']};\n")or die (error($lang_backup['file_write_err']));
                fwrite($fp, "USE {$db['name']};\n\n")or die (error($lang_backup['file_write_err']));
                fclose($fp);

                foreach ($tables_backup_characters as $value)
                    sql_table_dump ($db['addr'], $db['user'], $db['pass'], $db['name'],$value,$struc_backup,"$backup_dir/$file_name_new");
            }
        }
        else
        {
            $fp = fopen("$backup_dir/$file_name_new", 'r+') or die (error($lang_backup['file_write_err']));
            fseek($fp,0,SEEK_END);
            fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$characters_db[$realm_id]['name']};\n")or die (error($lang_backup['file_write_err']));
            fwrite($fp, "USE {$characters_db[$realm_id]['name']};\n\n")or die (error($lang_backup['file_write_err']));
            fclose($fp);

            foreach ($tables_backup_characters as $value)
                sql_table_dump ($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name'],$value,$struc_backup,"$backup_dir/$file_name_new");
        }

        Header("Content-type: application/octet-stream");
        Header("Content-Disposition: attachment; filename=$file_name_new");

        $fp = fopen("$backup_dir/$file_name_new", 'r') or die (error($lang_backup['file_write_err']));
        while (!feof($fp))
        {
            $output_file = fread($fp, 1024);
            echo $output_file;
        }

        fclose($fp);
        unlink("$backup_dir/$file_name_new");
        exit();
    }
    elseif (("save" == $backup_action)&&("web" == $backup_from_to))
    {
        //save backup to web/backup folder
        $struc_backup = addslashes($_GET['struc_backup']);
        $save_all_realms = addslashes($_GET['save_all_realms']);

        $file_name_new = $realm_db['name']."_backup_".date("m.d.y_H.i.s").".sql";
        $fp = fopen("$backup_dir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));

        fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$realm_db['name']};\n")or die (error($lang_backup['file_write_err']));
        fwrite($fp, "USE {$realm_db['name']};\n\n")or die (error($lang_backup['file_write_err']));
        fclose($fp);

        require_once("libs/db_lib/sql_lib.php");

        foreach ($tables_backup_realmd as $value)
            sql_table_dump ($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name'],$value,$struc_backup,"$backup_dir/$file_name_new");

        fclose($fp);

        if($save_all_realms)
        {
            foreach ($characters_db as $db)
            {
                $file_name_new = $db['name']."_backup_".date("m.d.y_H.i.s").".sql";
                $fp = fopen("$backup_dir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));

                fseek($fp,0,SEEK_END);
                fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$db['name']};\n")or die (error($lang_backup['file_write_err']));
                fwrite($fp, "USE {$db['name']};\n\n")or die (error($lang_backup['file_write_err']));
                fclose($fp);

                foreach ($tables_backup_characters as $value)
                    sql_table_dump ($db['addr'], $db['user'], $db['pass'], $db['name'],$value,$struc_backup,"$backup_dir/$file_name_new");

                fclose($fp);
            }
        }
        else
        {
            $file_name_new = $characters_db[$realm_id]['name']."_backup_".date("m.d.y_H.i.s").".sql";
            $fp = fopen("$backup_dir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
            fseek($fp,0,SEEK_END);
            fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$characters_db[$realm_id]['name']};\n")or die (error($lang_backup['file_write_err']));
            fwrite($fp, "USE {$characters_db[$realm_id]['name']};\n\n")or die (error($lang_backup['file_write_err']));
            fclose($fp);

            foreach ($tables_backup_characters as $value)
                sql_table_dump ($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name'],$value,$struc_backup,"$backup_dir/$file_name_new");

            fclose($fp);
        }

        redirect("backup.php?error=2");
        exit();
    }
    elseif (("save" == $backup_action)&&("acc_on_file" == $backup_from_to))
    {
        //save evry account in different file

        $struc_backup = addslashes($_GET['struc_backup']);
        $save_all_realms = addslashes($_GET['save_all_realms']);

        $sql = new SQL;
        $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

        $query = $sql->query("SELECT id FROM account");
        $subdir = "$backup_dir/accounts/".date("m_d_y_H_i_s");
        mkdir($subdir, 0750);


        while ($acc = $sql->fetch_array($query))
        {
            $file_name_new = $acc[0]."_{$realm_db['name']}.sql";
            $fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
            fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$realm_db['name']};\n")or die (error($lang_backup['file_write_err']));
            fwrite($fp, "USE {$realm_db['name']};\n\n")or die (error($lang_backup['file_write_err']));

            $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

            foreach ($tab_backup_user_realmd as $value)
            {
                $acc_query = $sql->query("SELECT * FROM $value[0] WHERE $value[1] = $acc[0]");
                $num_fields = $sql->num_fields($acc_query);
                $numrow = $sql->num_rows($acc_query);

                $result = "-- Dumping data for $value[0] ".date("m.d.y_H.i.s")."\n";
                $result .= "LOCK TABLES $value[0] WRITE;\n";
                $result .= "DELETE FROM $value[0] WHERE $value[1] = $acc[0];\n";

                if ($numrow)
                {
                    $result .= "INSERT INTO $value[0] (";

                    for($count = 0; $count < $num_fields; $count++)
                    {
                        $result .= "`".$sql->field_name($acc_query,$count)."`";
                        if ($count < ($num_fields-1))
                            $result .= ",";
                    }

                    $result .= ") VALUES \n";

                    for ($i =0; $i<$numrow; $i++)
                    {
                        $result .= "\t(";
                        $row = $sql->fetch_row($acc_query);
                        for($j=0; $j<$num_fields; $j++)
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                            if (isset($row[$j]))
                            {
                                if ($sql->field_type($j,$acc_query) == "int")
                                    $result .= "$row[$j]";
                                else
                                    $result .= "'$row[$j]'" ;
                            }
                            else
                                $result .= "''";

                            if ($j<($num_fields-1))
                                $result .= ",";
                        }

                        if ($i < ($numrow-1))
                            $result .= "),\n";
                    }
                    $result .= ");\n";
                }

                $result .= "UNLOCK TABLES;\n";
                $result .= "\n";
                fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
            }
            fclose($fp);

            foreach ($characters_db as $db)
            {
                $file_name_new = $acc[0]."_{$db['name']}.sql";
                $fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
                fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$db['name']};\n")or die (error($lang_backup['file_write_err']));
                fwrite($fp, "USE {$db['name']};\n\n")or die (error($lang_backup['file_write_err']));
                $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
                $all_char_query = $sql->query("SELECT guid, BINARY name AS name FROM `characters` WHERE account = $acc[0]");

                while ($char = $sql->fetch_array($all_char_query))
                {
                    fwrite($fp, "-- Dumping data for character $char[1]\n")or die (error($lang_backup['file_write_err']));
                    foreach ($tab_backup_user_characters as $value)
                    {
                        $char_query = $sql->query("SELECT * FROM $value[0] WHERE $value[1] = $char[0]");
                        $num_fields = $sql->num_fields($char_query);
                        $numrow = $sql->num_rows($char_query);

                        $result = "LOCK TABLES $value[0] WRITE;\n";
                        $result .= "DELETE FROM $value[0] WHERE $value[1] = $char[0];\n";

                        if ($numrow)
                        {
                            $result .= "INSERT INTO $value[0] (";

                            for($count = 0; $count < $num_fields; $count++)
                            {
                                $result .= "`".$sql->field_name($char_query,$count)."`";
                                if ($count < ($num_fields-1))
                                    $result .= ",";
                            }
                            $result .= ") VALUES \n";

                            for ($i =0; $i<$numrow; $i++)
                            {
                                $result .= "\t(";
                                $row = $sql->fetch_row($char_query);

                                for($j=0; $j<$num_fields; $j++)
                                {
                                    $row[$j] = addslashes($row[$j]);
                                    $row[$j] = preg_replace("/\n/","\\n",$row[$j]);

                                    if (isset($row[$j]))
                                    {
                                        if ($sql->field_type($j,$char_query) == "int")
                                            $result .= "$row[$j]";
                                        else
                                            $result .= "'$row[$j]'" ;
                                    }
                                    else
                                        $result .= "''";

                                    if ($j<($num_fields-1))
                                        $result .= ",";

                                }

                                if ($i < ($numrow-1))
                                    $result .= "),\n";
                            }
                            $result .= ");\n";

                        }

                        $result .= "UNLOCK TABLES;\n";
                        $result .= "\n";
                        fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
                    }
                }
                fclose($fp);
            }
        }
        $sql->close();
        unset($sql);
        redirect("backup.php?error=2");
    }
    elseif (("load" == $backup_action)&&("acc_on_file" == $backup_from_to))
    {
        //load saved account
        if ( empty($_POST['selected_file_name'])||empty($_POST['file_dir']))
            redirect("backup.php?error=1");
        else
        {
            $file_name = addslashes($_POST['selected_file_name']);
            $file_dir = addslashes($_POST['file_dir']);
            $use_db = addslashes($_POST['use_db']);
        }

        $file_tmp = "$backup_dir/accounts/$file_dir/".$file_name."_$use_db.sql";
        if (file_exists($file_tmp))
        {
            require_once("libs/db_lib/sql_lib.php");

            if ($use_db == $realm_db['name'])
                $queries = run_sql_script($realm_db['addr'], $realm_db['user'], $realm_db['pass'],$realm_db['name'], "$backup_dir/accounts/$file_dir/$file_name.sql",true);
            else
            {
                foreach ($characters_db as $db)
                    if ($use_db == $db['name'])
                        $queries = run_sql_script($db['addr'], $db['user'], $db['pass'],$db['name'], "$backup_dir/accounts/$file_dir/$file_name.sql",true);
            }

            redirect("backup.php?error=4&tot=$queries");
        }
        else
            error($lang_backup['file_not_found']);
    }
    else
    {
        //non of the options = error
        redirect("backup.php?error=1");
    }
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";
switch ($err)
{
    case 1:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_global['empty_fields']}</font>
            </h1>
        </div>";
        break;

    case 2:
        $output .= "
            <h1>
                <font class=\"error\">{$lang_backup['backup_finished']}</font>
            </h1>
        </div>";
        break;

    case 3:
        $output .= "
            <h1>{$lang_backup['select_backup']}</h1>
        </div>";
        break;

    case 4:
        if(isset($_GET['tot']))
            $total_queries = $_GET['tot'];
        else
            $total_queries = NULL;

        $output .= "
            <h1>
                <font class=\"error\">{$lang_backup['file_loaded']} $total_queries {$lang_backup['que_executed']}.</font>
            </h1>
        </div>";
        break;

    default: //no error
        $output .= "
            <h1>{$lang_backup['backup_acc']}</h1>
        </div>";

        $output .= "
        <center>
            <font class=\"large\">{$lang_backup['tables_to_save']}:</font>
            <br />
            <table width=\"700\" class=\"hidden\">
                <tr>
                    <td>";

        foreach ($tables_backup_realmd as $value)
            $output .= "
                        {$realm_db['name']}.$value / ";
        foreach ($tables_backup_characters as $value)
            $output .= " $value / ";
        $output .= "
                    </td>
                </tr>
            </table>
        </center>";
}


$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
    case "backup_step2":
        backup_step2();
        break;

    case "dobackup":
        dobackup();
        break;

    default:
        backup_step1();
}

include_once("footer.php");

?>
