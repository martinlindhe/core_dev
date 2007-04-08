<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('Bad id');

	$record_id = $_GET['id'];

	$band_id = getBandIdFromRecordId($record_id);
	if ($band_id) {
		$band_name = getBandName($band_id);
	} else {
		$band_name = 'Compilation';
	}

	$record_name = getRecordName($record_id);

	$title = 'inthc.net: "'.$band_name.' - '.$record_name.'" album overview';
	require('design_head.php');

	echo '<table cellpadding="3" cellspacing="0" border="1">';
	
	if (isModerated($record_id, MODERATION_RECORD) ||
		isPendingChange(MODERATIONCHANGE_RECORDNAME, $record_id)
		) {
		echo '<tr><td colspan="3" class="titlemod">';
	} else {
		echo '<tr><td colspan="3" class="title">';
	}
	echo $band_name.' - '.$record_name.'</td><td align="right"><a href="edit_record.php?id='.$record_id.'">Edit</a></td></tr>';

	$list = getRecordTracks($record_id);
	for ($i=0; $i<count($list); $i++)
	{
		$track = $list[$i]['trackNumber'];
		$lyric_id = $list[$i]['lyricId'];

		echo '<tr>';
		echo '<td width="25" align="right">'.$track.'</td>';

		if ($lyric_id)
		{
			if (isModerated($lyric_id, MODERATION_LYRIC) ||
				isPendingChange(MODERATIONCHANGE_LYRICLINK, $record_id, $track) ||
				isPendingChange(MODERATIONCHANGE_LYRIC, $lyric_id)
				) {
				echo '<td class="subtitlemod">';
			} else {
				echo '<td class="subtitle">';
			}
			if ($band_id == 0) {
				/* Show the band name of current track if it's a split/compilation */
				echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			}
			
			echo '<a href="show_lyric.php?id='.$lyric_id.'">'.stripslashes($list[$i]['lyricName']).'</a>';
			
			if ($list[$i]['authorId'] != $list[$i]['bandId'])
			{
				echo ' (Cover by <a href="show_band.php?id='.$list[$i]['authorId'].'">'.getBandName($list[$i]['authorId']).'</a>)';
			}
			
			if (!$list[$i]['lyricText'])
			{
				echo ' (Missing)';
			} else if (strstr($list[$i]['lyricText'], '???')) {
				echo ' (Incomplete)';
			}
			echo '</td>';
			echo '<td><a href="edit_lyric.php?id='.$lyric_id.'">Edit</a></td>';
			echo '<td><a href="clear_track.php?record='.$record_id.'&amp;track='.$track.'">Clear</a></td>';
		}
		else
		{
			if ($i == count($list)-1) {
				echo '<td bgcolor="#802040">';
			} else {
				echo '<td colspan="3" bgcolor="#802040">';
			}
			echo '<a href="add_lyric.php?record='.$record_id.'&amp;track='.$track.'">Add lyric</a> | ';
			echo '<a href="add_cover.php?record='.$record_id.'&amp;track='.$track.'">Add cover</a> | ';
			echo '<a href="link_with_existing_lyric.php?record='.$record_id.'&amp;track='.$track.'">Link to existing</a>';
			echo '</td>';
			if ($i == count($list)-1) {
				echo '<td colspan="2" align="right">';
				echo '<a href="remove_track.php?record='.$record_id.'&amp;track='.$track.'">Remove</a>';
				echo '</td>';
			}
		}

		echo '</tr>';
	}
	echo '<tr><td colspan="4"><a href="add_track.php?id='.$record_id.'">Add track</a> | <a href="import_tracks.php?id='.$record_id.'">Import tracklist</a> | <a href="show_record_lyrics.php?id='.$record_id.'">Show all lyrics</a></td></tr>';
	echo '<tr><td colspan="4" bgcolor="#909090">'.nl2br(getRecordInfo($record_id)).'</td></tr>';
	echo '</table>';
	
	echo '<br/>';
	if ($band_id) {
		echo '<a href="show_band.php?id='.$band_id.'">Back to '.$band_name.' page</a><br/>';
	}
	echo '<a href="index.php">Back to main page</a><br/>';
	
	require('design_foot.php');
?>