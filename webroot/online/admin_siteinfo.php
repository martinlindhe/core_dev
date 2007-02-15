<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }	

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen - Site information</b><br><br>";

	echo $_SERVER["SERVER_SOFTWARE"]." at ".$_SERVER["SERVER_ADDR"].":".$_SERVER["SERVER_PORT"]."<br>";
	echo "MySQL ".mysql_get_server_info()." at ".$config['database_1']['server']."<br>";
	echo "<br>";

	echo "<a href=\"admin_phpinfo.php\">&raquo; PHP-info</a><br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>