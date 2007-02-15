<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	//all except INFO_CREDITS, INFO_LICENCE
	phpinfo( INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_ENVIRONMENT | INFO_VARIABLES );

	include("design_foot.php");
?>