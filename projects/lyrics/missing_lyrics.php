<?
	require_once('config.php');
	require('design_head.php');

	echo 'Missing lyrics:<br/><br/>';

	$list = getMissingLyrics();

	for ($i=0; $i<count($list); $i++)
	{
		$query = '+"'.$list[$i]['bandName'].'" +"'.$list[$i]['lyricName'].'" +lyric';

		echo '<a href="show_band.php?id='.$list[$i]['bandId'].'">'.htmlspecialchars(stripslashes($list[$i]['bandName'])). '</a> - ';
		echo '<a href="show_lyric.php?id='.$list[$i]['lyricId'].'">'.htmlspecialchars(stripslashes($list[$i]['lyricName'])).'</a> ';
		echo '(<a href="http://www.google.com/search?q='.urlencode($query).'">google</a>)<br/>';
	}
	echo '<br/>';
	echo count($list).' missing lyrics.<br/>';

	require('design_foot.php');
?>