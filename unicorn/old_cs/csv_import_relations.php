<?
	die;

	set_time_limit(0);
	include("_config/online.include.php");
	
	$tot_cnt = 0;
	$gallx_allowed = 0;
	$csv_file = '/home/martin/csbilder/harem_kompis.csv';
	$handle = fopen($csv_file, "r");

	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		$num = count($data);

		if($num != 5 && $num != 1) die('ok:'.$num);
		$inp = '';
		if($num == 5) {
			$tot_cnt++;

			for ($c=0; $c < $num; $c++) {
				$data[$c] = secureINS($data[$c]);
			}
			$check1 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
			$check2 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[2]."' AND status_id = '1' LIMIT 1");
			if($check1 && $check2) {
				$sql->queryUpdate("REPLACE INTO s_userrelation SET
				main_id = '".$data[0]."',
				user_id = '".$data[1]."',
				friend_id = '".$data[2]."',
				rel_id = 'Vän',
				gallx = '".$data[4]."',
				activated_date = NOW()
				");
				
				if ($data[4]) $gallx_allowed++;
				
				if(@$sql->queryUpdate("INSERT INTO s_userrelation SET
					main_id = '".$data[0]."',
					user_id = '".$data[2]."',
					friend_id = '".$data[1]."',
					rel_id = 'Vän',
					activated_date = NOW()") == -1) {
						@$sql->queryUpdate("UPDATE s_userrelation SET
						main_id = '".$data[0]."',
						rel_id = 'Vän',
						activated_date = NOW()
						WHERE
						user_id = '".$data[2]."' AND
						friend_id = '".$data[1]."'
						");
				}
			}
		}
	}
	fclose($handle);
	echo 'Done. Processed '.$tot_cnt.' rows. '.$gallx_allowed.' allowed to see gallery x stuff';
	exit;
?>