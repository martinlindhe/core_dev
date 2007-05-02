<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	$result = performSearch();
	if (count($result['res']) == 1) {
		header('Location: user.php?id='.$result['res'][0]['id_id']);
		die;
	}

	require('design_head.php');
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