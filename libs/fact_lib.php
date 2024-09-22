<?php


//#############################################################################
// faction id and rep table

function fact_get_fact_id()
{
    return
        [ //              0    1  2       3               4            5            6            7            8             9             10            11
     //id => array(name,team,n,reputationListID,BaseRepMask1,BaseRepMask2,BaseRepMask3,BaseRepMask4,BaseRepValue1,BaseRepValue2,BaseRepValue3,BaseRepValue4)

      54 => ['Gnomeregan Exiles', 'Alliance',1,18,1037,690,64,0,3100,-42000,4000,0],
      72 => ['Stormwind',         'Alliance',1,19,1100,690,1,0,3100,-42000,4000,0],
      47 => ['Ironforge',         'Alliance',1,20,1097,690,4,0,3100,-42000,4000,0],
      69 => ['Darnassus',         'Alliance',1,21,1093,690,8,0,3100,-42000,4000,0],
     930 => ['Exodar',            'Alliance',1,49,77,946,1024,0,3000,-42000,4000,0],

      76 => ['Orgrimmar',        'Horde',2,14,160,1101,2,528,3100,-42000,4000,500],
     530 => ['Darkspear Trolls', 'Horde',2,15,34,1101,528,128,3100,-42000,500,4000],
      81 => ['Thunder Bluff',    'Horde',2,16,130,1101,528,32,3100,-42000,500,4000],
      68 => ['Undercity',        'Horde',2,17,162,1101,16,512,500,-42000,4000,3100],
     911 => ['Silvermoon City',  'Horde',2,55,162,1101,512,16,400,-42000,4000,3100],

     730 => ['Stormpike Guard',       'Alliance Forces',3,40,1101,690,0,0,0,-42000,0,0],
     890 => ['Silverwing Sentinels',  'Alliance Forces',3,45,1101,690,0,0,0,-42000,0,0],
     509 => ['The League of Arathor', 'Alliance Forces',3,53,1101,690,0,0,0,-42000,0,0],

     729 => ['Frostwolf Clan',    'Horde Forces',4,41,690,1101,0,0,0,-42000,0,0],
     889 => ['Warsong Outriders', 'Horde Forces',4,46,690,1101,0,0,0,-42000,0,0],
     510 => ['The Defilers',      'Horde Forces',4,52,690,1101,0,0,0,-42000,0,0],

      21 => ['Booty Bay', 'Steamwheedle Cartel',5,1,1791,0,0,0,500,0,0,0],
     369 => ['Gadgetzan', 'Steamwheedle Cartel',5,7,1791,0,0,0,500,0,0,0],
     470 => ['Ratchet',   'Steamwheedle Cartel',5,9,1791,0,0,0,500,0,0,0],
     577 => ['Everlook',  'Steamwheedle Cartel',5,28,1791,0,0,0,500,0,0,0],

     947 => ['Thrallmar',             'The Burning Crusade',6,37,690,1101,0,0,0,-42000,0,0],
     946 => ['Honor Hold',            'The Burning Crusade',6,38,1101,690,0,0,0,-42000,0,0],
     933 => ['The Consortium',        'The Burning Crusade',6,60,2047,0,0,0,0,0,0,0],
     941 => ['The Mag\'har',          'The Burning Crusade',6,61,690,1101,0,0,-500,-42000,0,0],
     942 => ['Cenarion Expedition',   'The Burning Crusade',6,64,2047,0,0,0,0,0,0,0],
     970 => ['Sporeggar',             'The Burning Crusade',6,65,2047,0,0,0,-2500,0,0,0],
     978 => ['Kurenai',               'The Burning Crusade',6,66,1101,690,0,0,-1200,-42000,0,0],
     989 => ['Keepers of Time',       'The Burning Crusade',6,67,1791,0,0,0,0,0,0,0],
     990 => ['The Scale of the Sands','The Burning Crusade',6,57,1791,0,0,0,0,0,0,0],
    1012 => ['Ashtongue Deathsworn',  'The Burning Crusade',6,70,1791,0,0,0,0,0,0,0],
    1015 => ['Netherwing',            'The Burning Crusade',6,71,1791,0,0,0,-42000,0,0,0],
    1038 => ['Ogri\'la',              'The Burning Crusade',6,73,1791,0,0,0,0,0,0,0],

     935 => ['The Sha\'tar',            'Shattrath City',7,39,1791,0,0,0,0,0,0,0],
     932 => ['The Aldor',               'Shattrath City',7,58,255,1024,512,0,0,3500,-3500,0],
     934 => ['The Scryers',             'Shattrath City',7,62,255,1024,512,0,0,-3500,3500,0],
    1011 => ['Lower City',              'Shattrath City',7,69,32767,0,0,0,0,0,0,0],
    1031 => ['Sha\'tari Skyguard',      'Shattrath City',7,72,1791,0,0,0,0,0,0,0],
    1077 => ['Shattered Sun Offensive', 'Shattrath City',7,73,1791,0,0,0,0,0,0,0],

    1050 => ['Valiance Expedition', 'Alliance Vanguard',8,74,1101,690,0,0,0,-42000,0,0],
    1068 => ['Explorers\' League',  'Alliance Vanguard',8,78,1101,690,0,0,0,-42000,0,0],
    1094 => ['The Silver Covenant', 'Alliance Vanguard',8,90,1101,690,0,0,0,-42000,0,0],
    1126 => ['The Frostborn',       'Alliance Vanguard',8,99,1101,690,0,0,0,-42000,0,0],

    1064 => ['The Taunka',            'Horde Expedition',9,76,690,1101,0,0,0,-42000,0,0],
    1067 => ['The Hand of Vengeance', 'Horde Expedition',9,77,690,1101,0,0,0,-42000,0,0],
    1085 => ['Warsong Offensive',     'Horde Expedition',9,81,1101,690,0,0,-42000,0,0,0],
    1124 => ['The Sunreavers',        'Horde Expedition',9,98,690,1101,0,0,0,-42000,0,0],

    1104 => ['Frenzyheart Tribe', 'Sholazar Basin',10,92,1791,0,0,0,0,0,0,0],
    1105 => ['The Oracles',       'Sholazar Basin',10,93,1791,0,00,0,0,0,0,0],

    1073 => ['The Kalu\'ak',              'Wrath of the Lich King',11,79,1791,0,0,0,0,0,0,0],
    1091 => ['The Wyrmrest Accord',       'Wrath of the Lich King',11,83,1791,0,0,0,0,0,0,0],
    1090 => ['Kirin Tor',                 'Wrath of the Lich King',11,84,1229,690,1101,690,0,0,3000,3000],
    1098 => ['Knights of the Ebon Blade', 'Wrath of the Lich King',11,91,0,0,0,0,3200,0,0,0],
    1106 => ['Argent Crusade',            'Wrath of the Lich King',11,94,32767,0,0,0,0,0,0,0],
    1119 => ['The Sons of Hodir',         'Wrath of the Lich King',11,97,1791,0,0,0,-42000,0,0,0],
    1156 => ['Ashen Veredict',            'Wrath of the Lich King',11,104,2097151,0,0,0,0,0,0,0],

      87 => ['Bloodsail Buccaneers',           'Other',12,0,1791,0,0,0,-6500,0,0,0],
      92 => ['Gelkis Clan Centaur',            'Other',12,2,1791,0,0,0,2000,0,0,0],
      93 => ['Magram Clan Centaur',            'Other',12,3,1791,0,0,0,2000,0,0,0],
      59 => ['Thorium Brotherhood',            'Other',12,4,1791,0,0,0,0,0,0,0],
     349 => ['Ravenholdt',                     'Other',12,5,1791,0,0,0,0,0,0,0],
      70 => ['Syndicate',                      'Other',12,6,1791,0,0,0,-10000,0,0,0],
     471 => ['Wildhammer Clan',                'Other',12,8,1097,690,4,0,150,-42000,500,0],
     169 => ['Steamwheedle Cartel',            'Other',12,10,1791,0,0,0,500,0,0,0],
     469 => ['Alliance',                       'Other',12,11,1101,690,0,0,3300,-42000,0,0],
      67 => ['Horde',                          'Other',12,12,690,1101,0,0,3500,-42000,0,0],
     529 => ['Argent Dawn',                    'Other',12,13,1791,0,0,0,200,0,0,0],
      86 => ['Leatherworking - Dragonscale',   'Other',12,22,1791,0,0,0,2999,0,0,0],
      83 => ['Leatherworking - Elemental',     'Other',12,23,1791,0,0,0,2999,0,0,0],
     549 => ['Leatherworking - Tribal',        'Other',12,24,1791,0,0,0,2999,0,0,0],
     551 => ['Engineering - Gnome',            'Other',12,25,1791,0,0,0,2999,0,0,0],
     550 => ['Engineering - Goblin',           'Other',12,26,1791,0,0,0,2999,0,0,0],
     589 => ['Wintersaber Trainers',           'Other',12,27,690,1101,0,0,-42000,0,0,0],
      46 => ['Blacksmithing - Armorsmithing',  'Other',12,29,1791,0,0,0,0,0,0,0],
     289 => ['Blacksmithing - Weaponsmithing', 'Other',12,30,1791,0,0,0,0,0,0,0],
     570 => ['Blacksmithing - Axesmithing',    'Other',12,31,1791,0,0,0,0,0,0,0],
     571 => ['Blacksmithing - Swordsmithing',  'Other',12,32,1791,0,0,0,0,0,0,0],
     569 => ['Blacksmithing - Hammersmithing', 'Other',12,33,1791,0,0,0,0,0,0,0],
     574 => ['Caer Darrow',                    'Other',12,34,1791,0,0,0,0,0,0,0],
     576 => ['Timbermaw Hold',                 'Other',12,35,1791,0,0,0,-3500,0,0,0],
     609 => ['Cenarion Circle',                'Other',12,36,1791,40,0,0,0,2000,0,0],
     749 => ['Hydraxian Waterlords',           'Other',12,42,1791,0,0,0,0,0,0,0],
     980 => ['Outland',                        'Other',12,43,0,0,0,0,0,0,0,0],
     809 => ['Shen\'dralar',                   'Other',12,44,1791,0,0,0,0,0,0,0],
     891 => ['Alliance Forces',                'Other',12,47,1101,178,0,0,0,-42000,0,0],
     892 => ['Horde Forces',                   'Other',12,48,690,77,0,0,0,-42000,0,0],
     909 => ['Darkmoon Faire',                 'Other',12,50,1791,0,0,0,0,0,0,0],
     270 => ['Zandalar Tribe',                 'Other',12,51,1791,0,0,0,0,0,0,0],
     910 => ['Brood of Nozdormu',              'Other',12,54,1791,0,0,0,-42000,0,0,0],
     922 => ['Tranquillien',                   'Other',12,56,690,1101,0,0,0,-42000,0,0],
     936 => ['Shattrath City',                 'Other',12,59,2047,0,0,0,0,0,0,0],
     967 => ['The Violet Eye',                 'Other',12,63,4095,0,0,0,0,0,0,0],
    1005 => ['Friendly, Hidden',               'Other',12,68,32767,0,0,0,3000,0,0,0],
    1037 => ['Alliance Vanguard',              'Other',12,88,1101,690,0,0,0,0,0,0],
    1052 => ['Horde Expedition',               'Other',12,75,690,1101,0,0,0,-42000,0,0],
    1097 => ['Northrend',                      'Other',12,89,0,0,0,0,0,0,0,0],
    1117 => ['Sholazar Basin',                 'Other',12,95,1791,0,0,0,0,0,0,0]
        ];
}


