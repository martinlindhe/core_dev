<?
	//Adds a lyric associated with a existing band.

	include('include_all.php');

	if (empty($_GET['band']) || !is_numeric($_GET['band'])) die('No id');

	$band_id = $_GET['band'];

	if (!empty($_POST['songname']) && isset($_POST['lyrictext']))
	{
		$song_name = $_POST['songname'];
		$lyric_text = $_POST['lyrictext'];

		$lyric_id = addLyric($db, $_SESSION['userId'], $band_id, 0, 0, $song_name, $lyric_text);
		if (!$lyric_id) die('Problems adding lyric');

		if ($_SESSION['userMode'] == 0) {
			/* Add lyricId to moderation queue */
			addModerationItem($db, $lyric_id, MODERATION_LYRIC);
		}
		header('Location: show_record.php?id='.$record_id);
		die;
	}

	include('design_head.php');

	echo 'Add new single lyric to the band <b>'.getBandName($db, $band_id).'</b>:<br/>';
	echo '<br/>';

	echo '<form name="addlyric" method="post" action="'.$_SERVER['PHP_SELF'].'?band='.$band_id.'">';
	echo 'Song name: <input type="text" name="songname" size="40"/><br/>';
	echo 'Lyric:<br/>';
	echo '<textarea name="lyrictext" cols="60" rows="30"></textarea><br/>';
	echo '<input type="submit" value="Add" class="buttonstyle"/>';
	echo '</form>';

	echo '<script type="text/javascript">';
	echo 'document.addlyric.songname.focus();';
	echo '</script>';

	include('design_foot.php');
?>