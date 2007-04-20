<?
/*
	SQL DB Base class

	Written by Martin Lindhe, 2007

	todo: metod för att anropa stored procedures
	
	todo: börja använda http://se2.php.net/manual/en/function.mysqli-fetch-object.php, returnera kolumnamn direkt som objektvariabler
*/

define('LOGLEVEL_NOTICE', 1);
define('LOGLEVEL_WARNING', 2);
define('LOGLEVEL_ERROR', 3);
define('LOGLEVEL_ALL', 5);

abstract class DB_Base
{
	/****************************************************/
	/* PUBLIC INTERFACE EXPOSED BY ALL DB MODULES				*/
	/****************************************************/

	/* Holds the ID of last successful INSERT */
	public $insert_id = 0;

	/* Holds a string indicating what "dialect" of sql is currently allowed by the db driver, possible values: mysql */
	public $dialect = '';

	/* Escapes a string for use in queries */
	abstract function escape($query);

	/* Performs a query that don't return anything
		Example: INSERT a=1 INTO t */
	abstract function query($query);
	
	/* Performs a query that does a DELETE, returns the number of rows affected
		Example: DELETE FROM t WHERE id=1 */
	abstract function delete($query);
	
	/* Returns an array with the results, with columns as array indexes
		Example: SELECT * FROM t */
	abstract function getArray($query);
	
	/* Returns an array with the results mapped as key => value
		Example: SHOW VARIABLES LIKE "%cache%" */
	abstract function getMappedArray($query);
	
	/* Returns an 1-dimensional array with a numeric index */
	abstract function getNumArray($query);

	/* Returns one row-result with columns as array indexes
		Example: SELECT * FROM t WHERE id=1 (where id is distinct) */
	abstract function getOneRow($query);

	/* Returns one column-result only
		Example: SELECT a FROM t WHERE id=1 (where id is distinct) */
	abstract function getOneItem($query);


	/****************************************************/
	/* PRIVATE INTERFACE USED INTERNALLY ONLY						*/
	/****************************************************/

	/* Creates a database connection */
	abstract protected function connect();
	
	/* Shows driver-specific settings + status */
	abstract function showDriverStatus();

	//default settings
	protected $host	= 'localhost';
	protected $port	= 3306;
	protected $username = 'root';
	protected $password = '';
	protected $database = '';

	//db variables
	public $db_handle = false;
	public $db_driver = '';				//holds the full name of the db driver, example: DB_MySQLi, DB_MySQL
	public $diaylect = '';					//holds the name of the database, example: mysql
	public $server_version = '';	//used for version checking
	public $client_version = '';	//used for version checking

	//debug variables
	public $debug = false;
	protected $connect_time = 0;
	protected $time_spent = array();
	protected $queries_cnt = 0;
	protected $queries = array();
	protected $query_error = array();
	
	
	/* Constructor */
	function __construct(array $settings)
	{
		if (!empty($settings['debug'])) $this->debug = $settings['debug'];
		if (!empty($settings['host'])) $this->host = $settings['host'];
		if (!empty($settings['port'])) $this->port = $settings['port'];
		if (!empty($settings['username'])) $this->username = $settings['username'];
		if (!empty($settings['password'])) $this->password = $settings['password'];
		if (!empty($settings['database'])) $this->database = $settings['database'];

		$this->connect();
	}

	/* Shows current settings */
	function showConfig()
	{
		echo '<b>Current database configuration</b><br/>';
		echo 'DB driver: <span class="okay">'.$this->db_driver.'</span><br/>';
		echo 'Host: '.$this->host.':'.$this->port.'<br/>';
		echo 'Login: '.$this->username.':'.($this->password?$this->password:'(blank)').'<br/>';
		echo 'Database: '.$this->database.'<br/>';
		echo 'Debug: '.($this->debug?'ON':'OFF').'<br/><br/>';

		echo '<b>DB driver specific settings</b><br/>';
		$this->showDriverStatus();
		
		if ($this->dialect == 'mysql') {
			/* Show MySQL query cache settings */
			$data = $this->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
			if ($data['have_query_cache'] == 'YES') {
				echo '<b>MySQL query cache settings</b><br/>';
				echo 'Type: '. $data['query_cache_type'].'<br/>';		//valid values: ON, OFF or DEMAND
				echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
				echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
				echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
				echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';
	
				/* Current query cache status */
				$data = $this->getMappedArray('SHOW STATUS LIKE "%Qcache%"', 'Variable_name', 'Value');
				echo '<b>MySQL query cache status</b><br/>';
				echo 'Hits: '. formatNumber($data['Qcache_hits']).'<br/>';
				echo 'Inserts: '. formatNumber($data['Qcache_inserts']).'<br/>';
				echo 'Queries in cache: '. formatNumber($data['Qcache_queries_in_cache']).'<br/>';
				echo 'Total blocks: '. formatNumber($data['Qcache_total_blocks']).'<br/>';
				echo '<br/>';
				echo 'Not cached: '. formatNumber($data['Qcache_not_cached']).'<br/>';
				echo 'Free memory: '. formatDataSize($data['Qcache_free_memory']).'<br/>';
				echo '<br/>';
				echo 'Free blocks: '. formatNumber($data['Qcache_free_blocks']).'<br/>';
				echo 'Lowmem prunes: '. formatNumber($data['Qcache_lowmem_prunes']).'<br/>';
			} else {
				echo '<b>MySQL Qcache is disabled!</b><br/>';
			}
		}
	}

