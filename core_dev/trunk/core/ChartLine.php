<?php
/**
 * Displays a line chart using SVG graphics
 *
 * https://secure.wikimedia.org/wikipedia/en/wiki/Line_chart
 */

//STATUS: draft, should replace YuiChart (flash)

//XXX TODO: png render ability

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

        return parent::render();
    }
}

?>
