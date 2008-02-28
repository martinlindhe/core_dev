<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$record_id = $_GET['id'];

	require_once('config.php');

	$band_id = getBandIdFromRecordId($record_id);
	if ($band_id) {
		$band_name = getBandName($band_id);
	} else {
		$band_name = 'Compilation';
	}

	$record_name = getRecordName($record_id);

	$title = '"'.htmlspecialchars($band_name).' - '.htmlspecialchars($record_name).'" album overview';
	require('design_head.php');

	echo '<table cellpadding="3" cellspacing="0" border="1">';
	echo '<tr><td colspan="3" class="title">';
	if ($band_id) {
		echo '<a href="show_band.php?id='.$band_id.'">'.htmlspecialchars($band_name).'</a>';
	} else {
		echo $band_name;
	}
	echo ' - '.$record_name.'</td>';
	if ($session->id) echo '<td align="right"><a href="edit_record.php?id='.$record_id.'">Edit</a></td></tr>';

	$list = getRecordTracks($record_id);
	foreach ($list as $row)
	{
		$track = $row['trackNumber'];
		$lyric_id = $row['lyricId'];

		echo '<tr>';
		echo '<td width="25" align="right">'.$track.'</td>';

		if ($lyric_id)
		{
			echo '<td>';
			if ($band_id == 0) {
				/* Show the band name of current track if it's a split/compilation */
				echo '<a href="show_band.php?id='.$row['bandId'].'">'.$row['bandName'].'</a> - ';
			}

			echo '<a href="show_lyric.php?id='.$lyric_id.'">'.htmlspecialchars(stripslashes($row['lyricName'])).'</a>';

			if ($row['authorId'] != $row['bandId'])
			{
				echo ' (Cover by <a href="show_band.php?id='.$row['authorId'].'">'.getBandName($row['authorId']).'</a>)';
			}

			if (!$row['lyricText'])
			{
				echo ' (Missing)';
			} else if (strstr($row['lyricText'], '???')) {
				echo ' (Incomplete)';
			}
			echo '</td>';
			if ($session->id) {
				echo '<td><a href="edit_lyric.php?id='.$lyric_id.'">Edit</a></td>';
				echo '<td><a href="clear_track.php?record='.$record_id.'&amp;track='.$track.'">Clear</a></td>';
			}
		}
		else if ($session->id)
		{
			echo '<td colspan="3" bgcolor="#802040">';

			echo '<a href="add_lyric.php?record='.$record_id.'&amp;track='.$track.'">Add lyric</a> | ';
			echo '<a href="add_cover.php?record='.$record_id.'&amp;track='.$track.'">Add cover</a> | ';
			echo '<a href="link_with_existing_lyric.php?record='.$record_id.'&amp;track='.$track.'">Link to existing</a>';
			echo '</td>';
			
			echo '<td colspan="2" align="right">';
			echo '<a href="remove_track.php?record='.$record_id.'&amp;track='.$track.'">Remove</a>';
			echo '</td>';

		} else {
			echo '<td>&nbsp;</td>';
		}

		echo '</tr>';
	}
	echo '<tr><td colspan="4">';
	if ($session->id) {
		echo '<a href="add_track.php?id='.$record_id.'">Add track</a> | ';
		echo '<a href="import_tracks.php?id='.$record_id.'">Import tracklist</a> | ';
	}
	echo '<a href="show_record_lyrics.php?id='.$record_id.'">Show all lyrics</a>';
	echo '</td></tr>';

	echo '<tr><td colspan="4" bgcolor="#909090">'.nl2br(getRecordInfo($record_id)).'</td></tr>';

	echo '</table>';

	echo '<br/>';

	require('design_foot.php');
?>