<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?
	/*
		sfd.php - Parsar XML, laddar ner bilder och genererar textfiler till flashfil, från sfd.se
			Detta skript ska köras regelbundet. Klienterna läser sedan de genererade filerna
	*/

	include('sfd_config.php');
	include('functions_sfd.php');

	//*****************************//
	// Alexander & Co							 //
	//*****************************//

	parse_sfd_data('http://net.sfd.se/Gateway.aspx?SFDGatewayID=32&RefObject=3');
	
	write_sfd_data($objekt, 'referens.txt');
	unset($objekt);
?>