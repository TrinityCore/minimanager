<?php

// page header, and any additional required libraries
require_once 'header.php';
// we get the error message which was passed to us
// ERROR_MSG is built server side and may hold markup; the query string never may
$err = (isset($_GET['err'])) ? htmlspecialchars($_GET['err'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : ((defined('ERROR_MSG')) ? ERROR_MSG : 'Oopsy...');

//  we start with a lead of 10 spaces,
//  because last line of header is an opening tag with 8 spaces
//  keep html indent in sync, so debuging from browser source would be easy to read
$output .= '
                <!-- start of error.php -->
                <center>
                <br />
                    <table width="400" class="flat">
                        <tr>
                            <td align="center">
                                <h1>
                                    <font class="error">
                                        <img src="img/warn_red.gif" width="48" height="48" alt="error" />
                                        <br />ERROR!
                                    </font>
                                </h1>
                                <br />'.$err.'<br />
                            </td>
                        </tr>
                    </table>
                <br />
                <table width="300" class="hidden">
                    <tr>
                        <td align="center">';

makebutton($lang_global['home'], 'index.php', 130);
makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
unset($err);

$output .= '
                        </td>
                    </tr>
                </table>
                <br />
                </center>
                <!-- end of error.php -->';

require_once 'footer.php';

?>
