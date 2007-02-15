<?
	include("include_all.php");

	if (isset($_GET["id"]) && $_GET["id"] && is_numeric($_GET["id"]))
	{
		$record_id = $_GET["id"];
		
		addTrack($db, $record_id);
		
		header("Location: show_record.php?id=".$record_id);
	}
	
	die;
?>