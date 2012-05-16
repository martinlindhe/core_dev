<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

//STATUS: wip

require_once('Image.php');

class ImageCropper extends Image
{
    protected $tmp_dir  = '/tmp/core_dev-images';

    /**
     * Crops selected image to the requested dimensions
     * @param $x coordinate x
     * @param $y coordinate y
     * @param $w width
     * @param $h height
     */
    function crop($x, $y, $w, $h)
    {
        if (!is_numeric($x) || !is_numeric($y) || !is_numeric($x) || !is_numeric($y))
            throw new Exception ('bad input');

        echo 'Cropping '.$this->width.'x'.$this->height.' to '.$x.','.$y.' '.$w.'x'.$h."\n";

        $key = 'cropped-'.$this->sha1.'-'.$x.'-'.$y.'-'.$w.'-'.$h;
        $tmp_file = $this->tmp_dir.'/'.$key;
        if (file_exists($tmp_file)) {
            $this->load($tmp_file);
            return;
        }

        $org = $this->resource;
        $this->resource = imagecreatetruecolor($w, $h);

        imagecopy($this->resource, $org, 0, 0, $x, $y, $w, $h);

        $this->width  = $w;
        $this->height = $h;

        if (!file_exists($this->tmp_dir)) {
            mkdir($this->tmp_dir);
            chmod($this->tmp_dir, 0777);
        }

        // cache a copy
        imagepng($this->resource, $tmp_file);
    }

}


?>
