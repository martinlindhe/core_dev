<?
	require('top_old.php');
?>
</head>
<body>
<div id="pd" style="display: none;"></div>
<div id="shell">
	<div id="wrapper">
		<?=defined('WILD')?'<div id="designWild"></div>':''?>
		<div id="top"></div>
		<script type="text/javascript">
		// <![CDATA[
			var so = new SWFObject("/member/menu/menu.swf?<?=@$_SESSION['data']['cache']?>&L=<?=(@$l?'1':'0')?>", "flash", "980", "141", "7", "#ffffff");
			so.addParam("quality", "high");
//		   	so.addParam("wmode", "transparent");
			so.write("top");
		// ]]>
		</script>
		<div id="contentMain">
