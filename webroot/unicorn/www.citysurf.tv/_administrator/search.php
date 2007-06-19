<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');

	$limit = 20;
	$ip_limit = 20;
	require("./set_formatadm.php");
	require("./set_onl.php");
	$sql = &new sql();
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'stat') === false) errorNEW('Ingen behörighet.');
	$page = 'SEARCH';
	$menu = $menu_SEARCH;

	$search = false;
	$showban = true;
	$ban = false;

	if($isCrew)
		$view = 'ss';
	else $view = 's';
	if(!empty($_GET['view']) && (($_GET['view'] == 's' && ($isCrew || strpos($_SESSION['u_a'][1], 'stat_s') !== false)) || ($_GET['view'] == 'ss' && ($isCrew || strpos($_SESSION['u_a'][1], 'stat_ss') !== false)) || ($_GET['view'] == 'sss' && ($isCrew || strpos($_SESSION['u_a'][1], 'stat_sss') !== false)))) {
		$view = $_GET['view'];
	}

	if(!empty($_GET['s'])) {
		$view = 's';
		$str = $_GET['s'];
		$search = true;
	}
	if(!empty($_POST['s'])) {
		$str = $_POST['s'];
		$search = true;
	}
	if(!empty($_GET['s1']) || !empty($_GET['s2'])) {
		$search = true;
	}

	if(!empty($_GET['ban'])) {
		$ban = true;
		$ban_ip = $_GET['ban'];
	} elseif(!empty($_POST['ban'])) {
		$ban = true;
		$ban_ip = $_POST['ban'];
	}
	if($ban) {
		mysql_query("INSERT INTO {$t}ban SET ban_ip = '".secureINS($ban_ip)."', ban_date = NOW(), ban_reason = ''");
		header("Location: search.php".((isset($_GET['t']))?'?t':''));
		exit;
	}
	if(!empty($_GET['ban_del']) && is_numeric($_GET['ban_del'])) {
		$res = mysql_query("SELECT * FROM {$t}ban WHERE main_id = '".secureINS($_GET['ban_del'])."' LIMIT 1");
		if(mysql_num_rows($res) == '1') {
			mysql_query("DELETE FROM {$t}ban WHERE main_id = '".secureINS($_GET['ban_del'])."'");
			header("Location: search.php");
			exit;
		}
	}

	if(!empty($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] > 1) {
		$p = $_GET['p'];
	} else {
		$p = 1;
	}
	$slimit = ($p-1) * $limit;
	$ext = $p * $limit;
	$pp1 = $p + 1;
	$pm1 = $p - 1;


	if($search && $view == 's') {
			if(is_numeric($str)) {
				$s_sql = mysql_query("SELECT a.date_cnt, SUBSTRING(a.sess_id, 1, 5) as sess_id, a.sess_ip, a.type_inf, a.unique_id, a.category_id FROM $log_tab a WHERE a.sess_id = '".secureINS($str)."' ORDER BY a.date_cnt DESC");
			} else {
				$s_sql = mysql_query("SELECT a.date_cnt, SUBSTRING(a.sess_id, 1, 5) as sess_id, a.sess_ip, a.type_inf, a.unique_id, a.category_id FROM $log_tab a WHERE a.sess_ip = '".secureINS($str)."' ORDER BY a.date_cnt DESC");
			}
	} elseif($view == 'ss') {
		$info = array(
's_user' => array('Användardata', 'a.id_id', array('a.u_alias', 'a.u_email'), "a.u_regdate, a.id_id, a.status_id, u.id_id, a.u_alias, a.u_birth, a.u_email", 0, '', 'a.id_id DESC', 'user.php?del='),
's_user_info' => array('Användarinfo', 'a.id_id', array('u.u_alias', 'a.u_fname', 'a.u_sname', 'a.u_street', 'a.u_cell'), "u.u_regdate, a.id_id, u.status_id, u.id_id, u.u_alias, a.u_fname, a.u_sname, a.u_street, a.u_cell", 0, '', 'a.id_id DESC', 'user.php?del='),
's_userblog' => array('Blogg', 'a.user_id', array('u.u_alias', 'a.blog_cmt', 'a.blog_title'), "a.blog_date, a.main_id, a.status_id, u.id_id, u.u_alias, a.blog_cmt, a.blog_title", 0, '', 'a.main_id DESC', 'obj.php?t&status=blog&del='),
's_userblogcmt' => array('Bloggkommentarer', 'a.user_id', array('u.u_alias', 'a.c_msg'), "a.c_date, a.main_id, a.status_id, u.id_id, u.u_alias, a.c_msg", 0, '', 'a.main_id DESC', 'obj.php?t&status=blogcmt&del='),
's_userchat' => array('Chat', 'a.sender_id', array('u.u_alias', 'u2.u_alias', 'a.sent_cmt'), "a.sent_date, a.main_id, 1, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.sent_cmt", 1, 'a.user_id', 'a.main_id DESC', 'obj.php?t&status=chat&del='),
's_djthought' => array('Diskutera (DJ/Karaoke)', 'a.logged_in', array('u.u_alias', 'u2.u_alias', 'a.gb_msg', 'a.answer_msg'), "a.gb_date, a.main_id, a.status_id, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.gb_msg, a.answer_msg", 1, 'a.answer_id', 'a.main_id DESC', 'obj.php?t&status=thought&del='),
's_usergb' => array('Gästbok', 'a.sender_id', array('u.u_alias', 'u2.u_alias', 'a.sent_cmt'), "a.sent_date, a.main_id, a.status_id, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.sent_cmt", 1, 'a.user_id', 'a.main_id DESC', 'obj.php?t&status=gb&del='),
's_pmoviecmt' => array('Filmkommentarer', 'logged_in', array('u.u_alias', 'a.c_msg'), "a.c_date, a.main_id, a.status_id, u.id_id, u.u_alias, a.c_msg", 0, '', 'a.main_id DESC', 'obj.php?t&status=mvcmt&del='),
's_f' => array('Forum', 'a.sender_id', array('u.u_alias', 'a.sent_cmt', 'a.sent_ttl'), "a.sent_date, a.main_id, a.status_id, u.id_id, u.u_alias, a.sent_cmt", 0, '', 'a.main_id DESC', 'obj.php?t&status=forum&del='),
's_userphoto' => array('Foton (Namn på foton)', 'a.user_id', array('u.u_alias', 'a.pht_cmt'), "a.pht_date, a.main_id, a.status_id, u.id_id, u.u_alias, a.pht_cmt", 0, '', 'a.main_id DESC', 'obj.php?t&status=photo&del='),
's_userphotocmt' => array('Fotokommentarer', 'a.user_id', array('u.u_alias', 'a.c_msg'), "a.c_date, a.main_id, u.id_id, a.status_id, u.u_alias, a.c_msg", 0, '', 'a.main_id DESC', 'obj.php?t&status=blogcmt&del='),
's_usermail' => array('Mail', 'a.sender_id', array('u.u_alias', 'u2.u_alias', 'a.sent_cmt', 'a.sent_ttl'), "a.sent_date, a.main_id, a.status_id, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.sent_cmt", 1, 'a.user_id', 'a.main_id DESC', 'obj.php?t&status=mail&del='),
's_obj' => array('Objekttabellen (Pres, Post-it osv)', 'a.owner_id', array('u.u_alias', 'a.content', 'a.content_type'), "a.obj_date, a.main_id, 1, u.id_id, u.u_alias, a.content_type, a.content", 0, '', 'a.main_id DESC', 'obj.php?t&status=obj&del='),
's_cal' => array('Partyplanket', 'a.user_id', array('u.u_alias', 'a.did_cmt'), "a.date_cnt, a.main_id, a.status_id, u.id_id, u.u_alias, a.day_cnt, a.did_cmt", 0, '', 'a.main_id DESC', 'obj.php?t&status=cal&del='),
's_thought' => array('Tyck till', 'a.logged_in', array('u.u_alias', 'u2.u_alias', 'a.gb_msg', 'a.answer_msg'), "a.gb_date, a.main_id, a.status_id, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.gb_msg, a.answer_msg", 1, 'a.answer_id', 'a.main_id DESC', 'obj.php?t&status=thought&del='),
's_pcmt' => array('Vimmelkommentarer', 'a.logged_in', array('u.u_alias', 'a.c_msg'), "a.c_date, a.main_id, a.status_id, u.id_id, u.u_alias, a.c_msg, a.unique_id", 0, '', 'a.main_id DESC', 'obj.php?t&status=cmt?del=')
		);

	} elseif($search && $view == 'sss') {
		$g1 = false;
		$g2 = false;
		if(!empty($_GET['s1'])) {
			$i1 = $sql->queryResult("SELECT id_id FROM {$t}user WHERE u_alias = '".secureINS($_GET['s1'])."' LIMIT 1");
			if(!empty($i1)) $g1 = true;
		}
		if(!empty($_GET['s2'])) {
			$i2 = $sql->queryResult("SELECT id_id FROM {$t}user WHERE u_alias = '".secureINS($_GET['s2'])."' LIMIT 1");
			if(!empty($i2)) $g2 = true;
		}
		$info = array(
's_userchat' => array('Chat', 'a.sender_id', 'a.user_id', array('a.sender_id', 'a.user_id'), "a.sent_date, a.main_id, 1, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.sent_cmt", 'a.main_id DESC', 'obj.php?t&status=chat&del='),
's_usergb' => array('Gästbok', 'a.sender_id', 'a.user_id', array('a.sender_id', 'a.user_id'), "a.sent_date, a.main_id, a.status_id, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.sent_cmt", 'a.main_id DESC', 'obj.php?t&status=gb&del='),
's_usermail' => array('Mail', 'a.sender_id', 'a.user_id', array('a.sender_id', 'a.user_id'), "a.sent_date, a.main_id, a.status_id, u.id_id, u.u_alias, u2.id_id, u2.u_alias, a.sent_cmt", 'a.main_id DESC', 'obj.php?t&status=mail&del=')
		);
	}

	if($showban) {
		# IP BAN
		$b_sql = mysql_query("SELECT * FROM {$t}ban ORDER BY ban_date DESC");
		$b_count = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}ban"), 0, 'count');
	}


	require("./_tpl/admin_head.php");

