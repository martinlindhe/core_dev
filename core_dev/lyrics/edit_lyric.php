<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$lyric_id = $_GET['id'];

	if (isset($_POST['lyric']) && isset($_POST['title']))
	{
		updateLyric($lyric_id, $_POST['title'], $_POST['lyric']);
	}

	$lyric_data = getLyricData($lyric_id);
	if (!$lyric_data) die;

	if (isset($_GET['delete']) && confirmed('Are you sure you want to delete this file?', 'delete', $lyric_id)) {
		removeLyric($lyric_id);
		header('Location: show_band.php?id='.$lyric_data['bandId']);
		die;
	}

	require('design_head.php');

	$lyric = $lyric_data['lyricText'];
	$lyric_name = $lyric_data['lyricName'];
	$band_name = $lyric_data['bandName'];

	echo '<form name="editlyric" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$lyric_id.'">';

	echo '<a href="show_band.php?id='.getLyricBandId($lyric_id).'">'.$band_name.'</a> - <input type="text" name="title" size="50" value="'.$lyric_name.'"/> ';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$lyric_id.'&amp;delete">Delete</a><br/>';
	echo '<a href="show_lyric.php?id='.$lyric_id.'">Show</a><br/>';
	echo '<textarea name="lyric" rows="37" cols="90">'.$lyric.'</textarea><br/>';
	echo '<input type="submit" value="Save changes" class="button"/>';
	echo '</form><br/>';

	echo 'This song appears on the following records:<br/>';

	$list = getLyricRecords($lyric_id);
	for ($i=0; $i<count($list); $i++)
	{
		$record_name = $list[$i]['recordName'];
		$author_name = $list[$i]['bandName'];
		if (!$record_name) $record_name = 's/t';

		if ($author_name) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$author_name.'</a>';
		} else {
			echo 'Compilation';
		}
		echo ' - <a href="show_record.php?id='.$list[$i]['recordId'].'">'. $record_name.'</a>, track #'.$list[$i]['trackNumber'].'<br/>';
	}

	require('design_foot.php');
?>