<?php


//#############################################################################
//get character race and side table

function char_char_get_race_names_n_sides_tab()
{
    global $lang_id_tab;

    return
        [
        1 => [$lang_id_tab['human'],    0],
        2 => [$lang_id_tab['orc'],      1],
        3 => [$lang_id_tab['dwarf'],    0],
        4 => [$lang_id_tab['nightelf'], 0],
        5 => [$lang_id_tab['undead'],   1],
        6 => [$lang_id_tab['tauren'],   1],
        7 => [$lang_id_tab['gnome'],    0],
        8 => [$lang_id_tab['troll'],    1],
        10 => [$lang_id_tab['bloodelf'], 1],
        11 => [$lang_id_tab['draenei'],  0],
        ];
}


//#############################################################################
//get character side name by side id

function char_get_side_name($side_id)
{
    global $lang_id_tab;

    $side_names =
    [
        0 => $lang_id_tab['Alliance'],
        1 => $lang_id_tab['Horde'],
    ];

  return $side_names[$side_id];
}


//#############################################################################
//get character side id by race id

function char_get_side_id($race_id)
{
    $race_sides = char_char_get_race_names_n_sides_tab();

    return $race_sides[$race_id][1];
}


//#############################################################################
//get character race name by race id

function char_get_race_name($race_id)
{
    $race_names = char_char_get_race_names_n_sides_tab();

    return $race_names[$race_id][0];
}


//#############################################################################
//get player class name by class id

function char_get_class_name($class_id)
{
    global $lang_id_tab;

    $class_names =
    [
        1  => $lang_id_tab['warrior'],
        2  => $lang_id_tab['paladin'],
        3  => $lang_id_tab['hunter'],
        4  => $lang_id_tab['rogue'],
        5  => $lang_id_tab['priest'],
        6  => $lang_id_tab['death_knight'],
        7  => $lang_id_tab['shaman'],
        8  => $lang_id_tab['mage'],
        9  => $lang_id_tab['warlock'],
        11 => $lang_id_tab['druid'],
    ];

    return $class_names[$class_id];
}


//#############################################################################
//get player honor rank name by side and honor points

function char_get_pvp_rank_name($honor_points=0, $side_id=0)
{
    global $lang_id_tab;

    $rank_names =
    [
        0 =>
            [
            '00' => $lang_id_tab['None'],
            '01' => $lang_id_tab['None'],
            0   => $lang_id_tab['None'],
            1   => $lang_id_tab['Private'],
            2   => $lang_id_tab['Corporal'],
            3   => $lang_id_tab['Sergeant'],
            4   => $lang_id_tab['Master_Sergeant'],
            5   => $lang_id_tab['Sergeant_Major'],
            6   => $lang_id_tab['Knight'],
            7   => $lang_id_tab['Knight-Lieutenant'],
            8   => $lang_id_tab['Knight-Captain'],
            9   => $lang_id_tab['Knight-Champion'],
            10   => $lang_id_tab['Lieutenant_Commander'],
            11   => $lang_id_tab['Commander'],
            12   => $lang_id_tab['Marshal'],
            13   => $lang_id_tab['Field_Marshal'],
            14   => $lang_id_tab['Grand_Marshal'],
            ],
        1 =>
            [
            '00' => $lang_id_tab['None'],
            '01' => $lang_id_tab['None'],
            0   => $lang_id_tab['None'],
            1   => $lang_id_tab['Scout'],
            2   => $lang_id_tab['Grunt'],
            3   => $lang_id_tab['Sergeant_H'],
            4   => $lang_id_tab['Senior_Sergeant'],
            5   => $lang_id_tab['First_Sergeant'],
            6   => $lang_id_tab['Stone_Guard'],
            7   => $lang_id_tab['Blood_Guard'],
            8   => $lang_id_tab['Legionnare'],
            9   => $lang_id_tab['Centurion'],
            10   => $lang_id_tab['Champion'],
            11   => $lang_id_tab['Lieutenant_General'],
            12   => $lang_id_tab['General'],
            13   => $lang_id_tab['Warlord'],
            14   => $lang_id_tab['High_Warlord'],
            ]
    ];

    return $rank_names[$side_id][char_get_pvp_rank_id($honor_points, $side_id)];
}


//#############################################################################
//get pvp rank ID by honor point

function char_get_pvp_rank_id($honor_points=0, $side_id=0)
{
    $rank_id = '0'.$side_id;

    if($honor_points > 0)
    {
        if($honor_points < 2000)
            $rank_id = 1;
        else
            $rank_id = ceil($honor_points / 5000) + 1;
    }

    if ($rank_id > 14)
        $rank_id = 14;

    return $rank_id;
}


//#############################################################################
//get avatar image by char level, gender, race, class and gm level

function char_get_avatar_img($level, $gender, $race, $class, $gm=0)
{
    if ($gm > 0)
    {
        if(file_exists('img/avatars/bliz/'.$gm.'.gif'))
            $avatar = 'img/avatars/bliz/'.$gm.'.gif';
        else
            $avatar = 'img/avatars/bliz/bliz.gif';
    }
    elseif($level < 60)
    {
        $avatar = 'img/avatars/np/'.$gender.'-'.$race.'-'.$class.'.gif';
    }
    elseif($level < 70)
    {
        $avatar = 'img/avatars/60/'.$gender.'-'.$race.'-'.$class.'.gif';
    }
    elseif($level < 80)
    {
        $avatar = 'img/avatars/70/'.$gender.'-'.$race.'-'.$class.'.gif';
    }
    else
        $avatar = 'img/avatars/80/'.$gender.'-'.$race.'-'.$class.'.gif';

    return $avatar;
}