	/* Stores profiling information about connect time to database */
	protected function profileConnect($time_started)
	{
		$this->connect_time = microtime(true) - $time_started;
	}

	/* Stores profiling information about query execution time */
	protected function profileQuery($time_started, $query)
	{
		$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
		$this->queries[ $this->queries_cnt ] = $query;
		$this->queries_cnt++;
	}

	protected function profileError($time_started, $query, $_error)
	{
		$this->query_error[ $this->queries_cnt ] = $_error;
		$this->profileQuery($time_started, $query);
	}

	/* Shows sql query profiling information */
	function showProfile($pageload_start = 0)
	{
		if (!$this->debug) return;

		$total_time = microtime(true) - $pageload_start;

		$rand_id = mt_rand(1,5000000);

		echo '<a href="#" onclick="return toggle_element_by_name(\'sql_profiling'.$rand_id.'\');">'.$this->queries_cnt.' sql</a>';

		//Shows all SQL queries from this page view
		$sql_height = ($this->queries_cnt+1)*21;
		if ($sql_height > 160) $sql_height = 160;

		$sql_time = 0;

		if (count($this->query_error)) $css_display = '';
		else $css_display = ' display: none;';

		echo '<div id="sql_profiling'.$rand_id.'" style="height:'.$sql_height.'px;'.$css_display.' overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';

		for ($i=0; $i<$this->queries_cnt; $i++)
		{
			$sql_time += $this->time_spent[$i];
			
			$query = htmlentities(nl2br($this->queries[$i]), ENT_COMPAT, 'UTF-8');

			echo '<div style="width: 45px; float: left;">';
				if (!empty($this->query_error[$i])) {
					echo '<img src="/gfx/icon_error.png" alt="SQL Error" title="SQL Error"/>';
				} else {
					echo round($this->time_spent[$i], 3).'s';
				}
			echo '</div> ';
			if (!empty($this->query_error[$i])) {
				echo '<b>'.$query.'</b><br/>';
				echo 'Error: <i>'.$this->query_error[$i].'</i>';
			} else {
				echo $query;
			}
			echo '<hr/>';
		}

		if ($pageload_start) {
			$php_time = $total_time - $sql_time;
			echo 'Total time spent: '.round($total_time, 3).'s '.' (SQL connect: '.round($this->connect_time, 3).'s, SQL queries: '.round($sql_time, 3).'s, PHP: '.round($php_time, 3).'s)';
		} else {
			echo 'Time spent - SQL: '.round($sql_time, 3);
		}
		echo '</div>';
	}
	
	/* Writes a log entry to tblLogs */
	function log($str, $entryLevel = LOGLEVEL_NOTICE)
	{
		global $session;
		if (!is_numeric($entryLevel)) return false;

		$enc_str = $this->escape($str);

		$userId = !empty($session) ? $session->id : 0;
		$userIP = !empty($session) ? $session->ip : IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);

		$this->query('INSERT INTO tblLogs SET entryText="'.$enc_str.'",entryLevel='.$entryLevel.',timeCreated=NOW(),userId='.$userId.',userIP='.$userIP);
	}

	/* Displays all events from the event log */
	function showEvents()
	{
		global $session;

		if (!$session->isAdmin) return false;

		if (isset($_GET['events_clearlog'])) {
			$this->query('DELETE FROM tblLogs WHERE entryLevel <= '.LOGLEVEL_ALL);
		}

		$list = $this->getArray('SELECT * FROM tblLogs WHERE entryLevel <= '.LOGLEVEL_ALL.' ORDER BY timeCreated DESC');
		echo count($list).' entries in event log<br/><br/>';

		for ($i=0; $i<count($list); $i++)
		{
			echo '<span class="event_log_header">';

			switch ($list[$i]['entryLevel']) {
				case LOGLEVEL_NOTICE:  echo 'Notice: '; break; 
				case LOGLEVEL_WARNING: echo 'Warning: '; break; 
				case LOGLEVEL_ERROR:   echo 'Error: '; break; 
				default: echo 'Errorx2: '; break;
			}

			echo '<b>Entry #'.$list[$i]['entryId'].'</b> - '.$list[$i]['entryText'].'</span><br/>';
			echo '<span class="event_log_author"><i>Generated by <b>';
			if ($list[$i]['userId']) echo $this->getUserName($list[$i]['userId']);
			else echo 'Unregistered';
			echo '</b> at '.$list[$i]['timeCreated'];

			$ip_v4 = GeoIP_to_IPv4($list[$i]['userIP']);
			echo ' from <b><a href="admin_ip.php?ip='.$ip_v4.'">'.$ip_v4.'</a></b>';

			echo '</i></span><br/><br/>';
		}

		echo '<a href="'.$_SERVER['PHP_SELF'].'?events_clearlog">Clear log</a>';
	}
	

	/* Looks up a username by id */
	function getUserName($_id)
	{
		if (!is_numeric($_id) || !$_id) return false;

		return $this->getOneItem('SELECT userName FROM tblUsers WHERE userId='.$_id);
	}

}

//General database related functions

	/* Returns current time in MySQL "NOW()" format */
	function now()
	{
		return strftime('%Y-%m-%d %H:%M:%S');
	}

	function sql_datetime($timestamp)
	{
		return date('Y-m-d H:i:s', $timestamp);
	}
?>