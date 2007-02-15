<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?
	/*
		sfd.php - Parsar XML, laddar ner bilder och genererar textfiler till flashfil, från sfd.se
			Detta skript ska köras regelbundet. Klienterna läser sedan de genererade filerna
	*/


	include('sfd_config.php');
	include('functions_sfd.php');



	//*****************************//
	// Skeppsholmen								 //
	//*****************************//
	$config['max_images_per_object']	= 7;
	$config['max_objects'] = 15;		//max antal objekt i textfilerna för alexander & co
	//storlek på thumbnails i referensobjekten:
	$config['thumb_height']				= 678;
	$config['thumb_width']				= 1200;

	//Laddar ner all data för skeppsholmen
	parse_sfd_data('http://net.sfd.se/Gateway.aspx?SFDGatewayID=21');
	write_sfd_data($objekt, 'skeppsholmen.txt');
	unset($objekt);

?>