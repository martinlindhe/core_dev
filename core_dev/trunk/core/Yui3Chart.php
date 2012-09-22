<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI 3 chart (svg or canvas object)
 *
 * http://developer.yahoo.com/yui/3/charts/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX also see jqPlot (needs jQuery): http://www.jqplot.com/

//TODO (MISSING FROM YUI2-chart): visa label för x & y led: setYTitle(), setXTitle()
//   - ticket open due to missing functionality: http://yuilibrary.com/projects/yui3/ticket/2529841

require_once('JSON.php');

class Yui3Chart
{
    protected $data_source;   ///< array of data to display
    protected $width  = 700;
    protected $height = 400;
    protected $category_key;      ///< group by this field (usually a timestamp)
    protected $x_title, $y_title; ///< display titles

    protected $color_grid  = '#dad8c9';
    protected $color_label = '#808080';

    protected $x_type = ''; ///< is the X-axis a timestamp?

    /**
     * @param $include_keys array with key names to include
     */
    function setDataSource($arr, $include_keys = false)
    {
        if (!$this->category_key)
            throw new Exception ('category_key must be set before data source');

        $this->data_source = array();

        if (!is_array($arr))
            throw new Exception ('data source is not a array: '.$arr);

        foreach ($arr as $idx => $vals)
        {
            $x = array($this->category_key => $idx);
            if (is_array($vals))
                foreach ($vals as $idx => $val) {
                    if (!$include_keys || in_array($idx, $include_keys))
                        $x[$idx] = $val;
                } else {
                    $x['value'] = $vals;
                }

            $this->data_source[] = $x;
        }
    }

    function setXType($t)
    {
        if ($t != 'time')
            throw new Exception ('unknown x type');

        $this->x_type = $t;
    }

    function setCategoryKey($name) { $this->category_key = $name; }

    function setXTitle($title) { $this->x_title = $title; }
    function setYTitle($title) { $this->y_title = $title; }

    function setGridColor($s) { $this->color_grid = $s; }
    function setLabelColor($s) { $this->color_label = $s; }

    function setWidth($n) { if (is_numeric($n)) $this->width = $n; }
    function setHeight($n) { if (is_numeric($n)) $this->height = $n; }

    function render()
    {
        /*if (!$this->data_source)
            throw new Exception ('no data source set');
*/
        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/3.7.1/build/yui/yui-min.js');


        $div_holder = 'yui_chart'.mt_rand();

        $header->embedCss('#'.$div_holder.' { width: '.$this->width.'px; height: '.$this->height.'px; }');

        $res =
        'YUI().use("charts", function (Y)'.
        '{'.
/*
            'var yAxisWidget = new YAHOO.widget.NumericAxis();'.
            'yAxisWidget.minimum = 0;'.
            'yAxisWidget.title = "'.$this->y_title.'";'.

            'var xAxisWidget = new YAHOO.widget.CategoryAxis();'.
            'xAxisWidget.minimum = 0;'.
            'xAxisWidget.title = "'.$this->x_title.'";'.
*/

            'var myTooltip = {'.
                'styles: { '.
                    'backgroundColor: "#333",'.
                    'color: "#eee",'.
                    'borderColor: "#fff",'.
                    'textAlign: "center"'.
                '},'.
                'markerLabelFunction: function(categoryItem, valueItem, itemIndex, series, seriesIndex)'.
                '{'.
                    'return '.
                    '"<span style=\"text-decoration:underline\">" + valueItem.displayName + " for " + categoryItem.value + "</span><br/>'.
                    '<div style=\"margin-top:5px;font-weight:bold\">" + (valueItem.value ? valueItem.value : "0") + "</div>";'.
                '},'.
                'setTextFunction: function(textField, val)'.
                '{'.
                    'textField.setHTML(val);'.
                '}'.
            '};'.

            'var styleDef = {'.
                'axes:{'.
                    'values:{'.
                        'label:{'.
                            //'rotation:-45,'.
                            'color:"'.$this->color_label.'"'.
                        '}'.
                    '},'.
                    $this->category_key.':{'.
                        'label:{'.
                            'rotation:-45,'.
                            'color: "'.$this->color_label.'"'.
                        '}'.
                    '}'.
                '}'.
            '};'.

            'var myDataValues = '.JSON::encode($this->data_source).';'.

            'var mychart = new Y.Chart('.
            '{'.
                'dataProvider:myDataValues,'.
                'categoryKey:"'.$this->category_key.'",'.
                ($this->x_type ? 'categoryType:"'.$this->x_type.'",' : '').

                'render: "#'.$div_holder.'",'.
                'tooltip: myTooltip,'.
                'styles: styleDef,'.

                'horizontalGridlines: {'.
                    'styles: { line: { color: "'.$this->color_grid.'" } }'.
                '},'.
                'verticalGridlines: {'.
                    'styles: { line: { color: "'.$this->color_grid.'" } }'.
                '}'.

//                'yAxis: yAxisWidget,'.
//                'xAxis: xAxisWidget,'.
            '});'.
        '});';

        return
        '<div id="'.$div_holder.'">'.'</div>'.js_embed($res);
    }
}

?>
