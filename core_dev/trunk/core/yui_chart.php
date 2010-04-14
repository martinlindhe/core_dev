<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI chart (flash object)
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: WIP

//TODO: include external yui files in a smarter way
//TODO: use jsArray-functions more

class yui_chart
{
    private $data_source = '';

    function setDataSource($arr) { $this->data_source = $arr; }

    private $x_field_name, $x_field_title;

    function setXField($name, $title) { $this->x_field_name = $name; $this->x_field_title = $title; }

    private $t_title; ///< display title for Y dimension
    function setYTitle($title) { $this->y_title = $title; }

    private $y_fields = array();
    function addYField($name, $title) { $this->y_fields[] = array('name'=>$name, 'title'=>$title); }

    function render()
    {
        $res = '<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo/yahoo-min.js&2.8.0r4/build/dom/dom-min.js&2.8.0r4/build/event/event-min.js&2.8.0r4/build/element/element-min.js&2.8.0r4/build/json/json-min.js&2.8.0r4/build/datasource/datasource-min.js&2.8.0r4/build/swf/swf-min.js&2.8.0r4/build/charts/charts-min.js"></script>';

        $res .=
        '<style type="text/css">'.
        '#chart { width: 500px; height: 350px; }'.
        '</style>';

        $res .=
        '<div id="chart">'.
        'Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher. '.
        'You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.'.
        '</div>';

        $res .=
        '<script type="text/javascript">'.
        'YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.8.0r4/build/charts/assets/charts.swf";';

        //--- data
        $res .=
        'YAHOO.example.DataSource = '.jsArray2D($this->data_source).';'."\n".
        'var myDataSource = new YAHOO.util.DataSource( YAHOO.example.DataSource);'."\n".
        'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'."\n";

        $res .=
        'myDataSource.responseSchema = { fields: [ ';
        $res .= '"'.$this->x_field_name.'",';
        for ($i=0; $i < count($this->y_fields); $i++)
            $res .= '"'.$this->y_fields[$i]['name'].'",';

        $res .= '] };'."\n";

        //--- chart
        $res .= 'var seriesDef = [';
        $res .= '{ displayName: "'.$this->x_field_title.'", yField: "'.$this->x_field_name.'" }, ';

        for ($i=0; $i < count($this->y_fields); $i++)
            $res .= '{ displayName: "'.$this->y_fields[$i]['title'].'", yField: "'.$this->y_fields[$i]['name'].'" }, ';

        $res .= ' ];';

        $res .=
        'YAHOO.example.getDataTipText = function( item, index, series )
        {
            var toolTipText = series.displayName + " at " + item.'.$this->x_field_name.';
            toolTipText += "\n" + item[series.yField];
            return toolTipText;
        }'."\n";

        //Style object for chart
        $res .=
        'var styleDef ='.
        '{'.
            'xAxis: { labelRotation:-90 },'.
            'yAxis: { titleRotation:-90 }'.
        '}'."\n";

        $res .=
        'var yAxisWidget = new YAHOO.widget.NumericAxis();'.
        'yAxisWidget.minimum = 0;'.
        'yAxisWidget.title = "'.$this->y_title.'";';

        $res .=
        'var xAxisWidget = new YAHOO.widget.CategoryAxis();'.
        'xAxisWidget.minimum = 0;'.
        'xAxisWidget.title = "'.$this->x_field_title.'";';

        $res .=
        'var mychart = new YAHOO.widget.LineChart( "chart", myDataSource, {'.
        'series: seriesDef,'.
        'xField: "'.$this->x_field_name.'",'.
        'yAxis: yAxisWidget,'.
        'xAxis: xAxisWidget,'.
        'style: styleDef,'.
        'dataTipFunction: YAHOO.example.getDataTipText,'.
        //only needed for flash player express install
        'expressInstall: "assets/expressinstall.swf"'.
        '});'."\n";

        $res .= '</script>';

        return $res;
    }
}

?>
