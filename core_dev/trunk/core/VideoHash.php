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
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: ok

require_once('IHash.php');
require_once('TempStore.php');

class VideoHash implements IHash
{
    /** @return 16-character string */
    public static function CalcFile($file)
    {
        if (!file_exists($file))
            return false;

        $temp = TempStore::getInstance();
        $key = 'videohash/'.$file;

        $hash = $temp->get($key);
        if ($hash)
            return $hash;

        $handle = fopen($file, 'rb');
        $fsize = filesize($file);
        if ($fsize < 65536) //XXXX what is minimum possible size?
            return false;

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
        $temp->set($key, $res);
        return $res;
    }

    public static function CalcString($s)
    {
        // XXX need reworking the code to implement, and not very useful
        throw new Exception ('VideoHash::CalcString not supported');
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
