<?
	include('include_all.php');
	include('body_header.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$band_id = $_GET['id'];
	$band_name = getBandName($db, $band_id);

	echo '<table width=600 cellpadding=3 cellspacing=0 border=1>';
	if (isModerated($db, $band_id, MODERATION_BAND)) {
		echo '<tr><td class="titlemod">'.$band_name.'</td></tr>';
	} else {
		echo '<tr><td class="title">'.$band_name.'</td></tr>';
	}
	echo '</table>';
	echo '<br>';

	echo 'Albums:<br>';
	$list = getBandRecords($db, $band_id);
	echo '<table width=600 cellpadding=3 cellspacing=0 border=1>';
	for ($i=0; $i<count($list); $i++)
	{
		$record_id = $list[$i]['recordId'];
		$record_name = dbStripSlashes($list[$i]['recordName']);
		if (!$record_name)
		{
			$record_name = 's/t';
		}
		if (isModerated($db, $record_id, MODERATION_RECORD) ||
			isPendingChange($db, MODERATIONCHANGE_RECORDNAME, $record_id)
			) {
			echo '<tr><td class="subtitlemod">';
		} else {
			echo '<tr><td class="subtitle">';
		}
		echo '<a href="show_record.php?id='.$record_id.'">'.$record_name.'</a> ('.getRecordTrackCount($db, $record_id).' tracks)';
		echo '</td></tr>';
	}
	if (!count($list)) {
		echo '<tr><td>None</td></tr>';
	}
	echo '<tr><td>';
	echo '<a href="add_record.php?band='.$band_id.'">Add record</a>';
	echo '</td></tr></table>';

	echo '<br>';
	echo 'Compilations / splits:<br>';
	$list = getBandCompilations($db, $band_id);
	for ($i=0; $i<count($list); $i++)
	{
		$record_id = $list[$i]['recordId'];
		$record_name = dbStripSlashes($list[$i]['recordName']);
		if (!$record_name)
		{
			$record_name = 's/t';
		}
		echo '<a href="show_record.php?id='.$record_id.'">'.$record_name.'</a> ('.getRecordTrackCount($db, $record_id).' tracks)<br>';
	}
	if (!count($list)) {
		echo 'None<br>';
	}
	echo '<br>';

	echo '<form>';

	$list = getLyricsThatBandCovers($db, $band_id);	
	if (count($list)) {
		echo 'This band covers the following songs:<br>';

		for ($i=0; $i<count($list); $i++) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'">'.dbStripSlashes($list[$i]['lyricName']).'</a>';
			echo ' (On <a href="show_record.php?id='.$list[$i]['recordId'].'">'.dbStripSlashes($list[$i]['recordName']).'</a>, track #'.$list[$i]['trackNumber'].')<br>';
		}
		echo '<br>';
	}

	$list = getLyricsThatOtherCovers($db, $band_id);
	if (count($list)) {
		echo 'The following songs have been covered by other bands:<br>';

		for ($i=0; $i<count($list); $i++) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'">'.$list[$i]['lyricName'].'</a>';
			echo ' (On <a href="show_record.php?id='.$list[$i]['recordId'].'">'.$list[$i]['recordName'].'</a>, track #'.$list[$i]['trackNumber'].')<br>';
		}
		echo '<br>';
	}

	$list = getBandLyrics($db, $band_id);
	echo 'Quickjump to lyric ('.count($list).' in total):<br>';
	echo '<select name="url" onChange="location.href=form.url.options[form.url.selectedIndex].value">';
	for ($i=0; $i<count($list); $i++)
	{
		echo '<option value="show_lyric.php?id='.$list[$i]['lyricId'].'">'.dbStripSlashes($list[$i]['lyricName']);
	}
	echo '</select> ';
	echo '<input type="submit" value="Go" class="buttonstyle" onClick="location.href=form.url.options[form.url.selectedIndex].value; return false;">';
	echo '</form>';
	
	echo '<a href="add_lyric_single.php?band='.$band_id.'">Add a single lyric</a><br><br>';

	echo '<a href="index.php">Back to main page</a><br>';

	include('body_footer.php');
?>