<?
	//NOTICE! this is for php 4 compatiblity, use functions_db.php which uses MySQLi when possible instead! it has better performance


	$config['debug'] = false;		//if true, alot more events will be logEntry'ed

	/*
		functions_db_mysql.php - MySQL-database layer, using MySQL extension (legacy crap, compatible with PHP 4.x)
	 */

	/* Creates a MySQL connection thru the older mysql extension */
	function dbOpen($db_config)
	{
		global $config;

		$db['queries'] = 0;

		if ($config['debug']) {
			$time_started = microtime(true);
		}

		$db['handle'] = mysql_pconnect($db_config['server'].':'.$db_config['port'], $db_config['username'], $db_config['password']);
		
		if (!$db['handle']) {
			if ($config['debug']) debugLog('dbOpen(): Connection failed (error: '.mysql_error().')');
			die;
		}

		mysql_select_db($db_config['database'], $db['handle']);

		if ($config['debug']) {
			$db['queries']++;

			$db['time_spent'][ $db['queries'] ] = microtime(true) - $time_started;
			$db['query'][ $db['queries'] ] = 'mysql_pconnect('.$db_config['server'].')';
		}

		return $db;
	}

	function dbQuery(&$db, $query)
	{
		global $config;

		$db['queries']++;
		if ($config['debug']) {
			$time_started = microtime(true);
			$db['query'][ $db['queries'] ] = $query;
		}

		$result['handle'] = mysql_query($query, $db['handle']);
		if ($config['debug'] && !$result['handle']) {
			$error = mysql_error($db['handle']);
   		debugLog('dbQuery() error: '.$error.' (query: '.$query.')');
   		$db['error'][ $db['queries'] ] = $error;
		} else if ($result['handle']) {
			$db['insert_id'] =  mysql_insert_id($db['handle']);
		} else {
			//if debug is turned off (production) and a query fail, just die silently
			die;
		}

		if ($config['debug']) {
			$result['query'] = $query;
			$db['time_spent'][ $db['queries'] ] = microtime(true) - $time_started;
		}

		return $result;
	}

	/* Helper function: fetches all rows from result and return as an array of arrays */
	function dbArray(&$db, $query)
	{
		$check = dbQuery($db, $query);
		$cnt = dbNumRows($check);

		if ($cnt>0) {
			for ($i=0; $i<$cnt; $i++) {
				$result[$i] = dbFetchArray($check);
			}
			return $result;
		}

		return;
	}

	/* helper function: returns one array of rows, each row must be only 1 data value */
	function dbOneArray(&$db, $query)
	{
		$check = dbQuery($db, $query);
		$cnt = dbNumRows($check);

		if ($cnt>0) {
			for ($i=0; $i<$cnt; $i++) {
				$res = dbFetchArray($check);
				reset($res);
				$result[$i] = current($res);	//returns the first item in $result[] array
			}
			return $result;
		}

		return;
	}

	/* Helper function for SHOW STATUS and such */
	function dbArrayNamedFields(&$db, $query, $key_name, $value_name)
	{
		$check = dbQuery($db, $query);
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
	function dbOneResult(&$db, $query)
	{
		global $config;

		$check = dbQuery($db, $query);
		$cnt = dbNumRows($check);
		if ($config['debug'] && $cnt > 1) {
			debugLog('ERROR: query '.$query.' in dbOneResult() returned more than 1 result!');
		}
		$result = dbFetchArray($check);
		return $result;
	}

	/* Helper function: returns 1 data value from query that expects to return 1 row, like a select count() */
	function dbOneResultItem(&$db, $query)
	{
		global $config;

		$check = dbQuery($db, $query);
		$cnt = dbNumRows($check);
		if ($config['debug'] && $cnt > 1) {
			debugLog('ERROR: query '.$query.' in dbOneResult() returned more than 1 result!');
		}
		$result = dbFetchArray($check);

		if ($result) {
			reset($result);
			return current($result);	//returns the first item in $result[] array
		}
		return false;
	}

	/* Returns the number of rows in the result from a dbQuery() */
	function dbNumRows($result_id)
	{
		global $config;

		if (is_bool($result_id)) {
			if ($config['debug']) debugLog('dbNumRows(): Fatal error: '.$result_id);
			die;
		}

		$count = mysql_num_rows($result_id['handle']);
		if (isset($count)) return $count;

		if ($config['debug']) debugLog('dbNumRows(): Invalid result on query: '.$result_id['query']);
		return 0;
	}

	/* Returns the result from a dbQuery() in a array */
	function dbFetchArray($result_id)
	{
		return mysql_fetch_array($result_id['handle'], MYSQL_ASSOC);
	}


	/* Properly closes the connection */
	function dbClose(&$db)
	{
		mysql_close($db['handle']);
	}

	/* Function to handle string quoting regardless of php settings */
	function dbAddSlashes(&$db, $string)
	{
		//Check for CHAR()-occurences in $string, see this url for more information:
		//http://www.websec.org/papers/charinjection.txt.html
		
		/* If the string contains CHAR and a (, return empty string (this is a MySQL-specific fix) */
		/*
		if (stripos($string, 'char')!== FALSE && strpos($string, '(')!==FALSE) {
			logEntry($db, 'dbAddSlashes: Suspected CHAR-injection attack blocked!');
			die;
		}*/

		return mysql_real_escape_string($string, $db['handle']);
	}

	function dbStripSlashes($string)
	{
		return stripslashes($string);
	}

?>