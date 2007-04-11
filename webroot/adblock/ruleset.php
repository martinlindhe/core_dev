<?
	require_once('config.php');

	//s=search phrase, search the adblock rules
	$search = '';
	if (!empty($_GET['s'])) $search = $_GET['s'];
	if (!empty($_POST['s'])) $search = $_POST['s'];

	@$types = $_POST['t0'].','.$_POST['t1'].','.$_POST['t2'].','.$_POST['t3'];
	$totRules = searchAdblockRuleCount($search, $types);

	$pager = makePager($totRules, 25, ($search ? '&s='.$search : ''));

	$list = searchAdblockRules($search, $types, $pager['page'], $pager['items_per_page'], !empty($_POST['sortbytime']));

	require('design_head.php');

	wiki('Ruleset');

	if ($search) {
		echo 'Search results for: "'.$search.'"<br/><br/>';
	} else {
		echo 'Displaying full list of adblock rules<br/><br/>';
	}
?>
	<form method="post" name="lf" action="<?=$_SERVER['PHP_SELF']?>">
	<table cellpadding="5" cellspacing="0" border="1">
		<tr><td align="right" class="centermenu">
			<label for="t1">Advertisment</label>
			<input name="t1" id="t1" value="1" type="checkbox" class="checkbox"<? if (!empty($_POST['t1'])) echo ' checked'; ?>/><br/>

			<label for="t2">Tracking</label>
			<input name="t2" id="t2" value="2" type="checkbox" class="checkbox"<? if (!empty($_POST['t2'])) echo ' checked'; ?>/><br/>

			<label for="t3">Counter</label>
			<input name="t3" id="t3" value="3" type="checkbox" class="checkbox"<? if (!empty($_POST['t3'])) echo ' checked'; ?>/><br/>
				
			<label for="t0">Unknown</label>
			<input name="t0" id="t0" value="0" type="checkbox" class="checkbox"<? if (!empty($_POST['t4'])) echo ' checked'; ?>/>
		</td>
		<td align="right" valign="top" width="180">
			<input type="text" name="s" value="<?=$search?>" size="13"/> 
			<input type="submit" value="Search"/><br/>
			<br/>
			<label for="sortbytime">Show newest first</label>
			<input type="checkbox" name="sortbytime" id="sortbytime" value="1" class="checkbox"<? if (!empty($_POST['sortbytime'])) echo ' checked="checked"'; ?>/>
		</td></tr>
	</table>
	</form>
	<br/>
<?
	echo $pager['head'];

	echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
	for ($i=0; $i<count($list); $i++) {
		$classname='objectNormal';
		if ($list[$i]['ruleType'] == 0) $classname='objectCritical';
		echo '<tr class="'.$classname.'" onmouseover="this.style.backgroundColor=\'#AAC8E4\';" onmouseout="this.style.backgroundColor=\'\';">';

		echo '<td>';
			if ($search) {
				$rule = str_replace($search, '<b>'.$search.'</b>', $list[$i]['ruleText']);
			} else {
				$rule = $list[$i]['ruleText'];
			}		
			echo '<a href="editrule.php?id='.$list[$i]['ruleId'].'">'.$rule.'</a>';
		echo '</td>';

		echo '<td class="'.$classname.'" width="90">';
		switch ($list[$i]['ruleType']) {
			case 0: echo 'Unknown'; break;
			case 1: echo 'Advertisment'; break;
			case 2: echo 'Tracking'; break;
			case 3: echo 'Counter'; break;
			default: echo 'INVALID RULE TYPE '.$list[$i]['ruleType'];
		}
		echo '</td></tr>';
	}
	echo '</table>';

	require('design_foot.php');
?>