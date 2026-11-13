<?php

    //-------------------Global SQL Injection Prevention Common to all Char pages-------------------
    // we need at least an id or we would have nothing to show
    if (empty($_GET['id']))
        error($lang_global['empty_fields']);

    // this is multi realm support, as of writing still under development
    //  this page is already implementing it
    if (empty($_GET['realm']))
        $realmid = $realm_id;
    else
    {
        $realmid = $sqlr->quote_smart($_GET['realm']);
        if (is_numeric($realmid))
            $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
        else
            $realmid = $realm_id;
    }

    $id = $sqlc->quote_smart($_GET['id']);
    if (is_numeric($id));
    else
        error($lang_global['empty_fields']);
    //-------------------Global SQL Injection Prevention Common to all Char pages-------------------

?>