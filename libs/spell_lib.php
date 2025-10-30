<?php


//#############################################################################
//get spell name by its id

function spell_get_name($id, &$sqlm)
{
    $spell_name = $sqlm->fetch_assoc($sqlm->query('SELECT field_136 FROM dbc_spell WHERE id='.$id.' LIMIT 1'));
    return $spell_name['field_136'];
}


//#############################################################################
//get spell icon - if icon not exists in item_icons folder D/L it from web.

function spell_get_icon($auraid, &$sqlm)
{
    global $get_icons_from_web, $get_icons_web_cdn, $item_icons;

    if ($auraid < 1)
        $auraid = 0;

    $result = $sqlm->query('SELECT field_133 FROM dbc_spell WHERE id = '.$auraid.' LIMIT 1');
    $displayid = ($result) ? $sqlm->result($result, 0) : 0;

    if ($displayid)
    {
        $result = $sqlm->query('SELECT field_1 FROM dbc_spellicon WHERE id = '.$displayid.' LIMIT 1');
        if ($result)
        {
            $aura_raw = $sqlm->result($result, 0);

            // --- Sanitize the icon name ---
            $aura = strtolower($aura_raw);
            $aura = str_replace('\\', '/', $aura); // convert backslashes
            $aura = preg_replace('#^interface/icons/#', '', $aura); // remove "interface/icons/"
            // --------------------------------

            if ($get_icons_from_web)
            {
                // Directly use web CDN
                return $get_icons_web_cdn . $aura . '.jpg';
            }

            // Local fallback
            $local_path = $item_icons . '/' . $aura . '.jpg';
            if (file_exists($local_path))
            {
                if (filesize($local_path) > 349)
                    return $local_path;
                else
                    unlink($local_path);
            }

            // If not found locally, fetch from web CDN
            $web_url = $get_icons_web_cdn . $aura . '.jpg';
            if (!$get_icons_from_web)
            {
                if (!file_exists($item_icons))
                    mkdir($item_icons, 0755, true);

                $data = @file_get_contents($web_url);
                if ($data)
                {
                    file_put_contents($local_path, $data);
                    return $local_path;
                }
                else
                {
                    return $web_url;
                }
            }
        }
    }

    return 'img/INV/INV_blank_32.gif'; // fallback
}


?>
