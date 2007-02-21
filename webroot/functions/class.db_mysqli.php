<?
//Object oriented interface for MySQL databases using the MySQLi extension
//other SQL-database interfaces should use the same interface

//todo: gör en base-class med generella funktioner, och använd "class DB_MySQLi extends DB_Base"

class DB_MySQLi
{
	//default settings
	private $debug = false;
	private $host	= 'localhost';
	private $port	= 3306;
	private $username = 'root';
	private $password = '';
	private $database = '';
	
	private $db_handle = false;

	public function __construct($settings) {
		//echo 'In DB_MySQLi constructor<br>';
		if (!empty($settings['debug'])) $this->debug = $settings['debug'];
		if (!empty($settings['host'])) $this->host = $settings['host'];
		if (!empty($settings['port'])) $this->port = $settings['port'];
		if (!empty($settings['username'])) $this->username = $settings['username'];
		if (!empty($settings['password'])) $this->password = $settings['password'];
		if (!empty($settings['database'])) $this->database = $settings['database'];
		
		//Open database connection:
		$this->db_handle = @ new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

		if (mysqli_connect_errno()) {
			$this->db_handle = false;
			echo 'Database connection error.';
			die();
		}
	}

	public function __destruct() {
		//echo 'In DB_MySQLi destructor<br>';
		if ($this->db_handle) $this->db_handle->close();
	}

	public function showSettings()
	{
		echo 'debug: '.$this->debug.'<br>';
		echo 'host: '.$this->host.':'.$this->port.'<br>';
		echo 'login: '.$this->username.':'.$this->password.'<br>';
		echo 'database: '.$this->database.'<br>';
		echo 'Host information: '. $this->db_handle->host_info.'<br>';
	}

	/* Returns an array with the results, including preserved indexes for the array */
	public function getArray($query)
	{
		echo 'performing query: '. $query;
	}
}


?>