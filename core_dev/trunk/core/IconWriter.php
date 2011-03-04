<?php
/**
 * $Id$
 *
 * Class to generate a Windows Icon (.ico) file from a GD2 resource
 *
 * Useful to create favicons on demand
 *
 * http://msdn.microsoft.com/en-us/library/ms997538.aspx
 * http://www.iconolog.org/info/icoFormat.html
 * http://en.wikipedia.org/wiki/ICO_(file_format)
 * http://en.wikipedia.org/wiki/Favicon
 *
 */

//STATUS: unfinished draft

//TODO later: allow adding multiple images in one .ico file

//TODO later: support Vista icon format (png:s inside .ico container)

require_once('core.php');

require_once('Image.php');

class IconWriter
{
    protected $images = array(); ///< array of Image objects

    function addImage($file)
    {
        $this->images[] = new Image($file);
    }

    private function _packFileHeader()
    {
        return
        pack('vvv',
            0, // WORD Reserved (always 0)
            1, // WORD ResourceType (always 1)
            count($this->images) // WORD IconCount, Number of icon bitmaps in file
        );
    }

    private static function _packIconEntry($im)
    {
        if ($im->width != 32 || $im->height != 32)
            throw new Exception ('unsupported image resolution');

        //XXX TODO: verify that image is 256 color

        $num_colors  = 0; // 0 = 256 or more colors
        $data_size   = 100; //XXXX
        $data_offset = 100; //XXXX

        return
        pack('CCCCCCVV',
            $im->width,  // BYTE Width of icon in pixels
            $im->height, // BYTE Height of icon in pixels
            $num_colors, // BYTE ColorCount Maximum number of colors
            0,           // BYTE Reserved (always 0)
            1,           // BYTE Planes (always 0 or 1)
            0,           // BYTE BitCount (always 0)
            $data_size,  // DWORD BytesInRes Length of icon bitmap in bytes
            $data_offset // DWORD ImageOffset Offset position of icon bitmap in file
        );
    }

    private static function _packIconData($im)
    {
/*
        return
        pack('xx',
            WIN3XBITMAPheadER   header;         // Bitmap header data.  40 byte
            WIN3XPALETTEELEMENT Palette[];      // Color palette
            BYTE                XorMap[];       // Icon bitmap
            BYTE                AndMap[];       // Display bit mask
        );
*/
    }

    function write($out)
    {
        if (!count($this->images))
            throw new Exception ('no images added');

        if (count($this->images) != 1)
            throw new Exception ('XXX multiple images not yet supported');

        $data = $this->_packFileHeader();

        // ICONENTRY  IconDir[]   Directory of icon entries
        foreach ($this->images as $im)
            $data .= self::_packIconEntry($this->images[0]);

        // ICONDATA  IconData[]  Listing of ICO bitmaps
        foreach ($this->images as $im)
            $data .= self::_packIconData($im);

dh($data);
    }

}

?>
