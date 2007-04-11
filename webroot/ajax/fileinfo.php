<?
	/*
		AJAX fileinfo.php

		returns details about a certain file, in XHTML format for inclusion in a div element
	*/

	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');

	//todo: this path is not good!
	include('../adblock/config.php');
	if (!$session->id) die('<bad/>');

	echo '<info><![CDATA[';
	echo $files->getFileInfo($_GET['i']);
	echo ']]></info>';
?>