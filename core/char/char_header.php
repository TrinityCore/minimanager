<?php

$output .= '
    <div id="tab">
        <ul>
            <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
            <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
            <li><a href="char_extra.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['extra'].'</a></li>
            '.(($char['level'] < 10) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>').'
            <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
            <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['reputation'].'</a></li>
            <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['skills'].'</a></li>
            <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>';
if (char_get_class_name($char['class']) === 'Hunter' )
    $output .= '
            <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['pets'].'</a></li>';
$output .= '
            <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
            <li><a href="char_spell.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['spells'].'</a></li>
            <li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['mail'].'</a></li>
        </ul>
    </div>';
$output .= '
    <div id="tab_content2">
        <font class="bold">
            '.htmlentities($char['name']).' -
            <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
            onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
            <img src="img/c_icons/'.$char['class'].'.gif"
            onmousemove="toolTip(\''.char_get_class_name($char['class']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).''.(basename($_SERVER['PHP_SELF']) === 'char_inv.php' ? ' &nbsp;&nbsp; <label for="group_bags_chk" style="cursor:pointer; font-weight:normal; font-size:12px;"><input type="checkbox" id="group_bags_chk" name="group_bags" value="1" '.(!empty($_GET['group_bags']) ? 'checked="checked" ' : '').'onchange="window.location.href=\'char_inv.php?id='.$id.'&amp;realm='.$realmid.'\'+(this.checked ? \'&amp;group_bags=1\' : \'\');" /> Agrupar mochilas</label>' : '').'
        </font>';
?>