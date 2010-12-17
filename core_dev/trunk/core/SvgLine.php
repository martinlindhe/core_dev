<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

//XXXX TODO use a SvgColor class

class SvgLine implements ISvgComponent
{
    var $x1, $x2, $y1, $y2;
    var $rgb;
    var $stroke_width = 2;

    function render()
    {
        //XXX handle RGB
        $res = '<line x1="'.$this->x1.'" y1="'.$this->y1.'" x2="'.$this->x2.'" y2="'.$this->y2.'" style="stroke:rgb(99,99,99);stroke-width:'.$this->stroke_width.'"/>';
        return $res;
    }

}

?>
