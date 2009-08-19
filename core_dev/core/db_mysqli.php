<?php
/**
 * $Id$
 *
 * MySQL db driver using the php_mysqli extension
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
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

		$this->connected = true;
		$this->driver = 'mysqli';
		$this->dialect = 'mysql';
		$this->server_version = $this->db_handle->server_info;
		$this->client_version = $this->db_handle->client_info;

		if ($this->debug) $this->profileConnect($time_started);
	}

	/**
	 * Shows MySQLi driver status
	 */
	function status()
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
		if (!$this->connected) $this->connect(); //XXX: need connection to use the escape function
		return $this->db_handle->real_escape_string($q);
	}

	function real_query($q)
	{
		if (!$this->connected) $this->connect();
		return $this->db_handle->query($q);
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

		$result = $this->real_query($q);

		if (!$result) {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			//else die; //if debug is turned off (production) and a query fail, just die silently
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

		$result = $this->real_query($q);

		$ret_id = 0;

		if ($result) {
			$ret_id = $this->db_handle->insert_id;
		} else {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			//else die; //if debug is turned off (production) and a query fail, just die silently
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

		$result = $this->real_query($q);

		$affected_rows = false;

		if ($result) {
			$affected_rows = $this->db_handle->affected_rows;
		} else {
			if ($this->debug) $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
			//else die; //if debug is turned off (production) and a query fail, just die silently
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

		if (!$result = $this->real_query($q)) {
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

		if (!$result = $this->real_query($q)) {
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

		if (!$result = $this->real_query($q)) {
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

		if (!$result = $this->real_query($q)) {
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

		if (!$result = $this->real_query($q)) {
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

	/**
	 * Returns true if a database with this name already exists
	 */
	function findDatabase($dbname)
	{
		$list = $this->getArray('SHOW DATABASES');
		foreach ($list as $row) {
			if ($row['Database'] == $dbname) return true;
		}
		return false;
	}

	function selectDatabase($dbname)
	{
		$this->database = $dbname;
		return $this->db_handle->select_db($this->database);
	}

	function createDatabase($dbname, $charset = 'utf8')
	{
		if ($this->findDatabase($dbname)) return false;

		$q = 'CREATE DATABASE '.$dbname.' CHARACTER SET utf8';
		return $this->query($q);
	}

	/**
	 * Returns true if a table with this name already exists
	 */
	function findTable($tblname)
	{
		$list = $this->getNumArray('SHOW TABLES FROM '.$this->database);
		foreach ($list as $row) {
			if ($row[0] == $tblname) return true;
		}
		return false;
	}

	function createTable($tblname, $layout, $charset = 'utf8')
	{
		$parsed = $this->parseLayout($layout);

		$q = "CREATE TABLE ".$tblname." (\n";
		$key_pri = '';
		foreach ($parsed as $col) {
			$q .= $col['Field'].' '.$col['Type'];
			switch ($col['Null']) {
			case 'NO': $q .= ' NOT NULL'; break;
			case 'YES': $q .= ' NULL'; break;
			}
			$q .= ($col['Default'] ? " default '".$col['Default']."'" : "");
			$q .= ($col['Extra'] ? ' '.$col['Extra'] : '').",\n";

			if ($col['Key'] == 'PRI') $key_pri = $col['Field'];
		}
		if ($key_pri) {
			$q .= "PRIMARY KEY (".$key_pri.")\n";
		}
		$q .= ") ENGINE=MyISAM DEFAULT CHARSET=".$charset."\n";
		return $this->query($q);
	}

	function verifyTable($tblname, $layout, $charset = 'utf8')
	{
		$list = $this->getArray('DESCRIBE '.$this->database.'.'.$tblname);
		if (!$list) return false;

		$parsed = $this->parseLayout($layout);

		if ($list == $parsed) return true;
		return false;
	}

	function parseLayout($layout)
	{
		$res = array();
		$idx = 0;
		foreach ($layout as $name=>$col) {
			$res[$idx]['Field'] = $name;
			$res[$idx]['Type'] = '';
			$res[$idx]['Null'] = 'YES';
			$res[$idx]['Key'] = '';
			$res[$idx]['Default'] = '';
			$res[$idx]['Extra'] = '';
			foreach ($col as $prop) {
				$ex = explode(':',$prop);

				switch ($ex[0]) {
				case 'key':
					$res[$idx]['Key'] = 'PRI';
					break;

				case 'extra':
					$res[$idx]['Extra'] = $ex[1];
					break;

				case 'default':
					$res[$idx]['Default'] = $ex[1];
					break;

				case 'null':
					$res[$idx]['Null'] = $ex[1];
					break;

				case 'datetime':
				case 'text':
				case 'smallint':
				case 'tinyint':
				case 'bigint':
					$res[$idx]['Type'] = $ex[0].(!empty($ex[1]) ? '('.$ex[1].')' : '').(!empty($ex[2]) ? ' '.$ex[2] : '');
					break;

				default:
					echo "createTable: unknown prop ".$ex[0]."\n";
				}
			}
			$idx++;
		}
		return $res;
	}

}


?>
