<?
//Object oriented interface for MySQL databases using the MySQLi extension
//other SQL-database interfaces should use the same interface

/*
	todo: gör en base-class med generella funktioner, och använd "class DB_MySQLi extends DB_Base"

	todo: kan debug-infon flyttas till base-classen?
	
	todo: räkna ej connect som en sql query i debug listan
	todo: baka in css direkt i funktionen, slipp externa dependencies
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
	private $query_error = array();
	
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

	/* Escapes a string for use in queries */
	public function escape($query)
	{
		return $this->db_handle->real_escape_string($query);
	}
	
	/* Performs a query that don't return anything */
	public function query($query)
	{
		if ($this->debug) $time_started = microtime(true);

		$result = $this->db_handle->query($query);

		if ($result) {
			$this->insert_id = $this->db_handle->insert_id;
		} else if ($this->debug && !$result) {
			$this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
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

	/* Shows current settings */
	public function showSettings()
	{
		echo 'Debug: '.($this->debug?'ON':'OFF').'<br>';
		echo 'Host: '.$this->host.':'.$this->port.'<br>';
		echo 'Login: '.$this->username.':'.$this->password.'<br>';
		echo 'Database: '.$this->database.'<br>';
		echo 'Host info: '. $this->db_handle->host_info.'<br>';
	}

	/* Shows debug/profiling information */
	public function showDebugInfo($pageload_start = 0)
	{
		if (!$this->debug) return;
		
		$total_time = microtime(true) - $pageload_start;
		
		echo '<a href="#" onClick="return toggle_element_by_name(\'debug_layer\');">'.$this->queries_cnt.' sql</a>';

		//Shows all SQL queries from this page view
		$sql_height = $this->queries_cnt*30;
		if ($sql_height > 160) $sql_height = 160;

		$sql_time = 0;

		echo '<div id="debug_layer" style="height:'.$sql_height.'px; display: none; font-family: verdana; font-size: 9px;">';
		for ($i=0; $i<$this->queries_cnt; $i++)
		{
			$sql_time += $this->time_spent[$i];
				
			echo '<div style="width: 50px; float: left;">';
				echo round($this->time_spent[$i], 3).'s';
				if (!empty($this->query_error[$i])) echo '<img src="design/delete.png" title="'.$this->query_error[$i].'">';
			echo '</div> ';
			echo htmlentities(nl2br($this->queries[$i]), ENT_COMPAT, 'UTF-8');
			echo '<hr>';
		}

		if ($pageload_start) {
			$php_time = $total_time - $sql_time;
			echo 'Total time spent: '.round($total_time, 3).'s '.' (SQL: '.round($sql_time, 3).'s, PHP: '.round($php_time, 3).'s)';
		} else {
			echo 'Time spent - SQL: '.round($sql_time, 3);
		}
		echo '</div>';
	}

}
?>