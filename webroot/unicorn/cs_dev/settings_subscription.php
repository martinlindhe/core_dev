<?
	require_once('config.php');
	$user->requireLoggedIn();

	require(DESIGN.'head.php');
?>
<div id="mainContent">

	<div class="subHead">inställningar - span</div><br class="clr"/>

	<? makeButton(false, "document.location='settings_presentation.php'", 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, "document.location='settings_fact.php'", 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, "document.location='settings_theme.php'", 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, "document.location='settings_img.php'", 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, "document.location='settings_personal.php'", 'icon_settings.png', 'personliga'); ?>
	<? makeButton(true, "document.location='settings_subscription.php'", 'icon_settings.png', 'span'); ?>
	<? makeButton(false, "document.location='settings_delete.php'", 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, "document.location='settings_vipstatus.php'", 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>

<?
	if (!$user->vip_check(VIP_LEVEL1)) {
		echo 'För att få tillgång till bevakningsfunktionen så måste du ha ett VIP-konto.<br/><br/>';
		echo '<b><a href="upgrade.php">Klicka här för mer information.</a></b>';
		include(DESIGN.'foot.php');
		die;
	}

	$list = spyGetList();
	
	if (!$list) echo 'Du har inga bevakningar.';
	
	$spy_forum = $spy_blog = $spy_gal = array();

	foreach ($list as $row)
	{
		switch ($row['type_id']) {
			case 'f':	$spy_forum[] = $row; break;
			case 'b': $spy_blog[] = $row; break;
			case 'g': $spy_gal[] = $row; break;
		}
	}

	if ($spy_forum) {
		echo '<div class="bigHeader">Forum span</div>';
		echo '<div class="bigBody"><div style="padding: 5px;"><table width="100%">';
	}
	foreach ($spy_forum as $row)
	{
		echo '<tr><td>';
		echo 'Forum - <a href="forum_read.php?id='.$row['object_id'].'">'.$row['title'].'</a></td><td width="100">';
		makeButton(false, 'goLoc(\'forum_read.php?id='.$row['object_id'].'&unsubscribe'.'\')', 'icon_delete.png', 'radera');
		echo '</td></tr>';
	}
	if ($spy_forum) echo '</table></div></div><br/>';

	if ($spy_blog) {
		echo '<div class="bigHeader">Blogg span</div>';
		echo '<div class="bigBody"><div style="padding: 5px;"><table width="100%">';
	}
	foreach ($spy_blog as $row)
	{
		echo '<tr><td>';
		echo 'Blogg - <a href="user_blog.php?id='.$row['object_id'].'">'.$row['title'].'</a></td><td width="100">';
		makeButton(false, 'goLoc(\'user_blog.php?id='.$row['object_id'].'&unsubscribe'.'\')', 'icon_delete.png', 'radera');
		echo '</td></tr>';
	}
	if ($spy_blog) echo '</table></div></div><br/>';
	
	if ($spy_gal) {
		echo '<div class="bigHeader">Galleri span</div>';
		echo '<div class="bigBody"><div style="padding: 5px;"><table width="100%">';
	}
	foreach ($spy_gal as $row)
	{
		echo '<tr><td>';
		echo 'Gallieri - <a href="user_gallery.php?id='.$row['object_id'].'">'.$row['title'].'</a></td><td width="100">';
		makeButton(false, 'goLoc(\'user_gallery.php?id='.$row['object_id'].'&unsubscribe'.'\')', 'icon_delete.png', 'radera');
		echo '</td></tr>';
	}
	if ($spy_gal) echo '</table></div></div>';

?>
	</div>
</div>
	
<?
	include(DESIGN.'foot.php');
?>
