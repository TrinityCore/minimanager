<?php


//##########################################################################################
//get player name
function get_char_name($id)
{
    global $characters_db, $realm_id;

    if($id)
    {
        $sqlc = new SQL;
        $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

        $result = $sqlc->query("SELECT BINARY `name` AS `name` FROM `characters` WHERE `guid` = '$id'");
        $player_name = $sqlc->result($result, 0);

        return $player_name;
    }
    else
        return NULL;
}

// Mail Source
$mail_source =
    [
    "0" => "Normal",
    "2" => "Auction",
    "3" => "Creature",
    "4" => "GameObject",
    "5" => "Item",
    ];

function get_mail_source($id)
{
    global $mail_source;
    return $mail_source[$id] ;
}

// Check State
$check_state =
    [
    //"0" => "Not Read",
    "1" => "Read",
    "2" => "Ret", //"Returned"
    "4" => "Co", //"Copied Checked"
    "8" => "COD", //"COD Pay Checked"
    "16" => "B" //"Mail has body"
    ];

function bitMask($mask = 0) 
{
    if(!is_numeric($mask))
        return [];

    $return = [];
    while ($mask > 0) 
    {
        for($i = 0, $n = 0; $i <= $mask; $i = 1 * pow(2, $n), $n++)
            $end = $i;

        $return[] = $end;
        $mask = $mask - $end;
    }
    sort($return);
    return $return;
}

function get_check_state($id)
{
    global $check_state;
    $result = "";
    
    if ($id == 0)
        return "Not Read";
    
    foreach (bitMask($id) as $k => $v)
        $result .= $check_state[$v].", ";

    return $result;
}

?>