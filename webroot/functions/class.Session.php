<?
/*
	Session class

	Written by Martin Lindhe, 2007
*/

class Session
{
	protected $session_name = 'sid';	//default name
	
	//todo: ability to change these default values... they are used in the constructor...
	public $timeout = 180;	//max allowed idle time (in seconds) before auto-logout
	public $check_ip = true;	//if set to true, client will be logged out if client ip is changed during the session
	public $sha1_key = 'sitecode_uReply';

	protected $db = 0;		//reference to db handle

	public function __construct($db_handle, $session_name = '')
	{
		$this->db = &$db_handle;

		if ($session_name) $this->session_name = $session_name;

		session_name($this->session_name);
		session_start();
		
		if (!isset($_SESSION['IP'])) $_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
		else if ($this->check_ip && ($_SESSION['IP'] != $_SERVER['REMOTE_ADDR'])) {
				$this->logOut();
				echo 'Session IP changed! Old IP was '.$_SESSION['IP'].' but current registered is '.$_SERVER['REMOTE_ADDR'].'. Possible session hijacking attempt, closing session.';
		}

		if (!empty($_SESSION['loggedIn'])) {
			if ($_SESSION['lastActive'] < (time()-$this->timeout)) {
				$this->logOut();
				echo 'Session timed out after '.(time()-$_SESSION['lastActive']).' (timeout is '.($this->timeout).')';
			} else {
				//Update last active timestamp
				$this->db->query('UPDATE tblUsers SET lastActive=NOW() WHERE userId='.$_SESSION['userId']);
				$_SESSION['lastActive'] = time();
			}
		}

		//POST to any page with 'usr' & 'pwd' variables set to log in
		if (!$_SESSION['loggedIn'] && !empty($_POST['usr']) && isset($_POST['pwd'])) {
			$this->logIn($_POST['usr'], $_POST['pwd']);
		}

		//GET to any page with 'logout' set to log out
		if (isset($_GET['logout'])) {
			$this->logOut();
		}
	}

	public function __destruct()
	{
	}

	public function logIn($username, $password)
	{
		//echo 'LOGGING IN<br>';
		$enc_username = $this->db->escape($username);
		$enc_password = sha1( sha1($this->sha1_key).sha1($password) );

		$data = $this->db->getOneRow('SELECT * FROM tblUsers WHERE userName="'.$enc_username.'" AND userPass="'.$enc_password.'"');
		if (!$data) {
			$_SESSION['lastError'] = 'Login failed';
			return false;
		}
		
		$_SESSION['userName'] = $enc_username;
		$_SESSION['userId'] = $data['userId'];
		$_SESSION['mode'] = $data['userMode'];		//0=normal user. 1=admin, 2=super admin

		if ($_SESSION['mode'] >= 1) $_SESSION['isAdmin'] = 1;
		if ($_SESSION['mode'] >= 2) $_SESSION['isSuperAdmin'] = 1;
		$_SESSION['loggedIn'] = true;

		//any use of setting cookies these days? the browsers are pretty good at remembering this themselves
		//$this->WriteCookie('usr', $enc_username, 90);	//remembers username in 90 days from last visit

		//Update last login time
		$this->db->query('UPDATE tblUsers SET lastLoginTime=NOW(), lastActive=NOW() WHERE userId='.$_SESSION['userId']);
		$_SESSION['lastActive'] = time();

		return true;
	}

	public function logOut()
	{
		//echo 'LOGGING OUT<br>';
		
		$_SESSION['userId'] = 0;
		$_SESSION['loggedIn'] = false;
		$_SESSION['mode'] = 0;
		$_SESSION['isAdmin'] = 0;
		$_SESSION['isSuperAdmin'] = 0;
	}

	public function showInfo()
	{
		echo 'Logged in: '. ($_SESSION['loggedIn']?'YES':'NO').'<br>';
		if ($_SESSION['loggedIn']) {
			echo '<b>User name: '.$_SESSION['userName'].'</b><br>';
			echo '<b>User ID: '.$_SESSION['userId'].'</b><br>';
			echo '<b>User mode: '.$_SESSION['mode'].'</b><br>';
		}
		echo 'Session name: '.$this->session_name.'<br>';
		echo 'Current IP: '.$_SESSION['IP'].'<br>';
		echo 'Session timeout: '.$this->timeout.'<br>';
		echo 'Check for IP changes: '. ($this->check_ip?'YES':'NO').'<br>';
		
		echo 'SHA1 key: '.$this->sha1_key.'<br>';
	}

}
?>