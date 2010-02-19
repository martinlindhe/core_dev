<?php
/**
 *
 */

//STATUS: WIP

//ersätter js/yui_chart.js - uppdatera SAVAK och skrota yui_charts.js när de e klart


class yui_chart
{
	private $data_sources = array();

	function addDataSource($name)
	{
		$this->data_sources[] = $name;
	}

	function render()
	{

		echo '
		<style type="text/css">
		#chart {
			width: 500px;
			height: 350px;
		}
		</style>

		<div id="chart">
		Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher.
		You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.
		</div>';


		echo '<script type="text/javascript">

		YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.8.0r4/build/charts/assets/charts.swf";
		';

		//--- data
		for ($i = 0; $i < count($this->data_sources); $i++) {
			echo 'var myDataSource'.$i.' = new YAHOO.util.DataSource( YAHOO.example.'.$this->data_sources[$i].' );'."\n";
			echo 'myDataSource'.$i.'.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;'."\n";
			echo 'myDataSource'.$i.'.responseSchema = { fields: [ "hours", "calls" ] };'."\n";
		}

		//--- chart

		echo '
		var seriesDef =
		[
			{ displayName: "Calls", yField: "calls" }
		];

		YAHOO.example.getDataTipText = function( item, index, series )
		{
			var toolTipText = series.displayName + " starting at " + item.hours;
			toolTipText += "\n" + item[series.yField];
			return toolTipText;
		}

		//Style object for chart
		var styleDef =
		{
			xAxis:
			{
				labelRotation:-90
			},
			yAxis:
			{
				titleRotation:-90
			}
		}


		var yAxisWidget = new YAHOO.widget.NumericAxis();
		yAxisWidget.minimum = 0;
		yAxisWidget.title = "calls";

		var xAxisWidget = new YAHOO.widget.CategoryAxis();
		xAxisWidget.minimum = 0;
		xAxisWidget.title = "hours";'
		;

		for ($i = 0; $i < count($this->data_sources); $i++) {
			echo 'var mychart = new YAHOO.widget.LineChart( "chart", myDataSource'.$i.', {'."\n";
			echo 'series: seriesDef,'."\n";
			echo 'xField: "hours",'."\n";
			echo 'yAxis: yAxisWidget,'."\n";
			echo 'xAxis: xAxisWidget,'."\n";
			echo 'style: styleDef,'."\n";
			echo 'dataTipFunction: YAHOO.example.getDataTipText,'."\n";
			echo 'expressInstall: "assets/expressinstall.swf"'."\n";			//only needed for flash player express install
			echo '});'."\n";
		}

		echo '</script>';

	}
}

?>
