<?
	require_once('config.php');

	if (!$l) die;	//user not logged in

	$_to_alias = $_header = $_body = $error = '';

	$_to_id = 0;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $_to_id = $_GET['id'];

	if (!$_to_id && !empty($_POST['to_alias'])) {
		$_to_alias = $_POST['to_alias'];
	} else {
		$tmp = $user->getuser($_to_id);
		$_to_alias = $tmp['u_alias'];
	}
	if (!empty($_POST['header'])) $_header = $_POST['header'];
	if (!empty($_POST['body'])) $_body = $_POST['body'];

	if ($_to_alias && $_header && $_body) {
		$error = sendMail($_to_alias, '', $_header, $_body);
		if ($error === true) {
			header('Location: mail.php');
			die;
		}
	}

	require('design_head.php');

/*
	todo: kopiera vald kompis från dropdownlistan till "to_alias" fältet med js
*/

	echo 'SKRIV NYTT MAIL<br/>';
	echo '<br/>';

	if ($error) echo $error.'<br>';

	echo '<form method="post" action="">';
	if ($_to_id) {
		echo 'Till: '.$_to_alias;
	} else {
		echo 'Till: <input name="to_alias" type="text" size="8" value="'.$_to_alias.'"/> ';
		$list = getRelations($l['id_id']);
	
		if ($list)
		{
			echo '<select name="friend_alias">';
			echo '<option>- Dina vänner -</option>';
			for ($i=0; $i<count($list); $i++) echo '<option value="'.$list[$i]['id_id'].'">'.$list[$i]['u_alias'].'</option>';
			echo '</select>';
		}
	}
	echo '<br/>';
	echo 'Rubrik: <input name="header" type="text" value="'.$_header.'"/><br/>';
	echo 'Meddelande:<br/>';
	echo '<textarea name="body">'.$_body.'</textarea><br/>';
	echo '<input type="submit" value="Skicka"/>';
	echo '</form>';

	require('design_foot.php');
?>