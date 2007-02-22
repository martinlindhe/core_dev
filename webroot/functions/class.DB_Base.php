<?
/*
	SQL DB Base class

	Written by Martin Lindhe, 2007
*/

abstract class DB_Base
{
	//default settings
	protected $host	= 'localhost';
	protected $port	= 3306;
	protected $username = 'root';
	protected $password = '';
	protected $database = '';
	protected $db_handle = false;
	protected $db_driver = '';

	//debug variables
	protected $debug = false;
	protected $connect_time = 0;
	protected $time_spent = array();
	protected $queries_cnt = 0;
	protected $queries = array();
	protected $query_error = array();

	public $insert_id = 0;

	abstract protected function connect();
	abstract public function escape($query);
	abstract public function query($query);
	abstract public function getArray($query);
	abstract public function getOneRow($query);
	abstract public function getOneItem($query);

	/* Stores profiling information during page load. Show result with showDebugInfo() */
	protected function profileQuery($time_started, $query)
	{
		$this->time_spent[ $this->queries_cnt ] = microtime(true) - $time_started;
		$this->queries[ $this->queries_cnt ] = $query;
		$this->queries_cnt++;
	}

	protected function profileConnect($time_started)
	{
		$this->connect_time = microtime(true) - $time_started;
	}

	/* Shows current settings */
	public function showSettings()
	{
		echo 'Debug: '.($this->debug?'ON':'OFF').'<br>';
		echo 'DB driver: '.$this->db_driver.'<br>';
		echo 'Host: '.$this->host.':'.$this->port.'<br>';
		echo 'Login: '.$this->username.':'.$this->password.'<br>';
		echo 'Database: '.$this->database.'<br>';
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

		echo '<div id="debug_layer" style="height:'.$sql_height.'px; display: none; overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000000 1px solid; font: 9px verdana;">';

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
			echo 'Total time spent: '.round($total_time, 3).'s '.' (SQL connect: '.round($this->connect_time, 3).'s, SQL queries: '.round($sql_time, 3).'s, PHP: '.round($php_time, 3).'s)';
		} else {
			echo 'Time spent - SQL: '.round($sql_time, 3);
		}
		echo '</div>';
	}

}
?>