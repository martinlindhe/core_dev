<?php

//echo '<pre>'; print_r($_SERVER);
echo '<h1>Access blocked</h1>';
echo $_SERVER['REQUEST_URI'].' is blocked for '.$_SERVER['REMOTE_ADDR'].'. you are logged<br/><br/>';

if ($_SERVER['REQUEST_URI'] != '/') {
	echo '&middot; <a href="/">Go to site index</a><br/>';
}

if (!empty($_SERVER['HTTP_REFERER'])) {
	echo '&middot; <a href="'.$_SERVER['HTTP_REFERER'].'">Go back to referring page</a><br/>';
}
?>
