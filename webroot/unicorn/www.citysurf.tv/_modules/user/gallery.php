<?
	require_once(dirname(__FILE__).'/../user/spy.fnc.php');
	require_once(dirname(__FILE__).'/../user/relations.fnc.php');

	$isFriends = $user->isFriends($s['id_id']);
	$allowed = ($own || $isFriends || $isAdmin)?true:false;

	if(isset($_GET['gallerycomment'])){
   		include('gallery_comment.php');
		exit;
	}

	if(!empty($key) && is_numeric($key)) {
		//hanterar även ändring av beskrivning
		include('gallery_view.php');
		exit;
	}

	/* bevakningar */
	if ($s['id_id'] && isset($_GET['subscribe'])) {
		spyAdd($s['id_id'], 'g');
	}

	if ($s['id_id'] && isset($_GET['unsubscribe'])) {
		spyDelete($s['id_id'], 'g');
	}

	//Markera en bild som raderad
	if(!empty($_GET['d'])) {
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, pht_date, pht_cmt FROM {$t}userphoto WHERE main_id = '".secureINS($_GET['d'])."' LIMIT 1", 1);
		if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $res['user_id'] != $l['id_id']) {
			errorACT('Felaktigt inlägg.', l('user', 'gallery', $s['id_id']));
		} else {
			$sql->queryUpdate("UPDATE {$t}userphoto SET status_id = '2' WHERE main_id = '".$res['main_id']."' LIMIT 1");
			$user->counterDecrease('gal', $l['id_id']);
			reloadACT(l('user', 'gallery', $s['id_id']));
		}
	}

	$paging = paging(@$_GET['p'], 10);
	$q = "SELECT main_id, pht_cmt, pht_cmts, pht_date, hidden_id FROM {$t}userphoto WHERE user_id = '".$s['id_id']."' AND status_id = '1' ORDER BY main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}";
	$res = $sql->query($q, 0, 1);
	$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphoto WHERE user_id = '".$s['id_id']."' AND status_id = '1'");

	$page = 'gallery';

	require(DESIGN.'head_user.php');

	/*
	if (!$user->vip_check(VIP_LEVEL1)) {
		echo 'Du måste vara VIP för att kunna se andras galleribilder.<br/><br/>';
		echo '<a href="/main/upgrade/">Klicka här</a> för mer information.';
		require(DESIGN.'foot_user.php');
		die;
	}
	*/
	echo '<br class="clr"/>';
	if ($own) {
		makeButton(false, 'makeUpload();', 'icon_gallery.png', 'ladda upp ny');
		makeButton(false, 'makeTiny(\''.l('text', 'mmshelp', '2').'\')', 'icon_gallery.png', 'mms-uppladdning');
	} else {
		if ($user->vip_check(VIP_LEVEL1)) {
			if (spyActive($s['id_id'], 'g')) {
				makeButton(false, 'goLoc(\''.l('user', 'gallery', $s['id_id']).'&unsubscribe'.'\')', 'icon_settings.png', 'sluta spana');
			} else {
				makeButton(false, 'goLoc(\''.l('user', 'gallery', $s['id_id']).'&subscribe'.'\')', 'icon_settings.png', 'spana');
			}
		}
	}
	
	$gall_res = $sql->query("SELECT * FROM {$t}userphoto WHERE user_id = '".$s['id_id']."' AND status_id = '1' AND hidden_id = '0' ORDER BY main_id DESC", 0, 1);
	$gallx_res = $sql->query("SELECT * FROM {$t}userphoto WHERE user_id = '".$s['id_id']."' AND status_id = '1' AND hidden_id = '1' ORDER BY main_id DESC", 0, 1);
	
	makeButton(false, 'document.location=\'#gall\'', 'icon_gallery.png', 'galleri ['.count($gall_res).']');
	makeButton(false, 'document.location=\'#gallx\'', 'icon_gallery.png', 'galleri X ['.count($gallx_res).']');
?>
<br class="clr"/><br/>

<a name="gall"></a>
<div class="subHead">galleri</div><br class="clr"/>
<?
/* <script type="text/javascript">var first = '<?=$first?>'; ext = '<?=$ext?>';</script> */
		$paging = paging(1, 20);
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphoto WHERE user_id = '".$s['id_id']."' AND status_id = '1' AND hidden_id = '0' LIMIT 1");
		$name = 'galleri';
		$all = true;
		$res = $gall_res;
		include('gallerylist.php');
?>
<br/>
<a name="gallx"></a>
<div class="subHead">galleri x</div><br class="clr"/>
<?
	if ( ($s['id_id'] != $l['id_id'] && getGallXStatus($s['id_id']) == 0) || !$isAdmin ) {
		echo 'Denna medlem har inte tillåtit dig att se dennes galleri x-bilder.';
	} else {
		$paging = paging(1, 20);
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userphoto WHERE user_id = '".$s['id_id']."' AND status_id = '1' AND hidden_id = '1'");
		$name = 'galleri x';
		$all = $allowed;
		$res = $gallx_res;
		include('gallerylist.php');
	}

	require(DESIGN.'foot_user.php');
?>