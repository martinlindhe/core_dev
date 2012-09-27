<?php
/**
 * $Id$
 *
 * Renders SVG graphics embedded into a XML (XHTML) document
 *
 * Documentation:
 * http://www.w3.org/TR/SVG11/
 * http://www.w3.org/TR/SVG/shapes.html
 *
 * SVG test suite:
 * http://www.w3.org/Graphics/SVG/Test/
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: fix SvgColor to support alpha-channel

namespace cd;

require_once('ISvgComponent.php');
require_once('SvgCircle.php');
require_once('SvgColor.php');
require_once('SvgLine.php');
require_once('SvgPolygon.php');
require_once('SvgRectangle.php');
require_once('SvgText.php');

class SvgImage
{
    protected $bgcolor;          ///< SvgColor background color
    protected $objs = array();
    protected $width  = 100;
    protected $height = 100;

    function add($o)
    {
        if (!($o instanceof ISvgComponent))
            throw new \Exception ('only want SvgLine');

        $this->objs[] = $o;
    }

    function setBackground($col) { $this->bgcolor = new SvgColor($col); }

    function render()
    {
        $page = XmlDocumentHandler::getInstance();
        $page->setMimeType('application/xhtml+xml');   //page wont even display in IE

        $res = "\n".
        '<svg xmlns="http://www.w3.org/2000/svg"'.
            ' version="1.1">'."\n";
            // viewBox="0 0 '.$this->width.' '.$this->height.'">'; // style="position:absolute; top:0; left:0; z-index:-1;">';

        // SVG has a transparent background by default, set background color with a filled rectangle
        if ($this->bgcolor) {
            $bg = new SvgRectangle();
            $bg->width  = "100%";
            $bg->height = "100%";
            $bg->fill_color  = $this->bgcolor;
            $res .= $bg->render()."\n";
        }

        foreach ($this->objs as $o)
            $res .= $o->render()."\n";

        $res .= '</svg>'."\n";

        return $res;
    }

    function save($filename)
    {
        file_put_contents($filename, $this->render());
    }

}

?>
