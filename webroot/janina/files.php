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

	$files->showFiles();
?>

<br/>
<a href="index.php">Tillbaka till framsidan</a>

<?
	require('design_foot.php');
?>