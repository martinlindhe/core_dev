<?php
/**
 * $Id$
 *
 * Windows Icon reader
 *
 * Extracts images from a .ico file to GD2 resources
 */

//STATUS: early WIP, only a few files supported

//TODO: handle different bit depths
//TODO later: read vista icons (contains PNG:s)


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
//d($entry);
        fseek($fp, $entry['DataOffset']);

        // read WIN3XBITMAPHEADER
        $header = unpack('VSize/VWidth/VHeight/vPlanes/vBitCount/VCompression/VImageSize/VXpixelsPerM/VYpixelsPerM/VColorsUsed/VColorsImportant', fread($fp, 40) );
//d($header);
        if ($header['Size'] != 40) {
            print_r($header);
            throw new Exception ('odd header size: '.$header['Size']);
        }

        if ($header['Compression'])
            throw new Exception ('compression not supported');

        if ($entry['Height'] > 1024 || $entry['Width'] > 1024)
            throw new Exception ('xxx too big');

        echo $entry['DataSize'].' bytes starting at 0x'.dechex($entry['DataOffset']).' --index='.($idx+1).' --width='.$entry['Width'].' --height='.$entry['Height'].' --bit-depth='.$header['BitCount'].' --palette-size='.$entry['NumColors']."\n";

        $colors = $header['ColorsUsed'] ? $header['ColorsUsed'] : $entry['NumColors'];
        if (!$colors && $entry['BitsPerPixel'] == 8) $colors = 256;
        echo 'colors: '.$colors."\n";

        $im = imagecreatetruecolor($entry['Width'], $entry['Height']);
        imagesavealpha($im, true);
        imagealphablending($im, false);

        // read icon data
        $data = fread($fp, $entry['DataSize'] - 40);

        $pos = 0;
        $palette = array();

        if ($entry['BitsPerPixel'] < 24) {
            // Read Palette for low bitcounts
            for ($i = 0; $i < $entry['NumColors']; $i++) {
                $b = ord($data[$pos++]);
                $g = ord($data[$pos++]);
                $r = ord($data[$pos++]);
                $pos++; // skip empty alpha channel
                $col = imagecolorexactalpha($im, $r, $g, $b, 0);

                echo '0x'.dechex($entry['DataOffset'] + 40 + $pos-4).': Color '.$i.' '.dechex($r).','.dechex($g).','.dechex($b)."\n";

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

                        list($hi, $lo) = byte_split( ord($data[$pos++]) );
                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[ $hi ]) or die('cant set pixel');
                        imagesetpixel($im, $x+1, $entry['Height'] - $y - 1, $palette[ $lo ]) or die('cant set pixel');

                        echo '0x'.dechex($entry['DataOffset'] + 40 + $pos-1).': XorMap '.$hi.', '.$lo."\n";

                        $x++;
                    } else if ($header['BitCount'] == 8) {
                        $col = ord($data[$pos++]);
                        $pal = $col; //XXX translate 8 bit value to RGB
//                        echo '0x'.dechex($entry['DataOffset'] + 40 + $pos-1).': XorMap 0x'.dechex($col)."\n";
                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $pal) or die('cant set pixel');
//die;
                    } else {
                        throw new Exception ('unhandled bitcount '.$header['BitCount']);

                        /*
                        if ($header['BitCount'] < 8) {
                            $col = array_shift($colors);
                            if (!is_null($col))
                                continue;

                            $byte = ord($data[$pos++]);
                            $tmp_color = 0;
                            for ($i = 7; $i >= 0; $i--) {
                                $bit_value = pow(2, $i);
                                $bit = floor($byte / $bit_value);
                                $byte = $byte - ($bit * $bit_value);
                                $tmp_color += $bit * pow(2, $i % $header['BitCount']);
                                if ($i % $header['BitCount'] == 0) {
                                    array_push($colors, $tmp_color);
                                    $tmp_color = 0;
                                }
                            }
                            $col = array_shift($colors);

                        } else
                            $col = ord($data[$pos++]);

                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[$col]) or die('cant set pixel');
                        */
                    }
                }
                // All rows end on the 32 bit
                if ($pos % 4) $pos += 4 - ($pos % 4);
            }

        } else {
//            throw new Exception ('true color not tested');
            // BitCount >= 24, No Palette
            // marking position because some icons mark all pixels transparent when using an AND map.
            $markPosition = $pos;
            $retry = true;
            $ignoreAlpha = false;
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
                        } elseif($ignoreAlpha) {
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

                        imagesetpixel($im, $x, $entry['Height'] - $y - 1, $col) or die('cant set pixel');
                    }
                    if ($pos % 4) $pos += 4 - ($pos % 4);
                }
                if ($header['BitCount'] == 32 && isset($alphas[0]) && count($alphas) == 1) {
                    $retry = true;
                    $pos = $markPosition;
                    $ignoreAlpha = true;
                }
            }
        }

        // AndMap (background bit mask) 1-bit-per-pixel mask that is the same size (in pixels) as the XorMap
        if ($header['BitCount'] < 32 || $ignoreAlpha) {

            if ($header['BitCount'] == 4 || $header['BitCount'] == 8) {

                $palette[-1] = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagecolortransparent($im, $palette[-1]);
                for ($y = 0; $y < $entry['Height']; $y++) {

                    for ($x = 0; $x < $entry['Width']; $x += 8) {

                        $byte = ord($data[$pos++]);
                        $bits = byte_to_bits($byte);

//                        echo '0x'.dechex($entry['DataOffset'] + 40 + $pos-1).': AndMap 0x'.dechex($byte)."\n";

                        for ($i = 0; $i <= 7; $i++) {
                            $color = array_shift($bits);

                            if ($color)
                                imagesetpixel($im, $x + $i, $entry['Height'] - $y - 1, $palette[-1]) or die('cant set pixel');
                        }
                    }
                    // All rows end on the 32 bit.
                    if ($pos % 4) $pos +=  4 - ($pos % 4);
                }
            } else {
                throw new Exception ('unhandled bit count: '.$header['BitCount']);
/*
                // Bitcount == 32, No AND (if using alpha)
                $palette[-1] = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagecolortransparent($im, $palette[-1]);
                for ($y = 0; $y < $entry['Height']; $y++) {
                    $colors = array();
                    for ($x = 0; $x < $entry['Width']; $x++) {
                        $color = array_shift($colors);
                        if (!is_null($color))
                            continue;

                        $byte = ord($data[$pos++]);
                        $tmp_color = 0;
                        for ($i = 7; $i >= 0; $i--) {
                            $bit_value = pow(2, $i);
                            $bit = floor($byte / $bit_value);
                            $byte = $byte - ($bit * $bit_value);
                            array_push($colors, $bit);
                        }
                        $color = array_shift($colors);

                        if ($color)
                            imagesetpixel($im, $x, $entry['Height'] - $y - 1, $palette[-1]) or die('cant set pixel');
                    }
                    // All rows end on the 32 bit.
                    if ($pos % 4) $pos +=  4 - ($pos % 4);
                }
*/
            }
        }

        if ($header['BitCount'] < 24)
            imagetruecolortopalette($im, true, pow(2, $header['BitCount']));

        return $im;
    }

}


?>