?>
<script type="text/javascript" src="fnc_adm.js"></script>
<script language="JavaScript" type="text/javascript">
function loadtop() {
	if(parent.<?=FRS?>head)
	parent.<?=FRS?>head.show_active('search');
}
toggle = 0;
function selectingAll(selecting) {
	if(toggle) toggle = 0; else toggle = 1;
	for(var i = 0; i < selecting.length; i++){
		selecting.options[i].selected = toggle;
	}
}

<?=(isset($_GET['t']))?'loadtop();':'';?>
</script>
	<table width="100%" height="100%">
<?makeMenuAdmin($page, $menu);?>
	<tr>
		<td width="75%" style="padding: 0 10px 0 0;">

			<table width="100%">
			<tr>
				<td height="35">
<? if($isCrew || strpos($_SESSION['u_a'][1], 'search_ss') !== false) { ?><input type="radio" class="inp_chk" value="ss" id="view_ss" onclick="document.location.href = 'search.php?view=' + this.value;"<?=($view == 'ss')?' checked':'';?>><label for="view_ss" class="txt_bld txt_look">Supersök</label><? } ?>
<? if($isCrew || strpos($_SESSION['u_a'][1], 'search_sss') !== false) { ?><input type="radio" class="inp_chk" value="sss" id="view_sss" onclick="document.location.href = 'search.php?view=' + this.value;"<?=($view == 'sss')?' checked':'';?>><label for="view_sss" class="txt_bld txt_look">"Kalas"-sök</label><? } ?>
<? if($isCrew || strpos($_SESSION['u_a'][1], 'search_s') !== false) { ?><input type="radio" class="inp_chk" value="s" id="view_s" onclick="document.location.href = 'search.php?s=<?=@secureOUT($str)?>&view=' + this.value;"<?=($view == 's')?' checked':'';?>><label for="view_s" class="txt_bld txt_look">Loggsök</label><? } ?>
<hr /><div class="hr"></div>
<?
	if($view == 's') {
?>
			<form name="search" method="get" action="./search.php">
			<input type="hidden" name="view" value="<?=$view?>">
	<b>Loggsök</b> (IP eller COOKIE)<br><input type="text" name="s" style="width: 300px;" class="inp_nrm" value="<?=($search)?secureOUT($str):'';?>" onfocus="this.select();" />
			</form>
<?
	} elseif($view == 'ss') {
?>
			<form name="search" method="post" action="./search.php?view=<?=$view?>">
	<b>Supersök</b> ( % som wildcard)<br>
	<table cellspacing="0">
	<tr>
		<td style="padding-right: 10px;"><input type="text" name="s" style="width: 300px;" class="inp_nrm" value="<?=($search)?secureOUT($_POST['s']):'';?>" onfocus="this.select();" /><br />Tänk på att tar du fler ord så spelar ordningen roll.<br /><b>frans%test</b> är inte samma som <b>test%frans</b>.
<?
	if($search) {
		echo '<br /><br />Genvägar:';
		foreach($_POST['search_in'] as $table)
		echo '<br /><a href="#'.$table.'">'.$info[$table][0]."</a>\n";
	}
?>

		</td>
		<td align="right">
<select name="search_in[]" id="search_in" multiple="1" size="15" style="margin-bottom: 5px;">
<?
	foreach($info as $search_in => $search_name) {
		echo '<option value="'.$search_in.'"'.((@in_array($search_in, $_POST['search_in']))?' selected':'').'>'.$search_name[0].'</option>';
	}
?>
</select><br /><a href="javascript:void(0);" onclick="selectingAll(document.getElementById('search_in'))">markera alla</a>
<input type="submit" value="sök" class="inp_orgbtn" style="margin: 0 0 0 107px;" />
		</td>
	</tr>
	</table>
			</form>
<?
	} elseif($view == 'sss') {
?>
			<form name="search" method="get" action="./search.php">
	<input type="hidden" name="view" value="<?=$view?>" />
	<b>"Kalas"-sök</b><br>
	<table cellspacing="0">
	<tr>
		<td>Användare 1:<br /><input type="text" name="s1" style="width: 300px;" class="inp_nrm" value="<?=($search)?secureOUT($_GET['s1']):'';?>" onfocus="this.select();" /><br />Användare 2:<br /><input type="text" name="s2" style="width: 300px;" class="inp_nrm" value="<?=($search)?secureOUT($_GET['s2']):'';?>" onfocus="this.select();" /><br /><br /><input type="submit" value="sök" class="inp_orgbtn" style="width: 50px; margin: 0 0 0 250px;" /></td>
	</tr>
	</table>
<?
	if($search) {
		echo 'Genvägar:';
		foreach($info as $table => $descr)
		echo '<br /><a href="#'.$table.'">'.$info[$table][0]."</a>\n";
	}
?>
			</form>
<?
	}
