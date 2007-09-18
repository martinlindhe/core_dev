<?
	/*
		callback function for when PSE-MS has successfully stored the user's video presentation
		
		used to update database. dont output VXML


		fixme: because we call this url thru <pse_submit notifyUrl=""> we cant preserve session data.
			so we pass user_id as "id" parameter.
			and we cannot update session params in this script
	*/

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('no id');

	$userId = $_GET['id'];

	require_once('config.php');

	//$_SESSION['stored_pres'] = 1;
	$q = 'UPDATE tblCallerSettings SET storedPres=1 WHERE userId='.$userId;
	$db->update($q);

	$session->log(serialize($_GET));	//debug-log PSE-MS info
?>
