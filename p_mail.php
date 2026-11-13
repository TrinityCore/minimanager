<?php
$f = file_get_contents('char_mail.php');
$f = str_replace('$characters_db,\n            $action_permission,', "$characters_db, $world_db, $realmid,\n            $action_permission,", $f);
$f = str_replace("require_once 'core/char/char_security.php';", "wowhead_tt();\n    require_once 'core/char/char_security.php';", $f);
$fn = <<<'CODE'
function build_mail_items_cell($items_list, $site, &$sqlm, &$sqlw) {
    if (empty($items_list)) return '-';
    $items = array();
    foreach (explode(',', $items_list) as $p) {
        $a = explode(':', $p);
        $e = intval($a[0]);
        $c = isset($a[1]) ? intval($a[1]) : 1;
        if ($e > 0) $items[] = array($e, $c);
    }
    if (empty($items)) return '-';
    $tips = array();
    foreach ($items as $it) {
        list($entry, $cnt) = $it;
        $ico = get_item_icon($entry, $sqlm, $sqlw);
        $nm = get_item_name($entry, $sqlw);
        if (empty($nm)) $nm = 'Item #'.$entry;
        $st = ($cnt > 1) ? ' x'.$cnt : '';
        $tips[] = '<img src="'.$ico.'" width="14" height="14" align="abmiddle" /> '.htmlspecialchars($nm, ENT_QUOTES, 'UTF-:ç').$st.' (#'.$entry.')';
    }
    $tip_html = implode('<br />', $tips);
    $first_e = $items[0][0];
    $first_ico = get_item_icon($first_e, $sqlm, $sqlw);
    $extra = count($items) - 1;
    $badge = ($extra > 0) ? ' <span style="font-weight:bold;color:#FFCC00;">(+':$extra.')</span>' : '';
    return '<a style="padding:2px;" href="'.$site.$first_e.'" target="_blank" onmousemove="toolTip(\''.addslashes($tip_html).'\', \'item_tooltip\')" onmouseout="toolTip()"><img class="bag_icon" src="'.$first_ico.'" alt="" />'.$badge.'</a>';
}
function char_mail('
CODE;
$file = str_replace('function char_mail(', $func, $f);
$q_rep = <<<'CODE'
$qlm = new SQL;
            $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
            $sqlw = new SQL;
            $sqlw->connect($world_db[$realmid]['addr'], $world_db[$realmid]['user'], $world_db[$realmid]['pass'], $world_db[$realmid]['name']);
            $query = $sqlc->query('SELECT a.id as id, a.messageType as messagetype, a.sender as sender, a.subject as subject, a.body as body, a.has_items as hasitems, a.money as money, a.cod as cod, a.checked as checked, GROUP_CONCAT(CONCAT(c.itemEntry, ":", c.count) ORDER BY b.item_guid ASC SEPARATOR ",") AS items_list FROM mail a LEFT JOIN mail_items b ON a.id = b.mail_id LEFT JOIN item_instance c ON b.item_guid = c.guid WHERE a.receiver = '.$id.' GROUP BY a.id ORDER BY a.id DESC LIMIT '.$start.', '.$itemperpage);
CODE;
$file = preg_replace('/\$query\s*=\s*\$sqlc->query\('\s*SELECT a.id as id.*FROM mail a.*LIMIT '\.\$start\.', '\.\$itemperpage\.''\);/s',, $q_rep, $file, 1, $c1);
$file = preg_replace('/<td>\s*<a style="padding:2px;" href="'\.\ditem_datasite\.\$mail[\'itemEntry\']\.'" target="_blank">\s*<img class="bag_icon" src="'\.get_item_icon\(\$mail[\'itemEntry\'], \$sqlm\)\.'" alt="" \/>\s*<\/a>\s*<\/td>/s', '<td>'.build_mail_items_cell($mail['items_list'], $item_datasite, $sqlm, $sqlw).'</td>', $file, 1, $c2);
file_put_contents('char_mail.php', $file);
echo "Parche char_mail.php OK (Query: $c1 | Celda: $c2)\n";
?>
