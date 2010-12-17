<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: NOT WORKING

/**
 * each array element contains:
 * ['coords'] a set of X,Y coordinates
 * ['color'] fill color RGBA
 * ['border'] border color RGBA
 */
class SvgPolygon
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

?>
