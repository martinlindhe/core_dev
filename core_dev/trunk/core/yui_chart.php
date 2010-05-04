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

class yui_chart
{
    private $data_source = '';
    private $div_holder;
    private $width  = 700;
    private $height = 400;

    function __construct()
    {
        $this->div_holder = 'yui_chart'.mt_rand(0,99999);
    }

    function setDataSource($arr) { $this->data_source = $arr; }

    private $x_field_name, $x_field_title;

    function setXField($name, $title) { $this->x_field_name = $name; $this->x_field_title = $title; }

    private $t_title; ///< display title for Y dimension
    function setYTitle($title) { $this->y_title = $title; }

    private $y_fields = array();
    function addYField($name, $title) { $this->y_fields[] = array('name'=>$name, 'title'=>$title); }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/element/element-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/datasource/datasource-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/json/json-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/swf/swf-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/connection/connection-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.0r4/build/charts/charts-min.js');

        $header->addCss('#'.$this->div_holder.' { width: '.$this->width.'px; height: '.$this->height.'px; }');

        $res =
        '<div id="'.$this->div_holder.'">'.
        'Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher. '.
        'You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.'.
        '</div>';

        $res .=
        '<script type="text/javascript">'.
        'YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.8.0r4/build/charts/assets/charts.swf";'.

        //--- data
        'YAHOO.example.DataSource = '.jsArray2D($this->data_source).';'.
        'var myDataSource = new YAHOO.util.DataSource( YAHOO.example.DataSource);'.
        'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'.

        'myDataSource.responseSchema = { fields: [ "'.$this->x_field_name.'",';
        for ($i=0; $i < count($this->y_fields); $i++)
            $res .= '"'.$this->y_fields[$i]['name'].'",';
        $res .= '] };';

        //--- chart
        $res .=
        'var seriesDef = ['.
        '{ displayName: "'.$this->x_field_title.'", yField: "'.$this->x_field_name.'" }, ';

        for ($i=0; $i < count($this->y_fields); $i++)
            $res .= '{ displayName: "'.$this->y_fields[$i]['title'].'", yField: "'.$this->y_fields[$i]['name'].'" }, ';

        $res .= ' ];';

        $res .=
        'YAHOO.example.getDataTipText = function( item, index, series )
        {
            var toolTipText = series.displayName + " at " + item.'.$this->x_field_name.';
            toolTipText += "\n" + item[series.yField];
            return toolTipText;
        };'.

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

        'var mychart = new YAHOO.widget.LineChart( "'.$this->div_holder.'", myDataSource, {'.
        'series: seriesDef,'.
        'xField: "'.$this->x_field_name.'",'.
        'yAxis: yAxisWidget,'.
        'xAxis: xAxisWidget,'.
        'style: styleDef,'.
        'dataTipFunction: YAHOO.example.getDataTipText,'.
        //only needed for flash player express install
        'expressInstall: "assets/expressinstall.swf"'.
        '});';

        $res .= '</script>';

        return $res;
    }
}

?>
