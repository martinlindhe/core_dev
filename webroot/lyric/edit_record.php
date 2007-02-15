<?
	include("include_all.php");
	include("body_header.php");
	
	if (isset($_GET["id"]) && $_GET["id"] && is_numeric($_GET["id"]))
	{
		$record_id = $_GET["id"];
		$band_id = getBandIdFromRecordId($db, $record_id);
		
		if (isset($_POST["title"]))
		{
			//if ($_SESSION["userMode"] == 0) {
				addPendingChange($db, MODERATIONCHANGE_RECORDNAME, $record_id, $_POST["title"]);
				echo "Change added to moderation queue<br>";
			//} else {
				//updateRecord($db, $record_id, $_POST["title"]);
				//echo "Title changed.<br>";
			//}
		}

		if (isset($_POST["band"]) && $_POST["band"] && is_numeric($_POST["band"])) {
			if ($band_id != $_POST["band"]) {
				changeRecordOwner($db, $record_id, $_POST["band"]);
				echo "Band changed.<br>";
			}
		}

		$band_name = getBandName($db, $band_id);
		$record_name = getRecordName($db, $record_id);
	}
	else
	{
		echo "Bad id";
		die;
	}
	
	

	echo "If you added this record by mistake to the wrong band, you can set it to a different band here.<br><br>";
	echo "todo: kunna editera recordInfo<br>";

	echo "<form name=\"editrecord\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$record_id."\">";

	echo "<b>".$band_name." - </b><input type=\"text\" name=\"title\" size=50 value=\"".$record_name."\"><br>";
	
	echo "Change band: <select name=\"band\">";
	$list = getBands($db);
	for ($i=0; $i<count($list); $i++)
	{
		echo "<option value=".$list[$i]["bandId"];
		if ($band_id == $list[$i]["bandId"]) {
			echo " selected";
		}

		echo ">".$list[$i]["bandName"];
	}
	echo "</select><br>";
	
	
	echo "<input type=\"submit\" value=\"Update changes\" class=\"buttonstyle\">";
	echo "</form>";

	echo "<a href=\"show_band.php?id=".$band_id."\">Back to ".$band_name." page</a>";

	include("body_footer.php");
?>