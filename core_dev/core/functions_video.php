<?php
/**
 * $Id$
 *
 * Video handling helper functions
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Embeds a video in a html page using Windows Media Player
 *
 * All parameters are documented here:
 * http://www.mioplanet.com/rsc/embed_mediaplayer.htm
 *
 * Common parameters:
 *	AutoStart	true/false
 *	uiMode		invisible, none, mini, full
 *	fullScreen	true/false
 *	PlayCount	numeric
 *
 * Windows Media Player 6.4: "clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"
 *
 * \param $url video url to embed
 * \param $w width of player window (not width of video clip)
 * \param $h height of player window (not height of video clip)
 * \return html code
 */
function embedVideo($url, $w = 352, $h = 288, $params = array())
{
	global $session;
	if (!is_numeric($w) || !is_numeric($h)) return false;

	if (strpos($session->user_agent, 'MSIE')) {
		//Tested in IE 7 (FIXME try IE 6)
		$data  = '<object type="application/x-oleobject'.
				' width="'.$w.'" height="'.$h.'"'.
				' classid="clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6">';	//Windows Media Player 7, 9, 10 and 11
		$data .= '<param name="URL" value="'.$url.'">';
		if (empty($params)) {
			//default settings
			$data .= '<param name="AutoStart" value="false">';
			$data .= '<param name="uiMode" value="mini">';
		} else {
			foreach ($params as $name => $val) {
				$data .= '<param name="'.$name.'" value="'.$val.'">';
			}
		}
		$data .= '</object>';
	} else {
		//This works with Firefox in Windows and Linux
		//For linux, install mozilla-plugin-vlc
		//For windows, install wmpfirefoxplugin.exe from http://port25.technet.com
		$data = '<embed type="application/x-mplayer2"'.
				' width="'.$w.'" height="'.$h.'"'.
				' src="'.$url.'"'.
				' ShowControls="1" ShowStatusBar="1" autostart="0">';
		$data .= '</embed>';
	}

	return $data;
}

/**
 * Helper function for embedding quicktime video
 * Requires Apple's AC_QuickTime.js from http://developer.apple.com/internet/ieembedprep.html
 * Parameter docs: http://www.apple.com/quicktime/tutorials/embed2.html
 *
 * \param $url url to embed
 * \param $mute bool. set to true to mute audio
 */
function embedQuickTimeVideo($url, $mute = false)
{
	$width = 176;
	$height = 144 + 16;	//some extra pixels for media player controller

	$fake_url = 'http://10.10.10.240/xinfo.php';	//FIXME file must exist

	$data  = '<script language="javascript" type="text/javascript">';
	$data .= 'QT_WriteOBJECT_XHTML("'.$fake_url.'", "'.$width.'", "'.$height.'", "", ';
		$data .= '"qtsrc", "'.$url.'", ';
		$data .= '"controller", "true", ';
		$data .= '"target", "myself", ';
		$data .= '"type", "video/quicktime", ';
		$data .= '"kioskmode", "true", ';		//hides right-click menu
		if ($mute) $data .= '"volume", "0", ';
		$data .= '"autoplay", "true"';
	$data .= ');';
	$data .= '</script>';

	return $data;
}

/**
 * Helper function to embed Flash .swf objects
 */
function embedSwf($url, $w = 0, $h = 0, $div_id = '')
{
	if (!$div_id) $div_id = 'div_'.mt_rand(1,999999);
	if (!$w) $w = 176 * 1.5;
	if (!$h) $h = 144 * 1.5;

	$data = '<div id="'.$div_id.'">';
	$data .= '<p>swf holder</p>';
	$data .= '</div>';

	$data .= '<script language="javascript" type="text/javascript">';

    //$data .= 'swfobject.embedSWF("'.$url.'", "'.$div_id.'", "'.$w.'", "'.$h.'", "9.0.0");';	//XXX: requires swfobject 2.0

	//XXX: this is for swfobject 1.5
	$data .= 'var fo = new SWFObject("'.$url.'", "mediaplayer", '.$w.', '.$h.', "8", "#FFFFFF");';
	$data .= 'fo.addParam("allowfullscreen","true");';
	$data .= 'fo.addVariable("width",'.$w.');';
	$data .= 'fo.addVariable("height",'.$h.');';
	$data .= 'fo.addVariable("file","'.$url.'");';
	$data .= 'fo.addVariable("autostart", false);';
	//$data .= 'fo.addVariable("image","video.jpg");';
	$data .= 'fo.write("'.$div_id.'");';
	$data .= '</script>';

	return $data;
}
?>
