<?
/*
	todo:

		file upload:
			* visa upload progress med ajax callback
			
		bildvisare:
			* centrera bilden i mitten av webbläsaren, över file-gadgeten (ska visas halvtransparent i bakgrunden)
			* rotera
			* förminska
			* förstora
			* förhandsgranska
			* spara
			* med ajax
			
		ljuduppspelare:
			* flash modul
*/

	require_once('config.php');

	require('design_head.php');

	echo 'file area<br/>';
	
	$files->showFiles();

	require('design_foot.php');
?>