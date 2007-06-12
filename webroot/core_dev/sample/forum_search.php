<?
	include('include_all.php');

	if (isset($_GET['p'])) {
		$page = $_GET['p'];
	} else {
		$page = 1;
	}

	if (isset($_GET['l'])) {
		$limit = $_GET['l'];
	} else {
		$limit = $config['forum']['search_results_per_page'];
	}

	setUserStatus($db, 'S&ouml;ker efter inl&auml;gg');

	include('design_head.php');
	include('design_forum_head.php');
	
	$content = '';

	if (isset($_POST['c']) || isset($_GET['c'])) {
		if (isset($_POST['c'])) {
			$orgcrit = $_POST['c'];
		} else {
			$orgcrit = $_GET['c'];
		}

		if (isset($_GET['m'])) {
			$method = $_GET['m'];
		} else if (isset($_POST['m'])) {
			$method = $_POST['m'];
		} else {
			$method = 'newfirst';
		}

		$criteria = substr($orgcrit, 0, 30); //chop off too long search queries

		$list = getForumSearchResults($db, $criteria, $method, $page, $limit);
		$hits = getForumSearchResultsCount($db, $criteria);

		$content .= 'Viser s&oslash;kerresultat side '.$page.' av '.round(($hits/$limit)+0.49).', '.$limit.' innlegg per side.<br>';
		$content .= $hits.' treffar p&aring; "'.$criteria.'", ';

		switch ($method) {
			case 'oldfirst':
				$content .= 'eldste innlegg f&oslash;rst.';
				break;

			case 'newfirst':
				$content .= 'nyeste innlegg f&oslash;rst.';
				break;

			case 'mostread':
			default:
				$content .= 'mest lest f&oslash;rst.';
				break;
		}

		$content .= '<br><br>';

		for ($i=0; $i<count($list); $i++) {
			//$content .= showForumPost($db, $list[$i], 'S&ouml;kresultat #'.($i+1), false, $criteria).'<br>';
			$content .= showForumPost($db, $list[$i], 'S&ouml;kresultat #'.($i+1), false).'<br>';
		}

		$criteria = urlencode($criteria);

		$content .= 'Side: ';

		if ($page > 1) {
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.($page-1).'&l='.$limit.'&m='.$method.'">&laquo;</a> ';
		}

		for ($i=1; $i<=round(($hits/$limit)+0.49); $i++) {
			if ($i == $page) $content .= '<b>';
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$i.'&l='.$limit.'&m='.$method.'">'.$i.'</a> ';
			if ($i == $page) $content .= '</b>';
		}

		if ($page < ($hits/$limit)) {
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.($page+1).'&l='.$limit.'&m='.$method.'">&raquo;</a>';
		}

		$content .= '<br>';
		$content .= 'Antall per side: ';
		for ($i=5; $i<=20; $i+=5) {
			if ($limit == $i) $content .= '<b>';
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$i.'&m='.$method.'">'.$i.'</a> ';
			if ($limit == $i) $content .= '</b>';
		}
		$content .= '<br><br>';

		$content .= 'S&oslash;kemetode:<br>';

		if ($method == 'newfirst') $content .= '<b>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$limit.'&m=newfirst">Nyeste f&oslash;rst</a><br>';
		if ($method == 'newfirst') $content .= '</b>';

		if ($method == 'oldfirst') $content .= '<b>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$limit.'&m=oldfirst">Eldste f&oslash;rst</a><br>';
		if ($method == 'oldfirst') $content .= '</b>';

		if ($method == 'mostread') $content .= '<b>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?c='.$criteria.'&p='.$page.'&l='.$limit.'&m=mostread">Mest lest f&oslash;rst</a><br>';
		if ($method == 'mostread') $content .= '</b>';

		$content .= '<br>';


		$content .= '<a href="'.$_SERVER['PHP_SELF'].'">Nytt s&oslash;k</a><br><br>';

	} else {

		$content .= getInfoField($db, 'hjalp-sok_i_forumet').'<br>';

		$content .= '<form name="searchforums" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= 'S&oslash;keord: <input type="text" name="c" size=50><br>';
		$content .= 'Vis resultat: ';
		$content .= '<input type="radio" class="radio" name="m" value="newfirst" checked>Nyeste f&oslash;rst ';
		$content .= '<input type="radio" class="radio" name="m" value="oldfirst">Eldste f&oslash;rst ';
		$content .= '<input type="radio" class="radio" name="m" value="mostread">Mest lest f&oslash;rst ';
		$content .= '<br><br>';

		$content .= '<input type="submit" class="button" value="'.$config['text']['link_search'].'">';
		$content .= '</form>';
	}

		echo '<div id="user_forum_content">';
		echo MakeBox('<a href="forum.php">Forum</a>|S&oslash;k p&aring; innlegg', $content, 500);
		echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>
<script type="text/javascript">
if (document.searchforums) document.searchforums.c.focus();
</script>