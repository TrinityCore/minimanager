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

        // 20 Mazmorras de WoW Clásico (ID Logro, Nombre)
        $classic_dungeons = array(
            array(628, "Sima Ígnea"),
            array(629, "Cuevas de los Lamentos"),
            array(630, "Las Minas de la Muerte"),
            array(631, "Castillo de Colmillo Oscuro"),
            array(632, "Cavernas de Brazanegra"),
            array(633, "Mazmorras de Ventormenta"),
            array(634, "Gnomeregan"),
            array(635, "Horado Rajacieno"),
            array(636, "Zahúrda Rajacieno"),
            array(637, "Monasterio Escarlata"),
            array(638, "Uldaman"),
            array(639, "Zul'Farrak"),
            array(640, "Maraudon"),
            array(641, "Templo Sumergido"),
            array(642, "Profundidades de Roca Negra"),
            array(643, "Cumbre de Roca Negra Inferior"),
            array(1307, "Cumbre de Roca Negra Superior"),
            array(644, "La Masacre"),
            array(645, "Scholomance"),
            array(646, "Stratholme")
        );

        // 6 Bandas de WoW Clásico (ID Logro, Nombre)
        $classic_raids = array(
            array(688, "Zul'Gurub"),
            array(689, "Ruinas de Ahn'Qiraj"),
            array(686, "Núcleo de Magma"),
            array(685, "Guarida de Alanegra"),
            array(687, "Templo de Ahn'Qiraj"),
            array(684, "Guarida de Onyxia (Nv. 60)")
        );

        // 16 Mazmorras de Terrallende (Normal, Heroico, Nombre)
        $tbc_dungeons = array(
            array(647, 667, "Murallas del Fuego Infernal"),
            array(648, 668, "El Horno de Sangre"),
            array(651, 678, "Las Salas Arrasadas"),
            array(649, 669, "Recinto de los Esclavos"),
            array(650, 670, "La Sotiénaga"),
            array(652, 671, "La Cámara de Vapor"),
            array(653, 672, "Tumbas de Maná"),
            array(654, 673, "Criptas Auchenai"),
            array(656, 674, "Salas Sethekk"),
            array(657, 675, "Laberinto de las Sombras"),
            array(655, 676, "Antiguas Laderas de Trabalomas"),
            array(658, 677, "La Apertura del Portal Oscuro"),
            array(659, 679, "El Mechanar"),
            array(660, 680, "El Invernáculo"),
            array(661, 681, "El Arcatraz"),
            array(662, 682, "Bancal del Magister")
        );

        // 8 Bandas de Terrallende (ID Logro, Nombre)
        $tbc_raids = array(
            array(690, "Karazhan"),
            array(692, "Guarida de Gruul"),
            array(693, "Guarida de Magtheridon"),
            array(694, "Caverna Santuario Serpiente"),
            array(696, "El Ojo (Castillo de la Tempestad)"),
            array(695, "La Batalla del Monte Hyjal"),
            array(697, "El Templo Oscuro"),
            array(698, "Meseta de La Fuente del Sol")
        );

        // 16 Mazmorras de Rasganorte (Normal, Heroico, Nombre)
        $wotlk_dungeons = array(
            array(477,  489,  "Fortaleza de Utgarde"),
            array(478,  490,  "El Nexo"),
            array(480,  491,  "Azjol-Nerub"),
            array(481,  492,  "Ahn'kahet: El Antiguo Reino"),
            array(482,  493,  "Fortaleza de Drak'Tharon"),
            array(483,  494,  "El Bastión Violeta"),
            array(484,  495,  "Gundrak"),
            array(485,  496,  "Cámaras de Piedra"),
            array(486,  497,  "Cámaras de Relámpagos"),
            array(487,  498,  "El Oculus"),
            array(488,  499,  "Pináculo de Utgarde"),
            array(479,  500,  "La Matanza de Stratholme"),
            array(4296, 4298, "Prueba del Campeón"),
            array(4516, 4519, "La Forja de Almas"),
            array(4517, 4520, "Foso de Saron"),
            array(4518, 4521, "Cámaras de Reflexión")
        );

        // 9 Bandas de Rasganorte (10j, 25j, Nombre)
        $wotlk_raids = array(
            array(576,  577,  "Naxxramas"),
            array(1876, 625,  "El Sagrario Obsidiana"),
            array(622,  623,  "El Ojo de la Eternidad"),
            array(1722, 1721, "La Cámara de Archavon"),
            array(2894, 2895, "Ulduar"),
            array(3917, 3916, "Prueba del Cruzado"),
            array(4396, 4397, "La Guarida de Onyxia"),
            array(4532, 4608, "Ciudadela de la Corona de Hielo"),
            array(4817, 4815, "El Sagrario Rubí")
        );

        $all_ids = array();
        foreach ($classic_dungeons as $d) { list($did, $nm) = $d; $all_ids[] = $did; }
        foreach ($classic_raids as $r)    { list($rid, $nm) = $r; $all_ids[] = $rid; }
        foreach ($tbc_dungeons as $d)     { list($n, $h, $nm) = $d; $all_ids[] = $n; $all_ids[] = $h; }
        foreach ($tbc_raids as $r)        { list($rid, $nm) = $r;   $all_ids[] = $rid; }
        foreach ($wotlk_dungeons as $d)   { list($n, $h, $nm) = $d; $all_ids[] = $n; $all_ids[] = $h; }
        foreach ($wotlk_raids as $r)      { list($r10, $r25, $nm) = $r; $all_ids[] = $r10; $all_ids[] = $r25; }

        $completed_ach = array();
        $ach_res = $sqlc->query("SELECT achievement FROM character_achievement WHERE guid = $id AND achievement IN (" . implode(',', $all_ids) . ")");
        while ($row = $sqlc->fetch_assoc($ach_res)) {
            $completed_ach[(int)$row['achievement']] = true;
        }

        $cls_d_done = 0; $cls_d_slices = array();
        foreach ($classic_dungeons as $d) {
            list($did, $nm) = $d;
            $done = isset($completed_ach[$did]);
            if ($done) $cls_d_done++;
            $cls_d_slices[] = array('name' => $nm, 'done' => $done);
        }
        $cls_d_pct = floor(($cls_d_done / 20) * 100);

        $cls_r_done = 0; $cls_r_slices = array();
        foreach ($classic_raids as $r) {
            list($rid, $nm) = $r;
            $done = isset($completed_ach[$rid]);
            if ($done) $cls_r_done++;
            $cls_r_slices[] = array('name' => $nm, 'done' => $done);
        }
        $cls_r_pct = floor(($cls_r_done / 6) * 100);

        $tbc_d_done = 0; $tbc_d_slices = array();
        foreach ($tbc_dungeons as $d) {
            list($n, $h, $nm) = $d;
            $done = isset($completed_ach[$n]) || isset($completed_ach[$h]);
            if ($done) $tbc_d_done++;
            $tbc_d_slices[] = array('name' => $nm, 'done' => $done);
        }
        $tbc_d_pct = floor(($tbc_d_done / 16) * 100);

        $tbc_r_done = 0; $tbc_r_slices = array();
        foreach ($tbc_raids as $r) {
            list($rid, $nm) = $r;
            $done = isset($completed_ach[$rid]);
            if ($done) $tbc_r_done++;
            $tbc_r_slices[] = array('name' => $nm, 'done' => $done);
        }
        $tbc_r_pct = floor(($tbc_r_done / 8) * 100);

        $dungeon_done = 0; $dungeon_slices = array();
        foreach ($wotlk_dungeons as $d) {
            list($n, $h, $nm) = $d;
            $done = isset($completed_ach[$n]) || isset($completed_ach[$h]);
            if ($done) $dungeon_done++;
            $dungeon_slices[] = array('name' => $nm, 'done' => $done);
        }
        $dungeon_pct = floor(($dungeon_done / 16) * 100);

        $raid_done = 0; $raid_slices = array(); $raid_rows = array();
        foreach ($wotlk_raids as $r) {
            list($r10, $r25, $nm) = $r;
            $d10 = isset($completed_ach[$r10]);
            $d25 = isset($completed_ach[$r25]);
            if ($d10) $raid_done++;
            if ($d25) $raid_done++;
            $raid_slices[] = $d10;
            $raid_slices[] = $d25;
            $raid_rows[] = array('name' => $nm, 'd10' => $d10, 'd25' => $d25);
        }
        $raid_pct = floor(($raid_done / 18) * 100);

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

        $cls_d_bools = array_map(function($x) { return $x['done']; }, $cls_d_slices);
        $cls_r_bools = array_map(function($x) { return $x['done']; }, $cls_r_slices);
        $tbc_d_bools = array_map(function($x) { return $x['done']; }, $tbc_d_slices);
        $tbc_r_bools = array_map(function($x) { return $x['done']; }, $tbc_r_slices);
        $d_bools     = array_map(function($x) { return $x['done']; }, $dungeon_slices);

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
              <div class="mm-prog-wrap">
                <div class="mm-prog-card">
                  <div class="mm-prog-title">Mazmorras Clásicas (Vanilla)</div>
                  <div class="mm-prog-sub">Completadas: <span class="mm-ok">'.$cls_d_done.'/20</span> (<b>'.$cls_d_pct.'%</b>)</div>
                  '.$draw_pie($cls_d_bools, $cls_d_pct, 'Mazmorras').'
                  <ul class="mm-prog-list">';

        foreach ($cls_d_slices as $ds) {
            $output .= $ds['done']
                ? '<li><span class="mm-ok">[&#10004;]</span> '.htmlspecialchars($ds['name']).'</li>'
                : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.htmlspecialchars($ds['name']).'</span></li>';
        }

        $output .= '
                  </ul>
                </div>
                <div class="mm-prog-card">
                  <div class="mm-prog-title">Bandas Clásicas (Vanilla)</div>
                  <div class="mm-prog-sub">Completadas: <span class="mm-ok">'.$cls_r_done.'/6</span> (<b>'.$cls_r_pct.'%</b>)</div>
                  '.$draw_pie($cls_r_bools, $cls_r_pct, 'Raids').'
                  <ul class="mm-prog-list">';

        foreach ($cls_r_slices as $rs) {
            $output .= $rs['done']
                ? '<li><span class="mm-ok">[&#10004;]</span> '.htmlspecialchars($rs['name']).'</li>'
                : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.htmlspecialchars($rs['name']).'</span></li>';
        }

        $output .= '
                  </ul>
                </div>
              </div>
              <div class="mm-prog-wrap">
                <div class="mm-prog-card">
                  <div class="mm-prog-title">Mazmorras de Terrallende (TBC)</div>
                  <div class="mm-prog-sub">Completadas: <span class="mm-ok">'.$tbc_d_done.'/16</span> (<b>'.$tbc_d_pct.'%</b>)</div>
                  '.$draw_pie($tbc_d_bools, $tbc_d_pct, 'Mazmorras').'
                  <ul class="mm-prog-list">';

        foreach ($tbc_d_slices as $ds) {
            $output .= $ds['done']
                ? '<li><span class="mm-ok">[&#10004;]</span> '.htmlspecialchars($ds['name']).'</li>'
                : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.htmlspecialchars($ds['name']).'</span></li>';
        }

        $output .= '
                  </ul>
                </div>
                <div class="mm-prog-card">
                  <div class="mm-prog-title">Bandas de Terrallende (TBC)</div>
                  <div class="mm-prog-sub">Completadas: <span class="mm-ok">'.$tbc_r_done.'/8</span> (<b>'.$tbc_r_pct.'%</b>)</div>
                  '.$draw_pie($tbc_r_bools, $tbc_r_pct, 'Raids').'
                  <ul class="mm-prog-list">';

        foreach ($tbc_r_slices as $rs) {
            $output .= $rs['done']
                ? '<li><span class="mm-ok">[&#10004;]</span> '.htmlspecialchars($rs['name']).'</li>'
                : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.htmlspecialchars($rs['name']).'</span></li>';
        }

        $output .= '
                  </ul>
                </div>
              </div>
              <div class="mm-prog-wrap">
                <div class="mm-prog-card">
                  <div class="mm-prog-title">Mazmorras de Rasganorte (WotLK)</div>
                  <div class="mm-prog-sub">Completadas: <span class="mm-ok">'.$dungeon_done.'/16</span> (<b>'.$dungeon_pct.'%</b>)</div>
                  '.$draw_pie($d_bools, $dungeon_pct, 'Mazmorras').'
                  <ul class="mm-prog-list">';

        foreach ($dungeon_slices as $ds) {
            $output .= $ds['done']
                ? '<li><span class="mm-ok">[&#10004;]</span> '.htmlspecialchars($ds['name']).'</li>'
                : '<li><span class="mm-no">[&#10008;]</span> <span class="mm-dim">'.htmlspecialchars($ds['name']).'</span></li>';
        }

        $output .= '
                  </ul>
                </div>
                <div class="mm-prog-card">
                  <div class="mm-prog-title">Bandas de Rasganorte (10 / 25)</div>
                  <div class="mm-prog-sub">Modalidades: <span class="mm-ok">'.$raid_done.'/18</span> (<b>'.$raid_pct.'%</b>)</div>
                  '.$draw_pie($raid_slices, $raid_pct, 'Raids').'
                  <ul class="mm-prog-list">';

        foreach ($raid_rows as $rr) {
            $t10 = $rr['d10'] ? '<span class="mm-ok">[10&#10004;]</span>' : '<span class="mm-no">[10&#10008;]</span>';
            $t25 = $rr['d25'] ? '<span class="mm-ok">[25&#10004;]</span>' : '<span class="mm-no">[25&#10008;]</span>';
            $output .= '<li>'.$t10.' '.$t25.' '.htmlspecialchars($rr['name']).'</li>';
        }

        $output .= '
                  </ul>
                </div>
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
