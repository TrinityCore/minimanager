<?php


function bbcode_fonts()
{
    $bbcode_fonts =
        [
        0 => "Fonts",
        1 => "Arial",
        2 => "Book Antiqua",
        3 => "Century Gothic",
        4 => "Comic Sans MS",
        5 => "Courier New",
        6 => "Georgia",
        7 => "Harrington",
        8 => "Impact",
        9 => "Lucida Console",
        10=> "Microsoft Sans Serif",
        11=> "Tahoma",
        12=> "Times New Roman",
        13=> "Verdana",
        ];
    return $bbcode_fonts;
}


function bbcode_colors()
{
    $bbcode_colors =
        [
        0 => ["colors",  "Colors"],
        1 => ["white",   "White"],
        2 => ["silver",  "Silver"],
        3 => ["gray",    "Gray"],
        4 => ["yellow",  "Yellow"],
        5 => ["olive",   "Olive"],
        6 => ["maroon",  "Maroon"],
        7 => ["red",     "Red"],
        8 => ["purple",  "Purple"],
        9 => ["fuchsia", "Fuchsia"],
        10=> ["navy",    "Navy"],
        11=> ["blue",    "Blue"],
        12=> ["teal",    "Teal"],
        13=> ["aqua",    "Aqua"],
        14=> ["lime",    "Lime"],
        15=> ["green",   "Green"],
        ];
    return $bbcode_colors;
}


function bbcode_emoticons()
{
    $bbcode_emoticons =
        [
        0 => [":)",    "smile",   "15","15"],
        1 => [":D",    "razz",    "15","15"],
        2 => [";)",    "wink",    "15","15"],
        3 => ["8)",    "cool",    "15","15"],
        4 => [":(",    "sad",     "15","15"],
        5 => [":mad:", "angry",   "15","15"],
        6 => [":|",    "neutral", "15","15"],
        7 => ["=)",    "happy",   "15","15"],
        8 => [":´(",   "cry",     "15","15"],
        9 => [":?",    "hmm",     "15","15"],
        10=> [":]",    "roll",    "15","15"],
        11=> [":S",    "smm",     "15","15"],
        12=> [":P",    "tongue",  "15","15"],
        13=> [":O",    "yikes",   "15","15"],
        14=> [":lol:", "lol",     "15","15"],
        ];
    return $bbcode_emoticons;
}


function bbcode_add_editor()
{
  global $output;
  $bbcode_fonts = bbcode_fonts();
  $bbcode_colors = bbcode_colors();
  $bbcode_emoticons = bbcode_emoticons();
  $output .= "
        <script type=\"text/javascript\" src=\"libs/js/bbcode.js\"></script>
        <div style=\"display:block\">
          <select>
            <option>".$bbcode_fonts[0]."</option>";
  for($i=1;$i<count($bbcode_fonts);$i++)
  {
    $output .= "
            <option onclick=\"addbbcode('msg','font','{$bbcode_fonts[$i]}');\" style=\"font-family:'{$bbcode_fonts[$i]}';\">{$bbcode_fonts[$i]}</option>";
  }
  $output .= "
          </select>
          <select>
            <option>Size</option>";
  for($i=1;$i<8;$i++)
  {
    $output .= "
            <option onclick=\"addbbcode('msg','size','{$i}');\">{$i}</option>";
  }
  $output .= "
          </select>
          <select>
            <option>".$bbcode_colors[0][1]."</option>";
  for($i=1;$i<count($bbcode_colors);$i++)
  {
    $output .= "
            <option onclick=\"addbbcode('msg','color','{$bbcode_colors[$i][0]}');\" style=\"color:{$bbcode_colors[$i][0]};background-color:#383838;\">{$bbcode_colors[$i][1]}</option>";
  }
  $output .= "
          </select>
          <img src=\"img/editor/bold.gif\" onclick=\"addbbcode('msg','b')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/italic.gif\" onclick=\"addbbcode('msg','i')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/underline.gif\" onclick=\"addbbcode('msg','u')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/justifyleft.gif\" onclick=\"addbbcode('msg','left')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/justifycenter.gif\" onclick=\"addbbcode('msg','center')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/justifyright.gif\" onclick=\"addbbcode('msg','right')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/image.gif\" onclick=\"add_img('msg')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/link.gif\" onclick=\"add_url('msg')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/mail.gif\" onclick=\"add_mail('msg')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/code.gif\" onclick=\"addbbcode('msg','code')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
          <img src=\"img/editor/quote.gif\" onclick=\"add_quote('msg')\" width=\"21\" height=\"20\" style=\"cursor:pointer;\" alt=\"\" />
        </div>
        <div style=\"display:block;padding-top:5px;\">";
  for($i=0;$i<count($bbcode_emoticons);$i++)
  {
    $output .= "
          <img src=\"img/emoticons/{$bbcode_emoticons[$i][1]}.gif\" onclick=\"addText('msg','{$bbcode_emoticons[$i][0]}')\" width=\"{$bbcode_emoticons[$i][2]}\" height=\"{$bbcode_emoticons[$i][3]}\" style=\"cursor:pointer;padding:1px;\" alt=\"\" />";
  }
  $output .= "
        </div>";
}


