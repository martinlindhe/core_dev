<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thispage = 'obj.php?status=cmtmv';
	$vimmel = &new vimmel($sql);
	$view_cmt = 0;
	if(!empty($_GET['all'])) {
		$view_cmt = 1;
	}

	if(!empty($_POST['validate'])) {
		$doall = false;
		if(!empty($_POST['main_id:all']) && is_numeric($_POST['main_id:all'])) {
			$doall = true;
		}
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(':', $key);
				$kid = $kid[1];
				if(isset($_POST['status_id:' . $kid])) {
					if($doall) {
						if(!empty($_POST['status_id:' . $kid])) {
// alla-knapp, men denna är markerad innan
							$sql->queryUpdate("UPDATE s_pmoviecmt SET view_id = '1' WHERE main_id = '".$kid."' LIMIT 1");
							$vimmel->vimmelUpdate('cmtmv', array('main_id' => $kid, 'm_id' => $_POST['m_id:' . $kid]), $_POST['status_id:' . $kid], $_POST['otatus_id:' . $kid], $_POST['mvstatus:' . $kid]);
						} else {
// alla-knapp
							$sql->queryUpdate("UPDATE s_pmoviecmt SET view_id = '1' WHERE main_id = '".$kid."' LIMIT 1");
							$vimmel->vimmelUpdate('cmtmv', array('main_id' => $kid, 'm_id' => $_POST['m_id:' . $kid]), $_POST['main_id:all'], $_POST['otatus_id:' . $kid], $_POST['mvstatus:' . $kid]);
						}
					} else {
						if($_POST['status_id:'.$kid]) {
							$sql->queryUpdate("UPDATE s_pmoviecmt SET view_id = '1' WHERE main_id = '".$kid."' LIMIT 1");
							$vimmel->vimmelUpdate('cmtmv', array('main_id' => $kid, 'm_id' => $_POST['m_id:' . $kid]), $_POST['status_id:' . $kid], $_POST['otatus_id:' . $kid], $_POST['mvstatus:' . $kid]);
						}
					}
				}
			}
		}
		header('Location: '.$thispage.'&all='.$view_cmt);
		exit;
	} elseif(!empty($_GET['del']) && is_numeric($_GET['del']) && isset($_GET['s']) && is_numeric($_GET['s'])) {
		$s = ($_GET['s'] == '2')?'0':'1';
		$vimmel->vimmelDelete('cmtmv', $_GET['del'], $s);
		header("Location: ".$thispage.'&all='.$view_cmt);
		exit;
	}


	$cmt_arr = array(
"0" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_pmoviecmt WHERE view_id = '0'"), 0, 'count'),
"1" => mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_pmoviecmt"), 0, 'count'));

	if($view_cmt) {
		$paging = paging(@$_GET['p'], 20);
		$list = $sql->query("SELECT a.*, b.topic_id, b.m_file, b.status_id as mvstatus, b.m_id, u.id_id, u.u_alias, u.u_picd, u.u_picvalid, u.u_picid FROM s_pmoviecmt a LEFT JOIN s_pmovie b ON b.m_id = a.unique_id LEFT JOIN s_user u ON u.id_id = a.logged_in AND u.status_id = '1' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} else {
		$list = $sql->query("SELECT a.*, b.topic_id, b.m_file, b.status_id as mvstatus, b.m_id, u.id_id, u.u_alias, u.u_picd, u.u_picvalid, u.u_picid FROM s_pmoviecmt a LEFT JOIN s_pmovie b ON b.m_id = a.unique_id LEFT JOIN s_user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.view_id = '0' ORDER BY a.main_id ASC", 0, 1);
#		$list = $sql->query("SELECT a.*, b.p_pic, b.main_id as picmain_id, b.statusID, b.status_id as mvstatus FROM s_pcmt a LEFT JOIN s_ppic b ON b.main_id = a.unique_id LEFT JOIN s_ptopic c ON c.main_id = a.topic_id WHERE a.view_id = '0' ORDER BY a.main_id DESC", 0, 1);
	}
	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$view_cmt)?' checked':'';?>><label for="view_0" class="txt_bld txt_look">Ogranskade</label> [<?=$cmt_arr[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($view_cmt)?' checked':'';?>><label for="view_1" class="txt_bld txt_look">Alla</label> [<?=$cmt_arr[1]?>]

			<form name="upd" method="post" action="./<?=$thispage?>&all=<?=$view_cmt?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">

			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">
<?
	if(isset($paging) && ($paging['p'] > 1 || $cmt_arr[$view_cmt] > $paging['slimit'] + $paging['limit'])) {
	$pm1 = $paging['p'] - 1;
	$pp1 = $paging['p'] + 1;
?>
<hr /><div class="hr"></div>
					<table width="100%">
					<tr>
						<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_cmt.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
						<td align="right" valign="center">
<?
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($cmt_arr[$view_cmt] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
						</td>
					</tr>
					</table>
<?	} else echo '<div>&nbsp;</div>';
?>
			<hr /><div class="hr"></div>
			<table width="100%">
<?
		foreach($list as $r) {
		$r['c_msg'] = trim($r['c_msg']);

		$pic = IMAGE_DIR.$r['topic_id'].'/'.$r['m_file'].'.jpg';
?>
			<tr><td>
			<table width="100%">
			<tr>
				<td colspan="2"<?=($r['mvstatus'] == '2' || $r['status_id'] == '2')?' class="bg_blk"':'';?>>
				<table width="100%"<?=($r['mvstatus'] == '2' || $r['status_id'] == '2')?' class="txt_wht"':'';?>>
				<tr><td colspan="2" style="padding-bottom: 5px;"><img src="./_img/status_<?=($r['view_id'] && $r['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:<?=$r['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($r['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:<?=$r['main_id']?>" onclick="changeStatus('status', this.id);"><?=
($r['logged_in'])?
	((!empty($r['id_id']))?'<a href="user.php?t&id='.$r['id_id'].'" class="txt_big user">'.secureOUT($r['u_alias']).'</a>':'[raderad]')
:
((!empty($r['c_email']))?
	'<a href="mailto:'.secureOUT($r['c_email']).'"><span class="txt_big">'.secureOUT($r['c_name']).'</span></a>'

:'<span class="txt_bld txt_big">'.secureOUT($r['c_name']).'</span>');?>
 - <em>inl&auml;gg skrivet <?=niceDate($r['c_date'])?></em> (#<?=$r['main_id']?>)</span></td></tr>
				<tr>
					<td style="width: 165px;"><img src="<?=$pic?>" style="margin-right: 5px;"></td>
					<td style="width: 100%;">
<?=($r['logged_in'] && $r['id_id'])?'<table cellspacing="0"><tr><td>'.$user->getphoto($r['id_id'].$r['u_picid'].$r['u_picd'], $r['u_picvalid'], 1, 1).'</td><td style="padding-left: 5px;"><div style="width: 350px; overflow: hidden;">'.(($r['c_html'])?safeOUT($r['c_msg']):secureOUT($r['c_msg'])).'</div></td></tr></table>':'<div style="width: 450px; overflow: hidden;">'.(($r['c_html'])?safeOUT($r['c_msg']):secureOUT($r['c_msg'])).'</div>';?></td>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td<?=($r['mvstatus'] == '2' || $r['status_id'] == '2')?' class="bg_blk"':'';?> style="padding: 8px 0 0 0;"><a href="search.php?t&s=<?=secureOUT($r['sess_id'])?>"><?=substr(secureOUT($r['sess_id']), 0, 5)?></a> | <a href="search.php?t&s=<?=secureOUT($r['sess_ip'])?>"><?=secureOUT($r['sess_ip'])?></a></td>
				<td align="right" style="height: 12px; padding: 8px 0 0 0;"<?=($r['mvstatus'] == '2' || $r['status_id'] == '2')?' class="bg_blk"':'';?>>
<input type="hidden" name="m_id:<?=$r['main_id']?>" value="<?=$r['m_id']?>">
<input type="hidden" name="mvstatus:<?=$r['main_id']?>" value="<?=$r['mvstatus']?>">
<input type="hidden" name="logged_id:<?=$r['main_id']?>" value="<?=$r['c_email']?>">
<input type="hidden" name="otatus_id:<?=$r['main_id']?>" value="<?=$r['status_id']?>">
<input type="hidden" name="status_id:<?=$r['main_id']?>" id="status_id:<?=$r['main_id']?>" value="<?=($r['view_id'])?$r['status_id']:'0';?>">
<input type="hidden" name="topic_id:<?=$r['main_id']?>" value="<?=$r['topic_id']?>">
<a href="<?=$thispage.'&all='.$view_cmt.'&del='.$r['main_id'].'&s='.$r['status_id'].'&mvstatus='.$r['mvstatus'];?>" onclick="return confirm('Säker ?');">RADERA</a>
				</td>
			</tr>
			<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>
<?
		}
?>
			</table>

<?
	if(isset($paging) && ($paging['p'] > 1 || $cmt_arr[$view_cmt] > $paging['slimit'] + $paging['limit'])) {
?>
			<table width="100%">
			<tr>
				<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'&all='.$view_cmt.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
				<td align="right" height="20" valign="center">
<?
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($cmt_arr[$view_cmt] > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&all='.$view_cmt.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
				</td>
			</tr>
			</table>
<?
	}
?>
		<hr /><div class="hr"></div>
			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">
			</form>
		</td>
	</tr>
	</table>
</body>
</html>