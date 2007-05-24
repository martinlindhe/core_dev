<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('config.php');

	if (!$l) die;	//user not logged in

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

	$mail = getMail($_GET['id']);
	if (!$mail) die;

	require('design_head.php');

	if (!$_header) $_header = 'Sv: '.$mail['sent_ttl'];
?>

	SVARA PÅ MAIL<br/>
	<br/>

	<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="to_alias" value="<?=$to_alias?>"/>
		Till: <?=$user->getstringMobile($mail['sender_id'])?><br/>
		Rubrik: <input type="text" name="header" value="<?=$_header?>"/><br/>
		Meddelande:<br/>
		<textarea name="body"><?=$_body?></textarea><br/>
		<input type="submit" value="Skicka"/>
	</form>


<?
	require('design_foot.php');
?>