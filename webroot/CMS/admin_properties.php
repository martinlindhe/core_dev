<?
	include('include_all.php');
	
	if (!$_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$content = getInfoField($db, 'page_settings');
	$content .= '<hr>';
	
	//$content .= 'HTTP_ACCEPT_LANGUAGE: '.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'<br>';
	$content .= 'Your autodetected preferred language is: '.$_SESSION['preferred_language'].'<br>';

	$content .= '<hr>';
	$content .= 'Usermode: '.$_SESSION['userMode'].'<br>';
	$content .= '<br>';
	
	//var_dump($_COOKIE);
	
	$content .= 'Your browser is: '.$_SESSION['browser']['name'].' '.$_SESSION['browser']['version'].' ('.$_SESSION['browser']['OS'].')<br>';
	$content .= 'Your screen resolution is: '.$_SESSION['browser']['width'].'x'.$_SESSION['browser']['height'].'<br><br>';

	$content .= 'Your IP is: '.$_SERVER['REMOTE_ADDR'].' ('.getGeoIPCountry($db, $_SERVER['REMOTE_ADDR']).')<br>';

	$content .= '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="134" height="60" id="version_check" align="middle">'.
								'<param name="allowScriptAccess" value="sameDomain" />'.
								'<param name="movie" value="flash_version_check/version_check.swf" />'.
								'<param name="quality" value="high" />'.
								'<embed src="flash_version_check/version_check.swf" quality="high" width="134" height="60" name="version_check" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'.
							'</object>';
	$content .= 'You should have Flash Player 8,0,24,0 / 9,0,16,0 or newer installed';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Session properties', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>