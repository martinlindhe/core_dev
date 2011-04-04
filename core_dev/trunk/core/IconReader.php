<?php
/**
 * $Id$
 *
 * Windows Icon reader
 *
 * Extracts images from a .ico file to GD2 resources
 *
 * http://msdn.microsoft.com/en-us/library/ms997538.aspx
 */

//STATUS: wip, works with all tested files

//TODO: support 16x16x1-1.ico (1 bpp)

//XXX: how to handle AndMap for 24 & 32-bpp ?

require_once('core.php');
require_once('bits.php');

class IconReader
{
    /** read ICONENTRY */
    private static function _readIconEntry($fp, $i)
    {
        fseek($fp, 6 + ($i * 16));

        return unpack('CWidth/CHeight/CColorCount/CReserved/vPlanes/vBitCount/VBytesInRes/VImageOffset', fread($fp, 16) );
    }

    static function listLmages($in)
    {
        if (!file_exists($in))
            throw new Exception ('file not found: '.$in);

        $fp = fopen($in, 'rb');

        // read ICONFILE
        $header = unpack('vReserved/vType/vCount', fread($fp, 6));

        $images = array();
        $cnt = $header['Count']; //XXX using temp variable fixes a bug.. in php? 2011-03-02

        for ($i = 0; $i < $cnt; $i++) {

            $entry = self::_readIconEntry($fp, $i);

            fseek($fp, $entry['ImageOffset']);

            // read icon data
            $data = fread($fp, 40);

            if (substr($data, 0, 4) == chr(0x89).'PNG') {
                $images[] = '0x'.dechex($entry['BytesInRes']).' ('.$entry['BytesInRes'].') bytes starting at 0x'.dechex($entry['ImageOffset'])."\t".'--index='.($i+1).' PNG image';
                continue;
            }

            // read BITMAPINFOHEADER
            $header = unpack('VSize/VWidth/VHeight/vPlanes/vBitCount/VCompression/VImageSize/VXpixelsPerM/VYpixelsPerM/VColorsUsed/VColorsImportant', substr($data, 0, 40) );

            $images[] = '0x'.dechex($entry['BytesInRes']).' ('.$entry['BytesInRes'].') bytes starting at 0x'.dechex($entry['ImageOffset'])."\t".'--index='.($i+1).' --size='.$entry['Width'].'x'.$entry['Height'].' --bit-depth='.$header['BitCount'];
        }

        fclose($fp);

        return $images;
    }

    /**
     * @return array of GD2 resources
     */
    static function getImages($in)
    {
        if (!file_exists($in))
            throw new Exception ('file not found');

        $images = array();

        $fp = fopen($in, 'rb');

        // read ICONFILE
        $header = unpack('vReserved/vType/vCount', fread($fp, 6));

        if ($header['Reserved'] != 0)
            throw new Exception ('Reserved is not 0');

        if ($header['Type'] != 1)
            throw new Exception ('Type is not 1');

        for ($i = 0; $i < $header['Count']; $i++)
            $images[] = self::_readIconResource($fp, $i);

        fclose($fp);

        return $images;
    }

