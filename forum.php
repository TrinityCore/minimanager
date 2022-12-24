<?php


require_once("header.php");
require_once("scripts/forum.conf.php");
require_once("scripts/forum_lib.php");
valid_login($action_permission['read']);

if (isset($_COOKIE["lang"])){
  $forumlang = $_COOKIE["lang"];
  if (!file_exists("lang/$forumlang.php")) $forumlang = $language;
  } else $forumlang = $language;
require_once("lang/$forumlang.php");

foreach($forum_skeleton as $cid => $category){
  if(!isset($category["level_read"])) $forum_skeleton[$cid]["level_read"] = 0;
  if(!isset($category["level_post"])) $forum_skeleton[$cid]["level_post"] = 0;
  if(!isset($category["level_post_topic"])) $forum_skeleton[$cid]["level_post_topic"] = 0;
  if(!isset($category["side_access"])) $forum_skeleton[$cid]["side_access"] = "ALL";
  foreach($category["forums"] as $id => $forum){
    if(!isset($forum["level_read"])) $forum_skeleton[$cid]["forums"][$id]["level_read"] = 0;
    if(!isset($forum["level_post"])) $forum_skeleton[$cid]["forums"][$id]["level_post"] = 0;
    if(!isset($forum["level_post_topic"])) $forum_skeleton[$cid]["forums"][$id]["level_post_topic"] = 0;
    if(!isset($forum["side_access"])) $forum_skeleton[$cid]["forums"][$id]["side_access"] = "ALL";
  }
}

// #######################################################################################################
// Forum_Index : Display the forums in categories
// #######################################################################################################
function forum_index(){
  global $enablesidecheck, $forum_skeleton, $forum_lang, $user_lvl, $output, $realm_db, $mmfpm_db;
  if($enablesidecheck)
  $side = get_side();
    $mysql = new SQL;
    $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
    $result = $mysql->query("SELECT `authorname`,`id`,`name`,`time`,`forum` FROM `mm_forum_posts` WHERE `id` IN (SELECT MAX(`id`) FROM `mm_forum_posts` GROUP BY `forum`) ORDER BY `forum`;");
    $lasts = [];
    if($mysql->num_rows($result) > 0){
        while($row = $mysql->fetch_row($result))
            $lasts[$row[4]] = $row;
    }
    $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a></div><center><table class=\"lined\">";
    foreach($forum_skeleton as $category){    if(($category["level_read"] > $user_lvl))
      continue;
    if($user_lvl == 0 && $enablesidecheck){
      if($category["side_access"] != "ALL"){ // Not an all side forum
        if($side == "NO") // No char
          continue;
        else if($category["side_access"] != $side) // Forumside different of the user side
                    continue;
            }
        }
        $output .= "<tr><td class=\"head\" align=\"left\">".$category["name"]."</td>
                    <td class=\"head\">{$forum_lang["topics"]}</td>
                    <td class=\"head\">{$forum_lang["replies"]}</td>
                    <td class=\"head\" align=\"right\">".$forum_lang["last_post"]."</td></tr>";
        foreach($category["forums"] as $id => $forum){
            if($forum["level_read"] > $user_lvl)
                continue;     if($user_lvl == 0 && $enablesidecheck){
        if($forum["side_access"] != "ALL"){ // Not an all side forum
          if($side == "NO") // No char
            continue;
          else if($forum["side_access"] != $side) // Forumside different of the user side
                        continue;
                }
            }
            $totaltopics = $mysql->query("SELECT id FROM mm_forum_posts WHERE forum = '$id' AND id = `topic`;");
            $numtopics = $mysql->num_rows($totaltopics);
            $totalreplies = $mysql->query("SELECT id FROM mm_forum_posts WHERE forum = '$id';");
            $numreplies = $mysql->num_rows($totalreplies);
            $output .= "<tr><td align=\"left\"><a href=\"forum.php?action=view_forum&amp;id=$id\">{$forum["name"]}</a><br />{$forum["desc"]}</td>
                        <td>{$numtopics}</td>
                        <td>{$numreplies}</td>";
            if(isset($lasts[$id])){
                $lasts[$id][2] = htmlspecialchars($lasts[$id][2]);
                $output .= "<td align=\"right\"><a href=\"forum.php?action=view_topic&amp;postid={$lasts[$id][1]}\">{$lasts[$id][2]}</a><br />by {$lasts[$id][0]} <br /> {$lasts[$id][3]} </td></tr>";
            }
            else{
                $output .= "<td align=\"right\">{$forum_lang["no_topics"]}</td></tr>";
                }
    }
  }
  $output .= "<tr><td align=\"right\" class=\"hidden\"></td></tr></table></center><br/>";
  $mysql->close();
  // Queries : 1
}

