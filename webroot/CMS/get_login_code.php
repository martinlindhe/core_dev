<?
	/*
	This script is called by SSI from a remote server. It returns a unique ID used to log the user in.
	
	If $_GET['id'] is set, it assumes a numeric userId -nonfunctional
	If $_GET['guid'] is set, it assumes a GUID string
	
	If $_GET['name'] is set, it saves/updates username
	If $_GET['age'] is set, it saves/updates user age
	
	*/

	include('include_all.php');

	mt_srand();
	$randomPassword = mt_rand(100000000, 999999999);

	$userName = '';
	if (!empty($_GET['name'])) $userName = $_GET['name'];
	$userAge = '0';
	if (!empty($_GET['age']) && is_numeric($_GET['age'])) $userAge = $_GET['age'];

/*
	//fixme: rewrite the $_GET['id'] part to use tblRemoteUsers
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$remoteUserId = $_GET['id'];

		$data = getUserData($db, $remoteUserId);
		if (!$data) {
			//create new user entry
			newUserEntry($db, $remoteUserId, $userName);
		}

		$sql = 'UPDATE tblUsers SET remoteId='.$randomPassword.' WHERE userId='.$remoteUserId;
		dbQuery($db, $sql);

		echo $randomPassword;
		die;
	}*/

	if (!empty($_GET['guid'])) {
		/*
		GUID can look like this for example:
		e8b65be6-46a3-4c89-a6e2-0a0ab5f50fde
		{62D0540A-424C-4E6B-B289-4C16CB2C258F}
		
		We convert it to a lowercase string and removes the { and } characters
		*/

		$remoteGUID = strtolower($_GET['guid']);
		$remoteGUID = str_replace(' ', '', $remoteGUID);
		$remoteGUID = str_replace('{', '', $remoteGUID);
		$remoteGUID = str_replace('}', '', $remoteGUID);
		$remoteGUID = dbAddSlashes($db, $remoteGUID);
		if (strlen($remoteGUID) != 36) die('Invalid GUID');

		$sql = 'SELECT userId FROM tblRemoteUsers WHERE remoteUserGUID="'.$remoteGUID.'"';
		$localUserId = dbOneResultItem($db, $sql);
		if (!$localUserId) {
			/* Create new user entry */
			if (!$userName) $userName = 'Unknown Name';
			$localUserId = newUserEntry($db, $userName);
			if (!$localUserId) die('Error: failed to create new user');

			$sql = 'INSERT INTO tblRemoteUsers SET userId='.$localUserId.',remoteUserGUID="'.$remoteGUID.'"';
			dbQuery($db, $sql);
		} else {
			if ($userName) setUserName($db, $localUserId, $userName);
		}
		
		if ($userAge) saveSetting($db, $localUserId, 'age', $userAge);

		$sql = 'UPDATE tblRemoteUsers SET randomPassword='.$randomPassword.',requestTime='.time().' WHERE userId='.$localUserId;
		dbQuery($db, $sql);
		
		echo $randomPassword;
		die;
	}
?>