<?
	include("include_all.php");
	
	if (isset($_GET["record"]) && isset($_GET["track"]) && $_GET["record"] && $_GET["track"] && is_numeric($_GET["record"]) && is_numeric($_GET["track"]))
	{
		$record_id = $_GET["record"];
		$track = $_GET["track"];
		
		/* Will be 0 if this is a comp/split */
		$coverband_id = getBandIdFromRecordId($db, $record_id);

		if (isset($_GET["coverband"]) && $_GET["coverband"]) {
			$coverband_id = $_GET["coverband"];
		}
		
		if (isset($_GET["band"]) && $_GET["band"]) {
			$band_id = $_GET["band"];
		}
		
		
		if (isset($_POST["lyricid"]) && $_POST["lyricid"])
		{
			$lyric_id = $_POST["lyricid"];
			
			if (!linkLyric($db, $record_id, $track, $lyric_id, $coverband_id))
			{
				echo "Failed to add lyric link";
			}
			else
			{
				//if ($_SESSION["userMode"] == 0) {
					/* Add to pending changes queue */
					addPendingChange($db, MODERATIONCHANGE_LYRICLINK, $record_id, $track);
				//}

				header("Location: show_record.php?id=".$record_id);
				die;
			}
		}
		
	}
	else
	{
		echo "Bad id";
		die;
	}
	
	include("body_header.php");	

	if ($coverband_id == 0) {
		/* Bandet som gör covern */
		
		echo "1. Select the band who is doing the cover in the dropdown below.<br>";
		echo "(Adding cover to track <b>".$track."</b> on <b>".getRecordName($db, $record_id)."</b>).<br><br>";
		
		echo "<form>";
		echo "<select name=\"url\" OnChange=\"location.href=form.url.options[form.url.selectedIndex].value\">";
		echo "<option>--- Select band ---\n";
		$list = getBands($db);
		for ($i=0; $i<count($list); $i++)
		{
			echo "<option value=\"".$_SERVER["PHP_SELF"]."?record=".$record_id."&track=".$track."&coverband=".$list[$i]["bandId"]."\">".$list[$i]["bandName"]."\n";
		}
		echo "</select><br>";
		echo "</form>";
		
	} else if ($band_id == 0) {
		/* Bandet som gjort covern från början */

		echo "2. Select the band who did the orginal song that <b>".getBandName($db, $coverband_id)."</b> covers.<br>";
		echo "(Adding cover to track <b>".$track."</b> on <b>".getRecordName($db, $record_id)."</b>).<br><br>";

		echo "<form>";
		echo "<select name=\"url\" OnChange=\"location.href=form.url.options[form.url.selectedIndex].value\">";
		echo "<option>--- Select band ---\n";
		$list = getBands($db);
		for ($i=0; $i<count($list); $i++)
		{
			echo "<option value=\"".$_SERVER["PHP_SELF"]."?record=".$record_id."&track=".$track."&coverband=".$coverband_id."&band=".$list[$i]["bandId"]."\">".$list[$i]["bandName"]."\n";
		}
		echo "</select><br>";
		echo "</form>";
		
		echo "<a href=\"".$_SERVER["PHP_SELF"]."?record=".$record_id."&track=".$track."\">Back to step 1</a><br>";

	} else {

		echo "3. Select the original song by <b>".getBandName($db, $band_id)."</b> that <b>".getBandName($db, $coverband_id)."</b> covers from the dropdown below.<br>";
		echo "(Adding cover to track <b>".$track."</b> on <b>".getRecordName($db, $record_id)."</b>).<br><br>";

		$list = getBandLyrics($db, $band_id);
		echo "<form name=\"linklyric\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?record=".$record_id."&track=".$track."&coverband=".$coverband_id."\">";
		echo "<select name=\"lyricid\">\n";
		for ($i=0; $i<count($list); $i++)
		{
			echo "<option value=\"".$list[$i]["lyricId"]."\">".$list[$i]["lyricName"]."\n";
		}
		echo "</select><br>\n";
		echo "<input type=\"submit\" value=\"Save link\" class=\"buttonstyle\">";
		echo "</form>";

		echo "<a href=\"".$_SERVER["PHP_SELF"]."?record=".$record_id."&track=".$track."&coverband=".$coverband_id."\">Back to step 2</a><br>";
	}
	
	echo "<a href=\"show_record.php?id=".$record_id."\">Back to record overview</a>";
	
	include("body_footer.php");
?>