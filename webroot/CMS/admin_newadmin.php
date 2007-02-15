<?
	//todo: javascript form validation

	include('include_all.php');
	
	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$registration_errors = '';

	$userId = 0;

	if (!empty($_POST['user']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {
		$error = makeNewUser($db, $_POST['user'], $_POST['pass1'], $_POST['pass2'], 2);
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
				header('Location: admin_newadmincreated.php');
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
	include('design_user_head.php');
	
		$content = '';

		$registered_users = getUserCount($db);
		if ($registered_users >= $config['user_max_allowed']) {
			if ($_SESSION['isAdmin']) {
				$content .= '<h1>Warning! The max number of registered users has been reached ('.$config['user_max_allowed'].').</h1>';
				$content .=  'Administrators can still create new users. To fix this problem, please increase the user_max_allowed setting.<br><br>';
			} else {
				$content .=  '<h1>Error! The max number of registered users has been reached ('.$config['user_max_allowed'].').</h1>';
				$content .= 'Please try again later, we are regularry increasing the max user limit, and cleaning out the database from old users.<br>';

				echo '<div id="user_admin_content">';
				echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Skapa nytt adminkonto', $content);
				echo '</div>';

				include('design_admin_foot.php');
				include('design_foot.php');
				die;
			}
		}

		$content .= getInfoField($db, 'page newadmin').'<br>';

		$enteredUserName = '';
		$enteredEmail = '';
		if (!empty($_POST['user'])) $enteredUserName = $_POST['user'];
		if (!empty($_POST['email'])) $enteredEmail = $_POST['email'];
		
		if ($registration_errors) $content .= '<span class="objectCritical">'.$registration_errors.'</span><br><br>';


		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="newuserform">';
		$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
		$content .= '<tr><td width=90>Username:</td><td><input type="text" name="user" value="'.$enteredUserName.'" size=25></td></tr>';
		$content .= '<tr><td>Password:</td><td><input type="password" name="pass1" size=25></td></tr>';
		$content .= '<tr><td>Repeat:</td><td><input type="password" name="pass2" size=25></td></tr>';
//		$content .= '<tr><td>E-mail:</td><td><input type="text" name="email" value="'.$enteredEmail.'" size=25></td></tr>';
		$content .= '<tr><td>&nbsp;</td><td><br><input type="submit" class="button" value="Skapa admin"></td></tr>';
		$content .= '</table>';
		$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Skapa nytt adminkonto', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>