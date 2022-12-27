<?php

require_once __DIR__ . '/IP2Location/IP2Location.php';

//#############################################################################
//get country code and country name by IP
// given IP, returns array('code','country')
// 'code' is country code, 'country' is country name.

function misc_get_country_by_ip($ip, &$sqlm)
{
    $db = new \IP2Location\Database(__DIR__ . '/IP2Location/databases/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);
    $records = $db->lookup($ip, \IP2Location\Database::ALL);
    $country = ["code" => strtolower($records['countryCode']), "country" => $records['countryName']];

    return $country;
}


//#############################################################################
//get country code and country name by IP
// given account ID, returns array('code','country')
// 'code' is country code, 'country' is country name.

function misc_get_country_by_account($account, &$sqlr, &$sqlm)
{
    $ip = $sqlr->fetch_assoc($sqlr->query('SELECT last_ip FROM account WHERE id='.$account.';'));

    return misc_get_country_by_ip($ip['last_ip'], $sqlm);
}


?>
