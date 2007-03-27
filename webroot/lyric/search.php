<?
	if (empty($_POST['query'])) {
		header('Location: index.php'); die;
	}

	include('include_all.php');

	$query = $_POST['query'];

	$list = searchLyrics($db, $query);
	if (count($list) == 1) {

		header('Location: show_lyric.php?id='.$list[0]['lyricId'].'&highlight='.urlencode($query));
		die;

	} else {

		include('body_header.php');

		echo 'Search results on "'.$query.'" ('.count($list).' hits):<br><br>';

		for ($i=0; $i<count($list); $i++) {
			echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName'].'</a> - ';
			echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'&highlight='.urlencode($query).'">'.dbStripSlashes($list[$i]['lyricName']).'</a><br>';
		}

		echo '<br><a href="index.php">Back to main</a>';
	}

	include('body_footer.php');
?>