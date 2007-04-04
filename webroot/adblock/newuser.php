<?
	//todo: javascript form validation

	include('include_all.php');
	
	if ($_SESSION['loggedIn'] && !$_SESSION['isAdmin']) {
		/* Redirect normal users */
		header('Location: index.php');
		die;
	}
	
	$registration_errors = '';

	$userId = 0;

	if (!empty($_POST['user']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {
		$error = makeNewUser($db, $_POST['user'], $_POST['pass1'], $_POST['pass2']);
		if (is_numeric($error)) $userId = $error;
		if ($userId) {

			//todo: den sparar nu setting om man uppger ok mail, om man uppger felaktig så ignorerar den bara det, säger inget..
			if (!empty($_POST['email']) && ValidEmail($_POST['email'])) {
				saveSetting($db, $userId, 'email', trim($_POST['email'])); 	
			/*} else if (!empty($_POST['email'])) {
				$registration_errors .= 'Invalid email';*/
			}

			$check = 0;
			if (!$_SESSION['isAdmin']) $check = loginUser($db, $_POST['user'], $_POST['pass1']);
			if ($check || $_SESSION['isAdmin']) {
				header('Location: newusercreated.php');
				die;
			}
			$registration_errors .= 'Failed to log in with the newly created user!';

		} else {
			$registration_errors .= 'Failed to create user, '.$error;
		}
	} else if (!empty($_POST['user']) || !empty($_POST['pass1']) || !empty($_POST['pass2'])) {
		$registration_errors .= 'Please fill in all forms';
	}

	include('design_head.php');

	echo getInfoField($db, 'page_newuser').'<br/>';

	$enteredUserName = '';
	$enteredEmail = '';
	if (!empty($_POST['user'])) $enteredUserName = $_POST['user'];
	if (!empty($_POST['email'])) $enteredEmail = $_POST['email'];
	
	if ($registration_errors)
	{
		echo '<span style=background-color:#FF6666>'.$registration_errors.'</span><br/><br/>';
	}
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="newuserform">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr><td width="90">User name:</td><td><input type="text" name="user" value="<?=$enteredUserName?>" size="25"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="pass1" size="25"/></td></tr>
	<tr><td>Repeat:</td><td><input type="password" name="pass2" size="25"/></td></tr>
	<tr><td>E-mail:</td><td><input type="text" name="email" value="<?=$enteredEmail;?>" size="25"/></td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" value="Make user"/></td></tr>
</table>
</form>
<?

	include('design_foot.php');
?>