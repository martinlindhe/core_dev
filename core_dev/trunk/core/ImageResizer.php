<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: store thumbs on disk: org file csum + target resolution?

require_once('Image.php');

class ImageResizer extends Image
{
    protected $resample = true;
    protected $tmp_dir  = '/tmp/core_dev-images/';

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
            throw new Exception ('need dst size');

        list($tn_width, $tn_height) = $this->calcAspect($to_width, $to_height);
//        echo 'Resizing from '.$this->width.'x'.$this->height.' to '.$tn_width.'x'.$tn_height.'<br/>';

        $key = 'resized-'.$this->sha1.'-'.$tn_width.'x'.$tn_height;
        $tmp_file = $this->tmp_dir.$key;
        if (file_exists($tmp_file)) {
            $this->load($tmp_file);
            return true;
        }

        $org = $this->resource;
        $this->resource = imagecreatetruecolor($tn_width, $tn_height);

        if ($this->resample)
            imagecopyresampled($this->resource, $org, 0,0,0,0, $tn_width, $tn_height, $this->width, $this->height);
        else
            imagecopyresized($this->resource, $org, 0,0,0,0, $tn_width, $tn_height, $this->width, $this->height);

        $this->width  = $tn_width;
        $this->height = $tn_height;

        if (!file_exists($this->tmp_dir))
            mkdir($this->tmp_dir);

        imagejpeg($this->resource, $tmp_file, $this->jpeg_quality);
    }

    /** calculates the max width & height, while keeping aspect ratio */
    protected function calcAspect($to_width, $to_height)
    {
        $x_ratio = $to_width  / $this->width;
        $y_ratio = $to_height / $this->height;

        if (($x_ratio * $this->height) < $to_height)
            return array($to_width, ceil($x_ratio * $this->height));

        return array(ceil($y_ratio * $this->width), $to_height);
    }

}


/**
 * Resizes selected image to $pct percent of orginal image dimensions
 *
 * @param $in_file filename of input image
 * @param $out_file filename of output image
 * @param $_pct percent to resize, relative to orginal image dimensions
 */

/*
function resizeImage($in_file, $out_file, $_pct)
{
    global $h, $config;
    if (!is_numeric($_pct)) return false;

    $mime = $h->files->lookupMimeType($in_file);

    if (!$h->files->image_convert) return false;

    //Resize with imagemagick
    switch ($mime) {
        case 'image/jpeg':
            $c = 'convert -resize '.$_pct. '% -quality '.$config['image']['jpeg_quality'].' '.escapeshellarg($in_file).' JPG:'.escapeshellarg($out_file);
            break;

        case 'image/png':
            $c = 'convert -resize '.$_pct. '% '.escapeshellarg($in_file).' PNG:'.escapeshellarg($out_file);
            break;

        case 'image/gif':
            $c = 'convert -resize '.$_pct. '% '.escapeshellarg($in_file).' GIF:'.escapeshellarg($out_file);
            break;

        default:
            echo 'resizeImage(): Unhandled mimetype "'.$mime.'"<br/>';
            return false;
    }
    //echo 'Executing: '.$c.'<br/>';
    exec($c);
    if (!file_exists($out_file)) return false;
    return true;
}
*/

?>
