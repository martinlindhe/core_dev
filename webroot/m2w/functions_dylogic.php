<?
	/* Defines different M2W-services */
	define('M2W_CHATROOM',	5);

	/* Called once at the beginning of a handled call. Stores call details */
	function storeCallDetails($_service)
	{
		global $db;
		if (!is_numeric($_service)) return false;

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

		//todo: kom ihåg att detta skrivits till databasen så att det inte skrivs igen vid senare sidanrop

		//debug: this is a full insert of all parameters, just for debugging
		$q = 'INSERT INTO tblCallDetails SET timeCreated=NOW(), params="'.$db->escape(serialize($_GET)).'"';
		$db->insert($q);

		//Store a entry
		if ($_GET['callDirection'] == 'INBOUND') {
			$q = 'INSERT INTO tblCurrentCalls SET timeCreated=NOW(), service='.$_service.', '.
				'callID="'.$db->escape($_GET['callID']).'", stack="'.$db->escape($_GET['stack']).'", callType="'.$db->escape($_GET['callType']).'", '.
				'callerID="'.$db->escape($_GET['callerID']).'", callerAlias="'.$db->escape($_GET['callerAlias']).'", callerIP="'.$db->escape($_GET['callerIP']).'", '.
				'calledID="'.$db->escape($_GET['calledID']).'", calledAlias="'.$db->escape($_GET['calledAlias']).'", calledIP="'.$db->escape($_GET['calledIP']).'"';

			$db->insert($q);
			error_log($q);
		}
	}

	//outputs a session ID value to the VoiceXML script. used to associate uploads with this user
	function setSID()
	{
		$n = mt_rand(1000000,9999999);
		echo '<var name="session_id" expr="'.$n.'"/>';
	}

?>
