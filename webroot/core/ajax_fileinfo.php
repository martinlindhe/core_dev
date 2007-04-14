<?
	/*
		AJAX fileinfo.php

		returns details about a certain file, in HTML format for inclusion in a div element
	*/

	//todo: this path is not good!
	require_once('../adblock/config.php');

	if ((!$session->id && !$files->anon_uploads) || empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

	$info = $files->getFileInfo($_GET['i']);
	if (!$info) die('bad');

	echo $info;
?>