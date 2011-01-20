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

//TODO: also calculate & show percentage in tooltip

require_once('output_js.php');

class Yui3PieChart
{
    protected $data_source = '';   ///< array of data to display
    protected $width  = 400;
    protected $height = 400;
    protected $category_key;      ///< group by this field (usually a timestamp)

    function setCategoryKey($name) { $this->category_key = $name; }
    function setWidth($n) { if (is_numeric($n)) $this->width = $n; }
    function setHeight($n) { if (is_numeric($n)) $this->height = $n; }

    function setDataSource($arr)
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
                foreach ($vals as $idx => $val)
                    $x[$idx] = $val;
            else
                $x['value'] = $vals;

            $this->data_source[] = $x;
        }
    }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js');

        $div_holder = 'yui_chart'.mt_rand(0,99999);

        $header->embedCss('#'.$div_holder.' { width: '.$this->width.'px; height: '.$this->height.'px; }');

        $res =
        'YUI().use("charts", function (Y)'.
        '{'.
            'var myDataValues = '.jsArray2D($this->data_source).';'.

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
                    '"<span style=\"text-decoration:underline\">" + '.
                    'categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")]) + '.
                    '"</span><br/>'.
                    '<div style=\"margin-top:5px;font-weight:bold\">" + valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:"", decimalPlaces:2}]) + "</div>";'.
                '}'.
            '};'.

            'var mychart = new Y.Chart({'.
                    'dataProvider:myDataValues,'.
                    'categoryKey:"'.$this->category_key.'",'.
                    'type:"pie",'.
                    'render: "#'.$div_holder.'",'.
                    'tooltip: myTooltip,'.

                    'seriesKeys:["value"],'.
                    'seriesCollection:['.
                        '{'.
                            'categoryKey:"'.$this->category_key.'",'.
                            'valueKey:"value"'.
                        '}'.
                    ']'.
                '});'.
        '});';

        return
        '<div id="'.$div_holder.'">'.'</div>'.js_embed($res);
    }
}

?>
