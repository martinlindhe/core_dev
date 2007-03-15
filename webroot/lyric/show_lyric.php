<?
	include('include_all.php');
	include('body_header.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('Bad id');

	$lyric_id = $_GET['id'];

	if (isset($_GET['highlight']) && $_GET['highlight']) {
		$highlight = urldecode($_GET['highlight']);
	}

	$lyric_data = getLyricData($db, $lyric_id);
	$lyric = $lyric_data['lyricText'];
	$lyric_name = $lyric_data['lyricName'];
	$band_name = $lyric_data['bandName'];

	echo '<title>'.$band_name.' - "'.$lyric_name.'" lyric</title>';

	echo '<table cellpadding=3 cellspacing=0 border=1 width="100%">';

	if (isModerated($db, $lyric_id, MODERATION_LYRIC) || isPendingChange($db, MODERATIONCHANGE_LYRIC, $lyric_id))
	{
		echo '<tr><td class="subtitlemod">';
	} else {
		echo '<tr><td class="subtitle">';
	}

	if (isset($highlight)) {
		$lyric = str_ireplace($highlight, '<font color=yellow>'.$highlight.'</font>', $lyric);
		$lyric_name = str_ireplace($highlight, '<font color=yellow>'.$highlight.'</font>', $lyric_name);
	}

	echo '<b>'.$band_name.' - '.$lyric_name.'</b></td><td width=30 align="right"><a href="edit_lyric.php?id='.$lyric_id.'">Edit</a></td></tr>';
	echo '<tr><td colspan=2>';

	echo nl2br($lyric);
	echo '</td></tr>';
	echo '</table>';
	echo '<br>';

	echo 'This song appears on the following records:<br>';

	$list = getLyricRecords($db, $lyric_id);
	for ($i=0; $i<count($list); $i++)
	{
		$record_name = dbStripSlashes($list[$i]['recordName']);
		$author_name = $list[$i]['bandName'];
		if (!$record_name) {
			$record_name = 's/t';
		}
		if ($author_name) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$author_name.'</a>';
		} else {
			echo 'Compilation';
		}
		echo ' - <a href="show_record.php?id='.$list[$i]['recordId'].'">'.$record_name.'</a>, track #'.$list[$i]['trackNumber'].'<br>';
	}
	echo '<br>';

	echo '<a href="show_band.php?id='.$lyric_data['bandId'].'">Back to '.$band_name.' page</a><br>';
	echo '<a href="index.php">Back to main</a><br>';

	include('body_footer.php');
?>