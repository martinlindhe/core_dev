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
    protected $resource;
    protected $jpeg_quality = 80;

    /**
     * @param $r GD resource
     */
    function __construct(&$r = false)
    {
        if ($r) //XXX check class name
            $this->resource = &$r;
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
