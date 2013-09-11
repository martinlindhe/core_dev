<?php
/**
 * $Id$
 *
 * Helper for writing text on images
 *
 * Font format support depends on the freetype configuration,
 * but can support:
 * .ttf (TrueType font)
 * .otf (OpenType font)
 *
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

//STATUS: wip

//WISHLIST: WOFF support in freetype? should already be there... ? http://www.google.com/webfonts

namespace cd;

require_once('Image.php');

class ImageText extends Image
{
    /**
     * @param $str text
     * @param $font ttf font
     * @param $color array(r,g,b)
     * @param $size font size
     * @param $px starting X coordinate
     * @param $py starting Y coordinate
     */
    function writeText($str, $font, $col = array(), $size = 12, $px = 10, $py = 10)
    {
        if (empty($col))
            $col = array(0,0,0); // black

        $color = imagecolorallocate($this->resource, $col[0], $col[1], $col[2]);

        imagettftext($this->resource, $size, 0, $px, $py, $color, $font, $str);
    }

}

?>
