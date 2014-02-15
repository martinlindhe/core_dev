<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2011 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class SvgPolygon implements ISvgComponent
{
    var $color;                   ///< XXXX fill color RGBA
    var $border;                  ///< XXXXX border color RGBA
    var $coords       = array();  ///< array of x,y coordinates
    var $border_width = 1;

    function addPoint($x, $y)
    {
        $this->coords[] = $x;
        $this->coords[] = $y;
    }

    function render()
    {
/*
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
*/
        if (!$this->color)
            $this->color = new SvgColor('#eeaa99');

        if (!$this->border)
            $this->border = new SvgColor('#88aa11');

/*
            ($fill_a < 1 ? ' fill-opacity="'.$fill_a.'"' : '');
            if ($poly['border'] !== false) {
                $res .=
                ' stroke-width="1" stroke="#'.sprintf('%06x', $poly['border']).'"'.
                ($stroke_a < 1 ? ' stroke-opacity="'.$stroke_a.'"': '');
            }
*/
        $res =
        '<polygon fill="'.$this->color->render().'" fill-opacity="4"'.
        ' stroke-width="'.$this->border_width.'" stroke="'.$this->border->render().'" stroke-opacity="4"'.
        ' points="'.implode(',', $this->coords).'"/>';

        return $res;
    }

}

?>
