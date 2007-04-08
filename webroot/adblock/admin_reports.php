<?
	require_once('config.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: index.php');
		die;
	}

	require('design_head.php');

	if (count($_POST)) {
		$list = getProblemSites($db);
		$remove_cnt = 0;
		for ($i=0; $i<count($list); $i++) {
			if (!empty($_POST[ 'remove_'.$list[$i]['siteId'] ])) {
				removeProblemSite($db, $_SESSION['userId'], $list[$i]['siteId']);
				$remove_cnt++;
			}
		}
		if ($remove_cnt) JS_Alert($remove_cnt.' reported sites removed!');
	}

	$list = getProblemSites();
?>
List of all current reported sites in database, oldest first (<?=count($list)?> entries):<br/>
<br/>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>">

<?
	for ($i=0; $i<count($list); $i++) {
		//fixme:
		
		$classname = 'objectNormal';
		if ($list[$i]['timeCreated'] < (time()-((7*24)*3600)) ) $classname = 'objectCritical'; // if timeAdded > 7 days mark background in other color
		echo '<table width="100%" cellpadding="0" cellspacing="0">';
			echo '<tr><td colspan="2" class="'.$classname.'">';

				echo 'Site: ';
				if (substr($list[$i]['url'],0,4) != 'http') $list[$i]['url'] = 'http://'.$list[$i]['url'];
				$list[$i]['url'] = htmlentities($list[$i]['url']);

				echo '<a href="'.$list[$i]['url'].'" target="_blank">'.$list[$i]['url'].'</a><br/>';

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
				echo 'Comment: '.$comment.'<br/><img src="gfx/c.gif" width="1" height="10" alt=""/>';
			echo '</td></tr>';

			echo '<tr><td class="'.$classname.'">';

				echo 'From ';
				if (!$list[$i]['userId']) {
					echo 'Unregistered';
				} else {
					echo $list[$i]['userName'];
				}
				$ip_v4 = GeoIP_to_IPv4($list[$i]['userIP']);
				echo ' from <a href="admin_ip.php?ip='.$ip_v4.'">'.$ip_v4.'</a>';

				echo ' <img src="../flags/'.GeoIP_ci_to_CountryShort($list[$i]['ci']).'.png" align="top" width="16" height="11" title="'.GeoIP_ci_to_Country($list[$i]['ci']).'" alt=""/> ';
				echo $list[$i]['timeCreated'];
			echo '</td>';
			echo '<td width="90" class="'.$classname.'" align="right"><input type="checkbox" name="remove_'.$list[$i]['siteId'].'" value="1" class="checkbox"/>Remove&nbsp;</td></tr>';

		echo '</table>';
		echo '<img src="gfx/c.gif" width="1" height="10" alt=""/><br/>';
	}
?>

<input type="submit" value="Remove selected"/>
</form>

<?
	require('design_foot.php');
?>