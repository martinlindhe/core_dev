<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('Bad id');

	$lyric_id = $_GET['id'];

	if (isset($_POST['lyric']) && isset($_POST['title']))
	{
		if (!$session->isAdmin) {
			addPendingChange(MODERATIONCHANGE_LYRIC, $lyric_id, $_POST['title'], $_POST['lyric']);
		} else {
			updateLyric($lyric_id, $_POST['title'], $_POST['lyric']);
		}
		echo 'Changes submitted.<br/>';
	}

	$lyric_data = getLyricData($lyric_id);
	if (!$lyric_data) die;

	require('design_head.php');

	if (isset($_GET['delete'])) {
		removeLyric($lyric_id);
		echo 'Lyric removed.<br/>';
		require('design_foot.php');
		die;
	}

	$lyric = $lyric_data['lyricText'];
	$lyric_name = $lyric_data['lyricName'];
	$band_name = $lyric_data['bandName'];

	echo '<form name="editlyric" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$lyric_id.'">';

	echo '<b>'.$band_name.'</b> - <input type="text" name="title" size="50" value="'.$lyric_name.'"/> ';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$lyric_id.'&amp;delete">Delete</a><br/>';
	echo '<a href="show_lyric.php?id='.$lyric_id.'">Show</a><br/>';
	echo '<textarea name="lyric" rows="27" cols="85">'.$lyric.'</textarea><br/>';
	echo '<input type="submit" value="Save changes" class="buttonstyle"/>';
	echo '</form><br/>';

	echo '<a href="show_lyric.php?id='.$lyric_id.'">Back to "View lyric" view</a><br/>';
	echo '<a href="show_band.php?id='.getLyricBandId($lyric_id).'">Go to '.$band_name.' page</a><br/>';

	if (isset($_SESSION['lastURL']) && $_SESSION['lastURL']) {
		echo '<a href="'.$_SESSION['lastURL'].'">Go back</a><br/>';
	}

	echo '<br/><br/>';
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
	echo '<br/>';
	echo '<a href="index.php">Back to main</a>';

	require('design_foot.php');
?>