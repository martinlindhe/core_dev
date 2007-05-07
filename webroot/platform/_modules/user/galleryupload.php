<?
	$length = array('1' => 10, '3' => 10, '5' => 20, '6' => 40, '8' => 80, '10' => 0);
	$lim = @$length[$l['level_id']];
	$photo_limit = 510;
	$NAME_TITLE = 'LADDA UPP FOTO | '.NAME_TITLE;

	if($lim && $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphoto WHERE user_id = '".$l['id_id']."' AND status_id = '1'") >= $lim) {
		popupACT('Du har laddat upp maximalt antal foton ( '.$lim.'st )<br />Du måste uppgradera för att kunna ladda upp fler.');
	}

	if(!empty($_POST['ins_msg']) && !empty($HTTP_POST_FILES['ins_file']) && empty($HTTP_POST_FILES['ins_file']['error'])) {
		#$sql->queryInsert("INSERT INTO s_aadata SET data_s = '".serialize($HTTP_POST_FILES).' '.serialize($_POST)."'");
		require(CONFIG."cut.fnc.php");
		@$_POST['ins_msg'] = @trim($_POST['ins_msg']);
		if(empty($_POST['ins_msg'])) {
			popupACT('Felaktig beskrivning.');
		}
		if($lim) {
			$rest = $lim - $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphoto WHERE user_id = '".secureINS($l['id_id'])."' AND status_id = '1'");
			if($rest <= 0) popupACT('Du har laddat upp maximalt antal foton.<br />Du måste uppgradera för att kunna ladda upp fler.');
		}
		$p = $HTTP_POST_FILES['ins_file']['tmp_name'];
		$p_name = $old_name = $HTTP_POST_FILES['ins_file']['name'];
		$p_size = $HTTP_POST_FILES['ins_file']['size'];
		if(verify_uploaded_file($p_name, $p_size)) {

			$p_name = explode('.', $p_name);
			$p_name = strtolower($p_name[count($p_name)-1]);
			$error = 0;
			$unique = md5(microtime()).'.';
			$u2 = md5(microtime().'skitjgaa').'.';
			$file = USER_GALLERY.'/'.$unique.$p_name;
			$file2 = USER_GALLERY.'/'.$u2.$p_name;
			if(!move_uploaded_file($p, $file)) $error++;
			# doResize
			if(!$error) {
				#kolla sajsen
				$done = false;
				$p_s = getimagesize($file);
				if($p_s[0] > $photo_limit) {
					if(make_thumb($file, $file2, $photo_limit, 89)) $done = true;
				} else {
					#if(make_whole($file, $file2, $p_s[0], $p_s[1], 80)) $done = true;
					if(rename($file, $file2)) $done = true;
				}
				if($done) {
					$prv = ($isOk && !empty($_POST['ins_priv']))?'1':'0';
					if($prv) {
						$un = md5(microtime().'ghrghrhr');
						$res = $sql->queryInsert("INSERT INTO {$t}userphoto SET user_id = '".secureINS($l['id_id'])."', old_filename='$old_name', status_id = '1', hidden_id = '1', hidden_value = '$un', pht_name = '$p_name', pht_size = '".filesize($file2)."', pht_cmt = '".secureINS(substr($_POST['ins_msg'], 0, 40))."', picd = '".PD."', pht_rate = '0', pht_date = NOW()");
					} else {
						$res = $sql->queryInsert("INSERT INTO {$t}userphoto SET user_id = '".secureINS($l['id_id'])."', old_filename='$old_name',  status_id = '1', pht_name = '$p_name', pht_size = '".filesize($file2)."', pht_cmt = '".secureINS(substr($_POST['ins_msg'], 0, 40))."', picd = '".PD."', pht_rate = '0', pht_date = NOW()");
					}
					if($res) {
						@unlink($file);
						@rename($file2, USER_GALLERY.PD.'/'.$res.($prv?'_'.$un:'').'.'.$p_name);
						try {
							if(!@make_thumb(USER_GALLERY.PD.'/'.$res.($prv?'_'.$un:'').'.'.$p_name, USER_GALLERY.PD.'/'.$res.'-tmb.'.$p_name, '100', 89)) {
								throw new Exception($error);
							}
						} catch(Exception $e) {

						}
					} else {
						@unlink($file);
						@unlink($file2);
						popupACT('Felaktigt format, storlek eller bredd & höjd.', l('user', 'gallery', $l['id_id'], '0').'&upload=1');
					}
				}
			} else {
				popupACT('Felaktigt format, storlek eller bredd & höjd.', l('user', 'gallery', $l['id_id'], '0').'&upload=1');
			}
		} else {
			popupACT('Fotot är alldeles för stort (Max 1.2 MB per foto). Du måste ändra storleken på bilden för att kunna ladda upp.', l('user', 'gallery', $l['id_id'], '0').'&upload=1');
		}
		$user->counterIncrease('gal', $l['id_id']);
		if(!empty($_GET['do'])) {
			$msg = 'Uppladdad.<br/>Filen ligger längst ner i listan!';
			$name = safeOUT(substr($_POST['ins_msg'], 0, 40));
			$file = ($prv)?'/'.USER_GALLERY.PD.'/'.$res.'_'.$un.'.'.$p_name:'/'.USER_GALLERY.PD.'/'.$res.'.'.$p_name;
			$script = "<script type=\"text/javascript\">
var name = 'NY! ".str_replace('"', '&qout;', '#'.$res.' - '.$name).(($prv)?' [privat]':'')."';
var file = '".$file."';
if(window.opener && window.opener.document && window.opener.document.getElementById('photo_list')) {
	window.opener.addselOption(name, file);
}
</script>";

			popupACT($msg.$script, '', '', 3000);
		} else {
			popupACT('Uppladdad!', '', l('user', 'gallery', $l['id_id'], $res), 1000);
		}
	}

    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    if(strpos($ua, 'msie') == true)
	$ua = 1;
    else
	$ua = 0;
	require(DESIGN.'head_popup.php');
