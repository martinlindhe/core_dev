<?
	$res = $sql->queryLine("SELECT id_id, u_picid, u_picvalid, u_picd, u_sex FROM s_user WHERE id_id = '".secureINS(intval($id))."' LIMIT 1");
	if(!empty($res) && count($res)) {
		if($res[2] == '1')
			@readfile(USER_IMG.$res[3].'/'.$res[0].$res[1].'_2.jpg');
		else {
			header('Location: '.OBJ.'u_noimg'.$res[4].'_2.gif');
		}
	} else {
		header('Location: '.OBJ.'u_noimg'.$res[4].'_2.gif');
	}
?>