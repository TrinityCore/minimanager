<?php


/*
//#############################################################################
//get achievement name by its id

function achieve_get_name($id, &$sqlm)
{
  $achievement_name = $sqlm->fetch_assoc($sqlm->query('SELECT name01 FROM dbc_achievement WHERE id= '.$id.' LIMIT 1'));
  return $achievement_name['name01'];
}


//#############################################################################
//get achievement reward name by its id

function achieve_get_reward($id, &$sqlm)
{
  $achievement_reward = $sqlm->fetch_assoc($sqlm->query('SELECT rewarddesc01 FROM dbc_achievement WHERE id ='.$id.' LIMIT 1'));
  return $achievement_reward['rewarddesc01'];
}


//#############################################################################
//get achievement points name by its id

function achieve_get_points($id, &$sqlm)
{
  $achievement_points = $sqlm->fetch_assoc($sqlm->query('SELECT rewpoints FROM dbc_achievement WHERE id = '.$id.' LIMIT 1'));
  return $achievement_points['rewpoints'];
}


//#############################################################################
//get achievement category name by its id

function achieve_get_category($id, &$sqlm)
{
  $category_id= $sqlm->fetch_assoc($sqlm->query('SELECT categoryid FROM dbc_achievement WHERE id = '.$id.' LIMIT 1'));
  $category_name = $sqlm->fetch_assoc($sqlm->query('SELECT name01 FROM dbc_achievement_category WHERE id = '.$category_id['categoryid'].' LIMIT 1'));
  return $category_name['name01'];
}
*/


//#############################################################################
//get achievements by category id

function achieve_get_id_category($id, &$sqlm)
{
    $achieve_cat = [];
    $result = ($sqlm->query('SELECT id, name01, description01, rewarddesc01, rewpoints FROM dbc_achievement WHERE categoryid = \''.$id.'\' ORDER BY `order` DESC'));
    while ($achieve_cat[] = $sqlm->fetch_assoc($result));
    return $achieve_cat;
}


//#############################################################################
//get achievement main category

function achieve_get_main_category(&$sqlm)
{
    $main_cat = [];
    $result = $sqlm->query('SELECT id, name01 FROM dbc_achievement_category WHERE parentid = -1 and id != 1 ORDER BY `order` ASC');
    while ($main_cat[] = $sqlm->fetch_assoc($result));
    return $main_cat;
}


//#############################################################################
//get achievement sub category

function achieve_get_sub_category(&$sqlm)
{
    $sub_cat = [];
    $result = $sqlm->query('SELECT id, parentid, name01 FROM dbc_achievement_category WHERE parentid != -1 ORDER BY `order` ASC');
    $temp = $sqlm->fetch_assoc($result);
    while ($sub_cat[$temp['parentid']][$temp['id']] = $temp['name01'])
        $temp = $sqlm->fetch_assoc($result);

    return $sub_cat;
}


//#############################################################################
//get achievement details by its id

function achieve_get_details($id, &$sqlm)
{
    $result = ($sqlm->query('SELECT id, name01, description01, rewarddesc01, rewpoints FROM dbc_achievement WHERE id = \''.$id.'\' LIMIT 1'));
    $details = $sqlm->fetch_assoc($result);
    return $details;
}


//#############################################################################
//get achievement icon

function achieve_get_icon($achieveid, &$sqlm)
{
    global $item_icons;

    $result = $sqlm->query('SELECT field_42 FROM dbc_achievement WHERE id = \''.$achieveid.'\' LIMIT 1');

    if ($result)
    {
        $displayid = $sqlm->result($result, 0);
    }
    else
    {
        $displayid = 0;
    }

    if ($displayid)
    {
        $result = $sqlm->query('SELECT field_1 FROM dbc_spellicon WHERE id = '.$displayid.' LIMIT 1');

        if($result)
        {
            $achieve_uppercase = $sqlm->result($result, 0);
            $achieve = strtolower($achieve_uppercase);
            $achieve_icon = substr($achieve, 16);

            if ($achieve_icon && file_exists(''.$item_icons.'/'.$achieve_icon.'.jpg'))
            {
                return ''.$item_icons .'/'.$achieve_icon.'.jpg';
            }
            else
            {
                return ''.$item_icons .'/inv_misc_questionmark.jpg';
            }
        }
        else
        {
            return ''.$item_icons .'/inv_misc_questionmark.jpg';
        }
    }
    else
    {
        return ''.$item_icons .'/inv_misc_questionmark.jpg';
    }
}

?>
