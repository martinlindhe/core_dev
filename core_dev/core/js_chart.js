<script type="text/javascript">

YAHOO.widget.Chart.SWFURL = "<?=$config['core']['web_root']?>js/yui/charts/assets/charts.swf";

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

YAHOO.example.formatCurrencyAxisLabel = function( value )
{
	return YAHOO.util.Number.format( value,
	{
		prefix: "",
		thousandsSeparator: ",",
		decimalPlaces: 0
	});
}

YAHOO.example.getDataTipText = function( item, index, series )
{
	var toolTipText = series.displayName + " starting at " + item.hours;
	toolTipText += "\n" + YAHOO.example.formatCurrencyAxisLabel( item[series.yField] );
	return toolTipText;
}

var currencyAxis = new YAHOO.widget.NumericAxis();
currencyAxis.minimum = 0;
currencyAxis.labelFunction = YAHOO.example.formatCurrencyAxisLabel;

var mychart = new YAHOO.widget.LineChart( "chart", myDataSource,
{
	series: seriesDef,
	xField: "hours",
	yAxis: currencyAxis,
	dataTipFunction: YAHOO.example.getDataTipText,
	//only needed for flash player express install
	expressInstall: "assets/expressinstall.swf"
});

</script>
