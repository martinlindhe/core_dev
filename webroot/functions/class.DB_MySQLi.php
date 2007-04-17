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
	
	function showMoreSettings()
	{
		echo 'Server info: '.$this->db_handle->server_info.' ('.$this->db_handle->host_info.')<br/>';
		echo 'Client info: '.$this->db_handle->client_info.'<br/>';
		echo 'Character set: '.$this->db_handle->character_set_name().'<br/>';
		echo 'Current error: '.$this->db_handle->error.'<br/>';
		echo 'Current errno: '.$this->db_handle->errno.'<br/>';
	}

	function escape($query)
	{
		return $this->db_handle->real_escape_string($query);
	}

	protected function connect()
	{
		if ($this->debug) $time_started = microtime(true);

		$this->db_driver = 'DB_MySQLi';

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