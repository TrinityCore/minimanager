<?php
    //hide undefined variables in some cases (some errorpages for ex.)
    if (!isset($debug))
        error_reporting(0); // fuck this, we need a real fix

    // level 1 debug prints total queries,
    //  so we would have to close these, or we can't have debug output
    if($debug)
    {
        if (isset($sql))
            $sql->close();
        if (isset($sqlr))
            $sqlr->close();
        if (isset($sqlc))
            $sqlc->close();
        if (isset($sqlm))
            $sqlm->close();
        if (isset($sqlw))
            $sqlw->close();

        // level 3 debug lists all global vars, but can't read classes
        // level 4 debug prints all global arrays, but can't print content of classes
        //  so we would have to close these, or we can't have debug output
        if(2 < $debug)
        {
            unset($sql);
            unset($sqlr);
            unset($sqlc);
            unset($sqlm);
            unset($sqlw);
        }
    }

    // we start with a lead of 10 spaces,
    //  because last line of header is an opening tag with 8 spaces
    //  so if the file before this follows the indent, we will be at the same place it starts
    //  keep html indent in sync, so debuging from browser source would be easy to read
    $output .= '
                <!-- start of footer.php -->
                </div>
                <div id="body_buttom">';
    // show login and register button at bottom of every page if guest mode is activated
    if($allow_anony && empty($_SESSION['logged_in']))
    {
        $lang_login = lang_login();
        $output .= '
                    <center>
                        <table>
                            <tr>
                                <td>
                                    <a class="button" style="width:130px;" href="register.php">Register</a>
                                    <a class="button" style="width:130px;" href="login.php">Login</a>
                                </td>
                            </tr>
                        </table>
                        <br />
                    </center>';
        unset($lang_login);
        unset($allow_anony);
    }
    $output .= '
                    <table class="table_buttom">
                        <tr>
                            <td class="table_buttom_left"></td>
                            <td class="table_buttom_middle">';

    $lang_footer = lang_footer();
    $output .=
                                $lang_footer['bugs_to_admin'].'<a href="mailto:'.$admin_mail.'"> '.$lang_footer['site_admin'].'</a><br />';
    unset($lang_footer);
    unset($admin_mail);
    $output .= sprintf('
                                Execute time: %.5f', (microtime(true) - $time_start));
    unset($time_start);

    // if any debug mode is activated, show memory usage
    if($debug)
    {
        $output .= '
                                Queries: '.$tot_queries.' on '.$_SERVER['SERVER_SOFTWARE'];
        unset($tot_queries);
        if (function_exists('memory_get_usage'))
            $output .= sprintf('
                                <br />Mem. Usage: %.0f/%.0fK Peek: %.0f/%.0fK Global: %.0fK Limit: %s',memory_get_usage()/1024, memory_get_usage(true)/1024,memory_get_peak_usage()/1024,memory_get_peak_usage(true)/1024,sizeof($GLOBALS),ini_get('memory_limit'));
    }

    // links at footer
    $output .= '
                                <p>
                                    <a href="https://www.trinitycore.org/" target="_blank"><img src="img/logo-trinity.png" class="logo_border" alt="trinity" /></a>
                                    <a href="https://www.php.net/" target="_blank"><img src="img/logo-php.png" class="logo_border" alt="php" /></a>
                                    <a href="https://www.mysql.com/" target="_blank"><img src="img/logo-mysql.png" class="logo_border" alt="mysql" /></a>
                                    <a href="https://validator.w3.org/check?uri=referer" target="_blank"><img src="img/logo-css.png" class="logo_border" alt="w3" /></a>
                                    <a href="https://www.spreadfirefox.com" target="_blank"><img src="img/logo-firefox.png" class="logo_border" alt="firefox" /></a>
                                    <a href="https://www.opera.com/" target="_blank"><img src="img/logo-opera.png" class="logo_border" alt="opera" /></a>
                                </p>
                            </td>
                            <td class="table_buttom_right"></td>
                        </tr>
                    </table>
                    <br />';
    echo $output;
    unset($output);
    // we need to close $output before we start debug mode 3 or higher
    //  we will get double output if we don't
    if(2 < $debug)
    {
        echo '
                    <table>
                        <tr>
                            <td align="left">
                                <pre>';
        $arrayObj = new ArrayObject(get_defined_vars());
        for($iterator = $arrayObj->getIterator(); $iterator->valid(); $iterator->next())
        {
            echo '
                                <br />'.$iterator->key() . ' => ' . print_r($iterator->current(), true);
        }
        echo '                                </pre>';
        unset($iterator);
        unset($arrayObj);
        // debug mode 3 lists all global vars and their values, but not for arrays
        // debug mode 4 branches all arrays and their content,
        if(3 < $debug)
        {
            echo '
                                <pre>';
                  print_r ($GLOBALS);
            echo '
                                </pre>';
        }
        echo '
                            </td>
                        </tr>
                    <table>';
    }

?>

        </div>
      </div>
    </center>
  </body>
</html>
