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

	parse_sfd_data('http://net.sfd.se/Gateway.aspx?SFDGatewayID=32');			//fixme: låt denna funktion returnera $objekt. nu deklareras den som global

	$objekt = parse_sfd_data_to_sections($objekt);

	unlink($config['server_cache_path'].'objekt.txt');
	write_sfd_data($objekt['villor'], 'villor', $config['server_cache_path'].'objekt.txt');
	write_sfd_data($objekt['lantstallen'], 'lantstallen', $config['server_cache_path'].'objekt.txt');
	write_sfd_data($objekt['vaningar'], 'vaningar', $config['server_cache_path'].'objekt.txt');
	file_put_contents($config['server_cache_path'].'objekt.txt', 'q=1', FILE_APPEND);

	unset($objekt);
?>