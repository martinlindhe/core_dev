<?
set_time_limit(0);



#$sql->db('cs_flytt');
$row = 0;
$handle = fopen("../harem_diary.csv", "r");
while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
	$row++;
	$num = count($data);
	echo "$num fields in line $row:<br />";
	if($num != 5 && $num != 1) die('ok:'.$num);
	$inp = '';
	#if($row > 10) die();
	if($num == 5) {
		for ($c=0; $c < $num; $c++) {
			$data[$c] = secureINS($data[$c]);
		}
		$check1 = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE id_id = '".$data[1]."' AND status_id = '1' LIMIT 1");
		if($check1) {
$sql->queryUpdate("REPLACE INTO s_userblog SET
main_id = '".$data[0]."',
user_id = '".$data[1]."',
status_id = '1',
blog_idx = '".$data[2]."',
blog_date = '".$data[2]."',
blog_cmt = '".$data[3]."',
blog_title = '".$data[4]."'
");
		 }
	}
}
fclose($handle);
exit;
?>