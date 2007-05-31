<?
	require_once('config.php');

	if (empty($_POST['query'])) die;
	$query = $_POST['query'];

	$list = searchLyrics($query);
	if (count($list) == 1) {
		header('Location: show_lyric.php?id='.$list[0]['lyricId'].'&highlight='.urlencode($query));
		die;
	}

	require('design_head.php');

	echo 'Search results on "'.$query.'" ('.count($list).' hits):<br/><br/>';

	for ($i=0; $i<count($list); $i++) {
		echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.htmlspecialchars($list[$i]['bandName']).'</a> - ';
		echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'&amp;highlight='.urlencode($query).'">'.htmlspecialchars(stripslashes($list[$i]['lyricName'])).'</a><br/>';
	}

	require('design_foot.php');
?>