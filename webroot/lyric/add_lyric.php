<?
	require_once('config.php');

	if (empty($_GET['record']) || !is_numeric($_GET['record']) || empty($_GET['track']) || !is_numeric($_GET['track'])) die;

	$record_id = $_GET['record'];
	$track = $_GET['track'];

	if (isset($_GET['band']) && $_GET['band']) {
		$band_id = $_GET['band'];
	} else {
		$band_id = getBandIdFromRecordId($record_id);
	}

	if (isset($_POST['songname']) && isset($_POST['lyrictext']) && $_POST['songname'])
	{
		$song_name = $_POST['songname'];
		$lyric_text = $_POST['lyrictext'];

		$lyric_id = addLyric($band_id, $record_id, $track, $song_name, $lyric_text);
		if (!$lyric_id) die('Problems adding lyric');

		if (!$session->isAdmin) {
			/* Add lyricId to moderation queue */
			addModerationItem($lyric_id, MODERATION_LYRIC);
		}
		header('Location: show_record.php?id='.$record_id);
		die;
	}

	require('design_head.php');

	if ($band_id == 0) {
		/* Skivan vi ska länka en text på är en split/compilation */

		echo 'Since this is a comp/split you first need to select a band.<br/>';
		echo 'Then you\'ll be able to select a lyric from that band to link to this track.<br/>';

		echo '<form name="linkband">';
		echo '<select name="url" onchange="location.href=form.url.options[form.url.selectedIndex].value">';
		echo '<option>--- Select band ---</option>';
		$list = getBands();
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="add_lyric.php?record='.$record_id.'&amp;track='.$track.'&amp;band='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</option>';
		}
		echo '</select><br/>';
		echo '</form>';

		echo '<script type="text/javascript">';
		echo 'document.linkband.url.focus();';
		echo '</script>';

	} else {

		echo '<b>'.getBandName($band_id).' - '.getRecordName($record_id).'</b><br/>';
		echo 'Type lyric for track <b>'.$track.'</b> below.<br/>';
		echo '<br/>';

		echo '<form name="addlyric" method="post" action="'.$_SERVER['PHP_SELF'].'?record='.$record_id.'&amp;track='.$track.'&amp;band='.$band_id.'">';
		echo 'Song name: <input type="text" name="songname" size="40"/><br/>';
		echo 'Lyric:<br/>';
		echo '<textarea name="lyrictext" cols="60" rows="25"></textarea><br/>';
		echo '<input type="submit" value="Add" class="button"/>';
		echo '</form>';

		echo '<script type="text/javascript">';
		echo 'document.addlyric.songname.focus();';
		echo '</script>';
	}

	require('design_foot.php');
?>