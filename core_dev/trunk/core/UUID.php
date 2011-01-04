<?php
/**
 * $Id$
 *
 * http://en.wikipedia.org/wiki/UUID
 *
 * 128-bit (16 byte) number
 *
 * The most widespread use of this standard is in Microsoft's Globally Unique Identifiers (GUIDs)
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: ok

//FIXME: replace strrev2 with some built-in PHP function

class UUID
{
    public static function isValid($uuid)
    {
        return preg_match(
        '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
        '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
        $uuid) === 1;
    }

    /**
     * @param $ns namespace
     * @param $name name
     * @return a v3 UUID (md5 of $name)
     */
    public static function v3($ns, $name)
    {
        $hash = md5( self::toBinary($ns).$name );
        return self::build($hash, 0x3000);
    }

    /** @return a v4 UUID (randomized value) */
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
     * @param $ns namespace
     * @param $name name
     * @return a v5 UUID (sha1 of $name)
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

    /**
     * Converts a UUID-formatted string to a hex value
     * @param $guid UUID as a string "3F2504E0-4F89-11D3-9A0C-0305E82C3301"
     * @return UUID as a string "E004253F894FD3119A0C0305E82C3301" (RAW 16)
     */
    static function toHex($uuid)
    {
        if (!self::isValid($uuid)) return false;

        $uuid = str_replace(array('{','}'), '', $uuid);

        if (strlen($uuid) != 36) return false;

        $parts = explode('-', $uuid);
        if (count($parts) != 5) return false;

        if (strlen($parts[0]) != 8) return false;    //Data1
        if (strlen($parts[1]) != 4) return false;    //Data2
        if (strlen($parts[2]) != 4) return false;    //Data3
        if (strlen($parts[3]) != 4) return false;    //Data4
        if (strlen($parts[4]) != 12) return false;   //Data4

        //Data4 stores the bytes in the same order as displayed in the GUID text encoding,
        //but other three fields are reversed on little-endian systems (e.g. Intel CPUs).
        return self::strrev2($parts[0]).self::strrev2($parts[1]).self::strrev2($parts[2]).$parts[3].$parts[4];
    }

    /**
     * Like built in strrev() but on character pairs
     * @param $str input string, must have even length
     */
    private static function strrev2($str)
    {
        if (strlen($str) % 2) return false;

        $ret = '';
        for ($i = strlen($str); $i >= 0; $i -= 2) {
            $ret .= substr($str, $i, 2);
        }
        return $ret;
    }

}

?>
