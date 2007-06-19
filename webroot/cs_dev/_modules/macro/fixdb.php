<?
	set_time_limit(0);
	$list = $db->getNumArray('SHOW TABLES');

	foreach($list as $row) {
		$db->query('OPTIMIZE TABLE '.$row[0]);
		$db->query('REPAIR TABLE '.$row[0]);
	}
?>