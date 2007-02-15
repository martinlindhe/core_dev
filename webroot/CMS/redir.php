<?
	//redir.php - redirect url click counter

	$url = '';
	if (!empty($_GET['url'])) $url = $_GET['url'];
	
	if ($url) {
		//todo: räkna klicken
		header('Location: '.$url);
	}

	die;
?>