?>
<?
	if($search) {
		if($view == 's') {
			echo '<table cellspacing="2" style="margin-top: 10px;">';
			while($row = mysql_fetch_assoc($s_sql)) {
				echo '<tr class="bg_gray"><td class="pdg nobr">'.implode('</td><td class="pdg">', $row).'</td></tr>';
			}
		} elseif($view == 'ss') {
			#echo '<pre>';
			$highmatch = explode('%', $str);
			if(!count($highmatch) || empty($highmatch)) $highmatch = array($str);
			if(!empty($_POST['search_in']) && count($_POST['search_in']) && strlen($_POST['s']) >= 3) {
			foreach($_POST['search_in'] as $table) {
				$construct = array();
				foreach($info[$table][2] as $column) {
					$construct[] = $column." LIKE '%".$str."%'";
				}
				$construct = implode(' OR ', $construct);
				echo '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><a name="'.$table.'"></a><b>'.$info[$table][0]."</b> (<a href=\"#top\">till toppen</a>)\n";
				echo '<table cellspacing="2" width="900">';
				if($table == 's_userphoto')
					$query = $sql->query("SELECT {$info[$table][3]}, hidden_id, hidden_value, pht_name, picd FROM $table a LEFT JOIN s_user u ON u.id_id = {$info[$table][1]}".(($info[$table][4])?" LEFT JOIN s_user u2 ON u2.id_id = {$info[$table][5]}":'')." WHERE ".$construct." ORDER BY ".$info[$table][6]);
				else
					$query = $sql->query("SELECT {$info[$table][3]} FROM $table a LEFT JOIN s_user u ON u.id_id = {$info[$table][1]}".(($info[$table][4])?" LEFT JOIN s_user u2 ON u2.id_id = {$info[$table][5]}":'')." WHERE ".$construct." ORDER BY ".$info[$table][6]);
				foreach($query as $item) {
					echo '<tr class="'.(($item[2] != '1')?'wht bg_blk':'bg_gray').'">';
					echo '<td class="pdg nobr">'.niceDate($item[0]).'</td>';
					$id = $item[1];
					$s1 = $item[4];
					$stat = $item[2];
					echo '<td class="pdg"><a href="user.php?t&id='.$item[3].'">'.formatText($item[4]).'</a></td>';
					if($info[$table][5]) {
						$s2 = $item[6];
						echo '<td class="pdg"><a href="user.php?t&id='.$item[5].'">'.formatText($item[6]).'</a></td>';
						unset($item[5]);
						unset($item[6]);
					}
					unset($item[0]);
					unset($item[1]);
					unset($item[2]);
					unset($item[3]);
					unset($item[4]);
					if($table == 's_userphoto') {
						$item[6] = '<img src="/_input/usergallery/'.$item[9].'/'.$id.(($item[6])?'_'.$item[7]:'').'.'.$item[8].'" onerror="this.style.display = \'none\'" /><img src="/_input/usergallery_off342/'.$id.(($stat != '1')?'_'.$item[7]:'').'.'.$item[8].'" onerror="this.style.display = \'none\'" /><img src="/_input/usergallery/'.$item[9].'/'.$id.'.'.$item[8].'" onerror="this.style.display = \'none\'" />';
						#unset($item[6]);
						unset($item[7]);
						unset($item[8]);
						unset($item[9]);
					}
					foreach($item as $key => $column) {
						foreach($highmatch as $line) $column = highlight($column, $line);
						if($table == 's_userphoto' && $key == '6') echo '<td class="pdg">'.$column.'</td>'; else echo '<td class="pdg">'.formatText($column).'</td>';
					}
					if($info[$table][4]) echo '<td class="pdg"><a href="search.php?view=sss&s1='.$s1.'&s2='.$s2.'">VISA</a></td>';
					echo '<td class="pdg"><a href="'.$info[$table][7].$id.'" onclick="return confirm(\'Säker ?\');">RADERA</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			} else if(!empty($_POST['s'])) echo 'Ingen sökning gjordes, för få tecken (Min 3).';
			#echo '</pre>';
		} elseif($view == 'sss') {
			if($g1 || $g2) {
			foreach($info as $table => $descr) {
				$construct = array();
				if($g1 && $g2) $construct[] = '('.$descr[3][0]." = '".$i1."' AND ".$descr[3][1]." = '".$i2."')";
				if($g1 && $g2) $construct[] = '('.$descr[3][0]." = '".$i2."' AND ".$descr[3][1]." = '".$i1."')";
				elseif($g1 && !$g2) $construct[] = $descr[3][1]." = '".$i1."'";
				elseif($g2 && !$g1) $construct[] = $descr[3][0]." = '".$i2."'";
				$construct = implode(' OR ', $construct);
				echo '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><a name="'.$table.'"></a><b>'.$info[$table][0]."</b> (<a href=\"#top\">till toppen</a>)\n";
				echo '<table cellspacing="2" width="900">';
				$query = $sql->query("SELECT {$info[$table][4]} FROM $table a LEFT JOIN s_user u ON u.id_id = {$info[$table][1]} LEFT JOIN s_user u2 ON u2.id_id = {$info[$table][2]} WHERE ".$construct." ORDER BY ".$info[$table][5]);
				foreach($query as $item) {
					echo '<tr class="'.(($item[2] != '1')?'wht bg_blk':'bg_gray').'">';
					echo '<td class="pdg nobr">'.niceDate($item[0]).'</td>';
					$s1 = $item[4];
					$id = $item[1];
					echo '<td class="pdg"><a href="user.php?t&id='.$item[3].'">'.formatText($item[4]).'</a></td>';
					if($info[$table][4]) {
						$s2 = $item[6];
						echo '<td class="pdg"><a href="user.php?t&id='.$item[5].'">'.formatText($item[6]).'</a></td>';
						unset($item[5]);
						unset($item[6]);
					}
					unset($item[0]);
					unset($item[1]);
					unset($item[2]);
					unset($item[3]);
					unset($item[4]);
					foreach($item as $column) {
						#foreach($highmatch as $line) $column = highlight($column, $line);
						echo '<td class="pdg">'.formatText($column).'</td>';
					}
					echo '<td class="pdg"><a href="search.php?view=sss&s1='.$s1.'&s2='.$s2.'">VISA</a></td>';
					echo '<td class="pdg"><a href="'.$info[$table][6].$id.'" onclick="return confirm(\'Säker ?\');">RADERA</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			} else if(!$g1 && !$g2) echo 'Ingen sökning gjordes.';
		}
	}
?>
				</td>
			</tr>
			</table>
			</form>
		</td>












		<!-- BANNADE IP -->
		<td width="25%" style="padding-left: 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">
			<form name="ban" method="post" action="./search.php" onsubmit="if(this.ban.value == '1.1.1.1' || this.ban.value == '') { alert('Skriv in ett IP.'); return false; } else { return true; }">
			<table width="100%">
			<tr>
				<td height="35"><b>Blockeringar</b><br><input type="text" name="ban" class="inp_nrm" value="1.1.1.1" onfocus="if(this.value == '1.1.1.1') { this.value = ''; } else { this.select(); }" onblur="if(this.value == '') { this.value = '1.1.1.1'; }" /></td>
			</tr>
			<tr><td height="25">Det finns <span class="txt_chead txt_bld"><?=$b_count?></span> IP blockad<?=(($b_count != '1')?'e':'')?>.</td></tr>
<?
	if(mysql_num_rows($b_sql) > 0) {
		print '			<tr><td style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>';
		while($row = mysql_fetch_assoc($b_sql)) {
?>
			<tr> 
				<td style="padding-bottom: 3px;"><a href="search.php?s=<?=secureOUT($row['ban_ip'])?>"><span class="txt_big"><?=secureOUT($row['ban_ip'])?></span></a> - <em>blockad <?=niceDate($row['ban_date'])?></em> <span class="txt_smin">(tills vidare)</span> - <a href="search.php?ban_del=<?=$row['main_id']?>">TILLÅT</a></td>
			</tr>
<?
		}
	}
?>
			</table>
			</form>
		</td>
	</tr>
	</table>
</body>
</html>