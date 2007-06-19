<?
class gallery {
	var $topic;
	function gallery() {
		if(!empty($topic) && is_md5($topic)) $this->topic = $topic;
	}
	function loop($v, $topic, $p = 1) {
		$upl_thumb = explode('x', GALLERY_THUMB);
		$nl = true;
		$i = 1;
		foreach($v as $val) {
			if($nl) echo ($i > 1?'</tr>':'')."\t\t\t<tr>\n";
			echo "\t\t\t\t".'<td style="padding: 0 0 10px '.($nl?'0':'24px').';"><a href="'.l('gallery', 'view', $val[0]).'&p='.$p.'"><img src="'.GALLERY.$topic.'/'.$val[1].'-thumb.jpg" class="brd" onmouseover="showBrd(this);" onmouseout="hideBrd(this);" width="'.$upl_thumb[0].'" height="'.$upl_thumb[1].'" alt=""></a></td>'."\n";
			if($nl) $nl = false;
echo '
<table cellspacing="0" width="100%" style="margin: 1px 0 0 0;"><tr><td style="padding-left: 1px;">'.$val[0].'</td><td align="center"><img src="'.OBJ.'icon_view.gif" style="margin: 1px 0 -1px 0;"> '.$val[2].'</td><td align="right"'.(($val[3])?' class=""':'').' style="padding-right: 1px;"><img src="'.OBJ.'icon_cmt.gif" style="margin: 1px 0 -1px 0;"> '.$val[3].'</td></tr></table>
';
			if($i++ % 4 == 0) $nl = true;
		}
		if($i != '1') echo "\t\t\t</tr>\n";
	}
	function loopall2($v, $topic, $p = 1) {
		$nl = true;
		$i = 1;
		foreach($v as $val) {
			echo "\t\t\t<tr>\n";
			echo "\t\t\t\t".'<td style="padding-bottom: 5px;"><script type="text/javascript">createFlash(\'gallery_single_view.swf\', \'main_vml\', 658, 438, \'BasicLink='.$val[0].'&p='.$p.'&BasicVariable='.GALLERY.$topic.'/'.$val[1].'\');</script><a href="gallery_single.php?id="></a><table cellspacing="0" width="658" style="margin: 0; height: 14px; border: 1px solid #FFF; border-top: 0;"><tr>';
			#echo "\t\t\t\t".'<td style="padding-bottom: 5px;"><a href="gallery_single.php?id='.$val[0].'&p='.$p.'"><img src="'.GALLERY.$topic.'/'.$val[1].'.jpg" alt=""></a><table cellspacing="0" width="658" style="margin: 0; height: 14px; border: 1px solid #FFF; border-top: 0;"><tr>
			echo '<td style="padding-left: 2px; width: 60px; padding-bottom: 6px;" class="spac">#'.$val[0].'</td><td align="right" style="width: 75%; padding-bottom: 6px;" class="spac"><img src="./_img/icon_view.gif" alt="" style="margin-bottom: -2px;">  '.$val[2].'</td><td align="right" class="spac'.(($val[3])?' bld':'').'" style="padding-right: 2px; padding-bottom: 6px; width: 120px;"><img src="./_img/icon_cmt.gif" alt="" style="margin-bottom: -2px;">  '.$val[3].'</td></tr></table></td>'."\n";
			echo "\t\t\t</tr>\n";
		}
		if($i != '1') echo "\t\t\t</tr>\n";
	}
	function loopall($v, $topic, $p = 1) {
		$nl = true;
		$i = 1;
		foreach($v as $val) {
			echo "\t\t\t<tr>\n";
			#echo "\t\t\t\t".'<td style="padding-bottom: 5px;"><script type="text/javascript">createFlash(\'gallery_single_view.swf\', \'main_vml\', 658, 438, \'BasicLink='.$val[0].'&p='.$p.'&BasicVariable='.GALLERY.$topic.'/'.$val[1].'\');</script><a href="gallery_single.php?id="></a><table cellspacing="0" width="658" style="margin: 0; height: 14px; border: 1px solid #FFF; border-top: 0;"><tr>';
			echo "\t\t\t\t".'<td style="padding-bottom: 5px;"><a href="gallery_single.php?id='.$val[0].'&p='.$p.'"><img src="'.GALLERY.$topic.'/'.$val[1].'.jpg" alt=""></a><table cellspacing="0" width="658" style="margin: 2px 0 0 0; height: 14px; border: 1px solid #FFF; border-top: 0;"><tr>';
			echo '<td style="padding-left: 2px; width: 60px; padding-bottom: 6px;" class="spac">#'.$val[0].'</td><td align="right" style="width: 75%; padding-bottom: 6px;" class="spac"><img src="./_img/icon_view.gif" alt="" style="margin-bottom: -2px;">  '.$val[2].'</td><td align="right" class="spac'.(($val[3])?' bld':'').'" style="padding-right: 2px; padding-bottom: 6px; width: 120px;"><img src="./_img/icon_cmt.gif" alt="" style="margin-bottom: -2px;">  '.$val[3].'</td></tr></table></td>'."\n";
			echo "\t\t\t</tr>\n";
		}
		if($i != '1') echo "\t\t\t</tr>\n";
	}
	function cmtloop($v, $topic, $p = 1) {
		$nl = true;
		$i = 1;
		foreach($v as $val) {
			echo "\t\t\t\t".'<tr><td style="padding-bottom: 3px;"><table cellspacing="0" class="bg_gray" style="width: 100%;"><tr><td style="width: 113px;"><a href="gallery_single.php?id='.$val['unique_id'].'"><img src="'.GALLERY.$val['topic_id'].'/'.$val['pic_id'].'-thumb.jpg" alt="" class="brd"></a></td><td class="pdg">'.((!empty($val['c_email']) && !is_md5($val['c_email']))?'<a href="mailto:'.secureOUT($val['c_email']).'" class="bld">'.secureOUT($val['c_name']).'</a>':((is_md5($val['c_email']))?'<a href="userspot.php?id='.secureOUT($val['c_email']).'" class="bld user">'.secureOUT($val['c_name']).'</a>':'<span class="bld">'.secureOUT($val['c_name']))).'</span> säger:</b><br>'.secureOUT($val['c_msg']).'</td></tr></table></td></tr>'."\n";
		}
		if($i != '1') echo "\t\t\t</tr>\n";
	}
	function loop_mini($v) {
		$upl_thumb = explode('x', GALLERY_THUMB);
		$nl = true;
		$i = 1;
		foreach($v as $val) {
			if($nl) echo "\t\t\t<tr>\n";
			echo "\t\t\t\t".'<td class="'.(($nl)?'':'g_rst').'" style="padding-top: 6px;"><a href="gallery_single.php?id='.$val[3].'" title="'.(($val[4])?secureOUT($val[5]):'Raderad').': '.secureOUT($val[0]).'"><img src="'.GALLERY.$val[2].'/'.$val[1].'-thumb.jpg" width="'.$upl_thumb[0].'" height="'.$upl_thumb[1].'" alt="'.(($val[4])?secureOUT($val[5]):'Raderad').': '.secureOUT($val[0]).'"></a></td>'."\n";
			if($nl) $nl = false;
			if($i++ % 4 == 0) $nl = true;
		}
		if($i != '1') echo "\t\t\t</tr>\n";
	}
	function galleryList($type, $topic = '', $slimit = 0, $limit = 0, $city = '') {
		global $db;
		switch($type) {
		case 'all':
			return $db->getArray("SELECT main_id, p_name, p_date, p_dday, p_views, p_cmts, p_pics, p_popular, owner_str, p_city, status_id FROM s_ptopic ORDER BY p_date DESC".(($limit)?" LIMIT $slimit, $limit":''));
		break;
		case 'allcity':
			return $db->getArray("SELECT main_id, p_name, p_date, p_dday, p_views, p_cmts, p_pics, p_popular, owner_str, p_city, status_id FROM s_ptopic WHERE p_city = '".$city."' ORDER BY p_date DESC".(($limit)?" LIMIT $slimit, $limit":''));
		break;
		case 'count':
			return $db->getArray("SELECT COUNT(*) as count FROM s_ptopic WHERE status_id = '1'");
		break;
		case 'movie':
			return $db->getArray("SELECT m.topic_id, m.m_id, m.m_name, m.m_date, m.m_dday, m.m_file, m.m_view, m.m_owner, m.m_edit, m.m_size, m.m_length, m.m_cmt, t.p_city, t.status_id FROM s_pmovie m INNER JOIN s_ptopic t ON t.main_id = m.topic_id WHERE m.status_id =  '1' ORDER BY t.p_date DESC".(($limit)?" LIMIT $slimit, $limit":''));
		break;
		case 'moviecity':
			return $db->getArray("SELECT m.topic_id, m.m_id, m.m_name, m.m_date, m.m_dday, m.m_file, m.m_view, m.m_owner, m.m_edit, m.m_size, m.m_length, m.m_cmt, t.p_city, t.status_id FROM s_pmovie m INNER JOIN s_ptopic t ON t.main_id = m.topic_id AND t.p_city = '".$city."' WHERE m.status_id =  '1' ORDER BY t.p_date DESC".(($limit)?" LIMIT $slimit, $limit":''));
		break;
		case 'movievip':
			return $db->getArray("SELECT m.topic_id, m.m_id, m.m_name, m.m_date, m.m_dday, m.m_file, m.m_view, m.m_owner, m.m_edit, m.m_size, m.m_length, m.m_cmt, t.p_city, t.status_id FROM s_pmovie m INNER JOIN s_ptopic t ON t.main_id = m.topic_id WHERE m.status_id =  '1' ORDER BY t.p_date DESC, m.main_id DESC".(($limit)?" LIMIT $slimit, $limit":''));
		break;
		case 'piccount':
			return $db->getOneItem("SELECT COUNT(*) FROM s_ppic WHERE topic_id = '".$db->escape($topic)."' AND status_id = '1'");
		break;
		case 'moviecount':
			return $db->getOneItem("SELECT COUNT(*) FROM s_pmovie WHERE status_id = '1'");
		break;
		case 'cmtnewcount':
			return $db->getOneItem("SELECT COUNT(*) FROM s_pcmt WHERE topic_id = '".$db->escape($topic)."' AND status_id = '1'");
		break;
		case 'cmtnewallcount':
			return $db->getOneItem("SELECT COUNT(*) FROM s_pcmt WHERE status_id = '1'");
		break;
		case 'cmtnew':
			return $db->getArray("SELECT a.main_id, a.unique_id, a.topic_id, a.pic_id, a.c_name, a.c_email, a.c_msg, a.c_date, a.c_html FROM s_pcmt a INNER JOIN s_ppic p ON p.main_id = a.unique_id AND p.status_id = '1' INNER JOIN s_ptopic t ON t.main_id = a.topic_id AND t.status_id = '1' WHERE a.topic_id = '".$db->escape($topic)."' AND a.status_id = '1' ORDER BY c_date DESC".(($limit)?" LIMIT $slimit, $limit":''), 0, 1);
		break;
		case 'cmtnewall':
			return $db->getArray("SELECT a.main_id, a.unique_id, a.topic_id, a.pic_id, a.c_name, a.c_email, a.c_msg, a.c_date, a.c_html FROM s_pcmt a INNER JOIN s_ppic p ON p.main_id = a.unique_id AND p.status_id = '1' INNER JOIN s_ptopic t ON t.main_id = a.topic_id AND t.status_id = '1' WHERE a.status_id = '1' ORDER BY c_date DESC".(($limit)?" LIMIT $slimit, $limit":''), 0, 1);
		break;
		case 'cmtcount':
			return $db->getOneItem("SELECT COUNT(*) FROM s_pcmt WHERE unique_id = '$topic' AND status_id = '1'");
		break;
		case 'cmtmvcount':
			return $db->getOneItem("SELECT COUNT(*) FROM s_pmoviecmt WHERE unique_id = '$topic' AND status_id = '1'");
		break;
		case 'topic':
			return $db->getArray("SELECT main_id, p_name, p_date, p_dday, p_views, p_cmts, p_pics, p_popular, owner_str, p_city, status_id FROM s_ptopic a WHERE a.main_id = '".$db->escape($topic)."' AND a.status_id = '1' LIMIT 1");
		break;
		case 'owner':
			return $db->getArray("SELECT o.p_name, o.p_pic, u.id_id, u.u_birth, u.u_alias, u.u_picid, u.u_picvalid, u.u_sex, u.u_picd, u.account_date FROM s_powner_rel r INNER JOIN s_powner o ON o.main_id = r.owner_id LEFT JOIN s_user u ON u.id_id = o.p_user AND u.status_id = '1' WHERE r.topic_id = '".$db->escape($topic)."' ORDER BY o.p_name", 0, 1);
		break;
		case 'topicvip':
			return $db->getArray("SELECT main_id, p_name, p_date, p_dday, p_views, p_cmts, p_pics, p_popular, owner_str, p_city, status_id FROM s_ptopic a WHERE a.main_id = '".$db->escape($topic)."' LIMIT 1");
		break;
		case 'multi':
			return $db->getArray("SELECT main_id, id, p_view, p_cmt FROM s_ppic WHERE topic_id = '".$db->escape($topic)."' AND status_id = '1' ORDER BY order_id, main_id".(($limit)?" LIMIT $slimit, $limit":''));
		break;
		case 'single':
			return $db->getOneItem("SELECT a.main_id, a.id, a.p_view, a.p_cmt, a.status_id, a.order_id, b.main_id AS topic_id, b.p_name, b.p_date, b.p_dday, o.p_name, o.p_pic, b.p_views, b.p_cmts, b.p_pics, b.p_city, b.status_id as topic_status, u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picid, u.u_picvalid, u.u_picd, u.account_date FROM (s_ppic a, s_ptopic b) LEFT JOIN s_powner o ON a.owner_id = o.main_id LEFT JOIN s_user u ON u.id_id = o.p_user AND u.status_id = '1' WHERE a.main_id = '".$db->escape($topic)."' AND a.status_id = '1' AND b.main_id = a.topic_id LIMIT 1");
		break;
		case 'singlevip':
			return $db->getArray("SELECT a.main_id, a.id, a.p_view, a.p_cmt, a.status_id, a.order_id, b.main_id AS topic_id, b.p_name, b.p_date, b.p_dday, a.owner_id, o.p_name, o.p_mail, o.p_pic, b.p_views, b.p_cmts, b.p_pics, b.p_city, b.status_id FROM s_ppic a INNER JOIN s_ptopic b ON b.main_id = a.topic_id LEFT JOIN s_powner o ON a.owner_id = o.main_id WHERE a.main_id = '".$db->escape($topic)."' AND a.status_id = '1' LIMIT 1");
		break;
		case 'cmt':
			return $db->getArray("SELECT a.main_id, a.c_msg, a.c_date, a.c_html, a.logged_in, u.u_alias, u.u_picvalid, u.u_picid, u.u_picd, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date FROM s_pcmt a LEFT JOIN s_user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.unique_id = '".$db->escape($topic)."' AND a.status_id = '1' ORDER BY a.main_id DESC".(($limit)?" LIMIT $slimit, $limit":''), 0, 1);
		break;
		case 'cmtmv':
			return $db->getArray("SELECT a.main_id, a.c_msg, a.c_date, a.c_html, a.logged_in, u.u_alias, u.u_picvalid, u.u_picid, u.u_picd, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date FROM s_pmoviecmt a INNER JOIN s_user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.unique_id = '".$db->escape($topic)."' AND a.status_id = '1' ORDER BY a.main_id DESC".(($limit)?" LIMIT $slimit, $limit":''), 0, 1);
		break;
		case 'mostviewed':
			return $db->getArray("SELECT main_id, id, p_view, p_cmt FROM s_ppic WHERE status_id = '1' AND p_view >0 AND topic_id = '".$db->escape($topic)."' ORDER BY p_view DESC, order_id ASC, main_id ASC LIMIT 4");
		break;
		case 'mostcommented':
			return $db->getArray("SELECT b.main_id, b.id, b.p_view, b.p_cmt FROM s_pcmt a INNER JOIN s_ppic b ON a.unique_id = b.main_id AND b.status_id = '1' WHERE a.topic_id = '".$db->escape($topic)."' AND a.status_id = '1' GROUP BY b.main_id ORDER BY p_cmt DESC LIMIT 4");
		break;
		case 'latestcomment':
			return $db->getArray("SELECT a.c_msg, a.pic_id, a.topic_id, a.unique_id, a.logged_in, u.u_alias FROM s_pcmt a INNER JOIN s_ppic b ON b.main_id = a.unique_id AND b.status_id = '1' INNER JOIN s_ptopic c ON c.main_id = a.topic_id AND c.status_id = '1' INNER JOIN s_user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' AND a.p_city = '".CITY."' ORDER BY a.main_id DESC LIMIT 8");
		break;
		case 'latestmvcmt':
			return $db->getArray("SELECT a.c_msg, a.topic_id, a.unique_id, a.logged_in, u.u_alias, b.m_file FROM s_pmoviecmt a INNER JOIN s_pmovie b ON b.m_id = a.unique_id AND b.status_id = '1' INNER JOIN s_ptopic c ON c.main_id = a.topic_id AND c.status_id = '1' AND c.p_city = '".CITY."' INNER JOIN s_user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' ORDER BY a.main_id DESC LIMIT 4");
		break;
		case 'latestcomment2':
			return $db->getArray("SELECT a.c_msg, a.pic_id, a.topic_id, a.unique_id FROM s_pcmt a, s_ppic b, s_ptopic c WHERE a.status_id = '1' AND b.main_id = a.unique_id AND b.topic_id = a.topic_id AND b.status_id = '1' AND c.main_id = a.topic_id AND c.status_id = '1' ORDER BY a.c_date DESC LIMIT 8");
		break;
		case 'b':
			return $db->getOneItem("SELECT main_id FROM s_ppic WHERE (order_id < '$limit' AND main_id != '$slimit' AND topic_id =  '".$db->escape($topic)."' AND status_id = '1') OR (order_id = '$limit' AND main_id < '$slimit' AND topic_id = '".$db->escape($topic)."' AND status_id = '1') ORDER BY order_id DESC, main_id DESC LIMIT 1");
		break;
		case 'f':
			return $db->getOneItem("SELECT main_id FROM s_ppic WHERE (order_id > '$limit' AND main_id != '$slimit' AND topic_id =  '".$db->escape($topic)."' AND status_id = '1') OR (order_id = '$limit' AND main_id > '$slimit' AND topic_id = '".$db->escape($topic)."' AND status_id = '1') ORDER BY order_id, main_id LIMIT 1");
		break;
		}
	}
	function galleryView($topic, $id) {
		global $db;
		$c = $db->getOneItem("SELECT COUNT(*) FROM s_ppicview WHERE sess_ip = '".$db->escape($_SERVER['REMOTE_ADDR'])."' AND unique_id = '".$db->escape($id)."' AND date_snl = NOW() LIMIT 1");
		$this->sql->logAdd($topic, $id, 'VIEW');
		if(!$c) {
			$db->update("UPDATE s_ppic SET p_view = p_view + 1, p_tview = p_tview + 1 WHERE main_id = '".$db->escape($id)."' LIMIT 1");
			$db->update("UPDATE s_ptopic SET p_views = p_views + 1 WHERE main_id = '".$db->escape($topic)."' LIMIT 1");
			$db->insert("INSERT INTO s_ppicview SET sess_ip = '".$db->escape($_SERVER['REMOTE_ADDR'])."', unique_id = '".$db->escape($id)."', date_snl = NOW(), date_cnt = NOW()");
			return false;
		} return true;
	}
// ta bort all info om en bild, radera bilden, töm cache i topic
	function galleryDelete($type, $id, $array = false, $parentstatus = 1) {
		global $db;
		switch($type) {
		case 'pic':
			$row = $this->sql->query("SELECT main_id, id, topic_id, p_view, p_cmt, status_id, statusID FROM s_ppic WHERE main_id = '".$db->escape($id)."' LIMIT 1");
			if($row) {
				$this->galleryDelete('file', '', $row);
				#$this->sql->queryUpdate("DELETE FROM s_pcmt WHERE unique_id = '".$db->escape($row[0][0])."'");
				$this->sql->queryUpdate("UPDATE s_pcmt SET status_id = '2' WHERE unique_id = '".$db->escape($row[0][0])."'");
				$this->sql->queryUpdate("DELETE FROM s_ppic WHERE main_id = '".$db->escape($row[0][0])."' LIMIT 1");
				if($row[0][5] == '1')
					$this->sql->queryUpdate("UPDATE s_ptopic SET p_views = p_views - {$row[0][3]}, p_cmts = p_cmts - {$row[0][4]}, p_pics = p_pics - 1 WHERE main_id = '".$db->escape($row[0][2])."' LIMIT 1");
			}
		break;
		case 'cmt':
			$row = $this->sql->query("SELECT main_id, unique_id, topic_id FROM s_pcmt WHERE main_id = '".$db->escape($id)."' LIMIT 1");
			if($row) {
				if($array) {
					$this->sql->queryUpdate("UPDATE s_ppic SET p_cmt = p_cmt - 1 WHERE main_id = '".$db->escape($row[0][1])."' LIMIT 1");
					if($parentstatus == '1') {
						$this->sql->queryUpdate("UPDATE s_ptopic SET p_cmts = p_cmts - 1 WHERE main_id = '".$db->escape($row[0][2])."' LIMIT 1");
					}
				}
				#$this->sql->queryUpdate("DELETE FROM s_pcmt WHERE main_id = '".$db->escape($row[0][0])."'");
				$this->sql->queryUpdate("UPDATE s_pcmt SET status_id = '2', view_id = '1' WHERE main_id = '".$db->escape($row[0][0])."'");
			}
		break;
		case 'cmtmv':
			$row = $this->sql->query("SELECT main_id, unique_id FROM s_pmoviecmt WHERE main_id = '".$db->escape($id)."' LIMIT 1");
			if($row) {
				if($array) {
					$this->sql->queryUpdate("UPDATE s_pmovie SET m_cmt = m_cmt - 1 WHERE m_id = '".$db->escape($row[0][1])."' LIMIT 1");
				}
				$this->sql->queryUpdate("UPDATE s_pmoviecmt SET status_id = '2', view_id = '1' WHERE main_id = '".$db->escape($row[0][0])."'");
			}
		break;
		case 'file':
			if($array) {
				if($array[0][4] == '2') {
					@unlink(ADMIN_GALLERY.$array[0][2].'/'.$array[0][1].'-'.$array[0][6].'.jpg');
					@unlink(ADMIN_GALLERY.$array[0][2].'/'.$array[0][1].'-full'.GALLERY_CODE.'-'.$array[0][6].'.jpg');
					@unlink(ADMIN_GALLERY.$array[0][2].'/'.$array[0][1].'-thumb-'.$array[0][6].'.jpg');
				} else {
					@unlink(ADMIN_GALLERY.$array[0][2].'/'.$array[0][1].'.jpg');
					@unlink(ADMIN_GALLERY.$array[0][2].'/'.$array[0][1].'-full'.GALLERY_CODE.'.jpg');
					@unlink(ADMIN_GALLERY.$array[0][2].'/'.$array[0][1].'-thumb.jpg');
				}
			}
		break;
		}
	}
	function galleryAdd($type, $topic, $status) {
		global $db;
		switch($type) {
		case 'pic':
			if($status == '1')
				$this->sql->queryUpdate("UPDATE s_ptopic SET p_pics = p_pics + 1 WHERE main_id = '".$db->escape($topic)."' LIMIT 1");
		break;
		}
	}
	function galleryUpdate($type, $topic, $status, $oldstatus, $parentstatus = 1) {
		global $user;
		switch($type) {
		case 'pic':

			if($status == '1' && $oldstatus != '1') {
// ska visas, adda 1
				$this->sql->queryUpdate("UPDATE s_ptopic SET p_pics = p_pics + 1, p_views = p_views + {$topic['p_view']}, p_cmts = p_cmts + {$topic['p_cmt']} WHERE main_id = '".$db->escape($topic['topic_id'])."' LIMIT 1");
			} elseif($status != '1' && $oldstatus == '1') {
// ska döljas, della 1
				$this->sql->queryUpdate("UPDATE s_ptopic SET p_pics = p_pics - 1, p_views = p_views - {$topic['p_view']}, p_cmts = p_cmts - {$topic['p_cmt']} WHERE main_id = '".$db->escape($topic['topic_id'])."' LIMIT 1");
			}
		break;
		case 'cmt':
			if($parentstatus == '1') {
				if($status == '1' && $oldstatus != '1') {
// ska visas, adda 1, fixspy!
					if(!empty($topic['str_id'])) $user->fixSpy('v', $topic['pic_id'], $topic['topic_id'].'/'.$topic['str_id'].'-thumb.jpg', @$topic['logged_id']);
					$this->sql->queryUpdate("UPDATE s_ptopic SET p_cmts = p_cmts + 1 WHERE main_id = '".$db->escape($topic['topic_id'])."' LIMIT 1");
					$this->sql->queryUpdate("UPDATE s_ppic SET p_cmt = p_cmt + 1 WHERE main_id = '".$db->escape($topic['pic_id'])."' LIMIT 1");
				} elseif($status != '1' && $oldstatus == '1') {
// ska döljas, della 1
					$this->sql->queryUpdate("UPDATE s_ptopic SET p_cmts = p_cmts - 1 WHERE main_id = '".$db->escape($topic['topic_id'])."' LIMIT 1");
					$this->sql->queryUpdate("UPDATE s_ppic SET p_cmt = p_cmt - 1 WHERE main_id = '".$db->escape($topic['pic_id'])."' LIMIT 1");
				}
				$this->sql->queryUpdate("UPDATE s_pcmt SET status_id = '".$db->escape($status)."' WHERE main_id = '".$db->escape($topic['main_id'])."' LIMIT 1");
			}
		break;
		case 'cmtmv':
			if($status == '1' && $oldstatus != '1') {
// ska visas, adda 1, fixspy!
				#if(!empty($topic['str_id'])) $user->fixSpy('mv', $topic['pic_id'], $topic['topic_id'].'/'.$topic['str_id'].'-thumb.jpg', @$topic['logged_id']);
				$this->sql->queryUpdate("UPDATE s_pmovie SET m_cmt = m_cmt + 1 WHERE m_id = '".$db->escape($topic['m_id'])."' LIMIT 1");
			} elseif($status != '1' && $oldstatus == '1') {
// ska döljas, della 1
				$this->sql->queryUpdate("UPDATE s_pmovie SET m_cmt = m_cmt - 1 WHERE m_id = '".$db->escape($topic['m_id'])."' LIMIT 1");
			}
			$this->sql->queryUpdate("UPDATE s_pmoviecmt SET status_id = '".$db->escape($status)."' WHERE main_id = '".$db->escape($topic['main_id'])."' LIMIT 1");
		break;
		case 'file':
			if($status == '2' && $oldstatus != '2') {
// ska blockas, byt namn, kom ihåg statusID
				$unique = md5(microtime());
				if(file_exists(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'.'.$topic['p_pic'])) {
					rename(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'.'.$topic['p_pic'], ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-'.$unique.'.'.$topic['p_pic']);
				}
				if(file_exists(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-full'.GALLERY_CODE.'.'.$topic['p_pic'])) {
					rename(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-full'.GALLERY_CODE.'.'.$topic['p_pic'], ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-full'.GALLERY_CODE.'-'.$unique.'.'.$topic['p_pic']);
				}
				if(file_exists(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-thumb.'.$topic['p_pic'])) {
					rename(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-thumb.'.$topic['p_pic'], ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-thumb-'.$unique.'.'.$topic['p_pic']);
				}
				return $unique;
			} elseif($status != '2' && $oldstatus == '2') {
// har varit blockad, är inte det längre, visa!
				if(file_exists(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-'.$topic['statusID'].'.'.$topic['p_pic'])) {
					rename(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-'.$topic['statusID'].'.'.$topic['p_pic'], ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'.'.$topic['p_pic']);
				}
				if(file_exists(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-full'.GALLERY_CODE.'-'.$topic['statusID'].'.'.$topic['p_pic'])) {
					rename(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-full'.GALLERY_CODE.'-'.$topic['statusID'].'.'.$topic['p_pic'], ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-full'.GALLERY_CODE.'.'.$topic['p_pic']);
				}
				if(file_exists(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-thumb-'.$topic['statusID'].'.'.$topic['p_pic'])) {
					rename(ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-thumb-'.$topic['statusID'].'.'.$topic['p_pic'], ADMIN_GALLERY.$topic['topic_id'].'/'.$topic['id'].'-thumb.'.$topic['p_pic']);
				}
				return false;
			}
		break;
		}
	}
	function galleryFix() {
		global $db;
		$topic = array();
		$return = $this->sql->query("SELECT main_id, topic_id, status_id FROM s_ppic");
		foreach($return as $val) {
			$c = $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pcmt WHERE unique_id = '{$val[0]}' AND status_id = '1'");
			$v = $this->sql->queryResult("SELECT COUNT(*) as count FROM s_ppicview WHERE unique_id = '{$val[0]}'");
			$this->sql->queryUpdate("UPDATE s_ppic SET p_cmt = '$c', p_view = '$v' WHERE main_id = '{$val[0]}' LIMIT 1");
			if($val[2] == '1') {
				if(!isset($topic[$val[1]]))
					 $topic[$val[1]] = array($c, $v, 1);
				else {
					$topic[$val[1]][0] += $c;
					$topic[$val[1]][1] += $v;
					$topic[$val[1]][2]++;
				}
			}
		}
		foreach($topic as $key => $val) {
			$this->sql->queryUpdate("UPDATE s_ptopic SET p_cmts = '{$val[0]}', p_views = '{$val[1]}', p_pics = '{$val[2]}' WHERE main_id = '".$key."' LIMIT 1");
		}
		$return = $this->sql->query("SELECT m_id FROM s_pmovie");
		foreach($return as $val) {
			$c = $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pmoviecmt WHERE unique_id = '{$val[0]}' AND status_id = '1'");
			$this->sql->queryUpdate("UPDATE s_pmovie SET m_cmt = '$c' WHERE m_id = '{$val[0]}' LIMIT 1");
		}
	}
	function galleryRefresh($total = true, $today = false) {
		global $db;
		if($total) {
			$stat = array(0, 0, 0, 0, 0, 0, 0, 0);
			$stat[0] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_logvisit");
			$result = $this->sql->query("SELECT main_id, p_pics, p_views, p_cmts, status_id FROM s_ptopic");
			foreach($result as $val) {
				$pic = $this->sql->query("SELECT id FROM s_ppic WHERE topic_id = '".$val[0]."' AND status_id = '1' ORDER BY p_view DESC LIMIT 1");
				if(count($pic))
					$this->sql->queryUpdate("UPDATE s_ptopic SET p_popular = '".$pic[0][0]."' WHERE main_id = '".$val[0]."' LIMIT 1");
				if($val[4] == '1') {
					$stat[1] += $val[1];
					$stat[2] += $val[2];
					$stat[3] += $val[3];
				}
			}
			#$stat[2] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_ppicview");
// fix for cream due to a problem regarding cookies, fixed, but desync on stat.
			$stat[4] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_usergb WHERE status_id = '1'");
			$stat[5] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pmovie WHERE status_id = '1'");
			$stat[6] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pmovievisit");
			$stat[7] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pmoviecmt m, s_pmovie t WHERE t.m_id = m.unique_id AND t.status_id = '1'");
			$stat = implode(':', $stat);
			$this->sql->queryUpdate("UPDATE s_text SET text_cmt = '$stat' WHERE main_id = 'stat' AND option_id = '1' LIMIT 1");
		}
		if($today) {
			$stat = array(0, 0, 0, 0, 0, 0, 0, 0);
			$stat[0] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_logvisit WHERE date_snl = NOW()");
			$stat[2] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_ppicview WHERE date_snl = NOW()");
			$stat[3] += $this->sql->queryResult("SELECT COUNT(a.main_id) as count FROM s_pcmt a, s_ppic b WHERE TO_DAYS(a.c_date) = TO_DAYS(CURRENT_DATE) AND a.status_id = '1' AND b.status_id = '1' AND b.main_id = a.unique_id");
			$stat[4] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_usergb WHERE TO_DAYS(gb_date) = TO_DAYS(CURRENT_DATE) AND status_id = '1'");
			$stat[6] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pmovievisit WHERE date_snl = NOW()");
			$stat[7] += $this->sql->queryResult("SELECT COUNT(*) as count FROM s_pmoviecmt WHERE TO_DAYS(c_date) = TO_DAYS(CURRENT_DATE) AND status_id = '1'");
			$stat = implode(':', $stat);
			$this->sql->queryUpdate("UPDATE s_text SET text_cmt = '$stat' WHERE main_id = 'stat' AND option_id = '0' LIMIT 1");
		}

	}
}
?>