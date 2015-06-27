<?php
/**
 * Encryption functions
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

/**
 * Check the key size.
 *
 * @return boolean $correct
 */
function correctKeySize()
{
    include 'config.php';
    switch (strlen($key)) {
        case 16:
        case 24:
        case 32:
            return true;
            break;
        default:
            return false;
            break;
    }
}

/**
 * Encrypt the credential.
 *
 * @param string $cred the credential to be encrypted
 *
 * @return string $encrypted the encrypted string
 */
function encryptCred($cred) 
{
    include 'config.php';
    $algorithm = MCRYPT_RIJNDAEL_128;
    $mode = MCRYPT_MODE_ECB;
    $rand = MCRYPT_DEV_URANDOM;
    $ivSize = mcrypt_get_iv_size($algorithm, $mode);
    $iv = mcrypt_create_iv($ivSize, $rand);

    return base64_encode(mcrypt_encrypt($algorithm, $key, $cred, $mode, $iv));
}

/**
 * Decrypt the credential from the database.
 *
 * @param string $encrypted the encrypted string
 *
 * @return string $decrypted the decrypted string
 */
function decryptCred($encrypted) 
{
    include 'config.php';
    $algorithm = MCRYPT_RIJNDAEL_128;
    $mode = MCRYPT_MODE_ECB;
    $rand = MCRYPT_DEV_URANDOM;
    $ivSize = mcrypt_get_iv_size($algorithm, $mode);
    $iv = mcrypt_create_iv($ivSize, $rand);

    return mcrypt_decrypt($algorithm, $key, base64_decode($encrypted), $mode, $iv);
}


/**
 * Generates a UUID v4 
 * From Andrew Moore's example: http://www.php.net/manual/en/function.uniqid.php#94959
 *
 * @return string $uniqueId
 */
function getUniqueId() 
{
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

/**
 * Hashes the id via CRYPT_SHA512
 *
 * @param string $id , $salt
 *
 * @param $salt
 * @return string $hashedId
 */
function hashId($id, $salt) 
{
    $hashedId = crypt($id, '$6$rounds=5000$' . $salt . '$');
    return $hashedId;
}

/**
 * Generates a 128-bit salt
 *
 * Unused? 
 *
 * @return string $salt
 */
function getSalt()
{
    $salt = substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22);
    return $salt;
}