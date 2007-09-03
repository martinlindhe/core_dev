<?
	//identisk med $user->getimg() förrutom user.php vs user_view.php
	function getadminimg($user_id, $big = 0, $text = '', $parent = false)
	{
		global $db, $config;
		
		$q = 'SELECT u_picid, u_picd, u_sex, u_picvalid FROM s_user WHERE id_id='.$user_id;
		$data = $db->getOneRow($q);

		$t = '<a href="user.php?id='.$user_id.'">';

		$target = '';
		if ($parent) $target = ' target="_parent"';

		if (!$data['u_picid'] || !$data['u_picvalid']) $t .= '<img src="'.$config['web_root'].'_gfx/u_noimg'.$data['u_sex'].(!$big?'_2':'').'.gif" ';
		else $t .= '<img src="'.$config['web_root'].UPLA.'images/'.$data['u_picd'].'/'.$user_id.$data['u_picid'].'.jpg" alt="'.secureOUT($text).'" ';

		$t .= ($big?'class="bbrd" style="width: 150px; height: 150px;"':'class="brd" style="width: 50px; height: 50px;"').' '.$parent.'/></a>';
	
		return $t;
	}

	function doLog($string = '', $about = '') {
		global $t, $sql;
		$sql->queryInsert("INSERT INTO s_admindolog SET owner_id = '".$_SESSION['u_i']."', string_info = '".$string."', about_id = '".$about."'");
	}

	function errorNEW($msg, $mv = '', $ttl = 'ERROR') {
		require('notice_apopup.php');
		die;
	}

	function makeMenuAdmin($sel, $arr, $print = true) {
		if($print) echo '<tr><td height="25">';
		$i = false;
		foreach($arr as $key => $val) {
			if(!$i) $i = true; else echo ' | ';
			echo '<a href="'.$val.'"'.((strtolower($key) == strtolower($sel))?' class="no_lnk"':'').'>'.$key.'</a>';
		}
		if($print) echo '</td></tr>';
	}

	function highlight($x, $var) {
		if ($var != "") {
			$xtemp = "";
			$i=0;
			while ($i<strlen($x)) {
				if ((($i + strlen($var)) <= strlen($x)) && (strcasecmp($var, substr($x, $i, strlen($var))) == 0)) { 
					$xtemp .= '<b style="font-size: 12px; color: green;">' . substr($x, $i , strlen($var)) . "</b>"; 
					$i += strlen($var); 
				} else {
					$xtemp .= $x{$i}; 
					$i++; 
				} 
			} 
			$x = $xtemp; 
		}
		return $x; 
	}

	function getEnumOptions($table, $field)
	{
		global $db;
		$finalResult = array();

		if (strlen(trim($table)) < 1) return false;
		$q  = 'SHOW COLUMNS FROM '.$table;
		$list = $db->getArray($q);

		foreach ($list as $row) {
			if ($field != $row["Field"]) continue;
			//check if enum type
			if (ereg('enum.(.*).', $row['Type'], $match) || ereg('set.(.*).', $row['Type'], $match)) {
				$opts = explode(',', $match[1]);
				foreach ($opts as $item)
					$finalResult[] = substr($item, 1, strlen($item)-2);
			}
			else
				return false;
		}
		return $finalResult;
	}

	function smallDate($date) {
		return stripzero(strftime("%d", strtotime($date))) . strftime(" %b %H:%m", strtotime($date));
	}

	function getPic($str) {
		global $extra_dir;

		$str = preg_replace("/\[bild=(h?t?t?p?:?\/?\/?)(.*)\](\#?[0-9]{1,8}).([a-zA-Z]{2,6})\[\/bild]/", '<a href="\\1\\2" target="_blank"><img src="'.$extra_dir.'\\3.\\4"></a>', $str);
		$str = preg_replace("/\[bild\](\#?[0-9]{1,8}).([a-zA-Z]{2,6})\[\/bild]/", '<img src="'.$extra_dir.'\\1.\\2">', $str);
		#	$str = preg_replace("/\<länk=(h?t?t?p?:?\/?\/?)(.*)\>(\#?[0-9]{1,8}).([a-zA-Z]{2,6})\<\/länk\>/is", '<a href="\\1" target="_blank"><img src="'.$extra_dir.'\\3.\\4"></a>', $str);
		#	$str = preg_replace("/\<länk>(\#?[0-9]{1,8}).([a-zA-Z]{2,6})\<\/länk>/is", '<img src="'.$extra_dir.'\\1.\\2">', $str);
		return $str;
	}

	function doPic($id) {
		return '<a href="showSingleNormal.php?id='.$id.'&l" target="bunnymain"><img src="showSingleNormal.php?id='.$id.'" style="margin: 0 0 0 -9px;"></a>';
	}

	function timeout($time = '10 MINUTES') {
		return date("Y-m-d H:i:s", strtotime("-$time"));
	}

	function addALog($txt) {
		global $db;
		$db->insert("INSERT INTO s_aalog SET data_s = '".$db->escape($txt)."'");
	}

	function sesslogADD($category = '', $unique = '', $type = 'START') {
		global $cookie_id, $t;
		$ret = false;
		$sql = mysql_query("INSERT INTO s_logvisit SET
		sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
		sess_id = '".secureINS($cookie_id)."',
		user_agent = '".secureINS($_SERVER['HTTP_USER_AGENT'])."',
		user_string = '".secureINS(get_os_($_SERVER['HTTP_USER_AGENT']).' - '.get_browser_($_SERVER['HTTP_USER_AGENT']))."',
		date_snl = NOW(),
		date_cnt = NOW()");
		mysql_query("INSERT INTO s_log SET
		sess_id = '".secureINS($cookie_id)."',
		sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
		category_id = '".((!empty($category))?secureINS($category):'')."',
		unique_id = '".((!empty($unique))?$unique:'')."',
		type_inf = '".((empty($type))?'START':$type)."',
		date_cnt = NOW()");
		return $ret;
	}

	function newsMailRead($id, $mail = '') {
		global $cookie_id, $t;
		if(!empty($mail))
			$sql = @mysql_query("INSERT INTO s_sendvisit SET
			sess_id = '".secureINS($cookie_id)."',
			sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
			category_id = '".((!empty($id))?secureINS($id):'')."',
			unique_id = '".((!empty($mail))?secureINS($mail):'')."',
			date_snl = NOW(),
			date_cnt = NOW()");
		if($sql) { mysql_query("UPDATE $send_tab SET tview_cnt = tview_cnt + 1 WHERE unique_id = '".secureINS($id)."' LIMIT 1"); sesslogADD(((!empty($id))?secureINS($id):''), ((!empty($mail))?secureINS($mail):''), 'MAILREAD'); }
		$sql = mysql_query("SELECT main_id FROM $send_tab WHERE unique_id = '".secureINS($id)."' LIMIT 1");
	}

	function sessWMVADD($id, $file_id, $file_get, $date, $type = 0) {
		global $film_tab, $topic_tab;
		$sql = mysql_query("SELECT main_id FROM $topic_tab WHERE main_id = '$id'");
		if(mysql_num_rows($sql) > 0) {
			$inp = mysql_query("INSERT INTO $film_tab SET
			m_id = '$file_id',
			m_file = '$file_get',
			topic_id = '$id',
			status_id = '0',
			date_cnt = NOW()");
			if($inp) return true; else return false;
		} else return false;
	}

	function sify($str) {
		return (strtolower(substr($str, -1))=='s')?$str:$str.'s';
	}

	function checkPic($id = '') {
		global $pic_view, $pic_tab, $cookie_id;
		$sql = @mysql_query("INSERT INTO $pic_view SET
			sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
			sess_id = '".secureINS($cookie_id)."',
			unique_id = '".secureINS($id)."',
			date_snl = NOW(),
			date_cnt = NOW()");
		if($sql) {
			mysql_query("UPDATE $pic_tab SET p_view = p_view + 1, p_tview = p_tview + 1 WHERE main_id = '".secureINS($id)."' LIMIT 1");
			return true;
		} else {
			mysql_query("UPDATE $pic_tab SET p_tview = p_tview + 1 WHERE main_id = '".secureINS($id)."' LIMIT 1");
			return false;
		}
	}

	function notallowed() {
		global $t;
		if(!isset($_SESSION['u_i']) || !is_numeric($_SESSION['u_i'])) return true;
		if(!mysql_num_rows(mysql_query("SELECT main_id FROM s_admin WHERE main_id = '".secureINS($_SESSION['u_i'])."' AND status_id = '1' LIMIT 1"))) return true;
		return false;
	}

	function getppl() {
		global $v_tab;
		$sql = mysql_query("SELECT COUNT(DISTINCT datetime, ip) AS ppl FROM $v_tab");
		$return = (mysql_num_rows($sql)>0)?mysql_result($sql, 0, 'ppl'):'0';
		return str_replace(",", ".", number_format($return));
	}

	function secureOUT2($str) {
		return nl2br(stripslashes(htmlentities($str)));
	}

	function secureOTH($str) {
		return htmlentities(strip_tags($str));
	}

	function secureBOX($str) {
		return doURL(fixLIST($str));
	}

	function secureMAIL($str) {
		$pattern = array("@", ".");
		$matches = array(" (at) ", " (dot) ");
		return str_replace($pattern, $matches, $str);
	}

	function plainDate($str, $type = true) {
		if($type)
			return ucfirst(date("Y-m-d", strtotime($str)));
		else
			return ucfirst(date("Y-m-d H:i", strtotime($str)));
	}

	function doDate($str) {
		if (date("Y-m-d", strtotime($str)) == date("Y-m-d")) return 'idag';
		if (date("Y-m-d", strtotime($str)) == date("Y-m-d", strtotime("-1 day"))) return 'igår';
		return strftime("%A", strtotime($str));
	}

	function dooDate($str) {
		if (date("Y-m-d", strtotime($str)) == date("Y-m-d")) return 'Idag';
		if (date("Y-m-d", strtotime($str)) == date("Y-m-d", strtotime("-1 day"))) return 'Igår';
		return ucfirst(date("Y-m-d", strtotime($str)));
	}

	function doBirth($date) {
		if (!valiDate($date)) return false;

		$age = explode("-",$date);
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		if(($month>$age[1]) || (($month==$age[1]) && ($day==$age[2])))
			$age = $year - $age[0];
		else
			$age = $year - $age[0] - 1;
		return $age;
	}

	function fixLIST($str) {
		$pattern = array("<ul>", "</ul>");
		$matches = array("	</p>\n	<ul>", "</ul>\n		<p class=\"bre_txt\">\n");
		$str = str_replace($pattern, $matches, $str);
		return $str . "\n";
	}

	function doSPEC($str) {
		return nl2br(urldecode(doURL(htmlentities($str))));
	}

	function checkURL($str) {
		return preg_replace("/(?<=^|\s)((https?|ftps?):\/\/)?(([-_a-zA-Z]+\.)+)([a-zA-Z]{2,6})(\/[^\]\[\s]*)?(?=\s|$)/e","'$1'!=''? '$1$3$5$6':'http://$3$5$6'",$str);
	}

	function getHOST($str) {
		$str = gethostbyaddr($str);
		if (preg_match("/(\d{1,3}\.\d{1,3})\.\d{1,3}\.\d{1,3}/", $str)) return '';

		$str = explode(".", $str);
		$str = $str[count($str) - 2] . '.' . $str[count($str) - 1];
		return $str;
	}

	function readBUL($id) {
		global $btab;
		$sql = mysql_query("SELECT date FROM $btab WHERE id = '{$_SESSION['id']}' AND subiunique = '$id' ORDER BY date DESC LIMIT 1");
		if(mysql_num_rows($sql) == '1')
			mysql_query("UPDATE $btab SET date = NOW() WHERE id = '{$_SESSION['id']}' AND subiunique = '$id'");
		else
			mysql_query("INSERT INTO $btab SET date = NOW(), id = '{$_SESSION['id']}', subiunique = '$id'");
	}

	function stripFile($str) {
		$pattern = array("*", "!", "[", "]", "(", ")", "#", "'", "\"", "\\", "/", "@", "?", ",", ":", ";", "<", ">", "|");
		$matches = array("");
		$str = trim(str_replace($pattern, $matches, $str));
		return $str;
	}

	function updatePic($tid, $id) {
		global $pic_tab;
		mysql_query("UPDATE $pic_tab SET p_view = p_view + 1 WHERE topic_id = '".addslashes($tid)."' AND id = '".addslashes($id)."' LIMIT 1");
	}

	function picDelete($tid, $id, $type = 'view') {
		global $gtable, $ctable;
		if($type == 'view') {
			mysql_query("UPDATE $gtable SET view = 0 WHERE tid = '".addslashes($tid)."' AND id = '".addslashes($id)."' LIMIT 1");
			return true;
		} elseif($type == 'cmnt') {
			mysql_query("DELETE FROM $ctable WHERE tid = '".addslashes($tid)."' AND id = '".addslashes($id)."'");
			return true;
		}
	}

	function sessionDelete($tid, $type = 'view') {
		global $gtable, $ctable;
		if($type == 'view') {
			mysql_query("UPDATE $gtable SET view = 0 WHERE tid = '".addslashes($tid)."'");
			return true;
		} elseif($type == 'cmnt') {
			mysql_query("DELETE FROM $ctable WHERE tid = '".addslashes($tid)."'");
			return true;
		} elseif($type == 'pics') {
			sessionDelete($tid, 'cmnt');
			sessionClean($tid);
			return true;
		}
	}

	function implode_assoc($inner_glue, $outer_glue, $array) {
		$output = array();
		if (is_array($array) && count($array) > 0) {
			foreach( $array as $key => $item )
				$output[] = $key . $inner_glue . trim($item);

			return implode($outer_glue, $output);
		}
		return false;
	}

	function sessionClean($tid) {
		global $gtable, $local_imagedir;
		$sdsql = mysql_query("SELECT id, pic, blocked, blockID FROM $gtable WHERE tid = '$tid' ORDER BY id");
		while($sdrow = mysql_fetch_assoc($sdsql)) {
			if($sdrow['blocked'] == '0') {
				$photo = $local_imagedir.$tid.'/'.$sdrow['id'].'_1.'.$sdrow['pic'];
				$tphoto = $local_imagedir.$tid.'/thumb_'.$sdrow['id'].'_1.'.$sdrow['pic'];
			} else {
				$photo = $local_imagedir.$tid.'/'.$sdrow['blockID'].'-'.$sdrow['id'].'_1.'.$sdrow['pic'];
				$tphoto = $local_imagedir.$tid.'/thumb_'.$sdrow['blockID'].'-'.$sdrow['id'].'_1.'.$sdrow['pic'];
			}
			if (file_exists($photo)) unlink($photo);
			if (file_exists($tphoto)) unlink($tphoto);
		}
		if (file_exists($local_imagedir.$tid)) {
			rmdir($local_imagedir.$tid);
			if (!file_exists($local_imagedir.$tid)) {
				mysql_query("DELETE FROM $gtable WHERE tid = '$tid'");
			}
		}
		return true;
	}

	function specDate($str) {
		if(date("Y-m-d", strtotime($str)) == date("Y-m-d"))
			return '<span class="txt_chead">'.strftime("%y%m%d", strtotime($str)).'</span>';
		else
			return strftime("%y%m%d", strtotime($str));
	}

	function newsDate($date) {
		$return = strftime("%d", strtotime($date)) . strftime(" %B", strtotime($date));
		if(date("Y") > date("Y", strtotime($date))) {
			$return .= date(" Y, ", strtotime($date));
		}
		return strtoupper($return);
	}

	function showDate($str) {
		return date("Y-m-d", strtotime($str));
	}

	function easyOUT($str) {
		return htmlentities(urldecode(stripslashes($str)));
	}

	function listDir($dir) {
		$dirs = array();
		if (is_dir($dir) && ($d = dir($dir)) != "") {
			while ($file = $d->read()) {
				if($file != '.' && $file != '..' && file_exists("$dir/$file") && is_zip($file))
					$dirs[] = $file;
			}
			$d->close();
		}
		sort($dirs);
		return $dirs;
	}

	function listMDir($dir) {
		$dirs = array();
		if (is_dir($dir) && ($d = dir($dir)) != "") {
			while ($file = $d->read()) {
				if($file != '.' && $file != '..' && file_exists("$dir/$file") && is_wmv($file))
					$dirs[] = $file;
			}
			$d->close();
		}
		sort($dirs);
		return $dirs;
	}

	function is_pic($str) {
		$ends = array("jpg", "jpeg", "gif", "png");
		$end = explode(".", $str);
		$end = strtolower($end[count($end) - 1]);
		if(in_array($end, $ends)) return true; else return false;
	}

	function is_zip($str) {
		$ends = array("zip");
		$end = explode(".", $str);
		$end = strtolower($end[count($end) - 1]);
		if(in_array($end, $ends)) return true; else return false;
	}

	function is_wmv($str) {
		$ends = array("wmv");
		$end = explode(".", $str);
		$end = strtolower($end[count($end) - 1]);
		if(in_array($end, $ends)) return true; else return false;
	}

	/* By Martin, Creates a paging list suitable for mobile devices */
	function dopagingMobile($paging, $url, $anchor = '', $width = 'med', $text = '&nbsp;', $vice = 1)
	{
		$max = 10;
		$paging['s'] = $paging['slimit'];
		$pages = ceil($paging['co'] / $paging['limit']);
		if($pages > $max) {
			$paging['slimit'] = $paging['p'] - floor($max / 2);
			if($paging['slimit'] > ($pages - $max + 1)) {
				$paging['slimit'] = $pages - $max + 1;
			}
		} else {
			$paging['slimit'] = 1;
		}
		if($paging['slimit'] < 1) $paging['slimit'] = 1;
		$stop = $paging['slimit'] + $max - 1;
		if($stop > $pages) $stop = $pages;
		if(strpos($text, '%') !== false)
			$text = sprintf($text, (($paging['co'])?$paging['s']+1:0), ($paging['co'] > ($paging['s']+$paging['limit']))?$paging['s']+$paging['limit']:$paging['co'], $paging['co']);

		if($width == 'med' || $width == 'medmin' || $width == 'big' || $width == 'medbig' || $width == 'biggest') echo $text;
		echo ($paging['p'] > 1?'<a href="'.$url.($paging['p']-1).$anchor.'">« '.(($vice)?'bakåt':'framåt').'</a>':'&nbsp;').'&nbsp;&nbsp;';

		if($paging['co'] > $paging['limit']) {
			echo 'sida';
			if($paging['slimit'] > 1) {
				echo ' ...';
			}
			if($stop) { for($i = $paging['slimit']; $i <= $stop; $i++) {
				if($paging['p'] != $i) {
					echo ' <a href="'.$url.$i.$anchor.'">'.$i.'</a>';
				} else {
					echo ' <b>'.$i.'</b>';
				}
			} } else echo '1';
			if($pages > $stop) {
				echo ' ... av '.$pages;
			}
		} else echo ' ';

		if($width == 'big' || $width == 'medbig' || $width == 'med' || $width == 'medmin' || $width == 'biggest') echo '&nbsp;&nbsp;'.($paging['p'] < $stop?'<a href="'.$url.($paging['p']+1).$anchor.'">'.((!$vice)?'bakåt':'framåt').' »</a>':'&nbsp;');
	}
?>