// #######################################################################################################
//
// #######################################################################################################
function forum_view_forum(){
  global $enablesidecheck, $forum_skeleton, $forum_lang, $maxqueries, $user_lvl, $output, $mmfpm_db;
  if($enablesidecheck) $side = get_side();
  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);
  if(!isset($_GET["page"])) $page = 0;
  else $page = $mysql->quote_smart($_GET["page"]);
  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum){
      if($fid == $id) $cat = $cid;
    }
  }
  if(empty($forum_skeleton[$cat]["forums"][$id])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum = $forum_skeleton[$cat]["forums"][$id];
  if(($forum_skeleton[$cat]["level_read"] > $user_lvl) || ($forum["level_read"] > $user_lvl)){
      error($forum_lang["no_access"]);
	  return;
  }

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        return;
    }
    if($forum["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum["side_access"] != $side) // Forumside different of the user side
        return;
    }
  }

  $start = ($maxqueries * $page);
  $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a> -> <a href=\"forum.php?action=view_forum&amp;id={$id}\">{$forum["name"]}</a></div>
        <center><table class=\"lined\">";
  $topics = $mysql->query("SELECT id, authorid, authorname, name, annouced, sticked, closed FROM mm_forum_posts WHERE (forum = '$id' AND id = `topic`) OR annouced = 1 AND id = `topic` ORDER BY annouced DESC, sticked DESC, lastpost DESC LIMIT $start, $maxqueries;");
  $result = $mysql->query("SELECT `topic` as curtopic,(SELECT count(`id`)-1 FROM mm_forum_posts WHERE `topic` = `curtopic`) AS replies,lastpost as curlastpost,(SELECT authorname FROM mm_forum_posts WHERE id = curlastpost) as authorname,(SELECT time FROM mm_forum_posts WHERE id = curlastpost) as time FROM `mm_forum_posts` WHERE (`forum` = $id AND `topic` = `id` ) OR annouced = 1;");
  $lasts = [];
  if($mysql->num_rows($result) > 0){
    while($row = $mysql->fetch_row($result))
      $lasts[$row[0]] = $row;
  }
  if($forum_skeleton[$cat]["level_post_topic"] <= $user_lvl && $forum["level_post_topic"] <= $user_lvl)
    $output .= "<tr><td colspan=\"4\" style=\"text-align:right;\"><a href=\"forum.php?action=add_topic&amp;id={$id}\">{$forum_lang["new_topic"]}</a></td></tr>";
  if($mysql->num_rows($topics)!=0){
    $output .= "<tr>
      <td style=\"width:35%;text-align:left;\">{$forum_lang["title"]}</td>
      <td style=\"width:15%;\">{$forum_lang["author"]}</td>
      <td>{$forum_lang["replies"]}</td>
      <td>{$forum_lang["last_post"]}</td>
    </tr>";
    while($topic = $mysql->fetch_row($topics)){
      $output .= "<tr>
              <td style=\"text-align:left;\">";
              if($topic[4]=="1")
                $output .= "{$forum_lang["annoucement"]} : ";
              else{
                if($topic[5]=="1")
                  $output .= "{$forum_lang["sticky"]} : ";
                else{
                  if($topic[6]=="1")
                    $output .= "[{$forum_lang["closed"]}] ";
                }
              }
              $topic[3] = htmlspecialchars($topic[3]);
              $output .= "<a href=\"forum.php?action=view_topic&amp;id={$topic[0]}\">{$topic[3]}</a></td><td>{$topic[2]}</td>
              <td>{$lasts[$topic[0]][1]}</td>
              <td>{$forum_lang["last_post_by"]} {$lasts[$topic[0]][3]}, {$lasts[$topic[0]][4]}</td>
            </tr>";
    }
    $totaltopics = $mysql->query("SELECT id FROM mm_forum_posts WHERE forum = '$id' AND id = `topic`;"); //My page system is so roxing, i can' t break this query xD
    $pages = ceil($mysql->num_rows($totaltopics)/$maxqueries);
    $output .= "<tr><td align=\"right\" colspan=\"4\">{$forum_lang["pages"]} : ";
    for($x = 1; $x <= $pages; $x++){
      $y = $x-1;
      $output .= "<a href=\"forum.php?action=view_forum&amp;id=$id&amp;page=$y\">$x</a> ";
    }
    $output .= "</td></tr>";
  }
  else
    $output .= "<tr><td>{$forum_lang["no_topics"]}</td></tr>";
  $mysql->close();
  $output .= "<tr><td align=\"right\" class=\"hidden\"></td></tr></table></center><br/>";
  // Queries : 3
}
// #######################################################################################################
//
// #######################################################################################################
function forum_view_topic(){

  global $enablesidecheck, $forum_skeleton, $forum_lang, $maxqueries, $user_lvl, $user_id, $output, $realm_db, $characters_db, $realm_id, $mmfpm_db;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if(isset($_GET["id"])){
    $id = $mysql->quote_smart($_GET["id"]);
    $post = false;
  }
  else{
    if(isset($_GET["postid"])){
      $id = $mysql->quote_smart($_GET["postid"]);
      $post = true;
    }
    else{
		error($forum_lang["no_such_topic"]);
		return;
	}
  }


  if(!isset($_GET["page"])) $page = 0;
  else $page = $mysql->quote_smart($_GET["page"]); // Fok you mathafoker haxorz
  $start = ($maxqueries * $page);

  if(!$post){
    $posts = $mysql->query("SELECT id,authorid,authorname,forum,name,text,time,annouced,sticked,closed FROM mm_forum_posts WHERE topic = '$id' ORDER BY id ASC LIMIT $start, $maxqueries;");

// Thx qsa for the query structure

    $link = $mysql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

$query = "SELECT account,name,gender,race,class,
 level,(SELECT SecurityLevel FROM `{$realm_db['name']}`.account_access WHERE `{$realm_db['name']}`.account_access.AccountID = `{$characters_db[$realm_id]['name']}`.characters.account) as SecurityLevel
FROM `{$characters_db[$realm_id]['name']}`.characters WHERE totaltime IN ( SELECT MAX(totaltime) FROM `{$characters_db[$realm_id]['name']}`.characters WHERE account IN (";
while($post = $mysql->fetch_row($posts)){
  $query .= "$post[1],";
}
$mysql->data_seek($posts,0);
$query .= "0) GROUP BY account);";
    $link = $mysql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
    $results = $mysql->query($query);

    while($avatar = $mysql->fetch_row($results)){
      $char_gender = str_pad(dechex($avatar[2]),8, 0, STR_PAD_LEFT);
      $avatars[$avatar[0]]["name"] = $avatar[1];
      $avatars[$avatar[0]]["sex"] = $char_gender[3];
      $avatars[$avatar[0]]["race"] = $avatar[3];
      $avatars[$avatar[0]]["class"] = $avatar[4];
      $avatars[$avatar[0]]["level"] = $avatar[5];
      $avatars[$avatar[0]]["gm"] = $avatar[6];
    }

//    $link = $mysql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $replies = $mysql->num_rows($posts);
    if($replies==0){
        error($forum_lang["no_such_topic"]);
		return;
	}
    $post = $mysql->fetch_row($posts);
    $fid = $post[3];
    $cat = 0;
    foreach($forum_skeleton as $cid => $category){
      foreach($category["forums"] as $fid_ => $forum){
        if($fid_ == $fid) $cat = $cid;
      }
    }
    if(empty($forum_skeleton[$cat]["forums"][$fid])){
        error($forum_lang["no_such_forum"]);
		return;
	}
    $forum = $forum_skeleton[$cat]["forums"][$fid];
    if($forum_skeleton[$cat]["level_read"] > $user_lvl || $forum["level_read"] > $user_lvl){
		error($forum_lang["no_access"]);
		return;
	}

    if($user_lvl == 0 && $enablesidecheck){
      if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
        if($side == "NO") // No char
          return;
        else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
          return;
      }
      if($forum["side_access"] != "ALL"){ // Not an all side forum
        if($side == "NO") // No char
          return;
        else if($forum["side_access"] != $side) // Forumside different of the user side
          return;
      }
    }

    $post[4] = htmlspecialchars($post[4]);
    $post[5] = htmlspecialchars($post[5]);
    $post[5] = bbcode_parse($post[5]);

    $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a> -> <a href=\"forum.php?action=view_forum&amp;id={$fid}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$id}\">{$post[4]}</a></div>
          <center><table class=\"lined\">
          <tr>
            <td style=\"width:15%;\">{$forum_lang["info"]}</td>
            <td style=\"text-align:left;\">{$forum_lang["text"]}</td>
            <td style=\"width:50%;text-align:right;\">";
            if($user_lvl > 0)
            {
              if($post[8]=="1"){
                if($post[7]=="1"){
                  // Annoucement
                  $output .= "{$forum_lang["annoucement"]}
                  <a href=\"forum.php?action=edit_announce&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/down.gif\" border=\"0\" alt=\"{$forum_lang["down"]}\" /></a>";
                }
                else{
                  // Sticky
                  $output .= "{$forum_lang["sticky"]}
                  <a href=\"forum.php?action=edit_stick&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/down.gif\" border=\"0\" alt=\"{$forum_lang["down"]}\" /></a>
                  <a href=\"forum.php?action=edit_announce&amp;id={$post[0]}&amp;state=1\"><img src=\"img/forums/up.gif\" border=\"0\" alt=\"{$forum_lang["up"]}\" /></a>";
                }
              }
              else{
                if($post[7]=="1"){
                  // Annoucement
                  $output .= "{$forum_lang["annoucement"]}
                  <a href=\"forum.php?action=edit_announce&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/down.gif\" border=\"0\" alt=\"{$forum_lang["down"]}\" /></a>";
                }
                else{
                  // Normal Topic
                  $output .= "{$forum_lang["normal"]}
                  <a href=\"forum.php?action=edit_stick&amp;id={$post[0]}&amp;state=1\"><img src=\"img/forums/up.gif\" border=\"0\" alt=\"{$forum_lang["up"]}\" /></a>";

                }
              }

              if($post[9]=="1")
                $output .= " <a href=\"forum.php?action=edit_close&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/lock.gif\" border=\"0\" alt=\"{$forum_lang["open"]}\" /></a>";
              else
                $output .= " <a href=\"forum.php?action=edit_close&amp;id={$post[0]}&amp;state=1\"><img src=\"img/forums/unlock.gif\" border=\"0\" alt=\"{$forum_lang["close"]}\" /></a>";
              $output .= " <a href=\"forum.php?action=move_topic&amp;id={$post[0]}\"><img src=\"img/forums/move.gif\" border=\"0\" alt=\"{$forum_lang["move"]}\" /></a>";
            }
            if(isset($avatars[$post[1]]))
              $avatar = gen_avatar_panel(
                $avatars[$post[1]]["level"],
                $avatars[$post[1]]["sex"],
                $avatars[$post[1]]["race"],
                $avatars[$post[1]]["class"],1,
                $avatars[$post[1]]["gm"]);
            else
              $avatar = "";
            $output .= "<tr><td style=\"width:15%;text-align:center;\"><center>$avatar</center>{$forum_lang["author"]} : ";
            if($user_lvl > 0)
              $output .= "<a href=\"user.php?action=edit_user&error=11&id={$post[1]}\">";
            if(isset($avatars[$post[1]]))
              $output .= $avatars[$post[1]]["name"];
            else
              $output .= $post[2];
            if($user_lvl > 0)
              $output .= "</a>";
            $output .= "<br /> {$forum_lang["at"]} : {$post[6]}</td>
            <td colspan=\"2\" style=\"text-align:left\">{$post[5]}<br /><div style=\"text-align:right;\">";
            if($user_lvl > 0 || $user_id == $post[1])
              $output .= "<a href=\"forum.php?action=edit_post&amp;id={$post[0]}\"><img src=\"img/forums/edit.gif\" border=\"0\" alt=\"{$forum_lang["edit"]}\" /></a>
               <a href=\"forum.php?action=delete_post&amp;id={$post[0]}\"><img src=\"img/forums/delete.gif\" border=\"0\" alt=\"{$forum_lang["delete"]}\" /></a>";
          $output .= "</div></td></tr>";
          $closed = $post[9];

    while($post = $mysql->fetch_row($posts)){
          $post[5] = htmlspecialchars($post[5]);
          $post[5] = bbcode_parse($post[5]);

            if(isset($avatars[$post[1]]))
              $avatar = gen_avatar_panel(
                $avatars[$post[1]]["level"],
                $avatars[$post[1]]["sex"],
                $avatars[$post[1]]["race"],
                $avatars[$post[1]]["class"],1,
                $avatars[$post[1]]["gm"]);
            else
              $avatar = "";
            $output .= "<tr><td style=\"width:15%;text-align:center;\"><center>$avatar</center>{$forum_lang["author"]} : ";
            if($user_lvl > 0)
              $output .= "<a href=\"user.php?action=edit_user&error=11&id={$post[1]}\">";
            if(isset($avatars[$post[1]]))
              $output .= $avatars[$post[1]]["name"];
            else
              $output .= $post[2];
            if($user_lvl > 0)
              $output .= "</a>";
            $output .= "<br /> {$forum_lang["at"]} : {$post[6]}</td>
            <td colspan=\"2\" style=\"text-align:left;\">{$post[5]}<br />";
            if($user_lvl > 0 || $user_id == $post[1])
              $output .= "<div style=\"text-align:right;\"><a href=\"forum.php?action=edit_post&amp;id={$post[0]}\"><img src=\"img/forums/edit.gif\" border=\"0\" alt=\"{$forum_lang["edit"]}\" /></a>
               <a href=\"forum.php?action=delete_post&amp;id={$post[0]}\"><img src=\"img/forums/delete.gif\" border=\"0\" alt=\"{$forum_lang["delete"]}\" /></a></div>";
          $output .= "</td></tr>";
    }

    $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    $totalposts = $mysql->query("SELECT id FROM mm_forum_posts WHERE topic = '$id';");
    $totalposts = $mysql->num_rows($totalposts);

    $pages = ceil($totalposts/$maxqueries);
    $output .= "<tr><td align=\"right\" colspan=\"3\">{$forum_lang["pages"]} : ";
    for($x = 1; $x <= $pages; $x++){
      $y = $x-1;
      $output .= "<a href=\"forum.php?action=view_topic&amp;id=$id&amp;page=$y\">$x</a> ";
    }
    $output .= "</td></tr><tr><td align=\"right\" class=\"hidden\"></td></tr></table>";

    // Quick reply form
    if((($user_lvl > 0)||!$closed)&&($forum_skeleton[$cat]["level_post"] <= $user_lvl && $forum["level_post"] <= $user_lvl)
    ){
      $output .= "<form action=\"forum.php?action=do_add_post\" method=\"POST\" name=\"form\">
      <table class=\"top_hidden\">
      <tr>
      <td align=\"left\">";
      makebutton($forum_lang["post"], "javascript:do_submit()",100);
      $output .= "</td><td align=\"right\">{$forum_lang["quick_reply"]}</td></tr>
      <tr><td colspan=\"2\">".bbcode_editor_js()."
        <a href=\"javascript:ajtBBCode('[b]','[/b]')\">{$forum_lang["bold"]}</a>,
        <a href=\"javascript:ajtBBCode('[i]','[/i]')\">{$forum_lang["italic"]}</a>,
        <a href=\"javascript:ajtBBCode('[u]','[/u]')\">{$forum_lang["underline"]}</a>,
        <a href=\"javascript:ajtBBCode('[img]','[/img]')\">{$forum_lang["image"]}</a>,
        <a href=\"javascript:ajtBBCode('[url]','[/url]')\">{$forum_lang["url"]}</a>,
        <a href=\"javascript:ajtBBCode('[url=Click here]','[/url]')\">{$forum_lang["url2"]}</a>,
        <a href=\"javascript:ajtBBCode('[code]','[/code]')\">{$forum_lang["code"]}</a>,
        <a href=\"javascript:ajtBBCode('[quote]','[/quote]')\">{$forum_lang["quote"]}</a>,
        <a href=\"javascript:ajtBBCode('[quote=Someone]','[/quote]')\">{$forum_lang["quote2"]}</a>,
        <a href=\"javascript:ajtBBCode('[media]','[/media]')\">{$forum_lang["media"]}</a>
                <a href=\"javascript:ajtBBCode('[youtube]','[/youtube]')\">{$forum_lang["YouTube"]}</a>
        {$forum_lang["color"]} : <select name=\"fontcolor\" onChange=\"ajtBBCode('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]'); this.selectedIndex=0;\" onMouseOver=\"helpline('fontcolor')\" style=\"background-color:#D7D7D7\">
          <option value=\"black\" style=\"color:black\">Black</option>
          <option value=\"silver\" style=\"color:silver\">Silver</option>
          <option value=\"gray\" style=\"color:gray\">Gray</option>
          <option value=\"maroon\" style=\"color:maroon\">Maroon</option>
          <option value=\"red\" style=\"color:red\">Red</option>
          <option value=\"purple\" style=\"color:purple\">Purple</option>
          <option value=\"fuchsia\" style=\"color:fuchsia\">Fuchsia</option>
          <option value=\"navy\" style=\"color:navy\">Navy</option>
          <option value=\"blue\" style=\"color:blue\">Blue</option>
          <option value=\"aqua\" style=\"color:aqua\">Aqua</option>
          <option value=\"teal\" style=\"color:teal\">Teal</option>
          <option value=\"lime\" style=\"color:lime\">Lime</option>
          <option value=\"green\" style=\"color:green\">Green</option>
          <option value=\"olive\" style=\"color:olive\">Olive</option>
          <option value=\"yellow\" style=\"color:yellow\">Yellow</option>
          <option value=\"white\" style=\"color:white\">White</option>
        </select>
        </td></tr><tr><td colspan=\"2\">
        <a href=\"javascript:ajtTexte(':)')\"><img style=\"border:0px;\" src=\"img/emoticons/smile.gif\"></a><a href=\"javascript:ajtTexte(':|')\"><img style=\"border:0px;\" src=\"img/emoticons/neutral.gif\"></a><a href=\"javascript:ajtTexte(':(')\"><img style=\"border:0px;\" src=\"img/emoticons/sad.gif\"></a><a href=\"javascript:ajtTexte(':D')\"><img style=\"border:0px;\" src=\"img/emoticons/razz.gif\"></a><a href=\"javascript:ajtTexte(':o')\"><img style=\"border:0px;\" src=\"img/emoticons/yikes.gif\"></a><a href=\"javascript:ajtTexte(';)')\"><img style=\"border:0px;\" src=\"img/emoticons/wink.gif\"></a><a href=\"javascript:ajtTexte(':/')\"><img style=\"border:0px;\" src=\"img/emoticons/hmm.gif\" /></a><a href=\"javascript:ajtTexte(':p')\"><img style=\"border:0px;\" src=\"img/emoticons/tongue.gif\"></a><a href=\"javascript:ajtTexte(':lol:')\"><img style=\"border:0px;\" src=\"img/emoticons/lol.gif\"></a><a href=\"javascript:ajtTexte(':mad:')\"><img style=\"border:0px;\" src=\"img/emoticons/angry.gif\"></a><a href=\"javascript:ajtTexte(':rolleyes:')\"><img style=\"border:0px;\" src=\"img/emoticons/roll.gif\"></a><a href=\"javascript:ajtTexte(':cool:')\"><img style=\"border:0px;\" src=\"img/emoticons/cool.gif\"></a>
        </td></tr></table><TEXTAREA NAME=\"msg\" ROWS=8 COLS=93></TEXTAREA><br/>
      <input type=\"hidden\" name=\"forum\" value=\"$fid\" />
      <input type=\"hidden\" name=\"topic\" value=\"$id\" />
      </form>";
    }

    $output .= "</center>";
    $mysql->close();
  }
  else{
    $output .= "<div class=\"top\"><h1>Stand by...</h1></div>";

    $post = $mysql->query("SELECT topic, id FROM mm_forum_posts WHERE id = '$id'"); // Get our post id
    if($mysql->num_rows($post)==0){
        error($forum_lang["no_such_topic"]);
		return;
	}
    $post = $mysql->fetch_row($post);
    if($post[0]==$post[1])
      redirect("forum.php?action=view_topic&id=$id");
    $topic = $post[0];
    $posts = $mysql->query("SELECT id FROM mm_forum_posts WHERE topic = '$topic';"); // Get posts in our topic
    $replies = $mysql->num_rows($posts);
    if($replies==0){
        error($forum_lang["no_such_topic"]);
		return;
	}
    $row = 0;
    while($post = $mysql->fetch_row($posts)){ // Find the row of our post, so we could have his ratio (topic x/total topics) and knew the page to show
      $row++;
      if($topic==$id) break;
    }
    $page = 0;
    while(($page * $maxqueries) < $row){
      $page++;
    };
    $page--;
    $mysql->close();
    redirect("forum.php?action=view_topic&id=$topic&page=$page");
  }
  // Queries : 2 with id || 2 (+2) with postid
}
function forum_do_edit_close(){
  global $forum_lang, $user_lvl, $mmfpm_db;
  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if($user_lvl == 0){
      error($forum_lang["no_access"]);
	  return;
  }

  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_topic"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  if(!isset($_GET["state"])){
	  error("Bad request, please mail admin and describe what you did to get this error.");
	  return;
  }
  else $state = $mysql->quote_smart($_GET["state"]);

  $mysql->query("UPDATE mm_forum_posts SET closed = '$state' WHERE id = '$id'");
  $mysql->close();
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}
function forum_do_edit_announce(){
  global $forum_lang, $user_lvl, $mmfpm_db;
  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if($user_lvl == 0){
      error($forum_lang["no_access"]);
	  return;
  }

  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_topic"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  if(!isset($_GET["state"])){
	  error("Bad request, please mail admin and describe what you did to get this error.");
	  return;
  }
  else $state = $mysql->quote_smart($_GET["state"]);

  $mysql->query("UPDATE mm_forum_posts SET annouced = '$state' WHERE id = '$id'");
  $mysql->close();
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}
function forum_do_edit_stick(){
  global $forum_lang, $user_lvl, $mmfpm_db;
  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if($user_lvl == 0){
      error($forum_lang["no_access"]);
	  return;
  }

  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_topic"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  if(!isset($_GET["state"])){
	  error("Bad request, please mail admin and describe what you did to get this error.");
	  return;
  }
  else $state = $mysql->quote_smart($_GET["state"]);

  $mysql->query("UPDATE mm_forum_posts SET sticked = '$state' WHERE id = '$id'");
  $mysql->close();
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}
function forum_delete_post(){
  global $enablesidecheck, $forum_skeleton, $forum_lang, $maxqueries, $user_lvl, $user_id, $output, $mmfpm_db;
  $mysql = new SQL;

  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_post"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  $topic = $mysql->query("SELECT id,topic,authorid,forum FROM mm_forum_posts WHERE id = '$id';");
  if($mysql->num_rows($topic)==0){
	  error($forum_lang["no_such_post"]);
	  return;
  }
  $topic = $mysql->fetch_row($topic);
  if($user_lvl == 0 && $topic[2] != $user_id){
	  error($forum_lang["no_access"]);
	  return;
  }
  $fid = $topic[3];

  $topic2 = $mysql->query("SELECT name FROM mm_forum_posts WHERE id = '{$topic[1]}';");
  $name = $mysql->fetch_row($topic2);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ == $fid) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$fid])){ // No such forum..
      error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum = $forum_skeleton[$cat]["forums"][$fid];
  $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a> -> <a href=\"forum.php?action=view_forum&amp;id={$fid}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$topic[1]}\">{$name[0]}</a> -> {$forum_lang["delete"]}!</div><center><table class=\"lined\">";
  if($topic[0]==$topic[1])
    $output .= "<tr><td>{$forum_lang["delete_topic"]}</td></tr></table><table class=\"hidden\"><tr><td>";
  else
    $output .= "<tr><td>{$forum_lang["delete_post"]}</td></tr></table><table class=\"hidden\"><tr><td>";
  makebutton($forum_lang["back"], "javascript:window.history.back()", 120);
  makebutton($forum_lang["confirm"], "forum.php?action=do_delete_post&amp;id={$topic[0]}", 120);
  $output .= "</td></tr></table></center>";
  $mysql->close();
  // Queries : 1
}
function forum_do_delete_post(){
  global $forum_lang, $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $mmfpm_db;

  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_post"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  $topic = $mysql->query("SELECT id,topic,name,authorid,forum FROM mm_forum_posts WHERE id = '$id';");
  if($mysql->num_rows($topic)==0){
	  error($forum_lang["no_such_post"]);
	  return;
  }
  $topic = $mysql->fetch_row($topic);
  if($user_lvl == 0 && $topic[3] != $user_id){
	  error($forum_lang["no_access"]);
	  return;
  }
  $fid = $topic[4];

  if($id==$topic[1]){
    $mysql->query("DELETE FROM mm_forum_posts WHERE topic = '$id'");
    redirect("forum.php?action=view_forum&id=$fid");
  }
  else
  {
    $mysql->query("DELETE FROM mm_forum_posts WHERE id = '$id'");
    $result = $mysql->query("SELECT id FROM mm_forum_posts WHERE topic = '{$topic[1]}' ORDER BY id DESC LIMIT 1;"); // get last post id
    $lastpostid = $mysql->fetch_row($result);
    $lastpostid = $lastpostid[0];
    $mysql->query("UPDATE mm_forum_posts SET lastpost = '$lastpostid' WHERE id = '{$topic[1]}'"); // update topic' s last post id
    redirect("forum.php?action=view_topic&id={$topic[1]}");
  }
  // Queries : 1 (if delete topic) || 4 if delete post
}

