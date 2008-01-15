<?
/**
 * functions_video.php - video handling helper functions
 */


	/**
	 * Helper function for embedding video in a html page
	 *
	 * http://www.mioplanet.com/rsc/embed_mediaplayer.htm
	 * http://www.mediacollege.com/video/streaming/embed/
	 *
	 * To embed an object in HTML document, the object class ID is required.
	 * The class ID for Windows Media Player 7, 9, 10 and 11 is clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6.
	 * If you want to embed Windows Media Player 6.4 instead of the latest version, the class ID is clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95.  
	 *
	 * \param $url video url to embed
	 * \return html code
	 */
	function embedVideo($url)
	{
		global $session;

		$data = '';

		if ($session->ua_ie) {	//FIXME: kolla User Agent i denna funktion istället
			//Detta funkar för IE:
			$data .= '<object id="VIDEO" width="176" height="144" '.		//qcif
					'CLASSID="CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6" '.
					'type="application/x-oleobject">';
			$data .=  '<param name="URL" VALUE="'.$url.'">';
			$data .=  '<param name="SendPlayStateChangeEvents" value="true">';
			$data .=  '<param name="AutoStart" value="true">';
			$data .=  '<param name="uiMode" value="none">';	//Possible values: invisible, none, mini, full
			$data .=  '<param name="PlayCount" value="1">';
			$data .=  '</object>';
		} else {
			//detta funkar för FF i linux iaf:
			//För linux, installera mozilla-plugin-vlc. För windows, installera wmpfirefoxplugin.exe från port25.technet.com

			$data .= '<embed type="application/x-mplayer2" src="'.$url.'"></embed>';
		}

		return $data;
	}

	//requires Apple's AC_QuickTime.js from http://developer.apple.com/internet/ieembedprep.html
	function embedQuickTimeVideo($url)
	{
		$width = 176;
		$height = 144 + 16;	//some extra pixels for media player controller

		$fake_url = 'http://10.10.10.240/xinfo.php';	//FIXME file must exist

		$data  = '<script language="JavaScript" type="text/javascript">';
		$data .= "QT_WriteOBJECT_XHTML('".$fake_url."', '".$width."', '".$height."', '',
						'qtsrc', '".$url."',
						'controller','true',
						'target','myself',
						'type','video/quicktime',
    				'autoplay', 'true');";
		$data .= '</script>';

		return $data;
	}
?>