<?
	include("include_all.php");
	
	if (isset($_GET["record"]) && isset($_GET["track"]) && $_GET["record"] && $_GET["track"] && is_numeric($_GET["record"]) && is_numeric($_GET["track"]))
	{
		$record_id = $_GET["record"];
		$track = $_GET["track"];

		if (isset($_GET["band"]) && $_GET["band"]) {
			$band_id = $_GET["band"];
		} else {
			$band_id = getBandIdFromRecordId($db, $record_id);
		}
		
		if (isset($_POST["songname"]) && isset($_POST["lyrictext"]) && $_POST["songname"])
		{
			$song_name = $_POST["songname"];
			$lyric_text = $_POST["lyrictext"];
			
			$lyric_id = addLyric($db, $_SESSION["userId"], $band_id, $record_id, $track, $song_name, $lyric_text);
			if (!$lyric_id)
			{
				echo "Problems adding lyric";
				die;
			}
			else
			{
				if ($_SESSION["userMode"] == 0) {
					/* Add lyricId to moderation queue */
					addModerationItem($db, $lyric_id, MODERATION_LYRIC);
				}
				
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

	if ($band_id == 0) {
		/* Skivan vi ska länka en text på är en split/compilation */
		
		echo "Since this is a comp/split you first need to select a band.<br>";
		echo "Then you'll be able to select a lyric from that band to link to this track.<br>";
		
		echo "<form name=\"linkband\">";
		echo "<select name=\"url\" OnChange=\"location.href=form.url.options[form.url.selectedIndex].value\">";
		echo "<option>--- Select band ---\n";
		$list = getBands($db);
		for ($i=0; $i<count($list); $i++)
		{
			echo "<option value=\"add_lyric.php?record=".$record_id."&track=".$track."&band=".$list[$i]["bandId"]."\">".$list[$i]["bandName"]."\n";
		}
		echo "</select><br>";
		echo "</form>";
		
		echo "<script language=\"JavaScript\">\n";
		echo "<!--\n";
		echo "document.linkband.url.focus();\n";
		echo "//-->\n";
		echo "</script>";

	} else {

		echo "<b>".getBandName($db, $band_id)." - ".getRecordName($db, $record_id)."</b><br>";
		echo "Type lyric for track <b>".$track."</b> below.<br>";
		echo "<br>";

		echo "<form name=\"addlyric\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?record=".$record_id."&track=".$track."&band=".$band_id."\">";
		echo "Song name: <input type=\"text\" name=\"songname\" size=40><br>";
		echo "Lyric:<br>";
		echo "<textarea name=\"lyrictext\" cols=60 rows=30></textarea><br>";
		echo "<input type=\"submit\" value=\"Add\" class=\"buttonstyle\">";
		echo "</form>";

		echo "<script language=\"JavaScript\">\n";
		echo "<!--\n";
		echo "document.addlyric.songname.focus();\n";
		echo "//-->\n";
		echo "</script>";

	}

	include("body_footer.php");
?>