<?
/**
 * Object oriented interface for SQLite databases using the php_sqlite.dll extension
 *
 * This interface translates MySQL syntax into SQLite syntax, see the function translate()
 *
 * IMPORTANT: This has only been tested with SQLite 3.3.17, as shipped with PHP 5.2.5 for Windows
 *
 * \todo THIS DRIVER IS CURRENTLY NOT COMPLETE. WIP!!!!
 *
 * \author Martin Lindhe, 2007
 */

require_once('class.DB_Base.php');

class DB_SQLite extends DB_Base
{
	function __destruct()
	{
		if ($this->db_handle) sqlite_close($this->db_handle);
	}

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
		
		//FIXME: set charset to utf8. is it even needed with sqlite????

		$this->db_driver = 'DB_SQLite';
		$this->dialect = 'sqlite';

		//FIXME: set proper parameters
		$this->server_version = '???'; //pg_parameter_status($this->db_handle, 'server_version');
		//$info = pg_version($this->db_handle);
		$this->client_version = '???';	//$info['client'];

		if ($config['debug']) $this->profileConnect($time_started);
	}

	function showDriverStatus()
	{
		//FIXME implement!
		/*
		echo 'Server encoding: '.pg_parameter_status($this->db_handle, 'server_encoding').'<br/>';
		echo 'Client encoding: '.pg_parameter_status($this->db_handle, 'client_encoding').'<br/>';
		echo 'Last error: '.pg_last_error($this->db_handle).'<br/>';
		echo 'Last notice: '.pg_last_notice($this->db_handle);
		*/
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

	function escape($q)
	{
		return sqlite_escape_string($q);
	}

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

	function delete($q)
	{
		global $config;
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		$result = sqlite_query($this->db_handle, $q);

		$affected_rows = false;

		if ($result) {
			$affected_rows = $this->db_handle->affected_rows;	//FIXME: how to do this?!?!?!?!
		} else {
			if ($config['debug']) $this->query_error[ $this->queries_cnt ] = sqlite_last_error($this->db_handle);
			else die; //if debug is turned off (production) and a query fail, just die silently
		}

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $affected_rows;
	}

	function getArray($q)
	{
		global $config;
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q)) {
			if ($config['debug']) $this->profileError($time_started, $q, sqlite_last_error($this->db_handle));
			return array();
		}

		$data = array();

		while ($row = $result->fetch_assoc()) {	//FIXME: how wo do this with sqlite?
			$data[] = $row;
		}

		$result->free();	//FIXME: how?!?!?

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

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

		$result->free();	//FIXME: how!!!!

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

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

		$result->free();	//FIXME: how!!!

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

	function getOneRow($q)
	{
		global $config;
		$q = $this->translate($q);

		if ($config['debug']) $time_started = microtime(true);

		if (!$result = sqlite_query($this->db_handle, $q)) {
			if ($config['debug']) $this->profileError($time_started, $q, sqlite_last_error($this->db_handle));
			return array();
		}

		if ($result->num_rows > 1) {		//FIXME: how?!?!?!
			die('ERROR: query '.$q.' in DB_SQLite::getOneRow() returned more than 1 result!');
		}

		$data = $result->fetch_array(MYSQLI_ASSOC);		//FIXME: how?!?!?!
		$result->free();	//FIXME: how?!?!

		if ($config['debug']) $this->profileQuery($time_started, $q);

		return $data;
	}

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