    /**
     * Extracts icon resource
     * @return GD2 resource
     */
    private static function _readIconResource($fp, $idx)
    {
        $entry = self::_readIconEntry($fp, $idx);
        fseek($fp, $entry['ImageOffset']);

        if ($entry['Reserved'] != 0)
            throw new Exception ('Reserved (0) is '. $entry['Reserved']);

        if ($entry['Planes'] > 1)
            throw new Exception ('odd planes: '.$entry['Planes']);

        // read icon data
        $data = fread($fp, $entry['BytesInRes']);
//file_put_contents('dump-'.($idx+1).'.raw', $data);

        if (substr($data, 0, 4) == chr(0x89).'PNG') {

            $im = imagecreatefromstring($data);
            imagesavealpha($im, true);
            imagealphablending($im, false);
            return $im;
        }

        // read BITMAPINFOHEADER
        $header = unpack('VSize/VWidth/VHeight/vPlanes/vBitCount/VCompression/VImageSize/VXpixelsPerM/VYpixelsPerM/VColorsUsed/VColorsImportant', substr($data, 0, 40) );

        if ($header['Size'] != 40) {
            print_r($header);
            throw new Exception ('odd header size: '.$header['Size']);
        }

        if ($header['Planes'] > 1)
            throw new Exception ('odd planes: '.$header['Planes']);

        if ($header['Compression'])
            throw new Exception ('compression not supported');

        if ($entry['Height'] > 1024 || $entry['Width'] > 1024)
            throw new Exception ('xxx too big');

        $im = imagecreatetruecolor($entry['Width'], $entry['Height']);
        imagesavealpha($im, true);
        imagealphablending($im, false);

        $pos = 40;
        $palette = array();

        if ($header['BitCount'] < 24) {
            // Read Palette for low bitcounts
            $pal_entries = $entry['ColorCount'];
            if (!$entry['ColorCount'] && $header['BitCount'] == 8)
                $pal_entries = 256;

            for ($i = 0; $i < $pal_entries; $i++) {
                $b = ord($data[$pos++]);
                $g = ord($data[$pos++]);
                $r = ord($data[$pos++]);
                $pos++; // skip empty alpha channel
                $col = imagecolorexactalpha($im, $r, $g, $b, 0);

//                echo '0x'.dechex($entry['ImageOffset'] + 40 + $pos-4).': Color '.$i.' '.dechex($r).','.dechex($g).','.dechex($b)."\n";

                if ($col >= 0)
                    $palette[] = $col;
                else
                    $palette[] = imagecolorallocatealpha($im, $r, $g, $b, 0);
            }

            // XorMap (contains the icon's foreground bitmap) Each value is an index into the Palette color map
            for ($y = 0; $y < $entry['Height']; $y++) {
                $colors = array();
                for ($x = 0; $x < $entry['Width']; $x++) {
                    switch ($header['BitCount']) {
                    case 4: // 8- and 16-color bitmap data is stored as 4 bits per pixel
                        list($hi, $lo) = byte_split( $data[$pos++] );
                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[ $hi ]);
                        imagesetpixel($im, $x+1, $entry['Height'] - $y - 1, $palette[ $lo ]);
                        $x++;
//                        echo '0x'.dechex($pos-1).': XorMap '.$hi.', '.$lo."\n";
                        break;

                    case 8:
                        if ($entry['ColorCount'])
                            throw new Exception ('xxx use available palette for 8bit??');

                        $byte = ord($data[$pos++]);
                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[ $byte ]);
//                        echo '0x'.dechex($pos-1).': XorMap 0x'.dechex($byte)."\n";
                        break;

                    default:
                        throw new Exception ('unhandled bitcount '.$header['BitCount']);
                    }
                }
                // All rows end on the 32 bit
                if ($pos % 4)
                    $pos += 4 - ($pos % 4);
            }

        } else {
            // BitCount >= 24, No Palette
            // marking position because some icons mark all pixels transparent when using an AND map.
            $mark_pos = $pos;
            $use_alpha = false;

            $alphas = array();
            for ($y = 0; $y < $entry['Height']; $y++) {
                for ($x = 0; $x < $entry['Width']; $x++) {
                    $b = ord($data[$pos++]);
                    $g = ord($data[$pos++]);
                    $r = ord($data[$pos++]);
                    if ($header['BitCount'] < 32) {
                        $alpha = 0;
                    } elseif (!$use_alpha) {
                        $alpha = 0;
                        $pos++;
                    } else {
                        $alpha = ord($data[$pos++]);
                        $alphas[$alpha] = $alpha;
                        $alpha = 127-round($alpha/255*127);
                    }

                    $col = imagecolorexactalpha($im, $r, $g, $b, $alpha);
                    if ($col < 0)
                        $col = imagecolorallocatealpha($im, $r, $g, $b, $alpha);

                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $col);
                }

                if ($pos % 4)
                    //$pos += 4 - ($pos % 4);
                    throw new Exception ('eh2: '.dechex($pos) );
            }
            if ($header['BitCount'] == 32 && !empty($alphas) && count($alphas) == 1) {
                echo "USE ALPHA\n";
                $pos = $mark_pos;
                $use_alpha = true;
            }
        }

        // AndMap (background bit mask) 1-bit-per-pixel mask that is the same size (in pixels) as the XorMap
        // Bitcount == 32, No AND (if using alpha) ?????
        if ($header['BitCount'] < 32 && $use_alpha) {

            $palette[-1] = imagecolorallocatealpha($im, 0, 0, 0, 127);
            imagecolortransparent($im, $palette[-1]);
            for ($y = 0; $y < $entry['Height']; $y++) {
                for ($x = 0; $x < $entry['Width']; $x += 8) {

                    $byte = ord($data[$pos++]);
                    $bits = byte_to_bits($byte);

                    echo '0x'.dechex($pos-1).': AndMap 0x'.dechex($byte)."\n";

                    for ($i = 0; $i <= 7; $i++) {
                        $color = array_shift($bits);

                        if ($color)
                            imagesetpixel($im, $x + $i, $entry['Height'] - $y - 1, $palette[-1]);
                    }
                }
                // All rows end on the 32 bit
                if ($pos % 4)
                    $pos += 4 - ($pos % 4);
            }
        }

        if ($header['BitCount'] < 24)
            imagetruecolortopalette($im, true, pow(2, $header['BitCount']));

        return $im;
    }

}


?>
