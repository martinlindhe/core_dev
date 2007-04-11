<?
/*
	SQL DB Base class

	Written by Martin Lindhe, 2007

	TODO:!!!!! en funktion som returnerar mysql_num_rows av angiven query enbart 	
	todo: metod för att anropa stored procedures
	todo: rename GetArray() till Array() ? eller är det för confusing med array() datatyp ?
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

	/* Escapes a string for use in queries */
	abstract public function escape($query);

	/* Performs a query that don't return anything
		Example: INSERT a=1 INTO t */
	abstract public function query($query);
	
	/* Returns the number of rows used in previous query */
	abstract public function num_rows(&$resultset);

	/* Returns an array with the results, with columns as array indexes
		Example: SELECT * FROM t */
	abstract public function getArray($query);

	/* Returns one row-result with columns as array indexes
		Example: SELECT * FROM t WHERE id=1 (where id is distinct) */
	abstract public function getOneRow($query);

	/* Returns one column-result only
		Example: SELECT a FROM t WHERE id=1 (where id is distinct) */
	abstract public function getOneItem($query);


	/****************************************************/
	/* PRIVATE INTERFACE USED INTERNALLY ONLY						*/
	/****************************************************/

	/* Creates a database connection */
	abstract protected function connect();
	
	/* Shows driver-specific settings */
	abstract protected function showMoreSettings();

	//default settings
	protected $host	= 'localhost';
	protected $port	= 3306;
	protected $username = 'root';
	protected $password = '';
	protected $database = '';
	protected $db_handle = false;
	protected $db_driver = '';

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
	function showSettings()
	{
		echo 'Debug: '.($this->debug?'ON':'OFF').'<br/>';
		echo 'DB driver: '.$this->db_driver.'<br/>';
		echo 'Host: '.$this->host.':'.$this->port.'<br/>';
		echo 'Login: '.$this->username.':'.$this->password.'<br/>';
		echo 'Database: '.$this->database.'<br/>';

		$this->showMoreSettings();
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

	/* Shows profiling information */
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

		echo '<div id="sql_profiling'.$rand_id.'" style="height:'.$sql_height.'px; display: none; overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';

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
		
		echo '<b>LOG: '.$str.'</b><br/>';

		//if ($session) {
			$this->query('INSERT INTO tblLogs SET entryText="'.$enc_str.'",entryLevel='.$entryLevel.',timeCreated=NOW(),userId='.$session->id.',userIP='.$session->ip);
		//} else {
			//echo 'WARNING - CANNOT LOG with session info!<br/>';
			//$this->query('INSERT INTO tblLogs SET entryText="'.$enc_str.'",entryLevel='.$entryLevel.',timeCreated=NOW(),userId=0,userIP=0');
		//}
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
		echo '<br/>'.count($list).' entries in event log<br/><br/>';

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
?>