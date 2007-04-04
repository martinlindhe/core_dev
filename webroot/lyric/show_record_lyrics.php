<?
	include('include_all.php');
	include('body_header.php');
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('Bad id');

	$record_id = $_GET['id'];
		
	$band_id = getBandIdFromRecordId($db, $record_id);

	echo '<a name="top"></a>';
	if ($band_id == 0) {
		echo '<b>V/A - '.getRecordName($db, $record_id).'</b>';
	} else {
		echo '<b>'.getBandName($db, $band_id).' - '.getRecordName($db, $record_id).'</b>';
	}
	echo '<br/><br/>';

	$list = getRecordTracks($db, $record_id);
	
	/* First list track titles */
	for ($i=0; $i<count($list); $i++)
	{
		$track = $list[$i]['trackNumber'];
		$lyric_id = $list[$i]['lyricId'];

		if ($band_id == 0) {
			echo '<b>'.$track.'. '.$list[$i]['bandName'] .' - '.$list[$i]['lyricName'].'</b>';
		} else {
			echo '<b><a href="#'.$i.'">'.$track.'. '.$list[$i]['lyricName'].'</a></b>';
		}
		
		if ($list[$i]['authorId'] != $list[$i]['bandId']) {
			echo ' (Cover by <a href="show_band.php?id='.$list[$i]['authorId'].'">'.getBandName($db, $list[$i]['authorId']).'</a>)';
		}
		echo '<br/>';
	}
	echo '<br/><br/><br/>';
	
	/* Then list the lyrics */
	for ($i=0; $i<count($list); $i++)
	{
		echo '<a name="'.$i.'"></a>';
		$track = $list[$i]['trackNumber'];
		$lyric_id = $list[$i]['lyricId'];

		if ($band_id == 0) {
			echo '<b>'.$track.'. '.$list[$i]['bandName'] .' - '.dbStripSlashes($list[$i]['lyricName']).'</b>';
		} else {
			echo '<b>'.$track.'. '.dbStripSlashes($list[$i]['lyricName']).'</b>';
		}

		if ($list[$i]['authorId'] != $list[$i]['bandId']) {
			echo ' (Cover by <a href="show_band.php?id='.$list[$i]['authorId'].'">'.getBandName($db, $list[$i]['authorId']).'</a>)';
		}
		echo ' <a href="edit_lyric.php?id='.$lyric_id.'">Edit</a><br/>';
		
		$lyric = dbStripSlashes($list[$i]['lyricText']);
		if ($lyric)
		{
			$lyric = str_replace('&amp;', '&', $lyric);
			$lyric = str_replace('&', '&amp;', $lyric);
			echo nl2br($lyric);

		} else {
			echo 'Lyric missing.';
		}
		echo '<br/>';
		echo '<a href="#top">To top</a><br/>';
		echo '<br/><br/><br/><br/>';
	}

	echo '<br/>';
	echo '<a href="show_band.php?id='.$band_id.'">Back to '.getBandName($db, $band_id).' page</a><br/>';

	include('body_footer.php');
?>