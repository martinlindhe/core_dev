<?
	require_once('config.php');

	$page = 1;
	if (!empty($_GET['p']) && is_numeric($_GET['p'])) $page = $_GET['p'];

	$l = 25;

	//s=search phrase, search the adblock rules
	$search = '';
	if (!empty($_GET['s'])) $search = $_GET['s'];
	if (!empty($_POST['s'])) $search = $_POST['s'];

	
	$sortByTime = 0;
	if (!empty($_POST['sortbytime'])) $sortByTime = 1;

	if ($search || !empty($_POST['t0']) || !empty($_POST['t1']) || !empty($_POST['t2']) || !empty($_POST['t3'])) {
		@$types = $_POST['t0'].','.$_POST['t1'].','.$_POST['t2'].','.$_POST['t3'];
		$list = searchAdblockRules($db, $search, $types, $page, $l, $sortByTime);
		$totRules = searchAdblockRuleCount($db, $search);	//fixme: count ignorerar $types
	} else {
		$list = getAdblockRules('', $page, $l);
		$totRules = getAdblockRulesCount();
	}
	
	$totPages = round($totRules / $l+0.5); // round to closest whole number

	require('design_head.php');
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr><td class="centermenu" valign="bottom">
<?
	echo getInfoField('page_ruleset');

	if ($search) {
		echo 'Search results for: "'.$search.'"<br/><br/>';
	} else {
		echo 'Displaying full list of adblock rules<br/><br/>';
	}
	echo 'Page '.$page.' of '.$totPages.' ('.count($list).' this page, '.$totRules.' total)<br/><br/>';

	if ($totPages > 1) {

		if ($page > 1) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.($page-1);
			if ($search) echo '&s='.$search;
			echo '" title="Previous page"><img src="gfx/arrow_prev.png" alt="Previous" width="11" height="12"/></a>';
		} else {
				echo '<img src="gfx/arrow_prev_gray.png" alt="" width="11" height="12"/>';
		}

		for ($i=1; $i<=$totPages; $i++) {
			if ($i==$page) echo '<b>';
			echo ' <a href="'.$_SERVER['PHP_SELF'].'?p='.$i;
			if ($search) echo '&s='.$search;
			echo '">'.$i.'</a>';
			if ($i==$page) echo '</b>';
			echo ' ';
		}
		if ($page < $totPages) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.($page+1);
			if ($search) echo '&s='.$search;
			echo '" title="Next page"><img src="gfx/arrow_next.png" alt="Next" width="11" height="12"/></a>';
		} else {
			echo '<img src="gfx/arrow_next_gray.png" alt="" width="11" height="12"/>';
		}
	}

	$t1check = 1; if (!isset($_POST['t2'])) $t1check = 0;
	$t2check = 1;	if (!isset($_POST['t2'])) $t2check = 0;
	$t3check = 1;	if (!isset($_POST['t3'])) $t3check = 0;
	$t0check = 1;	if (!isset($_POST['t0'])) $t0check = 0;
?>
	</td>
	<td width="320">

		<form method="post" name="lf" action="<?=$_SERVER['PHP_SELF']?>">
		<table width="100%" cellpadding="5" cellspacing="0" border="1">
			<tr><td align="right" class="centermenu">
				Advertisment<input name="t1" value="1" type="checkbox" class="checkbox"<? if ($t1check) echo ' checked'; ?>/><br/>
				Tracking<input name="t2" value="2" type="checkbox" class="checkbox"<? if ($t2check) echo ' checked'; ?>/><br/>
				Counter<input name="t3" value="3" type="checkbox" class="checkbox"<? if ($t3check) echo ' checked'; ?>/><br/>
				Unknown<input name="t0" value="0" type="checkbox" class="checkbox"<? if ($t0check) echo ' checked'; ?>/>
			</td>
			<td align="right" valign="top" width="180">
				<input type="text" name="s" value="<?=$search?>" size="14"/> 
				<input type="submit" value="Search"/><br/>
				<br/>
				<select name="l">
					<option value="15"<?if($l==15)echo ' selected="selected"'?>>15 per page</option>
					<option value="25"<?if($l==25)echo ' selected="selected"'?>>25 per page</option>
					<option value="40"<?if($l==40)echo ' selected="selected"'?>>40 per page</option>
					<option value="100"<?if($l==100)echo ' selected="selected"'?>>100 per page</option>
					<option value="500"<?if($l==500)echo ' selected="selected"'?>>500 per page</option>
					<option value="0"<?if($l==0)echo ' selected="selected"'?>>Show all</option>
				</select>&nbsp;<input type="submit" value="Set"/><br/>
				<br/>
				Sort by time created <input type="checkbox" name="sortbytime" value="1" class="checkbox"<? if ($sortByTime) echo ' checked="checked"'; ?>/>
			</td></tr>
		</table>
		</form>

	</td></tr>
</table>
<br/>
<?
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