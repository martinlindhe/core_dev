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

//TODO (MISSING FROM YUI2-chart): visa label för x & y led: setYTitle(), setXTitle()

require_once('output_js.php');

class Yui3Chart
{
    private $data_source = '';   ///< array of data to display
    private $width  = 700;
    private $height = 400;
    private $x_field;           ///< group by this field (usually a timestamp)
    private $x_title, $y_title; ///< display titles

    private $x_type = ''; ///< is the X-axis a timestamp?

    function setDataSource($arr)
    {
        if (!$this->x_field)
            throw new Exception ('x field must be set before data source');

        $this->data_source = array();

        foreach ($arr as $idx => $vals)
        {
            $x = array($this->x_field => $idx);
            if (is_array($vals))
                foreach ($vals as $idx => $val)
                    $x[$idx] = $val;
            else
                $x['value'] = $vals;

            $this->data_source[] = $x;
        }
    }

    function setXType($t)
    {
        if ($t != 'time')
            throw new Exception ('unknown x type');

        $this->x_type = $t;
    }

    function setXField($name) { $this->x_field = $name; }

    function setXTitle($title) { $this->x_title = $title; }
    function setYTitle($title) { $this->y_title = $title; }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js');

        $div_holder = 'yui_chart'.mt_rand(0,99999);

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
                    '"<span style=\"text-decoration:underline\">" + valueItem.displayName + " for " + '.
                    'categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")]) + '.
                    '"</span><br/>'.
                    '<div style=\"margin-top:5px;font-weight:bold\">" + valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:"", decimalPlaces:2}]) + "</div>";'.
                '}'.
            '};'.

            'var styleDef ='.
            '{'.
                'axes:{'.
                    'values:{'.
                        'label:{'.
                            //'rotation:-45,'.
                            //'color:"#ff0000"'.
                        '}'.
                    '},'.
                    $this->x_field.':{'.
                        'label:{'.
                            'rotation:-45,'.
                            //'color: "#ff0000"'.
                        '}'.
                    '}'.
                '}'.
            '};'.

            'var myDataValues = '.jsArray2D($this->data_source).';'.

            'var mychart = new Y.Chart('.
            '{'.
                'dataProvider:myDataValues,'.
                'categoryKey:"'.$this->x_field.'",'.
                ($this->x_type ? 'categoryType:"'.$this->x_type.'",' : '').

                'render: "#'.$div_holder.'",'.
                'tooltip: myTooltip,'.
                'styles: styleDef,'.

                'horizontalGridlines: {'.
                    'styles: { line: { color: "#dad8c9" } }'.
                '},'.
                'verticalGridlines: {'.
                    'styles: { line: { color: "#dad8c9" } }'.
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