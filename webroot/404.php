<?
	if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 404)
	{

		echo '<h1>File not found</h1>';
		echo $_SERVER['REQUEST_URI'].' doesn\'t exist on the server<br><br>';
		
		if ($_SERVER['REQUEST_URI'] != '/') {
			echo '&middot; <a href="/">Go to site index</a><br>';
		}

		if (!empty($_SERVER['HTTP_REFERER'])) {
			echo '&middot; <a href="'.$_SERVER['HTTP_REFERER'].'">Go back to referring page</a><br>';
		}
		die;
	}
?>