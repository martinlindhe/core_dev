<?
	//xspf_player - bsd licenced flash mp3 player with manual at http://musicplayer.sourceforge.net/

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	include('include_all.php');

	$file = GetFile($db, $fileId);
	
	if ($file['fileMime'] != 'audio/mpeg') {
		echo 'odd filetype: '.$file['fileMime'];
		die;
	}

	$player_url = 'flash_audio_player/xspf_player_slim.swf';

	$params = '?autoplay=true'.
						'&song_url='.urlencode('file.php?id='.$fileId).
						'&song_title='.urlencode($file['fileName']);
?>
<html>
<head>
<title><?=$file['fileName']?> - audio player</title>

<script type="text/javascript">
window.focus();
</script>
</head>

<body marginheight=0 marginwidth=0 topmargin=0 leftmargin=0 bottommargin=0 bgcolor=#FFFFFF>

Playing <?=$file['fileName']?> ...<br><br>
<a href="file.php?id=<?=$fileId?>&dl">Click here</a> to save the file<br>
<object type="application/x-shockwave-flash" width="400" height="170" data="<?=$player_url.$params?>">
<param name="movie" value="<?=$player_url.$params?>" />
</object>


</body>
</html>