<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

require_once('IHash.php');

class HashSha1 implements IHash
{
    /** @return 40-character string */
    public static function CalcFile($file)
    {
        if (!file_exists($file))
            return false;

        if (is_dir($file))
            return false;

        return sha1_file($file);
    }

    /** @return 40-character string */
    public static function CalcString($s)
    {
        return sha1($s);
    }

}

?>
