<?
/**
 * $Id$
 *
 * SQL DB Base class
 *
 * The SQL profiler features additional PHP profiling if the xdebug extension is loaded
 *
 * \todo Make a test script to verify each of the database classes returns data as expected
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_core.php');	//for makePager()

abstract class DB_Base
{
	/****************************************************/
	/* PUBLIC INTERFACE EXPOSED BY ALL DB MODULES				*/
	/****************************************************/

	/**
	 * Escapes a string for use in queries
	 * \param $q is the query to escape
	 * \return the escaped string, taking db-connection locale into account
	 */
	abstract function escape($q);

	/**
	 * Performs a general query. Should only be used with special commands that don't return anything.
	 * Use the other functions for common SQL operations such as select, insert, update, delete
	 * Example: LOCK TABLES
	 * \param $q is the query to execute
	 * \return the result of the query, if anything
	 */
	abstract function query($q);

	/**
	 * Performs a query that does a DELETE
	 * Example: DELETE FROM t WHERE id=1
	 * \param $q is the query to execute
	 * \return the number of rows affected
	 */
	abstract function delete($q);

	/**
	 * Performs a query that does a UPDATE
	 * Example: UPDATE t SET n=1
	 * \param $q is the query to execute
	 * \return the number of rows affected
	 */
	function update($q) { return $this->delete($q); }

	/**
	 * Performs a query that does a INSERT
	 * \param $q is the query to execute
	 * \return insert_id (autoincrement primary key of table)
	 */
	abstract function insert($q);

	/**
	 * Performs a query that does a REPLACE
	 * \param $q is the query to execute
	 * \return the number of rows affected
	 */
	function replace($q) { return $this->insert($q); }

	/**
	 * Selects data
	 * Example: SELECT * FROM t
	 * \param $q is the query to execute
	 * \return an array with the results, with columns as array indexes
	 */
	abstract function getArray($q);

	/**
	 * Selects data
	 * Example: SHOW VARIABLES LIKE "%cache%"
	 * \param $q is the query to execute
	 * \return an array with the results mapped as key => value
	 */
	abstract function getMappedArray($q);

	/**
	 * Selects data
	 * Example: SELECT textRow FROM t
	 * \param $q is the query to execute
	 * \return an 1-dimensional array with a numeric index
	 */
	abstract function getNumArray($q);

	/**
	 * Selects one row of data
	 * Example: SELECT * FROM t WHERE id=1 (where id is distinct)
	 * \param $q is the query to execute
	 * \return one row-result with columns as array indexes
	 */
	abstract function getOneRow($q);

	/**
	 * Selects one column of one row of data
	 * Example: SELECT a FROM t WHERE id=1 (where id is distinct)
	 * \param $q is the query to execute
	 * \param $num set to true if you expect a numeric response (to return 0 on failure rather than boolean false)
	 * \return one column-result only
	 */
	abstract function getOneItem($q, $num = false);



	/****************************************************/
	/* PRIVATE INTERFACE USED INTERNALLY ONLY						*/
	/****************************************************/

	/**
	 * Creates a database connection
	 * \return nothing
	 */
	abstract function connect();

	/**
	 * Shows driver-specific settings + status
	 * \return nothing
	 */
	abstract function showDriverStatus();


	//db settings
	protected $host	= '';					///<Hostname or numeric IP address of the db server
	protected $port	= 0;					///<Port number
	protected $username = '';			///<Username to use to connect to the database
	protected $password = '';			///<Password to use to connect to the database
	protected $database = '';			///<Name of the database to connect to
	protected $charset = 'utf8';	///<Default charset to use. utf8 should be used always

	//db variables
	public $db_handle = false;		///<Internal db handle
	public $db_driver = '';				///<holds the full name of the db driver, example: DB_MySQLi, DB_MySQL
	public $dialect = '';					///<tells what dialect of sql is currently allowed by the db driver, possible values: mysql, pgsql
	public $server_version = '';	///<used for version checking
	public $client_version = '';	///<used for version checking

	//profiling variables
	protected $connect_time = 0;			///<Used internally for the SQL profiler
	protected $time_spent = array();	///<Used internally for the SQL profiler
	protected $queries_cnt = 0;				///<Used internally for the SQL profiler
	protected $queries = array();			///<Used internally for the SQL profiler
	protected $query_error = array();	///<Used internally for the SQL profiler


	/**
	 * Constructor. Initializes db driver and connects to the database
	 * \param $settings is array with DB-specific settings
	 * \return nothing
	 */
	function __construct(array $settings)
	{
		if (!empty($settings['host'])) $this->host = $settings['host'];
		if (!empty($settings['port'])) $this->port = $settings['port'];
		if (!empty($settings['username'])) $this->username = $settings['username'];
		if (!empty($settings['password'])) $this->password = $settings['password'];
		if (!empty($settings['database'])) $this->database = $settings['database'];
		if (!empty($settings['charset'])) $this->charset = $settings['charset'];

		$this->connect();
	}

	/**
	 * Shows current settings
	 * \return nothing
	 */
	function showConfig()
	{
		echo '<div class="item">';
		echo '<h2>Current database configuration</h2>';
		echo 'DB driver: '.$this->db_driver.'<br/>';
		echo 'Server version: '.$this->server_version.'<br/>';
		echo 'Client version: '.$this->client_version.'<br/>';
		echo 'Host: '.$this->host.':'.$this->port.'<br/>';
		echo 'Login: '.$this->username.':'.($this->password ? $this->password : '(blank)').'<br/>';
		echo 'Database: '.$this->database.'<br/>';
		echo 'Configured charset: '.$this->charset;
		echo '</div><br/>';

		echo '<div class="item">';
		echo '<h2>DB host features</h2>';
		$db_time = $this->getOneItem('SELECT NOW()');
		echo 'DB time: '.$db_time.' (webserver time: '.now().')<br/>';
		echo '</div><br/>';

		echo '<div class="item">';
		echo '<h2>DB driver specific settings</h2>';
		$this->showDriverStatus();
		echo '</div><br/>';

		echo '<div class="item">';
		if ($this->dialect == 'mysql') {
			/* Show MySQL query cache settings */
			$data = $this->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
			if ($data['have_query_cache'] == 'YES') {
				echo '<h2>MySQL query cache settings</h2>';
				echo 'Type: '. $data['query_cache_type'].'<br/>';		//valid values: ON, OFF or DEMAND
				echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
				echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
				echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
				echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';
		
				/* Current query cache status */
				$data = $this->getMappedArray('SHOW STATUS LIKE "%Qcache%"', 'Variable_name', 'Value');
				echo '<h2>MySQL query cache status</h2>';
				echo 'Hits: '. formatNumber($data['Qcache_hits']).'<br/>';
				echo 'Inserts: '. formatNumber($data['Qcache_inserts']).'<br/>';
				echo 'Queries in cache: '. formatNumber($data['Qcache_queries_in_cache']).'<br/>';
				echo 'Total blocks: '. formatNumber($data['Qcache_total_blocks']).'<br/>';
				echo '<br/>';
				echo 'Not cached: '. formatNumber($data['Qcache_not_cached']).'<br/>';
				echo 'Free memory: '. formatDataSize($data['Qcache_free_memory']).'<br/>';
				echo '<br/>';
				echo 'Free blocks: '. formatNumber($data['Qcache_free_blocks']).'<br/>';
				echo 'Lowmem prunes: '. formatNumber($data['Qcache_lowmem_prunes']);
			} else {
				echo '<h2>MySQL Qcache is disabled!</h2>';
			}
		}
		echo '</div>';
	}

	/**
	 * Stores profiling information about connect time to database
	 * \param $time_started is the microtime of when the script execution started
	 * \return nothing
	 */
	function profileConnect($time_started)
	{
		$this->connect_time = microtime(true) - $time_started;
	}

	/**
	 * Stores profiling information about query execution time
	 * \param $time_started is microtime from when the execution of this query begun
	 * \param $q is the query being profiled
	 * \return nothing
	 */
	function profileQuery($time_started, $q)
	{
		$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
		$this->queries[ $this->queries_cnt ] = $q;
		$this->queries_cnt++;
	}

	/**
	 * Stores profiling information about a failed query execution
	 * \param $time_started is microtime from when the execution of this query begun
	 * \param $q is the query being profiled
	 * \param $err is the error message returned by the db driver in use
	 * \return nothing
	 */
	function profileError($time_started, $q, $err)
	{
		$this->query_error[ $this->queries_cnt ] = $err;
		$this->profileQuery($time_started, $q);
	}

	/**
	 * Shows SQL query profiling information
	 * \param $pageload_start is the microtime of when the script execution started
	 * \return nothing
	 */
	function showProfile($pageload_start = 0)
	{
		global $config;
		if (!$config['debug']) return;
		
		if (extension_loaded('xdebug')) {
			$total_time = xdebug_time_index();
		} else {
			$total_time = microtime(true) - $pageload_start;
		}

		$rand_id = mt_rand(1,5000000);

		echo '<a href="#" onclick="return toggle_element_by_name(\'sql_profiling'.$rand_id.'\');">'.$this->queries_cnt.' sql</a>';

		//Shows all SQL queries from this page view
		$sql_height = ($this->queries_cnt+1)*40;
		if ($sql_height > 400) $sql_height = 400;

		$sql_time = 0;

		if (count($this->query_error)) $css_display = '';
		else $css_display = ' display: none;';

		echo '<div id="sql_profiling'.$rand_id.'" style="height:'.$sql_height.'px;'.$css_display.' overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';

		for ($i=0; $i<$this->queries_cnt; $i++)
		{
			$sql_time += $this->time_spent[$i];

			$query = htmlentities(nl2br($this->queries[$i]), ENT_COMPAT, 'UTF-8');

			$sql_syntax = array('SET', 'WHERE', 'LEFT', 'GROUP', 'ORDER');
			$encoded_syntax = array('<br/>SET', '<br/>WHERE', '<br/>LEFT', '<br/>GROUP', '<br/>ORDER');
			$query = str_replace($sql_syntax, $encoded_syntax, $query);

			echo '<table summary=""><tr><td width="40">';
			if (!empty($this->query_error[$i])) {
				echo '<img src="'.$config['core_web_root'].'gfx/icon_error.png" alt="SQL Error" title="SQL Error"/>';
			} else {
				echo round($this->time_spent[$i], 3).'s';
			}
			echo '</td><td>';
			if (!empty($this->query_error[$i])) {
				echo '<b>'.$query.'</b><br/>';
				echo 'Error: <i>'.$this->query_error[$i].'</i>';
			} else {
				echo $query;
			}
			echo '</tr></table>';
			echo '<hr/>';
		}

		if ($pageload_start) {
			$php_time = $total_time - $this->connect_time - $sql_time;
			echo 'Total time spent: '.round($total_time, 3).'s '.' (SQL connect: '.round($this->connect_time, 3).'s, SQL queries: '.round($sql_time, 3).'s, PHP: '.round($php_time, 3).'s)<br/>';
		} else {
			echo 'Time spent - SQL: '.round($sql_time, 3).'<br/>';
		}

		if (extension_loaded('xdebug')) {
			//Show script memory usage
			echo 'Memory usage peaked at '.formatDataSize(xdebug_peak_memory_usage());
			echo ', currently '.formatDataSize(xdebug_memory_usage());
		}
		echo '</div>';
	}

	/**
	 * Displays all events from the event log
	 * \return nothing
	 */
	function showEvents()
	{
		global $session, $config;
		if (!$session->isAdmin) return false;

		if ($session->isSuperAdmin && isset($_GET['events_clearlog'])) {
			$this->query('TRUNCATE tblLogs');
		}

		$q = 'SELECT COUNT(*) FROM tblLogs WHERE entryLevel <= '.LOGLEVEL_ALL;
		$cnt = $this->getOneItem($q);

		$pager = makePager($cnt, 15);

		$q  = 'SELECT t1.*,t2.userName FROM tblLogs AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$q .= 'WHERE t1.entryLevel <= '.LOGLEVEL_ALL;

		if (!empty($_GET['sort']) && $_GET['sort']=='asc') {
			$q .= ' ORDER BY t1.timeCreated ASC,t1.entryId ASC';
			echo 'Showing oldest first - [<a href="'.$_SERVER['PHP_SELF'].getProjectPath(0).'">show newest first</a>]<br/>';
		} else {
			$q .= ' ORDER BY t1.timeCreated DESC,t1.entryId DESC';
			echo 'Showing newest first - [<a href="'.$_SERVER['PHP_SELF'].'?sort=asc'.getProjectPath().'">show oldest first</a>]<br/>';
		}
		$q .= $pager['limit'];

		$list = $this->getArray($q);
		echo $pager['head'];

		foreach ($list as $row)
		{
			switch ($row['entryLevel']) {
				case LOGLEVEL_NOTICE:  echo '<div class="event_log_notice">'; break;
				case LOGLEVEL_WARNING: echo '<div class="event_log_warning">Warning: '; break;
				case LOGLEVEL_ERROR:   echo '<div class="event_log_error">Error: '; break;
				default: die('Errorx2');
			}

			echo $row['entryText'].'<br/>';
			echo '<i>Generated by <b>';
			if ($row['userId']) echo Users::link($row['userId'], $row['userName']);
			else echo 'Unregistered';
			echo '</b> at '.$row['timeCreated'];

			echo ' from <a href="'.$config['core_web_root'].'admin/admin_ip.php?ip='.GeoIP_to_IPv4($row['userIP']).getProjectPath().'">'.GeoIP_to_IPv4($row['userIP']).'</a>';
			echo '</i></div><br/>';
		}

		if ($session->isSuperAdmin) echo '<a href="'.$_SERVER['PHP_SELF'].'?events_clearlog'.getProjectPath().'">Clear log</a>';
	}
}


/*******************************
* General sql helper functions *
*******************************/

	/**
	 * Returns the current time in the same format as the MySQL "NOW()" command
	 * \return time in MySQL datetime format
	 */
	function now()
	{
		return strftime('%Y-%m-%d %H:%M:%S');
	}

	/**
	 * Returns given UNIX timestamp in MySQL datetime format
	 * \param $timestamp is a UNIX timestamp
	 * \return given UNIX timestamp in MySQL datetime format
	 */
	function sql_datetime($timestamp)
	{
		return date('Y-m-d H:i:s', $timestamp);
	}
	
	/**
	 * Returns MySQL datetime in UNIX timestamp format
	 * \param $datetime is a MySQL datetime
	 * \return given MySQL datetime in UNIX timestamp format
	 */
	function datetime_to_timestamp($datetime)
	{
		return strtotime($datetime);
	}

	/**
	 * Compares two MySQL datetime timestamps
	 * \param $d1 is a MySQL datetime
	 * \param $d2 is a MySQL datetime
	 * \return true if $d1 is older date than $d2
	 */
	function datetime_less($d1, $d2)
	{
		if (strtotime($d1) < strtotime($d2)) return true;
		return false;
	}
?>