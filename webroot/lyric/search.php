<?
	if (empty($_POST['query'])) {
		header('Location: index.php'); die;
	}

	require_once('config.php');

	$query = $_POST['query'];

	$list = searchLyrics($query);
	if (count($list) == 1) {

		header('Location: show_lyric.php?id='.$list[0]['lyricId'].'&highlight='.urlencode($query));
		die;

	} else {

		require('design_head.php');

		echo 'Search results on "'.$query.'" ('.count($list).' hits):<br/><br/>';

		for ($i=0; $i<count($list); $i++) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'&amp;highlight='.urlencode($query).'">'.stripslashes($list[$i]['lyricName']).'</a><br/>';
		}

		echo '<br/><a href="index.php">Back to main</a>';
	}

	require('design_foot.php');
?>