//#############################################################################
//get reputation ranks lengths - https://www.wowwiki.com/Reputation

function fact_get_reputation_rank_length()
{
    return [36000, 3000, 3000, 3000, 6000, 12000, 21000, 999];
}


//#############################################################################
//get reputation ranks by its id - https://www.wowwiki.com/Reputation

function fact_get_reputation_rank_arr()
{
    return
        [
        0 => 'Hated',
        1 => 'Hostile',
        2 => 'Unfriendly',
        3 => 'Neutral',
        4 => 'Friendly',
        5 => 'Honored',
        6 => 'Revered',
        7 => 'Exalted'
        ];
}


//#############################################################################
//get faction name by its id

function fact_get_faction_name($id, &$sqlm)
{
    $faction_name = $sqlm->fetch_assoc($sqlm->query('SELECT field_23 FROM dbc_faction WHERE id = '.$id.' LIMIT 1'));
    return $faction_name['field_23'];
}


//#############################################################################
//get faction tree by its id - needs to be redone

function fact_get_faction_tree($id)
{
    $fact_id = fact_get_fact_id();

    if (isset($fact_id[$id]))
        return $fact_id[$id][2];
    else
        return 0;
}


//#############################################################################
//get faction name by its id

function fact_get_base_reputation($id, $race, &$sqlm)
{
    $faction_base_reputation = $sqlm->fetch_array($sqlm->query('SELECT field_1, field_2, field_3, field_4, field_5, field_10, field_11, field_12, field_13 FROM dbc_faction WHERE id = '.$id.' LIMIT 1'));

    if(isset($faction_base_reputation));
    else
        return 0;
    for ($i = 0; $i < 4; ++$i)
    {
        if ( $faction_base_reputation[1 + $i] & (1 << ($race-1)) )
            return $faction_base_reputation[5 + $i];
    }

    /*
    $fact_id = fact_get_fact_id();

    if(isset($fact_id[$id]));
    else
        return 0;
    for ($i = 0; $i < 4; ++$i)
    {
        if ($fact_id[$id][4 + $i] & (1 << ($race-1)))
            return $fact_id[$id][8 + $i];
    }
    */

    return 0;
}


