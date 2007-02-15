<?
	include('include_all.php');
	
	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	//bara admins kan ändra lösenord hos ESP
	

	
	/* Ändra lösenord */
	if (isset($_POST['pwdold']) || isset($_POST['pwdnew1']) || isset($_POST['pwdnew2'])) {
		$pwd_old  = $_POST['pwdold'];
		$pwd_new1 = $_POST['pwdnew1'];
		$pwd_new2 = $_POST['pwdnew2'];
		if (!$pwd_old || !$pwd_new1 || !$pwd_new2) {
			$pwd_error = 'Du m&aring;ste fylla i alla f&auml;lten f&ouml;r att byta l&ouml;senord!';
		} else if ($pwd_new1 != $pwd_new2) {
			$pwd_error = 'L&ouml;senorden matchar inte!';
		} else {
			if (checkPassword($db, $_SESSION['userId'], $pwd_old)) {
				setUserPassword($db, $_SESSION['userId'], $pwd_new1);
				$pwd_error = 'L&ouml;senordet &auml;ndrat!';
			} else {
				$pwd_error = 'Det gamla l&ouml;senordet &auml;r felaktigt!';
			}
		}
	}

	include('design_head.php');
	include('design_user_head.php');

		$content = '<b>&Auml;ndra l&ouml;senord</b><br><br>';

		$content .= '<table width="100%" cellpadding=2 cellspacing=0 border=0>';
			$content .= '<form name="changePassword" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$show.'#pwd">';
		
			if (isset($pwd_error)) $content .= '<tr><td colspan=2><span class="objectCritical">'.$pwd_error.'</span></td></tr>';

			$content .= '<tr><td width=60>Gamla</td><td><input type="password" name="pwdold"></td></tr>';
			$content .= '<tr><td width=60>Nya</td><td><input type="password" name="pwdnew1"></td></tr>';
			$content .= '<tr><td width=60>Bekr&auml;fta</td><td><input type="password" name="pwdnew2"></td></tr>';
			$content .= '<tr><td colspan=2>&nbsp;</td></tr>';
			$content .= '<tr><td colspan=2><input type="submit" value="&Auml;ndra" class="button"></td></tr>';
			$content .= '</form>';
		$content .= '</td></tr></table>';

		echo '<div id="user_misc_content">';
		echo MakeBox('<a href="'.$_SERVER['PHP_SELF'].'">Lag profil</a>|&Auml;ndra l&ouml;senord', $content);
		echo '</div>';
		
	include('design_user_foot.php');
	include('design_foot.php');
?>