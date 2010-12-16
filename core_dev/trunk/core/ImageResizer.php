<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//XXX: store thumbs on disk: org file csum + target resolution?

require_once('Image.php');

class ImageResizer extends Image
{
    protected $resample = true;

    /**
     * Resizes specified image file to specified dimensions
     *
     * @param $to_width if set, resize image to this width
     * @param $to_height if set, resize image to this height
     */
    function resizeAspect($to_width = 0, $to_height = 0)
    {
        if (!$this->width || !$this->height)
            throw new Exception ('can happen?');

        if (!$to_width && !$to_height)
            return false;

        list($tn_width, $tn_height) = $this->calc($to_width, $to_height);
//        echo 'Resizing from '.$this->width.'x'.$this->height.' to '.$tn_width.'x'.$tn_height.'<br/>';

        $i = imagecreatetruecolor($tn_width, $tn_height);

        if ($this->resample)
            imagecopyresampled($i, $this->resource, 0,0,0,0, $tn_width, $tn_height, $this->width, $this->height);
        else
            imagecopyresized($i, $this->resource, 0,0,0,0, $tn_width, $tn_height, $this->width, $this->height);

        $this->resource = $i;
        $this->width  = $tn_width;
        $this->height = $tn_height;
    }

    /** calculates the max width & height, while keeping aspect ratio */
    private function calc($to_width, $to_height)
    {
        $x_ratio = $to_width  / $this->width;
        $y_ratio = $to_height / $this->height;

        if (($x_ratio * $this->height) < $to_height)
            return array($to_width, ceil($x_ratio * $this->height));

        return array(ceil($y_ratio * $this->width), $to_height);
    }

}

?>