//#############################################################################
//get reputation by its id

function fact_get_reputation($id, $standing, $race, &$sqlm)
{
    return fact_get_base_reputation($id, $race, $sqlm) + $standing;
}


//#############################################################################
//get reputation rank by its id

function fact_get_reputation_rank($id, $standing, $race, &$sqlm)
{
    $reputation = fact_get_reputation($id, $standing, $race, $sqlm);
        return fact_reputation_to_rank($reputation);
}


//#############################################################################
//get reputation at rank by its id

function fact_get_reputation_at_rank($id, $standing, $race, &$sqlm)
{
    $reputation = fact_get_reputation($id, $standing, $race, $sqlm);
        return fact_reputation_at_rank($reputation);
}


//#############################################################################
//get base reputation rank by its id

function fact_get_base_reputation_rank($id, $race, &$sqlm)
{
    $reputation = fact_get_base_reputation($id, $race, $sqlm);
        return fact_reputation_to_rank($reputation);
}


//#############################################################################
//get reputation at to rank by its id
//- https://code.google.com/p/trinitycore/source/browse/src/game/ReputationMgr.h

function fact_reputation_at_to_rank($standing, $type)
{
    $reputation_rank        = fact_get_reputation_rank_arr();
    $reputation_rank_length = fact_get_reputation_rank_length();
    $reputation_cap         =  42999;
    $reputation_bottom      = -42000;
    $MIN_REPUTATION_RANK    =  0;
    $MAX_REPUTATION_RANK    =  8;

    $limit = $reputation_cap;
    for ($i = $MAX_REPUTATION_RANK-1; $i >= $MIN_REPUTATION_RANK; --$i)
    {
        $limit -= $reputation_rank_length[$i];
        if ($standing >= $limit )
            return (($type) ? $standing - $limit : $i);
    }
    return (($type) ? 0 : $MIN_REPUTATION_RANK);
}


//#############################################################################
//get reputation to rank by its id

function fact_reputation_to_rank($standing)
{
    return fact_reputation_at_to_rank($standing, 0);
}


//#############################################################################
//get reputation at rank by its id

function fact_reputation_at_rank($standing)
{
    return fact_reputation_at_to_rank($standing, 1);
}


?>
