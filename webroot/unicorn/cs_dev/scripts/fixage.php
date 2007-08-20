<?
	require_once('../config.php');

	$users = $db->getArray('SELECT id_id, status_id, u_birth FROM s_user ORDER BY id_id DESC');
	foreach ($users as $us) {
		if ($us['status_id'] != '1')
		{
			$res = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$us['id_id']."' LIMIT 1");
			if (!empty($res)) $db->replace("REPLACE INTO s_userlevel_off SET id_id = '".$us['id_id']."', level_id = '".$db->escape($res)."'");
			$db->delete("DELETE FROM s_userlevel WHERE id_id = '".$us['id_id']."' LIMIT 1");

			$org_data = $db->getOneItem("SELECT level_id FROM s_userlevel_off WHERE id_id = '".$us['id_id']."' LIMIT 1");
			if (strpos($org_data, 'BIRTH') !== false) {
				$data = @explode("BIRTH", $org_data);
				if(count($data) > 1) {
					$data = explode(' ', $data[1]);
					$data = $data[0];
				}
			} else $data = false;

			if (strpos($org_data, 'AGEOF') !== false) {
				$ageof = @explode("AGEOF", $org_data);
				if(count($ageof) > 1) {
					$ageof = @explode(' ', $ageof);
					$ageof = $ageof[0];
				}
			} else $ageof = false;
			if ($data && $ageof) {
				$group = $user->doagegroup($user->doage($data));
				$org_data = str_replace('AGEOF'.$ageof, 'AGEOF'.$group, $org_data);
				$db->update("UPDATE s_userlevel_off SET level_id = '".$org_data."' WHERE id_id = '".$us['id_id']."'");
			} elseif(!$data && !$ageof) {
				$group = $user->doagegroup($user->doage($us['u_birth']));
				$org_data .= ' AGEOF'.$group;
				$org_data .= ' BIRTH'.$us['u_birth'];
				$db->update("UPDATE s_userlevel_off SET level_id = '".$org_data."' WHERE id_id = '".$us['id_id']."'");
			}
		} else {
			$org_data = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$us['id_id']."' LIMIT 1");

			if (strpos($org_data, 'BIRTH') !== false) {
				$data = @explode("BIRTH", $org_data);
				if(count($data) > 1) {
					$data = explode(' ', $data[1]);
					$data = $data[0];
				}
			} else $data = false;

			if (strpos($org_data, 'AGEOF') !== false) {
				$ageof = @explode("AGEOF", $org_data);
				if(count($ageof) > 1) {
					$ageof = @explode(' ', $ageof);
					$ageof = $ageof[0];
				}
			} else $ageof = false;
			if ($data && $ageof) {
				$group = $user->doagegroup($user->doage($data));
				$org_data = str_replace('AGEOF'.$ageof, 'AGEOF'.$group, $org_data);
				$db->update("UPDATE s_userlevel SET level_id = '".$org_data."' WHERE id_id = '".$us['id_id']."'");
			} elseif(!$data && !$ageof) {
				$group = $user->doagegroup($user->doage($us['u_birth']));
				$org_data .= ' AGEOF'.$group;
				$org_data .= ' BIRTH'.$us['u_birth'];
				$db->update("UPDATE s_userlevel SET level_id = '".$org_data."' WHERE id_id = '".$us['id_id']."'");
			}
		}

	}
	die;
?>
