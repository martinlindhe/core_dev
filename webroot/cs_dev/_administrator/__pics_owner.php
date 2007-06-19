<?
session_start();
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	$sql = &new sql();
	require("./lib_dir.php");
	$dir = getDirList(ADMIN_OWNER_DIR);

	$change = false;

	if(!empty($_POST['p_name'])) {
		if(isset($_POST['id']) && is_numeric($_POST['id'])) {
			mysql_query("UPDATE $owner_tab SET
			p_name = '".secureINS($_POST['p_name'])."', p_pic = '".secureINS($_POST['p_pic'])."', p_mail = '".secureINS($_POST['p_mail'])."' WHERE main_id = '".secureINS($_POST['id'])."' LIMIT 1");
			$id = $_POST['id'];
		} else {
			mysql_query("INSERT INTO $owner_tab SET
			p_name = '".secureINS($_POST['p_name'])."',
			p_mail = '".secureINS($_POST['p_mail'])."',
			p_pic = '".secureINS($_POST['p_pic'])."'");
			$id = mysql_insert_id();
		}
		$gotpic = false;
		foreach($_FILES as $key => $val) {
			if(strpos($key, 'file') !== false) {
				$kid = explode(":", $key);
				$kid = $kid[1];
				if(!$_FILES['file:'. $kid]['error']) {
					$p = $_FILES['file:'. $kid]['tmp_name'];
					$p_name = $_FILES['file:'. $kid]['name'];
					$p_size = $_FILES['file:'. $kid]['size'];
					if(verify_uploaded_file($p_name, $p_size)) {
						$unique = md5(microtime());
						$p_name = explode('.', $p_name);
						$p_name = $p_name[count($p_name)-1];
						$error = 0;

						if(move_uploaded_file($p, ADMIN_OWNER_DIR.$id.'.'.$p_name)) {
							$gotpic = true;
							$sql->queryUpdate("UPDATE {$tab['owner']} SET p_pic = '".secureINS($id.'.'.$p_name)."' WHERE main_id = '".secureINS($id)."' LIMIT 1");
						} else {
							$msg = 'Felaktigt format, storlek eller bredd & höjd.';
							$js_mv = 'pics_owner.php';
							require("./_tpl/notice_admin.php");
							exit;
						}
					} else {
						$msg = 'Felaktig bild.';
						$js_mv = 'pics_owner.php';
						require("./_tpl/notice_admin.php");
						exit;
					}
				}
			}
		}
		header("Location: pics_owner.php");
		exit;
	}

	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql = mysql_query("SELECT p_pic FROM $owner_tab WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			@unlink(ADMIN_OWNER_DIR.mysql_result($sql, 0, 'p_pic'));
			mysql_query("DELETE FROM $owner_tab WHERE main_id = '".secureINS($_GET['del'])."'");
		}
		header("Location: pics_owner.php");
		exit;
	}

	if(isset($_GET['id']) && is_numeric($_GET['id'])) {
		$sql=mysql_query("SELECT * FROM $owner_tab WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$change = true;
			$row = mysql_fetch_assoc($sql);
		}
	}

	$photo = mysql_query("SELECT * FROM $owner_tab ORDER BY p_name");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>FOTOGRAFER | <?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
</head>

<body>
<script type="text/JavaScript">
  function changeByKey(e) {
	if (!e) var e=window.event;

	if (e['keyCode'] == 27) if(confirm('Ladda om huvudfönster för att uppdatera ändringar?')) { window.opener.location.reload(); window.close(); }

	if (e.ctrlKey && e['keyCode'] == 13) document.owner_edit.submit();
  }
function fixVal(na) {
	name = na;
	span = document.getElementById('img');
	if(name == '0') {
		span.innerHTML = '';
	} else {
		span.innerHTML = '<img src="' + name + '"><br>';
	}
}
document.onkeydown = changeByKey;
</script>
	<form name="owner_edit" method="post" action="pics_owner.php" ENCTYPE="multipart/form-data">
<?=($change)?'<input type="hidden" name="id" value="'.secureOUT($row['main_id']).'">':'';?>
	<table width="383" style="margin: 10px 0 0 10px;">
	<tr><td colspan="3" height="25"><b><a href="javascript:self.close();" onclick="if(confirm('Ladda om huvudfönster för att uppdatera ändringar?')) window.opener.location.reload();">Vimmel</a> - <?=($change)?'<a href="pics_owner.php">FOTOGRAFER</a>':'FOTOGRAFER';?></b></td></tr>
	<tr> 
		<td>Namn</td>
	</tr>
	<tr> 
		<td><input type="text" name="p_name" class="inp_nrm" style="width: 210px;" value="<?=($change)?secureOUT($row['p_name']):'';?>"></td>
	</tr>
	<tr> 
		<td>E-post</td>
	</tr>
	<tr> 
		<td><input type="text" name="p_mail" class="inp_nrm" style="width: 210px;" value="<?=($change)?secureOUT($row['p_mail']):'';?>"></td>
	</tr>
	<tr> 
		<td colspan="3">Bild</td>
	</tr>
	<tr> 
		<td colspan="2">
<select name="p_pic" class="inp_nrm" style="width: 262px;" onchange="fixVal(((this.value == '0')?0:'<?=ADMIN_OWNER_DIR?>/' + this.value));">
<option value="0">Välj</option>
<?
	foreach($dir['files'] as $val) {
		$val = substr(strrchr($val, "/"), 1);
		$selected = ($change && $row['p_pic'] == $val)?' selected':'';
		echo '<option value="'.$val.'"'.$selected.'>'.$val.'</option>';
	}
?>
</select>
<input type="file" name="file:0" class="inp_nrm">
<span id="img"></span>
		<td style="padding: 3px 0 0 7px;"><?=($change && $row['main_id'] != '0')?'<input type="button" name="del" onClick="javascript:window.location = \'pics_owner.php?del='.$row['main_id'].'\';" class="inp_orgbtn" style="margin: 0; width: 40px;" value="Rad." title="Radera">&nbsp;':'';?><input type="submit" name="submit" class="inp_orgbtn" style="margin: 0; width: <?=($change && $row['main_id'] != '0')?'40':'84';?>px;" value="<?=($change)?(($row['main_id'] != '0')?'Uppd.':'Uppdatera'):'Lägg till';?>"></td>
	</tr>
	<tr>
	<td colspan="3"><div class="hr"></div><hr /></td>
	</tr>
	</table>
	<table cellspacing="0" width="383" style="margin-left: 10px;">
<?
	while($row = mysql_fetch_assoc($photo)) {
?>
	<tr>
		<?=(!empty($row['p_pic']))?'<td style="width: 70px;"><img src="'.ADMIN_OWNER_DIR.$row['p_pic'].'">':'<td><em>Ingen</em></td>';?>
		<td><a href="pics_owner.php?id=<?=$row['main_id']?>" title="<?=$row['main_id']?>"><?=secureOUT($row['p_name'])?></a></td>
		<td align="right"><?=secureOUT($row['p_mail'])?></td>
	</tr>
<?	}
?>
	<tr>
	<td colspan="3"><div class="hr"></div><hr /></td>
	</tr>
	</table>
	</form>
</body>
</html>