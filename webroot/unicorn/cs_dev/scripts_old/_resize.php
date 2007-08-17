<?
require("_config/online.include.php");
	$photo_limit = 510;
	$get=$sql->query("SELECT user_id,picd,old_filename FROM s_userphoto");
		require(CONFIG."cut.fnc.php");
		$p = $_FILES['ins_file']['tmp_name'];
		$p_name = $_FILES['ins_file']['name'];
		$p_size = $_FILES['ins_file']['size'];
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
						$res = $sql->queryInsert("INSERT INTO s_userphoto SET user_id = '".secureINS($l['id_id'])."', status_id = '1', hidden_id = '1', hidden_value = '$un', pht_name = '$p_name', pht_size = '".filesize($file2)."', pht_cmt = '".secureINS(substr($_POST['ins_msg'], 0, 40))."', picd = '".PD."', pht_rate = '0', pht_date = NOW()");
					} else {
						$res = $sql->queryInsert("INSERT INTO s_userphoto SET user_id = '".secureINS($l['id_id'])."', status_id = '1', pht_name = '$p_name', pht_size = '".filesize($file2)."', pht_cmt = '".secureINS(substr($_POST['ins_msg'], 0, 40))."', picd = '".PD."', pht_rate = '0', pht_date = NOW()");
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

function imgError(ie_obj) {
	error = true;
	hideAll(ie_obj);
	return;
}
function scriptError(se_obj) {
	hideAll(se_obj);
	return;
}
function hideAll(h_obj) {
	h_obj.src = error_image;
	cont.style.display = 'none';
}
function showPre(val) {
	size.src = error_image;
	if(val != '') {
		var showimg = false;
		ext = val.split(".");
		ext = ext[ext.length - 1].toLowerCase();
		for(var i = 0; i <= allowedext.length; i++)
			if(allowedext[i] == ext) 
				showimg = true;
		if(showimg) {
			previewpic = val;
			error = false;
			obj.src = 'file://' + val.replace(/\\/g,'/');
			size.src = 'file://' + val.replace(/\\/g,'/');
			if(!error) {
				cont.style.display = '';
			}
		} else {
			scriptError(obj);
		}
	} else {
			scriptError(obj);
	}
	oldval = val;
}
</script>
<body style="border: 6px solid #FFF;">
<form name="msg" action="<?=l('user', 'galleryupload')?><?=(!empty($_GET['do']))?'&do='.secureOUT($_GET['do']):'';?>" method="post" enctype="multipart/form-data" onsubmit="if(validateUpl(this)) { return true; } else return false;">
		<div class="smallWholeContent cnti mrg">
			<div class="smallHeader1"><h4>ladda upp till galleri</h4></div>
			<div class="smallFilled2 pdg_t wht">
				<div style="margin: 10px 0 0 5px;">
				bläddra till fil:<br /><input type="file" name="ins_file" width="160" style="width: 160px; height: 22px; line-height: 14px;" class="txt" accept="image/jpeg, image/gif, image/png, image/pjpeg"><script type="text/javascript">document.msg.ins_file.focus();</script>
				<br />beskrivning:<br /><input type="text" name="ins_msg" width="160" style="width: 160px;" class="txt">
				<?=($isOk)?'<div style="float: left; margin-top: 3px;"><input type="checkbox" class="chk" name="ins_priv" id="ins_priv" '.(!empty($_GET['priv'])?'checked':'').' value="1"><label for="ins_priv"> Galleri X (för vänner)</label></div>':'';?>
				</div>
				<input type="submit" class="btn2_sml r" name="sub" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>
</body>
</html>