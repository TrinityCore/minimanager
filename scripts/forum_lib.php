<?php


function bbcode_editor_js(){
    //By Tucefa, https://www.4claverie.com/forums/index.php?showtopic=3904
    return "<script type=\"text/javascript\">
function ajtTexte(txt){
  var obj = document.getElementsByName(\"msg\")[0], sel;
  obj.focus();
  if(document.selection && document.selection.createRange){
    sel = document.selection.createRange();
    if (sel.parentElement()==obj)
      sel.text = sel.text+txt;
  }
  else if(String(typeof obj.selectionStart)!=\"undefined\"){
    sel = obj.selectionStart;
    obj.value = (obj.value).substring(0,sel) + txt + (obj.value).substring(sel,obj.value.length);
  }
  else
    obj.value+=txt;
  obj.focus();
}
function ajtBBCode(Tag, fTag){
  var obj = document.getElementsByName(\"msg\")[0], sel;
  obj.focus();
  if (document.selection && document.selection.createRange){
    sel = document.selection.createRange();
    if (sel.parentElement()==obj)
      sel.text = Tag+sel.text+fTag;
  }
  else if(String(typeof obj.selectionStart)!=\"undefined\"){
    var longueur= parseInt(obj.textLength);
    var selStart = obj.selectionStart;
    var selEnd = obj.selectionEnd;
    if (selEnd == 2 || selEnd == 1)
      selEnd = longueur;
    obj.value = (obj.value).substring(0,selStart)+Tag+(obj.value).substring(selStart,selEnd)+fTag+(obj.value).substring(selEnd,longueur);
  }
  else obj.value+=Tag+fTag;
  obj.focus();
}
</script>";
}

function bbcode_callbacks_wow($item){
    global $item_datasite;
    wowhead_tt();
    require_once("scripts/id_tab.php");
    require_once("scripts/get_lib.php");
    return "<a href=\"$item_datasite{$item[1]}\" target=\"_blank\"
onmouseover=\"toolTip('".addslashes(get_item_tooltip($item[1]))."','item_tooltip')\" onmouseout=\"toolTip()\">
<img src=\"".get_item_icon($item[1])."\" class=\"icon_border\" alt=\"\" /></a>";
}

function handle_url_tag($url, $link = ''){
    // From PunBB
    $full_url = str_replace([' ', '\'', '`', '"'], ['%20', '', '', ''], $url);
    if (strpos($url, 'www.') === 0)            // If it starts with www, we add https://
        $full_url = 'https://'.$full_url;
    else if (strpos($url, 'ftp.') === 0)    // Else if it starts with ftp, we add ftp://
        $full_url = 'ftp://'.$full_url;
    else if (!preg_match('#^([a-z0-9]{3,6})://#', $url, $bah))     // Else if it doesn't start with abcdef://, we add https://
        $full_url = 'https://'.$full_url;
    // Ok, not very pretty :-)
    $link = ($link == '' || $link == $url) ? ((strlen($url) > 55) ? substr($url, 0 , 39).' &hellip; '.substr($url, -10) : $url) : stripslashes($link);
    return '<a href="'.$full_url.'">'.$link.'</a>';
}

function do_clickable($text){
    global $userid;
    // From  PunBB
    $text = ' '.$text;
    $text = preg_replace('#([\s\(\)])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#si', '\'$1\'.handle_url_tag(\'$2://$3\')', $text);
    $text = preg_replace('#([\s\(\)])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#si', '\'$1\'.handle_url_tag(\'$2.$3\', \'$2.$3\')', $text);
    // Regex [youtube] et [/youtube]
    $uid = $userid;
    $text = preg_replace("#\[youtube\](.*?)\[/youtube\]#si", "[youtube:$uid]\\1[/youtube:$uid]", $text);
    $text = preg_replace("#\[media\](([a-z]+?)://([^, \n\r]+))\[/media\]#si", "[media:$uid]\\1[/media:$uid]", $text);
    return substr($text, 1);
}

function bbcode_parse($text, $brfix = 1, $emoticons = 1, $wow = 1){
    // By BlackWizard, https://www.phpcs.com/codes/BBCODE-SIMPLEMENT_17638.aspx
    global $forum_lang, $userid;
    $text = preg_replace("#\[img\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/img\]#si", "'<img border=\"0\" src=\\1' . str_replace(' ', '%20', '\\3') . '>'", $text);
    $text = preg_replace("#\[url\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/url\]#si", "'<a href=\"\\1' . str_replace(' ', '%20', '\\3') . '\" target=blank>\\1\\3</a>'", $text);
    $text = preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/", "<a href=\"$1\" target=\"blank\">$2</a>", $text);
    $text = preg_replace("/\[b\](.+?)\[\/b\]/", "<b>$1</b>", $text);
    $text = preg_replace("/\[i\](.+?)\[\/i\]/", "<i>$1</i>", $text);
    $text = preg_replace("/\[u\](.+?)\[\/u\]/", "<u>$1</u>", $text);
    $text = preg_replace("/\[code\](.+?)\[\/code\]/", "<table width=100%><tr><th align=left>Code :</th></tr><tr><td align=left><code>$1</code></td></tr></table>", $text);
    $text = preg_replace("/\[quote\](.+?)\[\/quote\]/", "<table width=100%><tr><th align=left>{$forum_lang["quote"]} :</th></tr><tr><td align=left>$1</td></tr></table>", $text);
    $text = preg_replace("/\[quote=(.+?)\](.+?)\[\/quote\]/", "<table width=100%><tr><th align=left>$1 {$forum_lang["wrote"]} :</th></tr><tr><td align=left>$2</td></tr></table>", $text);
    $text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/", "<font color=$1>$2</font>", $text);
    $uid = $userid;
    $text = preg_replace("#\[youtube\](.*?)\[/youtube\]#si", "[youtube:$uid]\\1[/youtube:$uid]", $text);
    $text = preg_replace("#\[media\](([a-z]+?)://([^, \n\r]+))\[/media\]#si", "[media:$uid]\\1[/media:$uid]", $text);
    if($wow = 1)
        $text = preg_replace_callback("/\[wow\](.+?)\[\/wow\]/","bbcode_callbacks_wow",$text);

    // Emoticons.
    if($emoticons = 1)
    {
        // Emoticons from PunBB
        $text = str_replace(":)", "<img src=\"img/emoticons/smile.gif\" />", $text);
        $text = str_replace("=)", "<img src=\"img/emoticons/smile.gif\" />", $text);
        $text = str_replace(":|", "<img src=\"img/emoticons/neutral.gif\" />", $text);
        $text = str_replace("=|", "<img src=\"img/emoticons/neutral.gif\" />", $text);
        $text = str_replace(":(", "<img src=\"img/emoticons/sad.gif\" />", $text);
        $text = str_replace("=(", "<img src=\"img/emoticons/sad.gif\" />", $text);
        $text = str_replace(":D", "<img src=\"img/emoticons/razz.gif\" />", $text);
        $text = str_replace("=D", "<img src=\"img/emoticons/razz.gif\" />", $text);
        $text = str_replace(":o", "<img src=\"img/emoticons/yikes.gif\" />", $text);
        $text = str_replace(":0", "<img src=\"img/emoticons/yikes.gif\" />", $text);
        $text = str_replace(";)", "<img src=\"img/emoticons/wink.gif\" />", $text);
        $text = preg_replace("/([^p|s])\:\//", "$1<img src=\"img/emoticons/hmm.gif\" />", $text);
        $text = str_replace(":P", "<img src=\"img/emoticons/tongue.gif\" />", $text);
        $text = str_replace(":p", "<img src=\"img/emoticons/tongue.gif\" />", $text);
        $text = str_replace(":lol:", "<img src=\"img/emoticons/lol.gif\" />", $text);
        $text = str_replace(":mad:", "<img src=\"img/emoticons/angry.gif\" />", $text);
        $text = str_replace(":rolleyes:", "<img src=\"img/emoticons/roll.gif\" />", $text);
        $text = str_replace(":cool:", "<img src=\"img/emoticons/cool.gif\" />", $text);
    }

    if($brfix = 1)
        $text = str_replace("&lt;br /&gt;", "<br /> ", $text);
    else
        $text = str_replace("<br />", "<br /> ", $text); // no comment :)

    $text = do_clickable(htmlspecialchars_decode($text));
    //WindowMediaPlayer
    $text = preg_replace("#\[media:$uid\](.*?)\[/media:$uid\]#si", "<object id=\"WMP\" type=\"video/x-ms-asf\" data=\"video.asx\" src=\"\\1\" width=\"450\" height=\"350\"><param name=\"AutoStart\" value=\"0\"> <embed width=\"450\" height=\"350\" AutoStart=\"0\" src=\"\\1\" ShowTracker=\"true\" ShowControls=\"true\" ShowGotoBar=\"true\" ShowDisplay=\"true\" ShowStatusBar=\"true\" AutoSize=\"true\" pluginspage=\"https://www.microsoft.com/windows/windowsmedia/download/\"></embed></OBJECT>", $text);
    $text = str_replace("[/win:$uid]", "", $text);
    //youtube
    $text = preg_replace("#\[youtube:$uid\](.*?)\[/youtube:$uid\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"\\1\"></param><embed src=\"\\1\" type=\"application/x-shockwave-flash\" width=\"425\" height=\"350\"></embed></object>"."[/youtube:$uid]", $text);
    $text = str_replace("[/youtube:$uid]", "", $text);
    $text = str_replace("https://www.youtube.com/watch?v=", "https://www.youtube.com/v/", $text);
    $text = str_replace("https://www.youtube.com", "https://youtube.com", $text);
    $text = str_replace("https://www.youtube.com", "https://fr.youtube.com", $text);
    return $text;
}

function get_side(){
    global $user_id, $characters_db, $realm_id;
    $mysql2 = new SQL;
    $mysql2->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
    $result = $mysql2->query("SELECT race FROM  `characters` WHERE account = '$user_id';");
    if(!$mysql2->num_rows($result))
        return "NO";
    $a = 0; $h = 0;
    while($race = $mysql2->fetch_row($result))
    {
        if($race[0] == 1 || $race[0] == 3 || $race[0] == 4 || $race[0] == 7 || $race[0] == 11) 
            $a++;
        else if($race[0] == 2 || $race[0] == 5 || $race[0] == 6 || $race[0] == 8 || $race[0] == 10) 
            $h++;
        else 
            continue;
    }
    $mysql2->close();
    if($a != 0 && $h == 0)
        return "A";
    else if($a == 0 && $h != 0)
        return "H";
    else
        return "NO";
    $mysql2->close();
}

function gen_avatar_panel($level,$sex,$race,$class,$info=1,$gm=0){
    require_once("libs/char_lib.php");
    global $lang_index;

    $return = "<div border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\" background: transparent url(";

    $return .= char_get_avatar_img($level, $sex, $race, $class, $gm);

    $return .= ") repeat scroll 0%; width: 64px; height: 64px;\">";

    $return .= "<div style=\"background: transparent url(img/avatars/frame/full.gif) repeat scroll 0%; position:relative;left:0px;top:0px; width: 64px; height: 64px;\"></div>";
    $return .= "<div style=\"text-align:center;font-weight:bold;color:white;position:relative;left:21px;top:-18px; width: 24px; height: 24px;\">
  $level
  </div></div>";

    if($gm>0)
    {
        require_once("scripts/get_lib.php");
        $return .= id_get_gm_level($gm) . "<br />";
    }

    if($info == 1)
    {
        $return .= "<div style=\"margin-top:2px;\">
    <a href=\"#\" onmouseover=\"toolTip('{$lang_index["class"]} ".char_get_class_name($class)."','item_tooltip')\" onmouseout=\"toolTip()\">
    <img src=\"img/c_icons/$class.gif\" border=\"0\" alt=".char_get_class_name($class)." /></a>
    <a href=\"#\" onmouseover=\"toolTip('{$lang_index["race"]} ".char_get_race_name($race)."','item_tooltip')\" onmouseout=\"toolTip()\">
    <img src=\"img/c_icons/$race-$sex.gif\" border=\"0\" alt=".char_get_class_name($class)." /></a>";
    }

    $return .= "</div>";
    return $return;
}
?>
