<?
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');
	$id = $_GET['id'];

	if ($id == $user->id) popupACT('Du kan inte blocka dig själv.');

	if (!empty($_POST['do'])) {
		blockRelation($id);
		popupACT('Nu har du blockerat personen.');
	}
	require(DESIGN.'head_popup.php');
?>
<body>
<form name="msg" action="<?=$_SERVER['PHP_SELF'].'?id='.$id?>" method="post">
<input type="hidden" name="do" value="1" />
		<div class="popupWholeContent cnti mrg">
			<div class="smallHeader"><h4>blockera</h4></div>
			<div class="smallBody">
			<table cellspacing="0" style="height: 150px;"><tr><td style="height: 150px; vertical-align: middle;">
blockera:<br /><span><?=$user->getstring($id, '', array('nolink' => true))?></span>
<br /><br /><p class="lft">varken du eller personen kommer att kunna kontakta varandra här på sidan. du kan när som helst ta bort din blockering under vänner / ovänner.</p><br /><b>fortsätt?</b><br /><br />
			</td></tr></table>
				<input type="submit" class="btn2_min r" value="blockera!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>
</body>
</html>
