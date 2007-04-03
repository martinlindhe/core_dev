<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	require("./set_tmb.php");
	require("./lib_dir.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	$page = 'VIMMEL/FILM';
	$menu = $menu_VIMMEL;

	$sql_handler = &new sql();
	$vimmel = &new vimmel($sql_handler);
	$year = date("Y");
	$today = date("Y-m-d");

	$wednesday = 3;
	$backminus = 2;
	$last_wednesday = date("w", strtotime("last wednesday"));
	$today_day = date("w");
	if($wednesday != $last_wednesday) {
		$last_wednesday = ($today_day - $today_day) - $today_day - $backminus;
		$last_wednesday = date("Y-m-d", strtotime($last_wednesday . " days"));
	} else {
		$last_wednesday = date("Y-m-d", strtotime("last wednesday"));
	}

	$lnk_str = (isset($_GET['singlefile']))?'':'';
	$df_lnk = (isset($_GET['singlefile']))?'singlefile&':'';

	$list = mysql_query("SELECT * FROM $topic_tab ORDER BY p_date DESC");

	$photopt[0] = array();
	$photopt[1] = '';
	$photo = mysql_query("SELECT * FROM $owner_tab ORDER BY p_name");
	while($row = mysql_fetch_assoc($photo)) {
		$photopt[0][] = $row['main_id'];
		$photopt[1] .= '<option value="'.$row['main_id'].'">'.$row['p_name']."</option>\n";
	}
	if(mysql_num_rows($photo) > 0) mysql_data_seek($photo, 0);

	$change = false;

	if(!empty($_POST['p_id'])) {
		$p_id = str_replace('#', '', $_POST['p_id']);
		$p_id = str_replace(' ', '', $p_id);
		$sql = mysql_query("SELECT main_id FROM $pic_tab WHERE main_id = '".secureINS($p_id)."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$numb = mysql_fetch_row($sql);
		}
	}

	if(!empty($_POST['movie']) && !empty($_POST['p_session'])) {
		if(file_exists($in_dir.$_POST['movie'])) {
			$sql = mysql_query("SELECT main_id, p_date, p_name, p_dday FROM $topic_tab WHERE main_id = '".secureINS($_POST['p_session'])."' LIMIT 1");
			if(mysql_num_rows($sql)) {
				$unique = microtime();
				$file_id = md5($unique . rand(1, 99999));
				$session_id = mysql_result($sql, 0, 'main_id');

				$file_get = MOVIE_PREFIX.'-'.mysql_result($sql, 0, 'p_date').'_ID'.rand(10, 9999);
				while(file_exists(ADMIN_IMAGE_DIR.$session_id.'/'.$file_get.'-'. md5('DETTAFILMKOERA').'.wmv')) {
					$file_get = MOVIE_PREFIX.'-'.mysql_result($sql, 0, 'p_date').'_ID'.rand(10, 9999);
				}

				$errors = 0;
				$img = explode('.', $_POST['movie']);
				unset($img[count($img)-1]);
				$img = implode('.', $img);
				if(!file_exists(ADMIN_IMAGE_DIR.$session_id.'/')) { 
					$oldunmask = umask(0); 
					mkdir(ADMIN_IMAGE_DIR.$session_id, 0777); 
					umask($oldunmask);
				}
				if(@rename($in_dir.'/'.$img.'.wmv', ADMIN_IMAGE_DIR.$session_id.'/'.$file_get.'-'. md5('DETTAFILMKOERA').'.wmv') && @rename($in_dir.'/'.$img.'.jpg', ADMIN_IMAGE_DIR.$session_id.'/'.$file_get.'.jpg') && !doThumb(ADMIN_IMAGE_DIR.$session_id.'/'.$file_get.'.jpg', ADMIN_IMAGE_DIR.$session_id.'/'.$file_get.'.jpg', $upl_movie[0], $upl_movie[1], 90)) {
					#sessWMVADD($session_id, $file_id, $file_get, mysql_result($sql, 0, 'p_date'), 0);
					mysql_query("INSERT INTO $film_tab SET
					m_id = '$file_id',
					m_file = '$file_get',
					m_content = '".filesize(ADMIN_IMAGE_DIR.$session_id.'/'.$file_get.'-'. md5('DETTAFILMKOERA').'.wmv')."',
					m_name = '".mysql_result($sql, 0, 'p_name')."',
					m_dday = '".mysql_result($sql, 0, 'p_dday')."',
					m_date = '".mysql_result($sql, 0, 'p_date')."',
					topic_id = '$session_id',
					status_id = '0',
					date_cnt = NOW()");
				}
			} else {
				$msg = 'Felaktig session.';
				$js_mv = 'pics.php';
				require("./_tpl/notice_admin.php");
				exit;
			}
		} else {
			$msg = 'Felaktig film: '.$in_dir.$_POST['movie'];
			$js_mv = 'pics.php';
			require("./_tpl/notice_admin.php");
			exit;
		}
		header("Location: pics.php?id=".$_POST['p_session']);
		exit;
	}

	if(!empty($_GET['del_movie']) && is_md5($_GET['del_movie'])) {
		$sql = mysql_query("SELECT * FROM $film_tab WHERE m_id = '".secureINS($_GET['del_movie'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$row = mysql_fetch_assoc($sql);
			@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['m_file'].'-'. md5('DETTAFILMKOERA').'.wmv');
			@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['m_file'].'.jpg');
			mysql_query("DELETE FROM $film_tab WHERE m_id = '".secureINS($row['m_id'])."' LIMIT 1");
		}
	}

	if(!empty($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM $topic_tab WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$change = true;
			$row = mysql_fetch_assoc($sql);
			$lnk_str .= 'id='.$row['main_id'];
			$front = mysql_query("SELECT * FROM $pic_tab WHERE topic_id = '".secureINS($_GET['id'])."' ORDER BY main_id");
			$front2 = mysql_query("SELECT * FROM $pic_tab WHERE topic_id = '".secureINS($_GET['id'])."' ORDER BY status_id, order_id, main_id ASC");
			$film = mysql_query("SELECT * FROM $film_tab WHERE topic_id = '".secureINS($_GET['id'])."' ORDER BY main_id DESC");
		}
	}

	if(!empty($_GET['del']) && is_numeric($_GET['del']) && isset($_GET['status']) && is_numeric($_GET['status']) && isset($_GET['picstatus']) && is_numeric($_GET['picstatus'])) {
		$vimmel->vimmelDelete('cmt', $_GET['del'], $_GET['status'], $_GET['picstatus']);
		header("Location: pics.php");
		exit;
	}

	if(!empty($_GET['fix'])) {
		$vimmel->vimmelRefresh(1, 1);
		if(!empty($_GET['fix_just'])) { $vimmel->vimmelFix(); }
		header("Location: pics.php");
		exit;
	}

	if(!empty($_GET['del_pic']) && is_numeric($_GET['del_pic'])) {
		$vimmel->vimmelDelete('pic', $_GET['del_pic']);
		header("Location: pics.php?id=".$_GET['id']);
		exit;
	}

	if(!empty($_GET['del_session'])) {
		$sql = mysql_query("SELECT main_id FROM $topic_tab WHERE main_id = '".secureINS($_GET['del_session'])."'");
		if(mysql_num_rows($sql) > 0) {
			$session = mysql_result($sql, 0, 'main_id');
			$sql = mysql_query("SELECT * FROM $pic_tab WHERE topic_id = '".secureINS($session)."'");
			while($row = mysql_fetch_assoc($sql)) {
				if($row['status_id'] == '2') {
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-'.$row['statusID'].'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-full'.GALLERY_CODE.'-'.$row['statusID'].'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-thumb-'.$row['statusID'].'.'.$row['p_pic']);
				} else {
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-full'.GALLERY_CODE.'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-thumb.'.$row['p_pic']);
				}
			}
			@delete_tree(ADMIN_IMAGE_DIR.$session);
			mysql_query("DELETE FROM $cmt_tab WHERE topic_id = '".secureINS($session)."'");
			mysql_query("DELETE FROM $pic_tab WHERE topic_id = '".secureINS($session)."'");
			mysql_query("DELETE FROM $film_tab WHERE topic_id = '".secureINS($session)."'");
			mysql_query("DELETE FROM $topic_tab WHERE main_id = '".secureINS($session)."'");
		}
		header("Location: pics.php");
		exit;
	}

	if(!empty($_GET['del_pics'])) {
		$sql = mysql_query("SELECT main_id FROM $topic_tab WHERE main_id = '".secureINS($_GET['del_pics'])."'");
		if(mysql_num_rows($sql) > 0) {
			$session = mysql_result($sql, 0, 'main_id');
			$sql = mysql_query("SELECT * FROM $pic_tab WHERE topic_id = '".secureINS($session)."'");
			while($row = mysql_fetch_assoc($sql)) {
				if($row['status_id'] == '2') {
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-'.$row['statusID'].'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-full'.GALLERY_CODE.'-'.$row['statusID'].'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-thumb-'.$row['statusID'].'.'.$row['p_pic']);
				} else {
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-full'.GALLERY_CODE.'.'.$row['p_pic']);
					@unlink(ADMIN_IMAGE_DIR.$row['topic_id'].'/'.$row['id'].'-thumb.'.$row['p_pic']);
				}
			}
			@delete_tree(ADMIN_IMAGE_DIR.$session);
			mysql_query("DELETE FROM $cmt_tab WHERE topic_id = '".secureINS($session)."'");
			mysql_query("DELETE FROM $pic_tab WHERE topic_id = '".secureINS($session)."'");
			mysql_query("UPDATE $topic_tab SET p_cmts = '0', p_pics = '0', p_views = '0' WHERE main_id = '".secureINS($session)."'");
		}
		header("Location: pics.php");
		exit;
	}

	if(!empty($_POST['dovalid'])) {
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
							$vimmel->vimmelUpdate('cmt', array('main_id' => $kid, 'pic_id' => $_POST['pic_id:' . $kid], 'topic_id' => $_POST['topic_id:' . $kid]), $_POST['status_id:' . $kid], $_POST['otatus_id:' . $kid], $_POST['picstatus:' . $kid]);
						} else {
// alla-knapp
							$vimmel->vimmelUpdate('cmt', array('main_id' => $kid, 'pic_id' => $_POST['pic_id:' . $kid], 'topic_id' => $_POST['topic_id:' . $kid]), $_POST['main_id:all'], $_POST['otatus_id:' . $kid], $_POST['picstatus:' . $kid]);
						}
					} else {
						$vimmel->vimmelUpdate('cmt', array('main_id' => $kid, 'pic_id' => $_POST['pic_id:' . $kid], 'topic_id' => $_POST['topic_id:' . $kid]), $_POST['status_id:' . $kid], $_POST['otatus_id:' . $kid], $_POST['picstatus:' . $kid]);
					}
				}
			}
		}
		header('Location: pics.php?view_cmt='.$view_cmt);
		exit;
	}

	if(!empty($_POST['dopicvalid'])) {
		$doall = false;
		if(!empty($_POST['main_id:all']) && is_numeric($_POST['main_id:all'])) {
			$doall = true;
		}
		$owners = array();
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$doblock = false;
				$kid = explode(":", $key);
				$kid = $kid[1];
				$sql = mysql_query("SELECT topic_id, id, p_pic, status_id, statusID, p_view, p_cmt FROM $pic_tab WHERE main_id = '".secureINS($kid)."' LIMIT 1");

				if(mysql_num_rows($sql) > 0) {
					$row = mysql_fetch_assoc($sql);
					if(isset($_POST['status_id:' . $kid]) && isset($_POST['order_id:' . $kid])) {
						if(!empty($_POST['status_id:'.$kid])) {
							$vimmel->vimmelUpdate('pic', $row, @$_POST['status_id:' . $kid], @$_POST['o_id:' . $kid]);
							$blockID = $vimmel->vimmelUpdate('file', $row, @$_POST['status_id:' . $kid], @$_POST['o_id:' . $kid]);
							mysql_query("UPDATE $pic_tab SET status_id = '".secureINS($_POST['status_id:' . $kid])."', ".(($blockID)?"statusID = '".secureINS($blockID)."', ":'')."order_id = '".secureINS($_POST['order_id:' . $kid])."', owner_id = '".secureINS($_POST['owner_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
							$isok = ($_POST['status_id:' . $kid] == '1')?true:false;
						} elseif($doall) {
// godkänn blanka
							$vimmel->vimmelUpdate('pic', $row, 1, @$_POST['o_id:' . $kid]);
							$blockID = $vimmel->vimmelUpdate('file', $row, 1, @$_POST['o_id:' . $kid]);
							mysql_query("UPDATE $pic_tab SET status_id = '1', order_id = '".secureINS($_POST['order_id:' . $kid])."', owner_id = '".secureINS($_POST['owner_id:' . $kid])."' WHERE main_id = '".secureINS($kid)."' LIMIT 1");
							$isok = true;
						}
						if($isok && !in_array($_POST['owner_id:' . $kid], $owners)) $owners[] = $_POST['owner_id:' . $kid];
					}
				}
			}
		}
		$owner_str = array();
		foreach($owners as $val) {
			$owner_str[] = $sql_handler->queryResult("SELECT p_name FROM {$tab['owner']} WHERE main_id = '".$val."' LIMIT 1");
		}
		sort($owner_str);
		$owner_str = implode(', ', $owner_str);
		$sql_handler->queryInsert("UPDATE {$tab['topic']} SET owner_str = '".$owner_str."' WHERE main_id = '".$_POST['id']."' LIMIT 1");
		header("Location: pics.php?id={$_POST['id']}");
		exit;
	}

	if(!empty($_POST['dopost'])) {
		$error = '';
		if(empty($error)) {
			$pst_stat = (!empty($_POST['p_status']))?$_POST['p_status']:'0';
			$pst_dday = (!empty($_POST['p_dday']))?'1':'0';
			$p_excl = (!empty($_POST['VIP']))?'1':'0';
			if(empty($_POST['edit'])) {
				$sql = mysql_query("INSERT INTO $topic_tab SET
				main_id = '".secureINS(md5(microtime().'HAHAHAHSALTISÅRET'))."',
				status_id = '$pst_stat',
				p_dday = '$pst_dday',
				p_exclusive = '$p_excl',
				owner_id = '".secureINS($_POST['owner_id'])."',
				p_city = '".secureINS($_POST['p_city'])."',
				owner_str = '".secureINS($_POST['owner_str'])."',
				p_name = '".secureINS($_POST['p_name'])."',
				p_date = '".secureINS($_POST['p_date'])."'");
				if(!$sql) {
					$error.='idtaken\n';
				}
				$id = mysql_insert_id();
			} else {
				if($_POST['owner_id'] != $_POST['old_owner_id']) {
					mysql_query("UPDATE $pic_tab SET owner_id = '".secureINS($_POST['owner_id'])."' WHERE owner_id = '".secureINS($_POST['old_owner_id'])."' AND topic_id = '".secureINS($_POST['edit'])."'");
				}
				$sql = mysql_query("UPDATE $topic_tab SET
				status_id = '$pst_stat',
				p_name = '".secureINS($_POST['p_name'])."',
				p_exclusive = '$p_excl',
				p_dday = '$pst_dday',
				p_city = '".secureINS($_POST['p_city'])."',
				owner_id = '".secureINS($_POST['owner_id'])."',
				owner_str = '".secureINS($_POST['owner_str'])."',
				p_date = '".secureINS($_POST['p_date'])."' WHERE main_id = '".secureINS($_POST['edit'])."' LIMIT 1");
				if(!$sql) {
					$error.='Error\n'.mysql_error();
				}
				$id = $_POST['edit'];
				if(!empty($_POST['o_status']) && $pst_stat == '1' && $_POST['o_status'] != '1') $vimmel->vimmelRefresh(1, 1);
			}
			$sql = &new sql();
			$user = &new user($sql);
			foreach($_POST as $key => $val) {
				if(strpos($key, 'm_id') !== false) {
					$kid = explode(":", $key);
					$kid = $kid[1];
					if(isset($_POST['m_id:'.$kid])) {
						mysql_query("UPDATE $film_tab SET m_dday = '".secureINS($_POST['m_dday:'.$kid])."', m_date = '".secureINS($_POST['m_date:'.$kid])."', m_name = '".secureINS($_POST['m_name:'.$kid])."', m_owner = '".secureINS($_POST['m_owner:'.$kid])."', m_edit = '".secureINS($_POST['m_edit:'.$kid])."', m_length = '".secureINS($_POST['m_length:'.$kid])."', m_size = '".secureINS($_POST['m_size:'.$kid])."', status_id = '".secureINS($_POST['m_status:'.$kid])."' WHERE m_id = '".secureINS($kid)."' LIMIT 1");
if(!empty($_POST['SPY_id:'.$kid])) {
	if($pst_stat == '1')
		$res = mysql_query("SELECT id_id FROM {$tab['user']} WHERE status_id = '1' AND city_id = '".$_POST['p_city']."'");
	elseif($pst_stat == '0')
		$res = mysql_query("SELECT id_id FROM {$tab['user']} WHERE status_id = '1' AND city_id = '".$_POST['p_city']."' AND level_id >= 6");
	while($row = mysql_fetch_row($res)) {
		$user->spy($row[0], $kid, 'MOV', array($_POST['p_city'], $_POST['m_name:'.$kid]));
	}
	mysql_query("INSERT INTO {$tab['notice']} SET
	ad_cmt = 'Nu ligger film uppe från <b>{$_POST['m_name:'.$kid]}</b>!',
	ad_date = NOW(),
	status_id = '1'");
}
					}
				}
			}
if(!empty($_POST['SPY'])) {
	if($pst_stat == '1')
		$res = mysql_query("SELECT id_id FROM {$tab['user']} WHERE status_id = '1' AND city_id = '".$_POST['p_city']."'");
	elseif($pst_stat == '0')
		$res = mysql_query("SELECT id_id FROM {$tab['user']} WHERE status_id = '1' AND city_id = '".$_POST['p_city']."' AND level_id >= 6");
	while($row = mysql_fetch_row($res)) {
		$user->spy($row[0], $id, 'GAL', array($_POST['p_city'], $_POST['p_name']));
	}
	$res = mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$tab['pic']} WHERE status_id = '1' AND topic_id = '".$id."'"), 'count');
	mysql_query("INSERT INTO {$tab['notice']} SET
	ad_cmt = 'Nu ligger $res vimmelbilder från <b>{$_POST['p_name']}</b> online!',
	ad_date = NOW(),
	status_id = '1'");
}
			header("Location: pics.php");
			exit;
		}
	}
	if(!empty($_POST['doupload'])) {
		$doit = false;
		if(!empty($_POST['p_session'])) {
			$sql = mysql_query("SELECT main_id, owner_id, p_exclusive FROM $topic_tab WHERE main_id = '".secureINS($_POST['p_session'])."' LIMIT 1");
			if(mysql_num_rows($sql) > 0) {
				$doit = true;
				$row = mysql_fetch_assoc($sql);
				$if_go = mysql_result(mysql_query("SELECT COUNT(*) as count FROM $pic_tab WHERE topic_id = '".secureINS($row['main_id'])."'"), 0, 'count');
				$p_own = $row['owner_id'];
			}
		}
		if(!$doit) {
			$msg = 'Felaktig session.';
			$js_mv = 'pics.php';
			require("./_tpl/notice_admin.php");
			exit;
		}
		if(!file_exists(ADMIN_IMAGE_DIR.$row['main_id'].'/')) { 
			$oldunmask = umask(0); 
			mkdir(ADMIN_IMAGE_DIR.$row['main_id'], 0777); 
			umask($oldunmask);
		}
		$gotpic = false;
		foreach($HTTP_POST_FILES as $key => $val) {
			if(strpos($key, 'file') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(!$HTTP_POST_FILES['file:'. $kid]['error']) {
					$p = $HTTP_POST_FILES['file:'. $kid]['tmp_name'];
					$p_name = $HTTP_POST_FILES['file:'. $kid]['name'];
					$p_size = $HTTP_POST_FILES['file:'. $kid]['size'];
					if(verify_uploaded_file($p_name, $p_size)) {

						$p_owner = (!empty($_POST['owner_id:X'.$kid]))?$_POST['owner_id:X'.$kid]:'';
						$unique = md5(microtime().'hejhejehej');
						$p_name = explode('.', $p_name);
						$p_name = $p_name[count($p_name)-1];
						$error = 0;
						# doSResize
		      				$error += @doThumb($p, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$unique.'.'.$p_name, $upl_full[0], $upl_full[1]);
						$error += @doThumb(ADMIN_IMAGE_DIR.$row['main_id'].'/'.$unique.'.'.$p_name, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$unique.'-thumb.'.$p_name, $upl_thumb[0], $upl_thumb[1], 90);
						$error += @doWM(ADMIN_IMAGE_DIR.$row['main_id'].'/'.$unique.'.'.$p_name, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$unique.'.'.$p_name, 93, $row['p_exclusive']);
						$error += @make_full($p, ADMIN_IMAGE_DIR.$row['main_id'].'/'.$unique.'-full.'.$p_name);
						unlink($p);
						if(!$error) {
							#$i++;
							$gotpic = true;
							mysql_query("INSERT INTO $pic_tab SET status_id = '".(($if_go)?'0':'1')."', topic_id = '{$row['main_id']}', id = '$unique', p_pic = '$p_name', p_date = NOW(), owner_id = '".secureINS(((!empty($p_owner))?$p_owner:$p_own))."'");
							$vimmel->vimmelAdd('pic', $row['main_id'], (($if_go)?'0':'1'));
						} else {
							$msg = 'Felaktigt format, storlek eller bredd & höjd. Kolla även rättigheterna för mappen.';
							$js_mv = 'pics.php?id=' . $row['main_id'];
							require("./_tpl/notice_admin.php");
							exit;
						}
					} else {
						$msg = 'Felaktig bild.';
						$js_mv = 'pics.php?id=' . $row['main_id'];
						require("./_tpl/notice_admin.php");
						exit;
					}
				}
			}
		}
		header("Location: pics.php?id={$row['main_id']}");
		exit;
	}

	if(!empty($_GET['q']) && is_numeric($_GET['q'])) {
		$q = intval($_GET['q']);
	} else {
		$q = 6;
	}

	require("./_tpl/admin_head.php");
?>
<script type="text/javascript" src="fnc_adm.js"></script>
<script type="text/JavaScript">
<?
	if(!empty($error)) {
		$error = str_replace("idtaken", "Upptaget ID", $error);
		echo '
alert("'.$error.'");
document.location.href = \'pics.php\';
</script>';
	}
?>
var allowedext = Array("jpg", "jpeg", "gif", "png");
function showError(obj) { obj.src = './_img/status_none.gif'; }
function showBig(obj) { obj.style.height = '92px'; obj.style.width = '137px'; }
function showSml(obj) { obj.style.height = '22px'; obj.style.width = '24px'; }
function showPre(val, id) {
	var picpre = document.getElementById(id);
	if(val != '') {
		var showimg = false;
		ext = val.split(".");
		ext = ext[ext.length - 1].toLowerCase();
		for(var i = 0; i <= allowedext.length; i++)
			if(allowedext[i] == ext) 
				showimg = true;
	
		if(showimg) {
			previewpic = val;
			picpre.src = 'file://' + val.replace(/\\/g,'/');
		} else
			showError(picpre);
	} else
			showError(picpre);
}
function genA() {
	window.open('pics_generate.php', 'generate', 'toolbars=0, scrollbars=0, location=0, statusbars=0, menubars=0, resizable=1, width=330, height=250, left = 100, top = 100');
}
function showPost(val, id) {
	var picpre = document.getElementById(id);
	if(val != '') {
		var showimg = false;
		ext = val.split(".");
		ext = ext[ext.length - 1].toLowerCase();

		for(var i = 0; i <= allowedext.length; i++)
			if(allowedext[i] == ext) 
				showimg = true;
		if(showimg) {
			previewpic = val;
			picpre.src = '<?=$in_dir?>' + val;
		} else
			showError(picpre);
	} else
			showError(picpre);
}

function toggle(type) {
	for (i = 0; i < document.addpic.length; i++) {
		var toggle = document.addpic.elements[i];
		if(toggle.type == 'checkbox') {
			if(type == 1) {
				if(!toggle.checked)	toggle.checked = true;
			} else {
				if(toggle.checked)	toggle.checked = false;
			}
		}
	}
}

function showAll() {
	for (i = 0; i < document.addpic.length; i++) {
		var toggle = document.addpic.elements[i];
		if(toggle.type == 'checkbox') {
			showPost(document.getElementById(toggle.value).value, toggle.id.substring(1));
		}
	}
}

function doOwner() {
	window.open('pics_owner.php','owner', 'toolbars=0, scrollbars=yes, location=0, status=yes, menubars=0, resizable=0, width=430, height=350, left = 100, top = 100');
}

function changeByKey(e) {
	if(!e) var e=window.event;
	if(e.ctrlKey && e['keyCode'] == 13) alert('abo');
}
function compareText() {
	v = document.getElementById('id');
	c = document.getElementById('pro');
	h = document.getElementById('head');
	if(c.innerHTML != v.value) {
		h.className = 'txt_look';
		v.title = 'Ej sparad.';
	} else {
		h.className = 'no_bld';
		v.title = '';
	}
}
function loadtop() {
	if(parent.head)
	parent.head.show_active('pics');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
document.onkeydown = changeByKey;

function changeOwner(id, val) {
	obj = document.getElementById('owner_id:' + id);
	obj.selectedIndex = val;
}
</script>
<div style="position: absolute; top: 4px; left: 150px;"></div>
<form action="pics.php" method="post">
<div style="position: absolute; top: 4x; right: 20px;">Sök bildnummer: <input type="text" name="p_id" class="inp_adm" style="width: 50px;" value="<?=((!empty($numb) && $numb[0] > 0)?secureOUT($numb[0]):'');?>" /><?=(!empty($numb) && $numb[0] > 0)?' - Länk: <a href="javascript:vimmel(\''.$numb[0].'\', 692, 625);">#'.$numb[0].'</a>':'';?> - <input type="button" class="inp_orgbtn" value="Uppdatera data" style="margin: 0;" onclick="this.disabled = true; this.value = 'Laddar...'; document.location.href = (confirm('Vill du även justera bildinformation? Det kommer att ta ett tag.\n\nOK för JA, Avbryt för NEJ.'))?'pics.php?fix=1&fix_just=1':'pics.php?fix=1';"></div>
</form>
	<table width="100%" height="100%">
	<tr><td height="25" colspan="3"><nobr><?makeMenu($page, $menu, 0);?></nobr></td></tr>
	<tr>
		<td width="50%" style="padding: 0 10px 20px 0;">

<!-- ADD/EDIT -->
		<form name="session_edit" action="pics.php" method="post">
		<input type="hidden" name="dopost" value="1">
		<input type="hidden" name="old_owner_id" value="<?=($change && isset($row['owner_id']))?$row['owner_id']:'';?>">
		<input type="hidden" name="edit" value="<?=($change)?$row['main_id']:'0';?>">
		<table>
		<tr>
			<td colspan="2" height="28" colspan="2"><b><?=($change)?'Ändra vimmel</b> [<a href="pics.php">Nytt vimmel</a>]':'Nytt vimmel</b>';?></td>
		</tr>
		<tr>
			<td colspan="2" style="width: 190px;">Namn</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 2px 0 0 0;"><input type="text" name="p_name" class="inp_nrm" value="<?=($change)?secureOUT($row['p_name']):'';?>">
		</tr>
		<tr>
			<td colspan="2" style="width: 190px;">Stad</td>
		</tr>
		<tr>
			<td style="padding: 2px 10px 0 0;"><select name="p_city" size="1" class="inp_nrm" style="width: 180px;">
<?
	foreach($cities as $key => $val) {
		$select = ($change && $row['p_city'] == $key)?' selected':'';
		echo '<option value="'.$key.'"'.$select.'>'.$val.'</option>';
	}
?>
			</select></td>
		</tr>
		<tr>
			<td colspan="2" style="width: 190px;">Datum</td>
		</tr>
		<tr>
			<td style="padding: 2px 10px 0 0;"><select name="p_date" size="1" class="inp_nrm" style="width: 180px;">
<?
	$gotit = false;
	if(!$gotit && $change && $row['p_date'] == $last_wednesday) {
		$selected = ' selected';
		$gotit = true;
	} else $selected = '';
	#echo '<option value="'.$last_wednesday.'"'.$selected.'>'.strtoupper(strftime("%A ", strtotime($last_wednesday))).specialDate($last_wednesday).'</option>';
	for($i = 0; $i <= gettxt('admin_vimmeldagar'); $i++) {
		#$days = $i * 7;
		$days = $i;
		$day = date("Y-m-d", strtotime("-$days DAYS"));
		if(!$gotit && $change && $row['p_date'] == $day) {
			$selected = ' selected';
			$gotit = true;
		} else $selected = '';
		echo '<option value="'.$day.'"'.$selected.'>'.strtoupper(strftime("%A ", strtotime($day))).specialDate($day).'</option>';
	}
	if(!$gotit && $change) echo '<option value="'.$row['p_date'].'" selected>'.specialDate($row['p_date']).'</option>';
?>
			</select></td>
			<td style="width: 190px; padding: 2px 0 0 0;"><input type="checkbox" class="inp_chk" name="p_dday" value="1" id="p_dday"<?=($change)?(($row['p_dday'])?' checked':''):'';?>><label for="p_dday">OCH DAGEN EFTER</label></td>
		</tr>
		<tr><td colspan="2" style="padding: 5px 0 10px 0;"><input type="checkbox" class="inp_chk" <?=($row['p_exclusive'])?'checked ':'';?>name="VIP" value="1" id="inp_VIP"><label for="inp_VIP"> Använd VIP-vattenstämpel</label></td></tr>
		<tr>
			<td colspan="2">Fotograf</td>
		</tr>

		<tr>
			<td style="padding-top: 2px;"><select name="owner_id" size="1" id="owner_id" class="inp_adm" onchange="if(document.getElementById('owner_str').value.length == 0) document.getElementById('owner_str').value = (document.getElementById('f:' + this.value).innerHTML == 'Välj')?'':document.getElementById('f:' + this.value).innerHTML;">
<option value="0" id="f:0">Välj</option>
<?
	while($prow = mysql_fetch_assoc($photo)) {
		if($change && $row['owner_id'] == $prow['main_id']) {
			echo '<option value="'.$prow['main_id'].'" id="f:'.$prow['main_id'].'" selected>'.$prow['p_name']."</option>\n";
		} else {
			echo '<option value="'.$prow['main_id'].'" id="f:'.$prow['main_id'].'">'.$prow['p_name']."</option>\n";
		}
	}
?>
			</select></td>
			<td style="padding-top: 4px;"><a href="javascript:doOwner();">Ändra fotografer</a></td>
		</tr>
		<tr>
			<td colspan="2">Fotograftext</td>
		</tr>
		<tr>
			<td style="padding-top: 2px;" colspan="2"><input name="owner_str" size="1" id="owner_str" class="inp_adm" style="width: 300px;" value="<?=($change)?((!empty($row['owner_str']))?secureOUT($row['owner_str']):''):'';?>"></td>
		</tr>
		<tr>
			<td colspan="2">
			<table width="100%">
			<tr>
				<td><input type="hidden" name="o_status" value="<?=($change)?$row['status_id']:'';?>"><input type="radio" class="inp_chk" name="p_status" value="1" id="p_1"<?=($change)?(($row['status_id'])?' checked':''):'';?>><label for="p_1">Visa för alla</label><br><input type="radio" class="inp_chk" name="p_status" value="0" id="p_0"<?=($change)?((!$row['status_id'])?' checked':''):'';?>><label for="p_0">Visa för VIP</label><br><input type="radio" class="inp_chk" name="p_status" value="2" id="p_2"<?=($change)?(($row['status_id'] == '2')?' checked':''):' checked';?>><label for="p_2">Stängd</label></td>
				<td align="right"><?=($change)?'<input type="button" class="inp_orgbtn" value="Radera" style="width: 80px; margin: 5px 7px 0 0;" onclick="if (confirm(\'Säker ?\')) { document.location.href = \'pics.php?del_session='.$row['main_id'].'\'; }"><input type="button" class="inp_orgbtn" value="Töm bilder" style="width: 80px; margin: 5px 7px 0 0;" onclick="if (confirm(\'Säker ?\')) { document.location.href = \'pics.php?del_pics='.$row['main_id'].'\'; }">':'';?><input type="submit" class="inp_orgbtn" value="<?=($change)?'Uppdatera':'Skapa';?>" style="width: 80px; margin: 5px 0 0 0;" /><br /><input type="checkbox" class="inp_chk" name="SPY" value="1" id="spy_tell"><label for="spy_tell"> [BEVAKNING] Meddela om nytt vimmel</label></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td colspan="2" style="padding: 5px 0 5px 0;"><hr /><div class="hr"></div></td></tr>
		</table>
<?
	if($change && mysql_num_rows($film)) {
?>
		<table width="100%">
		<tr>
			<td height="20" colspan="2"><b>Befintliga filmer</b></td>
		</tr>
		</table>
<?
		while($f = mysql_fetch_assoc($film)) {
		$img = ADMIN_IMAGE_DIR.$f['topic_id'].'/'.$f['m_file'].'.jpg';
?>
		<table width="100%">
		<tr>
			<td<?=($f['status_id'] == '2')?' class="bg_blk"':'';?>>
			<table width="100%">
			<tr>
				<td rowspan="1"><a href="../gallery_movie_fetch.php?id=<?=$f['m_id']?>"><img src="<?=$img?>" style="margin-right: 5px;"></a></td>
				<td style="padding-bottom: 8px; width: 100%; height: 10px;"<?=($f['status_id'] == '2')?' class="txt_wht"':'';?>><img src="./_img/status_<?=($f['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:M<?=$f['m_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($f['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:M<?=$f['m_id']?>" onclick="changeStatus('status', this.id);">
<br>Namn:<br><input type="text" class="txt" style="width: 140px;" name="m_name:<?=$f['m_id']?>" value="<?=secureOUT($f['m_name'])?>">
<br>Datum:<br><select name="m_date:<?=$f['m_id']?>" size="1" class="inp_nrm" style="width: 180px;">
<?
	$gotit = false;
	if(!$gotit && $change && $f['m_date'] == $last_wednesday) {
		$selected = ' selected';
		$gotit = true;
	} else $selected = '';
	#echo '<option value="'.$last_wednesday.'"'.$selected.'>'.strtoupper(strftime("%A ", strtotime($last_wednesday))).specialDate($last_wednesday).'</option>';
	for($i = 0; $i <= gettxt('admin_vimmeldagar'); $i++) {
		#$days = $i * 7;
		$days = $i;
		$day = date("Y-m-d", strtotime("-$days DAYS"));
		if(!$gotit && $change && $f['m_date'] == $day) {
			$selected = ' selected';
			$gotit = true;
		} else $selected = '';
		echo '<option value="'.$day.'"'.$selected.'>'.strtoupper(strftime("%A ", strtotime($day))).specialDate($day).'</option>';
	}
	if(!$gotit && $change) echo '<option value="'.$f['m_date'].'" selected>'.specialDate($f['m_date']).'</option>';
?>
			</select>
<br><input type="checkbox" class="inp_chk" name="m_dday:<?=$f['m_id']?>" value="1" id="m_dday<?=$f['m_id']?>"<?=($change)?(($f['m_dday'])?' checked':''):'';?>><label for="m_dday<?=$f['m_id']?>">OCH DAGEN EFTER</label>
<br>Storlek:<br><input type="text" class="txt" style="width: 140px;" name="m_size:<?=$f['m_id']?>" value="<?=secureOUT($f['m_size'])?>">
<br>Längd:<br><input type="text" class="txt" style="width: 140px;" name="m_length:<?=$f['m_id']?>" value="<?=secureOUT($f['m_length'])?>">
<br>Fotograf:<br><input type="text" class="txt" style="width: 140px;" name="m_owner:<?=$f['m_id']?>" value="<?=secureOUT($f['m_owner'])?>">
<br>Redigering:<br><input type="text" class="txt" style="width: 140px;" name="m_edit:<?=$f['m_id']?>" value="<?=secureOUT($f['m_edit'])?>">
<br><input type="checkbox" value="1" name="SPY_id:<?=$f['m_id']?>" id="spy<?=$f['m_id']?>"><label for="spy<?=$f['m_id']?>">[BEVAKNING] Meddela om ny film</label></td>
			</tr>
			<tr>
				<td align="left" style="height: 12px; vertical-align: bottom; padding: 0 0 0 0;"<?=($f['status_id'] == '2')?' class="bg_blk"':'';?>>
<input type="hidden" name="m_id:<?=$f['m_id']?>" value="<?=$f['status_id']?>">
<input type="hidden" name="m_status:<?=$f['m_id']?>" id="status_id:M<?=$f['m_id']?>" value="<?=$f['status_id']?>">
<a href="pics.php?del_movie=<?=$f['m_id']?>" onclick="return confirm('Säker ?')?true:false;">RADERA</a>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td align="right"><input type="submit" class="inp_orgbtn" value="Uppdatera" style="width: 80px; margin: 5px 0 0 0;" /></td>
		</table>
		<hr /><div class="hr"></div>
<?
		}
	}
?>
		</form>
		<script type="text/javascript">
		function toggle(obj, txt) {
			obj.parentNode.childNodes[0].innerHTML = txt;
		}
		</script>
		<table width="100%">
<?
	if(mysql_num_rows($list) > 0) {
		while($lrow = mysql_fetch_assoc($list)) {
			$got_film = 0;
			$film = mysql_query("SELECT status_id FROM $film_tab WHERE topic_id = '".secureINS($lrow['main_id'])."' LIMIT 1");
			if(!mysql_num_rows($film)) $got_film = 0; else { $got_film = 1; $film = mysql_result($film, 0, 'status_id'); }
			#$count = mysql_result(mysql_query("SELECT COUNT(*) as count FROM $pic_tab WHERE topic_id = '".secureINS($lrow['main_id'])."'"), 0, 'count');
?>
		<tr>
			<td><?=($lrow['status_id'] != '1')?'<nobr><a class="no_bld" ':'<a ';?>href="pics.php?<?=$df_lnk?>id=<?=$lrow['main_id']?>"><?=strtoupper(@$cities[$lrow['p_city']].' ').stripslashes($lrow['p_name'])?> - <?=specialDate($lrow['p_date'], $lrow['p_dday'])?></a><?=($lrow['status_id'] != '1')?(($lrow['status_id'] == '2')?' [S]':' [P]'):'';?><?=($got_film)?' [FILM'.(($film == '1')?' AKTIV':' <b>INAKTIV</b>').']':'';?></td>
			<td align="right"><nobr><label>[<b><span></span><span onmouseover="toggle(this, 'Bilder: ');" onmouseout="toggle(this, '');"><?=$lrow['p_pics']?></span></b> <b><span></span><span onmouseover="toggle(this, '| Visningar: ');" onmouseout="toggle(this, '');"><?=$lrow['p_views']?></span></b> <b><span></span><span onmouseover="toggle(this, '| Kommentarer: ');" onmouseout="toggle(this, '');"><?=$lrow['p_cmts']?></span></b>]</label><?=($lrow['status_id'] != '2')?' - <a target="_blank" href="../gallery_multi.php?id='.$lrow['main_id'].'">RESULTAT</a>':'';?></nobr></td></tr>
<?
		}
	} else print '<tr><td>Det finns inga bildsessioner.</td></tr>';
?>
		</table>


		</td>
		<td width="50%" style="padding: 0 10px 0 10px; background: url('_img/brd_h.gif'); background-repeat: repeat-y;">



<?
	if(isset($_GET['movie'])) {
		require("./_tpl/movie_admin.php");
	} elseif(!isset($_GET['singlefile'])) {
		require("./_tpl/postload_admin.php");
	} else {
?>



		<form name="session_upload" method="post" action="pics.php" enctype="multipart/form-data" onsubmit="if(this.p_session.options[this.p_session.selectedIndex].value != '0') return true; else { alert('Välj en bildsession!'); return false; }">
		<input type="hidden" name="doupload" value="1">
		Visar:<br>
			<input type="radio" class="inp_chk" name="upl" value="0" id="upl_0" onclick="document.location.href = 'pics.php?<?=$lnk_str?>';"><label for="upl_0" class="txt_bld txt_look">Postload</label> [<a href="javascript:popup('help.php?id=postload', 'help', 316, 355);">Hjälp</a>]
			<input type="radio" class="inp_chk" name="upl" value="1" id="upl_1" onclick="document.location.href = 'pics.php?singlefile&<?=$lnk_str?>';" checked><label for="upl_1" class="txt_bld txt_look">Uppladdning</label>
			<input type="radio" class="inp_chk" name="upl" value="2" id="upl_2" onclick="document.location.href = 'pics.php?movie&<?=$lnk_str?>';"><label for="upl_2" class="txt_bld txt_look">Film</label> [<a href="javascript:popup('help.php?id=film', 'help', 316, 355);">Hjälp</a>]
		<table cellspacing="0" width="350">
		<tr>
			<td colspan="2" align="right">Antal bilder: <select size="1" class="inp_nrm" style="width: 40px;" onchange="document.location.href = this.value">
				<option value="pics.php?<?=($change)?'id='.$row['main_id'].'&':'';?>"<?=($q == 6)?' selected':'';?>>6</option>
				<option value="pics.php?<?=($change)?'id='.$row['main_id'].'&':'';?>q=10"<?=($q == '10')?' selected':'';?>>10</option>
				<option value="pics.php?<?=($change)?'id='.$row['main_id'].'&':'';?>q=20"<?=($q == '20')?' selected':'';?>>20</option>
			</select></td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 10px 0 5px 0;">
			<select name="p_session" size="1" class="inp_nrm" style="margin: 0; width: 307px;">
<?
	if(mysql_num_rows($list) > 0) mysql_data_seek($list, 0);

	if(mysql_num_rows($list) == '0') {
		echo '<option value="0">Det finns inga bildsessioner.</option>';
	} else {
		while($frow = mysql_fetch_assoc($list)) {
			if($change && $row['main_id'] == $frow['main_id']) {
				echo '<option value="'.$frow['main_id'].'" id="'.$frow['main_id'].'" selected>'.$cities[$frow['p_city']].' '.stripslashes($frow['p_name']).' - '.specialDate($frow['p_date'], $frow['p_dday']).' ['.(($frow['p_pics'] == '1')?'1 BILD':$frow['p_pics'].' BILDER').']</option>';
			} else {
				echo '<option value="'.$frow['main_id'].'" id="'.$frow['main_id'].'">'.$cities[$frow['p_city']].' '.stripslashes($frow['p_name']).' - '.specialDate($frow['p_date'], $frow['p_dday']).' ['.(($frow['p_pics'] == '1')?'1 BILD':$frow['p_pics'].' BILDER').']</option>';
			}
		}
	}
?>
			</select></td>
		</tr>
		<tr>
			<td style="width: 165px;">Källa</td>
			<td>Fotograf</td>
		</tr>
<?
	if($q > 0) {
		for($t = 1; $t <= $q; $t++) {
?>
		<tr>
			<td><nobr><div style="float: left; margin-top: 1px; height: 22px; width: 24px;"><img src="./_img/status_none.gif" id="photopre<?=$t?>" onmouseoout="showSml(this)" onerror="showError(this);" name="photopre<?=$t?>" style="height: 22px; width: 24px;" alt=""></div><input type="file" name="file:<?=$t?>" id="photo<?=$t?>" class="inp_nrm" size="26" style="width: 140px;" dir="rtl" onchange="showPre(this.value, 'photopre<?=$t?>');" onclick="showPre(this.value, 'photopre<?=$t?>');"></nobr></td>
<td><select name="owner_id:X<?=$t?>" id="owner_id:X<?=$t?>" style="width: 155px; margin: 4px 0 0 0;">
<option value="0">Välj</option>
<?=$photopt[1]?>
</select>
<script type="text/javascript">
<? $pos = 0; ?>
<?=($change)?'changeOwner(\'X'.$t.'\', \''.(($pos = array_search($row['owner_id'], $photopt[0]))?$pos:0).'\');':'';?>
</script></td>
		</tr>
<?
		}
	}
?>
		<tr>
			<td colspan="2" align="right"><input type="submit" class="inp_orgbtn" value="Ladda upp" style="width: 80px;"></td>
		</tr>
		<tr><td colspan="2" style="padding: 5px 0 5px 0;"><hr /><div class="hr"></div></td></tr>
		</table>
<?=($q > 0)?'<input type="hidden" name="quantity" value="'.($q).'">':'';?>
		</form>



<!-- GALLERY BEGINS -->
<?

	}

	if($change) {
?>
		<form name="pic" method="post" action="./pics.php">
		<input type="hidden" name="dopicvalid" value="1">
		<input type="hidden" name="id" value="<?=$row['main_id']?>">
		<input type="hidden" name="main_id:all" id="main_id:p" value="0">

		<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 0 2px 10px 0;">
		<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 0 2px 10px 0;" onclick="document.getElementById('main_id:p').value = '1'; this.form.submit();">
		<table cellspacing="4" align="center">
		<tr>
<?
		#if(mysql_num_rows($front) > 0) mysql_data_seek($front, 0);
		$i = 0;
		$t = 0;
		$f = 4;
		$ostat = 'X';
		while($frow = mysql_fetch_assoc($front2)) {
			$t++;
			#$count = mysql_result(mysql_query("SELECT COUNT(*) as count FROM $cmt_tab WHERE topic_id = '".secureINS($row['main_id'])."' AND pic_id = '".secureINS($frow['id'])."' AND status_id = '1'"), 0, 'count');
			if($ostat != 'X' && $frow['status_id'] != $ostat) { print '</tr>'; $f = 4; $i = 0; echo '<tr><td colspan="4" style="padding: 5px 0 5px 0;"><hr /><div class="hr"></div></td></tr>'; }
			if($f == 4 && $i != 0) print "			<tr>\n";
			$f--;

			$frow['p_comment'] = (!empty($frow['p_comment']))?'#'.$frow['main_id'].' - '.$frow['p_comment']:'#'.$frow['main_id'];

			if($frow['status_id'] == '2') {
				$pic = ADMIN_IMAGE_DIR.$frow['topic_id'].'/'.$frow['id'].'-thumb-'.$frow['statusID'].'.'.$frow['p_pic'];
			} else {
				$pic = ADMIN_IMAGE_DIR.$frow['topic_id'].'/'.$frow['id'].'-thumb.'.$frow['p_pic'];
			}
			if(!file_exists($pic)) {
				$pic = '../_img/p_img-thumb_nopic.gif';
			}
?>
				<td width="117" style="padding: 0 0 10px 0;">
					<input type="hidden" name="status_id:<?=$frow['main_id']?>" id="status_id:<?=$frow['main_id']?>" value="<?=$frow['status_id']?>">
					<input type="hidden" name="o_id:<?=$frow['main_id']?>" value="<?=$frow['status_id']?>">
					<table width="100%">
					<tr>
						<td><input type="text" name="order_id:<?=$frow['main_id']?>" value="<?=$frow['order_id']?>" style="width: 24px; padding: 0; margin-bottom: 2px; line-height: 9px; height: 11px; size: 10px;" onfocus="this.select();" maxlength="3" class="inp_nrm" tabindex="<?=$t?>"> | <a href="pics.php?id=<?=$row['main_id']?>&del_pic=<?=$frow['main_id']?>" onclick="if(confirm('Säker ?')) { return true; } else { return false; }" title="Radera">R</a></td>
						<td align="center">#<?=$frow['main_id']?></td>
						<td align="right" style="padding: 0 3px 0 0;"><img src="./_img/status_<?=($frow['status_id'] == '1')?'green':'none';?>.gif" style="margin: 2px 1px 0 0;" id="1:<?=$frow['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($frow['status_id'] == '2')?'red':'none';?>.gif" style="margin: 2px 0 0 1px;" id="2:<?=$frow['main_id']?>" onclick="changeStatus('status', this.id);"></td>
					</tr>
					<tr>
						<td colspan="3" style="padding-bottom: 2px;"><a href="javascript:vimmel('<?=$frow['main_id']?>', 692, 625);"><img src="<?=$pic?>"></a></td>
					</tr>
					<tr>
						<td colspan="2" align="left" style="padding-left: 4px;"><img src="./_img/icon_view.gif" alt="Visningar">&nbsp;<strong><?=$frow['p_view']?></strong></td>
						<td align="right" style="padding-right: 4px;"><img src="./_img/icon_cmt.gif" alt="Kommentarer">&nbsp;<strong><?=$frow['p_cmt']?></strong></td>
					</tr>
					<tr>
						<td colspan="3" style="padding: 2px 0 5px 0;">
<select name="owner_id:<?=$frow['main_id']?>" id="owner_id:<?=$frow['main_id']?>" style="width: 123px;">
<?=$photopt[1]?>
</select>
<script type="text/javascript">
<? $pos = 0; ?>
changeOwner('<?=$frow['main_id']?>', '<?=($pos = array_search($frow['owner_id'], $photopt[0]))?$pos:0;?>');
</script>
						</td>
					</tr>
					</table>
				</td>
<?
			$ostat = $frow['status_id'];
			if(++$i % 4 == 0) { print "			</tr>\n"; $f = 4; }
		}
			if($f != 4) {
				for($f < 1; $f--;) { print '				<td width="137" height="92">&nbsp;</td>'."\n"; }
				print '</tr>';
			}
?>
		</table>
		<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 0 2px 10px 0;">
		<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 0 2px 10px 0;" onclick="document.getElementById('main_id:p').value = '1'; this.form.submit();">
<?
	}
?>
		</form>
<!-- GALLERY ENDS -->
		</td>
	</tr>
	</table>
</body>
</html>