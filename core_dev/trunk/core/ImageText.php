<?php
/**
 * $Id$
 *
 * Helper for writing text on images
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

//STATUS: DO A REWRITE TO A CLASS!

throw new Exception ('broken!');

require_once('Image.php');

class ImageText extends Image
{

    /**
     * Loads a font & sets font type & font height variables
     */
    function loadFont($str, $font, $ttf_size, $ttf_angle, &$ttf, &$fh)
    {
        $ttf = false;

        if (!is_numeric($font))
        {
            if (substr(strtolower($font), -4) == '.ttf' || substr(strtolower($font), -4) == '.otf')
            {
                // supported font formats:
                // .ttf (true type font)
                // .otf (open type font)
                $ttf = true;

                $fh = 0;
                foreach ($str as $txt)
                {
                    // find highest font height
                    $x = imagettfbbox($ttf_size, $ttf_angle, $font, $txt);
                    $t = $x[1] - $x[7];
                    if ($t > $fh) $fh = $t;
                }
            } else {
                // GDF font handling
                $font = imageloadfont($font);
            }
        }

        if (!$ttf)
            $fh = imagefontheight($font);

        return $font;
    }

    /**
     * Draws text centered horizontally & vertically
     *
     * @param $str array of lines of text to print
     * @param $template png image to use as template to draw the text upon
     * @param $font specify the font to use. numeric 1-5 for gd's internal fonts, or specify a .gdf or .ttf font instead
     * @param $col optional color to draw the font in, array(r,g,b). defaults to black
     * @param $ttf_size optional size of ttf font, defaults to 12
     * @return image resource
     */
    function pngCenterText($str, $template, $font = 1, $col = array(), $ttf_size = 12)
    {
        $ttf_angle = 0;

        $im = imagecreatefrompng($template);

        if (empty($col)) {
            $color = imagecolorallocate($im, 0, 0, 0); //defaults to black
        } else {
            $color = imagecolorallocate($im, $col[0], $col[1], $col[2]);
        }

        $font = loadFont($str, $font, $ttf_size, $ttf_angle, $ttf, $fh);

        $i = 0;

        //Prints the text in $str array centered vertically & horizontally over the image
        foreach ($str as $txt) {
            if (!$ttf) {
                $txt = mb_convert_encoding($txt, 'ISO-8859-1', 'auto'); //FIXME required with php 5.2, as imagestring() cant handle utf8
                $fw = strlen($txt) * imagefontwidth($font);
            } else {
                $x = imagettfbbox($ttf_size, $ttf_angle, $font, $txt);
                $fw = $x[2] - $x[0];    //font width
            }

            $px = (imagesx($im) / 2) - ($fw / 2);
            $py = (imagesy($im) / 2) - ( ((count($str)/2) - $i) * $fh);

            if (!$ttf) {
                imagestring($im, $font, $px, $py, $txt, $color);
            } else {
                imagettftext($im, $ttf_size, $ttf_angle, $px, $py + $fh, $color, $font, $txt);
            }

            $i++;
        }
        return $im;
    }

    function pngLeftText($str, $template, $font = 1, $col = array(), $ttf_size = 12, $px = 10, $py = 10)
    {
        $ttf_angle = 0;

        $im = imagecreatefrompng($template);

        if (empty($col)) {
            $color = imagecolorallocate($im, 0, 0, 0); //defaults to black
        } else {
            $color = imagecolorallocate($im, $col[0], $col[1], $col[2]);
        }

        $font = loadFont($str, $font, $ttf_size, $ttf_angle, $ttf, $fh);

        //Prints the text in $str array centered vertically & horizontally over the image
        foreach ($str as $txt) {
            if (!$ttf) {
                $txt = mb_convert_encoding($txt, 'ISO-8859-1', 'auto'); //FIXME required with php 5.2, as imagestring() cant handle utf8
            }

            $tmp_color = array();
            $p1 = strpos($txt, '[');
            $p2 = strpos($txt, ']');
            if ($p1 !== false && $p2 !== false && $p2 > $p1) {
                //extract RGB color code tag & use for current row only, format: [r,g,b]
                $cut = explode(',', substr($txt, $p1 +1, $p2-$p1-1));
                $tmp_color = imagecolorallocate($im, $cut[0], $cut[1], $cut[2]);
                $txt = substr($txt, $p2 +1);
            }

            $py += $fh;

            if (!$ttf) {
                imagestring($im, $font, $px, $py, $txt, empty($tmp_color) ? $color : $tmp_color);
            } else {
                imagettftext($im, $ttf_size, $ttf_angle, $px, $py, empty($tmp_color) ? $color : $tmp_color, $font, $txt);
            }
        }
        return $im;
    }

}

?>
