<?

	define('VIP_NONE',	1);		//normal user
	define('VIP_LEVEL1', 2);
	define('VIP_LEVEL2', 3);

class user {
	var $self, $info, $id, $isAdmin;

	function __construct() {
		session_start();
	}

	//returns true if the user is logged in
	function loggedIn()
	{
		if ($_SESSION['data']['id_id']) return true;
		return false;
	}

	function auth($id) {
		if (!is_numeric($id)) return false;
		if ($this->timeout('15 MINUTES') > $_SESSION['data']['account_date']) {
			$res = now();
			$_SESSION['data']['account_date'] = $res;
			$this->sql->queryUpdate("UPDATE s_user SET account_date = '".$res."' WHERE id_id = '".secureINS($id)."' LIMIT 1");
			$this->sql->queryUpdate("UPDATE s_useronline SET account_date = '".$res."' WHERE id_id = '".secureINS($id)."' LIMIT 1");
		}

		$this->id = $id;
		$this->isAdmin = false;
		if ($_SESSION['data']['level_id'] == 10) $this->isAdmin = true;

		return $this->getsessionuser($id);
	}
	
	//kollar ifall aktuell user har tillräckligt med vip
	function vip_check($_level) {
		if (!is_numeric($_level)) return false;
		$result = $this->sql->queryLine('SELECT level_id FROM s_user WHERE id_id = '.$this->id.' LIMIT 1', 0, 1);
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

		if ($id[0]) {
			$this->setrel($id[1], 'user_head', $user);
			@$_SESSION['data']['offsets'][$type.'_offset'] = ($c+1);
		}
	}

