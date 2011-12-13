<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

require_once('IHash.php');

class HashMd5 implements IHash
{
    /** @return 32-character string */
    public static function CalcFile($file)
    {
        if (!file_exists($file))
            return false;

        if (is_dir($file))
            return false;

        return md5_file($file);
    }

    /** @return 32-character string */
    public static function CalcString($s)
    {
        return md5($s);
    }

}

?>
