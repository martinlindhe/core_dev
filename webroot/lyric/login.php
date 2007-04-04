<?
	include('include_all.php');
	
	if ($_SESSION['loggedIn'] == true) {
		header('Location: index.php');
		die;		
	}


	/* Log in the user */

	$username = '';
	$password = '';

	if (!empty($_POST['user']) && !empty($_POST['pass'])) {

		$username = $_POST['user'];
		$password = $_POST['pass'];

	} else if (!empty($_SESSION['loginuser']) && !empty($_SESSION['loginpass'])) {

		$username = $_SESSION['loginuser'];
		$password = $_SESSION['loginpass'];

		$_SESSION['loginuser'] = '';
		$_SESSION['loginpass'] = '';
	}

	if ($username && $password) {

		$status = loginUser($db, $username, $password);
		if ($status === true) {

			$_SESSION['userName'] = $username;
			$_SESSION['userId'] = getUserId($db, $username);
			$_SESSION['userMode'] = getUserMode($db, $_SESSION['userId']);
			$_SESSION['loggedIn'] = true;
			$_SESSION['lastActive'] = time();
			$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
			
			setUsernameCookie($username); //remembers last username for up to 30 days (default)
			header('Location: index.php');
			die;

		} else {
			$login_error = $status;
		}
	}

	include('body_header.php');
	
	$usernamecookie = getUsernameCookie();
?>		
Log in<br/>
<form name="login" method="post" action="<? echo $_SERVER["PHP_SELF"]; if (isset($_GET["id"])) echo "?id=".$_GET["id"]; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td>

		<? if (isset($session_error)) echo '<font color="red">'.$session_error.'</font><br/><br/>'; ?>
		<? if (isset($login_error)) echo '<font color="red">'.$login_error.'</font><br/><br/>'; ?>
		Username:<br/>
		<input type="text" name="user" value="<? echo $usernamecookie; ?>" size="16" maxlength="20"/><br/>
		Password:<br/>
		<input type="password" name="pass" size="16" maxlength="20"/><br/><br/>
		<input type="submit" value="Log in" class="buttonstyle"/><br/><br/>

	</td></tr>
</table>
</form>
<?

	echo '<script type="text/javascript">'."\n";
	if ($usernamecookie == "") {
		echo "document.login.user.focus();\n";
	} else {
		echo "document.login.pass.focus();\n";
	}
	echo '</script>';

	include('body_footer.php');
?>