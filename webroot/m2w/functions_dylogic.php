<?
	function storeCDR()
	{
		global $db;
		
		//note: PHP translates "vendorIdentifier.productID" into "vendorIdentifier_productID"

		//fixme: callStatus sätts alltid till SETUP eftersom PSE-MS skickar 2 ex av callStatus parametern i url:en, den andra är callStatus=CONNECTED.

		if (isset($_GET['callID']) && isset($_GET['callStatus']) &&
			isset($_GET['callDirection']) && isset($_GET['stack']) &&
			isset($_GET['callerID']) && isset($_GET['callerAlias']) && isset($_GET['callerIP']) && 
			isset($_GET['calledID']) && isset($_GET['calledAlias']) && isset($_GET['calledIP']) && 
			isset($_GET['redirectingNumber']) && isset($_GET['callType']) &&
			isset($_GET['vendorIdentifier_productID']) && isset($_GET['vendorIdentifier_versionID'])
		) {
			//The first HTTP request for the VoiceXML application contains GET parameters with call info.
			//Subsequent requests will only contain the callID parameter

			//todo: kom ihåg att detta skrivits till databasen så att det inte skrivs igen vid senare sidanrop

			//todo: optimera tabellen som håller denna data
			$q = 'INSERT INTO tblCDR SET '.
				'callID="'.$db->escape($_GET['callID']).'",callStatus="'.$db->escape($_GET['callStatus']).'",'.
				'callDirection="'.$db->escape($_GET['callDirection']).'",stack="'.$db->escape($_GET['stack']).'",'.
				'callerID="'.$db->escape($_GET['callerID']).'",callerAlias="'.$db->escape($_GET['callerAlias']).'",callerIP="'.$db->escape($_GET['callerIP']).'",'.
				'calledID="'.$db->escape($_GET['calledID']).'",calledAlias="'.$db->escape($_GET['calledAlias']).'",calledIP="'.$db->escape($_GET['calledIP']).'",'.
				'redirectingNumber="'.$db->escape($_GET['redirectingNumber']).'",callType="'.$db->escape($_GET['callType']).'",'.
				'vendorProductID="'.$db->escape($_GET['vendorIdentifier_productID']).'",vendorVersionID="'.$db->escape($_GET['vendorIdentifier_versionID']).'"';
			$db->insert($q);
		}
	}
?>
