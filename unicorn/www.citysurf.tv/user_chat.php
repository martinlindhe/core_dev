<?
	require_once('config.php');
	$user->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('ingen mottagare');
	$id = $_GET['id'];

	if (amIBlocked($id)) errorACT('Användaren har blockerat dig.');

	if ($user->id == $id) popupACT('Du kan inte skicka till dig själv.');
	$user->blocked($id);
	
	$s = $user->getuser($id);
	
	$isFriends = $user->isFriends($id);
	$closed = false; //Default
	
	if(!$isFriends) {
		$onlL = $user->getinfo($user->id, 'private_chat');
		$onlS = $user->getinfo($id, 'private_chat');
		if ($onlL) {
			$closed = true;
			$n = 'Otillgänglig';
		} else if ($onlS) {
			$closed = true;
			$n = 'Otillgänglig';
		}
	}

	$NAME_TITLE = secureOUT($_SESSION['data']['u_alias']).' | privatchat';

	//$charset = 'ISO-8859-1';
	require(DESIGN.'head_popup.php');
?>
<script type="text/javascript">
<?
if(!$closed)
echo '
var id = \''.$id.'\';
var usr = \''.($s['u_alias']).'\';
var you = \''.($_SESSION['data']['u_alias']).'\';
';
else
echo '
var id = \''.$id.'\';
var usr = \''.($n.' användare').'\';
var you = \''.($_SESSION['data']['u_alias']).'\';
';
?>
</script>
<script type="text/javascript" src="<?=$config['web_root']?>js/xml_chat.js"></script>
<script type="text/javascript">document.onkeydown = ActivateByKey;</script>

<div class="boxMid1">

	<div class="subHead">chat</div><br class="clr"/>
	<div class="boxMid1mid">
		<table summary="" cellspacing="0" style="width: 510px;">
		<tr>
				<td style="padding: 6px 6px 0 6px;">
					<iframe name="msgs" src="user_chatwin.php?id=<?=$id?>" frameborder="no" scrolling="auto" style="width:100%; height:270px;"></iframe>
				</td>
				<td class="rgt">
					<?=(!$closed)?$user->getstring($s, '', array('nolink'=>1)):$n.' användare';?><br/><br/>
					<?=(!$closed)?$user->getimg($id, 1, '', 1):'';?>
				</td>
			</tr>
			<tr>
				<td class="btm" style="padding-top: 6px; padding-left: 5px;">
					<b>Meddelande:</b>&nbsp;&nbsp;&nbsp;(<span id="cha_lim">250</span> tecken kvar)<br />
					<textarea name="msgTextbox"<?=($closed)?' readonly':'';?> class="txt" id="msgTextbox" onblur="gotFocus = false;" onclick="taskStop();" onfocus="taskStop();" style="height: 50px; width: 330px;" onkeyup="fixlimit(this, 'cha_lim');" onkeydown="fixlimit(this, 'cha_lim');" onchange="fixlimit(this, 'cha_lim');"></textarea>
				</td>
				<td class="btm">
					<? makeButton(false, 'doMSG();', 'icon_qchat.png', 'Skicka'); ?>
				</td>
			</tr>
		</table>
	</div>

</div>

</body>
</html>
