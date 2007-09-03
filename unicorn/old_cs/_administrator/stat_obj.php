<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	if(!$isCrew && strpos($_SESSION['u_a'][1], 'stat') === false) errorNEW('Ingen behörighet.');
	$sql = &new sql();
	$user = &new user($sql);
	$page = 'TREND';
	$menu = $menu_STAT;
	$current = gettxt('statobj');
	$names = array(
'USER' => 'Nya användare',
'USERVISIT' => 'Profilbesök',
'VISIT' => 'Unika besökare',
'SPY' => 'Bevakningar',
'BLOG' => 'Blogg',
'BLOGCMT' => 'Bloggcmt',
'BLOGVISIT' => 'Bloggbesök',
'BLOGSPY' => 'Bevakade bloggar',
'CHAT' => 'Chat',
//'MOVIE' => 'Filmer',
//'MOVIECMT' => 'Filmkommentarer',
//'MOVIEVISIT' => 'Filmvisn',
'FORUM' => 'Forum',
'PHOTO' => 'Foton',
'PHOTOCMT' => 'Fotocmt',
'PHOTOVISIT' => 'Fotovisn',
'GB' => 'Gästbok',
'LOGIN' => 'Inloggningar',
'MAIL' => 'Mail',
//'CALENDAR' => 'Partyplanket',
//'RELATION' => 'Relationer',
'THOUGHT' => 'Tyck till',
//'GALLERY' => 'Vimmelbilder',
//'GALLERYCMT' => 'Vimmelkommentarer',
//'GALLERYVIEW' => 'Vimmelvisn',
//'INSMS' => 'Uppgrad. SMS',
//'INTELE' => 'Uppgrad. TEL',
//'SENDVISIT' => 'Mail-läsare',
//'ISGOLD' => 'Antal GULD',
//'ISPIC' => 'Profilbilder',
//'MEMORY' => 'Memory',
//'SNAKE' => 'Snake'
	);
$exceptions = array(
		'MOVIE', 'MOVIECMT', 'MOVIEVISIT', 'CALENDAR', 'GALLERY', 'GALLERYCMT', 'GALLERYVIEW', 'RELATION',
		'ISGOLD','ISPIC','INSMS','INTELE','SENDVISIT');
	#$sql->query("INSERT INTO s_logobject SET date_cnt = '".date("Y-m-d", strtotime("-".rand(1,4535)." DAYS"))."', data_s = '".serialize(array('GB' => rand(1, 12000), 'CHAT' => rand(44, 645645), 'MAIL' => rand(12, 534)))."'");
	$res = $sql->query("SELECT date_cnt, data_s FROM {$t}logobject ORDER BY date_cnt DESC");
	$todaydata = array();
	$lastday = false;
	for($i = count($res)-1; $i >= 0; $i--) {
		$data = unserialize($res[$i][1]);
		$todaydata[$res[$i][0]] = array();
		foreach($data as $type => $count) {
			if(!in_array($type, $exceptions) && !empty($lastday) && count($lastday) && isset($lastday[1][$type])) {
				$todaydata[$res[$i][0]][$type] = $count - @$lastday[1][$type];
			} else {
				$todaydata[$res[$i][0]][$type] = $count;
			}
		}
		$lastday = array($res[$i][0], $data);
	}
	krsort($todaydata);
	require("./_tpl/admin_head.php");
?>
<style type="text/css">
@media print {
	.hideme { display: none; }
	* { font-size: 8px;}
}
</style>
	<script type="text/javascript" src="fnc_adm.js"></script>
	<script type="text/javascript">
function loadtop() {
	if(parent.<?=FRS?>head)
	parent.<?=FRS?>head.show_active('stat');
}
<?=(isset($_GET['t']))?'loadtop();':'';?>
	</script>
<form name="csv" action="user_extract.php" method="post">
<input type="hidden" name="pass" value="0">
<input type="hidden" name="level" value="0">
</form>
	<table height="100%">
	<tr class="hideme"><td><?makeMenuAdmin($page, $menu, 0);?></td></tr>
	<tr>
		<td width="100%" style="padding: 0 10px 0 0;">
<input type="submit" class="inp_orgbtn hideme" value="UPPDATERA" onclick="document.location.href = 'update_levels.php?rtu';" style="width: 80px; margin: 11px 0 0 20px;"> Senast uppdaterad: <?=niceDate(gettxt('admin_latestupdate'))?>
			<table cellspacing="2" style="margin: 5px 0 10px 0;">
<?
	foreach($todaydata as $key => $row) {
		echo '<tr class="bg_gray nobr"><td class="pdg">'.specialDate($key).'</td>';
		foreach($row as $name => $count)
			if (!in_array($name, $exceptions)) {
				echo '<td class="pdg nobr">'.((!empty($names[$name]))?$names[$name]:$name).':<br /><b>'.$count.'</b></td>';
		}
	}
?>
			</table>
		</td>
	</tr>
	</table>
</body>
</html>