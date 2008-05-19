<?php

require_once('config.php');

//s=search phrase, search the adblock rules
$search = '';
if (!empty($_GET['s'])) $search = $_GET['s'];
if (!empty($_POST['s'])) $search = $_POST['s'];

@$types = $_POST['t0'].','.$_POST['t1'].','.$_POST['t2'].','.$_POST['t3'];

$tot_cnt = searchAdblockRuleCount($search, $types);
$pager = makePager($tot_cnt, 25, ($search ? '&amp;s='.$search : '') );

$list = searchAdblockRules($search, $types, $pager['limit'], !empty($_POST['sortbytime']));

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
	<tr><td align="right">
		<label for="t1">Advertisment</label><input name="t1" id="t1" value="1" type="checkbox" class="checkbox"<? if (!empty($_POST['t1'])) echo ' checked="checked"'; ?>/><br/>
		<label for="t2">Tracking</label><input name="t2" id="t2" value="2" type="checkbox" class="checkbox"<? if (!empty($_POST['t2'])) echo ' checked="checked"'; ?>/><br/>
		<label for="t3">Counter</label><input name="t3" id="t3" value="3" type="checkbox" class="checkbox"<? if (!empty($_POST['t3'])) echo ' checked="checked"'; ?>/><br/>
		<label for="t0">Unknown</label><input name="t0" id="t0" value="0" type="checkbox" class="checkbox"<? if (!empty($_POST['t4'])) echo ' checked="checked"'; ?>/>
	</td>
	<td align="right" valign="top" width="180">
		<input type="text" name="s" value="<?=$search?>" size="13"/>
		<input type="submit" class="button" value="Search"/><br/>
		<br/>
		<label for="sortbytime">Show newest first</label>
		<input type="checkbox" name="sortbytime" id="sortbytime" value="1" class="checkbox"<? if (!empty($_POST['sortbytime'])) echo ' checked="checked"'; ?>/>
	</td></tr>
</table>
</form>
<br/>
<?php

echo $pager['head'];

echo '<table>';
echo '<tr>';
echo '<th>Rule</th>';
echo '<th>Category</th>';
echo '</tr>';
$i = 0;
foreach ($list as $row) {
	$i++;
	echo '<tr style="background-color:'.($i%2 ? '#eee' : '#ccc').'">';
	if ($search) {
		$rule = str_replace($search, '<b>'.$search.'</b>', $row['ruleText']);
	} else {
		$rule = $row['ruleText'];
	}
	echo '<td><a href="editrule.php?id='.$row['ruleId'].'">'.$rule.'</a></td>';
	echo '<td>'.$ruleset_types[ $row['ruleType'] ].'</td>';
	echo '</tr>';
}
echo '</table>';

require('design_foot.php');
?>
