<?
session_start();
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	if(!$isCrew && strpos($_SESSION['u_a'][1], 'news_send') === false) errorNEW('Ingen behörighet.');
	$page = 'UTSKICK';
	$menu = $menu_NEWS;
	$change = false;
	$show = false;
	$archive = false;
	$onl_archive = false;
	$error = '';

	if(!empty($_GET['archive'])) $archive = true;
	if(!empty($_GET['onl_archive'])) $onl_archive = true;

	if(!empty($_POST['ins_txt'])) {
		$send = '';
		if(!empty($_POST['id'])) {
			mysql_query("UPDATE s_send SET n_cmt = '".secureINS($_POST['ins_txt'])."', n_week = '".secureINS($_POST['ins_week'])."', n_date = NOW() WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
		} else {
			$unique = md5(microtime());
			mysql_query("INSERT INTO s_send SET unique_id = '$unique', n_cmt = '".secureINS($_POST['ins_txt'])."', n_week = '".secureINS($_POST['ins_week'])."', n_date = NOW()");
			if(mysql_insert_id()) $send = '?show='.mysql_insert_id();
		}
		header("Location: send.php".$send);
		exit;
	}

	if(!empty($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM s_send WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) == '1') {
			$change = true;
			$row = mysql_fetch_assoc($sql);
		}
	} elseif(!empty($_GET['show'])) {
		$sql = mysql_query("SELECT * FROM s_send WHERE main_id = '".secureINS($_GET['show'])."' LIMIT 1");
		$row = mysql_fetch_assoc($sql);
		$show = true;
	}

	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql = mysql_query("SELECT * FROM s_send WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(mysql_num_rows($sql)) {
			mysql_query("DELETE FROM s_send WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			header("Location: send.php");
			exit;
		}
	}

	$list = mysql_query("SELECT a.main_id, a.unique_id, a.n_week, a.n_cmt, a.tview_cnt, COUNT(b.unique_id) as view_cnt FROM s_send a LEFT JOIN s_sendvisit b ON b.category_id = a.unique_id AND b.unique_id != '' AND b.site_visit = '1' GROUP BY a.main_id ORDER BY a.n_week DESC, a.n_date DESC");

	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_adm.js"></script>
<script type="text/javascript" src="fnc_txt.js"></script>
<script type="text/javascript">
function loadtop() {
	if(parent.head)
	parent.head.show_active('send');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
	<table width="100%" height="100%">
<tr><td colspan="2"><?makeMenuAdmin($page, $menu, 0);?></td></tr>
	<tr>
		<td width="45%" style="padding: 0 10px 0 0">
			<table width="100%">
			<tr>
				<td height="25"><b><?=($change)?'Ändra':'Nytt';?> utskick</b></td>
			</tr>
			</table>
					<form action="send.php" method="post">
<?=($change)?'<input type="hidden" name="id" value="'.$row['main_id'].'">':'';?>
					<table style="margin-bottom: 15px;">
					<tr>
						<td colspan="2" style="padding: 0 0 0 0;">
							<table cellspacing="0">
							<tr>
								<td>
								Namn<br />
								<input type="text" name="ins_txt" class="inp_nrm" value="<?=($change)?secureOUT($row['n_cmt']):'';?>" />
								</td>
								<td style="padding-left: 5px;">
								Vecka<br />
								<input type="text" name="ins_week" class="inp_nrm" style="width: 77px;" value="<?=($change)?$row['n_week']:date("YW");?>" />
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input type="submit" value="Skicka" class="inp_realbtn" style="margin: 4px 0 0 10px;"></td>
					</tr>
					</table>
					</form>
					<hr /><div class="hr"></div>
			<table width="100%">
			<tr>
				<td height="25"><b>Befintliga utskick</b> [<a href="http://www.360sverige.se/nyheter/" target="_blank">MALL</a>]</td>
			</tr>
			</table>
			<table width="100%" cellspacing="0">
<?
	while($r = mysql_fetch_assoc($list)) {
		$del = mysql_result(mysql_query("SELECT COUNT(*) as count FROM s_senddelete WHERE category_id = '".secureINS($r['unique_id'])."'"), 0, 'count');
		echo '<tr><td>'.substr($r['unique_id'], 0, 5).' <a href="send.php?show='.$r['main_id'].'">VECKA '.$r['n_week'].' - '.secureOUT($r['n_cmt']).'</a> [LÄST:'.(($r['tview_cnt'])?'<a href="send_extract.php?id='.$r['unique_id'].'">'.$r['tview_cnt'].'</a>':'<b>0</b>').'] [BESÖKT:'.(($r['view_cnt'])?'<a href="send_extract.php?r=1&id='.$r['unique_id'].'">'.$r['view_cnt'].'</a>':'<b>0</b>').'] [X:'.(($del)?'<a href="send_extract.php?d=1&id='.$r['unique_id'].'">'.$del.'</a>':'<b>0</b>').']</td><td align="right"><a href="send.php?id='.$r['main_id'].'">ÄNDRA</a> | <a href="send.php?del='.$r['main_id'].'" onclick="return (confirm(\'Säker ?\'))?true:false;">RADERA</a></td></tr>';
		echo '<tr><td><td></tr>';
	}
?>
			</table>

		</td>
		<td width="55%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
<?
	if($show) {
?>
			<table width="100%">
			<tr>
				<td height="25"><b>VECKA <?=$row['n_week']?> - <?=$row['n_cmt']?></b></td>
			</tr>
			</table>
			<table cellspacing="0" style="margin: 0 0 10px 0;">
			<tr>
				<td>
				<table cellspacing="0">
				<tr><td><b>*LÄNK*</b><label> 5st i mall. Länk till startsida.</label><br><br><span style="font-family: monospace; font-size: 12px;" class="txt_chead">
<?=htmlentities('http://www.360sverige.se/tb_go.php?id='.secureOUT($row['unique_id']).'&m=[$E-post]')?>
				</span></td></tr>
				</table>

				<table cellspacing="0" style="margin: 20px 0 0 0;">
				<tr><td><b>*UNSUB*</b><label> 1st i mall. Länk för avregistrering.</label><br><br><span style="font-family: monospace; font-size: 12px;" class="txt_chead">
<?=htmlentities('http://www.360sverige.se/tb_del.php?id='.secureOUT($row['unique_id']).'&m=[$E-post]')?>
				</span></td></tr>
				</table>

				<table cellspacing="0" style="margin: 20px 0 0 0;">
				<tr><td><b>*DOLDBILD*</b><label> 1st i mall, längst ner. Loggar e-post.</label><br><br><span style="font-family: monospace; font-size: 12px;" class="txt_chead">
<?=htmlentities('<img style="visibility: hidden;" height="1" width="1" src="http://www.360sverige.se/tb_check.php?id='.secureOUT($row['unique_id']).'&m=[$E-post]" border="0">')?>
				</span></td></tr>
				</table>
				</td>
			</tr>
			</table>
			<hr /><div class="hr"></div>
<?	}	?>


		</td>
	</tr>
	</table>
</body>
</html>