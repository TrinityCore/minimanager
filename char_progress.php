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
            array('key' => 'prog_pvp_ioc',  'lbl' => 'IoC',     'items' => array(3776, 3777, 3845, 3846, 3847, 3848, 3849, 3850, 3851, array(3857, 3957))),
            array('key' => 'prog_pvp_wg',   'lbl' => 'WG',      'items' => array(1717, 1718, 1755, 1727, 1723, 1751, 1737, 2085, 2089, array(1752, 2776)))
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
                $done = false;
                $pick_id = 0;
                if (is_array($it)) {
                    foreach ($it as $sub_id) {
                        $sub_int = (int)$sub_id;
                        if ($pick_id === 0) $pick_id = $sub_int;
                        if (isset($completed_ach[$sub_int])) {
                            $done = true;
                            $pick_id = $sub_int;
                            break;
                        }
                    }
                } else {
                    $pick_id = (int)$it;
                    $done = isset($completed_ach[$pick_id]);
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars($get_name($pick_id));
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($items);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
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

                // Logros individuales de Jefes Heroicos (ICC + Rubí) y Hard Modes (Ulduar + ToGC)
        // Formato: array(id_10j, id_25j)
        $icc_hc_bosses = array(
            array(4628, 4632), // Lord Marrowgar (Heroic)
            array(4629, 4633), // Lady Deathwhisper (Heroic)
            array(4630, 4634), // Icecrown Gunship Battle (Heroic)
            array(4631, 4635), // Deathbringer Saurfang (Heroic)
            array(4623, 4624), // Festergut (Heroic)
            array(4625, 4626), // Rotface (Heroic)
            array(4627, 4636), // Professor Putricide (Heroic)
            array(4622, 4637), // Blood Prince Council (Heroic)
            array(4621, 4638), // Blood-Queen Lana'thel (Heroic)
            array(4620, 4639), // Valithria Dreamwalker (Heroic)
            array(4619, 4640), // Sindragosa (Heroic)
            array(4583, 4584), // The Lich King (Heroic)
            array(4818, 4816)  // Halion (Heroic)
        );

        $ulduar_togc_hm = array(
            array(3056, 3057), // Orbit-uary (Flame Leviathan)
            array(3058, 3059), // Heartbreaker (XT-002)
            array(2941, 2944), // I Choose You, Steelbreaker
            array(3179, 3187), // Knock, Knock, Knock on Wood (Freya)
            array(3180, 3189), // Firefighter (Mimiron)
            array(3181, 3188), // I Love the Smell of Saronite (Vezax)
            array(3158, 3163), // One Light in the Darkness (Yogg-Saron)
            array(3159, 3164), // Alone in the Darkness (Yogg-Saron 0)
            array(3036, 3037), // Observed (Algalon)
            array(3810, 3819)  // A Tribute to Insanity (ToGC)
        );

        $hm_ids = array();
        foreach (array_merge($icc_hc_bosses, $ulduar_togc_hm) as $pair) {
            foreach ($pair as $pid) $hm_ids[] = (int)$pid;
        }
        $hm_in = implode(',', array_unique($hm_ids));

        $hm_names = array();
        $hm_dbc = $sqlm->query("SELECT id, name01 FROM dbc_achievement WHERE id IN ($hm_in)");
        while ($hrow = $sqlm->fetch_assoc($hm_dbc)) {
            $hm_names[(int)$hrow['id']] = $hrow['name01'];
        }

        $hm_comp = array();
        $hm_res = $sqlc->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($hm_in)");
        while ($crow = $sqlc->fetch_assoc($hm_res)) {
            $hm_comp[(int)$crow['achievement']] = true;
        }

        $build_10_25_card = function($title, $modes_lbl, $pie_lbl, $pairs) use ($hm_comp, $hm_names, $draw_pie) {
            $done = 0; $slices = array(); $li = '';
            foreach ($pairs as $pair) {
                $id10 = reset($pair);
                $id25 = end($pair);
                $d10 = isset($hm_comp[$id10]);
                $d25 = isset($hm_comp[$id25]);
                if ($d10) $done++;
                if ($d25) $done++;
                $slices[] = $d10;
                $slices[] = $d25;
                $t10 = $d10 ? '<span class="mm-ok">[10&#10004;]</span>' : '<span class="mm-no">[10&#10008;]</span>';
                $t25 = $d25 ? '<span class="mm-ok">[25&#10004;]</span>' : '<span class="mm-no">[25&#10008;]</span>';
                $raw_name = isset($hm_names[$id10]) ? $hm_names[$id10] : "Achievement #$id10";
                $clean_name = preg_replace('/\s*\((?:10|25)\s*player\)/i', '', $raw_name);
                $li .= '<li>'.$t10.' '.$t25.' '.htmlspecialchars($clean_name).'</li>';
            }
            $tot = count($pairs) * 2;
            $pct = $tot > 0 ? floor(($done / $tot) * 100) : 0;
            return '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($title).'</div>
                <div class="mm-prog-sub">'.$modes_lbl.': <span class="mm-ok">'.$done.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $pie_lbl).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        };

        $wotlk_hm_row = '<div class="mm-prog-wrap">'
            . $build_10_25_card($t('prog_wot_icc_hc'), $t('prog_modes'), 'Heroic', $icc_hc_bosses)
            . $build_10_25_card($t('prog_wot_hm'),     $t('prog_modes'), 'HardMode', $ulduar_togc_hm)
            . '</div>';

                        // Calcular progreso global usando el esquema real de mmfpm.dbc_achievement
        $cols = array();
        $col_res = $sqlm->query("SHOW COLUMNS FROM dbc_achievement");
        while ($crow = $sqlm->fetch_assoc($col_res)) {
            $cols[strtolower($crow['Field'])] = $crow['Field'];
        }
        // Detectar automáticamente la columna de puntos buscando cualquier campo con 'point' o 'rew'
        $pts_col = '0';
        foreach ($cols as $low => $orig) {
            if (strpos($low, 'point') !== false || $low === 'reward' || $low === 'pts') {
                $pts_col = $orig;
                break;
            }
        }
        $fac_col = '';
        foreach (array('factionflag', 'faction', 'requiredfaction', 'side') as $fcand) {
            if (isset($cols[$fcand])) { $fac_col = $cols[$fcand]; break; }
        }
        // En WoW DBC: factionflag 0 = Horda, 1 = Alianza, -1 = Ambos (en algunos DBC 1=Alianza, 2=Horda)
        $faction_cond = ($fac_col !== '') ? ($is_horde ? "AND $fac_col != 1" : "AND $fac_col NOT IN (0, 2)") : "";

        $tot_dbc_res = $sqlm->query("SELECT id, $pts_col AS pts FROM dbc_achievement WHERE categoryid != 81 AND (flags & 1) = 0 $faction_cond");
        $valid_ach_pts = array();
        $max_ach_count = 0;
        $max_ach_points = 0;
        while ($trow = $sqlm->fetch_assoc($tot_dbc_res)) {
            $aid = (int)$trow['id'];
            $pts = (int)$trow['pts'];
            if ($pts <= 0) continue;
            $valid_ach_pts[$aid] = $pts;
            $max_ach_count++;
            $max_ach_points += $pts;
        }

        $earned_ach_count = 0;
        $earned_ach_points = 0;
        $all_char_res = $sqlc->query("SELECT achievement FROM character_achievement WHERE guid = $id");
        while ($crow = $sqlc->fetch_assoc($all_char_res)) {
            $aid = (int)$crow['achievement'];
            if (isset($valid_ach_pts[$aid])) {
                $earned_ach_count++;
                $earned_ach_points += $valid_ach_pts[$aid];
            }
        }

        $global_pct = $max_ach_count > 0 ? round(($earned_ach_count / $max_ach_count) * 100, 1) : 0;
        $pts_pct    = $max_ach_points > 0 ? round(($earned_ach_points / $max_ach_points) * 100, 1) : 0;

        $global_bar_html = '
        <div style="width:92%; max-width:632px; margin:14px auto 8px auto; padding:14px 18px; border-radius:10px; background:rgba(10,25,45,0.9); border:1px solid #2b5b84; box-shadow:0 2px 10px rgba(0,0,0,0.4);">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
            <span style="color:#73d2ff; font-weight:bold; font-size:14px;">'.htmlspecialchars($t('prog_global_title')).'</span>
            <span style="font-size:12px; color:#e2e8f0;">
              '.$t('prog_global_ach').': <b style="color:#26e040;">'.$earned_ach_count.'</b> / '.$max_ach_count.' (<b>'.$global_pct.'%</b>)
              &nbsp;|&nbsp;
              '.$t('prog_global_pts').': <b style="color:#ffd100;">'.number_format($earned_ach_points).'</b> / '.number_format($max_ach_points).' (<b>'.$pts_pct.'%</b>)
            </span>
          </div>
          <div style="width:100%; height:18px; background:#071324; border:1px solid #1e3a5f; border-radius:9px; overflow:hidden; position:relative;">
            <div style="width:'.min(100, $global_pct).'%; height:100%; background:linear-gradient(90deg, #1d8cf8 0%, #26e040 100%); border-radius:8px; transition:width 0.4s ease;"></div>
            <div style="position:absolute; top:0; left:0; width:100%; height:100%; line-height:18px; text-align:center; font-size:11px; font-weight:bold; color:#ffffff; text-shadow:0 1px 2px #000;">'.$global_pct.'%</div>
          </div>
        </div>';

        // Comprobar logro especial 4496 (It's Over Nine Thousand!)
        $over9k_html = '';
        $o9k_res = $sqlc->query("SELECT achievement, date FROM character_achievement WHERE guid = $id AND achievement = 4496 LIMIT 1");
        if ($sqlc->num_rows($o9k_res)) {
            $o9k_row = $sqlc->fetch_assoc($o9k_res);
            $o9k_date = date('Y-m-d', (int)$o9k_row['date']);
            $o9k_dbc = $sqlm->query("SELECT name01, description01 FROM dbc_achievement WHERE id = 4496 LIMIT 1");
            $o9k_title = "It's Over Nine Thousand!";
            $o9k_desc  = "Earn more than 9000 achievement points.";
            $o9k_icon  = "img/a_icons/spell_holy_championsbond.jpg";
            if (!file_exists($o9k_icon)) {
                $icons = glob("img/a_icons/*.{jpg,gif,png}", GLOB_BRACE);
                if (!empty($icons)) $o9k_icon = reset($icons);
            }
            if ($sqlm->num_rows($o9k_dbc)) {
                $drow = $sqlm->fetch_assoc($o9k_dbc);
                if (!empty($drow['name01'])) $o9k_title = $drow['name01'];
                if (!empty($drow['description01'])) $o9k_desc = $drow['description01'];
            }
            $over9k_html = '
            <div style="width:92%; max-width:632px; margin:14px auto 6px auto; padding:12px 18px; border-radius:10px; background:radial-gradient(circle at center, rgba(65,45,10,0.95) 0%, rgba(15,25,45,0.95) 100%); border:2px solid #ffd100; box-shadow:0 0 18px rgba(255,209,0,0.45); display:flex; align-items:center; justify-content:center; gap:16px;">
              <svg width="54" height="54" viewBox="0 0 64 64" style="flex-shrink:0; filter:drop-shadow(0 0 6px rgba(255,209,0,0.75));">
  <defs>
    <linearGradient id="o9kGold" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fff3a1"/>
      <stop offset="45%" stop-color="#ffd100"/>
      <stop offset="100%" stop-color="#b87d00"/>
    </linearGradient>
    <radialGradient id="o9kBg" cx="50%" cy="40%" r="60%">
      <stop offset="0%" stop-color="#3b2606"/>
      <stop offset="100%" stop-color="#0d172a"/>
    </radialGradient>
  </defs>
  <!-- Escudo exterior -->
  <path d="M32 3 L56 12 V31 C56 46 45 56 32 61 C19 56 8 46 8 31 V12 Z" fill="url(#o9kBg)" stroke="url(#o9kGold)" stroke-width="3"/>
  <!-- Borde interior -->
  <path d="M32 8 L51 15.5 V30.5 C51 42.5 42 51 32 55.5 C22 51 13 42.5 13 30.5 V15.5 Z" fill="none" stroke="#ffd100" stroke-width="1" stroke-opacity="0.55"/>
  <!-- Estrella / Destello superior -->
  <polygon points="32,11 34.5,17.5 41.5,17.5 36,21.5 38,28 32,24 26,28 28,21.5 22.5,17.5 29.5,17.5" fill="url(#o9kGold)"/>
  <!-- Texto 9000+ -->
  <text x="32" y="43" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif" font-weight="bold" font-size="12.5" letter-spacing="0.5">9000+</text>
</svg>
              <div style="text-align:left;">
                <div style="color:#ffd100; font-size:16px; font-weight:bold; text-shadow:0 1px 4px #000;">&#9733; '.htmlspecialchars($o9k_title).' &#9733;</div>
                <div style="color:#e2e8f0; font-size:12px; margin-top:2px;">'.htmlspecialchars($o9k_desc).'</div>
                <div style="color:#73d2ff; font-size:11px; margin-top:3px;">Unlocked: <b>'.$o9k_date.'</b></div>
              </div>
            </div>';
        }

        $output .= '
        <style>
          .mm-sec-title { width: 92%; max-width: 632px; margin: 22px auto 6px auto; padding: 9px 16px; text-align: center; font-size: 15px; font-weight: bold; color: #ffd100; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(90deg, rgba(10,25,45,0.2) 0%, rgba(25,58,95,0.95) 50%, rgba(10,25,45,0.2) 100%); border-top: 1px solid #c79c3e; border-bottom: 1px solid #c79c3e; text-shadow: 0 1px 4px #000, 0 0 10px rgba(255,209,0,0.45); }
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
            <td>'.$global_bar_html.$over9k_html.'
              <div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_van')).'</div>
              <div class="mm-prog-wrap">'
                .$render_simple_card($t('prog_cls_d'), $t('prog_done'), $t('prog_dung'), $classic_dungeons)
                .$render_simple_card($t('prog_cls_r'), $t('prog_done'), $t('prog_raids'), $classic_raids)
              .'</div>
              <div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_tbc')).'</div>
              <div class="mm-prog-wrap">'
                .$render_simple_card($t('prog_tbc_d'), $t('prog_done'), $t('prog_dung'), $tbc_dungeons)
                .$render_simple_card($t('prog_tbc_r'), $t('prog_done'), $t('prog_raids'), $tbc_raids)
              .'</div>
              <div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_wot')).'</div>
              <div class="mm-prog-wrap">'
                .$render_simple_card($t('prog_wot_d'), $t('prog_done'), $t('prog_dung'), $wotlk_dungeons)
                .$wotlk_raid_card
              .'</div>'
              .$wotlk_hm_row
              .'';

        // Sección Metalogros (PvE Glory + Mundo/PvP/Eventos)
        $sqlm_m = new SQL;
        $sqlm_m->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $sqlc_m = new SQL;
        $sqlc_m->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

        $meta_cats = array(
            array(
                'key' => 'prog_meta_pve',
                'lbl' => 'PvE Glory',
                'items' => array(
                    1289, // Classic Dungeonmaster
                    1288, // Classic Raider
                    1287, // Outland Dungeon Hero
                    1286, // Outland Raider
                    1658, // Champion of the Frozen Wastes
                    2136, // Glory of the Hero
                    2137, // Glory of the Raider (10 player)
                    2138, // Glory of the Raider (25 player)
                    2957, // Glory of the Ulduar Raider (10 player)
                    2958, // Glory of the Ulduar Raider (25 player)
                    4602, // Glory of the Icecrown Raider (10 player)
                    4603  // Glory of the Icecrown Raider (25 player)
                )
            ),
            array(
                'key' => 'prog_meta_misc',
                'lbl' => 'World/PvP',
                'items' => array(
                    46,   // World Explorer
                    array(1681, 1682), // The Loremaster (Alianza / Horda)
                    1516, // Accomplished Angler
                    array(1563, 1784), // Hail to the Chef (Alianza / Horda)
                    array(2144, 2145), // What a Long, Strange Trip It's Been (Alianza / Horda)
                    2090, // The Arena Master
                    array(230, 1174),  // Battlemaster (Alianza / Horda)
                    array(1175, 1176), // Justicar / Conqueror
                    array(1752, 2776), // Master of Wintergrasp (Alianza / Horda)
                    948 ? ($is_horde ? 762 : 948) : 948 // Ambassador of the Horde / Alliance
                )
            )
        );

        $m_all_ids = array();
        foreach ($meta_cats as $mc) {
            foreach ($mc['items'] as $it) {
                if (is_array($it)) { foreach ($it as $s) $m_all_ids[] = (int)$s; }
                else { $m_all_ids[] = (int)$it; }
            }
        }
        $m_in = implode(',', array_unique($m_all_ids));

        $m_names = array();
        $m_dbc_res = $sqlm_m->query("SELECT id, name01 FROM dbc_achievement WHERE id IN ($m_in)");
        while ($mrow = $sqlm_m->fetch_assoc($m_dbc_res)) {
            $m_names[(int)$mrow['id']] = $mrow['name01'];
        }

        $m_comp = array();
        $m_comp_res = $sqlc_m->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($m_in)");
        while ($crow = $sqlc_m->fetch_assoc($m_comp_res)) {
            $m_comp[(int)$crow['achievement']] = true;
        }

        $output .= '<div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_meta')).'</div><div class="mm-prog-wrap">';
        foreach ($meta_cats as $mc) {
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($mc['items'] as $it) {
                $done = false;
                $pick_id = 0;
                if (is_array($it)) {
                    foreach ($it as $sub_id) {
                        $sub_int = (int)$sub_id;
                        if ($pick_id === 0) $pick_id = $sub_int;
                        if (isset($m_comp[$sub_int])) {
                            $done = true;
                            $pick_id = $sub_int;
                            break;
                        }
                    }
                } else {
                    $pick_id = (int)$it;
                    $done = isset($m_comp[$pick_id]);
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($m_names[$pick_id]) ? $m_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($mc['items']);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($t($mc['key'])).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $mc['lbl']).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        }

        $output .= '</div>';

        // Sección Profesiones (Categorías 169=General, 170=Cooking, 171=Fishing, 172=First Aid)
        $sqlm_p = new SQL;
        $sqlm_p->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $sqlc_p = new SQL;
        $sqlc_p->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

        $prof_cats = array(
            array('cat' => 170, 'key' => 'prog_prof_cook', 'lbl' => 'Cooking'),
            array('cat' => 171, 'key' => 'prog_prof_fish', 'lbl' => 'Fishing'),
            array('cat' => 172, 'key' => 'prog_prof_fa',   'lbl' => 'First Aid'),
            array('cat' => 169, 'key' => 'prog_prof_gen',  'lbl' => 'General')
        );

        $p_res = $sqlm_p->query("SELECT id, categoryid, name01 FROM dbc_achievement WHERE categoryid IN (169, 170, 171, 172) AND (flags & 1) = 0 ORDER BY id ASC");
        $p_by_cat = array();
        $p_names = array();
        $p_all_ids = array();
        while ($prow = $sqlm_p->fetch_assoc($p_res)) {
            $pid = (int)$prow['id'];
            $pcat = (int)$prow['categoryid'];
            $pnm = $prow['name01'];
            $p_names[$pid] = $pnm;
            $p_all_ids[] = $pid;
            if (!isset($p_by_cat[$pcat][$pnm])) {
                $p_by_cat[$pcat][$pnm] = array();
            }
            $p_by_cat[$pcat][$pnm][] = $pid;
        }

        $p_comp = array();
        if (!empty($p_all_ids)) {
            $p_in = implode(',', $p_all_ids);
            $p_comp_res = $sqlc_p->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($p_in)");
            while ($crow = $sqlc_p->fetch_assoc($p_comp_res)) {
                $p_comp[(int)$crow['achievement']] = true;
            }
        }

        $output .= '<div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_prof')).'</div><div class="mm-prog-wrap">';
        foreach ($prof_cats as $pc) {
            $items = isset($p_by_cat[$pc['cat']]) ? array_values($p_by_cat[$pc['cat']]) : array();
            if (empty($items)) continue;
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($items as $it) {
                $done = false;
                $pick_id = 0;
                foreach ($it as $sub_id) {
                    $sub_int = (int)$sub_id;
                    if ($pick_id === 0) $pick_id = $sub_int;
                    if (isset($p_comp[$sub_int])) {
                        $done = true;
                        $pick_id = $sub_int;
                        break;
                    }
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($p_names[$pick_id]) ? $p_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($items);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($t($pc['key'])).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $pc['lbl']).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        }

        $output .= '</div>
              <div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_pvp')).'</div>
              <div class="mm-prog-wrap">';

        foreach ($pvp_categories as $cat) {
            $output .= $render_simple_card($t($cat['key']), $t('prog_done'), $cat['lbl'], $cat['items']);
        }

        // Reconnect explicitly to mmfpm and characters DB to ensure active handles
        $sqlm_q = new SQL;
        $sqlm_q->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
        $sqlc_q = new SQL;
        $sqlc_q->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

        // 3.3.5a Quest Achievements split into General, Eastern Kingdoms, Kalimdor, Outland, and Northrend
        // (Includes zone/loremaster & major questline achievements for EK and Kalimdor)
                $q_cats_split = array(
            array(
                'key' => 'prog_q_gen',
                'lbl' => 'General',
                'ids' => array(
                    503, 504, 505, 506, 507, 508, 1576, 978,
                    973, 974, 975, 976, 977,
                    31, 941, 1560, 1182, array(1681, 1682)
                )
            ),
            array(
                'key' => 'prog_q_cls',
                'lbl' => 'Classic',
                'ids' => array(940, array(1676, 1677), array(1678, 1680))
            ),
            array(
                'key' => 'prog_q_out',
                'lbl' => 'Outland',
                'ids' => array(1189, 1190, 1191, 1192, 1193, 1194, 1195, array(1262, 1274))
            ),
            array(
                'key' => 'prog_q_nr',
                'lbl' => 'Northrend',
                'ids' => array(
                    array(33, 1358),
                    array(35, 1356),
                    array(37, 1359),
                    39,
                    array(36, 1357),
                    38, 40, array(41, 1360)
                )
            )
        );

        $q_all_ids = array();
        foreach ($q_cats_split as $qc) {
            foreach ($qc['ids'] as $it) {
                if (is_array($it)) { foreach ($it as $s) $q_all_ids[] = (int)$s; }
                else { $q_all_ids[] = (int)$it; }
            }
        }
        $q_in = implode(',', array_unique($q_all_ids));

        $q_names = array();
        $q_dbc_res = $sqlm_q->query("SELECT id, name01 FROM dbc_achievement WHERE id IN ($q_in)");
        while ($qrow = $sqlm_q->fetch_assoc($q_dbc_res)) {
            $q_names[(int)$qrow['id']] = $qrow['name01'];
        }

        $q_comp = array();
        $q_comp_res = $sqlc_q->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($q_in)");
        while ($crow = $sqlc_q->fetch_assoc($q_comp_res)) {
            $q_comp[(int)$crow['achievement']] = true;
        }

        $output .= '</div><div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_q')).'</div><div class="mm-prog-wrap">';
        foreach ($q_cats_split as $qc) {
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($qc['ids'] as $it) {
                $done = false;
                $pick_id = 0;
                if (is_array($it)) {
                    foreach ($it as $sub_id) {
                        $sub_int = (int)$sub_id;
                        if ($pick_id === 0) $pick_id = $sub_int;
                        if (isset($q_comp[$sub_int])) {
                            $done = true;
                            $pick_id = $sub_int;
                            break;
                        }
                    }
                } else {
                    $pick_id = (int)$it;
                    $done = isset($q_comp[$pick_id]);
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($q_names[$pick_id]) ? $q_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($qc['ids']);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($t($qc['key'])).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $qc['lbl']).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        }

        // Sección Reputación
        $rep_cats = array(
            array('cat' => 201,   'key' => 'prog_rep_gen', 'lbl' => 'General'),
            array('cat' => 14864, 'key' => 'prog_rep_cls', 'lbl' => 'Classic'),
            array('cat' => 14865, 'key' => 'prog_rep_tbc', 'lbl' => 'Outland'),
            array('cat' => 14866, 'key' => 'prog_rep_wot', 'lbl' => 'Northrend')
        );

        $rep_exclude = $is_horde ? array(948, 899) : array(762, 901);
        $r_dbc_res = $sqlm_q->query("SELECT id, categoryid, name01 FROM dbc_achievement WHERE categoryid IN (201, 14864, 14865, 14866) AND (flags & 1) = 0 ORDER BY id ASC");
        $r_by_cat = array();
        $r_names = array();
        $r_all_ids = array();
        while ($rrow = $sqlm_q->fetch_assoc($r_dbc_res)) {
            $rid = (int)$rrow['id'];
            if (in_array($rid, $rep_exclude)) continue;
            if ($rid === 1638) $rrow['categoryid'] = 14865;
            $rcat = (int)$rrow['categoryid'];
            $rnm = $rrow['name01'];
            $r_names[$rid] = $rnm;
            $r_all_ids[] = $rid;
            if (!isset($r_by_cat[$rcat][$rnm])) {
                $r_by_cat[$rcat][$rnm] = array();
            }
            $r_by_cat[$rcat][$rnm][] = $rid;
        }

        $r_comp = array();
        if (!empty($r_all_ids)) {
            $r_in = implode(',', $r_all_ids);
            $r_comp_res = $sqlc_q->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($r_in)");
            while ($crow = $sqlc_q->fetch_assoc($r_comp_res)) {
                $r_comp[(int)$crow['achievement']] = true;
            }
        }

        $output .= '</div><div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_rep')).'</div><div class="mm-prog-wrap">';
        foreach ($rep_cats as $rc) {
            $items = isset($r_by_cat[$rc['cat']]) ? array_values($r_by_cat[$rc['cat']]) : array();
            if (empty($items)) continue;
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($items as $it) {
                $done = false;
                $pick_id = 0;
                foreach ($it as $sub_id) {
                    $sub_int = (int)$sub_id;
                    if ($pick_id === 0) $pick_id = $sub_int;
                    if (isset($r_comp[$sub_int])) {
                        $done = true;
                        $pick_id = $sub_int;
                        break;
                    }
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($r_names[$pick_id]) ? $r_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($items);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($t($rc['key'])).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $rc['lbl']).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        }

        // Sección Exploración
        $exp_cats = array(
            array('cat' => 14777, 'key' => 'prog_exp_ek',  'lbl' => 'E. Kingdoms'),
            array('cat' => 14778, 'key' => 'prog_exp_kal', 'lbl' => 'Kalimdor'),
            array('cat' => 14779, 'key' => 'prog_exp_out', 'lbl' => 'Outland'),
            array('cat' => 14780, 'key' => 'prog_exp_nr',  'lbl' => 'Northrend'),
            array('cat' => 97,    'key' => 'prog_exp_gen', 'lbl' => 'General')
        );

        $e_res = $sqlm_q->query("SELECT id, categoryid, name01 FROM dbc_achievement WHERE categoryid IN (97, 14777, 14778, 14779, 14780) AND (flags & 1) = 0 ORDER BY id ASC");
        $e_by_cat = array();
        $e_names = array();
        $e_all_ids = array();
        while ($erow = $sqlm_q->fetch_assoc($e_res)) {
            $eid = (int)$erow['id'];
            $ecat = (int)$erow['categoryid'];
            $enm = $erow['name01'];
            $e_names[$eid] = $enm;
            $e_all_ids[] = $eid;
            if (!isset($e_by_cat[$ecat][$enm])) {
                $e_by_cat[$ecat][$enm] = array();
            }
            $e_by_cat[$ecat][$enm][] = $eid;
        }

        $e_comp = array();
        if (!empty($e_all_ids)) {
            $e_in = implode(',', $e_all_ids);
            $e_comp_res = $sqlc_q->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($e_in)");
            while ($crow = $sqlc_q->fetch_assoc($e_comp_res)) {
                $e_comp[(int)$crow['achievement']] = true;
            }
        }

        $output .= '</div><div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_exp')).'</div><div class="mm-prog-wrap">';
        foreach ($exp_cats as $ec) {
            $items = isset($e_by_cat[$ec['cat']]) ? array_values($e_by_cat[$ec['cat']]) : array();
            if (empty($items)) continue;
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($items as $it) {
                $done = false;
                $pick_id = 0;
                foreach ($it as $sub_id) {
                    $sub_int = (int)$sub_id;
                    if ($pick_id === 0) $pick_id = $sub_int;
                    if (isset($e_comp[$sub_int])) {
                        $done = true;
                        $pick_id = $sub_int;
                        break;
                    }
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($e_names[$pick_id]) ? $e_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($items);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($t($ec['key'])).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $ec['lbl']).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        }


// Sección General (Categoría 92 de 3.3.5a DBC)
        $g_res = $sqlm_q->query("SELECT id, name01 FROM dbc_achievement WHERE categoryid = 92 AND (flags & 1) = 0 ORDER BY id ASC");
        $g_grouped = array();
        $g_names = array();
        $g_all_ids = array();
        while ($grow = $sqlm_q->fetch_assoc($g_res)) {
            $gid = (int)$grow['id'];
            $gnm = $grow['name01'];
            $g_names[$gid] = $gnm;
            $g_all_ids[] = $gid;
            if (!isset($g_grouped[$gnm])) {
                $g_grouped[$gnm] = array();
            }
            $g_grouped[$gnm][] = $gid;
        }

        $g_comp = array();
        if (!empty($g_all_ids)) {
            $g_in = implode(',', $g_all_ids);
            $g_comp_res = $sqlc_q->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($g_in)");
            while ($crow = $sqlc_q->fetch_assoc($g_comp_res)) {
                $g_comp[(int)$crow['achievement']] = true;
            }
        }

        $g_items = array_values($g_grouped);
        // Dividimos en bloques de hasta 20 logros por quesito para que los gráficos se vean nítidos
        $g_chunks = !empty($g_items) ? array_chunk($g_items, 20) : array();

        $output .= '</div><div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_gen')).'</div><div class="mm-prog-wrap">';
        $chunk_idx = 1;
        $total_chunks = count($g_chunks);
        foreach ($g_chunks as $chunk) {
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($chunk as $it) {
                $done = false;
                $pick_id = 0;
                foreach ($it as $sub_id) {
                    $sub_int = (int)$sub_id;
                    if ($pick_id === 0) $pick_id = $sub_int;
                    if (isset($g_comp[$sub_int])) {
                        $done = true;
                        $pick_id = $sub_int;
                        break;
                    }
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($g_names[$pick_id]) ? $g_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($chunk);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $card_title = $t('prog_gen_all') . ($total_chunks > 1 ? " ($chunk_idx/$total_chunks)" : "");
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($card_title).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, 'General').'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
            $chunk_idx++;
        }

        // Sección Eventos del Mundo (World Events)
        $we_cats = array(
            array('cat' => 155,   'key' => 'prog_we_gen',   'lbl' => 'General'),
            array('cat' => 160,   'key' => 'prog_we_lunar', 'lbl' => 'Lunar'),
            array('cat' => 187,   'key' => 'prog_we_love',  'lbl' => 'Love'),
            array('cat' => 159,   'key' => 'prog_we_noble', 'lbl' => 'Noblegarden'),
            array('cat' => 163,   'key' => 'prog_we_child', 'lbl' => 'Children'),
            array('cat' => 161,   'key' => 'prog_we_mid',   'lbl' => 'Midsummer'),
            array('cat' => 162,   'key' => 'prog_we_brew',  'lbl' => 'Brewfest'),
            array('cat' => 158,   'key' => 'prog_we_hal',   'lbl' => "Hallow's"),
            array('cat' => 14981, 'key' => 'prog_we_pil',   'lbl' => 'Pilgrim'),
            array('cat' => 156,   'key' => 'prog_we_win',   'lbl' => 'Winter Veil'),
            array('cat' => 14941, 'key' => 'prog_we_arg',   'lbl' => 'Argent'),
            array('cat' => 157,   'key' => 'prog_we_fish',  'lbl' => 'Fishing'),
            array('cat' => 165,   'key' => 'prog_we_arena', 'lbl' => 'Arena')
        );

        $we_cat_ids = array(155, 160, 187, 159, 163, 161, 162, 158, 14981, 156, 14941, 157, 165);
        // Excluir logros exclusivos de la facción contraria en Eventos del Mundo
        // (ej. 1038 The Flame Warden = Alianza, 1039 The Flame Keeper = Horda, y sub-logros de facción)
        $we_exclude = $is_horde
            ? array(1038, 1034, 1035, 1022, 1023, 1024, 1028, 1029, 1030, 1684, 1254, 1656, 3556, 3576, 3596, 2760, 2761, 2762, 2763, 2764, 2770, 2771, 2772, 2777, 2778, 2779, 2780, 2781, 2782, 2788, 2817, 3676)
            : array(1039, 1036, 1037, 1025, 1026, 1027, 1031, 1032, 1033, 1683, 259,  1657, 3557, 3577, 3597, 2765, 2766, 2767, 2768, 2769, 2773, 2774, 2775, 2783, 2784, 2785, 2786, 2787, 2816, 3677);

        $we_res = $sqlm_q->query("SELECT id, categoryid, name01 FROM dbc_achievement WHERE categoryid IN (" . implode(',', $we_cat_ids) . ") AND (flags & 1) = 0 ORDER BY id ASC");
        $we_by_cat = array();
        $we_names = array();
        $we_all_ids = array();
        while ($wrow = $sqlm_q->fetch_assoc($we_res)) {
            $wid = (int)$wrow['id'];
            if (in_array($wid, $we_exclude)) continue;
            $wcat = (int)$wrow['categoryid'];
            $wnm = $wrow['name01'];
            $we_names[$wid] = $wnm;
            $we_all_ids[] = $wid;
            if (!isset($we_by_cat[$wcat][$wnm])) {
                $we_by_cat[$wcat][$wnm] = array();
            }
            $we_by_cat[$wcat][$wnm][] = $wid;
        }

        $we_comp = array();
        if (!empty($we_all_ids)) {
            $we_in = implode(',', $we_all_ids);
            $we_comp_res = $sqlc_q->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN ($we_in)");
            while ($crow = $sqlc_q->fetch_assoc($we_comp_res)) {
                $we_comp[(int)$crow['achievement']] = true;
            }
        }

        $output .= '</div><div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_we')).'</div><div class="mm-prog-wrap">';
        foreach ($we_cats as $wc) {
            $items = isset($we_by_cat[$wc['cat']]) ? array_values($we_by_cat[$wc['cat']]) : array();
            if (empty($items)) continue;
            $done_cnt = 0; $slices = array(); $li = '';
            foreach ($items as $it) {
                $done = false;
                $pick_id = 0;
                foreach ($it as $sub_id) {
                    $sub_int = (int)$sub_id;
                    if ($pick_id === 0) $pick_id = $sub_int;
                    if (isset($we_comp[$sub_int])) {
                        $done = true;
                        $pick_id = $sub_int;
                        break;
                    }
                }
                if ($done) $done_cnt++;
                $slices[] = $done;
                $nm = htmlspecialchars(isset($we_names[$pick_id]) ? $we_names[$pick_id] : "Achievement #$pick_id");
                $li .= $done
                    ? '<li><span class="mm-ok">[&#10004;]</span> '.$nm.'</li>'
                    : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.$nm.'</span></li>';
            }
            $tot = count($items);
            $pct = $tot > 0 ? floor(($done_cnt / $tot) * 100) : 0;
            $output .= '<div class="mm-prog-card">
                <div class="mm-prog-title">'.htmlspecialchars($t($wc['key'])).'</div>
                <div class="mm-prog-sub">'.$t('prog_done').': <span class="mm-ok">'.$done_cnt.'/'.$tot.'</span> (<b>'.$pct.'%</b>)</div>
                '.$draw_pie($slices, $pct, $wc['lbl']).'
                <ul class="mm-prog-list">'.$li.'</ul>
            </div>';
        }

        // Sección Proezas de Fuerza (Feats of Strength - Categoría 81)
        $fos_dbc_res = $sqlm_q->query("SELECT id, name01, description01 FROM dbc_achievement WHERE categoryid = 81");
        $fos_info = array();
        $fos_all_ids = array();
        while ($frow = $sqlm_q->fetch_assoc($fos_dbc_res)) {
            $fid = (int)$frow['id'];
            $fos_info[$fid] = array('name' => $frow['name01'], 'desc' => $frow['description01']);
            $fos_all_ids[] = $fid;
        }

        $fos_unlocked = array();
        if (!empty($fos_all_ids)) {
            $fos_in = implode(',', $fos_all_ids);
            $fos_comp_res = $sqlc_q->query("SELECT achievement, date FROM character_achievement WHERE guid = $id AND achievement IN ($fos_in) ORDER BY date DESC");
            while ($crow = $sqlc_q->fetch_assoc($fos_comp_res)) {
                $fid = (int)$crow['achievement'];
                $fos_unlocked[] = array(
                    'id'   => $fid,
                    'name' => isset($fos_info[$fid]) ? $fos_info[$fid]['name'] : "Feat #$fid",
                    'desc' => isset($fos_info[$fid]) ? $fos_info[$fid]['desc'] : '',
                    'date' => date('Y-m-d', (int)$crow['date'])
                );
            }
        }

        if (!empty($fos_unlocked)) {
            $fos_cnt = count($fos_unlocked);
            $fos_li = '';
            foreach ($fos_unlocked as $fu) {
                $tip = !empty($fu['desc']) ? ' title="'.htmlspecialchars($fu['desc']).'"' : '';
                $fos_li .= '<li style="display:flex; justify-content:space-between; align-items:center; gap:12px; border-bottom:1px solid rgba(255,209,0,0.12); padding:3px 0;"'.$tip.'>
                    <span><span style="color:#ffd100; font-weight:bold;">[&#9733;]</span> <span style="color:#f8fafc;">'.htmlspecialchars($fu['name']).'</span></span>
                    <span style="color:#73d2ff; font-size:11px; white-space:nowrap;">'.$fu['date'].'</span>
                </li>';
            }

            $output .= '</div><div class="mm-sec-title">'.htmlspecialchars($t('prog_sec_fos')).'</div><div class="mm-prog-wrap">
                <div class="mm-prog-card" style="width:100%; max-width:632px; border-color:#c79c3e;">
<div style="display:flex; align-items:center; justify-content:center; gap:18px; margin-bottom:12px;"><span style="font-size:34px; line-height:1; display:inline-block; filter:drop-shadow(0 0 6px rgba(255,209,0,0.6));">&#128170;</span><div style="text-align:center;"><div class="mm-prog-title" style="color:#ffd100; margin-bottom:2px;">'.htmlspecialchars($t('prog_sec_fos')).'</div><div class="mm-prog-sub" style="margin-bottom:0;">'.$t('prog_fos_sub').': <span style="color:#ffd100; font-weight:bold;">'.$fos_cnt.'</span></div></div><span style="font-size:34px; line-height:1; display:inline-block; transform:scaleX(-1); filter:drop-shadow(0 0 6px rgba(255,209,0,0.6));">&#128170;</span></div>
                  <ul class="mm-prog-list">'.$fos_li.'</ul>
                </div>';
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
