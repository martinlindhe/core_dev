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
	$config['max_images_per_object']	= 1;
	$config['max_objects'] = 45;		//max antal objekt i textfilerna för alexander & co
	//storlek på thumbnails i referensobjekten:
	$config['thumb_width']				= 181;
	$config['thumb_height']				= 181;
	
	//Laddar ner referensdata för alexander & co
	parse_sfd_data('http://net.sfd.se/Gateway.aspx?SFDGatewayID=32&RefObject=3');
	write_sfd_data($objekt, 'referens.txt');
	unset($objekt);
?>