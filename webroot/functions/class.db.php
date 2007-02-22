<?
/*
	SQL DB Base class
	Written by Martin Lindhe, 2007

	todo:
		* räkna ej connect som en sql query i debug listan
		* baka in css direkt i funktionen, slipp externa dependencies
*/

abstract class DB_Base
{
	abstract protected function connect();
	abstract public public function escape($query);
	abstract public public function query($query);
	abstract public public function getArray($query);
	abstract public public function getOneRow($query);
	abstract public public function getOneItem($query);
	
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