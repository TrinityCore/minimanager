<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS SPELL
//########################################################################################################################
function char_spell(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char, $lang_extra_char,
            $realm_id, $characters_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name,
            $spell_datasite, $itemperpage;
    wowhead_tt();

    require_once 'core/char/char_security.php';

    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start)); else $start=0;

    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender
                            FROM characters WHERE guid = '.$id.' LIMIT 1');

    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            $sqlm = new SQL; $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
            $sv = isset($_GET['spell_search']) ? trim($_GET['spell_search']) : '';
            $su = ($sv !== '') ? '&amp;spell_search='.urlencode($sv) : '';
            $ft = isset($_GET['spell_type']) ? trim($_GET['spell_type']) : 'all';
    $fp = isset($_GET['spell_prof']) ? intval($_GET['spell_prof']) : 0;
    $profs = array(
      171 => 'Alquimia', 164 => 'Herrería', 333 => 'Encantamiento', 202 => 'Ingeniería',
      182 => 'Herboristería', 773 => 'Inscripción', 755 => 'Joyería', 165 => 'Peletería',
      186 => 'Minería', 393 => 'Desuello', 197 => 'Sastrería', 185 => 'Cocina',
      129 => 'Primeros Auxilios', 356 => 'Pesca', 762 => 'Equitación'
    );
    if ($ft !== 'all') $su .= '&amp;spell_type='.urlencode($ft);
    if ($fp > 0) $su .= '&amp;spell_prof='.$fp;
    $ft = isset($_GET['spell_type']) ? trim($_GET['spell_type']) : 'all';
    $fp = isset($_GET['spell_prof']) ? intval($_GET['spell_prof']) : 0;
    $profs = array(
      171 => 'Alquimia', 164 => 'Herrería', 333 => 'Encantamiento', 202 => 'Ingeniería',
      182 => 'Herboristería', 773 => 'Inscripción', 755 => 'Joyería', 165 => 'Peletería',
      186 => 'Minería', 393 => 'Desuello', 197 => 'Sastrería', 185 => 'Cocina',
      129 => 'Primeros Auxilios', 356 => 'Pesca', 762 => 'Equitación'
    );
    if ($ft !== 'all') $su .= '&amp;spell_type='.urlencode($ft);
    if ($fp > 0) $su .= '&amp;spell_prof='.$fp;
    $sw = ' guid = '.$id.' AND active = 1';
    $pids = implode(',', array_keys($profs));

    // Obtener todos los hechizos activos del personaje para calcular contadores
    $char_spells = array();
    $qc = $sqlc->query('SELECT spell FROM character_spell WHERE guid = '.$id.' AND active = 1');
    while ($rw = $sqlc->fetch_row($qc)) { list($sp) = $rw; $char_spells[intval($sp)] = true; }
    $total_all = count($char_spells);

    // Identificar hechizos de talentos desde dbc_talent (rangos 1 a 5: field_4..field_8)
    $talent_map = array();
    $qt = $sqlm->query("SELECT field_4, field_5, field_6, field_7, field_8 FROM dbc_talent");
    while ($rw = $sqlm->fetch_row($qt)) {
      foreach ($rw as $tsp) {
        $tsp = intval($tsp);
        if ($tsp > 0) $talent_map[$tsp] = true;
      }
    }

    // Clasificar los hechizos del personaje usando dbc_skilllineability + dbc_skillline
    $class_map = array();
    $prof_map = array();
    $prof_counts = array_fill_keys(array_keys($profs), 0);
    $mount_map = array();
    $pet_map = array();

    if ($total_all > 0) {
      $sp_list = implode(',', array_keys($char_spells));
      $qa = $sqlm->query("SELECT a.field_2, a.field_1, l.field_1 FROM dbc_skilllineability a LEFT JOIN dbc_skillline l ON a.field_1 = l.id WHERE a.field_2 IN ($sp_list)");
      while ($rw = $sqlm->fetch_row($qa)) {
        list($sp, $sk, $cat) = $rw;
        $sp = intval($sp); $sk = intval($sk); $cat = intval($cat);
        if ($sk === 777) $mount_map[$sp] = true;
        elseif ($sk === 778) $pet_map[$sp] = true;
        elseif (isset($profs[$sk])) {
          if (!isset($prof_map[$sp])) {
            $prof_map[$sp] = true;
            $prof_counts[$sk]++;
          }
        } elseif ($cat === 7 && !isset($talent_map[$sp])) {
          $class_map[$sp] = true;
        }
      }
    }

    $cnt_talent = count(array_intersect_key($char_spells, $talent_map));
    $cnt_class  = count($class_map);
    $cnt_prof   = count($prof_map);
    $cnt_mount  = count($mount_map);
    $cnt_pet    = count($pet_map);

    // Resto de hechizos que no caen en ninguno de los grupos anteriores
    $known_grouped = $talent_map + $class_map + $prof_map + $mount_map + $pet_map;
    $other_spells = array_diff_key($char_spells, $known_grouped);
    $cnt_other = count($other_spells);

    if ($ft !== 'all') {
      $fids = array(0);
      if ($ft === 'class') $fids = array_merge($fids, array_keys($class_map));
      elseif ($ft === 'talent') $fids = array_merge($fids, array_keys(array_intersect_key($char_spells, $talent_map)));
      elseif ($ft === 'prof') {
        if ($fp > 0) {
          $qf = $sqlm->query("SELECT field_2 FROM dbc_skilllineability WHERE field_1 = $fp");
          while ($rw = $sqlm->fetch_row($qf)) { list($x) = $rw; $fids[] = intval($x); }
        } else {
          $fids = array_merge($fids, array_keys($prof_map));
        }
      }
      elseif ($ft === 'mount') $fids = array_merge($fids, array_keys($mount_map));
      elseif ($ft === 'pet') $fids = array_merge($fids, array_keys($pet_map));
      elseif ($ft === 'other') $fids = array_merge($fids, array_keys($other_spells));

      $sw .= ' AND spell IN ('.implode(',', $fids).')';
    }
    $pids = implode(',', array_keys($profs));
    if ($ft !== 'all') {
      $fq = '';
      if ($ft === 'class') $fq = "SELECT a.field_2 FROM dbc_skilllineability a JOIN dbc_skillline l ON a.field_1 = l.id WHERE l.field_1 = 7";
      elseif ($ft === 'prof') $fq = ($fp > 0) ? "SELECT field_2 FROM dbc_skilllineability WHERE field_1 = $fp" : "SELECT field_2 FROM dbc_skilllineability WHERE field_1 IN ($pids)";
      elseif ($ft === 'mount') $fq = "SELECT field_2 FROM dbc_skilllineability WHERE field_1 = 777";
      elseif ($ft === 'pet') $fq = "SELECT field_2 FROM dbc_skilllineability WHERE field_1 = 778";
      elseif ($ft === 'other') $fq = "SELECT a.field_2 FROM dbc_skilllineability a JOIN dbc_skillline l ON a.field_1 = l.id WHERE l.field_1 = 7 OR a.field_1 IN ($pids, 777, 778)";
      if ($fq !== '') {
        $qf = $sqlm->query($fq);
        $fids = array(0);
        while ($rw = $sqlm->fetch_row($qf)) { list($x) = $rw; $fids[] = intval($x); }
        $sw .= ($ft === 'other' ? ' AND spell NOT IN (' : ' AND spell IN (').implode(',', $fids).')';
      }
    }
            if ($sv !== '' || $ft !== 'all' || $fp > 0) {
                if (is_numeric($sv)) $sw .= ' AND spell = '.intval($sv);
                else {
                    $qp = $sqlm->query("SELECT id FROM dbc_spell WHERE field_136 LIKE '%".$sqlm->quote_smart($sv)."%'");
                    $ids = array(0);
                    while ($rw = $sqlm->fetch_row($qp)) { list($x) = $rw; $ids[] = intval($x); }
                    $sw .= ' AND spell IN ('.implode(',', $ids).')';
                }
            }
            $all_record = $sqlc->result($sqlc->query('SELECT count(spell) FROM character_spell WHERE'.$sw), 0);
            $result = $sqlc->query('SELECT spell FROM character_spell WHERE'.$sw.' ORDER BY spell ASC LIMIT '.$start.', '.$itemperpage);

            $output .= '
                        <center>
                            <div id="tab_content">
                                <h1>'.$lang_char['spells'].'</h1>
                                <br />';

            require_once 'core/char/char_header.php';
            $output .= '
                        <br /><div style="text-align:center; margin: 6px auto 10px auto;">
                        <form action="char_spell.php" method="get" name="form" style="margin-bottom: 6px;">
                            <input type="hidden" name="id" value="'.$id.'" />
                            <input type="hidden" name="realm" value="'.$realmid.'" />
                            <select name="spell_type" onchange="var s=document.getElementById(this.dataset.t);s.style.display=(this.value==this.dataset.p)?this.dataset.b:this.dataset.n;" data-t="spell_prof" data-p="prof" data-b="inline-block" data-n="none">
      <option value="all"'.($ft==='all'?' selected="selected"':'').'>Todos los hechizos ('.$total_all.')</option>
      <option value="class"'.($ft==='class'?' selected="selected"':'').'>Hechizos de clase ('.$cnt_class.')</option>
      <option value="talent"'.($ft==='talent'?' selected="selected"':'').'>Talentos ('.$cnt_talent.')</option>
      <option value="prof"'.($ft==='prof'?' selected="selected"':'').'>Hechizos de profesiones ('.$cnt_prof.')</option>
      <option value="mount"'.($ft==='mount'?' selected="selected"':'').'>Hechizos de monturas ('.$cnt_mount.')</option>
      <option value="pet"'.($ft==='pet'?' selected="selected"':'').'>Hechizos de mascotas ('.$cnt_pet.')</option>
      <option value="other"'.($ft==='other'?' selected="selected"':'').'>Otros hechizos ('.$cnt_other.')</option>
    </select>
    <select name="spell_prof" id="spell_prof" style="display:'.($ft==='prof'?'inline-block':'none').';">
      <option value="0">Todas las profesiones ('.$cnt_prof.')</option>';
    foreach ($profs as $pid => $pname) {
      $output .= '<option value="'.$pid.'"'.($fp==$pid?' selected="selected"':'').'>'.$pname.' ('.$prof_counts[$pid].')</option>';
    }
    $output .= '</select>
    <input type="text" name="spell_search" size="28" maxlength="50" autocomplete="off" value="'.htmlspecialchars($sv).'" placeholder="Buscar hechizo (Nombre o ID)" />
                        </form>
                        <table class="hidden" style="margin: 0 auto;"><tr><td>';
            makebutton($lang_global['search'], 'javascript:do_submit()', 80);
            if ($sv !== '' || $ft !== 'all' || $fp > 0) {
                $output .= '</td><td>';
                makebutton($lang_global['back'], 'char_spell.php?id='.$id.'&amp;realm='.$realmid, 80);
            }
            $output .= '</td></tr></table></div>';

            $output .= '
                                <br /><br />';

            if ($sqlc->num_rows($result))
            {
                $output .= '
                                <table class="lined" style="width: 550px;">
                                    <tr align="right">
                                        <td colspan="4">';
                $output .= generate_pagination('char_spell.php?id='.$id.'&amp;realm='.$realmid.$su.'&amp;start='.$start.'', $all_record, $itemperpage, $start);
                $output .= '
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>'.$lang_char['icon'].'</th>
                                        <th>'.$lang_char['name'].'</th>
                                        <th>'.$lang_char['icon'].'</th>
                                        <th>'.$lang_char['name'].'</th>
                                    </tr>';

                $sqlm = new SQL;
                $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

                while ($spell = $sqlc->fetch_assoc($result))
                {
                    $output .= '
                                    <tr>
                                        <td><a href="'.$spell_datasite.$spell['spell'].'"><img src="'.spell_get_icon($spell['spell'], $sqlm).'" class="icon_border_0" /></a></td>
                                        <td align="left"><a href="'.$spell_datasite.$spell['spell'].'">'.spell_get_name($spell['spell'], $sqlm).'</a></td>';
                    if($spell = $sqlc->fetch_assoc($result))
                        $output .='
                                        <td><a href="'.$spell_datasite.$spell['spell'].'"><img src="'.spell_get_icon($spell['spell'], $sqlm).'" class="icon_border_0" /></a></td>
                                        <td align="left"><a href="'.$spell_datasite.$spell['spell'].'">'.spell_get_name($spell['spell'], $sqlm).'</a></td>
                                    </tr>';
                    else
                        $output .='
                                        <td></td>
                                        <td></td>
                                    </tr>';
                }

                $output .= '
                                    <tr align="right">
                                        <td colspan="4">';
                $output .= generate_pagination('char_spell.php?id='.$id.'&amp;realm='.$realmid.$su.'&amp;start='.$start.'', $all_record, $itemperpage, $start);
                $output .= '
                                        </td>
                                    </tr>
                                </table>';
            }

            //---------------Page Specific Data Ends here----------------------------
            //---------------Character Tabs Footer-----------------------------------
            $output .= '
                                </div>
                                </div>
                                <br />';

            require_once 'core/char/char_footer.php';

            $output .='
                                <br />
                            </center>
                            <!-- end of char_spell.php -->';
        }
        else
          error($lang_char['no_permission']);
    }
    else
        error($lang_char['no_char_found']);

}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

$output .= '
        <div class="top">
            <h1>'.$lang_char['character'].'</h1>
        </div>';

char_spell($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
