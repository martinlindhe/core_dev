<?
	/*
		AJAX fileinfo.php

		returns details about a certain file, in XHTML format for inclusion in a div element
	*/
	require_once('../adblock/config.php');

	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');

	//todo: this path is not good!
	if (!$session->id) die('<bad/>');

	echo '<info><![CDATA[';
	echo $files->getFileInfo($_GET['i']);
	echo ']]></info>';
?>