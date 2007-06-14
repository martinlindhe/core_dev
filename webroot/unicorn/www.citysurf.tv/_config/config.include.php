<?
#local date-settings
setlocale(LC_TIME, 'sv_SE.ISO-8859-1');

#absolute path to www-root
define('CS', '/');
define('OBJ', CS.'_objects/');
define('DESIGN', '_design/');
define('CONFIG', '_config/');
define('PD', '02');
define('UPLA', CS.'_input/');
define('UPLL', '.'.UPLA);
define('UIMG', '150x150');
define('MAXIMUM_USERS', 750);
#standard title of page
define('DEFAULT_USER', '48d40b8b5dee4c06cd8864be1b35456d');
define('NAME_TITLE', 'CitySurf.tv - Nu kör vi!');
$NAME_TITLE = NAME_TITLE;
define('CH', ' SQL_CACHE ');

/*
define('SQL_U', 'webaccount');
define('SQL_P', 'df43534gbhDFAJpt455');
define('SQL_D', 'platform');
define('SQL_H', 'localhost');
*/
define('SQL_U', 'cs_user');
define('SQL_P', 'cs8x8x9ozoSSpp');
define('SQL_D', 'cs_platform');
define('SQL_H', 'pc3.icn.se');


define('SMTP_SERVER', 'localhost');
define('P2B', 'http://www.citysurf.tv/');
define('URL', 'citysurf.tv');
define('NAME_URL', 'CitySurf');
define("UO", '30 MINUTES');
define('ADMIN_NAME', 'CitySurf');
define('USER_GALLERY', '_input/usergallery/');
define('USER_IMG', '_input/images/');
define('USER_FIMG', 'user/image/');
define('NEWS', '/_output/news_');
$sex = array('M' => 'm', 'F' => 'k');
$sex_name = array('M' => 'man', 'F' => 'kvinna');
define('T', 's_');
$t = T;
define('GLOBAL_STRING', 'GLOBAL $sql, $NAME_TITLE, $user, $start, $t, $l;');
define("STATSTR", "listar <b>%1\$d</b> - <b>%2\$d</b> (totalt: <b>%3\$d</b>)");




	function makeButton($bool, $js, $img, $text, $number = false)
	{
		echo '<div class="'.($bool?'btnSelected':'btnNormal').'"'.($js?'onclick="'.$js.'"':'').'>';
		echo '<table summary="" cellpadding="0" cellspacing="0">';
		echo '<tr>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c1.png" alt=""/></td>';
			echo '<td style="background: url(\'/_gfx/themes/btn_head.png\');"></td>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c2.png" alt=""/></td>';
		echo '</tr>';

		echo '<tr style="height: 18px">';
			echo '<td width="3" style="background: url(\'/_gfx/themes/btn_left.png\');"></td>';
			echo '<td style="padding-left: 19px; padding-right: 4px; padding-top: 1px;">';
			if ($img) echo '<img src="/_gfx/'.$img.'" style="position: absolute; top: 5px; left: 4px;" alt=""/> ';
			echo $text;
			if ($number !== false) echo '&nbsp;&nbsp;'.$number;
			echo '</td>';
			echo '<td width="3" style="background: url(\'/_gfx/themes/btn_right.png\');"></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c3.png" alt=""/></td>';
			echo '<td style="background: url(\'/_gfx/themes/btn_foot.png\');"></td>';
			echo '<td width="3"><img src="/_gfx/themes/btn_c4.png" alt=""/></td>';
		echo '</tr>';

		echo '</table>';
		echo '</div>';
	}
	






























