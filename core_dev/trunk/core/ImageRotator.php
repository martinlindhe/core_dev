<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */
 
//STATUS: wip

require_once('Image.php');

class ImageRotator extends Image
{
    /**
     * Rotates a image the specified angle
     * @param $angle  %angle to rotate. between -360 and 360. 90 = "rotate to left", 180 = "upside down", 270 = "rotate to right"
     */
    function rotate($angle)
    {
        if (!function_exists('imagerotate'))
            throw new Exception ('php-gd2 missing');

        if (!$this->resource)
            throw new Exception ('no image resource loaded');                

        if (!is_numeric($angle))
            return false;

        $this->resource = imagerotate($this->resource, $angle, 0);
    }

}

?>
