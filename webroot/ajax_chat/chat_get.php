<?
	/*
		Called regularry
	
		Returns the last few lines of text, usernames & timestamps entered in the specified chat room
	*/

	include_once('include_all.php');

	if (empty($_GET['r'])) {
		if (empty($_POST['r']) || !is_numeric($_POST['r'])) die;
		$roomId = $_POST['r'];
	} else {
		$roomId = $_GET['r'];
	}

	if (isset($_POST['all'])) unset($_SESSION['chat'][$roomId]);

	$lastEntryId = 0;

	if (isset($_POST['all']) || !isset($_SESSION['chat']['lastUpdate'][$roomId]))
	{
		//Returns a full chat buffer, sorted by newest item first
		$list = getChatEntries($db, $roomId, $lastEntryId, $config['chat']['buffer_lines']);

	} else {
		//Only return the new entries since the last call
		$list = getNewChatEntries($db, $roomId, $lastEntryId, $_SESSION['chat']['lastUpdate'][$roomId]);
	}

	if ($list) $_SESSION['chat']['lastUpdate'][$roomId] = $lastEntryId;
	if (!isset($_SESSION['chat'][$roomId])) $_SESSION['chat'][$roomId] = array();
	
	if ($_SESSION['loggedIn']) {
		setChatRoomUser($db, $roomId, $_SESSION['userId']);
	}

	$members_list = getCurrentChatUsers($db, $roomId);

	/*if (!empty($_GET['r'])) {
		print_r($members_list); die;
	}*/

	header('Content-type: text/xml');

	echo '<?xml version="1.0" ?>';
	
	//Sort list by oldest item first
	//todo: borde inte detta gå att göra direkt i SQL-satsen för getChatEntries() ? sort by time asc,time desc ? temporary tables?
	if ($list) $list = aSortBySecondIndex($list, 'timeCreated');

	//Kolla vilka chat users som är nya i chattrumet
	$new_members = array();
	$left_members = array();
	$all_members = array();
	for ($i=0; $i<count($members_list); $i++) {
		if (!in_array($members_list[$i]['userName'], $_SESSION['chat'][$roomId])) {
			$_SESSION['chat'][$roomId][] = $members_list[$i]['userName'];
			$new_members[] = $members_list[$i];
		}
		$all_members[] = $members_list[$i]['userName'];
	}
	
	if (!empty($_GET['r']) && $new_members) {
		echo 'new members:';
		print_r($new_members);
		die;
	}

	//Loopar igenom alla "kända" users för detta chattrum, för att leta efter inaktiva users
	foreach ($_SESSION['chat'][$roomId] as &$username) {
		if ($username && !in_array($username, $all_members)) {
				
			$left_members[] = array('userName' => $username, 'userId' => 666);	//todo: skicka med userId
			$username = '';	//fixme: unset() verkar inte funka?
			//unset($_SESSION['chat'][$roomId][$username]);
		}
	}

	/*if (!empty($_GET['r']) && $left_members) {
		echo 'left members';
		print_r($left_members);
		die;
	}*/

	if (!$list && !$new_members && !$left_members) {
		echo '<x></x>';
		die;
	}
	
	echo '<x>';
	for ($i=0; $i<count($list); $i++) {
		echo '<l>';
			echo '<t>'.$list[$i]['timeCreated'].'</t>';
			echo '<u>'.$list[$i]['userName'].'</u>';
			echo '<i>'.$list[$i]['userId'].'</i>';
			if (strpos($list[$i]['msg'], '<') !== false) {
				//encode messages containing < as CDATA
				echo '<m><![CDATA['.$list[$i]['msg'].']]></m>';
			} else {
				echo '<m>'.$list[$i]['msg'].'</m>';
			}
		echo '</l>';
	}

	for ($i=0; $i<count($new_members); $i++) {
		echo '<s>';	//uSer joined
			echo '<u>'.$new_members[$i]['userName'].'</u>';
			echo '<i>'.$new_members[$i]['userId'].'</i>';
		echo '</s>';
	}

	for ($i=0; $i<count($left_members); $i++) {
		echo '<e>';	//usEr left
			echo '<u>'.$left_members[$i]['userName'].'</u>';
			echo '<i>'.$left_members[$i]['userId'].'</i>';
		echo '</e>';
	}
	echo '</x>';

?>
