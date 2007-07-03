<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	ini_set("max_execution_time", 0);
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	require("./set_formatadm.php");
	$page = 'OBJEKT';
	$menu = $menu_OBJECT;
	$status = (!empty($_GET['status']))?$_GET['status']:'';
	$sql = &new sql();
	$user = &new user($sql);
	if($isCrew || strpos($_SESSION['u_a'][1], 'obj') !== false) {
	if($status == 'thought' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_tho') !== false)) {
		require("./_tpl/obj_thought.php");
	} elseif($status == 'cmt' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pcm') !== false)) {
		require("./_tpl/obj_cmt.php");
	} elseif($status == 'cmtmv' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_mcm') !== false)) {
		require("./_tpl/obj_cmtmv.php");
	} elseif($status == 'scc' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false)) {
		require("./_tpl/obj_scc.php");
	} elseif($status == 'full' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_full') !== false)) {
		require("./_tpl/obj_full.php");
	} elseif($status == 'ue' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_ue') !== false)) {
		require("./_tpl/obj_ue.php");
	} elseif($status == 'img' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pimg') !== false)) {
		require("./_tpl/obj_img.php");
	} elseif($status == 'tele' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_tele') !== false)) {
		require("./_tpl/obj_tele.php");
	} elseif($status == 'photo' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pho') !== false)) {
		require("./_tpl/obj_photo.php");
	} elseif($status == 'event' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_event') !== false)) {
		require("./_tpl/obj_event.php");
	} elseif($status == 'sms' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_sms') !== false)) {
		require("./_tpl/obj_sms.php");
	} elseif($status == 'gb' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_gb') !== false)) {
		require("./_tpl/obj_gb.php");
	} elseif($status == 'mail' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_mail') !== false)) {
		require("./_tpl/obj_mail.php");
	} elseif($status == 'chat' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_chat') !== false)) {
		require("./_tpl/obj_chat.php");
	} elseif($status == 'blog' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_blog') !== false)) {
		require("./_tpl/obj_blog.php");
	} elseif($status == 'abuse' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_abuse') !== false)) {
		require("./_tpl/obj_abuse.php");
	} else 	require("./_tpl/obj_head.php");
	} else errorNEW('Ingen behörighet.');
?>
		</td>
	</tr>
	</table>
</body>
</html>