<?
	//popup fönster

	if (empty($_GET['v'])) die;	
	$text = $_GET['v'];

	require_once('config.php');

	$sections = array('cookies', 'radio', 'disclaimer', 'about', 'agree', 'contact', 'url', 'mmshelp');

	if (!in_array($text, $sections)) {
		errorACT('Felaktig sida.');
	}

/*	if(empty($id)) {
		if($v == 1)
			errorIACT(extOUT(gettxt($text)), $action, $v);
		else
			errorIACT(extOUT(gettxt($text)), $action, $v);
	} else if($id == 1)
		splashLACT(extOUT(gettxt($text), ''));
	else if($id == 2)
		popupLACT(extOUT(gettxt($text), ''));
	else if($id == 3)
*/
	require(DESIGN.'top.php');
	//echo (extOUT(gettxt($text)));
	popupLACT(extOUT(gettxt($text), ''));
?>