<?
	include("include_all.php");
	include("body_header.php");
	
	echo "Incomplete lyrics:<br><br>";
	echo "For a incomplete lyric to be listed here, it must contain ??? at least once.<br><br>";

	$list = getIncompleteLyrics($db);

	for ($i=0; $i<count($list); $i++)
	{
		$query = "+\"".$list[$i]["bandName"]."\" +\"".$list[$i]["lyricName"]."\" +lyric";
		
		echo "<a href=\"show_band.php?id=".$list[$i]["bandId"]."\">".$list[$i]["bandName"]. "</a> - ";
		echo "<a href=\"show_lyric.php?id=".$list[$i]["lyricId"]."\">".$list[$i]["lyricName"]."</a> ";
		echo "(<a href=\"http://www.google.com/search?q=".urlencode($query)."\">google</a>)<br>";
	}
	echo "<br>";
	echo count($list)." incomplete lyrics.<br>";

	include("body_footer.php");
?>