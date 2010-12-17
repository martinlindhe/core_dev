<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: NOT WORKING

/**
 * each array element contains:
 * ['x'] x-axis coordinate of the center of the circle
 * ['y'] y-axis coordinate of the center of the circle
 * ['r'] the radius of the circle
 * ['color'] fill color RGBA
 * ['border'] border color RGBA
 */
class SvgCircle
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

?>
