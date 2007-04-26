<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>x</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
table { border-collapse: collapse; }
body {
	background: #fff;
	color: #000;
	margin: 0px;
	height: 100%;
	width: 100%;
}
* { font-family: Arial, Helvetica, Sans-Serif; font-size: 11px; }
</style>
</head>

<body>
<div id="msgDiv" style="padding: 5px; overflow: hidden; width: 96%;"></div>
<script type="text/javascript">
	window.onload = function() {
		parent.getMSG(); 
		if(parent.window.opener) {
			//parent.window.opener.parent.comhead.getINFO();
		}
	}
	window.onfocus = function() { parent.textFocus(); }
</script>
</body>
</html>