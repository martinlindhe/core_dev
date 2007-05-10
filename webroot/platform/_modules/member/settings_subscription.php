<?
	require(CONFIG.'cut.fnc.php');
	require(CONFIG.'secure.fnc.php');

	require_once('settings.fnc.php');

	require_once(dirname(__FILE__).'/../user/spy.fnc.php');

	require(DESIGN.'head.php');
?>
<div id="mainContent">

	<img src="/_gfx/ttl_settings.png" alt="Inställningar"/><br/><br/>

	<? makeButton(false, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(true, 'goLoc(\''.l('member', 'settings', 'subscription').'\')', 'icon_settings.png', 'bevakningar'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<br/><br/><br/>

<?
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
		echo '<div class="centerMenuHeader">Forum-bevakningar</div>';
		echo '<div class="centerMenuBodyWhite"><div style="padding: 5px;"><table width="100%">';
	}
	foreach ($spy_forum as $row)
	{
		echo '<tr><td>';
		echo 'Forum - <a href="'.l('forum', 'read', $row['object_id']).'">'.$row['title'].'</a></td><td width="100">';
		makeButton(false, 'goLoc(\''.l('forum', 'read', $row['object_id']).'&unsubscribe'.'\')', 'icon_delete.png', 'sluta bevaka');
		echo '</td></tr>';
		
	}
	if ($spy_forum) echo '</table></div></div><br/>';

	if ($spy_blog) {
		echo '<div class="centerMenuHeader">Blogg-bevakningar</div>';
		echo '<div class="centerMenuBodyWhite"><div style="padding: 5px;"><table width="100%">';
	}
	foreach ($spy_blog as $row)
	{
		echo '<tr><td>';
		echo 'Blogg - <a href="'.l('user', 'blog', $row['object_id']).'">'.$row['title'].'</a></td><td width="100">';
		makeButton(false, 'goLoc(\''.l('user', 'blog', $row['object_id']).'&unsubscribe'.'\')', 'icon_delete.png', 'sluta bevaka');
		echo '</td></tr>';
	}
	if ($spy_blog) echo '</table></div></div><br/>';
	
	if ($spy_gal) {
		echo '<div class="centerMenuHeader">Galleri-bevakningar</div>';
		echo '<div class="centerMenuBodyWhite"><div style="padding: 5px;"><table width="100%">';
	}
	foreach ($spy_gal as $row)
	{
		echo '<tr><td>';
		echo 'Gallieri - <a href="'.l('user', 'gallery', $row['object_id']).'">'.$row['title'].'</a></td><td width="100">';
		makeButton(false, 'goLoc(\''.l('user', 'gallery', $row['object_id']).'&unsubscribe'.'\')', 'icon_delete.png', 'sluta bevaka');
		echo '</td></tr>';
	}
	if ($spy_gal) echo '</table></div></div>';

?>
	</div>
</div>
	
<?
	include(DESIGN.'foot.php');
?>