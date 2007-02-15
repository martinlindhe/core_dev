<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen - Content code logfile</b><br><br>";

	$fp = @fopen(LOGFILE_CONTENTCODES, "r");
	if ($fp != false) {
		$buf="";
		do {
			$buf .= fread($fp, 2048);
		} while (!feof($fp));
		fclose($fp);
	
		echo nl2br($buf);
	}

	echo "<br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>