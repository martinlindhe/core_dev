<?
	/*
		AJAX fileinfo.php

		returns details about a certain file, in HTML format for inclusion in a div element
	*/

	//todo: this path is not good!
	require_once('../adblock/config.php');

	if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

	echo $files->getFileInfo($_GET['i']);
?>