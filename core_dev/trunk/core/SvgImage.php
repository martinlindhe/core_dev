<?php
/**
 * $Id$
 *
 * Simple SVG renderer
 * Currently only capable of rendering a set of polygons
 *
 * Documentation:
 * http://www.w3.org/TR/SVG11/
 * http://www.w3.org/Graphics/SVG/
 * http://www.w3.org/TR/SVG/shapes.html
 *
 * SVG test suite:
 * http://www.w3.org/Graphics/SVG/Test/
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

//FIXME: opacity is not quite correct

require_once('SvgLine.php');
require_once('SvgPolygon.php');
require_once('SvgCircle.php');

class SvgImage
{
    var $objs = array();

    var $width, $height;
    var $bgcolor = false;    ///< background color, XXX REWORK TO USE SvgColor

    function __construct($width = 100, $height = 100)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    function add($o)
    {
        if (!($o instanceof SvgLine))
            throw new Exception ('only want SvgLine');

        $this->objs[] = $o;
    }

    function setBackground($col) { $this->bgcolor = $col; }

    function render()
    {
        $res =
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.
        '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"'.
            ' version="1.1" width="'.$this->width.'px" height="'.$this->height.'px" viewBox="0 0 '.$this->width.' '.$this->height.'">';

        //SVG has a transparent background by default. simulate background color with a filled rectangle
        if ($this->bgcolor !== false) {
            $res .=
            '<rect x="0" y="0" width="'.$this->width.'" height="'.$this->height.'" fill="#'.sprintf('%06x', $this->bgcolor).'"/>';
        }

        foreach ($this->objs as $o) {
            $res .= $o->render();
        }

        $res .=
        '</svg>';

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
