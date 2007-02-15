<?
	/*
		functions_db.php - MySQL-database layer, by deadprez
	 */

	define('DB_DEBUG', true);

	function dbLog($str)
	{
		if (DB_DEBUG) {
			//logEntry($db, 'dbLog: '. $str ); //fixme: passa $db som parameter
			//if ($_SESSION['isAdmin']) 
			echo 'dbLog: '.$str.'<br>';
		}
	}

	/* Helper function: Calls dbConnect() with data from $db_handle */
	function dbOpen($db_handle)
	{
		$db = dbConnect($db_handle['server'], $db_handle['port'], $db_handle['username'], $db_handle['password'], $db_handle['database']);
		if (!$db) {
			dbLog('Misslyckades att ansluta till databasen<br>');
			die;
		}

		return $db;
	}

	function dbConnect($server, $port, $username, $password, $database)
	{
		$connection_id['queries'] = 0;
		$connection_id['handle'] = mysql_connect($server.':'.$port, $username, $password);

		if(!$connection_id['handle']) {
			dbLog('dbConnect(): SQL-server not responding (error: '.mysql_error().')');
			return;
		}
		dbSelectDatabase($connection_id, $database);

		return $connection_id;
	}

	function dbSelectDatabase(&$connection_id, $database)
	{
		/*
		 * Selects a database
		 *
		 */

		mysql_select_db($database, $connection_id['handle']);
	}


	/* Helper function: fetches all rows from result and return as an array */
	function dbArray(&$connection_id, $query)
	{
		$check = dbQuery($connection_id, $query);
		$cnt = dbNumRows($check);

		if ($cnt>0) {
			for ($i=0; $i<$cnt; $i++) {
				$result[$i] = dbFetchArray($check);

				reset($result[$i]);
				while (list($key, $val) = each($result[$i])) {
    				$result[$i][$key] = dbStripSlashes($val);
				}
			}
			return $result;
		}

		return;
	}

	/* Helper function for SHOW STATUS and such */
	function dbArrayNamedFields(&$connection_id, $query, $key_name, $value_name)
	{
		$check = dbQuery($connection_id, $query);
		$cnt = dbNumRows($check);
		
		if ($cnt > 0) {
			for ($i=0; $i<$cnt; $i++) {
				$hold_result = dbFetchArray($check);

				$index = $hold_result[$key_name];
				$value = $hold_result[$value_name];

				$result[ $index ] = $value;
			}
			return $result;
		}

		return;
	}

	/* Helper function: returns a data array (full row) from a query that expects 1 row, like a select * from t1 where uniqueid=1 */
	function dbOneResult(&$connection_id, $query)
	{
		$check = dbQuery($connection_id, $query);
		$cnt = dbNumRows($check);
		if ($cnt > 1) {
			dbLog('ERROR: query '.$query.' in dbOneResult() returned more than 1 result!');
		}
		$result = dbFetchArray($check);
		return $result;
	}

	/* Helper function: returns 1 data value from query that expects to return 1 row, like a select count() */
	function dbOneResultItem(&$connection_id, $query)
	{
		$check = dbQuery($connection_id, $query);
		$cnt = dbNumRows($check);
		if ($cnt > 1) {
			dbLog('ERROR: query '.$query.' in dbOneResult() returned more than 1 result!');
		}
		$result = dbFetchArray($check);

		if ($result) {
			reset($result);
			return current($result);	//returns the first item in $result[] array
		}
		return false;
	}

	function dbQuery(&$connection_id, $query)
	{
		$connection_id['queries']++;
		if (DB_DEBUG === true) {
			$connection_id['query'][ $connection_id['queries'] ] = $query;
		}

		$result['handle'] = mysql_query($query, $connection_id['handle']);
		if (!$result['handle'] && (DB_DEBUG === true)) {
   		dbLog('dbQuery() error: '.mysql_error().' (query: '.$query.')');
		} else {
			$connection_id['insert_id'] =  mysql_insert_id();
		}

		if (DB_DEBUG === true) $result['query'] = $query;

		return $result;
	}

	function dbNumRows($result_id)
	{
		/*
		 *	Returns the number of rows in the result from a dbQuery()
		 *
		 */

		$count = mysql_num_rows($result_id['handle']);
		if (isset($count)) return $count;

		dbLog('dbNumRows(): Invalid result on query: '.$result_id['query']);
		return 0;
	}

	function dbFetchArray($result_id)
	{
		/*
	 	 * Returns the result from a dbQuery() in a array
		 */

		return mysql_fetch_array($result_id['handle'], MYSQL_ASSOC);
	}


	function dbClose($connection_id)
	{
		/* Properly closes the connection */

		mysql_close($connection_id['handle']);
	}

	/* Returns the ID of the last insert query */
	function dbInsertId($result) {	//fixme: deprecated! use $sql['insert_id'] instead
		return mysql_insert_id();
	}

	/* Function to handle string quoting regardless of php settings */
	function dbAddSlashes($string)
	{
		if (get_magic_quotes_gpc()) $string = stripslashes($string); //if variables get auto-quoted, unquote first
		return mysql_real_escape_string($string);
	}

	function dbStripSlashes($string)
	{
		if (!get_magic_quotes_runtime()) return stripslashes($string);
		return $string;
	}


	function ampifyString($string)
	{
		$string = str_replace('å', '&aring;', $string);
		$string = str_replace('ä', '&auml;', $string);
		$string = str_replace('ö', '&ouml;', $string);

		$string = str_replace('Å', '&Aring;', $string);
		$string = str_replace('Ä', '&Auml;', $string);
		$string = str_replace('Ö', '&Ouml;', $string);

		$string = str_replace('&ARING;', '&Aring;', $string);
		$string = str_replace('&AUML;', '&Auml;', $string);
		$string = str_replace('&OUML;', '&Ouml;', $string);

		return $string;
	}

	function deampifyString($string)
	{
		$string = str_replace('&ARING;', '&Aring;', $string);
		$string = str_replace('&AUML;', '&Auml;', $string);
		$string = str_replace('&OUML;', '&Ouml;', $string);

		$string = str_replace('&aring;', 'å', $string);
		$string = str_replace('&auml;', 'ä', $string);
		$string = str_replace('&ouml;', 'ö', $string);

		$string = str_replace('&Aring;', 'Å', $string);
		$string = str_replace('&Auml;', 'Ä', $string);
		$string = str_replace('&Ouml;', 'Ö', $string);

		return $string;
	}
?>