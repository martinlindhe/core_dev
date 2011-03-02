<?php
/**
 * $Id$
 *
 * Windows Icon reader
 *
 * Extracts images from a .ico file to GD2 resources
 *
 * http://www.daubnet.com/en/file-format-ico
 */

//STATUS: early WIP, only a few files supported

//XXX: need a 16 bpp sample
//XXX: do all 32-bpp pics have an alpha map????

require_once('core.php');
require_once('bits.php');

class IconReader
{
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

        $header = unpack('vReserved/vResourceType/vIconCount', fread($fp, 6));

        if ($header['Reserved'] != 0)
            throw new Exception ('Reserved is not 0');

        if ($header['ResourceType'] != 1)
            throw new Exception ('ResourceType is not 1');
/*
        if ($header['IconCount'] != 1)
            throw new Exception ('can only handle icons with 1 image, found '.$header['IconCount']);
*/
        for ($i = 0; $i < $header['IconCount']; $i++)
            $images[] = self::readIconResource($fp, $i);

        fclose($fp);

        return $images;
    }

    /**
     * Extracts icon resource into a GD resource
     * @param $entry ICONENTRY
     * @param $data raw ICON data (header, palette, XOR map, AND map)
     */
    private function readIconResource($fp, $idx)
    {
        fseek($fp, 6 + ($idx * 16));

        // read ICONENTRY
        $entry = unpack('CWidth/CHeight/CNumColors/CReserved/vNumPlanes/vBitsPerPixel/VDataSize/VDataOffset', fread($fp, 16) );
//d($entry);die;
        fseek($fp, $entry['DataOffset']);

        if ($entry['Reserved'] != 0)
            throw new Exception ('Reserved is not 0');

        if ($entry['NumPlanes'] > 1)
            throw new Exception ('odd numplanes: '.$entry['NumPlanes']);

        // read icon data
        $data = fread($fp, $entry['DataSize']);
//file_put_contents('dump-'.$idx.'.raw', $data);

        if (substr($data, 0, 4) == chr(0x89).'PNG') {

            $im = imagecreatefromstring($data);
            imagesavealpha($im, true);
            imagealphablending($im, false);

            echo '0x'.dechex($entry['DataSize']).' ('.$entry['DataSize'].') bytes starting at 0x'.dechex($entry['DataOffset'])."\t".'--index='.($idx+1).' --size='.imagesx($im).'x'.imagesy($im).' PNG image'."\n";

            return $im;
        }

        // read WIN3XBITMAPHEADER
        $header = unpack('VSize/VWidth/VHeight/vPlanes/vBitCount/VCompression/VImageSize/VXpixelsPerM/VYpixelsPerM/VColorsUsed/VColorsImportant', substr($data, 0, 40) );
//d($header);

        $colors = $header['ColorsUsed'] ? $header['ColorsUsed'] : $entry['NumColors'];
        if (!$colors && $header['BitCount'] == 8) $colors = 256;
        if (!$colors && $header['BitCount'] == 16) $colors = 65536;
        if (!$colors && $header['BitCount'] == 24) $colors = 16777216;
        if (!$colors && $header['BitCount'] == 32) $colors = 4294967296;

        echo '0x'.dechex($entry['DataSize']).' ('.$entry['DataSize'].') bytes starting at 0x'.dechex($entry['DataOffset'])."\t".'--index='.($idx+1).' --size='.$entry['Width'].'x'.$entry['Height'].' --bit-depth='.$header['BitCount'].' --colors='.$colors."\n";

        if ($header['Size'] != 40) {
            print_r($header);
            throw new Exception ('odd header size: '.$header['Size']);
        }

        if ($header['Planes'] != 1)
            throw new Exception ('odd planes: '.$header['Planes']);

        if ($header['Compression'])
            throw new Exception ('compression not supported');

        if ($entry['Height'] > 1024 || $entry['Width'] > 1024)
            throw new Exception ('xxx too big');

        $im = imagecreatetruecolor($entry['Width'], $entry['Height']);
        imagesavealpha($im, true);
        imagealphablending($im, false);

        $pos = 0x28; // 40 byte header
        $palette = array();

        $no_alpha = false;

        if ($header['BitCount'] < 24) {
            // Read Palette for low bitcounts
            $pal_entries = $entry['NumColors'];
            if (!$entry['NumColors'] && $header['BitCount'] == 8)
                $pal_entries = 256;

            for ($i = 0; $i < $pal_entries; $i++) {
                $b = ord($data[$pos++]);
                $g = ord($data[$pos++]);
                $r = ord($data[$pos++]);
                $pos++; // skip empty alpha channel
                $col = imagecolorexactalpha($im, $r, $g, $b, 0);

//                echo '0x'.dechex($entry['DataOffset'] + 40 + $pos-4).': Color '.$i.' '.dechex($r).','.dechex($g).','.dechex($b)."\n";

                if ($col >= 0)
                    $palette[] = $col;
                else
                    $palette[] = imagecolorallocatealpha($im, $r, $g, $b, 0);
            }

            // XorMap (contains the icon's foreground bitmap) Each value is an index into the Palette color map
            for ($y = 0; $y < $entry['Height']; $y++) {
                $colors = array();
                for ($x = 0; $x < $entry['Width']; $x++) {
                    if ($header['BitCount'] == 4) { // 8- and 16-color bitmap data is stored as 4 bits per pixel

                        list($hi, $lo) = byte_split( $data[$pos++] );
                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[ $hi ]);
                        imagesetpixel($im, $x+1, $entry['Height'] - $y - 1, $palette[ $lo ]);
                        $x++;
//                        echo '0x'.dechex($pos-1).': XorMap '.$hi.', '.$lo."\n";

                    } else if ($header['BitCount'] == 8) {
                        if ($entry['NumColors'])
                            throw new Exception ('xxx use available palette for 8bit??');

                        $byte = ord($data[$pos++]);
                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[ $byte ]);

//                        echo '0x'.dechex($pos-1).': XorMap 0x'.dechex($byte)."\n";
                    } else
                        throw new Exception ('unhandled bitcount '.$header['BitCount']);
                }
                // All rows end on the 32 bit
                if ($pos % 4)
                    throw new Exception ('eh1: '.dechex($pos) );
                    //$pos += 4 - ($pos % 4);
            }

        } else {
            // BitCount >= 24, No Palette
            // marking position because some icons mark all pixels transparent when using an AND map.
            $mark_pos = $pos;
            $retry = true;
//XXXX: clean up code
            while ($retry) {
                $alphas = array();
                $retry = false;
                for ($y = 0; $y < $entry['Height'] and !$retry; $y++) {
                    for ($x = 0; $x < $entry['Width'] and !$retry; $x++) {
                        $b = ord($data[$pos++]);
                        $g = ord($data[$pos++]);
                        $r = ord($data[$pos++]);
                        if ($header['BitCount'] < 32) {
                            $alpha = 0;
                        } elseif($no_alpha) {
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
                        throw new Exception ('eh2: '.dechex($pos) );
                        //$pos += 4 - ($pos % 4);
                }
                if ($header['BitCount'] == 32 && isset($alphas[0]) && count($alphas) == 1) {
                    $retry = true;
                    $pos = $mark_pos;
                    $no_alpha = true;
                }
            }
        }

        // AndMap (background bit mask) 1-bit-per-pixel mask that is the same size (in pixels) as the XorMap
        // Bitcount == 32, No AND (if using alpha) ?????
//        if ($header['BitCount'] < 32 || $no_alpha) {

            if ($no_alpha)
                throw new Exception ('XXXX: skip AndMap? ofs at 0x'.dechex($pos)."\n");

            if ($header['BitCount'] == 4 || $header['BitCount'] == 8 || $header['BitCount'] == 24 || $header['BitCount'] == 32) {

                $palette[-1] = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagecolortransparent($im, $palette[-1]);
                for ($y = 0; $y < $entry['Height']; $y++) {
                    for ($x = 0; $x < $entry['Width']; $x += 8) {

                        $byte = ord($data[$pos++]);
                        $bits = byte_to_bits($byte);

//                        echo '0x'.dechex($pos-1).': AndMap 0x'.dechex($byte)."\n";

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
            } else
                throw new Exception ('unhandled bit count: '.$header['BitCount']);

//        }

        if ($header['BitCount'] < 24)
            imagetruecolortopalette($im, true, pow(2, $header['BitCount']));

        return $im;
    }

}


?>
