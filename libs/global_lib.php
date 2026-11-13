<?php


//#############################################################################
//global output string - hands off...
$output = '';


//#############################################################################
//to avoid Strict Standards notices in php 5.1
if (function_exists ('date_default_timezone_set'))
{
    date_default_timezone_set($timezone);
}


//#############################################################################
// loading of wowhead tool tip script
function wowhead_tt()
{
    global $output, $tooltip_script;
    $output .='<script type="text/javascript" src="'.$tooltip_script.'"></script>';
}


//#############################################################################
//validates sessions' vars and restricting access to given level
function valid_login($restrict_lvl)
{
    if (isset($_SESSION['user_lvl']) && isset($_SESSION['user_id']) && isset($_SESSION['realm_id']) && isset($_SESSION['uname']))
    {
        $user_lvl = $_SESSION['user_lvl'];
        $ip = ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
        if ($ip === $_SESSION['client_ip']);
        else 
            redirect('login.php');
    }
    else 
        redirect('login.php');

    // a missing threshold (page not found in $menu_array) must deny, not compare against null
    if (!is_numeric($restrict_lvl) || $user_lvl < $restrict_lvl)
    {
        // a guest may still log in with a higher account, a logged in user just gets an error
        if (empty($_SESSION['logged_in']))
            redirect('login.php?error=5');

        // error.php and footer.php run in this scope, give them the globals they read
        global $output, $lang_global, $debug, $allow_anony, $github_repo, $time_start, $tot_queries,
            $sql, $sqlr, $sqlc, $sqlm, $sqlw;
        define('ERROR_MSG', $lang_global['err_no_permission']);
        require 'error.php';
        exit();
    }
}


//#############################################################################
// Fix reditection error under MS-IIS fuckedup-servers.
function redirect($url)
{
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false)
    {
        header('Location: '.$url);
        exit();
    }
    else 
        die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
}


//#############################################################################
// CSRF protection: one random token per session.
// put csrf_field() in every form (or csrf=csrf_token() in action links)
// and call csrf_check() first in every handler that changes data.
function csrf_token()
{
    if (empty($_SESSION['csrf']))
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="'.csrf_token().'" />';
}

function csrf_check()
{
    $token = $_POST['csrf'] ?? $_GET['csrf'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token))
    {
        http_response_code(403);
        exit('Invalid or expired security token. Go back, reload the page and try again.');
    }
}


//#############################################################################
// returns the highest GM level of an account on a realm, 0 if it has none
function get_user_lvl(&$sqlr, $user_id, $realm_id)
{
    return (int)$sqlr->result($sqlr->query('SELECT MAX(SecurityLevel) FROM account_access WHERE AccountID = '.(int)$user_id.' AND (RealmID = '.(int)$realm_id.' OR RealmID = -1)'), 0);
}


//#############################################################################
//redirects to error page with error code
function error($err)
{
    global $output, $lang_global;

    if (ob_get_length()>0) 
        ob_clean(); // clean output buffer
    ob_start();
	
    if (isset($_SESSION['user_lvl']))
		if ($_SESSION['user_lvl'] > 2)
			debug_print_backtrace(); // show this info to user_lvl 3 and above
    $contents = str_replace("\n", '<br /><br />', ob_get_contents()); // catch backtrace
    ob_end_clean();
    define('ERROR_MSG', $err . '<br /><br />' . $contents); // define error
    require('error.php'); // display it
}


//#############################################################################
//testing for open port
function test_port($server,$port)
{
    $sock = @fsockopen($server, $port, $ERROR_NO, $ERROR_STR, (float)0.5);
    if($sock)
    {
        @fclose($sock);
        return true;
    }
    else
        return false;
}


//#############################################################################
function aasort(&$array, $field, $order = false)
{
    $order = ($order ? SORT_ASC : SORT_DESC);
    $colum = array_column($array, $field);
    array_multisort($colum, $order, $array);
}