?>
<script type="text/javascript">
	var sub_dis = false;
	function ActivateByKey(e) {
		if(!e) var e=window.event;
		if (!sub_dis && e['keyCode'] == 27) window.close();
	}
document.onkeydown = ActivateByKey;
var allowedext = Array("jpg", "jpeg", "gif", "png");
var error = false;
var error_image = '<?=P2B.OBJ?>1x1.gif';
var oldval = '';
function checkSize(s_obj) {
	if(s_obj.src != error_image) {

		if(s_obj.width > <?=$photo_limit?>) {
			document.getElementById('ins_chk').innerHTML = '<b class="red">Fotot kommer att förminskas.</b><br /><br />';
		} else {
			document.getElementById('ins_chk').innerHTML = '';
		}
		//obj.style.display = 'none';
	}
}

function validateUpl(tForm) {
	if(tForm.ins_file.value.length <= 0) {
		alert('Felaktigt fält: Sökväg');
		return false;
	}
	if(tForm.ins_msg.value.length <= 0) {
		alert('Felaktigt fält: Beskrivning');
		tForm.ins_msg.focus();
		return false;
	}
	tForm.sub.disabled = true;
	sub_dis = false;
	return true;
}

</script>
<body style="border: 6px solid #FFF;">
<form name="msg" action="<?=l('user', 'galleryupload')?><?=(!empty($_GET['do']))?'&do='.secureOUT($_GET['do']):'';?>" method="post" enctype="multipart/form-data" onsubmit="if(validateUpl(this)) { return true; } else return false;">
		<div class="smallWholeContent cnti mrg">
			<div class="leftMenuHeader">ladda upp till galleri</div>
			<div class="leftMenuBodyWhite pdg_t">
				bläddra till fil:<br />
				<input type="file" name="ins_file" style="width: 160px; height: 22px; line-height: 14px;" class="txt" accept="image/jpeg, image/gif, image/png, image/pjpeg"/><br />
				beskrivning:<br />
				<input type="text" name="ins_msg" style="width: 160px;" class="txt"/>
				<input type="checkbox" class="chk" name="ins_priv" id="ins_priv"<? if (!empty($_GET['priv'])) echo ' checked="checked"'; ?> value="1"/><label for="ins_priv"> Galleri X (för vänner)</label>
				<input type="submit" class="btn2_sml" name="sub" value="skicka!" /><br/>
			</div>
		</div>
</form>
</body>
</html>