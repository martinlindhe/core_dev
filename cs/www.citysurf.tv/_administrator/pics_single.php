<?
session_start();

/*    ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');*/
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');

	require("./set_onl.php");
	$vimmel = &new vimmel();
	$next = false;
	$c = true;
	#if(!empty($_GET['n'])) { $next = true; }

	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'pics') === false) errorNEW('Ingen behörighet.');
	if(empty($_SESSION['ins_name'])) $_SESSION['ins_name'] = $_SESSION['u_n'];

	$start = execSt();


	if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
		$msg = 'Felaktigt bildnummer.';
		$js_ex = 'window.close();';
		require("./_tpl/notice_admin.php");
		exit;
	}

	$sql = mysql_query("SELECT a.main_id, a.id, a.topic_id, a.order_id, a.status_id, a.statusID, b.p_name, a.p_view, a.p_cmt, a.p_pic, b.main_id AS mid, b.status_id AS topicstatus_id, b.p_date, b.p_dday, b.owner_id, c.p_text AS wm FROM (s_ppic a, s_ptopic b) LEFT JOIN s_powner c ON a.owner_id = c.main_id WHERE a.main_id = '".secureINS($_GET['id'])."' AND b.main_id = a.topic_id");

	if(!mysql_num_rows($sql)) {
		$msg = 'Felaktigt bildnummer.';
		$js_ex = 'window.close();';
		require("./_tpl/notice_admin.php");
		exit;
	}

	$row = mysql_fetch_assoc($sql);

	if($row['status_id'] == '2') {
		$pic = $row['topic_id'].'/'.$row['id'].'-'.$row['statusID'].'.'.$row['p_pic'];
	} else {
		$pic = $row['topic_id'].'/'.$row['id'].'.'.$row['p_pic'];
	}

	#if(!file_exists(ADMIN_IMAGE_DIR.$pic)) {
	#	$msg = 'Felaktigt bildnummer.';
	#	$js_ex = 'window.close();';
	#	require("./_tpl/notice_admin.php");
	#	exit;
	#}
	$pic = IMAGE_DIR.$pic;
	if(!empty($_POST['ins_msg'])) {
		mysql_query("INSERT INTO $cmt_tab SET logged_in = '".$_SESSION['c_i']."', unique_id = '".secureINS($row['main_id'])."', topic_id = '".secureINS($row['topic_id'])."', c_html = '1', pic_id = '".secureINS($row['id'])."', c_msg = '".secureINS($_POST['ins_msg'])."', status_id = '1', c_date = NOW(), sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', sess_id = '".secureINS($cookie_id)."'");
#		mysql_query("INSERT INTO $cmt_tab SET logged_in = '".$_SESSION['c_i']."', unique_id = '".secureINS($row['main_id'])."', topic_id = '".secureINS($row['topic_id'])."', pic_id = '".secureINS($row['id'])."', c_msg = '".secureINS($_POST['ins_msg'])."', status_id = '1', c_date = NOW(), sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
		sesslogADD($row['main_id'], mysql_insert_id(), 'CMT');
		mysql_query("UPDATE $pic_tab SET p_cmt = p_cmt + 1 WHERE main_id = '".secureINS($row['main_id'])."' LIMIT 1");
		if($row['status_id'] == '1')
			mysql_query("UPDATE $topic_tab SET p_cmts = p_cmts + 1 WHERE main_id = '".secureINS($row['topic_id'])."' LIMIT 1");
		header('Location: pics_single.php?id='.$row['main_id']);
		exit;
	}

	if(!empty($_POST['dopost'])) {
		$sql = mysql_query("SELECT main_id, topic_id, id, p_pic, status_id, statusID, p_view, p_cmt FROM $pic_tab WHERE main_id = '".secureINS($row['main_id'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$row = mysql_fetch_assoc($sql);
			$vimmel->vimmelUpdate('pic', $row, @$_POST['status_id'], @$_POST['o_id']);
			$blockID = $vimmel->vimmelUpdate('file', $row, @$_POST['status_id'], @$_POST['o_id']);
			mysql_query("UPDATE $pic_tab SET status_id = '".secureINS($_POST['status_id'])."', ".(($blockID)?"statusID = '".secureINS($blockID)."', ":'')."order_id = '".secureINS($_POST['order_id'])."' WHERE main_id = '".secureINS($row['main_id'])."' LIMIT 1");
		}
		header("Location: pics_single.php?id={$row['main_id']}&rel=1");
		exit;
	}

	if(!empty($_GET['del']) && is_numeric($_GET['del']) && isset($_GET['status']) && is_numeric($_GET['status'])) {
		$check = mysql_query("SELECT status_id FROM $cmt_tab WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(mysql_num_rows($check) > 0) {
			mysql_query("DELETE FROM $cmt_tab WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			if(mysql_result($check, 0, 'status_id') == '1') {
				mysql_query("UPDATE $pic_tab SET p_cmt = p_cmt - 1 WHERE main_id = '".secureINS($row['main_id'])."' LIMIT 1");
				if($row['status_id'] == '1')
					mysql_query("UPDATE $topic_tab SET p_cmts = p_cmts - 1 WHERE main_id = '".secureINS($row['topic_id'])."' LIMIT 1");
			}
			header("Location: pics_single.php?id={$row['main_id']}&rel=1");
			exit;
		}

	}

	if(!empty($_GET['invert']) && is_numeric($_GET['invert'])) {
		$check = mysql_query("SELECT status_id FROM $cmt_tab WHERE main_id = '".secureINS($_GET['invert'])."' LIMIT 1");
		if(mysql_num_rows($check) > 0) {
			$check = mysql_result($check, 0, 'status_id');
			$check = ($check == '1' || $check == '2')?(($check == '1')?'2':'1'):'0';
			if($check == '1') {
				mysql_query("UPDATE $pic_tab SET p_cmt = p_cmt + 1 WHERE main_id = '".secureINS($row['main_id'])."' LIMIT 1");
				if($row['status_id'] == '1')
					mysql_query("UPDATE $topic_tab SET p_cmts = p_cmts + 1 WHERE main_id = '".secureINS($row['topic_id'])."' LIMIT 1");
			}

			if($check == '2') {
				mysql_query("UPDATE $pic_tab SET p_cmt = p_cmt - 1 WHERE main_id = '".secureINS($row['main_id'])."' LIMIT 1");
				if($row['status_id'] == '1')
					mysql_query("UPDATE $topic_tab SET p_cmts = p_cmts - 1 WHERE main_id = '".secureINS($row['topic_id'])."' LIMIT 1");
			}
			mysql_query("UPDATE $cmt_tab SET status_id = '$check' WHERE main_id = '".secureINS($_GET['invert'])."' LIMIT 1");
			header("Location: pics_single.php?id={$row['main_id']}&rel=1");
			exit;
		}
	}
$b = "SELECT main_id FROM $pic_tab WHERE (order_id < '{$row['order_id']}' AND main_id != '{$row['main_id']}' AND topic_id =  '".secureINS($row['topic_id'])."') OR (order_id = '{$row['order_id']}' AND main_id < '{$row['main_id']}' AND topic_id =  '".secureINS($row['topic_id'])."') ORDER BY order_id DESC, main_id DESC LIMIT 1";
	$dirs['b'] = @mysql_result(@mysql_query($b), 0, 'main_id');
$f = "SELECT main_id FROM $pic_tab WHERE (order_id > '{$row['order_id']}' AND main_id != '{$row['main_id']}' AND topic_id =  '".secureINS($row['topic_id'])."') OR (order_id = '{$row['order_id']}' AND main_id > '{$row['main_id']}' AND topic_id =  '".secureINS($row['topic_id'])."') ORDER BY order_id, main_id ASC LIMIT 1";
	$dirs['f'] = @mysql_result(@mysql_query($f), 0, 'main_id');
	$limit = 20;
	if(isset($_GET['p']) && $_GET['p'] > '1') {
		$p = $_GET['p'];
	} else {
		$p = 1;
	}

	$gpid = $p+1;
	$gmid = $p-1;

	$slimit = ($p-1) * $limit;

	$cmt = mysql_query("SELECT a.main_id, a.unique_id, a.logged_in, a.status_id, u.u_alias, a.sess_ip, a.sess_id, a.c_msg, a.c_date FROM $cmt_tab a INNER JOIN {$tab['user']} u ON u.id_id = a.logged_in WHERE a.unique_id = '".secureINS($row['main_id'])."' ORDER BY c_date DESC LIMIT $slimit, $limit");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>VIMMEL | <?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
	<meta http-equiv="imagetoolbar" content="no">
	<script type="text/javascript" src="fnc_adm.js"></script>
<script type="text/javascript">
<?=($next)?"
if(window.opener) window.opener.location.reload();
".((!empty($dirs['f']))?"document.location.href = 'pics_single.php?id=".$dirs['f']."';":'')."
":'';?>
<?=(!empty($_GET['rel']))?'if(window.opener) window.opener.location.reload();':'';?>
</script>
</head>
<body class="bg_fade" style="margin: 0; padding: 4px 0 0 0;">
			<center>
			<form name="e" method="post" action="pics_single.php?id=<?=$row['main_id']?>">
			<input type="hidden" name="dopost" value="1">
			<input type="hidden" name="status_id" id="status_id:<?=$row['main_id']?>" value="<?=$row['status_id']?>">
			<input type="hidden" name="o_id" value="<?=$row['status_id']?>">
			<table cellspacing="0">
			<tr>
				<td style="padding-bottom: 10px;"><?=($dirs['b'])?'<a href="pics_single.php?id='.$dirs['b'].'">bakåt</a>':'<b><strike>bakåt</strike></b>';?></td>
				<td style="padding-bottom: 10px;" class="cnt"><a href="javascript:window.close();"><?=stripslashes($row['p_name'])?> - <?=specialDate($row['p_date'], $row['p_dday'])?></a> #<?=$row['main_id']?></td>
				<td style="padding-bottom: 10px;" align="right"><?=($dirs['f'])?'<a href="pics_single.php?id='.$dirs['f'].'">framåt</a>':'<b><strike>framåt</strike></b>';?></td>
			</tr>
			<tr>
				<td><input type="text" name="order_id" value="<?=$row['order_id']?>" style="width: 24px; padding: 0; margin-bottom: 2px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="3" class="inp_nrm"></td>
				<td class="cnt"><input type="submit" class="inp_orgbtn" style="margin: 0;" value="Uppdatera"></td>
				<td align="right"><img src="./_img/status_<?=($row['status_id'] == '1')?'green':'none';?>.gif" style="margin: 2px 1px 0 0;" id="1:<?=$row['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($row['status_id'] == '2')?'red':'none';?>.gif" style="margin: 2px 0 0 1px;" id="2:<?=$row['main_id']?>" onclick="changeStatus('status', this.id);"></td>
			</tr>
			<tr>
				<td colspan="3" style="padding: 2px 0 5px 0;">
					<table cellspacing="0">	<tr><td class="cur wht" onclick="<?=($dirs['f'])?'document.location.href = \'pics_single.php?id='.$dirs['f'].'\';':'window.close();';?>" style="width: <?=$upl_full[0]?>px; height: <?=$upl_full[1]?>px; background-image: url('<?=$pic?>');" align="right"><?=$row['wm']?>&nbsp;</td></tr>
						<tr><td align="center" style="padding-top: 2px;">
							<div class="min" style="float: left;"><img src="./_img/icon_view.gif">&nbsp;<b><?=$row['p_view']?></strong></div>
							<div class="min" style="float: right;"><img src="./_img/icon_cmt.gif">&nbsp;<b><?=$row['p_cmt']?></strong></div>
						</td></tr>
					</table>
				</td>
			</tr>
			</table>
			</form>
			<table cellspacing="0" width="658" style="margin: 0 0 10px 0;">
			<tr>
				<td>
				<table width="100%" cellspacing="0">
<?
	if(mysql_num_rows($cmt)) { while($r = mysql_fetch_assoc($cmt)) {
		if($r['status_id'] == '2') {
			$c = ' bg_blk wht';
		} else {
			$c = '';
		}
?>
				<tr><td colspan="2" style="padding: 4px 0 6px 0;"><img src="./_img/dots_min.gif" width="150"></td></tr>
				<tr class="<?=$c?>"><td colspan="2" style="padding: 4px 10px 0 2px;" class="low"><a class="user" target="<?=FS?>main" href="user.php?t&id=<?=$r['logged_in'].'">'.secureOUT($r['u_alias']).'</a>';?> - <span class="em"><?=niceDate($r['c_date'])?></span></td></tr>
				<tr class="<?=$c?>"><td colspan="2" style="padding: 2px 10px 4px 2px;"><?=secureOUT($r['c_msg'])?></td></tr>
				<tr class="<?=$c?>">
					<td style="padding: 2px 0 0 2px;"><a href="gb.php?t&s=<?=secureOUT($r['sess_id'])?>" target="main" onclick="window.opener.focus();"><?=substr(secureOUT($r['sess_id']), 0, 5)?></a> | <a href="gb.php?t&s=<?=secureOUT($r['sess_ip'])?>" target="main" onclick="window.opener.focus();"><?=secureOUT($r['sess_ip'])?></a></td>
					<td style="padding: 2px 10px 4px 0;" align="right"><? if($r['status_id']) { ?><a href="pics_single.php?id=<?=$row['main_id']?>&invert=<?=$r['main_id']?>"><?=($r['status_id'] == '1')?'NEKA':'TILLÅT';?></a> | <a href="pics_single.php?id=<?=$row['main_id']?>&del=<?=$r['main_id']?>&status=<?=$r['status_id']?>">RADERA</a><? } else echo '<em>ej granskad</em>'; ?></td>
				</tr>
<?
	} } else echo '<tr><td colspan="2" style="padding: 4px 0 6px 0;"><img src="./_img/dots_min.gif" width="150"></td></tr><tr><td class="cnt" style="padding: 4px 0 0 0;">Inga kommentarer.</td></tr>';
?>
				</table>
				</td>
				<td width="266" style="padding: 4px 0 0 0;">
				<form action="pics_single.php?id=<?=$row['main_id']?>" method="post" name="cmt_w">
				<table cellspacing="0">
				<tr>
					<td><textarea style="width: 210px; height: 50px; margin: 0;" class="inp_nrm" tabindex="3" name="ins_msg"></textarea><script type="text/javascript">if(document.cmt_w.ins_name.value.length > 0) document.cmt_w.ins_msg.focus(); else document.cmt_w.ins_name.focus();</script></td>
					<td><input type="submit" class="inp_orgbtn" tabindex="4" value="SKICKA" style="width: 50px; height: 50px; margin: 1px 0 0 5px;"></td>
				</tr>
				</table>
				</form>
				</td>
			</tr>
			</table>			
			</center>
</body>
</html>