//#############################################################################
//making buttons - just to make them all look the same
function makebutton($xtext, $xlink, $xwidth)
{
    global $output;
    $output .= '
              <div>
                <a class="button" style="width:'.$xwidth.'px;" href="'.$xlink.'">'.$xtext.'</a>
              </div>';
}


//#############################################################################
//make javascript tooltip
function maketooltip($text, $link, $tip, $class, $target = 'target="_self"')
{
    global $output;
    //COMMENTED OUT SINCE WE WANT WOWHEAD TOOLTIPS ONLY
    //$output .='<a style="padding:2px;" href="$link" $target onmouseover="toolTip(\''.addslashes($tip).'\', \''.$class.'\')" onmouseout="toolTip()">'.$text.'</a>';

    $output .= '<a style="padding:2px;" href="'.$link.'" '.$target.'>'.$text.'</a>';
}


//#############################################################################
// Generate paging navigation.
// Original from PHPBB with some modifications to make them more simple
function generate_pagination($base_url, $num_items, $per_page, $start_item, $start_tag = 'start', $add_prevnext_text = TRUE)
{
    global $lang_global;
    if ($num_items);
    else 
        return '';
        
    $total_pages = ceil($num_items/$per_page);
    if (1 == $total_pages)
        return '';

    $on_page = floor($start_item / $per_page)+1;
    $page_string = '';
    if (10 < $total_pages)
    {
        $init_page_max = (3 < $total_pages) ? 3 : $total_pages;
        $count = $init_page_max+1;
        for($i=1; $i<$count; ++$i)
        {
            $page_string .= ($i == $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
            if ($i < $init_page_max)
                $page_string .= ', ';
        }
        if (3 < $total_pages)
        {
            if (1 < $on_page && $on_page < $total_pages)
            {
                $page_string  .= (5 < $on_page) ? ' ... ' : ', ';
                $init_page_min = (4 < $on_page) ? $on_page : 5;
                $init_page_max = ($on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

                $count = $init_page_max+2;
                for($i=$init_page_min-1; $i<$count; ++$i)
                {
                    $page_string .= ($i === $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
                    if ($i <  $init_page_max+1)
                        $page_string .= ', ';
                }
                $page_string .= ($on_page < $total_pages-4) ? ' ... ' : ', ';
            }
            else
                $page_string .= ' ... ';
            $count = $total_pages+1;
            for($i=$total_pages-2; $i<$count; ++$i)
            {
                $page_string .= ($i == $on_page) ? '<b>'.$i.'</b>'  : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
                if($i < $total_pages)
                    $page_string .= ', ';
            }
        }
    }
    else
    {
        $count = $total_pages+1;
        for($i=1; $i<$count; ++$i)
        {
            $page_string .= ($i == $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
            if ($i <  $total_pages)
                $page_string .= ', ';
        }
    }
    if ($add_prevnext_text)
    {
        if (1 < $on_page)
            $page_string = '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($on_page-2)*$per_page).'">'.$lang_global['prev'].'</a>&nbsp;&nbsp;'.$page_string;
            
        if ($on_page < $total_pages)
            $page_string .= '&nbsp;&nbsp;<a href="'.$base_url.'&amp;'.$start_tag.'='.($on_page*$per_page).'">'.$lang_global['next'].'</a>';
    }
    
    $page_string = 'Page: '.$page_string;

    return $page_string;
}

/**
 * Build URIs for top100 table header links
 */
function buildTopUrl($linkCategory, $type, $start, $sortOrder)
{
    return "top100.php?" . http_build_query([
            'order_by' => $linkCategory,
            'type' => $type,
            'start' => $start,
            'sort_order' => $sortOrder  === 'ASC' ? 'DESC' : 'ASC'
        ]);
}

/**
 * Build class name for top100 table header links
 */
function buildTopSortClass($type, $orderBy, $sortOrder) {
    return $orderBy == $type
        ? $sortOrder
        : '';
}

?>
