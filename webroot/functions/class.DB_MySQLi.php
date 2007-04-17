<?
/*
	Object oriented interface for MySQL databases using the php_mysqli.dll extension

	Written by Martin Lindhe, 2007
*/

require_once('class.DB_Base.php');

class DB_MySQLi extends DB_Base
{
	function __destruct()
	{
		if ($this->db_handle) $this->db_handle->close();
	}
	
	function showDriverStatus()
	{
		echo 'Server info: '.$this->db_handle->server_info.' ('.$this->db_handle->host_info.')<br/>';
		echo 'Client info: '.$this->db_handle->client_info.'<br/>';
		echo 'Character set: '.$this->db_handle->character_set_name().'<br/>';
		echo 'Last error: '.$this->db_handle->error.'<br/>';
		echo 'Last errno: '.$this->db_handle->errno.'<br/><br/>';

		/* Show MySQL query cache settings */
		$data = $this->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
		if ($data['have_query_cache'] == 'YES') {
			echo '<b>MySQL query cache settings:</b><br/>';
			echo 'Type: '. $data['query_cache_type'].'<br/>';		//valid values: ON, OFF or DEMAND
			echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
			echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
			echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
			echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';

			/* Current query cache status */
			$data = $this->getMappedArray('SHOW STATUS LIKE "%Qcache%"', 'Variable_name', 'Value');
			echo '<b>MySQL query cache status:</b><br/>';
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

	function escape($query)
	{
		return $this->db_handle->real_escape_string($query);
	}

	protected function connect()
	{
		if ($this->debug) $time_started = microtime(true);

		$this->db_driver = 'DB_MySQLi';
		$this->dialect = 'mysql';

		$this->db_handle = @ new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

		if (mysqli_connect_errno()) {
			$this->db_handle = false;

			die('<bad>Database connection error.</bad>');
		}
		
		if ($this->debug) $this->profileConnect($time_started);
	}

	function query($q)
	{
		if ($this->debug) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		if ($result) {
			$this->insert_id = $this->db_handle->insert_id;
		} else if ($this->debug && !$result) {
			$this->insert_id = 0;
			$this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
		} else {
			//if debug is turned off (production) and a query fail, just die silently
			die;
		}

		if ($this->debug) $this->profileQuery($time_started, $q);
		
		return $result;
	}
	
	function getArray($query)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($query)) {
			if ($this->debug) $this->profileError($time_started, $query, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $query);

		return $data;
	}

	function getMappedArray($query)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($query)) {
			if ($this->debug) $this->profileError($time_started, $query, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {
			$data[ $row[0] ] = $row[1];
		}

		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $query);

		return $data;
	}

	function getNumArray($query)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($query)) {
			if ($this->debug) $this->profileError($time_started, $query, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {
			$data[] = $row[0];
		}

		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $query);

		return $data;
	}

	function getOneRow($query)
	{
		if ($this->debug) $time_started = microtime(true);	

		if (!$result = $this->db_handle->query($query)) {
			if ($this->debug) $this->profileError($time_started, $query, $this->db_handle->error);
			return array();
		}

		if ($result->num_rows > 1) {
			die('ERROR: query '.$query.' in DB_MySQLi::getOneRow() returned more than 1 result!');
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);
		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $query);

		return $data;
	}

	function getOneItem($query)
	{
		if ($this->debug) $time_started = microtime(true);	

		if (!$result = $this->db_handle->query($query)) {
			if ($this->debug) $this->profileError($time_started, $query, $this->db_handle->error);
			return '';
		}

		if ($result->num_rows > 1) {
			die('ERROR: query '.$query.' in DB_MySQLi::getOneItem() returned more than 1 result!');
		}

		$data = $result->fetch_row();
		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $query);

		if (!$data) return false;
		return $data[0];
	}
}
?>