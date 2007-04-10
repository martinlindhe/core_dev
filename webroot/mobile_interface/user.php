<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) $_id = $l['id_id'];
	else $_id = $_GET['id'];

	require('config.php');
	require('design_head.php');
	
	$user_data = $user->getuser($_id);

	echo 'PROFIL - <b>'.$user_data['u_alias'].'</b> kön&ålder (onlinestatus?)<br/>';
	echo '<br/>';
	echo '<a href="relations_create.php?id='.$_id.'">BLI VÄN</a> ';
	echo '<a href="relations_block.php?id='.$_id.'">BLOCKERA</a> ';
	echo '<a href="gallery.php?id='.$_id.'">GALLERI</a> ';
	echo '<a href="relations.php?id='.$_id.'">VÄNNER</a> ';
	echo '<a href="mail_new.php?id='.$_id.'">MAILA</a> ';
	echo '<a href="guestbook.php?id='.$_id.'">GÄSTBOK</a>';
	echo '<br/>';

	echo 'Mina fakta här.<br/>';
	echo 'Min persentation här...<br/>';
	echo '<br/>';
	echo '<a href="user_change_facts.php">ÄNDRA FAKTA</a><br/>';
	echo '<a href="user_change_password.php">ÄNDRA LÖSENORD</a><br/>';

	require('design_foot.php');
?>