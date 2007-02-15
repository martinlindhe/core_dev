<?
	/*
		functions_db.php - MySQL-database layer, using MySQLi extension
	 */

	function dbLog($str)
	{
		global $config;

		if ($config['debug']) {
			if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin']) echo 'dbLog: '.$str.'<br>';
		}
	}

	/* Creates a MySQL connection thru the mysqli extension */
	function dbOpen($db_config)
	{
		$db['queries'] = 0;
		$db['handle'] = mysqli_connect($db_config['server'], $db_config['username'], $db_config['password'], $db_config['database'], $db_config['port']);

		if (!$db['handle']) {
			dbLog('dbOpen(): Connection failed (error: '.mysqli_connect_error().')');
			die;
		}

		//fixme: i only get a connection thru TCP/IP to localhost under windows!
		//printf("Host information: %s\n", mysqli_get_host_info($db['handle']));

		return $db;
	}

	/* Helper function: fetches all rows from result and return as an array */
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
			dbLog('ERROR: query '.$query.' in dbOneResult() returned more than 1 result!');
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
			dbLog('ERROR: query '.$query.' in dbOneResult() returned more than 1 result!');
		}
		$result = dbFetchArray($check);

		if ($result) {
			reset($result);
			return current($result);	//returns the first item in $result[] array
		}
		return false;
	}

	function dbQuery(&$db, $query)
	{
		global $config;

		$db['queries']++;
		if ($config['debug']) $db['query'][ $db['queries'] ] = $query;

		$result['handle'] = mysqli_query($db['handle'], $query);
		if ($config['debug'] && !$result['handle']) {
   		dbLog('dbQuery() error: '.mysqli_error($db['handle']).' (query: '.$query.')');
		} else {
			$db['insert_id'] =  mysqli_insert_id($db['handle']);
		}

		if ($config['debug']) $result['query'] = $query;

		return $result;
	}

	function dbNumRows($result_id)
	{
		/*
		 *	Returns the number of rows in the result from a dbQuery()
		 *
		 */

		$count = mysqli_num_rows($result_id['handle']);
		if (isset($count)) return $count;

		dbLog('dbNumRows(): Invalid result on query: '.$result_id['query']);
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