//#############################################################################
//set color per Level range

function char_get_level_color($lvl)
{
    if($lvl < 40)
    {
        if($lvl < 20)
        {
            if($lvl < 10)
                $level_color = '<font color="#FFFFFF">'.$lvl.'</font>';
            else
                $level_color = '<font color="#858585">'.$lvl.'</font>';
        }
        else
        {
            if($lvl < 30)
                $level_color = '<font color="#339900">'.$lvl.'</font>';
            else
                $level_color = '<font color="#3300CC">'.$lvl.'</font>';
        }
    }
    else
    {
        if($lvl < 60)
        {
            if($lvl < 50)
                $level_color = '<font color="#C552FF">'.$lvl.'</font>';
            else
                $level_color = '<font color="#FF8000">'.$lvl.'</font>';
        }
        else
        {
            if($lvl < 70)
                $level_color = '<font color="#FFF280">'.$lvl.'</font>';
            else
            {
                if($lvl < 80)
                    $level_color = '<font color="#FF0000">'.$lvl.'</font>';
                else
                    $level_color = '<font color="#000000">'.$lvl.'</font>';
            }
        }
    }

    return $level_color;
}

/*
  if($lvl < 10)
    $level = '<font color="#FFFFFF">'.$lvl.'</font>';
  else if($lvl < 20)
    $level = '<font color="#858585">'.$lvl.'</font>';
  else if($lvl < 30)
    $level = '<font color="#339900">'.$lvl.'</font>';
  else if($lvl < 40)
    $level = '<font color="#3300CC">'.$lvl.'</font>';
  else if($lvl < 50)
    $level = '<font color="#C552FF">'.$lvl.'</font>';
  else if($lvl < 60)
    $level = '<font color="#FF8000">'.$lvl.'</font>';
  else if($lvl < 70)
    $level = '<font color="#FFF280">'.$lvl.'</font>';
  else if($lvl < 80)
    $level = '<font color="#FF0000">'.$lvl.'</font>';
  else
    $level = '<font color="#000000">'.$lvl.'</font>';

  I'm leaving the old one here as a guide

  the old one:
   it will take 1 'if's for a level  1 to get its' color
   it will take 4 'if's for a level 40 to get its' color
   it will take 8 'if's for a level 80 to get its' color

   hence best case is 1, worst case is 8, average case is 4

  the new one:
   it will take 3 'if's for a level  1 to get its' color
   it will take 3 'if's for a level 40 to get its' color
   it will take 3 'if's for a level 70 to get its' color
   it will take 4 'if's for a level 80 to get its' color

   hence best case is 3, worst case is 4, average case less than 4

  so if we are sorting a list of characters by level high to low
    with most are level 70-80

    old one will have average case of around 7-8
    new one will have average case of around 3-4

  so if we are sorting a list of characters by level low to high
    with most are level 1-10

    old one will have average case of around 1-2
    new one will have average case of around 3

  the old one will only out perform the new one when displaying level 1-30 by a margin from 1 to 2 (12-25%)
  the new one will out perform the old one when displaying level 30-80 by a margin from 1 to 4 (12-50%)

  Xiong Guoy
  2009-08-13
*/


//#############################################################################
// for calc next level xp
function xp_Diff($lvl)
{
    if( $lvl < 29 )
        return 0;
    if( $lvl == 29 )
        return 1;
    if( $lvl == 30 )
        return 3;
    if( $lvl == 31 )
        return 6;
    else
        return (5*($lvl-30));
}

function mxp($lvl)
{
    if ($lvl < 60)
    {
        return (45 + (5*$lvl));
    }
    else
    {
        return (235 + (5*$lvl));
    }
}

function char_get_xp_to_level($lvl)
{
    $RATE_XP_PAST_70 = 1;
    $xp = 0;
    if ($lvl < 60)
    {
        $xp = (8*$lvl + xp_Diff($lvl)) * mxp($lvl);
    }
    elseif ($lvl == 60)
    {
        $xp = (155 + mxp($lvl) * (1344 - 70 - ((69 - $lvl) * (7 + (69 - $lvl) * 8 - 1)/2)));
    }
    elseif ($lvl < 70)
    {
        $xp = (155 + mxp($lvl) * (1344 - ((69-$lvl) * (7 + (69 - $lvl) * 8 - 1)/2)));
    }
    else
    {
        // level higher than 70 is not supported
        $xp = (779700 * (pow($RATE_XP_PAST_70, $lvl - 69)));
        return (($xp < 0x7fffffff) ? $xp : 0x7fffffff);
    }
    // The $xp to Level is always rounded to the nearest 100 points (50 rounded to high).
    $xp = (($xp + 50) / 100) * 100;                  // use additional () for prevent free association operations in C++

    if (($lvl > 10) && ($lvl < 60))                  // compute discount added in 2.3.x
    {
        $discount = ($lvl < 28) ? ($lvl - 10) : 18;
        $xp = ($xp * (100 - $discount)) / 100;         // apply discount
        $xp = ($xp / 100) * 100;                       // floor to hundreds
    }

    return $xp;
}


?>
