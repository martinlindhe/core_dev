<?
	require_once('config.php');
	require('design_head.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$band_id = $_GET['id'];
	$band_name = getBandName($band_id);

	echo '<table width="600" cellpadding="3" cellspacing="0" border="1"><tr>';
	if (isModerated($band_id, MODERATION_BAND)) {
		echo '<td class="titlemod">'.$band_name.'</td>';
	} else {
		echo '<td class="title">'.$band_name.'</td>';
	}
	echo '<td width=50><a href="edit_band.php?id='.$band_id.'">Edit</a></td></tr>';
	echo '</table>';
	echo '<br/>';

	echo 'Albums:<br/>';
	$list = getBandRecords($band_id);
	echo '<table width="600" cellpadding="3" cellspacing="0" border="1">';
	for ($i=0; $i<count($list); $i++)
	{
		$record_id = $list[$i]['recordId'];
		$record_name = stripslashes($list[$i]['recordName']);
		if (!$record_name) $record_name = 's/t';

		if (isModerated($record_id, MODERATION_RECORD) ||
			isPendingChange(MODERATIONCHANGE_RECORDNAME, $record_id)
			) {
			echo '<tr><td class="subtitlemod">';
		} else {
			echo '<tr><td class="subtitle">';
		}
		echo '<a href="show_record.php?id='.$record_id.'">'.$record_name.'</a> ('.getRecordTrackCount($record_id).' tracks)';
		echo '</td></tr>';
	}
	if (!count($list)) {
		echo '<tr><td>None</td></tr>';
	}
	echo '<tr><td>';
	echo '<a href="add_record.php?band='.$band_id.'">Add record</a>';
	echo '</td></tr></table>';

	echo '<br/>';
	echo 'Compilations / splits:<br/>';
	$list = getBandCompilations($band_id);
	for ($i=0; $i<count($list); $i++)
	{
		$record_id = $list[$i]['recordId'];
		$record_name = $db->escape($list[$i]['recordName']);
		if (!$record_name) $record_name = 's/t';

		echo '<a href="show_record.php?id='.$record_id.'">'.$record_name.'</a> ('.getRecordTrackCount($record_id).' tracks)<br/>';
	}
	if (!count($list)) echo 'None<br/>';
	echo '<br/>';

	$list = getLyricsThatBandCovers($band_id);	
	if (count($list)) {
		echo 'This band covers the following songs:<br/>';

		for ($i=0; $i<count($list); $i++) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'">'.stripslashes($list[$i]['lyricName']).'</a>';
			echo ' (On <a href="show_record.php?id='.$list[$i]['recordId'].'">'.stripslashes($list[$i]['recordName']).'</a>, track #'.$list[$i]['trackNumber'].')<br/>';
		}
		echo '<br/>';
	}

	$list = getLyricsThatOtherCovers($band_id);
	if (count($list)) {
		echo 'The following songs have been covered by other bands:<br/>';

		for ($i=0; $i<count($list); $i++) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'">'.$list[$i]['lyricName'].'</a>';
			echo ' (On <a href="show_record.php?id='.$list[$i]['recordId'].'">'.$list[$i]['recordName'].'</a>, track #'.$list[$i]['trackNumber'].')<br/>';
		}
		echo '<br/>';
	}

	$list = getBandLyrics( $band_id);
	if ($list) {
		echo '<form action="">';
		echo 'Quickjump to lyric ('.count($list).' in total):<br/>';
		echo '<select name="url" onchange="location.href=form.url.options[form.url.selectedIndex].value">';
		for ($i=0; $i<count($list); $i++)
		{
			echo '<option value="show_lyric.php?id='.$list[$i]['lyricId'].'">'.stripslashes($list[$i]['lyricName']).'</option>';
		}
		echo '</select> ';
		echo '<input type="submit" value="Go" class="button" onclick="location.href=form.url.options[form.url.selectedIndex].value; return false;"/>';
		echo '</form>';
	}
	
	echo '<a href="add_lyric_single.php?band='.$band_id.'">Add a single lyric</a><br/><br/>';

	require('design_foot.php');
?>