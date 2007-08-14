<?
	die;
	set_time_limit(0);
	include("_config/online.include.php");

	$tot_cnt = 0;
	$csv_file = '/home/martin/csbilder/harem_gb.csv';

	$handle = fopen($csv_file, "r");

	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		$num = count($data);

		if($num != 7 && $num != 1) die('ok:'.$num);
		$inp = '';
		if($num == 7) {
			$tot_cnt++;
			for ($c=0; $c < $num; $c++) {
				$data[$c] = secureINS($data[$c]);
			}
			$check1 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
			$check2 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[2]."' AND status_id = '1' LIMIT 1");
			if($check1) {
			$check = $sql->queryResult("SELECT u_alias FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
	$sql->queryUpdate("REPLACE INTO s_usergb SET
	main_id = '".$data[0]."',
	user_id = '".$data[1]."',
	sender_id = '".$data[2]."',
	sent_date = '".$data[3]."',
	sent_cmt = '".$data[4].($data[6]?"\n\nSvar från ".$check.":\n".$data[6]:'')."',
	user_read = '".$data[5]."'
	");
			$or = array($data[1], $data[2]);
			sort($or);
			$sql->queryInsert("REPLACE INTO s_usergbhistory SET users_id = '".implode('-', $or)."', msg_id = '".$data[0]."'");
			 }
		}
	}
	fclose($handle);
	echo 'Done. Processed '.$tot_cnt.' rows';
	exit;
?>