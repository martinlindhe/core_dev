<?
	require('config.php');

	$_to_alias = $_header = $_body = $error = '';

	if (!empty($_POST['to_alias'])) $_to_alias = $_POST['to_alias'];
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
	todo: lista kompis-namn
	todo: kopiera vald kompis från dropdownlistan till "to_alias" fältet med js
*/

	echo 'SKRIV NYTT MAIL<br/>';
	echo '<br/>';

	if ($error) echo $error.'<br>';

	echo '<form method="post" action="">';
	echo 'Till: <input name="to_alias" type="text" size="8" value="'.$_to_alias.'"/> ';
	$list = getUserFriends();
	if ($list)
	{
		echo '<select name="friend_alias">';
		echo '<option>- Dina vänner -</option>';
		for ($i=0; $i<count($list); $i++) echo '<option>'.$list[$i]['xxx'].'</option>';
		echo '</select>';
	}
	echo '<br/>';
	echo 'Rubrik: <input name="header" type="text" value="'.$_header.'"/><br/>';
	echo 'Meddelande:<br/>';
	echo '<textarea name="body">'.$_body.'</textarea><br/>';
	echo '<input type="submit" value="Skicka"/>';
	echo '</form>';

	require('design_foot.php');
?>