function forum_add_topic(){
  global $enablesidecheck, $forum_lang, $forum_skeleton, $maxqueries, $minfloodtime, $user_lvl, $user_id, $output, $mmfpm_db;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)
  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if($minfloodtime > 0)
  {
    $userposts = $mysql->query("SELECT time FROM mm_forum_posts WHERE authorid = '$user_id' ORDER BY id DESC LIMIT 1;");
    if($mysql->num_rows($userposts) != 0)
    {
      $mintimeb4post = $mysql->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if($mintimeb4post < $minfloodtime){
          error($forum_lang["please_wait"]);
		  return;
	  }
    }
  }

  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum){
      if($fid == $id) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$id])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum = $forum_skeleton[$cat]["forums"][$id];
  if($forum_skeleton[$cat]["level_post_topic"] > $user_lvl || $forum["level_post_topic"] > $user_lvl){
	  error($forum_lang["no_access"]);
	  return;
  }

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        return;
    }
    if($forum["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum["side_access"] != $side) // Forumside different of the user side
        return;
    }
  }


  $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a> -> <a href=\"forum.php?action=view_forum&amp;id={$id}\">{$forum["name"]}</a> -> {$forum_lang["new_topic"]}</div><center><table class=\"lined\">";

  $output .= "<form action=\"forum.php?action=do_add_topic\" method=\"POST\" name=\"form\"><table class=\"top_hidden\"><tr><td align=\"left\">";
  makebutton("Post", "javascript:do_submit()",100);
  $output .= "</td><td align=\"right\">{$forum_lang["topic_name"]}: <input name=\"name\" SIZE=\"40\"></td></tr>
  <tr><td colspan=\"2\">".bbcode_editor_js()."
        <a href=\"javascript:ajtBBCode('[b]','[/b]')\">{$forum_lang["bold"]}</a>,
        <a href=\"javascript:ajtBBCode('[i]','[/i]')\">{$forum_lang["italic"]}</a>,
        <a href=\"javascript:ajtBBCode('[u]','[/u]')\">{$forum_lang["underline"]}</a>,
        <a href=\"javascript:ajtBBCode('[img]','[/img]')\">{$forum_lang["image"]}</a>,
        <a href=\"javascript:ajtBBCode('[url]','[/url]')\">{$forum_lang["url"]}</a>,
        <a href=\"javascript:ajtBBCode('[url=Click here]','[/url]')\">{$forum_lang["url2"]}</a>,
        <a href=\"javascript:ajtBBCode('[code]','[/code]')\">{$forum_lang["code"]}</a>,
        <a href=\"javascript:ajtBBCode('[quote]','[/quote]')\">{$forum_lang["quote"]}</a>,
        <a href=\"javascript:ajtBBCode('[quote=Someone]','[/quote]')\">{$forum_lang["quote2"]}</a>,
        <a href=\"javascript:ajtBBCode('[media]','[/media]')\">{$forum_lang["media"]}</a>
                <a href=\"javascript:ajtBBCode('[youtube]','[/youtube]')\">{$forum_lang["YouTube"]}</a>
        {$forum_lang["color"]} : <select name=\"fontcolor\" onChange=\"ajtBBCode('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]'); this.selectedIndex=0;\" onMouseOver=\"helpline('fontcolor')\" style=\"background-color:#D7D7D7\">
          <option value=\"black\" style=\"color:black\">Black</option>
          <option value=\"silver\" style=\"color:silver\">Silver</option>
          <option value=\"gray\" style=\"color:gray\">Gray</option>
          <option value=\"maroon\" style=\"color:maroon\">Maroon</option>
          <option value=\"red\" style=\"color:red\">Red</option>
          <option value=\"purple\" style=\"color:purple\">Purple</option>
          <option value=\"fuchsia\" style=\"color:fuchsia\">Fuchsia</option>
          <option value=\"navy\" style=\"color:navy\">Navy</option>
          <option value=\"blue\" style=\"color:blue\">Blue</option>
          <option value=\"aqua\" style=\"color:aqua\">Aqua</option>
          <option value=\"teal\" style=\"color:teal\">Teal</option>
          <option value=\"lime\" style=\"color:lime\">Lime</option>
          <option value=\"green\" style=\"color:green\">Green</option>
          <option value=\"olive\" style=\"color:olive\">Olive</option>
          <option value=\"yellow\" style=\"color:yellow\">Yellow</option>
          <option value=\"white\" style=\"color:white\">White</option>
        </select>
        </td></tr><tr><td colspan=\"2\">
        <a href=\"javascript:ajtTexte(':)')\"><img style=\"border:0px;\" src=\"img/emoticons/smile.gif\"></a><a href=\"javascript:ajtTexte(':|')\"><img style=\"border:0px;\" src=\"img/emoticons/neutral.gif\"></a><a href=\"javascript:ajtTexte(':(')\"><img style=\"border:0px;\" src=\"img/emoticons/sad.gif\"></a><a href=\"javascript:ajtTexte(':D')\"><img style=\"border:0px;\" src=\"img/emoticons/razz.gif\"></a><a href=\"javascript:ajtTexte(':o')\"><img style=\"border:0px;\" src=\"img/emoticons/yikes.gif\"></a><a href=\"javascript:ajtTexte(';)')\"><img style=\"border:0px;\" src=\"img/emoticons/wink.gif\"></a><a href=\"javascript:ajtTexte(':/')\"><img style=\"border:0px;\" src=\"img/emoticons/hmm.gif\" /></a><a href=\"javascript:ajtTexte(':p')\"><img style=\"border:0px;\" src=\"img/emoticons/tongue.gif\"></a><a href=\"javascript:ajtTexte(':lol:')\"><img style=\"border:0px;\" src=\"img/emoticons/lol.gif\"></a><a href=\"javascript:ajtTexte(':mad:')\"><img style=\"border:0px;\" src=\"img/emoticons/angry.gif\"></a><a href=\"javascript:ajtTexte(':rolleyes:')\"><img style=\"border:0px;\" src=\"img/emoticons/roll.gif\"></a><a href=\"javascript:ajtTexte(':cool:')\"><img style=\"border:0px;\" src=\"img/emoticons/cool.gif\"></a>
        </td></tr></table><TEXTAREA NAME=\"msg\" ROWS=8 COLS=93></TEXTAREA>
  <input type=\"hidden\" name=\"forum\" value=\"$id\" /></form>";
  $output .= "</center><br/>";
  $mysql->close();
  // Queries : 1
}
function forum_do_add_topic(){
  global $enablesidecheck, $forum_skeleton, $forum_lang, $user_lvl, $user_name, $user_id, $mmfpm_db, $minfloodtime;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

if($minfloodtime > 0)
  {
    $userposts = $mysql->query("SELECT time FROM mm_forum_posts WHERE authorid = '$user_id' ORDER BY id DESC LIMIT 1;");
    if($mysql->num_rows($userposts) != 0)
    {
      $mintimeb4post = $mysql->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if($mintimeb4post < $minfloodtime){
        error($forum_lang["please_wait"]);
		return;
	  }
    }
  }

  if(!isset($_POST['forum'])) {
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  else $forum = $mysql->quote_smart($_POST['forum']);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum_){
      if($fid == $forum) $cat = $cid;
    }
  }
  if(empty($forum_skeleton[$cat]["forums"][$forum])) {
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum_ = $forum_skeleton[$cat]["forums"][$forum];
  
  if($forum_skeleton[$cat]["level_post_topic"] > $user_lvl || $forum_["level_post_topic"] > $user_lvl) {
	  error($forum_lang["no_access"]);
	  return;
  }

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        return;
    }
    if($forum_["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum_["side_access"] != $side) // Forumside different of the user side
        return;
    }
  }

