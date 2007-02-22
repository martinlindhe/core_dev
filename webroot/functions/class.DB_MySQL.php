<?
/*
	Object oriented interface for MySQL databases using the MySQL extension

	When possible, use class.DB_MySQLi.php instead

	Written by Martin Lindhe, 2007
*/

class DB_MySQL extends DB_Base
{
	//default settings
	protected $host	= 'localhost';
	protected $port	= 3306;
	protected $username = 'root';
	protected $password = '';
	protected $database = '';
	protected $db_handle = false;
	
	//debug variables
	protected $debug = false;
	protected $time_spent = array();
	protected $queries_cnt = 0;
	protected $queries = array();
	protected $query_error = array();
	
	public $insert_id = 0;

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
		if ($this->db_handle) mysql_close($this->db_handle);
	}

	/* Opens a database connection */
	protected function connect()
	{
		if ($this->debug) $time_started = microtime(true);

		//Open database connection
		$this->db_handle = @mysql_connect($this->host.':'.$this->port, $this->username, $this->password);

		if (mysqli_connect_errno()) {
			$this->db_handle = false;
			die('Database connection error.');
		}

		mysql_select_db($this->database, $this->db_handle);

		if ($this->debug) {
			$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
			$this->queries[ $this->queries_cnt ] = 'connect('.$this->host.'), '.mysql_get_host_info($this->db_handle);
			$this->queries_cnt++;
		}
	}

	/* Escapes a string for use in queries */
	public function escape($query)
	{
		return mysql_real_escape_string($query, $this->db_handle);
	}

	/* Performs a query that don't return anything */
	public function query($query)
	{
		if ($this->debug) $time_started = microtime(true);

		$result = mysql_query($query, $this->db_handle);

		if ($result) {
			$this->insert_id = mysql_insert_id($this->db_handle);
			mysql_free_result($result);
		} else if ($this->debug && !$result) {
			$this->query_error[ $this->queries_cnt ] = mysql_error($this->db_handle);
		} else {
			//if debug is turned off (production) and a query fail, just die silently
			die;
		}

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

		if (!$result = mysql_query($query, $this->db_handle)) return array();

		$rows = mysql_num_rows($result);

		$data = array();

		for ($i=0; $i<$rows; $i++) {
			$data[$i] = mysql_fetch_array($result, MYSQL_ASSOC);
		}

		mysql_free_result($result);

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

		if (!$result = mysql_query($query, $this->db_handle)) return array();

		if (mysql_num_rows($result) > 1) {
			die('ERROR: query '.$query.' in DB_MySQL::getOneRow() returned more than 1 result!');
		}

		$data = mysql_fetch_array($result, MYSQL_ASSOC);
		mysql_free_result($result);

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

		if (!$result = mysql_query($query, $this->db_handle)) return array();

		if (mysql_num_rows($result) > 1) {
			die('ERROR: query '.$query.' in DB_MySQL::getOneItem() returned more than 1 result!');
		}

		$data = mysql_fetch_row($result);
		mysql_free_result($result);

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