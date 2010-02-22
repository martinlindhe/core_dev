<style type="text/css">
	#chart
	{
		width: 500px;
		height: 350px;
	}
</style>

<div id="chart">
	Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher.
	You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.
</div>


<script type="text/javascript">

YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.8.0r4/build/charts/assets/charts.swf";

//--- data

var myDataSource = new YAHOO.util.DataSource( YAHOO.example.DailyCalls );
myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
myDataSource.responseSchema =
{
	fields: [ "hours", "calls" ]
};

//--- chart
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
yAxisWidget.title = 'calls';

var xAxisWidget = new YAHOO.widget.CategoryAxis();
xAxisWidget.minimum = 0;
xAxisWidget.title = 'hours';

var mychart = new YAHOO.widget.LineChart( "chart", myDataSource,
{
	series: seriesDef,
	xField: "hours",
	yAxis: yAxisWidget,
	xAxis: xAxisWidget,
	style: styleDef,
	dataTipFunction: YAHOO.example.getDataTipText,
	//only needed for flash player express install
	expressInstall: "assets/expressinstall.swf"
});

</script>
