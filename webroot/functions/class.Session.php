<?
/*
	Session class

	Written by Martin Lindhe, 2007
*/

require_once('functions_ip.php');

class Session
{
	private $session_name = 'sid';			//default session name
	private $timeout = 1800;						//max allowed idle time (in seconds) before auto-logout
	private $check_ip = true;						//if set to true, client will be logged out if client ip is changed during the session
	private $sha1_key = 'rpxp8xkeWljo';	//used to further encode sha1 passwords, to make rainbow table attacks harder

	private $db;												//reference to db handle, used internally only
	
	//Aliases of $_SESSION[] variables
	public $error;
	public $ip;
	public $id;
	public $username;
	public $mode;
	public $lastActive;
	public $isAdmin;
	public $isSuperAdmin;

	public function __construct($db_handle, array $session_config)
	{
		$this->db = &$db_handle;

		if (isset($session_config['name'])) $this->session_name = $session_config['name'];
		if (isset($session_config['timeout'])) $this->timeout = $session_config['timeout'];
		if (isset($session_config['check_ip'])) $this->check_ip = $session_config['check_ip'];
		if (isset($session_config['sha1_key'])) $this->sha1_key = $session_config['sha1_key'];
		
		$this->db->attachSession($this);

		session_name($this->session_name);
		session_start();
		
		if (!isset($_SESSION['error'])) $_SESSION['error'] = '';
		if (!isset($_SESSION['ip'])) $_SESSION['ip'] = 0;
		if (!isset($_SESSION['id'])) $_SESSION['id'] = 0;
		if (!isset($_SESSION['username'])) $_SESSION['username'] = '';
		if (!isset($_SESSION['mode'])) $_SESSION['mode'] = 0;
		if (!isset($_SESSION['lastActive'])) $_SESSION['lastActive'] = 0;
		if (!isset($_SESSION['isAdmin'])) $_SESSION['isAdmin'] = 0;
		if (!isset($_SESSION['isSuperAdmin'])) $_SESSION['isSuperAdmin'] = 0;

		$this->error = &$_SESSION['error'];
		$this->ip = &$_SESSION['ip'];	//store IP as an unsigned 32bit int
		$this->id = &$_SESSION['id'];	//if id is set, also means that the user is logged in
		$this->username = &$_SESSION['username'];
		$this->mode = &$_SESSION['mode'];
		$this->lastActive = &$_SESSION['lastActive'];
		$this->isAdmin = &$_SESSION['isAdmin'];
		$this->isSuperAdmin = &$_SESSION['isSuperAdmin'];

		if (!$this->ip) {
			$this->ip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
		}

		if ($this->check_ip && $this->ip && ($this->ip != IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']))) {
				$this->error = 'Client IP changed';
				$this->db->log('Client IP changed! Old IP: '.GeoIP_to_IPv4($this->ip).', current: '.GeoIP_to_IPv4($_SERVER['REMOTE_ADDR']));
				$this->logOut();
		}

		if ($this->id) {
			if ($this->lastActive < (time()-$this->timeout)) {
				$this->error = 'Inactivity timeout';
				$this->db->log('Session timed out after '.(time()-$this->lastActive).' (timeout is '.($this->timeout).')');
				$this->logOut();
			} else {
				//Update last active timestamp
				$this->db->query('UPDATE tblUsers SET lastActive=NOW() WHERE userId='.$this->id);
				$this->lastActive = time();
			}
		}

		//POST to any page with 'usr' & 'pwd' variables set to log in
		if (!$this->id && !empty($_POST['usr']) && isset($_POST['pwd'])) {
			$this->logIn($_POST['usr'], $_POST['pwd']);
		}

		//GET to any page with 'logout' set to log out
		if ($this->id && isset($_GET['logout'])) {
			$this->db->log('user logged out');
			$this->logOut();
			header('Location: '.basename($_SERVER['SCRIPT_NAME']));
			die;
		}
	}

	public function __destruct()
	{
	}

	public function logIn($username, $password)
	{
		$enc_username = $this->db->escape($username);
		$enc_password = sha1( sha1($this->sha1_key).sha1($password) );

		$data = $this->db->getOneRow('SELECT * FROM tblUsers WHERE userName="'.$enc_username.'" AND userPass="'.$enc_password.'"');
		if (!$data) {
			$this->error = 'Login failed';
			$this->db->log('failed login attempt: username '.$enc_username);
			return false;
		}

		$this->error = '';
		$this->username = $enc_username;
		$this->id = $data['userId'];
		$this->mode = $data['userMode'];		//0=normal user. 1=admin, 2=super admin

		if ($this->mode >= 1) $this->isAdmin = 1;
		if ($this->mode >= 2) $this->isSuperAdmin = 1;

		//Update last login time
		$this->db->query('UPDATE tblUsers SET lastLoginTime=NOW(), lastActive=NOW() WHERE userId='.$this->id);
		$this->lastActive = time();

		$this->db->log('user logged in');

		return true;
	}

	public function logOut()
	{
		$this->id = 0;
		$this->ip = 0;
		$this->mode = 0;
		$this->isAdmin = 0;
		$this->isSuperAdmin = 0;
	}
	
	public function showLoginForm()
	{
		echo '<form name="login_form" method="post">';
		if ($this->error) {
			echo 'Error: '.$this->error.'<br>';
		}
		echo 'Username: <input name="usr" type="text"><br>';
		echo 'Password: <input name="pwd" type="password"><br>';
		echo '<input type="submit" value="Log in">';
		echo '</form>';
	}

	public function showInfo()
	{
		echo 'Logged in: '. ($this->id?'YES':'NO').'<br>';
		if ($this->id) {
			echo '<b>User name: '.$this->username.'</b><br>';
			echo '<b>User ID: '.$this->id.'</b><br>';
			echo '<b>User mode: '.$this->mode.'</b><br>';
		}
		echo 'Session name: '.$this->session_name.'<br>';
		echo 'Current IP: '.$this->ip.'<br>';
		echo 'Session timeout: '.$this->timeout.'<br>';
		echo 'Check for IP changes: '. ($this->check_ip?'YES':'NO').'<br>';
		if ($this->isSuperAdmin) {
			echo 'SHA1 key: '.$this->sha1_key.'<br>';
		}
	}

}
?>