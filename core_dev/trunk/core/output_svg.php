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

//TODO: rename file to SvgImage.php

//FIXME: opacity is not quite correct

class SvgLine
{
    var $x1,$x2,$y1,$y2;
    var $rgb;   //XXXX TODO use a SvgColor class
    var $stroke_width = 2;

    function render()
    {
        //XXX handle RGB
        $res = '<line x1="'.$this->x1.'" y1="'.$this->y1.'" x2="'.$this->x2.'" y2="'.$this->y2.'" style="stroke:rgb(99,99,99);stroke-width:'.$this->stroke_width.'"/>';
        return $res;
    }
}


/**
 * each array element contains:
 * ['coords'] a set of X,Y coordinates
 * ['color'] fill color RGBA
 * ['border'] border color RGBA
 */
class SvgPolygon  //XXX NOT WORKING
{
    function render()
    {
        $fill_a = ($poly['color'] >> 24) & 0xFF;
        $fill_a = round($fill_a/127, 2);        //XXX loss of precision
        if (!$fill_a) $fill_a = 1;    //set missing alpha as 100% alpha
        $poly['color'] = $poly['color'] & 0xFFFFFF;

        if ($poly['border'] !== false) {
            $stroke_a = ($poly['border'] >> 24) & 0xFF;
            $stroke_a = round($stroke_a/127, 2);
            if (!$stroke_a) $stroke_a = 1;
            $poly['border'] = $poly['border'] & 0xFFFFFF;
        }

        $res .=
        '<polygon'.
            ' fill="#'.sprintf('%06x', $poly['color']).'"'.
            ($fill_a < 1 ? ' fill-opacity="'.$fill_a.'"' : '');
            if ($poly['border'] !== false) {
                $res .=
                ' stroke-width="1" stroke="#'.sprintf('%06x', $poly['border']).'"'.
                ($stroke_a < 1 ? ' stroke-opacity="'.$stroke_a.'"': '');
            }

        $res .= ' points="';
        for ($i=0; $i<count($poly['coords']); $i+=2) {
            $res .= $poly['coords'][$i].','.$poly['coords'][$i+1];
            if ($i < count($poly['coords'])-2) $res .= ',';
        }
        $res .= '"/>';

        return $res;
    }
}


/**
 * each array element contains:
 * ['x'] x-axis coordinate of the center of the circle
 * ['y'] y-axis coordinate of the center of the circle
 * ['r'] the radius of the circle
 * ['color'] fill color RGBA
 * ['border'] border color RGBA
 */
class SvgCircle ///XXX NOT WORKING
{
    function render()
    {
        $fill_a = ($circ['color'] >> 24) & 0xFF;
        $fill_a = round($fill_a/127, 2);        //XXX loss of precision
        if (!$fill_a) $fill_a = 1;    //set missing alpha as 100% alpha
        $circ['color'] = $circ['color'] & 0xFFFFFF;

        if ($circ['border'] !== false) {
            $stroke_a = ($circ['border'] >> 24) & 0xFF;
            $stroke_a = round($stroke_a/127, 2);
            if (!$stroke_a) $stroke_a = 1;
            $circ['border'] = $circ['border'] & 0xFFFFFF;
        }

        $res .=
        '<circle'.
            ' fill="#'.sprintf('%06x', $circ['color']).'"'.
            ($fill_a < 1 ? ' fill-opacity="'.$fill_a.'"' : '');
            if ($circ['border'] !== false) {
                $res .=
                ' stroke-width="1" stroke="#'.sprintf('%06x', $circ['border']).'"'.
                ($stroke_a < 1 ? ' stroke-opacity="'.$stroke_a.'"': '');
            }

        $res .= ' cx="'.$circ['x'].'" cy="'.$circ['y'].'" r="'.$circ['r'].'"/>';

        return $res;
    }
}

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