	function obj_set($type, $rel = '', $user, $val = '') {
		$id = $this->setinfo($user, $type, $val);
		if($id[0]) $this->setrel($id[1], $rel, $user);
	}
	function counterSet($id) {
		$info = $this->getcontent($id, 'user_head');
		//die('fixme getcontent!!');
		$_SESSION['data']['offsets'] = array('gb_offset' => intval(@$info['gb_offset'][1]), 'mail_offset' => intval(@$info['mail_offset'][1]), 'forum_offset' => intval(@$info['forum_offset'][1]), 'gal_offset' => intval(@$info['gal_offset'][1]), 'blog_offset' => intval(@$info['blog_offset'][1]), 'blocked_offset' => intval(@$info['blocked_offset'][1]), 'rel_offset' => intval(@$info['rel_offset'][1]));
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

	function setRelCount($uid)
	{
		global $db;
		if (!is_numeric($uid)) return false;

		$q = 'SELECT COUNT(*) FROM s_userrelquest a INNER JOIN s_user u ON u.id_id = a.sender_id AND u.status_id = "1" WHERE a.user_id = '.$uid.' AND a.status_id = "0"';
		$rel_c = $db->getOneItem($q);
		$id = $this->setinfo($uid, 'rel_count', $rel_c);
		if ($id[0]) $this->setrel($id[1], 'user_retrieve', $uid);
	}

	function get_cache() {
		$arr = $this->sql->queryLine("SELECT u_picd, u_picid, u_picvalid FROM s_user WHERE id_id = '".$this->id."' LIMIT 1");
		if(@$_SESSION['data']['u_picd'] != $arr[0]) @$_SESSION['data']['u_picd'] = $arr[0];
		if(@$_SESSION['data']['u_picid'] != $arr[1]) @$_SESSION['data']['u_picid'] = $arr[1];
		if(@$_SESSION['data']['u_picvalid'] != $arr[0]) @$_SESSION['data']['u_picvalid'] = $arr[2];
		$_SESSION['data']['cachestr'] = $this->cachestr();
	}
	function fix_img() {
		$arr = $this->sql->queryLine("SELECT u_picd, u_picid, u_picvalid FROM s_user WHERE id_id = '".$this->id."' LIMIT 1");
		if(@$_SESSION['data']['u_picd'] != $arr[0]) @$_SESSION['data']['u_picd'] = $arr[0];
		if(@$_SESSION['data']['u_picid'] != $arr[1]) @$_SESSION['data']['u_picid'] = $arr[1];
		if(@$_SESSION['data']['u_picvalid'] != $arr[0]) @$_SESSION['data']['u_picvalid'] = $arr[2];
	}

	function cachestr($id = 0)
	{
		global $db, $sex;
		if (!is_numeric($id)) return false;

		$translater = array('m' => 'm', 'c' => 'c', 'g' => 'g', 'r' => 'v', 's' => 'l');

		if (!$id) $id = $this->id;
		$info = $this->getcontent($id, 'user_retrieve');

		$str = '';
		foreach ($info as $item) {
			$str .= @($item[0]?$translater[substr($item[0], 0, 1)].':'.$item[1].'#':'');
		}
		$cha_c = $db->getOneItem('SELECT COUNT(DISTINCT(sender_id)) FROM s_userchat WHERE user_id = '.$id.' AND user_read = "0"');
		if ($cha_c) {
			$cha_id = $db->getOneItem('SELECT c.sender_id FROM s_userchat AS c INNER JOIN s_user u ON u.id_id = c.sender_id AND u.status_id = "1" WHERE c.user_id = '.$id.' AND c.user_read = "0" ORDER BY c.sent_date ASC LIMIT 1');
		} else {
			$cha_id = 0;
		}

		if ($cha_id) {
			$str .= 'c:'.$cha_c.':'.$cha_id;
		} else {
			$str .= 'c:0:0';
		}
		$rel_onl = $db->getArray('SELECT rel.friend_id, u.u_alias, u.u_sex, u.u_birth, u.level_id  FROM s_userrelation rel INNER JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = "1" WHERE rel.user_id = '.$id.' AND u.account_date > '.$this->timeout(UO).' ORDER BY u.u_alias');
		$rel_s = '';
		foreach($rel_onl as $row) {
			$rel_s .= $row[0].'|'.rawurlencode($row[1]).'|'.$sex[$row[2]].$this->doage($row[3], 0).'|'.$this->dobirth($row[3]).';'; //$row[6].$len.rawurlencode($row[1]).$sex[$row[2]].$user->doage($row[3], 0).$user->dobirth($row[3]).';';
		}
		if(empty($rel_s)) $rel_s = ';';
		$str .= '#f:'.$rel_s;
		return $str;
	}

	function isuser($id, $status = '1') {
		return ($this->sql->queryResult("SELECT status_id FROM s_user WHERE id_id = '".secureINS($id)."' LIMIT 1") == $status)?true:false;
	}
	function level($level, $allowed = '10') {
		if(intval($level) >= intval($allowed)) return true; else return false;
	}

	function getuser($id, $more = '')
	{
		global $db;
		if (!is_numeric($id)) return false;

		if (@$_SESSION['c_i'] == $id) return $this->getsessionuser($id);

		$q = 'SELECT status_id, id_id, u_alias, u_sex, u_picid, u_picd, u_picvalid, u_birth, level_id, account_date, u_pstlan_id, CONCAT(u_pstort, ", ", u_pstlan) as u_pst, lastlog_date, lastonl_date, u_regdate, beta '.$more.' FROM s_user '.
					'WHERE id_id = '.$id.' LIMIT 1';
		$return = $db->getOneRow($q);
		return ($return && $return['status_id'] == '1') ? $return : false;
	}

	function getsessionuser($id) {
		return @$_SESSION['data'];
	}
	function getuserfill($arr, $line = '*') {
		if($line != '*') $line = substr($line, 2);
		$return = $this->sql->queryLine("SELECT $line FROM s_user WHERE id_id = '".secureINS($arr['id_id'])."' LIMIT 1", 1);
		return ($return)?array_merge($arr, $return):$arr;
	}
	function getuserfillfrominfo($arr, $line = '*') {
		if($line != '*') $line = substr($line, 2);
		$return = $this->sql->queryLine("SELECT $line FROM s_userinfo WHERE id_id = '".secureINS($arr['id_id'])."' LIMIT 1", 1);
		return ($return)?array_merge($arr, $return):$arr;
	}
	function info($id, $level = '1') {
		switch($level) {
		case '1':
			return $this->sql->queryAssoc("SELECT u_email, u_pstnr, u_subscr, u_fname, u_sname, u_street, u_cell FROM s_user WHERE id_id = '".secureINS($id)."' LIMIT 1");
		break;
		}
	}
	function blocked($uid, $type = 1) {
		$isBlocked = $this->sql->queryResult("SELECT rel_id FROM s_userblock WHERE user_id = '".secureINS($this->id)."' AND friend_id = '".secureINS($uid)."' LIMIT 1");
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
		global $isAdmin;
		if($id == $this->id) return true;
		if($noadmin)
			return ($this->sql->queryResult("SELECT rel_id FROM s_userrelation WHERE user_id = '".secureINS($this->id)."' AND friend_id = '".secureINS($id)."' LIMIT 1"))?true:false;
		else
			return ($isAdmin || $this->sql->queryResult("SELECT rel_id FROM s_userrelation WHERE user_id = '".secureINS($this->id)."' AND friend_id = '".secureINS($id)."' LIMIT 1"))?true:false;
	}
	function tagline($str) {
		return secureOUT(ucwords(strtolower($str)));
	}

	function getcontent($id, $type)
	{
		global $db;
		if (!is_numeric($id)) return false;

		$q = 'SELECT o.content_type, o.content, o.main_id FROM s_objrel r '.
				'LEFT JOIN s_obj o ON o.main_id = r.object_id WHERE r.content_type = "'.$db->escape($type).'" AND r.owner_id = '.$id;

		return $db->getArray($q);
	}

	function timeout($time = '1 HOUR') {
		return date("Y-m-d H:i:s", strtotime("-$time"));
	}
	function isOnline($date) {
		if($date > $this->timeout(UO)) return true; else return false;
	}

	function getimg($arr, $valid = 1, $big = 0, $extra = '') {
		$id = substr($arr, 0, -5);
		$pd = substr($arr, -3, -1);
		$sex = substr($arr, -1);
		$arr = substr($arr, -5, -3);//style="width: 225px; height: 300px;"
		
		$x = '<a href="'.l('user', 'view', $id).'"'.(!empty($extra['text'])?' title="'.$extra['text'].'"':'').(!empty($extra['toparent'])?' target="_blank" onclick="if(window.opener) { window.opener.location.href = this.href; window.opener.focus(); return false; }"':'').'>';
					
		$x .= '<img  alt="'.(!empty($extra['text'])?$extra['text']:'').'" src="'.($valid?UPLA.'images/'.$pd.'/'.$id.$arr.(!$big?'_2':'').'.jpg':'/_objects/u_noimg'.$sex.(!$big?'_2':'').'.gif').'" '.($big?'class="bbrd" style="width: 150px; height: 150px;"':'class="brd" style="width: 50px; height: 50px;"').' /></a>';

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
				$this->sql->queryUpdate("UPDATE s_user SET account_date = '$res' WHERE id_id = '".secureINS($arr['id_id'])."' LIMIT 1");
				$this->sql->queryUpdate("UPDATE s_useronline SET account_date = '$res' WHERE id_id = '".secureINS($arr['id_id'])."' LIMIT 1");
				$arr['account_date'] = $res;
			}
		}
		if (@$arr['id_id'] == 'SYS') {
			return '<span class="bld">SYSTEM</span>';
		}
		
