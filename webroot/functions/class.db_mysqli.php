<?
//Object oriented interface for MySQL databases using the MySQLi extension
//other SQL-database interfaces should use the same interface

/*
	todo: gör en base-class med generella funktioner, och använd "class DB_MySQLi extends DB_Base"

*/

class DB_MySQLi
{
	//default settings
	private $host	= 'localhost';
	private $port	= 3306;
	private $username = 'root';
	private $password = '';
	private $database = '';
	private $db_handle = false;
	
	//debug variables
	private $debug = false;
	private $time_spent = array();
	private $queries_cnt = 0;
	private $queries = array();

	//Constructor
	public function __construct($settings)
	{
		if (!empty($settings['debug'])) $this->debug = $settings['debug'];
		if (!empty($settings['host'])) $this->host = $settings['host'];
		if (!empty($settings['port'])) $this->port = $settings['port'];
		if (!empty($settings['username'])) $this->username = $settings['username'];
		if (!empty($settings['password'])) $this->password = $settings['password'];
		if (!empty($settings['database'])) $this->database = $settings['database'];

		$this->connect();
	}

	//Destructor
	public function __destruct()
	{
		if ($this->db_handle) $this->db_handle->close();
	}
	
	/* Opens a database connection */
	private function connect()
	{
		if ($this->debug) $time_started = microtime(true);
		
		//Open database connection
		$this->db_handle = @ new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

		if (mysqli_connect_errno()) {
			$this->db_handle = false;
			die('Database connection error.');
		}
	
		if ($this->debug) {
			$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
			$this->queries[ $this->queries_cnt ] = 'connect('.$this->host.'), '.$this->db_handle->host_info;
			$this->queries_cnt++;
		}
	}

	/* Shows current settings */
	public function showSettings()
	{
		echo 'debug: '.$this->debug.'<br>';
		echo 'host: '.$this->host.':'.$this->port.'<br>';
		echo 'login: '.$this->username.':'.$this->password.'<br>';
		echo 'database: '.$this->database.'<br>';
		echo 'Host information: '. $this->db_handle->host_info.'<br>';
	}
	
	/* Shows debug/profiling information */
	public function showDebugInfo()
	{
		if (!$this->debug) return;
		
		echo $this->queries_cnt.' queries executed.<br>';
		for ($i=0; $i<$this->queries_cnt; $i++) {
			echo '['.round($this->time_spent[$i],5).'] ';
			echo $this->queries[$i].'<br>';
			echo '<hr>';
		}
	}

	/* Escapes a string for use in queries */
	public function escape($query)
	{
		$data = $this->db_handle->real_escape_string($query);

		return $data;
	}
	
	/* Performs a query that don't return anything */
	public function query($query)
	{
		if ($this->debug) $time_started = microtime(true);

		$this->db_handle->query($query);

		if ($this->debug) {
			$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
			$this->queries[ $this->queries_cnt ] = $query;
			$this->queries_cnt++;
		}
	}

	/* Returns an array with the results, with columns as array indexes */
	public function getArray($query)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($query)) return array();

		$rows = $result->num_rows;
		
		$data = array();

		for ($i=0; $i<$rows; $i++) {
			$data[$i] = $result->fetch_array(MYSQLI_ASSOC);
		}

		$result->free();

		if ($this->debug) {
			$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
			$this->queries[ $this->queries_cnt ] = $query;
			$this->queries_cnt++;
		}

		return $data;
	}
	
	/* Returns one row-result with columns as array indexes */
	public function getOneRow($query)
	{
		if ($this->debug) $time_started = microtime(true);	

		if (!$result = $this->db_handle->query($query)) return array();

		if ($result->num_rows > 1) {
			die('ERROR: query '.$query.' in DB_MySQLi::getOneResult() returned more than 1 result!');
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);
		$result->free();

		if ($this->debug) {
			$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
			$this->queries[ $this->queries_cnt ] = $query;
			$this->queries_cnt++;
		}

		return $data;
	}
	
	/* Returns one column-result only (SELECT a FROM t WHERE id=1), where id is distinct */
	public function getOneItem($query)
	{
		if ($this->debug) $time_started = microtime(true);	

		if (!$result = $this->db_handle->query($query)) return array();

		if ($result->num_rows > 1) {
			die('ERROR: query '.$query.' in DB_MySQLi::getOneResult() returned more than 1 result!');
		}

		$data = $result->fetch_row();
		$result->free();

		if ($this->debug) {
			$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
			$this->queries[ $this->queries_cnt ] = $query;
			$this->queries_cnt++;
		}

		if (!$data) return false;
		return $data[0];
	}

}
?>