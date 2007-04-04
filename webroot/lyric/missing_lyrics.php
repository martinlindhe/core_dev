<?
	include('include_all.php');
	include('body_header.php');
	
	echo 'Missing lyrics:<br/><br/>';

	$list = getMissingLyrics($db);

	for ($i=0; $i<count($list); $i++)
	{
		$query = '+"'.$list[$i]['bandName'].'" +"'.$list[$i]['lyricName'].'" +lyric';

		echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.$list[$i]['bandName']. '</a> - ';
		echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'">'.$list[$i]['lyricName'].'</a> ';
		echo '(<a href="http://www.google.com/search?q='.urlencode($query).'">google</a>)<br/>';
	}
	echo '<br/>';
	echo count($list).' missing lyrics.<br/>';

	include('body_footer.php');
?>