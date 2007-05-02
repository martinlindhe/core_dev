<?
	set_time_limit(0);
	$lpsdl = $sql->query("SHOW TABLES");
	foreach($lpsdl as $row) {
		print_r($sql->query("OPTIMIZE TABLE ".$row[0]));
		print_r($sql->query("REPAIR TABLE ".$row[0]));
	}
?>