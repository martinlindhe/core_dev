<?
	//full ruta

	if (empty($_GET['v'])) die;	
	$text = $_GET['v'];

	require_once('config.php');

	$sections = array('cookies', 'register-accept', 'disclaimer', 'about', 'agree', 'contact', 'url', 'mmshelp');
	
	require(DESIGN.'head.php');

	if (!in_array($text, $sections)) {
		errorACT('Felaktig sida.');
	}

	echo (extOUT(gettxt($text)));
	//popupLACT(extOUT(gettxt($text), ''));
	
	require(DESIGN.'foot.php');
?>