<?php
/**
 * $Id$
 *
 * Utility class for images, supporting PNG/GIF/JPEG images and GD resources
 *
 * @author Martin Lindhe, 2010-2012 <martin@startwars.org>
 */

//STATUS: wip

//TODO: see ImageResource class in MediaResource.php

//XXX: drop automatic sha1 generation, add a calcSha1() method

namespace cd;

class Image
{
    protected $resource;   ///< holds gd image resource

    var $width, $height;
    var $mimetype;
    protected $jpeg_quality = 80;
    protected $sha1;

    /**
     * @param $r GD resource, or full path to image file
     */
    function __construct($r = false)
    {
        if (!extension_loaded('gd'))
            throw new \Exception ('sudo apt-get install php5-gd');

        if ($r)
            $this->load($r);
    }

    function getWidth() { return $this->width; }
    function getHeight() { return $this->height; }

    function getPixel($x, $y)
    {
        return imagecolorat($this->resource, $x, $y);
    }

    function getResource() { return $this->resource; }

    /**
     * Initializes object from an resource
     *
     * @param $r can be a path to a image file, a GD resource or a File object
     */
    function load($r)
    {
        if ($r instanceof File)
            $r = File::getUploadPath($r->id);

        if (is_resource($r) && get_resource_type($r) == 'gd')
        {
            dp($r);
            $this->resource = $r;
            $this->width  = imagesx($r);
            $this->height = imagesy($r);
            return;
        }

        if (!is_readable($r))
            throw new \Exception ('image resource not found: '.$r);

        $info = getimagesize($r);

        switch ($info['mime']) {
        case 'image/jpeg': $im = imagecreatefromjpeg($r); break;
        case 'image/png':  $im = imagecreatefrompng($r); break;
        case 'image/gif':  $im = imagecreatefromgif($r); break;
        default: throw new \Exception ('Unsupported image type '.$info['mime'].' for '.$r);
        }

        $this->resource = $im;
        $this->width    = $info[0];
        $this->height   = $info[1];
        $this->mimetype = $info['mime'];
        $this->sha1     = sha1_file($r);
        return;
    }

    function render($type = 'png', $dst_file = '')
    {
        if (!$this->resource)
            throw new \Exception ('no image resource loaded');

        $page = XmlDocumentHandler::getInstance();
        $page->disableHtmlHeaders();
        $page->disableDesign();

        switch ($type) {
        case 'image/gif':
        case 'gif':
            if (!$dst_file)
                $page->setMimeType('image/gif');
            imagegif($this->resource, $dst_file);
            break;

        case 'image/jpeg':
        case 'jpg':
        case 'jpeg':
            if (!$dst_file)
                $page->setMimeType('image/jpeg');
            imagejpeg($this->resource, $dst_file, $this->jpeg_quality);
            break;

        case 'image/png':
        case 'png':
            if (!$dst_file)
                $page->setMimeType('image/png');
            imagepng($this->resource, $dst_file);
            break;

        default:
            throw new \Exception ('odd render type '.$type);
        }
    }

}

function showThumb($id, $title = '', $w = 50, $h = 50)
{
    $i = new XhtmlComponentImage();
    $i->src = getThumbUrl($id, $w, $h);
    $i->alt = strip_tags($title);
    $i->title = strip_tags($title);

    return $i->render();
}

function getThumbUrl($id, $width = 50, $height = 50)
{
    if (!is_numeric($width) || !is_numeric($height))
       return;

    if (is_float($width))  $width  = floor($width);
    if (is_float($height)) $height = floor($height);

    $page = XmlDocumentHandler::getInstance();

    return
        $page->getRelativeUrl().'c/image/'.$id.
        ($width ? '?w='.$width : '').
        ($height ? '&h='.$height : '');
}

?>
