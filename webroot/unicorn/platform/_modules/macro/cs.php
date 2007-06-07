<?
set_time_limit(0);



#$sql->db('cs_flytt');
$row = 0;
$handle = fopen("../harem_users.csv", "r");
while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
	$row++;
	$num = count($data);
	echo "$num fields in line $row:<br />";
	if($num != 68 && $num != 1) die('ok');
	$inp = '';
	if($num == 68) {
		for ($c=0; $c < $num; $c++) {
			#$inp .= ($c?',':'')."'".secureINS($data[$c])."'";
			$data[$c] = secureINS($data[$c]);
		}
		if($data[4] == 'True') $data[4] = 'F'; else $data[4] = 'M';
		if($data[10] == 'False') $data[10] = '2'; else $data[10] = '1';
		if($data[65] == 'True') $data[10] = '3';
$sql->queryUpdate("REPLACE INTO s_user SET
id_id = '".$data[0]."',
u_alias = '".$data[1]."',
u_pass = '".$data[2]."',
u_email = '".$data[3]."',
u_sex = '".$data[4]."', 
u_birth = '".$data[6]."',
u_regdate = '".$data[8]."',
lastlog_date = '".$data[9]."',
account_date = '".$data[9]."',
lastonl_date = '".$data[9]."',
status_id = '".$data[10]."',
u_pstort = '".$data[13]."',
u_oldpic = '".$data[60]."'
");
$sql->queryUpdate("REPLACE INTO s_userinfo SET
id_id = '".$data[0]."'
");
if($data[10] == '1') {
	$birth = explode(' ', $data[6]);
	$birth = $birth[0];
	$age = $user->doage($birth);
	$group = $user->doagegroup($age);
	$sql->queryUpdate("REPLACE INTO s_userlevel SET
	id_id = '".$data[0]."',
	level_id = 'ACTIVE SEX".$data[4]." LEVEL1 BIRTH".$birth." AGEOF".$group." ORT".str_replace(' ', '', strtoupper($data[13]))." LÄN".str_replace(' ', '', strtoupper($data[13]))."'");
}
$sql->queryUpdate("REPLACE INTO s_userbirth SET
id_id = '".$data[0]."',
level_id = '".$data[6]."'");

$user->obj_set('login_offset', 'user_head', $data[0], $data[11]);
$user->obj_set('user_pres', 'user_profile', $data[0], nl2br($data[5]));
#$user->obj_set('gal_offset', 'user_head', $data[0], $data[12]);


	}
	#	if($num == 68) {
	#		$sql->queryInsert("INSERT INTO harem_users VALUES (".$inp.")");
	#	}
	#if($row > 3) die();
}
fclose($handle);
exit;

/*





function mb_csv_split($line, $delim = ';', $removeQuotes = true) {
   $fields = array();
   $fldCount = 0;
   $inQuotes = false;

   for ($i = 0; $i < mb_strlen($line); $i++) {
       if (!isset($fields[$fldCount])) $fields[$fldCount] = "";
       $tmp = mb_substr($line, $i, mb_strlen($delim));
       if ($tmp === $delim && !$inQuotes) {
           $fldCount++;
           $i+= mb_strlen($delim) - 1;
       } 
       else if ($fields[$fldCount] == "" && mb_substr($line, $i, 1) == '"' && !$inQuotes) {
           if (!$removeQuotes) $fields[$fldCount] .= mb_substr($line, $i, 1);
           $inQuotes = true;
       } 
       else if (mb_substr($line, $i, 1) == '"') {
           if (mb_substr($line, $i+1, 1) == '"') {
               $i++;
               $fields[$fldCount] .= mb_substr($line, $i, 1);
           } else {
               if (!$removeQuotes) $fields[$fldCount] .= mb_substr($line, $i, 1);
               $inQuotes = false;
           }
       }
       else {
           $fields[$fldCount] .= mb_substr($line, $i, 1);
       }
   }
   return $fields;
}
count(mb_csv_split(file_get_contents("./_modules/macro/harem_users.csv")));



*/
?>