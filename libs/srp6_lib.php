<?php

    function CalculateSRP6Verifier($username, $password, $salt)
    {
        // algorithm constants
        $g = gmp_init(7);
        $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
        
        // calculate first hash
        $h1 = sha1(strtoupper($username . ':' . $password), TRUE);
        
        // calculate second hash
        $h2 = sha1($salt.$h1, TRUE);
        
        // convert to integer (little-endian)
        $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);
        
        // g^h2 mod N
        $verifier = gmp_powm($g, $h2, $N);
        
        // convert back to a byte array (little-endian)
        $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);
        
        // pad to 32 bytes, remember that zeros go on the end in little-endian!
        $verifier = str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);
        
        // done!
        return $verifier;
    }
    
    function GetSRP6RegistrationData($username, $password)
    {
        // generate a random salt
        $salt = random_bytes(32);
        
        // calculate verifier using this salt
        $verifier = CalculateSRP6Verifier($username, $password, $salt);
        
        // done - this is what you put in the account table!
        return array($salt, $verifier);
    }
    
    function VerifySRP6Login($username, $password, $salt, $verifier)
    {
        // re-calculate the verifier using the provided username + password and the stored salt
        $checkVerifier = CalculateSRP6Verifier($username, $password, $salt);
        
        // compare it against the stored verifier
        return ($verifier === $checkVerifier);
    }
    
?>
