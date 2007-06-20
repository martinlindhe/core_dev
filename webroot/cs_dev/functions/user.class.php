<?

	define('VIP_NONE',	1);		//normal user
	define('VIP_LEVEL1', 2);
	define('VIP_LEVEL2', 3);

class user {
	var $self, $info, $id;

	function auth($id) {
		global $db;
		if (!is_numeric($id)) return false;
		if ($this->timeout('15 MINUTES') > @$_SESSION['data']['account_date']) {
			$res = now();
			$_SESSION['data']['account_date'] = $res;
			$db->update('UPDATE s_user SET account_date = "'.$res.'" WHERE id_id = '.$id.' LIMIT 1');
			$db->update('UPDATE s_useronline SET account_date = "'.$res.'" WHERE id_id = '.$id.' LIMIT 1');
		}
		$this->id = $id;
		return $this->getsessionuser($id);
	}
	
	//kollar ifall aktuell user har tillräckligt med vip
	function vip_check($_level) {
		global $db;
		if (!is_numeric($_level)) return false;
		$result = $db->getOneItem('SELECT level_id FROM s_user WHERE id_id = '.$this->id.' LIMIT 1');
		if ($result[0] >= $_level) return true;
		return false;
	}

	function update_retrieve() {
		$info = $this->cachestr();
		$_SESSION['data']['cachestr'] = $info;
	}
	function counterIncrease($type, $user) {
		$c = $this->getinfo($user, $type.'_offset');
		if(!$c) $c = 0;
		$id = $this->setinfo($user, $type.'_offset', ($c+1));
		if($id[0]) $this->setrel($id[1], 'user_head', $user);
		@$_SESSION['data']['offsets'][$type.'_offset'] = ($c+1);
	}
	function obj_set($type, $rel = '', $user, $val = '') {
		$id = $this->setinfo($user, $type, $val);
		if($id[0]) $this->setrel($id[1], $rel, $user);
	}
	function counterSet($id) {
		$info = $this->getcontent($id, 'user_head');
		foreach ($info as $row) {
			if ($row['content_type']) {
				$_SESSION['data']['offsets'][ $row['content_type'] ] = $row['content'];
				//$_SESSION['data']['offsets'] = array('gb_offset' => intval(@$info['gb_offset'][1]), 'mail_offset' => intval(@$info['mail_offset'][1]), 'forum_offset' => intval(@$info['forum_offset'][1]), 'gal_offset' => intval(@$info['gal_offset'][1]), 'blog_offset' => intval(@$info['blog_offset'][1]), 'blocked_offset' => intval(@$info['blocked_offset'][1]), 'rel_offset' => intval(@$info['rel_offset'][1]));
			}
		}
	}
	function counterDecrease($type, $user) {
		$c = $this->getinfo($user, $type.'_offset');
		if(!$c || $c <= 0) $c = 1;
		$id = $this->setinfo($user, $type.'_offset', ($c-1));
		if($id[0]) $this->setrel($id[1], 'user_head', $user);
		@$_SESSION['data']['offsets'][$type.'_offset'] = ($c-1);
	}
	function notifyReset($type, $user) {
		$id = $this->setinfo($user, $type.'_count', '0');
		if($id[0]) $this->setrel($id[1], 'user_head', $user);
		if($user == $this->id) $this->update_retrieve();
	}
	function notifyIncrease($type, $user) {
		$c = $this->getinfo($user, $type.'_count');
		if(!$c) $c = 0;
		$id = $this->setinfo($user, $type.'_count', ($c+1));
		if($id[0]) $this->setrel($id[1], 'user_retrieve', $user);
		if($user == $this->id) $this->update_retrieve();
	}
	function notifyDecrease($type, $user) {
		$c = $this->getinfo($user, $type.'_count');
		if(!$c || $c <= 0) $c = 1;
		$id = $this->setinfo($user, $type.'_count', ($c-1));
		if($id[0]) $this->setrel($id[1], 'user_retrieve', $user);
		if($user == $this->id) $this->update_retrieve();
	}
	function setRelCount($uid) {
		global $db;
		if (!is_numeric($uid)) return false;
		$rel_c = $db->getOneItem('SELECT COUNT(*) FROM s_userrelquest a INNER JOIN s_user u ON u.id_id = a.sender_id AND u.status_id = "1" WHERE a.user_id = '.$uid.' AND a.status_id = "0"');
		$id = $this->setinfo($uid, 'rel_count', $rel_c);
		if($id[0]) $this->setrel($id[1], 'user_retrieve', $uid);
	}
	function get_cache() {
		global $db;
		$arr = $db->getOneRow('SELECT u_picd, u_picid, u_picvalid FROM s_user WHERE id_id = '.$this->id.' LIMIT 1');
		if(@$_SESSION['data']['u_picd'] != $arr[0]) @$_SESSION['data']['u_picd'] = $arr[0];
		if(@$_SESSION['data']['u_picid'] != $arr[1]) @$_SESSION['data']['u_picid'] = $arr[1];
		if(@$_SESSION['data']['u_picvalid'] != $arr[0]) @$_SESSION['data']['u_picvalid'] = $arr[2];
		$_SESSION['data']['cachestr'] = $this->cachestr();
	}
	function fix_img() {
		global $db;
		$arr = $db->getOneRow('SELECT u_picd, u_picid, u_picvalid FROM s_user WHERE id_id = '.$this->id.' LIMIT 1');
		if(@$_SESSION['data']['u_picd'] != $arr['u_picd']) @$_SESSION['data']['u_picd'] = $arr['u_picd'];
		if(@$_SESSION['data']['u_picid'] != $arr['u_picid']) @$_SESSION['data']['u_picid'] = $arr['u_picid'];
		if(@$_SESSION['data']['u_picvalid'] != $arr['u_picd']) @$_SESSION['data']['u_picvalid'] = $arr['u_picvalid'];
	}
	function cachestr($id = '') {
		global $db, $sex;
		$translater = array('m' => 'm', 'c' => 'c', 'g' => 'g', 'r' => 'v', 's' => 'l');
		if(empty($id)) $id = $this->id;
		
		if (!is_numeric($id)) die('urbad');
		
		$info = $this->getcontent($id, 'user_retrieve');
		$str = '';
		foreach($info as $item) {
			$str .= @($item[0]?$translater[substr($item[0], 0, 1)].':'.$item[1].'#':'');
		}
		$cha_c = $db->getOneItem('SELECT COUNT(DISTINCT(sender_id)) FROM s_userchat WHERE user_id = '.$id.' AND user_read = "0"');
		if($cha_c > 0)
			$cha_id = $db->getOneItem('SELECT c.sender_id FROM s_userchat c INNER JOIN s_user u ON u.id_id = c.sender_id AND u.status_id = "1" WHERE c.user_id = '.$id.' AND c.user_read = "0" ORDER BY c.sent_date ASC LIMIT 1');
		else $cha_id = '';
		if($cha_id) {
			$str .= 'c:'.$cha_c.':'.$cha_id;
		} else {
			$str .= 'c:0:0';
		}
		$rel_onl = $db->getArray('SELECT rel.friend_id, u.u_alias, u.u_sex, u.u_birth, u.level_id  FROM s_userrelation rel INNER JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = "1" WHERE rel.user_id = '.$id.' AND u.account_date > "'.$this->timeout(UO).'" ORDER BY u.u_alias');
		$rel_s = '';
		foreach($rel_onl as $row) {
			$rel_s .= $row[0].'|'.rawurlencode($row[1]).'|'.$sex[$row[2]].$this->doage($row[3], 0).'|'.$this->dobirth($row[3]).';'; //$row[6].$len.rawurlencode($row[1]).$sex[$row[2]].$user->doage($row[3], 0).$user->dobirth($row[3]).';';
		}
		if(empty($rel_s)) $rel_s = ';';
		$str .= '#f:'.$rel_s;
		return $str;
	}
	function isuser($id, $status = '1') {
		global $db;
		if (!is_numeric($id)) return false;

		$q = 'SELECT status_id FROM s_user WHERE id_id = '.$id.' LIMIT 1';

		if ($db->getOneItem($q) == $status) return true;
		return false;
	}
	function level($level, $allowed = '10') {
		if (intval($level) >= intval($allowed)) return true;
		return false;
	}
	function getuser($id, $more = '') {
		global $db;
		if (!is_numeric($id)) return false;
		if(@$_SESSION['c_i'] == $id) return $this->getsessionuser($id);

		$return = $db->getOneRow('SELECT status_id, id_id, u_alias, u_sex, u_picid, u_picd, u_picvalid, u_birth, level_id, account_date, u_pstlan_id, CONCAT(u_pstort, ", ", u_pstlan) as u_pst, lastlog_date, lastonl_date, u_regdate, beta '.$more.' FROM s_user WHERE id_id = '.$id.' LIMIT 1');
		return ($return && $return['status_id'] == '1') ? $return : false;
	}
	function getsessionuser($id) {
		return @$_SESSION['data'];
	}
	function getuserfill($arr, $line = '*') {
		if ($line != '*') $line = substr($line, 2);
		$return = $db->getOneRow('SELECT '.$line.' FROM s_user WHERE id_id = '.$arr['id_id'].' LIMIT 1');
		return ($return) ? array_merge($arr, $return) : $arr;
	}
	function getuserfillfrominfo($arr, $line = '*') {
		if($line != '*') $line = substr($line, 2);
		$return = $db->getOneRow('SELECT '.$line.' FROM s_userinfo WHERE id_id = '.$arr['id_id'].' LIMIT 1');
		return ($return)?array_merge($arr, $return):$arr;
	}
	function info($id, $level = '1') {
		global $db;
		switch($level) {
		case '1':
			return $db->getOneRow('SELECT u_email, u_pstnr, u_subscr, u_fname, u_sname, u_street, u_cell FROM s_user WHERE id_id = '.$id.' LIMIT 1');
		break;
		}
	}
	function blocked($uid, $type = 1) {
		global $db;
		if (!is_numeric($uid)) return false;
		$isBlocked = $db->getOneItem('SELECT rel_id FROM s_userblock WHERE user_id = '.$this->id.' AND friend_id = '.$uid.' LIMIT 1');
		
		if($isBlocked) {
			if($isBlocked == 'u') {
				if($type == 1)
					errorACT('Du har blockerat personen.', l('user', 'view', $this->id));
				elseif($type == 2)
					popupACT('Du har blockerat personen.');
				elseif($type == 3)
					return true;
			} else {
				if($type == 1)
					errorACT('Du är blockerad.', l('user', 'view', $this->id)); 
				elseif($type == 2)
					popupACT('Du är blockerad.');
				elseif($type == 3)
					return true;
			}
		}
		if($type == 3) return false;
	}
	function isFriends($id, $noadmin = 0) {
		global $db;
		if (!is_numeric($id)) return false;
		if ($id == $this->id) return true;

		return ($db->getOneItem('SELECT rel_id FROM s_userrelation WHERE user_id = '.$this->id.' AND friend_id = '.$id.' LIMIT 1'))?true:false;
	}
	function tagline($str) {
		return secureOUT(ucwords(strtolower($str)));
	}
	function getcontent($id, $type) {
		global $db;
		if (!is_numeric($id)) return false;
		return $db->getArray('SELECT o.content_type, o.content, o.main_id FROM s_objrel r LEFT JOIN s_obj o ON o.main_id = r.object_id WHERE r.content_type = "'.$db->escape($type).'" AND r.owner_id = '.$id);
	}
	//makeover
	/*function getphoto($line, $valid = false, $small = 0, $admin = false, $string = '', $size = '') {
		if(empty($line)) {
			return '<img alt=""'.((!$small)?'':((!empty($size))?'height="'.$size.'" ':'')).'src="'.(($admin)?'.':'').'./_img/user_deleted'.(($small)?'_2':'').'.gif" />';
		} elseif(substr($line, 0, 3) == 'SYS') {
			return '<img alt=""'.((!$small)?'':((!empty($size))?'height="'.$size.'" ':'')).'src="'.(($admin)?'.':'').'./_img/SYS'.(($small)?'_2':'').'.jpg" />';
		} else {
			if($admin) {
				return '<a href="user.php?t&id='.substr($line, 0, -4).'" title="'.secureOUT($string).'"><img alt="" class="brd" '.((!$small)?'width="150" height="150" ':((!empty($size))?'height="'.$size.'" ':'')).'src="'.(($valid == '1')?ADMIN_USER_DIR.substr($line, -2).'/'.substr($line, 0, -2).(($small)?'_2':'').'.jpg':'../_img/user_nopic'.(($small)?'_2':'').'.gif').'" /></a>';
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
		$id = substr($arr, 0, -5);
		$pd = substr($arr, -3, -1);
		$sex = substr($arr, -1);
		$arr = substr($arr, -5, -3);//style="width: 225px; height: 300px;"
		
		//$x = (!$id?'<a>':'<a href="'.l('user', 'view', $id).'"'.(!empty($extra['text'])?' title="'.secureOUT($extra['text']).'"':'').(!empty($extra['toparent'])?' target="_blank" onclick="if(window.opener) { window.opener.location.href = this.href; window.opener.focus(); return false; }"':'').'>').'<img  alt="'.(!empty($extra['text'])?secureOUT($extra['text']):'').'" src="'.($valid?UPLA.'images/'.$pd.'/'.$id.$arr.(!$big?'_2':'').'.jpg':'/_objects/u_noimg'.$sex.(!$big?'_2':'').'.gif').'" '.($big?'class="bbrd" style="width: 150px; height: 150px;"':'class="brd" style="width: 50px; height: 50px;"').' /></a>';
		
		$x = '<a href="'.l('user', 'view', $id).'"'.(!empty($extra['text'])?' title="'.secureOUT($extra['text']).'"':'').(!empty($extra['toparent'])?' target="_blank" onclick="if(window.opener) { window.opener.location.href = this.href; window.opener.focus(); return false; }"':'').'>';
					
		$x .= '<img  alt="'.(!empty($extra['text'])?secureOUT($extra['text']):'').'" src="'.($valid?UPLA.'images/'.$pd.'/'.$id.$arr.(!$big?'_2':'').'.jpg':'/_objects/u_noimg'.$sex.(!$big?'_2':'').'.gif').'" '.($big?'class="bbrd" style="width: 150px; height: 150px;"':'class="brd" style="width: 50px; height: 50px;"').' /></a>';
		

		return $x;
	}

	function getministring($arr) {
		global $sex_name;
		return $arr['u_alias'].', '.$sex_name[$arr['u_sex']].' '.$this->doage($arr['u_birth']).'år';
	}

	function getstring($arr, $suffix = '', $extra = '') {
		global $sex_name;
		if (!is_array($arr) && is_numeric($arr)) {
			$arr = $this->getuser($arr);
		}
		if (@$arr['id_id'] == @$_SESSION['data']['id_id']) {
			if(empty($arr['account_date']) || !$this->isOnline($arr['account_date'])) {
				$res = now();
				$_SESSION['data']['account_date'] = $res;
				$db->update('UPDATE s_user SET account_date = "'.$res.'" WHERE id_id = '.$arr['id_id'].' LIMIT 1');
				$db->update('UPDATE s_useronline SET account_date = "'.$res.'" WHERE id_id = '.$arr['id_id'].' LIMIT 1');
				$arr['account_date'] = $res;
			}
		}
		if ($arr['id_id'] == 'SYS') {
			return '<span class="bld">SYSTEM</span>';
		}
		
		if (empty($arr['u_alias'])) {
			return '<span class="bld">[BORTTAGEN]</span>'; //($this->isOnline($arr['account_date'])
		}

		//$own = ($arr['id_id'] == $this->id?true:false);
	  //$mail_add = (!$own)?'<a href="javascript:makeMail(\''.$arr['id_id'].'\');" title="Skriv mail"><img alt="" src="'.OBJ.'mail_write.gif" style="margin: 0 0 -2px 3px;" /></a>':'';
		$result  = (empty($extra['nolink'])?'<a href="'.l('user', 'view', $arr['id_id'.$suffix]).'">':'');
		
		if (!empty($arr['account_date'.$suffix])) {
			$curr_class = ($this->isOnline($arr['account_date'.$suffix])?'on':'off').'_'.$sex_name[$arr['u_sex'.$suffix]];
		} else {
			$curr_class = 'off_'.@$sex_name[$arr['u_sex'.$suffix]];
		}
		
		$result .= '<span class="'.$curr_class.'"'.(!isset($extra['noimg'])?' onmouseover="launchHover(event, \''.$arr['id_id'].'\');" onmouseout="clearHover();"':'').'>'.secureOUT($arr['u_alias'.$suffix]);
		$result .= (empty($extra['nosex'])?' <img alt="'.@$sex_name[$arr['u_sex'.$suffix]].'" align="absmiddle" src="'.OBJ.'icon_'.$arr['u_sex'.$suffix].'1.png" />':'');
		$result .= (empty($extra['noage'])?$this->doage($arr['u_birth'.$suffix]):'');
		$result .= (empty($extra['nolink'])?'</a>':'');
		$result .= '</span>';
		return $result;
		//(!empty($extra['top']) && !$own?'<a href="javascript:makeGb(\''.$arr['id_id'].'\''.(($extra && @$extra['gb'])?', \'&amp;a='.$extra['gb'].'\'':'').');" title="Skriv GB-inlägg"><img alt="Skriv GB-inlägg" src="'.OBJ.'guestbook_write.gif" style="margin: 0 0 -2px 3px;" /></a><a href="javascript:makeChat(\''.$arr['id_id'].'\');"><img alt="Öppna privatchat" src="'.OBJ.'chat_write.gif" style="margin: 0 0 -2px 3px;" /></a>'.$mail_add:'').'</span>';
	}

	/* By Martin: Returns clickable username, age & gender, suited for mobile display */
	function getstringMobile($user_id, $suffix = '', $extra = '') {
		global $sex_name;
		if (!$user_id) return 'SYSTEM';

		$own = ($user_id == $this->id?true:false);
		
		$user = $this->getuser($user_id);
		if (!$user) return 'ANVÄNDAREN BORTTAGEN';

		$online = $this->isOnline($user['lastonl_date']);
		
		$out = '<a class="'.($online?'user_online':'user_offline').'" href="user.php?id='.$user_id.'">'.$user['u_alias'].'</a>';
		$out .= ' <img alt="'.$sex_name[$user['u_sex']].'" src="gfx/icon_'.$user['u_sex'].'.png" border="0"/>'.$this->doage($user['u_birth']);
		
		return $out;
	}

