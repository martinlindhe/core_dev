</div>	<!-- end mainContent -->

<div id="rightMenu">

<?
	$online = gettxt('stat_online');
	$online = explode(':', $online);
?>
	<div class="smallHeader">inloggade</div>
	<div class="smallBody">

		<table summary="" cellspacing="0">
			<tr><td width="80"><a href="<?=l('list', 'users', '1')?>">Online</a></td><td><a href="<?=l('list', 'users', 1)?>"><?=@intval($online[0])?></a></td></tr>
			<tr><td><a href="<?=l('list', 'users', 'M')?>">Killar</a></td><td><a href="<?=l('list', 'users', 'M')?>"><?=@intval($online[1])?></a></td></tr>
			<tr><td><a href="<?=l('list', 'users', 'F')?>">Tjejer</a></td><td><a href="<?=l('list', 'users', 'F')?>"><?=@intval($online[2])?></a></td></tr>
		</table>
		<br/>
		<a href="<?=l('list', 'users')?>">Senast inloggade</a><br/>
		<a href="/list/userfind/1">Slumpa</a><br/>
		<div onmouseover="checkTime(1);">Snabbsök</div>

		<div id="userfind" style="display: none" onmouseover="checkTime(1);">
			<form action="/list/userfind" method="post">
			<input type="text" class="txt" id="userfind_inp" onfocus="checkTime(1);" onblur="checkTime(0);" name="a" value="" />
			</form>
		</div>
	</div><br/>


<? if (defined('U_GBWRITE')) { ?>
	<div class="smallHeader">skriv i gästboken</div>
	<div class="smallBody">
		<form name="msg" action="<?=l('user', 'gbwrite', $s['id_id'])?>main=1" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert('Felaktigt meddelande: Minst 2 tecken!'); this.ins_cmt.select(); return false; }">
		<textarea class="txt msgWrite" name="ins_cmt"></textarea>
		<?
			if ($user->vip_check(VIP_LEVEL1)) echo '<input type="checkbox" name="ins_priv" id="ins_priv"><label for="ins_priv">Privat (VIP)</label>';
		?>
		<input type="submit" class="btn2_sml r" value="skicka!" /><br class="clr" />
		</form>
	</div><br/>
<? }

	if ($l && !defined('NO_FOL')) {
?>
		<div id="friendsOnline">
			<div class="smallHeader" onclick="friendsToggle();">
				vänner online (<span id="friendsOnlineCount">0</span>)
			</div>
			<div id="friendsOnlineList" class="smallBody" style="display:none;"></div>
		</div>
		<br class="clr"/>
		<script type="text/javascript">
		executeTimeout();
		executeData('<?=@$_SESSION['data']['cachestr']?>');
		<?= (!empty($_COOKIE['friendsOnline'])?"friendsToggle();":'')?>
		</script>
<? }

	if(defined('U_VISIT')) {
		$res = $db->getArray('SELECT o.visit_date, u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd FROM s_uservisit o INNER JOIN s_user u ON u.id_id = o.visitor_id AND u.status_id = "1" WHERE o.user_id = "'.$s['id_id'].'" ORDER BY o.main_id DESC LIMIT '.(isset($_GET['more'])?'10':'5'));
		echo '<a name="visit"></a>';
		if ($own && $user->vip_check(VIP_LEVEL2)) {
			echo '<div class="smallHeader">besökare (<a href="'.l('user', 'view', $s['id_id']).(!isset($_GET['more'])?'&amp;more#visit">fler':'#visit">färre').'</a>)</div>';
		} else {
			echo '<div class="smallHeader">besökare</div>';
		}
		echo '<div class="smallBody">';
		echo '<ul class="friends_list">';
		if(!empty($res) && count($res)) {
			$i = 0;
			$nl = true;
			if(isset($_GET['more'])) {
			foreach($res as $row) {
				echo '<li>'.$user->getstring($row).'<br /><br />besökte: '.nicedate($row['visit_date']).'</li>';
				$i++;
			} } else {
			foreach($res as $row) {
				echo '<li>'.$user->getstring($row).'</li>';
				$i++;
			} }
		} else {
			echo '<li>Inga besökare.</li>';
		}
		echo '</ul></div><br/>';
	}

	require('foot.php');
?>
</div>	<!-- end rightMenu -->