//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
  $msg = trim($mysql->quote_smart($_POST['msg']), " ");
//  $_POST['name'] = htmlspecialchars($_POST['name']);
  $name = trim($mysql->quote_smart($_POST['name']), " ");

  if (strlen($name) > 49){
    $mysql->close();
    error($forum_lang["name_too_long"]);
	return;
  }

  if (strlen($name) < 5){
	$mysql->close();
    error($forum_lang["name_too_short"]);
	return;
  }
  
  $msg = str_replace('\n', '<br />', $msg);
  $msg = str_replace('\r', '<br />', $msg);

  if (strlen($msg) < 5){
    $mysql->close();
    error($forum_lang["msg_too_short"]);
	return;
  }

  $time = date("m/d/y H:i:s");

  $mysql->query("INSERT INTO mm_forum_posts (authorid, authorname, forum, name, text, time) VALUES ('$user_id', '$user_name', '$forum', '$name', '$msg', '$time');");
  $id = $mysql->insert_id();
  $mysql->query("UPDATE mm_forum_posts SET topic = '$id', lastpost = '$id' WHERE id = '$id';");

  $mysql->close();

  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 3
}
function forum_do_add_post(){
  global $enablesidecheck, $forum_skeleton, $forum_lang, $minfloodtime, $user_lvl, $user_name, $user_id, $mmfpm_db;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if($minfloodtime > 0)
  {
    $userposts = $mysql->query("SELECT time FROM mm_forum_posts WHERE authorid = '$user_id' ORDER BY id DESC LIMIT 1;");
    if($mysql->num_rows($userposts) != 0)
    {
      $mintimeb4post = $mysql->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if($mintimeb4post < $minfloodtime){
        error($forum_lang["please_wait"]);
		return;
	  }
    }
  }

  if(!isset($_POST['forum'])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  else $forum = $mysql->quote_smart($_POST['forum']);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum_){
      if($fid == $forum) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$forum])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum_ = $forum_skeleton[$cat]["forums"][$forum];
  if(isset($closed))
	if((($user_lvl > 0)||!$closed)&&($forum_skeleton[$cat]["level_post"] > $user_lvl || $forum_["level_post"] > $user_lvl)){
		error($forum_lang["no_access"]);
		return;
	}

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        return;
    }
    if($forum_["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        return;
      else if($forum_["side_access"] != $side) // Forumside different of the user side
        return;
    }
  }

  if(!isset($_POST['topic'])){
	  error($forum_lang["no_such_topic"]);
	  return;
  }
  else $topic = $mysql->quote_smart($_POST['topic']);

