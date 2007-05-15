<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require('design_head.php');

	$band_id = $_GET['id'];
	$band_name = getBandName($band_id);

	echo '<table summary="" width="600" cellpadding="3" cellspacing="0" border="1"><tr>';
	echo '<td class="title">'.$band_name.'</td>';
	if ($session->id) echo '<td width="50"><a href="edit_band.php?id='.$band_id.'">Edit</a></td></tr>';
	echo '</table>';
	echo '<br/>';

	echo 'Albums:<br/>';
	$list = getBandRecords($band_id);
	echo '<table summary="" width="600" cellpadding="3" cellspacing="0" border="1">';
	foreach ($list as $row)
	{
		$record_name = stripslashes($row['recordName']);
		if (!$record_name) $record_name = 's/t';

		echo '<tr><td class="subtitle">';
		echo '<a href="show_record.php?id='.$row['recordId'].'">'.$record_name.'</a> ('.$row['cnt'].' tracks)';
		echo '</td></tr>';
	}
	if (!count($list)) {
		echo '<tr><td>None</td></tr>';
	}
	if ($session->id) echo '<tr><td><a href="add_record.php?band='.$band_id.'">Add record</a></td></tr>';
	echo '</table>';

	echo '<br/>';
	echo 'Compilations / splits:<br/>';
	$list = getBandCompilations($band_id);
	foreach ($list as $row)
	{
		$record_name = $db->escape($row['recordName']);
		if (!$record_name) $record_name = 's/t';

		echo '<a href="show_record.php?id='.$row['recordId'].'">'.$record_name.'</a> ('.$row['cnt'].' tracks)<br/>';
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

	if ($session->id) echo '<a href="add_lyric_single.php?band='.$band_id.'">Add a single lyric</a><br/><br/>';

	require('design_foot.php');
?>