<?php

require_once 'header.php';
require_once 'libs/telnet_lib.php';
valid_login($action_permission['insert']);

function main()
{
    global $output, $lang_global, $lang_message;

    $output .= '
                <div class="top"><h1>'.$lang_message['main'].'</h1></div>
                <center>
                    <form action="message.php?action=send" method="post" name="form">
                        <table class="top_hidden">
                            <tr>
                                <td align="center">
                                    Send :
                                    <select name="type">
                                        <option value="1" selected="selected">'.$lang_message['announcement'].'</option>
                                        <option value="2">'.$lang_message['notification'].'</option>
                                        <option value="3">'.$lang_message['both'].'</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <textarea id="msg" name="msg" rows="26" cols="80"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <table align="center" class="hidden">
                                        <tr>
                                            <td>';

    makebutton($lang_message['send'], 'javascript:do_submit()" type="wrn', 130);
    $output .= '
                                            </td>
                                            <td>';

    makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
    $output .= '
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>';
}

function check()
{
    global $output, $lang_telnet, $realm_id, $server;

    $telnet = new telnet_lib();
    $result = $telnet->Connect($server[$realm_id]['addr'], $server[$realm_id]['telnet_port'], $server[$realm_id]['telnet_user'], $server[$realm_id]['telnet_pass']);

    if (0 == $result)
    {
        $telnet->Disconnect();
        redirect('message.php?action=main');
    }
    elseif (1 == $result)
        $mess_str = $lang_telnet['unable'];
    elseif (2 == $result)
        $mess_str = $lang_telnet['unknown_host'];
    elseif (3 == $result)
        $mess_str = $lang_telnet['login_failed'];
    elseif (4 == $result)
        $mess_str = $lang_telnet['not_supported'];

    unset($result);
    unset($telnet);

    redirect('message.php?action=result&mess='.$mess_str.'');
}

function send(&$sqlc)
{
    global $lang_telnet, $lang_message, $realm_id, $server;

    if (empty($_POST['msg'])) redirect('message.php?action=result&mess='.$lang_message['empty_fields'].'');

    $type = (isset($_POST['type'])) ? $sqlc->quote_smart($_POST['type']) : 3;
    if (is_numeric($type));
    else
        $type = 3;

    $msg = $sqlc->quote_smart(($_POST['msg'] ?? ''));
    if (4096 < strlen($msg))
        redirect('message.php?action=result&mess='.$lang_message['message_too_long'].'');

    $telnet = new telnet_lib();
    $result = $telnet->Connect($server[$realm_id]['addr'], $server[$realm_id]['telnet_port'], $server[$realm_id]['telnet_user'], $server[$realm_id]['telnet_pass']);
    if (0 == $result)
    {
        $mess_str = '';
        if ( 2 == $type);
        else
        {
            $telnet->DoCommand('announce '.$msg, $result);
            $mess_str .= ''.$lang_message['system_message'].': "'.$msg.'" '.$lang_message['sent'].'.';
        }
        if ( 3 == $type)
            $mess_str .= '<br /><br />';
        if ( 1 == $type);
        else
        {
            $telnet->DoCommand('notify '.$msg, $result);
            $mess_str .= ''.$lang_message['global_notify'].': "'.$msg.'" '.$lang_message['sent'].'.';
        }
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

    unset($result);
    unset($telnet);
    unset($type);
    unset($msg);

    redirect('message.php?action=result&mess='.$mess_str.'');
}

function result()
{
    global $output, $lang_global, $lang_message;

    $mess = (isset($_GET['mess'])) ? $_GET['mess'] : NULL;

    $output .= '
                <div class="top"><h1>'.$lang_message['message_result'].'</h1></div>
                <center>
                    <table class="top_hidden" width="400">
                        <tr>
                            <td align="center">
                                <br />'.$mess.'<br /><br />';
    unset($mess);
    $output .= '
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table align="center" class="hidden">
                                    <tr>
                                        <td>';
                                            makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
                                            $output .= '
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </center>';
}

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_message = lang_message();
$lang_telnet = lang_telnet();

if ('send' === $action)
    send($sqlc);
elseif ('result' === $action)
    result();
elseif ('main' === $action)
    main();
else
    check();

unset($action);
unset($action_permission);
unset($lang_telnet);
unset($lang_message);

require_once 'footer.php';

?>
