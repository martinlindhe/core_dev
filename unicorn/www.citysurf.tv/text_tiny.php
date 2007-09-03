<?
	//popup fönster

	if (empty($_GET['v'])) die;	
	$text = $_GET['v'];

	require_once('config.php');

	$sections = array('cookies', 'radio', 'disclaimer', 'about', 'agree', 'contact', 'url', 'mmshelp');

	if (!in_array($text, $sections)) {
		errorACT('Felaktig sida.');
	}

	require(DESIGN.'top.php');
	popupLACT(extOUT(gettxt($text), ''));
?>