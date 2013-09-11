<?php
/**
 * $Id$
 *
 * https://en.wikipedia.org/wiki/MD5
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IHash.php');

class HashMd5 implements IHash
{
    public static function fromFile($file)
    {
        if (!file_exists($file))
            return false;

        if (is_dir($file))
            return false;

        return hash_file('md5', $file);
    }

    public static function fromString($s)
    {
        return hash('md5', $s);
    }

}
