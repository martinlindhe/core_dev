<?php
/**
 * $Id$
 *
 * Hash calculation code based on snippet from
 * http://trac.opensubtitles.org/projects/opensubtitles/wiki/HashSourceCodes
 *
 * Calculates: size + 64bit chksum of the first and last 64k
 * (even if they overlap because the file is smaller than 128k)
 *
 * The algorithm is based on video hash algoritm from Media Player Classic
 * (Licenced under GPL)
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: ok

require_once('Cache.php');

class VideoHash
{
    /**
     * @return 16-character string
     */
    static function Calc($file)
    {
        if (!file_exists($file))
            return false;

        $hash_cache = new Cache();
        $hash_cache->setCacheTime(60*60*24*7); //7 days
        $hash = $hash_cache->get('videohash/'.$file);
        if ($hash) {
            //echo "CACHE: REUSING VIDEO HASH ".$hash."\n";
            return $hash;
        }

        $handle = fopen($file, 'rb');
        $fsize = filesize($file);

        $hash = array(
        3 => 0,
        2 => 0,
        1 => ($fsize >> 16) & 0xFFFF,
        0 => $fsize & 0xFFFF);

        for ($i = 0; $i < 8192; $i++)
            $hash = self::AddUINT64($hash, $handle);

        $offset = $fsize - 65536;
        fseek($handle, $offset > 0 ? $offset : 0, SEEK_SET);

        for ($i = 0; $i < 8192; $i++)
            $hash = self::AddUINT64($hash, $handle);

        fclose($handle);

        $res = sprintf("%04x%04x%04x%04x", $hash[3], $hash[2], $hash[1], $hash[0]);

        //echo "CALCULATED HASH ".$res." for ".$file."\n";
        $hash_cache->set('videohash/'.$file, $res);
        return $res;
    }

    private static function AddUINT64($a, $handle)
    {
        $u = unpack("va/vb/vc/vd", fread($handle, 8));
        $b = array(0 => $u["a"], 1 => $u["b"], 2 => $u["c"], 3 => $u["d"]);

        $o = array(0 => 0, 1 => 0, 2 => 0, 3 => 0);

        $carry = 0;
        for ($i = 0; $i < 4; $i++)
        {
            if (($a[$i] + $b[$i] + $carry) > 0xffff)
            {
                $o[$i] += ($a[$i] + $b[$i] + $carry) & 0xffff;
                $carry = 1;
            }
            else
            {
                $o[$i] += ($a[$i] + $b[$i] + $carry);
                $carry = 0;
            }
        }

        return $o;
    }

}

?>