/*
define("ELIN", 'http://www.360sverige.se/');
define("SOFIA", 'http://s.360sverige.se/');
define("AGNES", 'http://a.360sverige.se/');
define("FS", SOFIA);
define("ND", '/net/shared/');
define("HOST", ELIN);
define("URL", '360sverige.se');
define("P2B", ELIN);
define("LANG", 'sv-SE');
define("IMAGE_DIR", FS.'gallery/');
define("NDIMAGE_DIR", ND.'gallery/');
define("USER_DIR", './user_img/');
define('VR', './ui/');
define("PHOTO_DIR", './user_photo/');
define("PD", '02');
define("ES", ', u_pstort, u_pstlan, u_regdate, lastonl_date, lastlog_date');
define("OWNER_DIR", '_img_owner/');
define("NEWS_DIR", FS.'_img_news/');
define("MEDIA_DIR", './user_media/');
define("UE_DIR", './user_mms/');
define("SE_DIR", './user_sel/');
define("AD_DIR", FS.'_img_ad/');

define("GOT_AD", true);
define("FLV", './user_flv/');
define("MOVIE_PREFIX", '360');
define("SMS_PREFIX", MOVIE_PREFIX);
define("SMS_USR", '360sverige');
define("SMS_PSS", 'h3tmfGMj');
define("SMS_NMB", '72456');
define("TELE_NMB", '0939-10055');
define("TELE_15", '0939-1005515');
define("TELE_25", '0939-1005525');
define("TELE_50", '0939-1002250');

define("MYSQL_DB", '360sverige');
define("MYSQL_USER", 'web');
define("MYSQL_PASS", 'qQkK911060526');
define("MYSQL_HOST", '192.168.101.2');
*/
/*
define("MYSQL_DB", '360');
define("MYSQL_USER", '360frans');
define("MYSQL_PASS", 'franspassword');
define("MYSQL_HOST", '193.13.74.204');
*/
/*
define("STATSTR", "visar <b>%1\$d</b> - <b>%2\$d</b> av <b>%3\$d</b>");
define("IMAGE_FULL", '658x437');
define("IMAGE_THUMB", '160x106');
$cities = array('100' => 'STOCKHOLM', '110' => 'SUNDSVALL', '120' => 'VÄSTERÅS');
	$ttt = (!empty($_COOKIE['TTT']))?intval($_COOKIE['TTT']):false;
	$ttt = (!empty($_SESSION['cc'])?intval($_SESSION['cc']):$ttt);
if(!empty($ttt) && array_key_exists(@$ttt, $cities))
	define("CITY", intval($ttt));
if(!defined('CITY')) define("CITY", 100);
define("NAME_URL", '360sverige.se');
define("NAME_TITLE", NAME_URL.' - '.ucwords(strtolower($cities[CITY])));
$NAME_TITLE = NAME_TITLE;
define("GALLERY_CODE", '1537');
#for($i = 127; $i <= 400; $i++) echo '<b>'.$i.'</b>: &#'.$i.';';
define("NAME_TEXT", '360'); #&#176;');
define("NAME_FILE", '360SVERIGE');
define("NAME_DESC", 'Sveriges roligaste nattklubbar och vimmel');
define("NAME_KEYWORDS", '360, grader, moatje, aveny, stockholm, sundsvall, kramfors, cream, sinners, bunny, oxid, nattklubb, nightclub, club, film, klubb, vimmel, community, mötesplats, mingel, kramm, statt, community, mötesplats, mingel');
define("NAME_FOOTER", "COPYRIGHT © ".date("Y")." - 360SVERIGE.SE");
$member_email = 'member@360sverige.se';
$t = 's_';
$sex = array('' => '', 'M' => 'K', 'F' => 'T');
$det_type = array('b' => 'Upptagen', 'f' => 'Flexibel', 's' => 'Singel');
$contact_mail = "frans@styleform.se";
$levels = array('1' => 'Standard', '3' => 'Brons', '5' => 'Silver', '6' => 'Guld', '7' => 'Staff', '10' => 'Admin');
$tab = array(
'tab' => 's_', 'text' => 's_text','news' => 's_news','dj' => 's_dj','logvisit' => 's_logvisit','log' => 's_log','ref' => 's_logreferer','ban' => 's_ban','user' => 's_user','changes' => 's_changes','topic' => 's_ptopic',
'thought' => 's_thought','relation' => 's_userrel','relquest' => 's_userrelquest','moviec' => 's_pmoviecmt','pic' => 's_ppic','toplist' => 's_toplist','quest' => 's_quest','online' => 's_useronline',
'chat' => 's_userchat','sess' => 's_usersess','gb' => 's_usergb','regfast' => 's_userregfast','block' => 's_userblock', 'photo' => 's_userphoto','photov' => 's_userphotovisit','notice' => 's_newsnotice','f' => 's_f', 'ft' => 's_ftopic','photosel' => 's_userphotosel','upgrade' => 's_userupgrade','upgradelog' => 's_userupgradelog','blog' => 's_userblog','blogvisit' => 's_userblogvisit','birth' => 's_userlevelbirth','pos' => 's_userposition','level' => 's_userlevel1','gbhis' => 's_usergbhistory','owner' => 's_powner','login' => 's_sesslogin',
'spycheck' => 's_spycheck','picvalid' => 's_userpicvalid','adminchat' => 's_adminchat','view' => 's_ppicview','extra' => 's_extra','pic' => 's_ppic','psms' => 's_psms','sms' => 's_sms','full' => 's_pfull','cmt' => 's_pcmt','cal' => 's_cal','calr' => 's_calread','calc' => 's_calcount','email' => 's_email','movie' => 's_pmovie','moviev' => 's_pmovievisit','admin' => 's_admin','settings' => 's_textsettings','ip' => 's_adminlog','poll' => 's_poll','pollv' => 's_pollvisit','ad' => 's_ad',
'stat' => 's_logstat','advisit' => 's_advisit','pst' => 'p_pst','lan' => 'p_pstlan','ort' => 'p_pstort','rel' => 's_objrel','obj' => 's_obj');
*/
?>