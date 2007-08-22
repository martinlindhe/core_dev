<?
	require_once('find_config.php');

	if (!$isCrew && strpos($_SESSION['u_a'][1], 'obj') === false) errorNEW('Ingen behörighet.');

	//require("./set_formatadm.php");
	
	$page = 'OBJEKT';
	$menu = $menu_OBJECT;
	$status = (!empty($_GET['status']))?$_GET['status']:'';

	if ($status == 'thought' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_tho') !== false)) 			require("obj_thought.php");
	else if ($status == 'cmt' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pcm') !== false)) 		require("obj_cmt.php");
	else if ($status == 'cmtmv' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_mcm') !== false))		require("obj_cmtmv.php");
	else if ($status == 'scc' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false)) 		require("obj_scc.php");
	else if ($status == 'full' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_full') !== false)) 	require("obj_full.php");
	else if ($status == 'ue' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_ue') !== false)) 			require("obj_ue.php");
	else if ($status == 'img' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pimg') !== false)) 		require("obj_img.php");
	else if ($status == 'tele' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_tele') !== false))		require("obj_tele.php");
	else if ($status == 'photo' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pho') !== false))		require("obj_photo.php");
	else if ($status == 'event' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_event') !== false)) require("obj_event.php");
	else if ($status == 'sms' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_sms') !== false)) 		require("obj_sms.php");
	else if ($status == 'gb' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_gb') !== false)) 			require("obj_gb.php");
	else if ($status == 'mail' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_mail') !== false)) 	require("obj_mail.php");
	else if ($status == 'chat' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_chat') !== false)) 	require("obj_chat.php");
	else if ($status == 'blog' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_blog') !== false)) 	require("obj_blog.php");
	else if ($status == 'abuse' && ($isCrew || strpos($_SESSION['u_a'][1], 'obj_abuse') !== false)) require("obj_abuse.php");
	else require("obj_head.php");

?>
		</td>
	</tr>
	</table>
</body>
</html>
