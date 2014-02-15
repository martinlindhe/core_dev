<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2011 <martin@ubique.se>
 */

//STATUS: wip

//XXX TODO: handle alpha channel in colors

namespace cd;

class SvgCircle implements ISvgComponent
{
    var $color;             ///< XXX fill color RGBA
    var $border;            ///< XXX border color RGBA
    var $x, $y;             ///< coordinates of the center of the circle
    var $radius;            ///< radius of the circle
    var $border_width = 1;

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

        if (!$this->color)
            $this->color = new SvgColor('#aaeeaa');

        if (!$this->border)
            $this->border = new SvgColor('#888');

/*
            ' fill="#'.sprintf('%06x', $circ['color']).'"'.
            ($fill_a < 1 ? ' fill-opacity="'.$fill_a.'"' : '');
            if ($circ['border'] !== false) {
                $res .=
                ' stroke-width="1" stroke="#'.sprintf('%06x', $circ['border']).'"'.
                ($stroke_a < 1 ? ' stroke-opacity="'.$stroke_a.'"': '');
            }
*/

        $res =
        '<circle fill="'.$this->color->render().'" fill-opacity="4"'.
        ' stroke-width="'.$this->border_width.'" stroke="'.$this->border->render().'" stroke-opacity="4"'.
        ' cx="'.$this->x.'" cy="'.$this->y.'" r="'.$this->radius.'"/>';

        return $res;
    }

}

?>
