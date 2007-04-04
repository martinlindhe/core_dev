<?
	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: index.php');
		die;
	}

	if (!empty($_POST['rule'])) {
		$ruleId = addAdblockRule($db, $_SESSION['userId'], $_POST['rule'], $_POST['type'], $_POST['sampleurl']);
		
		//todo: checkbox "make comment private"
		include('design_head.php');
		if (is_numeric($ruleId) && $ruleId) {
			logEntry($db, 'Adblock rule # '.$ruleId.' created');
			$private = false;
			if (isset($_POST['commentprivate'])) $private = true;
			addComment($db, COMMENT_ADBLOCKRULE, $ruleId, $_POST['comment'], $private);

			echo 'Rule <b>'.$_POST['rule'].'</b> added successfully!<br/><br/>';
			echo '<a href="editrule.php?id='.$ruleId.'">Edit the new rule</a><br/><br/>';

			//todo: list already existing similar rules

		} else {
			echo 'Failed to add the rule <b>'.$_POST['rule'].'</b><br/><br/>';
		}
		echo '<a href="'.$_SERVER['PHP_SELF'].'">Create another rule</a>';
		include('design_foot.php');
		die;
	}

	include('design_head.php');
	
	echo getInfoField($db, 'page_new_adblock_rule').'<br/>';
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" name="newrule">
<table width="500" cellpadding="0" cellspacing="0" border="0">
	<tr><td width="20">&nbsp;</td><td class="centermenu">
		New rule:<br/>
		<input type="text" name="rule" size="86"/><br/>
		<br/>
		Sample URL:<br/>
		<input type="text" name="sampleurl" size="86"/><br/>
		<br/>
		Comment:<br/>
		<textarea name="comment" rows="8" cols="84"></textarea><br/>
		<input type="checkbox" name="commentprivate"/>Make this comment private<br/>
		<br/>
	
		Type of rule:<br/>
		<select name="type">
			<option value="0">Unknown &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</option>
			<option value="1" selected="selected">Advertising</option>
			<option value="2">Tracker</option>
			<option value="3">Counter</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Add rule"/>
	</td></tr>
</table>
</form>

<script type="text/javascript">
document.newrule.rule.focus();
</script>

<?
	include('design_foot.php');
?>