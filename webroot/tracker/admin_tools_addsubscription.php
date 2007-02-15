<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$siteId = $_GET['id'];

	$site = getTrackSite($db, $siteId);
	if (!$site) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (!empty($_POST['name'])) {
		//Create subscription
		$subId = addSubscription($db, SUBSCRIBE_TRACKSITE, $siteId);

		if (!$subId) {
			die('Fatal error: failed to create new subscription');
		}

		//Store settings for the subscription
		saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'name', $_POST['name']);
		saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'startpage', $_POST['startpage']);
		saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'trackpoint', $_POST['tracker']);
		
		$top_search_phrases = 0;
		if (!empty($_POST['include_top_search_phrases'])) {
			$top_search_phrases = $_POST['top_search_phrases'];
		}	
		saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_top_search_phrases', $top_search_phrases);

		if (!empty($_POST['include_search_popularity'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_search_popularity', 1);
		} else {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_search_popularity', 0);
		}

		if (!empty($_POST['include_browser_popularity'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_browser_popularity', 1);
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'browser_popularity_cnt', $_POST['browser_popularity_cnt']);
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'browser_versions_cnt', $_POST['browser_versions_cnt']);
		} else {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_browser_popularity', 0);
		}

		if (!empty($_POST['include_os_popularity'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_os_popularity', 1);
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'os_popularity_cnt', $_POST['os_popularity_cnt']);
		} else {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_os_popularity', 0);
		}

		if (!empty($_POST['include_google_pr'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_google_pr', 1);
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'google_pr_mode', $_POST['google_pr']);
		} else {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_google_pr', 0);
		}

		if (!empty($_POST['include_google_indexing'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_google_indexing', 1);
		} else {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_google_indexing', 0);
		}
		
		if (!empty($_POST['include_google_ranking'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_google_ranking', 1);

			//todo: loopa
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'google_rank_1', $_POST['google_rank_1']);
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'google_rank_2', $_POST['google_rank_2']);
		} else {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'include_google_ranking', 0);
		}
		
		//interval: "daily", "weekly" or "monthly" is valid
		if (!empty($_POST['interval'])) {
			saveSetting($db, SETTING_SUBSCRIPTION, $subId, 'interval', $_POST['interval']);
		}
		
		//Redirect to subscription overview page
		header('Location: admin_tools_showsubscription.php?id='.$subId);
		die;
	}

	include('design_head.php');

	echo '<h2>Track site tools - Create new subscription</h2>';
	echo 'Site name: '.$site['siteName'].'<br>';
	echo '<br>';
	
	$start_page = 'http://'.$site['siteName'].'/';
?>

	<form method="post" action="<?=$_SERVER['PHP_SELF'].'?id='.$siteId?>">

	Subscription name: <input name="name" type="text" size=50 value="Subscription"><br>
	Start page: <input name="startpage" type="text" size=50 value="<?=$start_page?>"><br><br>
	Track point in effect:
	<select name="tracker">
<?
		$list = getTrackPoints($db, $siteId);
		for ($i=0; $i<count($list); $i++) {
			echo '<option value="'.$list[$i]['trackerId'].'">'.$list[$i]['location'];
		}
?>
	</select>
	<h2>Include in this subscription:</h2>
	
	<div id="setting_search_phrases">
	<input name="include_top_search_phrases" type="checkbox" checked onClick="toggle_element_by_name('setting_search_phrases_details');">Top search phrases<br>
		<div id="setting_search_phrases_details">
		Show the <input name="top_search_phrases" type="text" size=2 value="10"> most popular search phrases<br>
		</div>
	</div>

	<div id="setting_search_popularity">
	<input name="include_search_popularity" type="checkbox" checked onClick="toggle_element_by_name('setting_search_popularity_details')">Search engine popularity<br>
		<div id="setting_search_popularity_details">
		Shows how many visitors are coming from search engines
		</div>
	</div>

	<div id="setting_browser_popularity">
	<input name="include_browser_popularity" type="checkbox" checked onClick="toggle_element_by_name('setting_browser_popularity_details')">Browser popularity<br>
		<div id="setting_browser_popularity_details">
		Show the <input name="browser_popularity_cnt" type="text" size=2 value="5"> most popular browsers<br>
		And the <input name="browser_versions_cnt" type="text" size=2 value="0"> most popular browser versions (not implemented yet)<br>
		</div>
	</div>

	<div id="setting_os_popularity">
	<input name="include_os_popularity" type="checkbox" checked onClick="toggle_element_by_name('setting_os_popularity_details')">OS popularity<br>
		<div id="setting_os_popularity_details">
		Show the <input name="os_popularity_cnt" type="text" size=2 value="3"> most popular operating systems<br>
		</div>
	</div>

	<div id="setting_google_pr">
	<input name="include_google_pr" type="checkbox" onClick="toggle_element_by_name('setting_google_pr_details')">Google pageranks<br>
		<div id="setting_google_pr_details" style="display:none">
		Show page rank for:<br>
		<input name="google_pr" value="all" type="radio" class="radio">All pages<br>
		<input name="google_pr" value="startpage" type="radio" class="radio" checked>Only the start page<br>
		</div>
	</div>
	
	<div id="setting_google_indexing">
	<input name="include_google_indexing" type="checkbox" onClick="toggle_element_by_name('setting_google_indexing_details')">Google indexing<br>
		<div id="setting_google_indexing_details" style="display:none">
		Displays if any pages are not indexed in google
		</div>
	</div>

	<div id="setting_google_search_phrase_ranking">
	<input name="include_google_ranking" type="checkbox" onClick="toggle_element_by_name('setting_google_search_phrase_ranking_details')">Google search phrase ranking<br>
		<div id="setting_google_search_phrase_ranking_details" style="display:none">
		Keeps track of page ranking on the following search phrases:<br>
		<input name="google_rank_1" type="text" value="phrase 1"><br>
		<input name="google_rank_2" type="text" value="phrase 2"><br>
		</div>
	</div>
	<br>

	Reporting interval:<br>
	<select name="interval">
		<option value="daily">Daily (every day at 00:00)
		<option value="weekly">Weekly (mondays at 00:00)
		<option value="monthly">Monthly (the 1:st in next month at 00:00)
	</select><br>

	<br>
	<input type="submit" class="button" value="Create...">
	</form>
	<br>
<?
	echo '<a href="admin_tools_tracksite.php?id='.$siteId.'">Back to overview</a><br>';

	include('design_foot.php');
?>