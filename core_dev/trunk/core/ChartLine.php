<?php
/**
 * Displays a line chart using SVG graphics
 *
 * https://secure.wikimedia.org/wikipedia/en/wiki/Line_chart
 */

//STATUS: draft, should replace YuiChart (flash)

// ... OR base this on http://www.liquidx.net/plotkit/ (BSD license)

//TODO: render using html canvas (for IE9 compatibility)
//TODO: render using png (for IE6,7 compatibility)

require_once('SvgImage.php');

class ChartLine extends SvgImage
{
    protected $data_lists; ///< array containing arrays of data values
    var $txt_horiz = 'horizontal text';
    var $txt_vert  = 'vertical text';

    function __construct($width = 100, $height = 100)
    {
        $this->width  = $width;
        $this->height = $height;
    }

    /** adds one set of data values */
    function addData($arr)
    {
        $this->data_lists[] = $arr;
    }

    function render()
    {
        // attach svg objects to parent class
        $txt = new SvgText($this->txt_horiz);
        $txt->x = 50;
        $txt->y = $this->height - 3;
        $this->add($txt);

        $txt = new SvgText($this->txt_vert);
        $txt->vertical = true;
        $txt->x = -35;
        $txt->y = -80; //XXX not scalable
        $this->add($txt);

        $h_line = new SvgLine();
        $h_line->color = new SvgColor('#b00');
        $h_line->x1 = "2%";
        $h_line->x2 = "98%";
        $h_line->y1 = "93%";
        $h_line->y2 = "93%";
        $this->add($h_line);

        $v_line = new SvgLine();
        $v_line->color = new SvgColor('#b00');
        $v_line->x1 = "2%";
        $v_line->x2 = "2%";
        $v_line->y1 = "2%";
        $v_line->y2 = "93%";
        $this->add($v_line);


        return parent::render();
    }
}

?>
