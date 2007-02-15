<?
	$config['debug'] = false;		//if true, alot more events will be logEntry'ed

	/*
		functions_db.php - MySQL-database layer, using MySQLi extension
	 */

	/* Creates a MySQL connection thru the mysqli extension */
	function dbOpen($db_config)
	{
		global $config;

		$db['queries'] = 0;

		if ($config['debug']) {
			$time_started = microtime(true);
		}

		$db['handle'] = @mysqli_connect($db_config['server'], $db_config['username'], $db_config['password'], $db_config['database'], $db_config['port']);

		if (!$db['handle']) {
			if ($config['debug']) debugLog('dbOpen(): Connection failed (error: '.mysqli_connect_error().')');
			die;
		}

		if ($config['debug']) {
			$db['queries']++;

			$db['time_spent'][ $db['queries'] ] = microtime(true) - $time_started;
			$db['query'][ $db['queries'] ] = 'mysqli_connect('.$db_config['server'].'), '.mysqli_get_host_info($db['handle']);
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

		$result['handle'] = mysqli_query($db['handle'], $query);
		if ($config['debug'] && !$result['handle']) {
			$error = mysqli_error($db['handle']);
   		debugLog('dbQuery() error: '.$error.' (query: '.$query.')');
   		$db['error'][ $db['queries'] ] = $error;
		} else if ($result['handle']) {
			$db['insert_id'] =  mysqli_insert_id($db['handle']);
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

		$count = mysqli_num_rows($result_id['handle']);
		if (isset($count)) return $count;

		if ($config['debug']) debugLog('dbNumRows(): Invalid result on query: '.$result_id['query']);
		return 0;
	}

	/* Returns the result from a dbQuery() in a array */
	function dbFetchArray($result_id)
	{
		return mysqli_fetch_array($result_id['handle'], MYSQLI_ASSOC);
	}


	/* Properly closes the connection */
	function dbClose(&$db)
	{
		mysqli_close($db['handle']);
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

		return mysqli_real_escape_string($db['handle'], $string);
	}

	function dbStripSlashes($string)
	{
		return stripslashes($string);
	}

?>