	function doagegroup($age) {
		if($age <= 20) return 1;
		elseif($age <= 25) return 2;
		elseif($age <= 30) return 3;
		elseif($age <= 35) return 4;
		elseif($age <= 40) return 5;
		elseif($age <= 45) return 6;
		elseif($age <= 50) return 7;
		elseif($age <= 55) return 8;
		else return 9;
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
	function spy($user, $id, $type, $info = '') {
/*
		if(!isset($stop) && empty($stop)) {
			if($type == 'BCT' || $type == 'PHT' || $type == 'BCA' || $type == 'PHA' || $type == 'COM') $type = 'CMT';
			if($type == 'MOV') $type = 'GAL';
			if($type == 'DTH' || $type == 'KTH') $type = 'THO';
			$db->insert("INSERT INTO s_userspy SET user_id = '".$user."', status_id = '1', spy_date = NOW(), msg_id = '".$info."', link_id = '".@$url."', object_id = '$id', type_id = '$type'");
			$c = $this->getinfo($user, 'spy_count');
			if(!$c) $c = 0;
			$this->setinfo($user, 'spy_count', ($c+1));
		}
*/
		global $db;

		$db->insert("INSERT INTO s_usermail SET
		user_id = '".$user."',
		sender_id = 'SYS',
		status_id = '1',
		sender_status = '2',
		user_read = '0',
		sent_cmt = '".$db->escape($id)."',
		sent_ttl = '".$db->escape('test')."',
		sent_date = NOW()");
		$this->counterIncrease('mail', $user);
		$this->notifyIncrease('mail', $user);
	}
	function getline($opt, $id) {
		global $db;
		$res = $db->getOneRow('SELECT '.$opt.' FROM s_user WHERE id_id = '.$id.' LIMIT 1');
		if(!$res) return false;
		return $res;
	}
	function getinfo($id, $opt) {
		global $db;
		if (!is_numeric($id)) return false;
		return $db->getOneItem('SELECT content FROM s_obj WHERE owner_id = '.$id.' AND content_type = "'.$opt.'" LIMIT 1');
	}

	function setinfo($id, $opt, $val) {
		global $db;
		if (!is_numeric($id)) return false;
		
		$opt = $db->escape($opt);
		$val = $db->escape($val);
		
		$res = $db->getOneRow('SELECT content, main_id FROM s_obj WHERE owner_id = '.$id.' AND content_type = "'.$opt.'" LIMIT 1');
		if(!$res['main_id']) {
			$obj = $db->insert('INSERT INTO s_obj SET content = "'.$val.'", content_type = "'.$opt.'", owner_id = '.$id.', obj_date = NOW()');
			$ret = array('1', $obj);
		} else {
			$ret = array('0', $res['main_id']);
			$q = 'UPDATE s_obj SET content = "'.$val.'", obj_date = NOW() WHERE owner_id = '.$id.' AND content_type = "'.$opt.'" LIMIT 1';
			$db->update($q);
		}
		return $ret;
	}

	function setrel($obj, $type, $id) {
		global $db;
		$db->insert('INSERT INTO s_objrel SET obj_date = NOW(), content_type = "'.$db->escape($type).'", object_id = "'.$db->escape($obj).'", owner_id = '.$id);
	}
	
	function get_os_($user_agent) {
		$oses = array (
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
		'Windows 98' => '(Windows 98)|(Win98)|(Win 9x)',
		'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
		'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
		'Windows 2003' => '(Windows NT 5.2)',
		'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|(Windows-NT)',
		'Windows ME' => 'Windows ME',
		'Open BSD' => 'OpenBSD',
		'Sun OS' => 'SunOS',
		'Linux' => '(Linux)|(X11)',
		'Macintosh' => '(Mac_PowerPC)|(Macintosh)|(Mac_PPC)',
		'QNX' => 'QNX',
		'BeOS' => 'BeOS',
		'Sony Ericsson' => 'SonyEricsson',
		'OS/2' => 'OS/2',
		'Search Bot' => '(nuhk)|(Googlebot)|(Google)|(Yammybot)|(Openbot)|(psbot)|(Slurp/cat)|(msnbot)|(ia_archiver)|(Cerberian Drtrs)',
		'LG' => 'LG'
		);

		foreach($oses as $os=>$pattern)
		{
			if (eregi($pattern, $user_agent)) return $os;
		}
		return 'Unknown';
	}

	function get_browser_($user_agent) {
		$browsers = array(
		'Opera' => 'Opera',
		'Mozilla Firefox' => '(Firebird)|(Firefox)',
		'Galeon' => 'Galeon',
		'Mozilla' => 'Gecko',
		'MyIE' => 'MyIE',
		'Lynx' => 'Lynx',
		'Lotus-Notes' => 'Lotus-Notes',
		'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
		'Konqueror' => 'Konqueror',
		'Pic Bot' => 'psbot',
		'Google' => 'Google',
		'T610' => 'T610',
		'T630' => 'T630',
		'K500i' => 'K500i',
		'K700i' => 'K700i',
		'K750i' => 'K750i',
		'P800' => 'P800',
		'W800i' => 'W800i',
		'Z1010' => 'Z1010',
		'Z800' => 'Z800',
		'U8138' => 'U8138',
		'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)|(Cerberian Drtrs)',
		'Internet Explorer 7' => '(MSIE 7\.[0-9]+)|(MSIE 7)',
		'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
		'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
		'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
		'Internet Explorer' => 'MSIE'
		);

		foreach($browsers as $browser=>$pattern)
		{
			if (eregi($pattern, $user_agent)) return $browser;
		}
		return 'Unknown';
	}

	function logAdd($category = '', $unique = '', $type = 'START') {
		global $db;
		$ret = false;
		if($type != 'INDEX') {
			$cookie_id = gc();
			$q = 'INSERT INTO s_log SET sess_id = "'.$db->escape($cookie_id).'",'.
						'sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'",'.
						'category_id = "'.$db->escape($category).'",'.
						'unique_id = "'.$db->escape($unique).'",'.
						'type_inf = "'.((empty($type))?'START':$type).'",'.
						'date_cnt = NOW()';
			$db->insert($q);
			
			$q = 'INSERT INTO s_logvisit SET sess_id = "'.$db->escape($cookie_id).'",'.
						'sess_ip = "'.$db->escape($_SERVER['REMOTE_ADDR']).'",'.
						'user_string = "'.$this->get_os_($_SERVER['HTTP_USER_AGENT']).' - '.$this->get_browser_($_SERVER['HTTP_USER_AGENT']).'",'.
						'date_snl = NOW(),date_cnt = NOW()';
			$db->insert($q);
		}
		if(!empty($_SERVER['HTTP_REFERER'])) {
			$c = $db->getOneItem("SELECT type_cnt FROM s_logreferer WHERE type_referer = '".$db->escape($_SERVER['HTTP_REFERER'])."' LIMIT 1");
			if($c) {
				$db->update("UPDATE s_logreferer SET type_cnt = type_cnt + 1 WHERE type_referer = '".$db->escape($_SERVER['HTTP_REFERER'])."'");
			} else {
				$db->insert("INSERT INTO s_logreferer SET type_cnt = '1', type_referer = '".$db->escape($_SERVER['HTTP_REFERER'])."'");
			}
		}
		return $ret;
	}
}

//av martin, för å kolla någons vip-level
function get_vip($_userid) {
	global $db;
	if (!is_numeric($_userid)) return false;
	return $db->getOneItem('SELECT level_id FROM s_user WHERE id_id = '.$_userid.' LIMIT 1');
}

//av martin. används häråvar
function addVIP($user_id, $vip_level, $days)
{
	global $db;
		
	if (!is_numeric($user_id) || !is_numeric($vip_level) || !is_numeric($days)) return false;

	$q = 'SELECT userId FROM s_vip WHERE userId='.$user_id.' AND level='.$vip_level;

	if ($db->getOneItem($q)) {
		$q = 'UPDATE s_vip SET days=days+'.$days.',timeSet=NOW() WHERE userId='.$user_id.' AND level='.$vip_level;
		$db->update($q);
	} else {
		$q = 'INSERT INTO s_vip SET userId='.$user_id.',level='.$vip_level.',days='.$days.',timeSet=NOW()';
		$db->insert($q);
	}
}


	function getCurrentVIPLevel($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT level FROM s_vip WHERE userId='.$_id.' ORDER BY level DESC LIMIT 1';
		$level = $db->getOneItem($q);

		if (!$level) return 1;		//1=normal user
		return $level;
	}

	function getVIPLevels($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;
		
		$q = 'SELECT * FROM s_vip WHERE userId='.$_id.' ORDER BY level DESC';
		return $db->getArray($q);
	}
	
?>