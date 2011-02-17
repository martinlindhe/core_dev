<?php
/**
 * $Id$
 *
 * Renders a GD image resource to PNG/GIF/JPEG
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: drop automatic sha1 generation, add a calcSha1() method

class Image
{
    protected $resource;   ///< holds gd image resource

    protected $jpeg_quality = 80;
    protected $width, $height;
    protected $mimetype;
    protected $sha1;

    /**
     * @param $r GD resource, or full path to image file
     */
    function __construct($r = false)
    {
        if (!function_exists('gd_info'))
            throw new Exception ('sudo apt-get install php5-gd');

        if ($r)
            $this->load($r);
    }

    function getWidth() { return $this->width; }
    function getHeight() { return $this->height; }

    function getPixel($x, $y)
    {
        return imagecolorat($this->resource, $x, $y);
    }

    function load($r)
    {
        if (is_resource($r) && get_resource_type($r) == 'gd') {
            $this->resource = $r;
            $this->width  = imagesx($r);
            $this->height = imagesy($r);
            return;
        }

        if (file_exists($r))
        {
            $info = getimagesize($r);

            switch ($info['mime']) {
            case 'image/jpeg': $im = imagecreatefromjpeg($r); break;
            case 'image/png':  $im = imagecreatefrompng($r); break;
            case 'image/gif':  $im = imagecreatefromgif($r); break;
            default: die('Unsupported image type for '.$r);
            }

            $this->resource = $im;

            $this->width    = $info[0];
            $this->height   = $info[1];
            $this->mimetype = $info['mime'];
            $this->sha1     = sha1_file($r);
            return;
        }

        bt();
        throw new Exception  ('init class with width&height from resource!!! '. $r);
    }

    function render($type = 'png', $dst_file = '')
    {
        $page = XmlDocumentHandler::getInstance();
        $page->disableDesign();

        switch ($type) {
        case 'gif':
            $page->setMimeType('image/gif');
            imagegif($this->resource, $dst_file);
            break;

        case 'jpg':
        case 'jpeg':
            $page->setMimeType('image/jpeg');
            imagejpeg($this->resource, $dst_file, $this->jpeg_quality);
            break;

        case 'png':
        default:
            $page->setMimeType('image/png');
            imagepng($this->resource, $dst_file);
            break;
        }

        imagedestroy($this->resource);
    }

}

?>
