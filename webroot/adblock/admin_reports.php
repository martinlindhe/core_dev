<?
	require_once('config.php');

	$session->requireAdmin();

	$remove_cnt = 0;
	if (count($_POST)) {
		$list = getProblemSites();
		for ($i=0; $i<count($list); $i++) {
			if (!empty($_POST[ 'remove_'.$list[$i]['siteId'] ])) {
				removeProblemSite($list[$i]['siteId']);
				$remove_cnt++;
			}
		}
	}

	require('design_head.php');

	if ($remove_cnt) echo $remove_cnt.' reported sites removed!<br/><br/>';

	$list = getProblemSites();
?>
List of all current reported sites in database, oldest first (<?=count($list)?> entries):<br/>
<br/>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<?
	for ($i=0; $i<count($list); $i++)
	{
		$url = $list[$i]['url'];
		if (substr($url,0,4) != 'http') $url = 'http://'.$list[$i]['url'];
		$url = htmlentities($url);

		echo '<div class="_row_container">';
		echo '<div class="_row_col1" style="cursor: pointer;" onclick="urlOpen(\''.$url.'\')">';
		echo 'Site: ';

		echo $url.'<br/>';

		if ($list[$i]['type']) {
			echo 'Type: <b>';
			switch ($list[$i]['type']) {
				case 1: echo 'Site contains advertisement'; break;
				case 2: echo 'Blocking rules breaks the site'; break;
				default: echo 'Invalid type!';
			}
			echo '</b><br/>';
		}
			
		$comment = nl2br(htmlspecialchars($list[$i]['comment'], ENT_NOQUOTES, 'utf-8'));
		echo 'Comment: '.$comment.'<br/><br/>';

		echo 'From ';
		if (!$list[$i]['userId']) {
			echo 'Unregistered';
		} else {
			echo $list[$i]['userName'];
		}
		$ip_v4 = GeoIP_to_IPv4($list[$i]['userIP']);
		echo ', <a href="admin_ip.php?ip='.$ip_v4.'">'.$ip_v4.'</a>';

		echo ' at '.$list[$i]['timeCreated'];
		echo '</div>';	//_row_col1

		echo '<div class="_row_col2">';
		echo '<input type="checkbox" name="remove_'.$list[$i]['siteId'].'" id="remove_'.$list[$i]['siteId'].'" value="1" class="checkbox"/>';
		echo '<label for="remove_'.$list[$i]['siteId'].'">Remove</label>';
		echo '</div>';	//_row_col2
		echo '</div>';	//_row_container
	}
?>
<input type="submit" class="button" value="Remove selected"/>
</form>

<?
	require('design_foot.php');
?>