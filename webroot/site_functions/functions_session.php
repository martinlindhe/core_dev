<?
	//functions_session.php 


	$config['session_name'] = 'URsessID';	//name of session-id cookie
	$config['session_timeout'] = 1800;		//(in seconds) 30 minutes idle = automatically logged out
	$config['session_check_ip'] = true;	//set to true to check if IP has changed during session and kill session as a security precaution (det omöjliggör access för en del gamla jobbiga modemanvändare)


	//this function is called from loginUser()
	function StartSession(&$db, $username, $userdata)
	{
		/* Update last login time */
		$sql = 'UPDATE tblUsers SET lastLoginTime=NOW() WHERE userId='.$userdata['userId'];
		dbQuery($db, $sql);

		/* Populate session data */
		DestroySession($db);

		$_SESSION['userName'] = $username;
		$_SESSION['userId'] = $userdata['userId'];
		$_SESSION['userMode'] = $userdata['userMode'];	//0=normal user. 1=admin, 2=super admin

		$_SESSION['sessionIP'] = $_SERVER['REMOTE_ADDR'];		//save current session IP for session verification later
		if ($_SESSION['userMode'] >= 1) $_SESSION['isAdmin'] = 1;
		if ($_SESSION['userMode'] >= 2) $_SESSION['isSuperAdmin'] = 1;
		$_SESSION['loggedIn'] = true;
		$_SESSION['lastActive'] = time();
		
		return $_SESSION['userId'];		
	}

	//This function needs to be called each page view
	function ContinueSession(&$db)
	{
		global $config;

		session_name($config['session_name']);
		session_start();
		
		if (!isset($_SESSION['lastError']))						$_SESSION['lastError'] = '';
		if (!isset($_SESSION['loggedIn']))						$_SESSION['loggedIn'] = false;
		if (!isset($_SESSION['userId']))							$_SESSION['userId'] = 0;
		if (!isset($_SESSION['isAdmin']))							$_SESSION['isAdmin'] = 0;
		if (!isset($_SESSION['isSuperAdmin']))				$_SESSION['isSuperAdmin'] = 0;
		if (!isset($_SESSION['sessionIP']))						$_SESSION['sessionIP'] = 0;
		if (!isset($_SESSION['lastUserName']))				$_SESSION['lastUserName'] = '';

		//handle user login
		if (!$_SESSION['loggedIn'] && !empty($_POST['usr']) && isset($_POST['pwd'])) {
			$user = $_POST['usr'];
			$pass = $_POST['pwd'];	//pwd is empty if sha1-hash is used

			$check = loginUser($db, $user, $pass);
			if ($check) {
				WriteCookie('usr', $user, 90);	//remembers username in 90 days from last visit
			} else {
				$_SESSION['lastError'] = 'Login failed';
			}
		}
		
		if (isset($_GET['logout'])) {
			DestroySession($db);
			return;
		}

		if ($_SESSION['loggedIn'])
		{
			if ($_SESSION['lastActive'] < (time()-$config['session_timeout'])) {
				$_SESSION['lastError'] = 'session timeout';
				logEntry($db, 'Session timed out after '.makeTimePeriodShort(time()-$_SESSION['lastActive']).' (timeout is '.makeTimePeriodShort($config['session_timeout']).')', LOGLEVEL_NOTICE);
				DestroySession($db);
				header('Location: '.$config['start_page']);
				die;
			}
			if ($config['session_check_ip'] && ($_SESSION['sessionIP'] != $_SERVER['REMOTE_ADDR'])) {
				//Client IP have changed during the current session, kill session by logging out user
				$_SESSION['lastError'] = 'IP changed!';
				logEntry($db, 'Session IP changed! Old IP was '.$_SESSION['sessionIP'].' but current registered is '.$_SERVER['REMOTE_ADDR'].'. Possible session hijacking attempt, closing session.', LOGLEVEL_WARNING);
				DestroySession($db);
				header('Location: '.$config['start_page']);
				die;
			}
			$_SESSION['lastActive'] = time();
			updateUserActivity($db);
		} else {
			$_SESSION['lastUserName'] = ReadCookie('usr');
		}
	}

	//used to destroy session data, but preserve a few important data to avoid unnessecary sql queries. used when logging in and out
	function DestroySession(&$db)
	{
		//Note: As of PHP 4.3.3, calling session_start() while the session has already been started will result in an error of level E_NOTICE. Also, the second session start will simply be ignored.
		global $config;

		session_name($config['session_name']);
		@session_start();
		
		if ($_SESSION['userId']) {
			//We set lastActive to 0 at logout
			
			//todo: gör en extrakolumn "tblUsers.loggedIn" och sätt den till 0 här
			//updateUserActivity($db, 0);
		}

		$err = $_SESSION['lastError'];
		//$preferred_language = $_SESSION['preferred_language'];

		$_SESSION = array(); //destroy session data
		$_SESSION['lastError'] = $err; //but remember last error (session timeout)
		//$_SESSION['preferred_language'] = $preferred_language;	//and remember preferred language

		DeleteCookie('setting_listlimit');
		DeleteCookie('setting_email');
		DeleteCookie('setting_signature');

		/* Initialize session variables */
		$_SESSION['userId'] = 0;
		$_SESSION['loggedIn'] = false;
		$_SESSION['isAdmin'] = 0;
		$_SESSION['isSuperAdmin'] = 0;
		$_SESSION['sessionIP'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['lastUserName'] = ReadCookie('usr');	/* Fetch username from previous session */
		$_SESSION['browser'] = array();
	}

	function ReadCookie($cookieName, $defaultValue = '')
	{
		//fixme: kolla om det är nåra konstiga tecken i cookien.
		if (!empty($_COOKIE[$cookieName])) return $_COOKIE[$cookieName];
		return $defaultValue;
	}
	
	function WriteCookie($cookieName, $value, $DaysUntilExpire = 0)
	{
		//fixme: kolla om det är nåra konstiga tecken i cookien. t.ex html taggar, vad mer?

		if ($DaysUntilExpire) {
			$expire_time = time()+(86400*$DaysUntilExpire);		//there is 86400 seconds in 24 hours
			setcookie($cookieName, $value, $expire_time);
		} else {
			setcookie($cookieName, $value);		//expires at end of session
		}
	}

	function DeleteCookie($cookieName)
	{
		setcookie($cookieName, '', time()-3600);
	}
?>