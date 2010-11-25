<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI chart (flash object)
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: use jsArray-functions more

require_once('output_js.php');

class YuiChart
{
    private $data_source = '';
    private $width  = 700;
    private $height = 400;
    private $x_field_name, $x_field_title;
    private $t_title; ///< display title for Y dimension
    private $y_fields = array();

    function setDataSource($arr) { $this->data_source = $arr; }

    function setXField($name, $title) { $this->x_field_name = $name; $this->x_field_title = $title; }

    function setYTitle($title) { $this->y_title = $title; }

    function addYField($name, $title)
    {
        foreach ($this->y_fields as $field)
            if ($field['name'] == $name)
                return false;

        $this->y_fields[] = array('name' => $name, 'title' => $title);
    }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/element/element-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/json/json-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/swf/swf-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/connection/connection-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/charts/charts-min.js');

        $div_holder = 'yui_chart'.mt_rand(0,99999);

        $header->embedCss('#'.$div_holder.' { width: '.$this->width.'px; height: '.$this->height.'px; }');

        $res =
        'YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.8.2r1/build/charts/assets/charts.swf";'.

        //--- data
        'YAHOO.example.DataSource = '.($this->data_source ? jsArray2D($this->data_source) : '""').';'.
        'var myDataSource = new YAHOO.util.DataSource(YAHOO.example.DataSource);'.
        'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'.

        'myDataSource.responseSchema = { fields: ["'.$this->x_field_name.'",';
        for ($i=0; $i < count($this->y_fields); $i++)
            $res .= '"'.$this->y_fields[$i]['name'].'",';
        $res .= '] };';

        //--- chart
        $res .=
        'var seriesDef = ['.
        '{ displayName: "'.$this->x_field_title.'", yField: "'.$this->x_field_name.'" },';

        for ($i=0; $i < count($this->y_fields); $i++)
            $res .= '{ displayName: "'.$this->y_fields[$i]['title'].'", yField: "'.$this->y_fields[$i]['name'].'" },';

        $res .= ' ];';

        $res .=
        'YAHOO.example.getDataTipText = function( item, index, series )'.
        '{'.
            'var toolTipText = series.displayName + " at " + item.'.$this->x_field_name.';'.
            'toolTipText += "\n" + item[series.yField];'.
            'return toolTipText;'.
        '};'.

        //Style object for chart
        'var styleDef ='.
        '{'.
            'xAxis: { labelRotation:-90 },'.
            'yAxis: { titleRotation:-90 }'.
        '};'.

        'var yAxisWidget = new YAHOO.widget.NumericAxis();'.
        'yAxisWidget.minimum = 0;'.
        'yAxisWidget.title = "'.$this->y_title.'";'.

        'var xAxisWidget = new YAHOO.widget.CategoryAxis();'.
        'xAxisWidget.minimum = 0;'.
        'xAxisWidget.title = "'.$this->x_field_title.'";'.

        'var mychart = new YAHOO.widget.LineChart("'.$div_holder.'",myDataSource,'.
        '{'.
            'series: seriesDef,'.
            'xField: "'.$this->x_field_name.'",'.
            'yAxis: yAxisWidget,'.
            'xAxis: xAxisWidget,'.
            'style: styleDef,'.
            'dataTipFunction: YAHOO.example.getDataTipText,'.
            //only needed for flash player express install
            'expressInstall: "assets/expressinstall.swf"'.
        '});';

        return
        '<div id="'.$div_holder.'">'.
        'Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher. '.
        'You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.'.
        '</div>'.js_embed($res);
    }
}

?>
