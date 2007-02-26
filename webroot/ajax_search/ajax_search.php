<? header('Content-type: text/xml'); echo '<?xml version="1.0" ?>';

	if (empty($_GET['s'])) die('<x></x>');
	$search = $_GET['s'];

	/* fetches all rows from result and return as an array */
	function dbFetchArray(&$db, $query)
	{
		$check = mysql_query($query, $db);
		$cnt = mysql_num_rows($check);

		if (!$cnt) return array();

		for ($i=0; $i<$cnt; $i++) {
			$result[$i] = mysql_fetch_array($check, MYSQL_ASSOC);
		}
		return $result;
	}

	$db = @ mysql_connect('localhost:3306', 'root', '');
	if (!$db) die;

	mysql_select_db('dbAJAXSearch', $db);

	$search = mysql_real_escape_string($search, $db);
	$list = dbFetchArray($db, 'SELECT * FROM tblText WHERE txt LIKE "%'.$search.'%"');

	echo '<x>';
	for ($i=0; $i<count($list); $i++) {
		echo '<s id="'.$list[$i]['id'].'">'.$list[$i]['txt'].'</s>';
	}
	echo '</x>';
?>