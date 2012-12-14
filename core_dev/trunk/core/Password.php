<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2012 <martin@startwars.org>
 */

// STATUS: ok

namespace cd;

class Password
{
    public static function encrypt($salt1, $salt2, $pwd, $algo)
    {
        switch ($algo) {
        case 'sha1':
        case 'sha224':
        case 'sha256':
        case 'sha384':
        case 'sha512':
            return $algo.':'.hash($algo, $salt1 . hash($algo, $salt2) . hash($algo, $pwd));
        default:
            throw new \Exception ('unknown method: '.$method);
        }
    }

    /** @return bool */
    public static function isForbidden($s)
    {
        $chk_file = dirname(__FILE__).'/Password.forbidden.txt';
        if (!file_exists($chk_file))
            throw new \Exception ('file not found '.$chk_file);

        $data = file_get_contents($chk_file);
        $data = trim($data);

        $arr = explode("\n", $data);

        return in_array(strtolower($s), $arr);
    }

}

?>
