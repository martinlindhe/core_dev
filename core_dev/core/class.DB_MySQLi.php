<?php
/**
 * $Id$
 *
 * Object oriented interface for MySQL databases using the php_mysqli.dll extension
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('class.DB_Base.php');

class DB_MySQLi extends DB_Base
{
	/**
	 * Destructor
	 *
	 * \return nothing
	 */
	function __destruct()
	{
		if ($this->db_handle) $this->db_handle->close();
	}

	/**
	 * Opens a connection to MySQL database
	 *
	 * \return nothing
	 */
	function connect()
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		//MySQL defaults
		if (!$this->host) $this->host = 'localhost';
		if (!$this->port) $this->port = 3306;	//MySQL default port
		if (!$this->username) $this->username = 'root';

		$this->db_handle = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

		if (mysqli_connect_errno()) {
			$this->db_handle = false;
			if ($config['debug']) die('DB_MySQLi: Database connection error '.mysqli_connect_errno().': '.mysqli_connect_error().'.</bad>');
			else die;
		}

		if (!$this->db_handle->set_charset($this->charset)) {
			die('Error loading character set '.$this->charset.': '.$this->db_handle->error);
		}

		$this->db_driver = 'DB_MySQLi';
		$this->dialect = 'mysql';
		$this->server_version = $this->db_handle->server_info;
		$this->client_version = $this->db_handle->client_info;

		if ($config['debug']) $this->profileConnect($time_started);
	}

	/**
	 * Shows MySQLi driver status
	 *
	 * \return nothing
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
	 * \param $q the query to escape
	 * \return escaped query
	 */
	function escape($q)
	{
		return $this->db_handle->real_escape_string($q);
	}

	/**
	 * Executes a SQL query
	 *
	 * \param $q the query to execute
	 * \return result
	 */
	function query($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		if (!$result) {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			else die;	//if debug is turned off (production) and a query fail, just die silently
		}

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $result;
	}

	/**
	 * Helper function for SQL INSERT queries
	 *
	 * \param $q the query to execute
	 * \return insert_id
	 */
	function insert($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		$ret_id = 0;

		if ($result) {
			$ret_id = $this->db_handle->insert_id;
		} else {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $ret_id;
	}

	/**
	 * Helper function for SQL DELETE queries
	 *
	 * \param $q the query to execute
	 * \return number of rows affected
	 */
	function delete($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		$result = $this->db_handle->query($q);

		$affected_rows = false;

		if ($result) {
			$affected_rows = $this->db_handle->affected_rows;
		} else {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $affected_rows;
	}

	/**
	 * Helper function for SQL SELECT queries who returns array of data
	 *
	 * \param $q the query to execute
	 * \return result
	 */
	function getArray($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($config['debug']) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$result->free();

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns mapped array of data
	 *
	 * \param $q the query to execute
	 * \return result
	 */
	function getMappedArray($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($config['debug']) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {
			$data[ $row[0] ] = $row[1];
		}

		$result->free();

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns array of data with numerical index
	 *
	 * \param $q the query to execute
	 * \return result
	 */
	function getNumArray($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($config['debug']) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {
			$data[] = $row;
		}

		$result->free();

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns one row of data
	 *
	 * \param $q the query to execute
	 * \return result
	 */
	function getOneRow($q)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($config['debug']) $this->profileError($time_started, $q, $this->db_handle->error);
			return array();
		}

		if ($result->num_rows > 1) {
			die('ERROR: query '.$q.' in DB_MySQLi::getOneRow() returned more than 1 result!');
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);
		$result->free();

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns one entry of data
	 *
	 * \param $q the query to execute
	 * \param $num if set to true, return "0" instead of false on empty result
	 * \return result
	 */
	function getOneItem($q, $num = false)
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = $this->db_handle->query($q)) {
			if ($config['debug']) $this->profileError($time_started, $q, $this->db_handle->error);
			return '';
		}

		if ($result->num_rows > 1) {
			die('ERROR: query '.$q.' in DB_MySQLi::getOneItem() returned more than 1 result!');
		}

		$data = $result->fetch_row();
		$result->free();

		if ($config['debug']) $this->profileQuery($time_started, $q);

		if (!$data) {
			if ($num) return 0;
			return false;
		}
		return $data[0];
	}
}
?>
