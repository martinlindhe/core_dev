<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

namespace cd;

require_once('IHash.php');

class HashSha256 implements IHash
{
    var $characters = 40; ///< number of hex character needed to represent the value

    public static function fromFile($file)
    {
        if (!file_exists($file))
            return false;

        if (is_dir($file))
            return false;

        return hash_file('sha256', $file);
    }

    public static function fromString($s)
    {
        return hash('sha256', $s);
    }

}
