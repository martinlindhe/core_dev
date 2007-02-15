<?
	include_once('include_all.php');

	if ($_SESSION['isAdmin'] && !empty($_GET['remove']) && is_numeric($_GET['remove'])) {
		include('design_head.php');
		
		if (!isset($_GET['confirmed'])) {
?>
			Are you sure you want to delete this rule?<br><br>
			<a href="<?=$_SERVER['PHP_SELF'].'?remove='.$_GET['remove'].'&confirmed'?>">Yes, I am sure</a><br>
			<br>
			<a href="<?=$_SERVER['PHP_SELF'].'?id='.$_GET['remove']?>">No, wrong button</a><br>
<?
		} else {
			//remove rule
			removeAdblockRule($db, $_SESSION['userId'], $_GET['remove']);
			
			echo 'Rule successfully removed!<br><br>';
			echo '<a href="ruleset.php">Return to rules overview</a><br>';
		}
		include('design_foot.php');
		die;
	}

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$ruleId = $_GET['id'];

	if ($_SESSION['isAdmin'] && !empty($_POST['rule']) && !empty($_POST['type']) && isset($_POST['sampleurl'])) {
		updateAdblockRule($db, $_SESSION['userId'], $ruleId, $_POST['rule'], $_POST['type'], $_POST['sampleurl']);
	}
		
	if (!empty($_POST['comment'])) {
		$private = false;
		if (!empty($_POST['commentprivate'])) $private = true;
		addComment($db, COMMENT_ADBLOCKRULE, $ruleId, $_POST['comment'], $private);
	}
	
	/* Delete comments */
	if ($_SESSION['isAdmin'] && !empty($_GET['deletecomment'])) {
		deleteComment($db, $_GET['deletecomment']);
	}
	
	$rule = getAdblockRule($db, $ruleId);
	if (!$_SESSION['isSuperAdmin'] && $rule['deletedBy']) {
		header('Location: index.php');
		die;
	}

	include('design_head.php');
	
	if (!$rule || $rule['deletedBy']) {
	
		if ($rule['deletedBy']) echo '<span style=background-color:#FF6666>Error:</span> This rule has been deleted by '.getUserName($db, $rule['deletedBy']).' at '.$rule['timeDeleted'].'.<br><br>';
		if (!$rule) echo '<span style=background-color:#FF6666>Error:</span> No such rule exists<br><br>';

		include('design_foot.php');
		die;
	}
	
	if ($_SESSION['isAdmin']) {
?>
Edit rule # <?=$ruleId?>:<br><br>
<form method="post" action="<?=$_SERVER['PHP_SELF'].'?id='.$ruleId?>" name="editrules">
<table width="100%" cellpadding=0 cellspacing=0 border=0>
	<tr><td>
		Current rule:<br>
		<input type="text" name="rule" value="<?=$rule['ruleText']?>" size=90><br>
		<br>
		Sample URL:<br>
		<input type="text" name="sampleurl" value="<?=$rule['sampleUrl']?>" size=90><br>
		<br>
		Type of rule:<br>
		<select name="type">
			<option value="0"<? if ($rule['ruleType']==0) echo ' selected';?>>Unsorted &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			<option value="1"<? if ($rule['ruleType']==1) echo ' selected';?>>Advertising
			<option value="2"<? if ($rule['ruleType']==2) echo ' selected';?>>Tracker
			<option value="3"<? if ($rule['ruleType']==3) echo ' selected';?>>Counter
		</select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Save changes"><img src="gfx/c.gif" width=280 height=1>
		<a href="<?=$_SERVER['PHP_SELF']?>?remove=<?=$ruleId?>"><img src="gfx/delete.png" align="top" width=16 height=16 title="Delete rule"></a><br>
		<br>
	</td></tr>
</table>
</form>
<?
	} else {
		echo 'Rule # '.$ruleId.':<br><br>';
		echo 'Current rule:<br>';
		echo $rule['ruleText'].'<br><br>';

		echo 'Sample URL:<br>';
		echo $rule['sampleUrl'].'<br><br>';
		
		echo 'Type of rule:<br>';
		switch ($rule['ruleType']) {
			case 0: echo 'Unsorted'; break;
			case 1: echo 'Advertising'; break;
			case 2: echo 'Tracker'; break;
			case 3: echo 'Counter'; break;
			default: echo 'INVALID DATA'; break;
		}
		echo '<br><br>';
	}

	if ($_SESSION['isAdmin']) {
		$list = getComments($db, COMMENT_ADBLOCKRULE, $ruleId, true);	//get private comments
	} else {
		$list = getComments($db, COMMENT_ADBLOCKRULE, $ruleId, false);
	}

	if (count($list)) {
		echo '<b>';
		if (count($list) == 1) echo 'one comment:';
		else echo count($list).' comments:';
		echo '</b><br><br>';

		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['commentPrivate']) echo '<span style="background-color: #FF6666;"><b>Private comment:</b><br>';
			echo nl2br($list[$i]['commentText']).'<br>';
			
			if ($list[$i]['userName']) $name = $list[$i]['userName'];
			else $name = 'Unregistered';
			echo '<span style="color: #606060;"><i>Written by '.$name;
			echo ' at '.$list[$i]['timeCreated'].'</i></span>';
			if ($_SESSION['isAdmin']) {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$ruleId.'&deletecomment='.$list[$i]['commentId'].'" title="Delete comment"><img src="gfx/delete.png" width=16 height=16></a>';
			}
			if ($list[$i]['commentPrivate']) echo '</span>';
			echo '<br><br>';
		}
	}
	echo 'Enter a new comment:';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$ruleId.'" name="ruleAddComment">';
	echo '<textarea name="comment" rows=8 cols=87></textarea><br>';
	echo '<input type="checkbox" name="commentprivate">Make this comment private<br>';
	echo '<input type="submit" value="Save comment">';
	echo '</form><br>';

	echo 'Created by '.$rule['creatorName'].' at '.$rule['timeCreated'].'<br>';
	if ($rule['editorId']) echo '<b>Last edited by '.$rule['editorName'].' at '.$rule['timeEdited'].'</b><br>';
?>
<script type="text/javascript">
document.ruleAddComment.comment.focus();
</script>
<?
	include('design_foot.php');
?>