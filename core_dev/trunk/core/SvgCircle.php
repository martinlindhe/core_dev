<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

class SvgCircle implements ISvgComponent
{
    var $color;   /// XXXX fill color RGBA
    var $border;  /// XXXX border color RGBA
    var $x;       ///< x-axis coordinate of the center of the circle
    var $y;       ///< y-axis coordinate of the center of the circle
    var $radius;  ///< the radius of the circle

    function render()
    {
/*
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
*/
        $res =
        '<circle fill="#aaeeaa" fill-opacity="4" stroke-width="1" stroke="#888888" stroke-opacity="4"';
/*
            ' fill="#'.sprintf('%06x', $circ['color']).'"'.
            ($fill_a < 1 ? ' fill-opacity="'.$fill_a.'"' : '');
            if ($circ['border'] !== false) {
                $res .=
                ' stroke-width="1" stroke="#'.sprintf('%06x', $circ['border']).'"'.
                ($stroke_a < 1 ? ' stroke-opacity="'.$stroke_a.'"': '');
            }
*/
        $res .= ' cx="'.$this->x.'" cy="'.$this->y.'" r="'.$this->radius.'"/>';

        return $res;
    }

}

?>