//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
  $msg = trim($mysql->quote_smart($_POST['msg']), " ");

  $msg = str_replace('\n', '<br />', $msg);
  $msg = str_replace('\r', '<br />', $msg);

  if (strlen($msg) < 5){
    $mysql->close();
    error($forum_lang["msg_too_short"]);
	return;
  }

  $name = $mysql->query("SELECT name FROM mm_forum_posts WHERE id = '$topic';");
  $name = $mysql->fetch_row($name);
  $name = $mysql->quote_smart($name[0]);

  $time = date("m/d/y H:i:s");

  $mysql->query("INSERT INTO mm_forum_posts (authorid, authorname, forum, topic, name, text, time) VALUES ('$user_id', '$user_name', '$forum', $topic, '$name', '$msg', '$time');");
  $id = $mysql->insert_id();
  $mysql->query("UPDATE mm_forum_posts SET lastpost = $id WHERE id = $topic;");

  $mysql->close();

  redirect("forum.php?action=view_topic&id=$topic");
  // Queries : 4
}

function forum_edit_post(){
  global $forum_skeleton, $forum_lang, $maxqueries, $minfloodtime, $user_lvl, $user_id, $output, $mmfpm_db;

  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_post"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  $post = $mysql->query("SELECT id,topic,authorid,forum,name,text FROM mm_forum_posts WHERE id = '$id';");
  if($mysql->num_rows($post)==0) error($forum_lang["no_such_post"]);
  $post = $mysql->fetch_row($post);

  if($user_lvl == 0 && $user_id != $post[2]){
	  error($forum_lang["no_access"]);
	  return;
  }

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ == $post[3]) $cat = $cid;
    }
  }
  if(empty($forum_skeleton[$cat]["forums"][$post[3]])){ // No such forum..
      error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum = $forum_skeleton[$cat]["forums"][$post[3]];

  $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a> -> <a href=\"forum.php?action=view_forum&amp;id={$post[3]}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$post[1]}\">{$post[4]}</a> -> {$forum_lang["edit"]}</div><form action=\"forum.php?action=do_edit_post\" method=\"POST\" name=\"form\"><center><table class=\"lined\">";

  $output .= "<table class=\"top_hidden\"><tr><td align=\"left\">";
  makebutton("Post", "javascript:do_submit()",220);
  if($post[0] = $post[0])
    $output .= "</td><td align=\"right\"><input type=\"hidden\" name=\"topic\" value=\"1\" /><input name=\"name\" SIZE=\"50\" value=\"$post[4]\"></td></tr>";
  else
    $output .= "</td><td align=\"right\">$post[4]</td></tr>";

  $post[5] = str_replace('<br />', chr(10), $post[5]);

  $output .= "<tr><td colspan=\"2\">".bbcode_editor_js()."
        <a href=\"javascript:ajtBBCode('[b]','[/b]')\">{$forum_lang["bold"]}</a>,
        <a href=\"javascript:ajtBBCode('[i]','[/i]')\">{$forum_lang["italic"]}</a>,
        <a href=\"javascript:ajtBBCode('[u]','[/u]')\">{$forum_lang["underline"]}</a>,
        <a href=\"javascript:ajtBBCode('[img]','[/img]')\">{$forum_lang["image"]}</a>,
        <a href=\"javascript:ajtBBCode('[url]','[/url]')\">{$forum_lang["url"]}</a>,
        <a href=\"javascript:ajtBBCode('[url=Click here]','[/url]')\">{$forum_lang["url2"]}</a>,
        <a href=\"javascript:ajtBBCode('[code]','[/code]')\">{$forum_lang["code"]}</a>,
        <a href=\"javascript:ajtBBCode('[quote]','[/quote]')\">{$forum_lang["quote"]}</a>,
        <a href=\"javascript:ajtBBCode('[quote=Someone]','[/quote]')\">{$forum_lang["quote2"]}</a>,
        <a href=\"javascript:ajtBBCode('[media]','[/media]')\">{$forum_lang["media"]}</a>
                <a href=\"javascript:ajtBBCode('[youtube]','[/youtube]')\">{$forum_lang["YouTube"]}</a>
        {$forum_lang["color"]} : <select name=\"fontcolor\" onChange=\"ajtBBCode('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]'); this.selectedIndex=0;\" onMouseOver=\"helpline('fontcolor')\" style=\"background-color:#D7D7D7\">
          <option value=\"black\" style=\"color:black\">Black</option>
          <option value=\"silver\" style=\"color:silver\">Silver</option>
          <option value=\"gray\" style=\"color:gray\">Gray</option>
          <option value=\"maroon\" style=\"color:maroon\">Maroon</option>
          <option value=\"red\" style=\"color:red\">Red</option>
          <option value=\"purple\" style=\"color:purple\">Purple</option>
          <option value=\"fuchsia\" style=\"color:fuchsia\">Fuchsia</option>
          <option value=\"navy\" style=\"color:navy\">Navy</option>
          <option value=\"blue\" style=\"color:blue\">Blue</option>
          <option value=\"aqua\" style=\"color:aqua\">Aqua</option>
          <option value=\"teal\" style=\"color:teal\">Teal</option>
          <option value=\"lime\" style=\"color:lime\">Lime</option>
          <option value=\"green\" style=\"color:green\">Green</option>
          <option value=\"olive\" style=\"color:olive\">Olive</option>
          <option value=\"yellow\" style=\"color:yellow\">Yellow</option>
          <option value=\"white\" style=\"color:white\">White</option>
        </select>
        </td></tr><tr><td colspan=\"2\">
        <a href=\"javascript:ajtTexte(':)')\"><img style=\"border:0px;\" src=\"img/emoticons/smile.gif\"></a><a href=\"javascript:ajtTexte(':|')\"><img style=\"border:0px;\" src=\"img/emoticons/neutral.gif\"></a><a href=\"javascript:ajtTexte(':(')\"><img style=\"border:0px;\" src=\"img/emoticons/sad.gif\"></a><a href=\"javascript:ajtTexte(':D')\"><img style=\"border:0px;\" src=\"img/emoticons/razz.gif\"></a><a href=\"javascript:ajtTexte(':o')\"><img style=\"border:0px;\" src=\"img/emoticons/yikes.gif\"></a><a href=\"javascript:ajtTexte(';)')\"><img style=\"border:0px;\" src=\"img/emoticons/wink.gif\"></a><a href=\"javascript:ajtTexte(':/')\"><img style=\"border:0px;\" src=\"img/emoticons/hmm.gif\" /></a><a href=\"javascript:ajtTexte(':p')\"><img style=\"border:0px;\" src=\"img/emoticons/tongue.gif\"></a><a href=\"javascript:ajtTexte(':lol:')\"><img style=\"border:0px;\" src=\"img/emoticons/lol.gif\"></a><a href=\"javascript:ajtTexte(':mad:')\"><img style=\"border:0px;\" src=\"img/emoticons/angry.gif\"></a><a href=\"javascript:ajtTexte(':rolleyes:')\"><img style=\"border:0px;\" src=\"img/emoticons/roll.gif\"></a><a href=\"javascript:ajtTexte(':cool:')\"><img style=\"border:0px;\" src=\"img/emoticons/cool.gif\"></a>
        </td></tr></table>";

  $output .= "<TEXTAREA NAME=\"msg\" ROWS=8 COLS=93>$post[5]</TEXTAREA>
  <input type=\"hidden\" name=\"forum\" value=\"{$post[3]}\" />
  <input type=\"hidden\" name=\"post\" value=\"{$post[0]}\" />";

  $output .= "</center></form><br/>";
  $mysql->close();
  // Queries : 1
}
function forum_do_edit_post(){
  global $forum_lang, $user_lvl, $user_name, $user_id, $mmfpm_db;

  $mysql = new SQL;
  $link = $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if(!isset($_POST['forum'])){
	 error($forum_lang["no_such_forum"]);
	 return;
  }
  else $forum = $mysql->quote_smart($_POST['forum']);
  if(!isset($_POST['post'])){
	  error($forum_lang["no_such_post"]);
	  return;
  }
  else $post = $mysql->quote_smart($_POST['post']);

  if(!isset($_POST['name']))
    $topic = 0;
  else{
    $topic = 1;
//    htmlspecialchars($_POST['name']);
    $name = $mysql->quote_smart($_POST['name']);
    if (strlen($name) > 49){
      $mysql->close();
      error($forum_lang["name_too_long"]);
	  return;
    }
    if (strlen($name) < 5){
      $mysql->close();
      error($forum_lang["name_too_short"]);
	  return;
    }
  }

//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
  $msg = trim($mysql->quote_smart($_POST['msg']), " ");

  if (strlen($msg) < 5){
    $mysql->close();
    error($forum_lang["msg_too_short"]);
	return;
  }

  $msg = str_replace('\n', '<br />', $msg);
  $msg = str_replace('\r', '<br />', $msg);

  $result = $mysql->query("SELECT topic FROM mm_forum_posts WHERE id = $post;");
  $topicid = $mysql->fetch_row($result);

  $mysql->query("UPDATE mm_forum_posts SET text = '$msg' WHERE id = $post;");

  if($topic == 1){
    $mysql->query("UPDATE mm_forum_posts SET name = '$name' WHERE topic = {$topicid[0]};");
  }

  $result = $mysql->query("SELECT topic FROM mm_forum_posts WHERE id = $post;");
  $topicid = $mysql->fetch_row($result);

  $mysql->close();
  redirect("forum.php?action=view_topic&id={$topicid[0]}");
  // Queries : 3 (+1 if topic)
}

