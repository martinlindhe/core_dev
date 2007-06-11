<?
	include(CONFIG.'secure.fnc.php');
	
	$q = "SELECT * FROM {$t}userphoto WHERE main_id = '".secureINS($key)."' LIMIT 1";
	$res = $sql->queryLine($q, 1);
	if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $s['id_id'] != $res['user_id'] || ($res['hidden_id'] && !$allowed)) {
		errorACT('Felaktigt galleriinlägg.', l('user', 'gallery', $s['id_id']));
	}
	
	if (!empty($_POST['chg_pic_desc']) && $res['user_id'] == $l['id_id']) {
		$q = 'UPDATE s_userphoto SET pht_cmt="'.secureINS($_POST['chg_pic_desc']).'" WHERE main_id = "'.secureINS($key).'"';
		$sql->queryUpdate($q);
		$res['pht_cmt'] = $_POST['chg_pic_desc'];
		reloadACT(l('user', 'gallery', $res['user_id']));
	}

	//radera kommentar
	if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
		$r = $sql->queryLine("SELECT main_id, status_id, user_id, id_id FROM {$t}userphotocmt WHERE main_id = '".secureINS($_GET['del_msg'])."' LIMIT 1");
		if(!empty($r) && count($r) && $r[1] == '1') {
			if($isAdmin || $r[2] == $l['id_id'] || $r[3] == $l['id_id']) {
				$re = $sql->queryUpdate("UPDATE {$t}userphotocmt SET status_id = '2' WHERE main_id = '".secureINS($r[0])."' LIMIT 1");
			}
			if($re) {
				$sql->queryUpdate("UPDATE {$t}userphoto SET pht_cmts = pht_cmts - 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			}
			reloadACT(l('user', 'gallery', $s['id_id'], $res['main_id']));
		}
	}
	
	//räkna visning av bilden
	if (!$own) {
		$hidden = $user->getinfo($l['id_id'], 'hidden_login');
		if (!$hidden) {
			$q = 'REPLACE INTO s_userphotovisit SET status_id = "1", visit_date=NOW(), visitor_id = "'.secureINS($l['id_id']).'", visitor_obj = "'.secureINS($res['main_id']).'"';
			$visit = $sql->queryUpdate($q);
			$beenhere = ($visit != '2')?false:true;
		}

		if (!$hidden && !$beenhere) {
			$sql->queryUpdate("UPDATE {$t}userphotovisit SET visit_item = visit_item + 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			$sql->queryUpdate("UPDATE {$t}userphotovisit SET status_id = '1', visit_date = NOW() WHERE visitor_id = '".secureINS($l['id_id'])."' AND photo_id = '".secureINS($res['main_id'])."' LIMIT 1");

			$q = 'UPDATE s_userphoto SET pht_click=pht_click+1 WHERE main_id='.secureINS($key);
			$sql->queryUpdate($q);
		}
	}

	$page = 'gallery';

	require(DESIGN.'head_user.php');

	$file_ext = explode('.', $res['old_filename']);
	$file_ext = stripslashes(strtolower($file_ext[count($file_ext)-1]));
	if (!$file_ext) $file_ext = $res['pht_name'];
?>

<div class="subHead">galleri</div><br class="clr"/>

<? makeButton(false, 'makePhotoComment('.$s['id_id'].','.$res['main_id'].')', 'icon_blog.png', 'skriv kommentar'); ?>
<br/><br/><br/>

<div class="bigHeader"><?=secureOUT($res['pht_cmt'])?> - publicerad: <?=nicedate($res['pht_date'])?></div>
<div class="bigBody cnt">
	<a name="view"></a>
<?
	switch ($file_ext) {
		case 'jpg':
		case 'jpeg':
		case 'gif':
		case 'png':
			echo '<img onmousedown="blockRightClick(event)" src="/_input/usergallery/'.$res['picd'].'/'.$res['main_id'].($res['hidden_id']?'_'.$res['hidden_value']:'').'.'.$file_ext.'" class="cnti mrg" alt="" border="0"/>';
			break;
		
		case '3gp':
			$vid_filename = '/_input/usergallery/'.$res['picd'].'/'.$res['main_id'].($res['hidden_id']?'_'.$res['hidden_value']:'').'.'.$file_ext;
			$vid_width = 176;
			$vid_height = 144;
			$vid_bg_color = '#000000';
			$show_controls = 'false';
			
		?>3gp video<br/>
			<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="<?=$vid_width?>" height="<?=$vid_height?>" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
			<param name="src" value="<?=$vid_filename?>">
			<param name="autoplay" value="true">
			<param name="type" value="video/quicktime">
			<param name="controller" value="<?=$show_controls?>">
			<embed src="<?=$vid_filename?>" width="<?=$vid_width?>" height="<?=$vid_height?>" autoplay="true" controller="<?=$show_controls?>" bgcolor="<?=$vid_bg_color?>" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed>
			</object>		
		<?
			break;
			
		default: die('ext '.$file_ext);
	}
?>
<? 	if (!empty($_GET['c'])) { ?>
	<div>
		<center>
		<form name="gal_change" method="post" action="">
			<table><tr>
				<td>
					Ändra bildbeskrivningen:
					<input type="text" name="chg_pic_desc" value="<?=secureOUT($res['pht_cmt'])?>"/>
				</td>
				<td width="66">
					<? makeButton(false, 'document.gal_change.submit();', 'icon_blog.png', 'spara'); ?>
				</td>
			</tr></table>
		</form>
		</center>

	</div>
<?	} ?>
</div><br/>


<div class="bigHeader">kommentarer</div>
<div class="bigBody">
<?
	$c_paging = paging(@$_GET['p'], 20);
	$c_paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphotocmt WHERE photo_id = '".$res['main_id']."' AND status_id = '1'");

	$odd = 1;
	$cmt = $sql->query("SELECT b.main_id, b.c_msg, b.c_date, b.c_html, b.private_id, u.* FROM {$t}userphotocmt b LEFT JOIN {$t}user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.photo_id = '".$res['main_id']."' AND b.status_id = '1' ORDER BY b.main_id DESC LIMIT {$c_paging['slimit']}, {$c_paging['limit']}", 0, 1);
	if(count($cmt) && !empty($cmt)) {
		foreach($cmt as $val) {
			if ($val['private_id'] && (!$own && !$isAdmin)) continue;
			$msg_own = ($val['id_id'] == $l['id_id'] || $own || $isAdmin)?true:false;
			$odd = !$odd;
			echo '
				<table summary="" cellspacing="0" style="width: 594px;'.($odd?'':' background: #ecf1ea;').'">
				<tr><td class="pdg" style="width: 55px;" rowspan="2">'.$user->getimg($val['id_id'].$val['u_picid'].$val['u_picd'].$val['u_sex'], $val['u_picvalid']).'</td><td class="pdg"><h5 class="l">'.$user->getstring($val, '', array('noimg' => 1)).' - '.nicedate($val['c_date']).($val['private_id']?' <b>[privat inlägg]</b>':'').'</h5><div class="r"></div><br class="clr" />
				'.secureOUT($val['c_msg']).'
				</td>';
		
			if ($msg_own) {
				echo '<td class="pdg" width="66"><br/>';
				makeButton(false, 'if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'gallery', $s['id_id'], $res['main_id']).'del_msg='.$val['main_id'].'\');', 'icon_delete.png', 'radera');
				echo '</td>';
			}
		
			echo '</tr></table>';

		}
	} else {
		echo '<table summary="" cellspacing="0" width="100%"><tr><td class="cnt pdg spac">Inga kommentarer.</td></tr></table>';
	}
?>
</div>

<?
	require(DESIGN.'foot_user.php');
?>