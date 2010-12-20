<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

class SvgLine implements ISvgComponent
{
    var $x1, $x2, $y1, $y2;
    var $color;                ///< SvgColor object
    var $border_width = 2;

    function render()
    {
        if (!$this->color)
            $this->color = new SvgColor('#888');

        $res = '<line x1="'.$this->x1.'" y1="'.$this->y1.'" x2="'.$this->x2.'" y2="'.$this->y2.'" stroke-width="'.$this->border_width.'" stroke="'.$this->color->render().'"/>';
        return $res;
    }

}

?>
