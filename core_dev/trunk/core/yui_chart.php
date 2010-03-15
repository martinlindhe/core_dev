<?php
/**
 *
 */

//STATUS: WIP

//ersätter js/yui_chart.js - uppdatera SAVAK och skrota yui_charts.js när de e klart


class yui_chart
{
	private $data_source = '';
	private $fields = array();

	function setDataSource($arr) { $this->data_source = $arr; }
	function addField($name, $title) { $this->fields[] = array('name'=>$name, 'title'=>$title); }

	function render()
	{
		echo '<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo/yahoo-min.js&2.8.0r4/build/dom/dom-min.js&2.8.0r4/build/event/event-min.js&2.8.0r4/build/element/element-min.js&2.8.0r4/build/json/json-min.js&2.8.0r4/build/datasource/datasource-min.js&2.8.0r4/build/swf/swf-min.js&2.8.0r4/build/charts/charts-min.js"></script>';

		echo
		'<style type="text/css">'.
		'#chart { width: 500px; height: 350px; }'.
		'</style>';

		echo
		'<div id="chart">'.
		'Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher. '.
		'You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.'.
		'</div>';

		echo
		'<script type="text/javascript">'.
		'YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.8.0r4/build/charts/assets/charts.swf";';

		//--- data
		echo jsArray('YAHOO.example.DataSource', $this->data_source);
		echo 'var myDataSource = new YAHOO.util.DataSource( YAHOO.example.DataSource);'."\n";

		echo 'myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'."\n";
		echo 'myDataSource.responseSchema = { fields: [ ';
		for ($i=0; $i < count($this->fields); $i++)
			echo '"'.$this->fields[$i]['name'].'", ';
		echo '] };'."\n";

		//--- chart
		echo 'var seriesDef = [';
		for ($i=0; $i < count($this->fields); $i++)
			echo '{ displayName: "'.$this->fields[$i]['title'].'", yField: "'.$this->fields[$i]['name'].'" }, ';
		echo ' ];';

		echo
		'YAHOO.example.getDataTipText = function( item, index, series )
		{
			var toolTipText = series.displayName + " starting at " + item.hours;
			toolTipText += "\n" + item[series.yField];
			return toolTipText;
		}'."\n";

		//Style object for chart
		echo
		'var styleDef ='.
		'{'.
			'xAxis: { labelRotation:-90 },'.
			'yAxis: { titleRotation:-90 }'.
		'}'."\n";

		echo
		'var yAxisWidget = new YAHOO.widget.NumericAxis();'.
		'yAxisWidget.minimum = 0;'.
		'yAxisWidget.title = "calls";';

		echo
		'var xAxisWidget = new YAHOO.widget.CategoryAxis();'.
		'xAxisWidget.minimum = 0;'.
		'xAxisWidget.title = "hours";';

		echo 'var mychart = new YAHOO.widget.LineChart( "chart", myDataSource, {';
		echo 'series: seriesDef,';
		echo 'xField: "'.$this->fields[0]['name'].'",';
		echo 'yAxis: yAxisWidget,';
		echo 'xAxis: xAxisWidget,';
		echo 'style: styleDef,';
		echo 'dataTipFunction: YAHOO.example.getDataTipText,';
		//only needed for flash player express install
		echo 'expressInstall: "assets/expressinstall.swf"';
		echo '});'."\n";

		echo '</script>';
	}
}

?>
