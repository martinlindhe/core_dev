<?php
/**
 * $Id$
 *
 * Simple SVG renderer
 * Currently only capable of rendering a set of polygons
 *
 * Documentation:
 * http://www.w3.org/TR/SVG11/
 * http://www.w3.org/TR/SVG/shapes.html
 *
 * SVG test suite:
 * http://www.w3.org/Graphics/SVG/Test/
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: fix SvgColor to support alpha-channel

require_once('ISvgComponent.php');
require_once('SvgCircle.php');
require_once('SvgColor.php');
require_once('SvgLine.php');
require_once('SvgPolygon.php');
require_once('SvgRectangle.php');
require_once('SvgText.php');

class SvgImage
{
    var $width, $height;
    var $bgcolor;          ///< SvgColor background color
    var $objs = array();

    function __construct($width = 100, $height = 100)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    function add($o)
    {
        if (!($o instanceof ISvgComponent))
            throw new Exception ('only want SvgLine');

        $this->objs[] = $o;
    }

    function setBackground($col) { $this->bgcolor = new SvgColor($col); }

    function render()
    {
        $res =
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.
        '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"'.
            ' version="1.1" width="'.$this->width.'px" height="'.$this->height.'px"'.
            ' viewBox="0 0 '.$this->width.' '.$this->height.'">';

        // SVG has a transparent background by default, set background color with a filled rectangle
        if ($this->bgcolor) {
            $bg = new SvgRectangle();
            $bg->width  = $this->width;
            $bg->height = $this->height;
            $bg->fill_color  = $this->bgcolor;
            $res .= $bg->render();
        }

        foreach ($this->objs as $o)
            $res .= $o->render();

        $res .= '</svg>';

        return $res;
    }

    function output()
    {
        header('Content-type: image/svg+xml');

        echo $this->render();
    }

    function save($filename)
    {
        file_put_contents($filename, $this->render());
    }

}

?>
