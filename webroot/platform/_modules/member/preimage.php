<?

	$res = $sql->queryLine("SELECT id_id, flow_id, status_id, img_id FROM {$t}userpicvalid WHERE id_id = '".secureINS($l['id_id'])."' LIMIT 1");
	if(!empty($res) && count($res)) {
		if($res[2] == '1')
			@readfile('./_input/preimages/'.$res[0].'_'.$res[1].'.jpg');
		elseif($res[2] == '3') {
			$f = './_input/preimages/'.$res[0].'_'.$res[1].'-pre.'.$res[3];
			if(!@readfile('./_input/preimages/'.$res[0].'_'.$res[1].'.jpg')) {
				$fp = fopen($f, 'rb');
				fpassthru($fp);
			}
		}
	} else {
		header('Location: '.OBJ.'u_nopic_2.gif');
	}


?>