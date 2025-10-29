<?php


//#############################################################################
//get map name by its id

function get_map_name($id, &$sqlm)
{
    $map_name = $sqlm->fetch_assoc($sqlm->query('SELECT name01 FROM dbc_map WHERE id='.$id.' LIMIT 1'));
    return $map_name['name01'] ?? 'N/A';
}


//#############################################################################
//get zone name by its id

function get_zone_name($id, &$sqlm)
{
    $zone_name = $sqlm->fetch_assoc($sqlm->query('SELECT name01 FROM dbc_areatable WHERE id='.$id.' LIMIT 1'));
    return $zone_name['name01'] ?? 'N/A';
}


?>