<?
	require_once('config.php');

	$res = $db->getOneRow("SELECT id_id, flow_id, status_id, img_id FROM s_userpicvalid WHERE id_id = '".$user->id."' LIMIT 1");
	if (!empty($res) && count($res)) {
		if($res['status_id'] == '1')
			@readfile('./_input/preimages/'.$res['id_id'].'_'.$res['flow_id'].'.jpg');
		else if($res['status_id'] == '3') {
			$f = './_input/preimages/'.$res['id_id'].'_'.$res['flow_id'].'-pre.'.$res['img_id'];
			if (!@readfile('./_input/preimages/'.$res['id_id'].'_'.$res['flow_id'].'.jpg')) {
				$fp = fopen($f, 'rb');
				fpassthru($fp);
			}
		}
	} else {
		header('Location: '.$config['web_root'].'_gfx/u_nopic_2.gif');
	}
?>
