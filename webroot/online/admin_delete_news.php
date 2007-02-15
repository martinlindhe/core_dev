<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	if (isset($_GET["id"])) {
		deleteNews($db, $_GET["id"]);
	}
	
	header("Location: index.php");
?>