function bbcode_bbc2html($text)
{
  $bbcode_emoticons = bbcode_emoticons();
  // By BlackWizard, https://www.phpcs.com/codes/BBCODE-SIMPLEMENT_17638.aspx
  $text = preg_replace("#\[img\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/img\]#si", "'<img src=\\1' . str_replace(' ', '%20', '\\3') . ' />'", $text);
  $text = preg_replace("#\[url=((ht|f)tp://)([^\r\n\t<\"]*?)\](.+?)\[\/url\]#si", "'<a href=\"\\1' . str_replace(' ', '%20', '\\3') . '\" target=blank>\\4</a>'", $text);
  $text = preg_replace("#\[url\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/url\]#si", "'<a href=\"\\1' . str_replace(' ', '%20', '\\3') . '\" target=blank>\\1\\3</a>'", $text);
  $text = preg_replace("#\[b\](.+?)\[\/b\]#si", "'<b>\\1</b>'", $text);
  $text = preg_replace("#\[i\](.+?)\[\/i\]#si", "'<i>\\1</i>'", $text);
  $text = preg_replace("#\[u\](.+?)\[\/u\]#si", "'<u>\\1</u>'", $text);
  $text = preg_replace("#\[h1\](.+?)\[\/h1\]#si", "'<h1>\\1</h1>'", $text);
  $text = preg_replace("#\[h2\](.+?)\[\/h2\]#si", "'<h2>\\1</h2>'", $text);
  $text = preg_replace("#\[code\](.+?)\[\/code\]#si", "'<br /><table class=\"flat\" width=90%><tr><th align=left style=\"background-color:#344;font-size:16px;\">#:</th></tr><tr><td align=left style=\"background-color:#333;\"><code>\\1</code></td></tr></table>'", $text);
  $text = preg_replace("#\[quote\](.+?)\[\/quote\]#si", "'<br /><table class=\"flat\" width=90%><tr><th align=left style=\"background-color:#443;font-size:16px;\">Cita :</th></tr><tr><td align=left style=\"background-color:#333;\">\\1</td></tr></table>'", $text);
  $text = preg_replace("#\[quote=(.+?)\](.+?)\[\/quote\]#si", "'<br /><table class=\"flat\" width=90%><tr><th align=left style=\"background-color:#443;font-size:16px;\">\\1 :</th></tr><tr><td align=left style=\"background-color:#333;>\\2</td></tr></table>'", $text);
  $text = preg_replace("#\[color=(.+?)\](.+?)\[\/color\]#si", "'<font color=\\1>\\2</font>'", $text);
  $text = preg_replace("#\[size=(.+?)\](.+?)\[\/size\]#si", "'<font size=\\1>\\2</font>'", $text);
  $text = preg_replace("#\[font=(.+?)\](.+?)\[\/font\]#si", "'<font face=\"\\1\">\\2</font>'", $text);
  $text = preg_replace("#\[left\](.+?)\[\/left\]#si", "'<p style=\"text-align:left;\">\\1</p>'", $text);
  $text = preg_replace("#\[right\](.+?)\[\/right\]#si", "'<p style=\"text-align:right;\">\\1</p>'", $text);
  $text = preg_replace("#\[center\](.+?)\[\/center\]#si", "'<center>\\1</center>'", $text);
  $text = preg_replace( "/([^\/=\"\]])((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>",  $text);
  $text = preg_replace('/([^\/=\"\]])(www\.)(\S+)/', '\\1<a href="https://\\2\\3" target="_blank">\\2\\3</a>', $text);
  $text = preg_replace('#\r\n#', '<br />', $text);
  $text = str_replace('#\r#', '<br />', $text);

  // Emoticons
  for($i=0;$i<count($bbcode_emoticons);$i++)
  {
    $text = preg_replace("#".preg_quote($bbcode_emoticons[$i][0])."#si", "'<img src=\"img/emoticons/{$bbcode_emoticons[$i][1]}.gif\" />'", $text);
  }
  $text = str_replace("&lt;br /&gt;", "<br />", $text);
  return $text;
}

?>
