<?php
/**
 * $Id$
 *
 * Object oriented interface for PostgreSQL databases using the php_pgsql.dll extension
 *
 * This interface translates MySQL syntax into PostgreSQL syntax, see the function translate()
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//TODO: THIS DRIVER IS CURRENTLY NOT COMPLETE. WIP!!!!

require_once('class.DB_Base.php');

class DB_PostgreSQL extends DB_Base
{
	/**
	 * Destructor
	 */
	function __destruct()
	{
		if ($this->db_handle) pg_close($this->db_handle);
	}

	/**
	 * Opens a connection to PostgreSQL database
	 */
	function connect()
	{
		if ($this->debug) $time_started = microtime(true);

		//PostgreSQL defaults
		if (!$this->host) $this->host = 'localhost';
		if (!$this->port) $this->port = 5432;
		if (!$this->username) $this->username = 'postgres';

		$str = 'host='.$this->host.' user='.$this->username.' password='.$this->password.' dbname='.$this->database.' port='.$this->port;
		$this->db_handle = pg_connect($str);

		if (!$this->db_handle) {
			$this->db_handle = false;

			die('<bad>Database connection error.</bad>');
		}

		//FIXME: set charset if it is not utf8 (will be the default anyways if none is specified)

		$this->db_driver = 'DB_PostgreSQL';
		$this->dialect = 'pgsql';

		$this->server_version = pg_parameter_status($this->db_handle, 'server_version');
		$info = pg_version($this->db_handle);
		$this->client_version = $info['client'];

		if ($this->debug) $this->profileConnect($time_started);
	}

	/**
	 * Shows PostgreSQL driver status
	 */
	function showDriverStatus()
	{
		echo 'Server encoding: '.pg_parameter_status($this->db_handle, 'server_encoding').'<br/>';
		echo 'Client encoding: '.pg_parameter_status($this->db_handle, 'client_encoding').'<br/>';
		echo 'Last error: '.pg_last_error($this->db_handle).'<br/>';
		echo 'Last notice: '.pg_last_notice($this->db_handle);
	}

	/**
	 * Transparently translates MySQL queries to PostgreSQL queries.
	 *
	 * @param $q MySQL query to translate
	 * @return PostgreSQL version of the query
	 */
	private function translate($q)
	{
		//FIXME implement
		return $q;
	}

	/**
	 * Escapes the string for use in PostgreSQL queries
	 *
	 * @param $q the query to escape
	 * @return escaped query
	 */
	function escape($q)
	{
		return pg_escape_string($this->db_handle, $q);
	}

	/**
	 * Executes a SQL query
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function query($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		$result = pg_query($this->db_handle, $q);

		if (!$result) {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = pg_last_error($this->db_handle);
			else die;	//if debug is turned off (production) and a query fail, just die silently
		}

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $result;
	}

	/**
	 * Helper function for SQL INSERT queries
	 *
	 * @param $q the query to execute
	 * @return insert_id
	 */
	function insert($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		$result = pg_query($this->db_handle, $q);

		$ret_id = 0;

		if ($result) {
			$ret_id = $this->db_handle->insert_id;	//FIXME: how to return last insert_id for pgsql???
		} else {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = pg_last_error($this->db_handle);
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $ret_id;
	}

	/**
	 * Helper function for SQL DELETE queries
	 *
	 * @param $q the query to execute
	 * @return number of rows affected
	 */
	function delete($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		$result = pg_query($this->db_handle, $q);

		$affected_rows = false;

		if ($result) {
			$affected_rows = $this->db_handle->affected_rows;	//FIXME: this is not correct!
		} else {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = pg_last_error($this->db_handle);
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $affected_rows;
	}

	/**
	 * Helper function for SQL SELECT queries who returns array of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getArray($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		if (!$result = pg_query($this->db_handle, $q)) {
			if ($this->debug) $this->profileError($time_started, $q, pg_last_error($this->db_handle));
			return array();
		}

		$data = array();

		while ($row = $result->fetch_assoc()) {		//FIXME: this is not correct
			$data[] = $row;
		}

		$result->free();	//FIXME: how?

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns mapped array of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getMappedArray($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		if (!$result = pg_query($this->db_handle, $q)) {
			if ($this->debug) $this->profileError($time_started, $q, pg_last_error($this->db_handle));
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {	//FIXME: this is not correct
			$data[ $row[0] ] = $row[1];
		}

		$result->free();	//FIXME: how?

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns array of data with numerical index
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getNumArray($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		if (!$result = pg_query($this->db_handle, $q)) {
			if ($this->debug) $this->profileError($time_started, $q, pg_last_error($this->db_handle));
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {		//FIXME: how???
			$data[] = $row[0];
		}

		$result->free();	//FIXME: how???

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns one row of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getOneRow($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		if (!$result = pg_query($this->db_handle, $q)) {
			if ($this->debug) $this->profileError($time_started, $q, pg_last_error($this->db_handle));
			return array();
		}

		if ($result->num_rows > 1) {		//FIXME: how???
			echo "ERROR: DB_PostgreSQL::getOneRow() returned ".$result->num_rows." rows!\n";
			if ($this->debug) echo "Query: ".$q."\n";
			die;
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);		//FIXME: this is not correct
		$result->free();		//FIXME: how???

		if ($this->debug) $this->profileQuery($time_started, $q);

		return $data;
	}

	/**
	 * Helper function for SQL SELECT queries who returns one entry of data
	 *
	 * @param $q the query to execute
	 * @return result
	 */
	function getOneItem($q)
	{
		$q = $this->translate($q);

		if ($this->debug) $time_started = microtime(true);

		if (!$result = pg_query($this->db_handle, $q)) {
			if ($this->debug) $this->profileError($time_started, $q, pg_last_error($this->db_handle));
			return '';
		}

		if (pg_num_rows($result) > 1) {
			echo "ERROR: DB_PostgreSQL::getOneItem() returned ".$result->num_rows." rows!\n";
			if ($this->debug) echo "Query: ".$q."\n";
			die;
		}

		$data = pg_fetch_row($result);

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
		//$this->query('LOCK TABLES '.$t.' READ');
	}

	/**
	 * Unlock tables
	 */
	function unlock()
	{
		//$this->query('UNLOCK TABLES');
	}

}
?>
