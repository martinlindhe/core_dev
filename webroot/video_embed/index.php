<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<title>xx</title>
</head>
<body>
<?
	$width = 128;
	$height = 96;
	$filename = 'video.avi';

	//if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) {
		/* Hack to make the player a little bit bigger for firefox users since it cant turn off controls */
		$width = round($width * 1.1);
		$height = round($height * 1.8);
	//}


	/*
		* enbart object funkar i IE7, firefox 2. EJ i opera 9.2
	
		* embed krävs för opera 9.2
	
		* controls syns alltid i firefox, från "WMP Firefox Plugin RelNotes.txt", för np-mswmp.dll 1.0.0.8:
			"The Status bar is always displayed regardless of the ShowStatusBar value"
			enligt http://port25.technet.com/archive/2007/04/16/windows-media-player-plug-in-for-firefox.aspx,
			så ska controls försvinna med "uimode=none" i firefox
			
		<embed type="application/x-mplayer2" src="<?=$filename?>" width="<?=$width?>" height="<?=$height?>" autostart="true" showcontrols="false" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/"/>
	*/
?>

<object type="application/x-mplayer2" style="width: 200px; height: 200px;" data="<?=$filename?>">
	<param name="filename" value="<?=$filename?>"/>
	<embed type="application/x-mplayer2" width="200px" height="200px"  showcontrols="0" src="<?=$filename?>" />
</object> 


</body></html>
