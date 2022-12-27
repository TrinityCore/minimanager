<?php

// page header, and any additional required libraries
require_once 'tab_lib.php';

//##########################################################################################
//Delete character
function del_char($guid, $realm)
{
    global 	$characters_db, $realm_db,
            $user_lvl, $user_id,
            $tab_del_user_characters, $tab_del_pet;

    $sqlr = new SQL;
    $sqlc = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $sqlc->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);

    $query = $sqlc->query('SELECT account, online 
                            FROM characters  
                            WHERE guid = '.$guid.' LIMIT 1');
    $owner_acc_id = $sqlc->result($query, 0, 'account');

    $owner_gmlvl = $sqlr->result($sqlr->query('SELECT SecurityLevel 
                                                FROM account_access 
                                                WHERE id = '.$owner_acc_id.''), 0);

    if ( ($user_lvl > $owner_gmlvl) || ($owner_acc_id == $user_id) )
    {
        if ($sqlc->result($query, 0, 'online'));
        else
        {
            //Delete pet aura ,spells and cooldowns
            foreach ($tab_del_pet as $value)
                $sqlc->query('DELETE 
                            FROM '.$value[0].' 
                            WHERE '.$value[1].' IN
                        (SELECT id 
                            FROM character_pet 
                            WHERE owner IN
                        (SELECT guid 
                            FROM characters 
                            WHERE guid = '.$row['guid'].'))');
            foreach ($tab_del_user_characters as $value)
                $sqlc->query('DELETE 
                            FROM '.$value[0].' 
                            WHERE '.$value[1].' = '.$guid.'');

            $chars_in_acc = $sqlr->result($sqlr->query('SELECT numchars 
                                                        FROM realmcharacters 
                                                        WHERE acctid ='.$owner_acc_id.' AND realmid = '.$realm.''), 0);
            if ($chars_in_acc)
                $chars_in_acc--;
            else
                $chars_in_acc = 0;
            $sqlr->query('UPDATE realmcharacters 
                            SET numchars='.$chars_in_acc.' 
                            WHERE acctid ='.$owner_acc_id.' 
                            AND realmid = '.$realm.'');
            return true;
        }
    }
    return false;
}


//##########################################################################################
//Delete Account - return array(deletion_flag , number_of_chars_deleted)
function del_acc($acc_id)
{
    global 	$characters_db, $realm_db,
            $user_lvl, $user_id,
            $tab_del_user_realmd, $tab_del_user_char, $tab_del_user_characters, $tab_del_pet;

    $del_char = 0;

    $sqlc = new SQL;
    $sqlr = new SQL;
    $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

    $query = $sqlr->query('SELECT `account`.`online`, `account_access`.`SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$acc_id.'');


    $SecurityLevel = $sqlr->result($query, 0, 'SecurityLevel');

    if (($user_lvl > $SecurityLevel)||($acc_id == $user_id))
    {
        if ($sqlr->result($query, 0, 'online'));
        else
        {
            foreach ($characters_db as $db)
            {
                $sqlc->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
                $result = $sqlc->query('SELECT guid 
                                        FROM characters 
                                        WHERE account = '.$acc_id.'');
                while ($row = $sqlc->fetch_assoc($result))
                {
                    //Delete pet aura ,spells and cooldowns
                    foreach ($tab_del_pet as $value)
                        $sqlc->query('DELETE 
                                    FROM '.$value[0].' 
                                    WHERE '.$value[1].' IN
                                (SELECT id 
                                    FROM character_pet 
                                    WHERE owner IN
                                (SELECT guid 
                                    FROM characters 
                                    WHERE guid = '.$row['guid'].'))');
                    foreach ($tab_del_user_characters as $value)
                        $sqlc->query('DELETE 
                                    FROM '.$value[0].' 
                                    WHERE '.$value[1].' = '.$row['guid'].'');
                    $del_char++;
                }
                $sqlc->query('DELETE 
                            FROM account_data 
                            WHERE account = '.$acc_id.'');
            }
            foreach ($tab_del_user_realmd as $value)
                $sqlr->query('DELETE 
                            FROM '.$value[0].' 
                            WHERE '.$value[1].' = '.$acc_id.'');
            if ($sqlr->affected_rows())
            return [true, $del_char];
        }
    }
    return [false, $del_char];
}


//##########################################################################################
//Delete Guild
function del_guild($guid, $realm)
{
    global $characters_db, $tab_del_guild;

    require_once 'data_lib.php';

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);

    $sqlc->query('DELETE 
                    FROM item_instance 
                    WHERE guid IN 
                (SELECT item_guid 
                    FROM guild_bank_item 
                    WHERE guildid ='.$guid.')');

    foreach ($tab_del_guild as $value)
        $sqlr->query('DELETE 
                    FROM '.$value[0].' 
                    WHERE '.$value[1].' = '.$guid.'');

    if ($sqlc->affected_rows())
        return true;
    else
        return false;

}


//##########################################################################################
//Delete Arena Team
function del_arenateam($guid, $realm)
{
    global	$characters_db,
            $tab_del_arena;

    $sqlc = new SQL;
    $sqlc->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);

    foreach ($tab_del_arena as $value)
        $sqlr->query('DELETE 
                    FROM '.$value[0].' 
                    WHERE '.$value[1].' = '.$guid.'');

    if ($sqlc->affected_rows())
        return true;
    else
        return false;

}


?>
