<?php
/**
 * $Id$
 *
 * SQL DB Base class
 *
 * The SQL profiler features additional PHP profiling if the xdebug extension is loaded
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//STATUS: ok

//TODO Make a test script to verify each of the database classes returns data as expected
//TODO: only mysqli driver uses $this->connected, fix the rest!

//TODO: use register_shutdown_function('myfunc') to add profiler at end of script if debug=true
//TODO: move out the profiling stuff from here

abstract class db_base extends CoreBase
{
	//db settings
	protected $host	     = '';      ///< Hostname or numeric IP address of the db server
	protected $port	     = 0;       ///< Port number
	protected $username  = '';      ///< Username to use to connect to the database
	protected $password  = '';      ///< Password to use to connect to the database
	protected $database  = '';      ///< Name of the database to connect to
	protected $charset   = 'utf8';  ///< Default charset to use. utf8 should be used always
	protected $connected = false;   ///< Are we connected to the db?

	//db variables
	public $db_handle      = false; ///< Internal db handle
	public $driver         = '';    ///< holds the full name of the db driver, example: DB_MySQLi, DB_MySQL
	public $dialect        = '';    ///< tells what dialect of sql is currently allowed by the db driver, possible values: mysql, pgsql
	public $server_version = '';    ///< used for version checking
	public $client_version = '';    ///< used for version checking

	//profiling variables
	public $time_initial = 0;       ///< profiler: microtime for db instance
	public $time_measure = 0;       ///< profiler: time when profiling started
	public $time_connect = 0;       ///< profiler: time it took to connect to db
	public $time_spent   = array(); ///< Used internally for the SQL profiler
	public $queries_cnt  = 0;       ///< Used internally for the SQL profiler
	public $queries      = array(); ///< Used internally for the SQL profiler
	public $query_error  = array(); ///< Used internally for the SQL profiler

	function getErrorCount() { return count($this->query_error); }

	/****************************************************/
	/* PUBLIC INTERFACE EXPOSED BY ALL DB MODULES       */
	/****************************************************/

	/**
	 * Escapes a string for use in queries
	 *
	 * @param $q is the query to escape
	 * @return the escaped string, taking db-connection locale into account
	 */
	abstract function escape($q);

	/**
	 * Performs a general query. Should only be used with special commands that don't return anything.
	 * Use the other functions for common SQL operations such as select, insert, update, delete
	 * Example: LOCK TABLES
	 *
	 * @param $q is the query to execute
	 * @return the result of the query, if anything
	 */
	abstract function query($q);

	/**
	 * Performs a query that does a DELETE
	 * Example: DELETE FROM t WHERE id=1
	 *
	 * @param $q is the query to execute
	 * @return the number of rows affected
	 */
	abstract function delete($q);

	/**
	 * Performs a query that does a UPDATE
	 * Example: UPDATE t SET n=1
	 *
	 * @param $q is the query to execute
	 * @return the number of rows affected
	 */
	function update($q) { return $this->delete($q); }

	/**
	 * Performs a query that does a INSERT
	 *
	 * @param $q is the query to execute
	 * @return insert_id (autoincrement primary key of table)
	 */
	abstract function insert($q);

	/**
	 * Performs a query that does a REPLACE
	 *
	 * @param $q is the query to execute
	 * @return the number of rows affected
	 */
	function replace($q) { return $this->insert($q); }

	/**
	 * Selects data
	 * Example: SELECT * FROM t
	 *
	 * @param $q is the query to execute
	 * @return an array with the results, with columns as array indexes
	 */
	abstract function getArray($q);

	/**
	 * Selects data, 1 column result
	 * Example: SELECT val FROM t WHERE id=3
	 */
	abstract function get1dArray($q);

	/**
	 * Selects data
	 * Example: SHOW VARIABLES LIKE "%cache%"
	 *
	 * @param $q is the query to execute
	 * @return an array with the results mapped as key => value
	 */
	abstract function getMappedArray($q);

	/**
	 * Selects data
	 * Example: SELECT textRow FROM t
	 *
	 * @param $q is the query to execute
	 * @return an 1-dimensional array with a numeric index
	 */
	abstract function getNumArray($q);

	/**
	 * Selects one row of data
	 * Example: SELECT * FROM t WHERE id=1 (where id is distinct)
	 *
	 * @param $q is the query to execute
	 * @return one row-result with columns as array indexes
	 */
	abstract function getOneRow($q);

	/**
	 * Selects one column of one row of data
	 * Example: SELECT a FROM t WHERE id=1 (where id is distinct)
	 *
	 * @param $q is the query to execute
	 * @return one column-result only
	 */
	abstract function getOneItem($q);

	/**
	 * Lock db/table helper function
	 *
	 * @param $t table to lock
	 */
	abstract function lock($t);

	/**
	 * Unlock db/table helper function
	 */
	abstract function unlock();


	/****************************************************/
	/* PRIVATE INTERFACE USED INTERNALLY ONLY						*/
	/****************************************************/

	/**
	 * Creates a database connection
	 */
	abstract function connect();

	/**
	 * Shows driver-specific settings + status
	 */
	abstract function status();


	/**
	 * Constructor. Initializes db driver and connects to the database
	 *
	 * @param $settings is array with DB-specific settings
	 */
	function __construct(array $conf)
	{
		global $config;

		$this->time_initial = microtime(true);

		if (!empty($conf['host']))     $this->host     = $conf['host'];
		if (!empty($conf['port']))     $this->port     = $conf['port'];
		if (!empty($conf['username'])) $this->username = $conf['username'];
		if (!empty($conf['password'])) $this->password = $conf['password'];
		if (!empty($conf['database'])) $this->database = $conf['database'];
		if (!empty($conf['charset']))  $this->charset  = $conf['charset'];
		if (!empty($conf['debug']))    $this->debug    = $conf['debug'];
	}

	/**
	 * Shows current settings
	 */
	function showConfig()
	{
		echo '<div class="item">';
		echo '<h2>Current database configuration</h2>';
		echo 'DB driver: '.$this->driver.'<br/>';
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
			//Show MySQL query cache settings
			$data = $this->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
			if ($data['have_query_cache'] == 'YES') {
				echo '<h2>MySQL query cache settings</h2>';
				echo 'Type: '. $data['query_cache_type'].'<br/>';		//valid values: ON, OFF or DEMAND
				echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
				echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
				echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
				echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';

				//Current query cache status
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
				echo '<h2>MySQL query cache is disabled</h2>';
			}
		}
		echo '</div>';
	}

	/**
	 * Saves time for profiling current action (connect, execute query, ...)
	 */
	function measure_time()
	{
		if (!$this->debug) return;
		$this->time_measure = microtime(true);
	}

	/**
	 * Calculates the time it took to connect to database
	 */
	function measure_connect()
	{
		$this->time_connect = microtime(true) - $this->time_measure;
	}

	/**
	 * Calculates the time it took to execute a query
	 */
	function measure_query($q)
	{
		if (!$this->debug) return;
		$this->time_spent[ $this->queries_cnt ] = microtime(true) - $this->time_measure;
		$this->queries[ $this->queries_cnt ] = $q;
		$this->queries_cnt++;
	}

	/**
	 * Stores profiling information about a failed query execution
	 *
	 * @param $time_started is microtime from when the execution of this query begun
	 * @param $q is the query being profiled
	 * @param $err is the error message returned by the db driver in use
	 */
	function profileError($q, $err)
	{
		$this->query_error[ $this->queries_cnt ] = $err;
		$this->measure_query($q);
	}

	/**
	 * Shows SQL query profiling information
	 */
	function showProfile()
	{
		global $config;
		if (!$this->debug) return;

		$rand_id = mt_rand(1,5000000);

		echo '<a href="#" onclick="return toggle_element(\'sql_profiling'.$rand_id.'\');">'.$this->queries_cnt.' sql</a>';

		//Shows all SQL queries from this page view
		$sql_height = ($this->queries_cnt*60)+60;
		if ($sql_height > 400) $sql_height = 400;

		$css_display = count($this->query_error) ? '' : ' display:none;';

		echo '<div id="sql_profiling'.$rand_id.'" style="height:'.$sql_height.'px;'.$css_display.' overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';

		$sql_time = 0;
		for ($i=0; $i<$this->queries_cnt; $i++)
		{
			$sql_time += $this->time_spent[$i];

			$query = htmlentities(nl2br($this->queries[$i]), ENT_COMPAT, 'UTF-8');

			$keywords = array(
			'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ',
			' FROM ', ' SET ', ' WHERE ', ' LEFT JOIN ', ' GROUP BY ', ' ORDER BY ',
			' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ',
			' IS NULL', ' NOT NULL ', ' DESC', ' ASC',
			' != ',
			'NOW()', 'COUNT',
			);
			$decorated = array(
			'<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ',
			'<br/><b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ', '<br/><b>LEFT JOIN</b> ', '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
			' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ',
			' <b>IS NULL</b>', ' <b>NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
			' <b>!=</b> ',
			'<b>NOW()</b>', '<b>COUNT</b>',
			);
			$query = str_replace($keywords, $decorated, $query);

			echo '<table summary=""><tr><td width="40">';
			if (!empty($this->query_error[$i])) {
				echo coreButton('Error', '', 'SQL Error');
			} else {
				echo round($this->time_spent[$i], 2).'s';
			}
			echo '</td><td>';

			if (!empty($this->query_error[$i]))
				echo 'Error: <b>'.$this->query_error[$i].'</b><br/><br/>';

			echo $query;
			echo '</td></tr></table>';
			echo '<hr/>';
		}

		$total_time = microtime(true) - $this->time_initial + $sql_time + $this->time_connect;
		$php_time = $total_time - $sql_time - $this->time_connect;

		echo 'Time spent: <b>'.round($total_time, 2).'s</b> '.
			' (DB connect: '.round($this->time_connect, 2).'s, '.
			sizeof($this->queries).' SQL queries: '.round($sql_time, 2).'s, '.
			'PHP: '.round($php_time, 2).'s)<br/>';

		//Show memory usage
		echo dm().'<br/>';

		echo '<b>'.$this->host.'</b> running <i>MySQL '.$this->server_version.'</i><br/>';
		echo '<b>'.$_SERVER['SERVER_NAME'].'</b> running <i>PHP '.phpversion().'</i><br/>';
		echo date('r');

		echo '</div>';
	}
}
?>
