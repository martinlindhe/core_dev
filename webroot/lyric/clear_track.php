<?
	include("include_all.php");
	
	if (isset($_GET["record"]) && isset($_GET["track"]) && $_GET["record"] && $_GET["track"] && is_numeric($_GET["record"]) && is_numeric($_GET["track"]))
	{
		clearTrack($db, $_GET["record"], $_GET["track"]);
		
		header("Location: show_record.php?id=".$_GET["record"]);
		die;
	}
	else
	{
		echo "Bad id";
		die;
	}

?>