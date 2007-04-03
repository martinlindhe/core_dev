<?
class user {
	var $sql, $self, $info, $t, $id;
	function user($sql = false) {
		if($sql) $this->sql = $sql;
		$this->t = T;
	}
	function auth($id) {
		if(!@is_md5($id)) return false;
		#empty($_SESSION['c_h']) && 
		if($this->timeout('15 MINUTES') > @$_SESSION['data']['account_date']) {
			$res = now();
			$_SESSION['data']['account_date'] = $res;
			$this->sql->queryUpdate("UPDATE {$this->t}user SET account_date = '$res' WHERE id_id = '".secureINS($id)."' LIMIT 1");
			$this->sql->queryUpdate("UPDATE {$this->t}useronline SET account_date = '$res' WHERE id_id = '".secureINS($id)."' LIMIT 1");
		}
		$this->id = $id;
		#$this->setRelCount($id);
		#$this->get_cache();
		if(!@$_SESSION['data']['cachetime'] || $this->timeout('30 SECONDS') > @$_SESSION['data']['cachetime']) {
			$_SESSION['data']['cachetime'] = now();
			$this->setRelCount($id);
			$this->get_cache();
		}
		return $this->getsessionuser($id);
	}
	function setRelCount($uid) {
		$rel_c = $this->sql->queryResult("SELECT COUNT(*) as count FROM {$this->t}userrelquest a INNER JOIN {$this->t}user u ON u.id_id = a.sender_id AND u.status_id = '1' WHERE a.user_id = '".secureINS($uid)."' AND a.status_id = '0'");
		$id = $this->setinfo($uid, 'rel_count', $rel_c);
		if($id[0]) $this->setrel($id[1], 'user_retrieve', $uid);
	}
	function get_cache() {
		$arr = $this->sql->queryLine("SELECT u_picd, u_picid FROM {$this->t}user WHERE id_id = '".$this->id."' LIMIT 1");
		if(@$_SESSION['data']['u_picd'] != $arr[0]) @$_SESSION['data']['u_picd'] = $arr[0];
		if(@$_SESSION['data']['u_picid'] != $arr[1]) @$_SESSION['data']['u_picid'] = $arr[1];
		$info = $this->getcontent($this->id, 'user_retrieve');
		$arr = '';
		$i = 0;
		$str = array();
		foreach($info as $item) {
			$arr .= ($i?'&':'').strtoupper(substr($item[0], 0, 1)).'='.$item[1];
			if(is_numeric($item[1]) && $item[1] > 0) $str[] = str_replace('r', 'v', substr($item[0], 0, 1)).$item[1];
			$i++;
		}
		$cha_c = $this->sql->queryResult("SELECT COUNT(DISTINCT(sender_id)) as count FROM {$this->t}userchat WHERE user_id = '".secureINS($this->id)."' AND user_read = '0'");
		if($cha_c > 0)
			$cha_id = $this->sql->queryResult("SELECT c.sender_id FROM {$this->t}userchat c INNER JOIN {$this->t}user u ON u.id_id = c.sender_id AND u.status_id = '1' WHERE c.user_id = '".secureINS($this->id)."' AND c.user_read = '0' ORDER BY c.sent_date ASC LIMIT 1");
		else $cha_id = '';
		if($cha_id) {
			#echo 'chat_count:'.$cha_c.':'.$cha_id;
			$arr .= '&C='.$cha_c.'&I='.$cha_id;
			$str[] = 'c'.$cha_c;
		}
		$str = implode(' ', $str);
		if($arr != @$_SESSION['data']['cache']) {
			$_SESSION['data']['cache'] = $arr;
		}
		$_SESSION['data']['cachestr'] = $str;
	}
	function isuser($id, $status = '1') {
		return ($this->sql->queryResult("SELECT status_id FROM {$this->t}user WHERE id_id = '".secureINS($id)."' LIMIT 1") == $status)?true:false;
	}
	function level($level, $allowed = '10') {
		if(intval($level) >= intval($allowed)) return true; else return false;
	}
	function getuser($id, $more = '') {
		if(@$_SESSION['c_i'] == $id) return $this->getsessionuser($id);
		//removed u_picvalid because of a later validation instead of prevalidation.
		$return = $this->sql->queryLine("SELECT status_id, id_id, u_alias, u_sex, u_picid, u_picd, u_picvalid, u_birth, level_id, account_date, u_pstlan_id, CONCAT(u_pstort, ', ', u_pstlan) as u_pst, lastlog_date, lastonl_date, u_regdate, beta $more FROM {$this->t}user WHERE id_id = '".secureINS($id)."' LIMIT 1", 1);
		return ($return && $return['status_id'] == '1')?$return:false;
	}
	function getsessionuser($id) {
		return @$_SESSION['data'];
	}
	function getuserfill($arr, $line = '*') {
		if($line != '*') $line = substr($line, 2);
		$return = $this->sql->queryLine("SELECT $line FROM {$this->t}user WHERE id_id = '".secureINS($arr['id_id'])."' LIMIT 1", 1);
		return ($return)?array_merge($arr, $return):$arr;
	}
	function getuserfillfrominfo($arr, $line = '*') {
		if($line != '*') $line = substr($line, 2);
		$return = $this->sql->queryLine("SELECT $line FROM {$this->t}user_info WHERE id_id = '".secureINS($arr['id_id'])."' LIMIT 1", 1);
		return ($return)?array_merge($arr, $return):$arr;
	}
	function info($id, $level = '1') {
		switch($level) {
		case '1':
			return $this->sql->queryAssoc("SELECT u_email, u_pstnr, u_subscr, u_fname, u_sname, u_street, u_cell FROM {$this->t}user WHERE id_id = '".secureINS($id)."' LIMIT 1");
		break;
		}
	}
	function blocked($user) {
		$isBlocked = $this->sql->queryResult("SELECT rel_id FROM {$this->t}userblock WHERE user_id = '".secureINS($this->id)."' AND friend_id = '".secureINS($user)."' LIMIT 1");
		if($isBlocked) { if($isBlocked == 'u') errorACT('Du har blockerat personen.', l('user', 'view', $this->id)); else errorACT('Du är blockerad.', l('user', 'view', $this->id)); }
	}
	function isFriends($id, $noadmin = 0) {
		global $isAdmin;
		if($id == $this->id) return true;
		if($noadmin)
			return ($this->sql->queryResult("SELECT rel_id FROM {$this->t}userrelation WHERE user_id = '".secureINS($this->id)."' AND friend_id = '".secureINS($id)."' LIMIT 1"))?true:false;
		else
			return ($isAdmin || $this->sql->queryResult("SELECT rel_id FROM {$this->t}userrelation WHERE user_id = '".secureINS($this->id)."' AND friend_id = '".secureINS($id)."' LIMIT 1"))?true:false;
	}
	function tagline($str) {
		return secureOUT($str);
	}
	function getcontent($id, $type) {
		return $this->sql->querybycontent("SELECT o.content_type, o.content, o.main_id FROM {$this->t}objrel r LEFT JOIN {$this->t}obj o ON o.main_id = r.object_id WHERE r.content_type = '$type' AND r.owner_id = '".secureINS($id)."'");
	}
	//makeover
	/*function getphoto($line, $valid = false, $small = 0, $admin = false, $string = '', $size = '') {
		if(empty($line)) {
			return '<img alt=""'.((!$small)?'':((!empty($size))?'height="'.$size.'" ':'')).'src="'.(($admin)?'.':'').'./_img/user_deleted'.(($small)?'_2':'').'.gif" />';
		} elseif(substr($line, 0, 3) == 'SYS') {
			return '<img alt=""'.((!$small)?'':((!empty($size))?'height="'.$size.'" ':'')).'src="'.(($admin)?'.':'').'./_img/SYS'.(($small)?'_2':'').'.jpg" />';
		} else {
			if($admin) {
				return '<a href="user.php?t&id='.substr($line, 0, -4).'" title="'.secureOUT($string).'"><img alt="" class="brd" '.((!$small)?'width="150" height="200" ':((!empty($size))?'height="'.$size.'" ':'')).'src="'.(($valid == '1')?ADMIN_USER_DIR.substr($line, -2).'/'.substr($line, 0, -2).(($small)?'_2':'').'.jpg':'../_img/user_nopic'.(($small)?'_2':'').'.gif').'" /></a>';
			} else {
				if($small)
					return '<a href="user.php?id='.substr($line, 0, -4).'" title="'.secureOUT($string).'" target="commain"><img '.((!empty($size))?'height="'.$size.'" ':'').' src="'.(($valid == '1')?USER_DIR.substr($line, -2).'/'.substr($line, 0, -2).'_2.jpg':(($valid == '2')?'./_img/user_wait_2.gif':'./_img/user_nopic_2.gif')).'" /></a>';
				else
					return '<a href="user.php?id='.substr($line, 0, -4).'" title="'.secureOUT($string).'" target="commain"><img '.((!empty($size))?$size:'src="'.(($valid == '1')?USER_DIR.substr($line, -2).'/'.substr($line, 0, -2).'.jpg':(($valid == '2')?'./_img/user_wait.gif':'./_img/user_nopic.gif')).'" ').' /></a>';
			}
		}
	}*/
	function timeout($time = '1 HOUR') {
		return date("Y-m-d H:i:s", strtotime("-$time"));
	}
	function isOnline($date) {
		if($date > $this->timeout(UO)) return true; else return false;
	}
	/*function getalias($id, $alias, $s, $birth, $date, $extra = '') {
		global $sex;
		if($id == 'SYS')
			return '<span class="bld">SYSTEM</span>';
		elseif(!isset($alias))
			return '<span class="bld">[DELETED]</span>';
		else
			return '<a href="user.php?id='.$id.'" target="commain" class="bld '.(($this->isOnline($date))?'on':'off').'">'.secureOUT($alias).'</a> '.$sex[$s].$this->doage($birth).((!empty($_SESSION['c_i']) && $id != $_SESSION['c_i'])?'&nbsp;<a href="javascript:makeGb(\''.$id.'\''.(($extra && @$extra['gb'])?', \'&a='.$extra['gb'].'\'':'').');" title="Skriv GB-inlägg"><img alt="" src="'.OBJ.'guestbook_write.gif" /></a><a href="javascript:makeChat(\''.$id.'\');" title="Öppna privatchat"><img alt="" src="'.OBJ.'chat_write.gif" style="margin-left: 3px;" /></a>':'');
	}*/
	function getimg($arr, $valid = 1, $big = 0, $extra = '') {
		$id = substr($arr, 0, 32);
		$pd = substr($arr, 34, 2);
		$sex = substr($arr, 36, 1);
		$arr = substr($arr, 32, 2);//style="width: 225px; height: 300px;"
		return (strlen($id) != 32?'<a>':'<a href="'.l('user', 'view', $id).'"'.(!empty($extra['text'])?' title="'.secureOUT($extra['text']).'"':'').(!empty($extra['toparent'])?' target="_blank" onclick="if(window.opener) { window.opener.location.href = this.href; window.opener.focus(); return false; }"':'').'>').'<img alt="" src="'.($valid?UPLA.'images/'.$pd.'/'.$id.$arr.(!$big?'_2':'').'.jpg':'/_objects/u_noimg'.$sex.(!$big?'_2':'').'.gif').'" '.($big?'class="bbrd"':'class="brd" style="width: 50px; height: 67px;"').' /></a>';
	}
	function getstring($arr, $suffix = '', $extra = '') {
		global $sex;
		if($arr['id_id'] == 'SYS')
			return '<span class="bld">SYSTEM</span>';
		elseif(empty($arr['u_alias']))
			return '<span class="bld">[DELETED]</span>'; //($this->isOnline($arr['account_date'])
		else
			return (empty($extra['nolink'])?'<a href="'.l('user', 'view', $arr['id_id'.$suffix]).'"':'<a').' class="bld"><span class="'.($this->isOnline($arr['account_date'])?'on':'off').' bld">'.secureOUT($arr['u_alias'.$suffix]).'</span> '.$sex[$arr['u_sex'.$suffix]].$this->doage($arr['u_birth'.$suffix]).'</a>'.(!empty($extra['top'])?'<nobr><a href="javascript:makeGb(\''.$arr['id_id'].'\''.(($extra && @$extra['gb'])?', \'&a='.$extra['gb'].'\'':'').');" title="Skriv GB-inlägg"><img alt="" src="'.OBJ.'guestbook_write.gif" style="margin: 0 0 -2px 3px;" /></a><a href="javascript:makeChat(\''.$arr['id_id'].'\');" title="Öppna privatchat"><img alt="" src="'.OBJ.'chat_write.gif" style="margin: 0 0 -2px 3px;" /></a></nobr>':'');




			#return '<b><a href="'.l('user', 'view', $arr['id_id'.$suffix]).'">'.secureOUT($arr['u_alias'.$suffix]).' '.$sex[$arr['u_sex'.$suffix]].$this->doage($arr['u_birth'.$suffix]).'</a></b>'.((@empty($extra['noicon']) && $arr['level_id'.$suffix] > 1)?'&nbsp;<img alt="" src="./_img/'.$arr['level_id'.$suffix].'.gif" />':'');
			#.((!empty($_SESSION['c_i']) && $arr['id_id'] != $_SESSION['c_i'] && @empty($extra['noicon']))?'&nbsp;<a target="commain" href="javascript:makeGb(\''.$arr['id_id'].'\''.(($extra && @$extra['gb'])?', \'&a='.$extra['gb'].'\'':'').');" title="Skriv GB-inlägg"><img alt="" src="./_img/link_gb.gif" height="12" style="margin-bottom: -1px;" /></a><a target="commain" href="javascript:makeChat(\''.$arr['id_id'].'\');" title="Öppna privatchat"><img alt="" src="./_img/link_cha.gif" height="12" style="margin: 0 0 -1px 3px;" /></a>':'');
	}
	function doage($birth, $on = true) {
		if(!empty($birth)) {
			$today = explode('-', date("Y-m-d"));
			$birth = explode('-', $birth);
			$age = $today[0] - $birth[0];
			if($birth[1] > $today[1])
				$age--;
			else if($birth[1] == $today[1] && $birth[2] == $today[2]) {
				//birthday
				if($on) $age .= '';
			} else if($birth[1] == $today[1] && $birth[2] > $today[2])
				$age--;
			return $age;
		} else return 'X';
	}
	function dobirth($birth) {
		$today = explode('-', date("Y-m-d"));
		$birth = explode('-', $birth);
		if($birth[1] == $today[1] && $birth[2] == $today[2]) return 1; else return 0;
	}
	/*
	function gotimage($state) {
		return (!$state || $state == '3')?false:true;
	}
	function gotrights($user, $level, $item) {
		return ($user == $item)?true:false;
	}
	function getbtn($submit = false, $location = '', $class = '4', $name = '', $type = '200x500xMSG', $size = '', $real_name = '') {
		$type = explode('x', $type);
		return '<table cellspacing="0"><tr><td class="btn'.$class.'_l"></td><td><input type="'.(($submit == '1')?'submit':'button').'" '.((!$submit)?'onclick="document.location.href = \''.$location.'\';" ':(($submit == '2')?'onclick="makePop(\''.$location.'\', \''.$type[2].'\', '.$type[0].', '.$type[1].');" ':'')).'value="'.$name.'" class="b'.$class.'" name="'.$real_name.'" '.((!empty($size))?'style="width: '.$size.'px;" ':'').'tabindex="10"></td><td class="btn'.$class.'_r"></td></tr></table>';
	}*/
	/*function getsex($sex) {
		$sex = ($sex == 'M')?'k':'t';
		return $sex;
	}*/
	/*function getage($birth) {
		if(!empty($birth)) {
			$today = explode('-', date("Y-m-d"));
			$birth = explode('-', $birth);
			$age = $today[0] - $birth[0];
			if($birth[1] > $today[1])
				$age--;
			else if($birth[1] == $today[1] && $birth[2] == $today[2]) {
				#if(!$off) $age .= '<img src="'.$prefix.'_img/link_birth.gif" height="12" style="margin: 0 0 -2px 4px;" alt="Fyller år idag!">';
			} else if($birth[1] == $today[1] && $birth[2] > $today[2])
				$age--;
			return $age;
		} else return '?';
	}*/
	/*function getactions($id, $own = false, $s = 0, $l = 50) {
	 	if($own) {
			return $this->sql->query("SELECT r.main_id, o.main_id as obj_id, o.content_type, o.content, o.content_more, o.obj_date, o.state_id, o.section_id, o.sender_id, u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picid, u.u_picvalid, u.account_date FROM {$this->t}objrel r LEFT JOIN {$this->t}obj o ON o.main_id = r.object_id LEFT JOIN {$this->t}user u ON u.id_id = o.sender_id WHERE r.content_type = 'MESS' AND r.owner_id = '".secureINS($id)."' ORDER BY r.main_id DESC".(($l)?" LIMIT $s, $l":''), 0, 1);
		} else {
			return $this->sql->query("SELECT o.content, o.main_id as obj_id, o.content_type, o.obj_date, o.content_more, o.state_id, o.section_id, o.sender_id, u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picid, u.u_picvalid, u.account_date FROM {$this->t}objrel r LEFT JOIN {$this->t}obj o ON o.main_id = r.object_id LEFT JOIN {$this->t}user u ON u.id_id = o.sender_id WHERE r.content_type = 'MESS' AND r.owner_id = '".secureINS($id)."' ORDER BY r.main_id DESC".(($l)?" LIMIT $s, $l":''), 0, 1);
		}
	}
	function spy($user, $id, $type, $info = '') {
		#if(!@require("./_set/mod_spy.php")) @require("../_set/mod_spy.php");
		if(file_exists("./_set/mod_spy.php")) @require("./_set/mod_spy.php"); else @require("../_set/mod_spy.php");
		if(!isset($stop) && empty($stop)) {
			if($type == 'BCT' || $type == 'PHT' || $type == 'BCA' || $type == 'PHA' || $type == 'COM') $type = 'CMT';
			if($type == 'MOV') $type = 'GAL';
			if($type == 'DTH' || $type == 'KTH') $type = 'THO';
			$this->sql->queryInsert("INSERT INTO {$this->t}userspy SET user_id = '".$user."', status_id = '1', spy_date = NOW(), msg_id = '".secureINS($info)."', link_id = '".@$url."', object_id = '$id', type_id = '$type'");
			$c = $this->getinfo($user, 'spy_count');
			if(!$c) $c = 0;
			$this->setinfo($user, 'spy_count', "'".($c+1)."'");
		}
	}
	function cleanspy($user, $id, $type) {
		$r = $this->sql->queryUpdate("UPDATE {$this->t}userspy SET status_id = '2' WHERE user_id = '".$user."' AND status_id = '1' AND object_id = '$id' AND type_id = '$type'");
		if($r) $this->setinfo($user, 'spy_count', "content - ".$r);
	}*/
	function getline($opt, $id) {
		$res = $this->sql->queryResult("SELECT $opt FROM {$this->t}user WHERE id_id = '$id' LIMIT 1");
		if(!$res) return false;
		return $res;
	}
	function getinfo($id, $opt) {
		return $this->sql->queryResult("SELECT content FROM {$this->t}obj WHERE owner_id = '$id' AND content_type = '$opt' LIMIT 1");
	}

	function setinfo($id, $opt, $val) {
		$res = $this->sql->queryLine("SELECT content, main_id FROM {$this->t}obj WHERE owner_id = '$id' AND content_type = '$opt' LIMIT 1");
		if(!$res[1]) {
			$obj = $this->sql->queryInsert("INSERT INTO {$this->t}obj SET content = $val, content_type = '$opt', owner_id = '$id', obj_date = NOW()");
			$ret = array('1', $obj);
		} else {
			$ret = array('0', $res[1]);
			$this->sql->queryUpdate("UPDATE {$this->t}obj SET content = $val, obj_date = NOW() WHERE owner_id = '$id' AND content_type = '$opt' LIMIT 1");
		}
		return $ret;
	}

	function setrel($obj, $type, $id) {
		$this->sql->queryInsert("INSERT INTO {$this->t}objrel SET content_type = '$type', object_id = '$obj', owner_id = '$id'");
	}
}
?>