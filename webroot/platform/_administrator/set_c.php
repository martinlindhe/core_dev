<?
	require_once(dirname(__FILE__).'/../_config/config.include.php');
	require_once(dirname(__FILE__).'/../_config/main.fnc.php');
	require_once(dirname(__FILE__).'/../_config/sql.class.php');
	#require("../../_config/set_vimmel.php");
	require_once(dirname(__FILE__).'/../_config/user.class.php');
	$isCrew = @$_SESSION['u_c'];
	$t_pages = array('news' => 'Nyheter', 'gb' => 'Gästbok', 'extra' => 'Extra', 'send' => 'Register', 'stat' => 'Statistik', 'pics' => 'Vimmel', 'settings' => 'Inställningar', 'changes' => 'Ändringar');
	$in_dir = '../_postloadinside1594/';
	define("FRS", 'amsCS');
	$title = 'CS';
	$anv_txt = array('obj_tho' => 'TYCK TILL', 'obj_pcm' => 'VIMMELKOMMENTARER', 'obj_mcm' => 'FILMKOMMENTARER', 'obj_party' => 'PARTYPLANKET (på sidan)', 'obj_full' => 'HÖGUPPLÖST', 'obj_ue' => 'BILDMAIL', 'obj_pimg' => 'PROFILBILDER', 'obj_event' => 'EVENT', 'obj_sms' => 'SMS', 'obj_tele' => 'TELE', 'obj_pho' => 'FOTOALBUM', 'obj_gb' => 'USER-GB', 'obj_mail' => 'USER-MAIL', 'obj_chat' => 'USER-CHAT', 'obj_blog' => 'USER-BLOG', 'poll' => 'POLL', 'news_notice' => 'NOTISER', 'news_send' => 'NYHETER', 'pics' => 'GALLERI', 'search_s' => 'SÖK', 'search_ss' => 'LOGGSÖK', 'search_sss' => 'SUPERSÖK', 'stat' => 'STATISTIK', 'log' => 'LOGG');
	$cities = array('100' => 'SWEDEN');//, '110' => 'SUNDSVALL', '120' => 'VÄSTERÅS', '170' => '');
	$levels = array('10' => 'ADMIN');
	define("ADMIN_PHOTO_DIR", '../'.USER_GALLERY);
	define('ADMIN_NEWS', '..'.NEWS);
	/*define("ADMIN_IMAGE_DIR", ND.'gallery/');
	define("ADMIN_USER_DIR", '.'.USER_DIR);
	define("ADMIN_OWNER_DIR", '.'.OWNER_DIR);
	define("ADMIN_NEWS_DIR", ND.'_img_news/');
	define("ADMIN_AD_DIR", ND.'_img_ad/');
	define("ADMIN_UE_DIR", '.'.UE_DIR);*/
	define('ADMIN_EMAIL', 'frans@styleform.se');
	define('ADMIN_FROM_EMAIL', 'frans@styleform.se');
	$t = 's_';

	define('AOP', '../_output/');
	define('OP', '_output/');
	#$upl_full = explode('x', IMAGE_FULL);
	#$upl_thumb = explode('x', IMAGE_THUMB);
	$menu_USER = array('USER' => 'user.php', 'MASSMESS' => 'user_send.php');
	$menu_OBJECT = array('OBJEKT' => 'obj.php');
	$menu_SEARCH = array('SÖK' => 'search.php');
	$menu_ADMIN = array('ADMIN' => 'settings.php');
	$menu_STAT = array('STATISTIK' => 'stat.php', 'TREND' => 'stat_obj.php');
	$menu_VIMMEL = array('VIMMEL/FILM' => 'pics.php');
	$sql = &new sql();
	if($isCrew) {
		$menu_LOG = array('LOGG' => 'changes.php?t', 'ANVÄNDARE' => 'settings.php');
		$menu_NEWS = array('NYHETER' => 'news.php', 'NOTISER' => 'news_notice.php', 'ANNONSER' => 'adver.php', 'UTSKICK' => 'send.php', 'MASSMESS' => 'user_send.php', 'POLL' => 'poll.php', 'EDITORIAL' => 'editorial.php', 'TEXT' => 'text.php');
	} else {
		if(!empty($_SESSION['u_a'][1])) {
		$ua = $sql->queryLine("SELECT city_id, pos_all FROM {$t}admin WHERE main_id = '".$_SESSION['u_i']."' LIMIT 1");
		if($ua[1] != $_SESSION['u_a'][1] || $ua[0] != $_SESSION['u_a'][0]) {
			$_SESSION['u_a'][0] = $ua[0];
			$_SESSION['u_a'][1] = $ua[1];
		}
		$menu_LOG = array('LOGG' => 'changes.php?t');
		$arr = array();
		$keys = explode(',', @$_SESSION['u_a'][1]);
		foreach($keys as $val) {
			if(strpos($val, 'news_') !== false) {
				$val = explode('_', $val);
				$arr[$val[1]] = 1;
			}
		}
		$menu_NEWS = array();
		if(@$arr['news']) { $menu_NEWS['NYHETER'] = 'news.php'; $menu_S = 'news.php'; }
		if(@$arr['notice']) { $menu_NEWS['NOTISER'] = 'news_notice.php'; if(empty($menu_S)) $menu_S = 'news_notice.php'; }
		if(@$arr['send']) { $menu_NEWS['UTSKICK'] = 'send.php'; if(empty($menu_S)) $menu_S = 'send.php'; }
		if(@$arr['poll']) { $menu_NEWS['POLL'] = 'poll.php'; if(empty($menu_S)) $menu_S = 'poll.php'; }
		if(@$arr['editorial']) { $menu_NEWS['EDITORIAL'] = 'editorial.php'; if(empty($menu_S)) $menu_S = 'editorial.php'; }
		} else 	$menu_LOG = array('LOGG' => 'changes.php?t');
	}
?>