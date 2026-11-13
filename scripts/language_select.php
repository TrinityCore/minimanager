<?php


function generate_language_selectbox()
{
    global $lang_global, $locales_search_option;

    include_once("get_lib.php");
    $selectedlanguage = get_lang_id();

    // generate a html option list with available locales_entries
    // taken from $locales_search_option in your scripts/config.php

    // if user language is supported select this one, else default language
    $select_option = ( $locales_search_option & pow(2,$selectedlanguage) == 0  ) ? 0 : $selectedlanguage;

    $searchbox = "
                <select name=\"language\">
                  <option value=\"0\">{$lang_global['language_0']}</option>";
    for ($i=1; $i<9;$i++)
    {
        if (($locales_search_option & pow(2,$i-1)) != 0)
        {
            $searchbox .= "
                  <option value=\"{$i}\"";
            if ($select_option == $i)
                $searchbox .= "selected=\"selected\"";
            $searchbox .= ">{$lang_global['language_'.$i]}</option>";
        }
    }
    $searchbox .= "
                  </select>";
    return $searchbox;
}


?>
