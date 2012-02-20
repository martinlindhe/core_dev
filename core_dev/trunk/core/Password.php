<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

class Password
{
    public static function encrypt($salt1, $pwd, $algo)
    {
        $session = SessionHandler::getInstance();
        $salt2 = $session->getEncryptKey();

        switch ($algo) {
        case 'sha1':
        case 'sha224':
        case 'sha256':
        case 'sha384':
        case 'sha512':
            return $algo.':'.hash($algo, $salt1 . hash($algo, $salt2) . hash($algo, $pwd));
        default:
            throw new Exception ('unknown method: '.$method);
        }
    }

}

?>
