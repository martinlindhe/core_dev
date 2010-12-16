<?php
/**
 * $Id$
 *
 * Renders a GD image resource to PNG/GIF/JPEG
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

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
    function __construct(&$r = false)
    {
        if ($r)
            $this->load($r);
    }

    function load(&$r)
    {
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

        if ($r) //XXX check class name
            $this->resource = &$r;
bt();
        throw new Exception  ('init class with width&height from resource!!! '. $r);
    }

    function render()
    {
        return $this->renderType('png');
    }

    function renderType($s)
    {
        switch ($s) {
        case 'gif':
        default:
            return $this->renderGif();

        case 'jpg':
        case 'jpeg':
        default:
            return $this->renderJpeg();

        case 'png':
        default:
            return $this->renderPng();
        }
    }

    function renderPng()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->disableDesign();
        $page->setMimeType('image/png');

        imagepng($this->resource);
        imagedestroy($this->resource);
    }

    function renderGif()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->disableDesign();
        $page->setMimeType('image/gif');

        imagegif($this->resource);
        imagedestroy($this->resource);
    }

    function renderJpeg()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->disableDesign();
        $page->setMimeType('image/jpeg');

        imagejpeg($this->resource, '', $this->jpeg_quality);
        imagedestroy($this->resource);
    }

}

?>
