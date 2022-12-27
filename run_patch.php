<?php

// page header, and any additional required libraries
require_once 'header.php';
// minimum permission to view page
valid_login($action_permission['delete']);

//#####################################################################################################
// DO UPLOAD/SUBMIT PATCH
//#####################################################################################################
function print_upload()
{
    global $output, $lang_run_patch, $realm_db, $world_db, $characters_db, $mmfpm_db;

    if (isset($_FILES['uploaded_file']['name']))
    {
        if ('application/octet-stream' == $_FILES['uploaded_file']['type'] || 'text/plain' == $_FILES['uploaded_file']['type']);
        else
            error($lang_run_patch['run_sql_file_only'].'<br />'. $_FILES['uploaded_file']['type']);
        if (file_exists($_FILES['uploaded_file']['tmp_name']))
            $buffer = implode('', file($_FILES['uploaded_file']['tmp_name']));
        else
            error($lang_run_patch['file_not_found']);
    }
    else
        $buffer = '';

    $upload_max = ini_get('upload_max_filesize');
    if (preg_match('/([0-9]+)K/i', $upload_max, $tempregs))
        $upload_max=$tempregs[1]*1024;
    if (preg_match('/([0-9]+)M/i', $upload_max, $tempregs))
        $upload_max=$tempregs[1]*1024*1024;

    $post_max = ini_get('post_max_size');
    if (preg_match('/([0-9]+)K/i', $post_max, $tempregs))
        $post_max=$tempregs[1]*1024;
    if (preg_match('/([0-9]+)M/i', $post_max, $tempregs))
        $post_max=$tempregs[1]*1024*1024;
    // sanity check- a single upload should not be more than 50% the size limit of the total post
    $post_max = $post_max /2;
    $upload_max_filesize = ($upload_max < $post_max) ? $upload_max : $post_max;
    unset($upload_max);
    unset($post_max);

    $output .= '
                <center>
                    '.$lang_run_patch['select_sql_file'].' :<br />
                    '.$lang_run_patch['max_filesize'].' '.$upload_max_filesize.' bytes('.round ($upload_max_filesize/1024/1024).' Mbytes)<br />
                    <table class="hidden">
                        <tr>
                            <td>
                                <form enctype="multipart/form-data" action="run_patch.php?action=print_upload" method="post" name="form">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="'.$upload_max_filesize.'" />
                                    <input type="file" name="uploaded_file" />
                                </form>
                            </td>
                            <td>';

    makebutton($lang_run_patch['open'], 'javascript:do_submit()', 130);
    unset($upload_max_filesize);

    $output .= '
                            </td>
                        </tr>
                    </table>
                <hr />
                <form action="run_patch.php?action=do_run_patch" method="post" name="form1">
                    <table class="hidden">
                        <tr>
                            <td align="left">'.$lang_run_patch['run_rules'].'</td>
                            <td align="right">'.$lang_run_patch['select_db'].':
                                <select name="use_db">';
    foreach ($world_db as $db)
        $output .= '
                                    <option value="'.$db['name'].'">'.$db['name'].'</option>';
    foreach ($characters_db as $db)
        $output .= '
                                    <option value="'.$db['name'].'">'.$db['name'].'</option>';
    unset($db);
    $output .= '
                                    <option value="'.$realm_db['name'].'">'.$realm_db['name'].'</option>
                                    <option value="'.$mmfpm_db['name'].'">'.$mmfpm_db['name'].'</option>
                                </select>
                            </td>
                        </tr>
                        <tr><td colspan="2"><textarea name="query" rows="26" cols="97">'.$buffer.'</textarea></td></tr>
                        <tr>
                            <td colspan="2">';
    makebutton($lang_run_patch['run_sql'], 'javascript:do_submit(\'form1\',0)" type="wrn', 130);
    unset($buffer);
    $output .= '
                            </td>
                        </tr>
                    </table>
                </form>
                </center>';
}

//#####################################################################################################
// DO Run the Query line by line
//#####################################################################################################
function do_run_patch()
{
    global $output, $lang_run_patch, $world_db, $realm_db, $characters_db, $mmfpm_db;

    if (empty($_POST['query']) || empty($_POST['use_db']))
        redirect('run_patch.php?error=1');

    $sql = new SQL;
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $use_db = $sql->quote_smart($_POST['use_db']);
    $query = $_POST['query'];

    if ($use_db == $realm_db['name'])
        $sql->db($realm_db['name']);
    elseif ($use_db == $mmfpm_db['name'])
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
    else
    {
        foreach ($world_db as $db)
            if ($use_db == $db['name'])
                $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
        foreach ($characters_db as $db)
            if ($use_db == $db['name'])
                $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
    }
    unset($use_db);

    $new_queries = [];
    $line = 0;

    $queries = explode("\n",$query);
    unset($query);
    $n_queries = count($queries);
    for($i=0; $i<$n_queries; ++$i)
    {
        $queries[$i] = trim($queries[$i]);
        if ( 0 === strpos ($queries[$i], '#') || 0 === strpos ($queries[$i], '--') )
            ++$line;
        else
            array_push($new_queries, $queries[$i]);
    }
    unset($n_queries);
    $qr=split(";\n",implode("\n",$new_queries));
    unset($new_queries);

    $good = 0;
    $bad = 0;
    foreach($qr as $qry)
    {
        ++$line;
        if (trim($qry))
            ($sql->query(trim($qry)) ? ++$good : ++$bad);
        if ($bad)
        {
            $err = preg_replace ('\n',   '',$sql->error());
            $err = preg_replace ('\r\n$','',$err);
            $err = preg_replace ('\r$',  '',$err);
            error($lang_run_patch['err_in_line'].': '.$line.' <br />'.$err);
            exit();
        }
    }
    unset($qry);
    unset($qr);
    unset($line);
    unset($bad);

    if ($queries)
        redirect('run_patch.php?error=2&tot='.$good.'');
    else
        redirect('run_patch.php?error=3');
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
        <div class="top">';

// load language
$lang_run_patch = lang_run_patch();

// defines the title header in error cases
// if else is always faster then switch case
if (1 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_global['empty_fields'].'</font>
            </h1>';
elseif (2 == $err)
{
    if(isset($_GET['tot']))
        $tot = $_GET['tot'];
    else
        $tot = NULL;
    $output .= '
            <h1>
                <font class="error">'.$tot.' '.$lang_run_patch['query_executed'].'</font>
            </h1>';
    unset($tot);
}
elseif (3 == $err)
    $output .= '
            <h1>
                <font class="error">'.$lang_run_patch['no_query_found'].'</font>
            </h1>';
else
    $output .= '
            <h1>'.$lang_run_patch['run_patch'].'</h1>';
unset($err);

$output .= '
        </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// if else is always faster then switch case
if ('do_run_patch' == $action)
    do_run_patch();
else
    print_upload();

unset($action);
unset($action_permission);
unset($lang_run_patch);

require_once 'footer.php';

?>
