<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$trackId = $_GET['id'];
	$trackPoint = getTrackPoint($db, $trackId);

	if (!$trackPoint) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$siteId = $trackPoint['siteId'];

	if (isset($_POST['note']) && isset($_POST['location'])) {
		setTrackPointLocation($db, $trackId, $_POST['location']);
		setTrackPointNote($db, $trackId, $_POST['note']);
		$trackPoint = getTrackPoint($db, $trackId);
	}

	include('design_head.php');

	echo '<h2>Edit track point</h2>';
	echo 'Edit track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';
	echo 'Created by '.getUserName($db, $trackPoint['creatorId']).' at '.getDateStringShort($trackPoint['timeCreated']).'<br>';
	if ($trackPoint['timeEdited']) echo '<i>Last edited by '.getUserName($db, $trackPoint['editorId']).' at '.getDateStringShort($trackPoint['timeEdited']).'</i><br>';
	echo '<br>';

	echo MakeTrackerBox('Notes', $trackPoint['trackerNotes']).'<br>';

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$trackId.'">';
	echo 'Track point location:<br>';
	echo '<input type="text" name="location" value="'.$trackPoint['location'].'" size=80><br><br>';

	echo 'Notes:<br>';
	echo '<textarea name="note" cols=80 rows=10>'.$trackPoint['trackerNotes'].'</textarea><br><br>';
	echo '<input type="submit" class="button" value="Save changes"><br>';
	echo '</form><br>';
?>
<script type="text/javascript">
function Select_JS() {
	show_element_by_name('scr_js');
	hide_element_by_name('scr_php');
	hide_element_by_name('scr_asp');
	hide_element_by_name('scr_jsp');
}
function Select_PHP() {
	show_element_by_name('scr_php');
	hide_element_by_name('scr_js');
	hide_element_by_name('scr_asp');
	hide_element_by_name('scr_jsp');
}
function Select_ASP() {
	show_element_by_name('scr_asp');
	hide_element_by_name('scr_js');
	hide_element_by_name('scr_php');
	hide_element_by_name('scr_jsp');
}
function Select_JSP() {
	show_element_by_name('scr_jsp');
	hide_element_by_name('scr_js');
	hide_element_by_name('scr_php');
	hide_element_by_name('scr_asp');
}
</script>

<?
	if ($_SERVER['SERVER_PORT'] != 80) {	
		$server_addr = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
	} else {
		$server_addr = $_SERVER['SERVER_NAME'];
	}

	$script_dir = dirname($_SERVER['SCRIPT_NAME']);

	if ($script_dir == '/' || $script_dir == '\\') $script_dir = '';

	echo '<input type="radio" name="stype" onClick="Select_JS();" checked>Javascript ';
	echo '<input type="radio" name="stype" onClick="Select_PHP();">PHP ';
	echo '<input type="radio" name="stype" onClick="Select_ASP();">ASP ';
	echo '<input type="radio" name="stype" onClick="Select_JSP();">JSP';
	echo '<br>';

	echo '<div id="scr_js">';
	$js_code =
		'&lt;script type="text/javascript"&gt;'."\n".
		"\t".'var i=new Image();'."\n".
		"\t".'i.src="http://'.$server_addr.$script_dir.'/track.php?i='.$trackId.'&amp;l="+escape(document.location)+"&amp;r="+escape(document.referrer);'."\n".
		'&lt;/script&gt;';

	echo 'Javascript to implement this track point (click anywhere on the text to select it):<br><br>';
	echo '<form name="js" action="">';
	echo '<textarea name="scr" class="code" readonly onClick="select_text(\'js.scr\')" cols=130 rows=4>'.$js_code.'</textarea>';
	echo '</form>';
	echo '</div>';
	
	echo '<div id="scr_php" style="display:none;">';
	$php_code =
		'&lt;?php'."\n".
		"\t".'$ref = !empty($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:"";'."\n".
		"\t".'$loc = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];'."\n".
		"\t".'file_get_contents("http://'.$server_addr.$script_dir.'/track.php?i='.$trackId.'&amp;l=".urlencode($loc)."&amp;r=".urlencode($ref).'."\n".
		"\t".'"&amp;ssi&amp;ip=".urlencode($_SERVER["REMOTE_ADDR"])."&amp;ua=".urlencode($_SERVER["HTTP_USER_AGENT"]));'."\n".
		'?&gt;';

	echo 'PHP code to implement track point server-side.<br>';
	echo '<b>Important: This code requires PHP 5.0 <i>or newer</i> to be installed on the web server.</b><br><br>';
	echo '<form name="php" action="">';
	echo '<textarea name="scr" class="code" readonly onClick="select_text(\'php.scr\')" cols=130 rows=6>'.$php_code.'</textarea>';
	echo '</form>';
	echo '</div>';

	echo '<div id="scr_asp" style="display:none;">';
	echo 'ASP code to implement track point server-side.<br>';
	echo '<b>Important: This code requires IIS version XXX <i>or newer</i> to be installed on the web server.</b><br><br>';
	echo '<b>TODO: ASP code is not yet written.</b><br><br>';
	echo '<form name="asp" action="">';
	echo '<textarea name="scr" class="code" readonly onClick="select_text(\'asp.scr\')" cols=130 rows=5>todo</textarea>';
	echo '</form>';
	echo '</div>';

	echo '<div id="scr_jsp" style="display:none;">';
	echo 'JSP code to implement track point server-side.<br>';
	echo '<b>Important: This code requires Tomcat version XXX <i>or newer</i> to be installed on the web server.</b><br><br>';
	echo '<b>TODO: JSP code is not yet written.</b><br><br>';
	echo '<form name="jsp" action="">';
	echo '<textarea name="scr" class="code" readonly onClick="select_text(\'jsp.scr\')" cols=130 rows=5>todo</textarea>';
	echo '</form>';
	echo '</div>';

	echo '<br>';
	echo '<a href="admin_clear_trackpoint.php?id='.$trackId.'">Clear track point data</a>';

	include('design_foot.php');
?>