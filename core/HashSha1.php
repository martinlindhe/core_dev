<?php
/**
 * $Id$
 *
 * https://en.wikipedia.org/wiki/SHA-1
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

namespace cd;

require_once('IHash.php');

class HashSha1 implements IHash
{
    public static function fromFile($file)
    {
        if (!file_exists($file))
            return false;

        if (is_dir($file))
            return false;

        return hash_file('sha1', $file);
    }

    public static function fromString($s)
    {
        return hash('sha1', $s);
    }

}
