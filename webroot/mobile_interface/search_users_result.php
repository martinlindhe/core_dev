<?
	require_once('config.php');
	require('design_head.php');
	
	//todo: gå direkt till profilsidan vid bara 1 sökresultat
	
	$result = performSearch();
?>

	SÖK ANVÄNDARE - RESULTAT<br/>
	<br/>

<?
	echo count($result['res']).' träffar:<br/>';

	foreach ($result['res'] as $row)
	{
		echo '(online/offline) ';
		echo '<a href="user.php?id='.$row['id_id'].'">'.$row['u_alias'].'</a> K42<br/>';
	}

	require('design_foot.php');
?>