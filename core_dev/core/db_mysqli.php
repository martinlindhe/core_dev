<?php
/**
 * $Id$
 *
 * MySQL db driver using the php_mysqli extension
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('db_base.php');

class db_mysqli extends db_base
{
	/**
	 * Destructor
	 */
	function __destruct()
	{
		if ($this->db_handle) $this->db_handle->close();
	}

	/**
	 * Opens a connection to MySQL database
	 */
	function connect()
	{
		if ($this->debug) $time_started = microtime(true);

		//MySQL defaults
		if (!$this->host) $this->host = 'localhost';
		if (!$this->port) $this->port = 3306;
		if (!$this->username) $this->username = 'root';

		$this->db_handle = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

		if (mysqli_connect_errno()) {
			$this->db_handle = false;
			if ($this->debug) die('DB_MySQLi: Database connection error '.mysqli_connect_errno().': '.mysqli_connect_error().'.</bad>');
			else die('Database not responding');
		}

		if (!$this->db_handle->set_charset($this->charset)) {
			die('Error loading character set '.$this->charset.': '.$this->db_handle->error);
		}

		$this->driver = 'mysqli';
		$this->dialect = 'mysql';
		$this->server_version = $this->db_handle->server_info;
		$this->client_version = $this->db_handle->client_info;

		if ($this->debug) $this->profileConnect($time_started);
	}

	/**
	 * Shows MySQLi driver status
	 */
	function showDriverStatus()
	{
		echo 'Host info: '.$this->db_handle->host_info.'<br/>';
		echo 'Connection character set: '.$this->db_handle->character_set_name().'<br/>';
		echo 'Last error: '.$this->db_handle->error.'<br/>';
		echo 'Last errno: '.$this->db_handle->errno;
	}

	/**
	 * Escapes the string for use in MySQL queries
	 *
	 * @param $q the query to escape
	 * @return escaped query
	 */
	function escape($q)
	{
		return $this->db_handle->real_escape_string($q);
	}

	/**
	 * Executes a SQL query
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function query($q)
	{
		if ($this->debug) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		if (!$result) {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			else die;	//if debug is turned off (production) and a query fail, just die silently
		}

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $result;
	}

	/**
	 * For SQL INSERT queries
	 *
	 * @param $q the query to execute
	 * @return insert_id
	 */
	function insert($q)
	{
		if ($this->debug) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		$ret_id = 0;

		if ($result) {
			$ret_id = $this->db_handle->insert_id;
		} else {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $ret_id;
	}

	/**
	 * For SQL DELETE queries
	 *
	 * @param $q the query to execute
	 * @return number of rows affected
	 */
	function delete($q)
	{
		if ($this->debug) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		$affected_rows = false;

		if ($result) {
			$affected_rows = $this->db_handle->affected_rows;
		} else {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $affected_rows;
	}

	/**
	 * For SQL SELECT queries who returns array of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getArray($q)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($this->debug) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * For SQL SELECT queries who returns mapped array of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getMappedArray($q)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($this->debug) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {
			$data[ $row[0] ] = $row[1];
		}

		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * For SQL SELECT queries who returns array of data with numerical index
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getNumArray($q)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($this->debug) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {
			$data[] = $row;
		}

		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * For SQL SELECT queries who returns one row of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getOneRow($q)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($this->debug) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		if ($result->num_rows > 1) {
			echo "ERROR: DB_MySQLi::getOneRow() returned ".$result->num_rows." rows!\n";
			if ($this->debug) echo "Query: ".$q."\n";
			die;
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);
		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * For SQL SELECT queries who returns one entry of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getOneItem($q)
	{
		if ($this->debug) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($this->debug) $this->profileError($time_started, $q, $this->db_handle->error);
			return '';
		}

		if ($result->num_rows > 1) {
			echo "ERROR: DB_MySQLi::getOneItem() returned ".$result->num_rows." rows!\n";
			if ($this->debug) echo "Query: ".$q."\n";
			die;
		}

		$data = $result->fetch_row();
		$result->free();

		if ($this->debug) $this->profileQuery($time_started, $q);

		if (!$data) return false;
		return $data[0];
	}

	/**
	 * Lock table from reading
	 *
	 * @param $t table to lock
	 */
	function lock($t)
	{
		$this->query('LOCK TABLES '.$t.' READ');
	}

	/**
	 * Unlock tables
	 */
	function unlock()
	{
		$this->query('UNLOCK TABLES');
	}

}
?>
