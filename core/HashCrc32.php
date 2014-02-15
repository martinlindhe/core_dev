<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

namespace cd;

require_once('IHash.php');

class HashCrc32 implements IHash
{
    public static function fromFile($file)
    {
        if (!file_exists($file))
            return false;

        if (is_dir($file))
            return false;

        return hash_file('crc32', $file);
    }

    public static function fromString($s)
    {
        return hash('crc32', $s);
    }

}
