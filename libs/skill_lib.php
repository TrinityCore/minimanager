<?php


//#############################################################################
//get skill type by its id

function skill_get_type($id, &$sqlm)
{
    $skill_type = $sqlm->fetch_assoc($sqlm->query('SELECT field_1 FROM dbc_skillline WHERE id='.$id.' LIMIT 1'));
    return $skill_type['field_1'];
}


//#############################################################################
//get skill name by its id

function skill_get_name($id, &$sqlm)
{
    $skill_name = $sqlm->fetch_assoc($sqlm->query('SELECT field_3 FROM dbc_skillline WHERE id='.$id.' LIMIT 1'));
    return $skill_name['field_3'];
}


?>