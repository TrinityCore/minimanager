<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHAR REPUTATION
//########################################################################################################################
function char_rep(&$sqlr, &$sqlc)
{
    global $output, $lang_global, $lang_char,
            $realm_id, $characters_db, $mmfpm_db,
            $action_permission, $user_lvl, $user_name;

    require_once 'libs/fact_lib.php';
    $reputation_rank = fact_get_reputation_rank_arr();
    $reputation_rank_length = fact_get_reputation_rank_length();

    require_once 'core/char/char_security.php';

    $result = $sqlc->query('SELECT account, BINARY name AS name, race, class, level, gender FROM characters WHERE guid = '.$id.' LIMIT 1');

    if ($sqlc->num_rows($result))
    {
        $char = $sqlc->fetch_assoc($result);

        // we get user permissions first
        $owner_acc_id = $char['account'];
        $result = $sqlr->query('SELECT `username`, `SecurityLevel` FROM `account` LEFT JOIN `account_access` ON `account`.`id`=`account_access`.`AccountID` WHERE `account`.`id` = '.$owner_acc_id.' ORDER BY `SecurityLevel` DESC LIMIT 1');
        $owner_name = $sqlr->result($result, 0, 'username');
        $owner_gmlvl = $sqlr->result($result, 0, 'SecurityLevel');
        if (empty($owner_gmlvl))
            $owner_gmlvl = 0;

        if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
        {
            $result = $sqlc->query('SELECT faction, standing FROM character_reputation WHERE guid = '.$id.' AND (flags & 1 = 1)');

            $output .= '
                        <center>
                            <div id="tab_content">
                            <h1>'.$lang_char['reputation'].'</h1>
                            <br />';

            require_once 'core/char/char_header.php';

            $output .= '
                            <br /><br />';

            $temp_out = array
            (
                1 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi1" onclick="expand(\'i1\', this, \'Alliance\')">[-] '.$lang_char['Alliance'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i1" class="lined" style="width: 535px; display: table;">',0),
                2 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi2" onclick="expand(\'i2\', this, \'Horde\')">[-] '.$lang_char['Horde'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i2" class="lined" style="width: 535px; display: table;">',0),
                3 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi3" onclick="expand(\'i3\', this, \'Alliance Forces\')">[-] '.$lang_char['Alliance Forces'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i3" class="lined" style="width: 535px; display: table;">',0),
                4 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi4" onclick="expand(\'i4\', this, \'Horde Forces\')">[-] '.$lang_char['Horde Forces'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i4" class="lined" style="width: 535px; display: table;">',0),
                5 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi5" onclick="expand(\'i5\', this, \'Steamwheedle Cartels\')">[-] '.$lang_char['Steamwheedle Cartel'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i5" class="lined" style="width: 535px; display: table;">',0),
                6 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi6" onclick="expand(\'i6\', this, \'The Burning Crusade\')">[-] '.$lang_char['The Burning Crusade'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i6" class="lined" style="width: 535px; display: table;">',0),
                7 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi7" onclick="expand(\'i7\', this, \'Shattrath City\')">[-] '.$lang_char['Shattrath City'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i7" class="lined" style="width: 535px; display: table;">',0),
                8 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi8" onclick="expand(\'i8\', this, \'Alliance Vanguard\')">[-] '.$lang_char['Alliance Vanguard'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i8" class="lined" style="width: 535px; display: table;">',0),
                9 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi9" onclick="expand(\'i9\', this, \'Horde Expedition \')">[-] '.$lang_char['Horde Expedition'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i9" class="lined" style="width: 535px; display: table;">',0),
               10 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi10" onclick="expand(\'i10\', this, \'Sholazar Basin\')">[-] '.$lang_char['Sholazar Basin'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i10" class="lined" style="width: 535px; display: table;">',0),
               11 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi11" onclick="expand(\'i11\', this, \'Wrath of the Lich King\')">[-] '.$lang_char['Wrath of the Lich King'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i11" class="lined" style="width: 535px; display: table;">',0),
               12 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi12" onclick="expand(\'i12\', this, \'Other\')">[-] '.$lang_char['Other'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i12" class="lined" style="width: 535px; display: table;">',0),
                0 => array('
                            <table class="lined" style="width: 550px;">
                                <tr>
                                    <th colspan="3" align="left">
                                        <div id="divi13" onclick="expand(\'i13\', this, \'Unknown\')">[-] '.$lang_char['Unknown'].'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="i13" class="lined" style="width: 535px; display: table;">',0),
            );

            $sqlm = new SQL;
            $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

            if ($sqlc->num_rows($result))
            {
                while ($fact = $sqlc->fetch_assoc($result))
                {
                    $faction  = $fact['faction'];
                    $standing = $fact['standing'];

                    $rep_rank      = fact_get_reputation_rank($faction, $standing, $char['race'], $sqlm);
                    $rep_rank_name = $reputation_rank[$rep_rank];
                    $rep_cap       = $reputation_rank_length[$rep_rank];
                    $rep           = fact_get_reputation_at_rank($faction, $standing, $char['race'], $sqlm);
                    $faction_name  = fact_get_faction_name($faction, $sqlm);
                    $ft            = fact_get_faction_tree($faction);

                    // not show alliance rep for horde and vice versa:
                    if ((((1 << ($char['race'] - 1)) & 690) && ($ft == 1 || $ft == 3))
                        || ( ((1 << ($char['race'] - 1)) & 1101) && ($ft == 2 || $ft == 4)));
                    else
                    {
                        $temp_out[$ft][0] .= '
                                            <tr>
                                                <td width="30%" align="left">'.$faction_name.'</td>
                                                <td width="55%" valign="top">
                                                    <div class="faction-bar">
                                                        <div class="rep'.$rep_rank.'">
                                                            <span class="rep-data">'.$rep.'/'.$rep_cap.'</span>
                                                            <div class="bar-color" style="width:'.(100*$rep/$rep_cap).'%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td width="15%" align="left" class="rep'.$rep_rank.'">'.$rep_rank_name.'</td>
                                            </tr>';
                        $temp_out[$ft][1] = 1;
                    }
                }
            }
            else
                $output .= '
                                            <tr>
                                                <td colspan="2"><br /><br />'.$lang_global['err_no_records_found'].'<br /><br /></td>
                                            </tr>';

            foreach ($temp_out as $out)
                if ($out[1])
                    $output .= $out[0].'
                                        </table>
                                    </td>
                                </tr>
                            </table>';
            $output .= '
                            <br />
                        </div>
                        <br />
                        </div>
                        <br />';

            require_once 'core/char/char_footer.php';

            $output .='
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


//########################################################################################################################
// MAIN
//########################################################################################################################

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
char_rep($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
