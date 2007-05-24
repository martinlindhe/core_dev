<?
	require_once('config.php');
	require('design_head.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$record_id = $_GET['id'];

	$band_id = getBandIdFromRecordId($record_id);

	echo '<a name="top"></a>';
	if ($band_id == 0) {
		echo '<b>V/A - '.getRecordName($record_id).'</b>';
	} else {
		echo '<b><a href="show_band.php?id='.$band_id.'">'.htmlspecialchars(getBandName($band_id)).'</a>';
		echo ' - '.getRecordName($record_id).'</b>';
	}
	echo '<br/><br/>';

	$list = getRecordTracks($record_id);

	/* First list track titles */
	foreach ($list as $row)
	{
		if ($band_id == 0) {
			echo '<b>'.$row['trackNumber'].'. '.htmlspecialchars($row['bandName']) .' - '.htmlspecialchars($row['lyricName']).'</b>';
		} else {
			echo '<b><a href="#lyric_'.$row['trackNumber'].'">'.$row['trackNumber'].'. '.stripslashes($row['lyricName']).'</a></b>';
		}

		if ($row['authorId'] != $row['bandId']) {
			echo ' (Cover by <a href="show_band.php?id='.$row['authorId'].'">'.getBandName($row['authorId']).'</a>)';
		}
		echo '<br/>';
	}
	echo '<br/><br/><br/>';

	/* Then list the lyrics */
	foreach ($list as $row)
	{
		echo '<a name="lyric_'.$row['trackNumber'].'"></a><br/><b>'.$row['trackNumber'].'. ';

		if ($band_id == 0) {
			echo $row['bandName'] .' - '.stripslashes($row['lyricName']).'</b>';
		} else {
			echo stripslashes($row['lyricName']).'</b>';
		}

		if ($row['authorId'] != $row['bandId']) {
			echo ' (Cover by <a href="show_band.php?id='.$row['authorId'].'">'.getBandName($row['authorId']).'</a>)';
		}
		echo '<br/>';
		if ($session->id) echo '<a href="edit_lyric.php?id='.$row['lyricId'].'">Edit</a><br/>';

		$lyric = stripslashes($row['lyricText']);
		if ($lyric) {
			$lyric = str_replace('&amp;', '&', $lyric);
			$lyric = str_replace('&', '&amp;', $lyric);
			echo nl2br($lyric);

		} else {
			echo 'Lyric missing.';
		}
		echo '<br/>';
		echo '<a href="#top">To top</a><br/>';
		echo '<br/><br/><br/>';
	}

	require('design_foot.php');
?>