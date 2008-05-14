<?php
/**
 * $Id$
 *
 * Object oriented interface for SQLite databases using the php_sqlite.dll extension
 *
 * This interface translates MySQL syntax into SQLite syntax, see the function translate()
 *
 * IMPORTANT: This has only been tested with SQLite 3.3.17, as shipped with PHP 5.2.5 for Windows
 *
 * \todo THIS DRIVER IS CURRENTLY NOT COMPLETE. WIP!!!!
 * \todo REWRITE TO USE object oriented interface. but first: make it work procedural style
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('class.DB_Base.php');

class DB_SQLite extends DB_Base
{
	/**
	 * Destructor
	 *
	 * \return nothing
	 */
	function __destruct()
	{
		if ($this->db_handle) sqlite_close($this->db_handle);
	}

	/**
	 * Opens a connection to SQLite database
	 *
	 * \return nothing
	 */
	function connect()
	{
		global $config;

		if ($config['debug']) $time_started = microtime(true);

		//SQLite defaults
		if (!$this->database) $this->database = 'default';

		$this->db_handle = sqlite_open($this->database, 0666, $err);

		if (!$this->db_handle) {
			$this->db_handle = false;
			die('<bad>Database connection error: '.$err.'.</bad>');
		}
		
		//FIXME: set charset to utf8. see http://se.php.net/manual/en/function.sqlite-libencoding.php WARNINGS for more details of the problem

		$this->db_driver = 'DB_SQLite';
		$this->dialect = 'sqlite';

		$this->server_version = sqlite_libversion();
		$this->client_version = sqlite_libversion();

		if ($config['debug']) $this->profileConnect($time_started);
	}

	/**
	 * Shows SQLite driver status
	 *
	 * \return nothing
	 */
	function showDriverStatus()
	{
		echo 'Encoding: '.sqlite_libencoding().'<br/>';
		echo 'Last error: '.sqlite_last_error($this->db_handle).'<br/>';
	}

	/**
	 * Transparently translates MySQL queries to SQLite queries.
	 *
	 * \param $q MySQL query to translate
	 * \return SQLite version of the query
	 */
	private function translate($q)
	{
		//FIXME implement
		return $q;
	}

	/**
	 * Escapes the string for use in SQLite queries
	 *
	 * \param $q the query to escape
	 * \return escaped query
	 */
	function escape($q)
	{
		return sqlite_escape_string($q);
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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		$result = sqlite_query($this->db_handle, $q);

		if (!$result) {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = sqlite_last_error($this->db_handle);
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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		$result = sqlite_query($this->db_handle, $q);

		$ret_id = 0;

		if ($result) {
			$ret_id = $this->db_handle->insert_id;		//FIXME: how to do this for sqlite???
		} else {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = sqlite_last_error($this->db_handle);
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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		$result = sqlite_query($this->db_handle, $q);

		$affected_rows = false;

		if ($result) {
			$affected_rows = sqlite_changes($this->db_handle);
		} else {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = sqlite_last_error($this->db_handle);
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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q, SQLITE_ASSOC, $err)) {	//FIXME: untested. sqlite_array_query also exists
			if ($config['debug']) $this->profileError($time_started, $q, $err);
			return array();
		}

		$data = sqlite_fetch_array($result); //FIXME: untested
		
		//$result->free();	//FIXME: how?!?!?

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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q)) {
			if ($config['debug']) $this->profileError($time_started, $q, sqlite_last_error($this->db_handle));
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {	//FIXME: how!!!
			$data[ $row[0] ] = $row[1];
		}

		//$result->free();	//FIXME: how!!!!

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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q)) {
			if ($config['debug']) $this->profileError($time_started, $q, sqlite_last_error($this->db_handle));
			return array();
		}

		$data = array();

		while ($row = $result->fetch_row()) {	//FIXME: how!!
			$data[] = $row[0];
		}

		//$result->free();	//FIXME: how!!!

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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q)) {
			if ($config['debug']) $this->profileError($time_started, $q, sqlite_last_error($this->db_handle));
			return array();
		}

		if (sqlite_num_rows($result) > 1) {
			die('ERROR: query '.$q.' in DB_SQLite::getOneRow() returned more than 1 result!');
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);		//FIXME: how?!?!?!
		//$result->free();	//FIXME: how?!?!

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
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q)) {
			if ($config['debug']) $this->profileError($time_started, $q, sqlite_last_error($this->db_handle));
			return '';
		}

		if (sqlite_num_rows($result) > 1) {
			die('ERROR: query '.$q.' in DB_SQLite::getOneItem() returned more than 1 result!');
		}

		$data = pg_fetch_row($result);		//FIXME: sqlite_fetch_row finns inte!

		if ($config['debug']) $this->profileQuery($time_started, $q);

		if (!$data) {
			if ($num) return 0;
			return false;
		}
		return $data[0];
	}

}
?>
