<?
	//Adds a lyric associated with a existing band.

	require_once('config.php');

	if (empty($_GET['band']) || !is_numeric($_GET['band'])) die('No id');

	$band_id = $_GET['band'];

	if (!empty($_POST['songname']) && isset($_POST['lyrictext']))
	{
		$song_name = $_POST['songname'];
		$lyric_text = $_POST['lyrictext'];

		$lyric_id = addLyric($band_id, 0, 0, $song_name, $lyric_text);
		if (!$lyric_id) die('Problems adding lyric');

		if (!$session->isAdmin) {
			/* Add lyricId to moderation queue */
			addModerationItem($lyric_id, MODERATION_LYRIC);
		}
		header('Location: show_record.php?id='.$record_id);
		die;
	}

	require('design_head.php');

	echo 'Add new single lyric to the band <b>'.getBandName($band_id).'</b>:<br/>';
	echo '<br/>';

	echo '<form name="addlyric" method="post" action="'.$_SERVER['PHP_SELF'].'?band='.$band_id.'">';
	echo 'Song name: <input type="text" name="songname" size="40"/><br/>';
	echo 'Lyric:<br/>';
	echo '<textarea name="lyrictext" cols="60" rows="30"></textarea><br/>';
	echo '<input type="submit" value="Add" class="button"/>';
	echo '</form>';

	echo '<script type="text/javascript">';
	echo 'document.addlyric.songname.focus();';
	echo '</script>';

	require('design_foot.php');
?>