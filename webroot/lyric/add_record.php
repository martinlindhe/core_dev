<?
	include ("include_all.php");
	include("body_header.php");

	if (isset($_GET["band"]) && $_GET["band"]) {
		$band_id = $_GET["band"];
	}

	if ($_SESSION["loggedIn"] && isset($_POST["band"]) && isset($_POST["recordname"]) && isset($_POST["info"]) && isset($_POST["tracks"]) && $_POST["band"] && $_POST["tracks"])
	{
		$band_id = $_POST["band"];
		$record_name = trim($_POST["recordname"]);
		$record_info = trim($_POST["info"]);
		$tracks = $_POST["tracks"];
		
		$record_id = addRecord($db, $_SESSION["userId"], $band_id, $record_name, $record_info);
		if (!$record_id)
		{
			echo "Problems adding record.<br>";
		}
		else
		{
			createTracks($db, $record_id, $tracks);
			
			echo "Record '".$record_name."' added.<br><br>";
			
			if ($_SESSION["userMode"] == 0) {
				/* Add recordId to moderation queue */
				addModerationItem($db, $record_id, MODERATION_RECORD);
				echo "Record added to moderation queue aswell.<br><br>";
			}

			echo "<a href=\"show_record.php?id=".$record_id."\">Click here to go to it now</a>.<br><br>";
		}
	}


	echo "<table width=400 cellpadding=0 cellspacing=0 border=0>";
	echo "<form name=\"addrecord\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	echo "<tr><td width=120>Band name:</td><td><select name=\"band\">";
	echo "<option>--- Select band ---\n";
	$list = getBands($db);
	for ($i=0; $i<count($list); $i++)
	{
		echo "<option value=".$list[$i]["bandId"];
		if (isset($band_id)) {
			if ($band_id == $list[$i]["bandId"]) {
				echo " selected";
			}
		}
		
		echo ">".$list[$i]["bandName"];
	}
	echo "</select></td></tr>";
	
	echo "<tr><td>Record name:</td><td><input type=\"text\" name=\"recordname\"> (leave empty for s/t)</td></tr>";
	echo "<tr><td>Number of tracks:</td><td><input type=\"text\" name=\"tracks\" value=\"1\"></td></tr>";
	echo "<tr><td valign=\"top\">Record info:<br>(optional)</td><td><textarea name=\"info\" cols=40 rows=8></textarea></td></tr>";
	echo "<tr><td colspan=2><input type=\"submit\" value=\"Add\" class=\"buttonstyle\"></td></tr>";
	echo "</form>";
	echo "</table>";
	
	if (isset($band_id)) {
		echo "<script language=\"JavaScript\">\n";
		echo "<!--\n";
		echo "document.addrecord.recordname.focus();\n";
		echo "//-->\n";
		echo "</script>";
	}
	
	include("body_footer.php");
?>