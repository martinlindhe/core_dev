<?
	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (empty($_GET['s'])) die('<x></x>');
	$search = $_GET['s'];

	/* fetches all rows from result and return as an array */
	function dbFetchArray(&$db, $query)
	{
		$check = mysqli_query($db, $query);
		$cnt = mysqli_num_rows($check);

		if (!$cnt) return array();

		for ($i=0; $i<$cnt; $i++) {
			$result[$i] = mysqli_fetch_array($check, MYSQLI_ASSOC);
		}
		return $result;
	}

	$db = @mysqli_connect('localhost', 'root', '', 'dbAJAXSearch', 3306);
	if (!$db) die;

	$search = mysqli_real_escape_string($db, $search);
	$list = dbFetchArray($db, 'SELECT * FROM tblText WHERE txt LIKE "%'.$search.'%"');

	echo '<x>';
	for ($i=0; $i<count($list); $i++) {
		echo '<s id="'.$list[$i]['id'].'">'.$list[$i]['txt'].'</s>';
	}
	echo '</x>';
?>