<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

class UUID
{
    /** Returns a v3 UUID (randomized value) */
    public static function v4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Returns a v3 UUID (md5 of $name)
     * @param $ns namespace
     * @param $name name
     */
    public static function v3($ns, $name)
    {
        $hash = md5( self::toBinary($ns).$name );
        return self::build($hash, 0x3000);
    }

    /**
     * Returns a v5 UUID (sha1 of $name)
     * @param $ns namespace
     * @param $name name
     */
    public static function v5($ns, $name)
    {
        $hash = sha1( self::toBinary($ns).$name );
        return self::build($hash, 0x5000);
    }

    private static function build($hash, $version)
    {
        return sprintf('%08s-%04s-%04x-%04x-%12s',
        substr($hash, 0, 8), // 32 bits "time_low"
        substr($hash, 8, 4), // 16 bits "time_mid"
        (hexdec(substr($hash, 12, 4)) & 0x0fff) | $version, // 16 bits "time_hi_and_version", four most significant bits holds version number
        (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000, // 16 bits, 8 bits "clk_seq_hi_res", 8 bits "clk_seq_low", two most significant bits holds zero and one for variant DCE1.1
        substr($hash, 20, 12)                             // 48 bits "node"
        );
    }

    /** Creates 32-letter hex string to 16-byte binary string of input UUID */
    static function toBinary($uuid)
    {
        if (!self::isValid($uuid)) return false;

        $hex = str_replace(array('-','{','}'), '', $uuid);

        $res = '';

        for ($i = 0; $i < strlen($hex); $i+=2)
            $res .= chr(hexdec($hex[$i].$hex[$i+1]));

        return $res;
    }

    public static function isValid($uuid) { return is_valid_uuid($uuid); }
}

function is_valid_uuid($uuid)
{
    return preg_match(
    '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
    '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
    $uuid) === 1;
}

?>
