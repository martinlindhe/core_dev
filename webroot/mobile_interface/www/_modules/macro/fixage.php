<?
	$users = $sql->query("SELECT id_id, status_id, u_birth FROM {$t}user ORDER BY id_id DESC");
	foreach($users as $us) {
		if($us[1] != '1') {
			$res = $sql->queryResult("SELECT l.level_id FROM {$t}userlevel l WHERE l.id_id = '".$us[0]."' LIMIT 1");
			if(!empty($res)) $sql->queryUpdate("REPLACE INTO {$t}userlevel_off SET id_id = '".$us[0]."', level_id = '".secureINS($res)."'");
			$sql->queryUpdate("DELETE FROM {$t}userlevel WHERE id_id = '".$us[0]."' LIMIT 1");
			$org_data = $sql->queryResult("SELECT level_id FROM {$t}userlevel_off WHERE id_id = '".$us[0]."'");
			if(strpos($org_data, 'BIRTH') !== false) {
				$data = @explode("BIRTH", $org_data);
				if(count($data) > 1) {
					$data = explode(' ', $data[1]);
					$data = $data[0];
				}
			} else $data = false;

			if(strpos($org_data, 'AGEOF') !== false) {
				$ageof = @explode("AGEOF", $org_data);
				if(count($ageof) > 1) {
					$ageof = explode(' ', $ageof);
					$ageof = $ageof[0];
				}
			} else $ageof = false;
			if($data && $ageof) {
				$group = $user->doagegroup($user->doage($data));
				$org_data = str_replace('AGEOF'.$ageof, 'AGEOF'.$group, $org_data);
				$sql->queryUpdate("UPDATE {$t}userlevel_off SET
				level_id = '".$org_data."' WHERE id_id = '".$us[0]."'");
			} elseif(!$data && !$ageof) {
				$group = $user->doagegroup($user->doage($us[2]));
				$org_data .= ' AGEOF'.$group;
				$org_data .= ' BIRTH'.$us[2];
				$sql->queryUpdate("UPDATE {$t}userlevel_off SET
				level_id = '".$org_data."' WHERE id_id = '".$us[0]."'");
			}
		} else {
			$org_data = $sql->queryResult("SELECT level_id FROM {$t}userlevel WHERE id_id = '".$us[0]."'");

			if(strpos($org_data, 'BIRTH') !== false) {
				$data = @explode("BIRTH", $org_data);
				if(count($data) > 1) {
					$data = explode(' ', $data[1]);
					$data = $data[0];
				}
			} else $data = false;

			if(strpos($org_data, 'AGEOF') !== false) {
				$ageof = @explode("AGEOF", $org_data);
				if(count($ageof) > 1) {
					$ageof = explode(' ', $ageof);
					$ageof = $ageof[0];
				}
			} else $ageof = false;
			if($data && $ageof) {
				$group = $user->doagegroup($user->doage($data));
				$org_data = str_replace('AGEOF'.$ageof, 'AGEOF'.$group, $org_data);
				$sql->queryUpdate("UPDATE {$t}userlevel SET
				level_id = '".$org_data."' WHERE id_id = '".$us[0]."'");
			} elseif(!$data && !$ageof) {
				$group = $user->doagegroup($user->doage($us[2]));
				$org_data .= ' AGEOF'.$group;
				$org_data .= ' BIRTH'.$us[2];
				$sql->queryUpdate("UPDATE {$t}userlevel SET
				level_id = '".$org_data."' WHERE id_id = '".$us[0]."'");
			}
		}

	}
	exit;
?> 