<?
/*
	todo:

		file upload:
			* visa upload progress med ajax callback (kräver nån custom apache modul tror jag)
			
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

	//todo: fixa denna sökväg
	require_once('../layout/image_zoom_layer.html');

	echo 'file area<br>';
	
	$files->showFiles();

	require('design_foot.php');
?>