<?
	/* Defines different M2W-services */
	define('M2W_CHATROOM',	5);

	function registerCallStart()
	{
		global $db, $config;

		unset($_SESSION['stored_call']);
		storeCallDetails($config['vxml']['service']);

		$_SESSION['stored_pres'] = 0;

		//load stored settings & update call history
		$q = 'SELECT * FROM tblCallerSettings WHERE callerID="'.$db->escape($_GET['callerID']).'"';
		$data = $db->getOneRow($q);
		if (!$data) {
			$q = 'INSERT INTO tblCallerSettings SET callerID="'.$db->escape($_GET['callerID']).'", callCnt=1, timeFirstCall=NOW(), timeLastCall=NOW()';
			$userId = $db->insert($q);
		} else {
			$userId = $data['userId'];
			$q = 'UPDATE tblCallerSettings SET callCnt=callCnt+1, timeLastCall=NOW() WHERE userId='.$userId;
			$db->update($q);
			if ($data['storedPres']) $_SESSION['stored_pres'] = 1;
		}
		$_SESSION['user_id'] = $userId;
	}

	/* Called once at the beginning of a handled call. Stores call details */
	function storeCallDetails($_service)
	{
		global $db;
		if (!is_numeric($_service)) return false;
		
		if (!empty($_SESSION['stored_call'])) return true;	//call details is already stored

		//Note: PHP translates "vendorIdentifier.productID" into "vendorIdentifier_productID"

		//The first HTTP request for the VoiceXML application contains GET parameters with call info.
		//Subsequent requests will only contain the callID parameter

		//PSE-MS bug: callStatus sätts alltid till SETUP eftersom PSE-MS skickar 2 ex av callStatus parametern i url:en, den andra är callStatus=CONNECTED.

		if (!isset($_GET['callID']) || !isset($_GET['callStatus']) ||
			!isset($_GET['callDirection']) || !isset($_GET['stack']) ||
			!isset($_GET['callerID']) || !isset($_GET['callerAlias']) || !isset($_GET['callerIP']) ||
			!isset($_GET['calledID']) || !isset($_GET['calledAlias']) || !isset($_GET['calledIP']) ||
			!isset($_GET['redirectingNumber']) || !isset($_GET['callType'])
		) return false;

		//this is a full insert of all parameters, for debugging
		$q = 'INSERT INTO tblCallDetails SET timeCreated=NOW(), service='.$_service.', callID="'.$db->escape($_GET['callID']).'", params="'.$db->escape(serialize($_GET)).'"';
		$entryId = $db->insert($q);

		//Store a entry
		if ($_GET['callDirection'] == 'INBOUND') {

			//kom ihåg att detta skrivits till databasen så att det inte skrivs igen vid senare sidanrop
			$_SESSION['stored_call'] = true;

			$q = 'INSERT INTO tblCurrentCalls SET entryId='.$entryId.', timeCreated=NOW(), service='.$_service.', '.
				'callID="'.$db->escape($_GET['callID']).'", stack="'.$db->escape($_GET['stack']).'", callType="'.$db->escape($_GET['callType']).'", '.
				'callerAlias="'.$db->escape($_GET['callerAlias']).'", callerID="'.$db->escape($_GET['callerID']).'", callerIP="'.$db->escape($_GET['callerIP']).'", '.
				'calledAlias="'.$db->escape($_GET['calledAlias']).'", calledID="'.$db->escape($_GET['calledID']).'", calledIP="'.$db->escape($_GET['calledIP']).'"';

			$db->insert($q);
			//error_log('inserted callSTART data');
		}
	}

	//Logs the termination of a call
	//	"id"	Set by PSE-MS as a unique call ID
	function terminateCall($callID)
	{
		global $db;
		//maybe todo: store what type of hangup occured. did client chose to "hang up" in menus or did he just terminate the call? is this relevant..?

		$q = 'UPDATE tblCallDetails SET timeHangup=NOW() WHERE callID="'.$db->escape($_GET['id']).'"';
		$db->update($q);

		$q = 'DELETE FROM tblCurrentCalls WHERE callID="'.$db->escape($_GET['id']).'"';
		$db->delete($q);
	}

	/* Returns the number of active calls on current service */
	function getActiveCalls()
	{
		global $db, $config;
	
		$q = 'SELECT COUNT(entryId) FROM tblCurrentCalls WHERE service='.$config['vxml']['service'];
		$n = $db->getOneItem($q);	
		if ($n > 3) return 'Many';
		return $n;
	}

?>
