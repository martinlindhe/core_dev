<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<title>xx</title>
</head>
<body>
<?
	$width = 128;
	$height = 96;
	$filename = 'taxi.avi';

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

<OBJECT ID="MediaPlayer1" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95"
 CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701"
 standby="Loading Microsoft Windows Media Player components..."
 TYPE="application/x-oleobject" width="<?=$width?>" height="<?=$height?>">
 <PARAM NAME="FileName" VALUE="<?=$filename?>">

 <PARAM NAME="ClickToPlay" VALUE="TRUE">
 <PARAM NAME="AutoStart" VALUE="TRUE">
 <PARAM NAME="ShowControls" VALUE="TRUE">
 <PARAM NAME="ShowDisplay" VALUE="TRUE">
 <PARAM NAME="ShowStatusBar" VALUE="TRUE">
 <embed TYPE="application/x-mplayer2"
  pluginspage="http://www.microsoft.com/windows/windowsmedia/download/"
  filename="<?=$filename?>"
  SRC="<?=$filename?>"
  Name=MediaPlayer1
  ClickToPlay=1
  AutoStart=0
  ShowControls=1
  ShowDisplay=1
  ShowStatusBar=1
  controls="PlayButton"
  width=320
  height=290>
 </embed>
</OBJECT>

</body></html>
