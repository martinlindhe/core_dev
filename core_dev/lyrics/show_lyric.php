<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$lyric_id = $_GET['id'];

	if (isset($_GET['highlight']) && $_GET['highlight']) {
		$highlight = urldecode($_GET['highlight']);
	}

	$lyric_data = getLyricData($lyric_id);
	if (!$lyric_data) die;

	$lyric = $lyric_data['lyricText'];
	$lyric_name = $lyric_data['lyricName'];
	$band_name = $lyric_data['bandName'];

	$title = htmlspecialchars($band_name).' - "'.htmlspecialchars($lyric_name).'" lyric';
	require('design_head.php');

	echo '<table summary="" cellpadding="3" cellspacing="0" border="1" width="100%">';
	echo '<tr><td class="subtitle">';

	$lyric = nl2br(htmlspecialchars($lyric));

	if (isset($highlight)) {
		$lyric = str_ireplace($highlight, '<font color="yellow">'.$highlight.'</font>', $lyric);
		$lyric_name = str_ireplace($highlight, '<font color="yellow">'.$highlight.'</font>', $lyric_name);
	}

	echo '<b><a href="show_band.php?id='.$lyric_data['bandId'].'">'.htmlspecialchars($band_name).'</a>';

	echo ' - '.$lyric_name.'</b></td>';
	if ($session->id) echo '<td width="30" align="right"><a href="edit_lyric.php?id='.$lyric_id.'">Edit</a></td></tr>';
	echo '<tr><td colspan="2">';

	echo $lyric;
	echo '</td></tr>';
	echo '</table>';
	echo '<br/>';

	$list = getLyricRecords($lyric_id);

	if (count($list)) {
		echo 'This song appars on the following records:<br/>';

		for ($i=0; $i<count($list); $i++)
		{
			$record_name = stripslashes($list[$i]['recordName']);
			$author_name = $list[$i]['bandName'];
			if (!$record_name) $record_name = 's/t';

			if ($author_name) {
				echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.htmlspecialchars($author_name).'</a>';
			} else {
				echo 'Compilation';
			}
			echo ' - <a href="show_record.php?id='.$list[$i]['recordId'].'">'.htmlspecialchars($record_name).'</a>, track #'.$list[$i]['trackNumber'].'<br/>';
		}
		echo '<br/>';
	}

	require('design_foot.php');
?>