		if (empty($arr['u_alias'])) {
			return '<span class="bld">[BORTTAGEN]</span>'; //($this->isOnline($arr['account_date'])
		}

		$result  = (empty($extra['nolink'])?'<a href="'.l('user', 'view', $arr['id_id'.$suffix]).'">':'');
		
		if (!empty($arr['account_date'.$suffix])) {
			$curr_class = ($this->isOnline($arr['account_date'.$suffix])?'on':'off').'_'.$sex_name[$arr['u_sex'.$suffix]];
		} else {
			$curr_class = 'off_'.@$sex_name[$arr['u_sex'.$suffix]];
		}
		
		$result .= '<span class="'.$curr_class.'"'.(!isset($extra['noimg'])?' onmouseover="launchHover(event, \''.$arr['id_id'].'\');" onmouseout="clearHover();"':'').'>'.secureOUT($arr['u_alias'.$suffix]);
		$result .= (empty($extra['nosex'])?' <img alt="'.@$sex_name[$arr['u_sex'.$suffix]].'" align="absmiddle" src="/_objects/icon_'.$arr['u_sex'.$suffix].'1.png" />':'');
		$result .= (empty($extra['noage'])?$this->doage($arr['u_birth'.$suffix]):'');
		$result .= (empty($extra['nolink'])?'</a>':'');
		$result .= '</span>';
		return $result;
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