function forum_move_topic(){
  global $forum_skeleton, $forum_lang, $maxqueries, $user_lvl, $user_id, $output, $mmfpm_db;
  $mysql = new SQL;

  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  if(!isset($_GET["id"])){
	  error($forum_lang["no_such_topic"]);
	  return;
  }
  else $id = $mysql->quote_smart($_GET["id"]);

  $topic = $mysql->query("SELECT id,topic,authorid,forum, name FROM mm_forum_posts WHERE id = '$id';");
  //                0 1   2   3   4
  if($mysql->num_rows($topic)==0) error($forum_lang["no_such_topic"]);
  $topic = $mysql->fetch_row($topic);
  if($user_lvl == 0){
	  error($forum_lang["no_access"]);
	  return;
  }
  $fid = $topic[3];

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ == $fid) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$fid])){ // No such forum..
      error($forum_lang["no_such_forum"]);
	  return;
  }
  $forum = $forum_skeleton[$cat]["forums"][$fid];

  $output .= "<div class=\"top\"><h1>{$forum_lang["forums"]}</h1>{$forum_lang["you_are_here"]} : <a href=\"forum.php\">{$forum_lang["forum_index"]}</a> -> <a href=\"forum.php?action=view_forum&amp;id={$fid}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$topic[1]}\">{$topic[4]}</a> -> {$forum_lang["move"]}!</div><center><table class=\"lined\">
  <tr><td>{$forum_lang["where"]} : <form action=\"forum.php?action=do_move_topic\" method=\"POST\" name=\"form\"><select name=\"forum\">";

  foreach($forum_skeleton as $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ != $fid)
        $output .= "<option value='$fid_'>{$forum["name"]}</option>";
      else
        $output .= "<option value='$fid_' selected>{$forum["name"]}</option>";
    }
  }

  $output .= "</select><input type=\"hidden\" name=\"id\" value=\"$id\" /></form></td></tr></table><table class=\"hidden\"><tr><td>";
  makebutton($forum_lang["back"], "javascript:window.history.back()", 120);
  makebutton($forum_lang["confirm"], "javascript:do_submit()", 120);
  $output .= "</td></tr></table></center>";
  $mysql->close();
  // Queries : 1
}
function forum_do_move_topic(){
  global $forum_lang, $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $mmfpm_db;

  $mysql = new SQL;
  $link = $mysql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  if(!isset($_POST['forum'])){
	  error($forum_lang["no_such_forum"]);
	  return;
  }
  else $forum = $mysql->quote_smart($_POST['forum']);
  if(!isset($_POST['id'])){
	  error($forum_lang["no_such_topic"]);
	  return;
  }
  else $id = $mysql->quote_smart($_POST['id']);

  $mysql->query("UPDATE mm_forum_posts SET forum = '$forum' WHERE topic = '$id'"); // update topic' s last post id
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}



if(isset($_GET['action']))
    $action = addslashes($_GET['action']);
else $action = NULL;

$forum_lang = lang_forum();

switch ($action){
  case "index": forum_index(); break;
  case "view_forum": forum_view_forum(); break;
  case "view_topic": forum_view_topic(); break;
  case "add_topic": forum_add_topic(); break;
  case "do_add_topic": forum_do_add_topic(); break;
  case "edit_post": forum_edit_post(); break;
  case "do_edit_post": forum_do_edit_post(); break;
  case "delete_post": forum_delete_post(); break;
  case "do_delete_post": forum_do_delete_post(); break;
  case "do_add_post": forum_do_add_post(); break;
  case "edit_stick": forum_do_edit_stick(); break;
  case "edit_announce": forum_do_edit_announce(); break;
  case "edit_close": forum_do_edit_close(); break;
  case "move_topic": forum_move_topic(); break;
  case "do_move_topic": forum_do_move_topic(); break;
  default: forum_index();
}

unset($action);
unset($forum_lang);

require_once("footer.php");

?>
