<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/archieve_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//#############################################################################
function char_achievements(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char,
            $realm_id, $characters_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name,
            $achievement_datasite;

    // this page uses wowhead tooltops
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $show_type = (isset($_POST['show_type'])) ? $sqlc->quote_smart($_POST['show_type']) : 0;
    if (is_numeric($show_type));
        else $show_type = 0;

    // getting character data from database
    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender
                            FROM characters WHERE guid = '.$id.' LIMIT 1');

    // no point going further if character does not exist
    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        // we get user permissions first
        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `AccountID` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `AccountID` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'AccountID');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        // check user permission
        if ( ($user_lvl > $owner_gmlvl) || ($owner_name === $user_name) )
        {
            //------------------------Character Tabs---------------------------------
            // we start with a lead of 10 spaces,
            //  because last line of header is an opening tag with 8 spaces
            //  keep html indent in sync, so debuging from browser source would be easy to read
            $output .= '
            <center>
                <script type="text/javascript">
                    function expand(thistag)
                    {
                        var i = 0;
                        %%REPLACE%%

                        if (thistag == \'tsummary\')
                        {
                            document.getElementById(\'tsummary\').style.display="table";
                            document.getElementById(\'divsummary\').innerHTML = \'[-] '.$lang_char['summary'].'\' ;
                            for(x in main_cats)
                            {
                                if(document.getElementById(main_cats[x]).style.display=="table")
                                {
                                    document.getElementById(main_cats[x]).style.display="none";
                                    document.getElementById(main_cats_achieve[x]).style.display="none";
                                    document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                                }
                            }
                            for(x in main_sub_cats)
                            {
                                if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                {
                                    document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                    document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                }
                            }
                        }
                        else
                        {
                            if (document.getElementById(\'tsummary\').style.display="table")
                            {
                                document.getElementById(\'tsummary\').style.display="none";
                                document.getElementById(\'divsummary\').innerHTML = \'[+] '.$lang_char['summary'].'\' ;
                            }
                            for(x in main_cats)
                            {
                                if (main_cats[x] == thistag)
                                {
                                    i = 1;
                                }
                            }

                            if (i == 1)
                            {
                                for(x in main_cats)
                                {
                                    if (main_cats[x] == thistag)
                                    {
                                        if(document.getElementById(main_cats[x]).style.display=="table")
                                        {
                                            document.getElementById(main_cats[x]).style.display="none";
                                            document.getElementById(main_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                                            document.getElementById(\'tsummary\').style.display="table";
                                            document.getElementById(\'divsummary\').innerHTML = \'[-] '.$lang_char['summary'].'\' ;
                                        }
                                        else
                                        {
                                            document.getElementById(main_cats[x]).style.display="table";
                                            document.getElementById(main_cats_achieve[x]).style.display="table";
                                            document.getElementById(main_cats_div[x]).innerHTML = \'[-] \' + main_cats_name[x];
                                        }
                                    }
                                    else
                                    {
                                        if(document.getElementById(main_cats[x]).style.display=="table")
                                        {
                                            document.getElementById(main_cats[x]).style.display="none";
                                            document.getElementById(main_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                                        }
                                    }
                                }
                                for(x in main_sub_cats)
                                {
                                    if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                    {
                                        document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                        document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                    }
                                }
                            }
                            else if (i == 0)
                            {
                                for(x in main_sub_cats)
                                {
                                    if (main_sub_cats[x] == thistag)
                                    {
                                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                        {
                                            document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                        }
                                        else
                                        {
                                            document.getElementById(main_sub_cats_achieve[x]).style.display="table";
                                            document.getElementById(main_sub_cats_div[x]).innerHTML = \'[-] \' + main_sub_cats_name[x];
                                        }
                                    }
                                    else
                                    {
                                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                                        {
                                            document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                                            document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                                        }
                                    }
                                }
                                for(x in main_cats)
                                {
                                    if(document.getElementById(main_cats_achieve[x]).style.display=="table")
                                    {
                                        document.getElementById(main_cats_achieve[x]).style.display="none";
                                    }
                                }
                            }
                        }
                    }
                </script>';
            $output .= '
                <center>
                    <div id="tab_content">
                        <h1>'.$lang_char['progress'].'</h1>
                        <br />';

            require_once 'core/char/char_header.php';

        $lang_eng = lang_char(); // Fallback garantizado en inglés
        $t = function($k) use ($lang_char, $lang_eng) {
            return isset($lang_char[$k]) ? $lang_char[$k] : (isset($lang_eng[$k]) ? $lang_eng[$k] : $k);
        };

        $is_horde = in_array((int)$char['race'], array(2, 5, 6, 8, 10));

        // IDs de logros oficiales (en mazmorras Heroic/Normal se toma el nombre oficial de DBC)
        $classic_dungeons = array(628, 629, 630, 631, 632, 633, 634, 635, 636, 637, 638, 639, 640, 641, 642, 643, 1307, 644, 645, 646);
        $classic_raids    = array(688, 689, 686, 685, 687, 684);

        $tbc_dungeons = array(
            array(647, 667), array(648, 668), array(651, 678), array(649, 669),
            array(650, 670), array(652, 671), array(653, 672), array(654, 673),
            array(656, 674), array(657, 675), array(655, 676), array(658, 677),
            array(659, 679), array(660, 680), array(661, 681), array(662, 682)
        );
        $tbc_raids = array(690, 692, 693, 694, 696, 695, 697, 698);

        $wotlk_dungeons = array(
            array(477, 489), array(478, 490), array(480, 491), array(481, 492),
            array(482, 493), array(483, 494), array(484, 495), array(485, 496),
            array(486, 497), array(487, 498), array(488, 499), array(479, 500),
            array(4296, 4298), array(4516, 4519), array(4517, 4520), array(4518, 4521)
        );
        $wotlk_raids = array(
            array(576, 577), array(1876, 625), array(622, 623),
            array(1722, 1721), array(2894, 2895), array(3917, 3916),
            array(4396, 4397), array(4532, 4608), array(4817, 4815)
        );

        $pvp_categories = array(
            array('key' => 'prog_pvp_gen',  'lbl' => 'General', 'items' => array(245, 246, 247, array(700, 701), 1157, 512, 516, 513, 515, 870)),
            array('key' => 'prog_pvp_arn',  'lbl' => 'Arena',   'items' => array(399, 400, 402, 406, 408, 1159, 403, 407, 1161, 2090)),
            array('key' => 'prog_pvp_av',   'lbl' => 'Alterac', 'items' => array(218, 219, 221, 222, 225, 226, 582, array(706, 707), array(224, 1151), array(1167, 1168))),
            array('key' => 'prog_pvp_ab',   'lbl' => 'Arathi',  'items' => array(154, 155, 156, 157, 158, 159, 161, 162, 583, array(1169, 1170))),
            array('key' => 'prog_pvp_eots', 'lbl' => 'EotS',    'items' => array(208, 209, 211, 212, 213, 214, 216, 587, 1258, array(1171, 1259))),
            array('key' => 'prog_pvp_wsg',  'lbl' => 'WSG',     'items' => array(166, 167, 168, 199, 200, 201, array(202, 1502), array(1251, 1252), array(206, 1252), array(1172, 1173))),
            array('key' => 'prog_pvp_sota', 'lbl' => 'SotA',    'items' => array(1308, 1309, 1310, 1761, 1762, 1763, 1764, 1765, 2189, array(2194, 2195))),
            array('key' => 'prog_pvp_ioc',  'lbl' => 'IoC',     'items' => array(3776, 3777, 3845, 3846, 3847, 3848, 3849, 3850, 3851, array(3857, 3957)))
        );

        // Recolectar todos los IDs para consultarlos de una sola vez
        $all_ids = array_merge($classic_dungeons, $classic_raids, $tbc_raids);
        foreach ($tbc_dungeons as $p)   { $all_ids[] = $p[0]; $all_ids[] = $p[1]; }
        foreach ($wotlk_dungeons as $p) { $all_ids[] = $p[0]; $all_ids[] = $p[1]; }
        foreach ($wotlk_raids as $p)    { $all_ids[] = $p[0]; $all_ids[] = $p[1]; }
        foreach ($pvp_categories as $c) {
            foreach ($c['items'] as $it) {
                if (is_array($it)) { $all_ids[] = $it[0]; $all_ids[] = $it[1]; }
                else { $all_ids[] = $it; }
            }
        }
        $all_ids = array_unique($all_ids);
        $in_clause = implode(',', $all_ids);

        // 1. Obtener nombres oficiales desde mmfpm.dbc_achievement
        $sqlm = new SQL;
        $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $ach_names = array();
        $dbc_res = $sqlm->query("SELECT id, name01 FROM dbc_achievement WHERE id IN ($in_clause)");
        while ($row = $sqlm->fetch_assoc($dbc_res)) {
            $ach_names[(int)$row['id']] = $row['name01'];
        }
        $get_name = function($aid) use ($ach_names) {
            return isset($ach_names[$aid]) ? $ach_names[$aid] : "Achievement #$aid";
        };

        // 2. Obtener logros completados del personaje
        $completed_ach = array();
        $ach_res = $sqlc->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($in_clause)");
        while ($row = $sqlc->fetch_assoc($ach_res)) {
            $completed_ach[(int)$row['achievement']] = true;
        }

        $draw_pie = function($slices, $pct, $label) {
            $total = count($slices);
            $cx = 75; $cy = 75; $r = 68; $inner = 38;
            $svg = '<div class="mm-pie-box"><svg width="150" height="150" viewBox="0 0 150 150">';
            for ($i = 0; $i < $total; $i++) {
                $a1 = deg2rad(($i * (360 / $total)) - 90);
                $a2 = deg2rad((($i + 1) * (360 / $total)) - 90 - 1.5);
                $x1 = $cx + $r * cos($a1); $y1 = $cy + $r * sin($a1);
                $x2 = $cx + $r * cos($a2); $y2 = $cy + $r * sin($a2);
                $color = $slices[$i] ? '#26e040' : '#e02929';
                $svg .= '<path d="M '.$cx.' '.$cy.' L '.round($x1,2).' '.round($y1,2).' A '.$r.' '.$r.' 0 0 1 '.round($x2,2).' '.round($y2,2).' Z" fill="'.$color.'" />';
            }
            $svg .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="'.$inner.'" fill="#0a1e38" />';
            $svg .= '</svg><div class="mm-pie-center"><span class="mm-pie-pct">'.$pct.'%</span><span class="mm-pie-lbl">'.$label.'</span></div></div>';
            return $svg;
        };

        $render_simple_card = function($title, $sub_lbl, $pie_lbl, $items) use ($completed_ach, $get_name, $draw_pie) {
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($items as $it) {
                if (is_array($it)) {
                    $done = isset($completed_ach[$it[0]]) || isset($completed_ach[$it[1]]);
                    $pick_id = isset($completed_ach[$it[1]]) ? $it[1] : $it[0];
                } else {
                    $done = isset($completed_ach[$it]);
                    $pick_id = $it;
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars($get_name($pick_id));
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($items);
            $pct = floor(($done_cnt / $tot) * 100);
            return '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($title).'</div>
                <div class="mm-prog-sub">'.$sub_lbl.': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $pie_lbl).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        };

        // Tarjeta especial 10/25 para WotLK Raids
        $r_done = 0; $r_slices = array(); $r_li = '';
        foreach ($wotlk_raids as $pair) {
            $d10 = isset($completed_ach[$pair[0]]);
            $d25 = isset($completed_ach[$pair[1]]);
            if ($d10) $r_done++;
            if ($d25) $r_done++;
            $r_slices[] = $d10; $r_slices[] = $d25;
            $t10 = $d10 ? '<span class="mm-ok">[10&#10004;]</span>' : '<span class="mm-no">[10&#10008;]</span>';
            $t25 = $d25 ? '<span class="mm-ok">[25&#10004;]</span>' : '<span class="mm-no">[25&#10008;]</span>';
            $r_li .= '<li>'.$t10.' '.$t25.' '.htmlspecialchars($get_name($pair[0])).'</li>';
        }
        $r_pct = floor(($r_done / 18) * 100);
        $wotlk_raid_card = '<div class="mm-prog-card">
            <div class="mm-prog-title">'.htmlspecialchars($t('prog_wot_r')).'</div>
            <div class="mm-prog-sub">'.$t('prog_modes').': <span class="mm-ok">'.$r_done.'/18</span> (<b>'.$r_pct.'%</b>)</div>
            '.$draw_pie($r_slices, $r_pct, $t('prog_raids')).'
            <ul class="mm-prog-list">'.$r_li.'</ul>
        </div>';

        $output .= '
        <style>
          .mm-prog-wrap { display:flex; flex-wrap:wrap; justify-content:center; gap:24px; padding:12px 6px; }
          .mm-prog-card { background:rgba(10,25,45,0.85); border:1px solid #2b5b84; border-radius:10px; padding:16px; width:300px; text-align:left; color:#e2e8f0; }
          .mm-prog-title { text-align:center; color:#73d2ff; font-weight:bold; font-size:14px; margin-bottom:4px; }
          .mm-prog-sub { text-align:center; font-size:12px; margin-bottom:12px; color:#cbd5e1; }
          .mm-pie-box { position:relative; width:150px; height:150px; margin:0 auto 14px auto; }
          .mm-pie-center { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; pointer-events:none; }
          .mm-pie-pct { font-size:20px; font-weight:bold; color:#ffffff; display:block; }
          .mm-pie-lbl { font-size:11px; color:#73d2ff; }
          .mm-prog-list { list-style:none; padding:0; margin:0; font-size:12px; line-height:1.65; }
          .mm-ok { color:#22ee44; font-weight:bold; }
          .mm-no { color:#ff4444; font-weight:bold; }
          .mm-dim { color:#94a3b8; }
        </style>
        <table class="lined" style="width: 100%;">
          <tr>
            <td>
              <div class="mm-prog-wrap">'
                .$render_simple_card($t('prog_cls_d'), $t('prog_done'), $t('prog_dung'), $classic_dungeons)
                .$render_simple_card($t('prog_cls_r'), $t('prog_done'), $t('prog_raids'), $classic_raids)
              .'</div>
              <div class="mm-prog-wrap">'
                .$render_simple_card($t('prog_tbc_d'), $t('prog_done'), $t('prog_dung'), $tbc_dungeons)
                .$render_simple_card($t('prog_tbc_r'), $t('prog_done'), $t('prog_raids'), $tbc_raids)
              .'</div>
              <div class="mm-prog-wrap">'
                .$render_simple_card($t('prog_wot_d'), $t('prog_done'), $t('prog_dung'), $wotlk_dungeons)
                .$wotlk_raid_card
              .'</div>
              <div class="mm-prog-wrap">';

        foreach ($pvp_categories as $cat) {
            $output .= $render_simple_card($t($cat['key']), $t('prog_done'), $cat['lbl'], $cat['items']);
        }

        $output .= '
              </div>
            </td>
          </tr>
        </table>';


    require_once 'core/char/char_footer.php';

            $output .= '
                <br />
            </center>
            <!-- end of char_achieve.php -->';
        }
        else
            error($lang_char['no_permission']);
    }
    else
        error($lang_char['no_char_found']);
}


//#############################################################################
// MAIN
//#############################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();

$output .= '
        <div class="top">
            <h1>'.$lang_char['character'].'</h1>
        </div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_achievements($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