	function spy($user, $id, $type, $info = '') {
		$this->sql->queryInsert("INSERT INTO s_usermail SET
		user_id = '".$user."',
		sender_id = 'SYS',
		status_id = '1',
		sender_status = '2',
		user_read = '0',
		sent_cmt = '".secureINS($id)."',
		sent_ttl = '".secureINS('test')."',
		sent_date = NOW()");
		$this->counterIncrease('mail', $user);
		$this->notifyIncrease('mail', $user);
	}
	function getline($opt, $id) {
		$res = $this->sql->queryResult("SELECT $opt FROM s_user WHERE id_id = '$id' LIMIT 1");
		if(!$res) return false;
		return $res;
	}

	function getinfo($id, $opt)
	{
		global $db;
		if (!is_numeric($id) || !is_numeric($opt)) return false;

		return $db->getOneItem('SELECT content FROM s_obj WHERE owner_id = '.$id.' AND content_type = '.$opt.' LIMIT 1');
	}

	function setinfo($id, $opt, $val)
	{
		global $db;
		if (!is_numeric($id) || !is_numeric($opt)) return false;

		$res = $db->getOneRow('SELECT content, main_id FROM s_obj WHERE owner_id = '.$id.' AND content_type = '.$opt.' LIMIT 1');
		if (!$res['main_id']) {
			$q = 'INSERT INTO s_obj SET content = "'.$db->escape($val).'", content_type = '.$opt.', owner_id = '.$id.', obj_date = NOW()';
			$obj = $db->insert($q);
			$ret = array('1', $obj);
		} else {
			$q = 'UPDATE s_obj SET content = "'.$db->escape($val).'", obj_date = NOW() WHERE owner_id = '.$id.' AND content_type = '.$opt.' LIMIT 1';
			$db->update($q);
			$ret = array('0', $res['main_id']);
		}
		return $ret;
	}

	function setrel($obj, $type, $id) {
		$this->sql->queryInsert("INSERT INTO s_objrel SET obj_date = NOW(), content_type = '$type', object_id = '$obj', owner_id = '$id'");
	}
}



//av martin, för å kolla någons vip-level
function get_vip($_userid) {
	global $sql;
	if (!is_numeric($_userid)) return false;
	$result = $sql->queryResult('SELECT level_id FROM s_user WHERE id_id = '.$_userid.' LIMIT 1');
	if ($result > 1) return $result;
	return false;
}

//av martin. används häråvar
function addVIP($user_id, $vip_level, $days)
{
	global $sql;
		
	if (!is_numeric($user_id) || !is_numeric($vip_level) || !is_numeric($days)) return false;

	$q = 'SELECT userId FROM s_vip WHERE userId='.$user_id.' AND level='.$vip_level;

	if ($sql->queryLine($q)) {
		$q = 'UPDATE s_vip SET days=days+'.$days.',timeSet=NOW() WHERE userId='.$user_id.' AND level='.$vip_level;
		$sql->queryUpdate($q);
	} else {
		$q = 'INSERT INTO s_vip SET userId='.$user_id.',level='.$vip_level.',days='.$days.',timeSet=NOW()';
		$sql->queryUpdate($q);
	}
}

function getCurrentVIPLevel($_id)
{
	global $sql;
	if (!is_numeric($_id)) return false;

	$q = 'SELECT level FROM s_vip WHERE userId='.$_id.' ORDER BY level DESC LIMIT 1';
	$level = $sql->queryResult($q, 0, 1);

	if (!$level) return 1;		//1=normal user
	return $level;
}

function getVIPLevels($_id)
{
	global $sql;
	if (!is_numeric($_id)) return false;

	$q = 'SELECT * FROM s_vip WHERE userId='.$_id.' ORDER BY level DESC';
	return $sql->query($q, 0, 1);
}
	
?>
