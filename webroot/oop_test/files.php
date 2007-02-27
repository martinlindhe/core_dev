<?
/*
	todo:
	
		- rita upp en "files-gadget"
		- kunna ladda upp nya bilder direkt från gadgeten,
			* visa upload progress med ajax---
			
		- kunna redigera bilder:
			* rotera
			* förminska
			* förstora
			* förhandsgranska
			* spara
			* med ajax
			
		- kunna spela .mp3or
			* med flash modul
*/

	require_once('config.php');

	require('design_head.php');

	echo 'file area<br>';
	
	$files->showFiles();

	require('design_foot.php');
?>