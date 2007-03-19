<?
	//PHP 4 emulation of file_put_contents(), introduced in PHP 5
	if (!function_exists('file_put_contents')) {
		define('FILE_APPEND', 1);
		function file_put_contents($n, $d, $flag = false)
		{
			$mode = ($flag == FILE_APPEND) ? 'a' : 'w';
			$f = @fopen($n, $mode);
			if (!$f) {
				return 0;
			} else {
				if (is_array($d)) $d = implode($d);
				$bytes_written = fwrite($f, $d);
				fclose($f);
				return $bytes_written;
			}
		}
	}

	//PHP 4 emulation of str_ireplace(), introduced in PHP 5
	if (!function_exists('str_ireplace')) {
		function str_ireplace($search, $replacement, $string) {
			$delimiters = array(1,2,3,4,5,6,7,8,14,15,16,17,18,19,20,21,22,23,24,25,
				26,27,28,29,30,31,33,247,215,191,190,189,188,187,186,185,184,183,182,180,177,176,175,174,173,172,171,169,
				168,167,166,165,164,163,162,161,157,155,153,152,151,150,149,148,147,146,145,144,143,141,139,137,136,135,
				134,133,132,130,129,128,127,126,125,124,123,96,95,94,63,62,61,60,59,58,47,46,45,44,38,37,36,35,34);

			foreach ($delimiters as $d) {
				if (strpos($string, chr($d))===false){
					$delimiter = chr($d);
					break;
				}
			}
			if (!empty($delimiter)) {
				return preg_replace($delimiter.quotemeta($search).$delimiter.'i', $replacement, $string);
			}
			trigger_error('Homemade str_ireplace could not find a proper delimiter.', E_USER_ERROR);
		}
	}

	function aRSortBySecondIndex($multiArray, $secondIndex)
	{
		while (list($firstIndex, ) = each($multiArray)) {
			if (isset($multiArray[$firstIndex][$secondIndex])) $indexMap[$firstIndex] = $multiArray[$firstIndex][$secondIndex];
		}

		arsort($indexMap);
		while (list($firstIndex, ) = each($indexMap))
			if (is_numeric($firstIndex))
				$sortedArray[] = $multiArray[$firstIndex];
			else $sortedArray[$firstIndex] = $multiArray[$firstIndex];
		return $sortedArray;
	}


	/*
	sfd xml gränssnitt:
	http://w4.sfd.se/obj/obj.dll/listxml?firmanr=26301&develop=1
	
	bild-url:
	http://pics.objektdata.se/pic/pic.dll/imageX?url=26301/SFDC8587FFD889348E9B30755DB454F0BC3.jpg							-	100x75px thumbnail
	http://pics.objektdata.se/pic/pic.dll/imageX?url=26301/SFDC8587FFD889348E9B30755DB454F0BC3.jpg&sizex=500		-	500x375px


	nya xml-gränssnittet:
	http://net.sfd.se/Gateway.aspx?SFDGatewayID=21
	http://net.sfd.se/WebPack/ObjectList/(xa2dlgec0yr3k4iojxa5mpjt)/ObjectList.aspx?DBSpace=11816&Custom=1&RenderAsXML=1
	*/

	function startElement($parser, $name, $attrs)
	{
		global $objekt, $objekt_name, $objekt_cnt;

		if (count($attrs)) {
			$objekt[$objekt_cnt][$name]['attrs'] = $attrs;
		}
		$objekt_name = $name;
	}

	function endElement($parser, $name)
	{
		global $objekt_cnt;
		if ($name == 'OBJEKT') {
			$objekt_cnt++;
		}
	}

	function characterData($parser, $data)
	{
		if (trim($data)) {
			global $objekt, $objekt_name, $objekt_cnt;

			if (!isset($objekt[$objekt_cnt][$objekt_name]['attrs'])) {
				$objekt[$objekt_cnt][$objekt_name] = $data;
			} else {
				$objekt[$objekt_cnt][$objekt_name]['data'] = $data;
			}
		}
	}

	function download_sfd_image($url, $file)
	{
		global $config;

		$file_normal = $file.'_width'.$config['image_width'].'.jpg';

		if (file_exists($file_normal) && (time()-filemtime($file_normal)) < $config['cache_expire_time']) {
			//echo 'Recent cached image '.$file_normal.' found, skipping download<br>';
			return;
		}

		set_time_limit(120);	//add another 120 seconds to finish this download
		echo 'Downloading '.$url.' ...<br>';
		
		$data = file_get_contents($url);
		if (!$data) {
			echo 'Error: No data returned!<br>';
			return;
		}

		file_put_contents($file_normal, $data);

		list($width, $height) = getimagesize($file_normal);
		
		if ($width == 0 || $height == 0) {
			echo 'Error: Image dimensions for '.$file_normal.' are invalid!: '.$width.'x'.$height.'<br>';
			return;
		}

		if ($width < $config['image_width']) {
			/* Resize image to expected dimensions */
			$ratio = $width / $height;

			$new_width = $config['image_width'];
			$new_height = round($config['image_width']/$ratio);

			echo 'Resizing image from '.$width.'x'.$height.' to '.$new_width.'x'.$new_height.' (ratio '.$ratio.')...<br>';

			$o_im = imagecreatefromjpeg($file_normal);
			$t_im = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($t_im, $o_im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			//imagecopyresized($t_im, $o_im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagejpeg($t_im, $file_normal, $config['jpeg_quality']);

			$width = $new_width;
			$height = $new_height;
		}
		echo 'Image '.$file_normal.' saved ('.$width.'x'.$height.')<br>';
		//echo '<img src="../sfd_cache/'.basename($file_normal).'"><br>';
	}
	
	function generate_sfd_thumbnail($file)
	{
		/* Skapa thumbnail

				Thumbnail skapar vi genom att göra orginalbilden thumb_height pixlar hög
				(utgår från liggande bild), sen klipper vi den så den blir thumb_width pixlar bred
		*/

		global $config;

		set_time_limit(30);	//add another 30 seconds to handle the file resize

		$file_normal = $file.'_width'.$config['image_width'].'.jpg';
		$file_thumb = $file.'_thumb.jpg';

		//kontrollera om thumbnail med angivna dimensioner redan finns:
		if (file_exists($file_thumb) && (time()-filemtime($file_normal)) < $config['cache_expire_time']) {
			list($org_thumb_width, $org_thumb_height) = getimagesize($file_thumb);
			if ($org_thumb_width == $config['thumb_width'] && $org_thumb_height == $config['thumb_height']) {
				//echo 'Thumbnail p&aring; '.$org_thumb_width.'x'.$org_thumb_height.' finns redan, skippar<br>';
				return;
			}
		}

		list($width, $height) = getimagesize($file_normal);
		
		if ($width == 0 || $height == 0) {
			echo 'Error: Image dimensions for '.$file_normal.' are invalid!: '.$width.'x'.$height.'<br>';
			return;
		}

		$ratio = $height / $width;
		$thumb_width = round($config['thumb_height']/$ratio);
		$thumb_height = $config['thumb_height'];
		
		if ($thumb_width < $config['thumb_width']) {
			/*  Bilden är stående */
			$ratio = $width / $height;

			$thumb_width = $config['thumb_width'];
			$thumb_height = round($config['thumb_width']/$ratio);
		}

		echo 'Generating '.$thumb_width.'x'.$thumb_height.' thumbnail...<br>';

		$o_im = imagecreatefromjpeg($file_normal);
		$t_im = imagecreatetruecolor($thumb_width, $thumb_height);
		imagecopyresampled($t_im, $o_im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
		//imagecopyresized($t_im, $o_im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
		imagejpeg($t_im, $file_thumb, $config['jpeg_quality']);
		imagedestroy($t_im);
		imagedestroy($o_im);
		echo $file_thumb.' saved.<br>';
	

		/* Nu klipper vi bilden till 181x181 ... */
		echo 'Cropping thumbnail to '.$config['thumb_width'].'x'.$config['thumb_height'].' ...<br>';

		$o_im = imagecreatefromjpeg($file_thumb);
		$t_im = imagecreatetruecolor($config['thumb_width'], $config['thumb_height']);

		if ($thumb_height > $config['thumb_height']) {
			/* Klipp stående bild */
			//echo 'Klipper stående bild<br>';

			$src_x = 0;
			$src_y = ($thumb_height/2)-($config['thumb_height']/2);

		} else {
			/* Klipp liggande bild */
			//echo 'Klipper liggande bild<br>';

			$src_x = ($thumb_width/2)-($config['thumb_width']/2);
			$src_y = 0;
		}
		//echo 'src_x = '.$src_x.', src_y = '.$src_y.'<br>';
		imagecopy($t_im, $o_im, 0, 0, $src_x, $src_y, $config['thumb_width'], $config['thumb_height']);
		imagejpeg($t_im, $file_thumb, $config['jpeg_quality']);
	
		//echo '<img src="../sfd_cache/'.basename($file_thumb).'"><br>';
	}

	function parse_sfd_data($xml_file)
	{
		global $config, $objekt, $objekt_cnt;
		
		echo '<b>Parsing '.$xml_file.' ...</b><br>';

		$objekt_cnt = 0;

		$xml_parser = xml_parser_create();

		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");
	
		$data = file_get_contents($xml_file);

		if ($data === false) {
			die('Error: Could not open XML input file "'.$xml_file.'"');
		}

		if (!xml_parse($xml_parser, $data)) {
			die( printf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)) );
		}

		xml_parser_free($xml_parser);
		
		//echo '<pre>'; print_r($objekt);
		
		/* Spara ner alla bildobjekt till hårddisken */
		for ($i=0; $i<count($objekt); $i++)
		{
			$objekt[$i]['antalbilder'] = 0;
			
			echo 'Processing object '.$i.' ...<br>';

			for ($current_pic_num = 0; $current_pic_num < $config['max_images_per_object']; $current_pic_num++)
			{
				eval("\$current_pic = &\$objekt[\$i]['BILDURL".$current_pic_num."'];");
				if (empty($current_pic)) continue;

				$objekt[$i]['antalbilder']++;
				
				//ta bort &sizex=320&sizey=240 från url-namnet (sfd skickar med det by default)
				$pos = strpos($current_pic, '&sizex');
				if ($pos !== FALSE) $current_pic = substr($current_pic, 0, $pos);

				$url = $current_pic.'&sizex='.$config['image_width'];
				$file = $config['server_cache_path'].$objekt[$i]['GID'].'_'.$current_pic_num;
				$objekt[$i]['bild'][$current_pic_num] = $file;
				
				download_sfd_image($url, $file);
				generate_sfd_thumbnail($file);
			}
		}
		
		//rensa upp XML-strukturen: 'PRIS', 'ADRESS', 'RUM', 'STORLEK', 'BOAREA', 'OMRADE', 'ADRESS', 'BESKRIVNING'
		for ($i=0; $i<count($objekt); $i++)
		{	
			$pris = 'Prisuppgift saknas';
			if (!empty($objekt[$i]['PRIS'])) {
				if (is_numeric($objekt[$i]['PRIS'])) {
					//om det kommer i formatet 1500000, ändra till 1 500 000
					$pris = number_format($objekt[$i]['PRIS'], 0, '.', ' ').' kr/bud';
				} else {
					$pris = $objekt[$i]['PRIS'];		//[PRIS] => 8.500.000 kr/bud
				}
			}
			$objekt[$i]['PRIS'] = $pris;
	
			if (!empty($objekt[$i]['OMRADE']) && !empty($objekt[$i]['KOMMUN'])) {
				$objekt[$i]['ADRESS'] = $objekt[$i]['OMRADE'].', '.$objekt[$i]['KOMMUN'];
			} else if (!empty($objekt[$i]['ADRESS']) && !empty($objekt[$i]['KOMMUN'])) {
				$objekt[$i]['ADRESS'] = $objekt[$i]['ADRESS'].', '.$objekt[$i]['KOMMUN'];
			}
			
			if (empty($objekt[$i]['ADRESS'])) {
				$objekt[$i]['ADRESS'] = 'Adress saknas';
			}

			if (!empty($objekt[$i]['RUM'])) $objekt[$i]['RUM'] = intval($objekt[$i]['RUM']);		//<Rum>6 rok</Rum>
			if (!empty($objekt[$i]['BOAREA'])) $objekt[$i]['BOAREA'] = intval($objekt[$i]['BOAREA']);		//<BoArea>263 kvm</BoArea>
			
			if (empty($objekt[$i]['RUM']) && empty($objekt[$i]['BOAREA'])) {
				$objekt[$i]['STORLEK'] = 'Uppgift saknas';
			} else if (empty($objekt[$i]['RUM'])) {
				$objekt[$i]['STORLEK'] = $objekt[$i]['BOAREA'].' kvm';
			} else {
				$objekt[$i]['STORLEK'] = $objekt[$i]['RUM'].' rum & k'.mb_convert_encoding('ö','UTF-8','ISO-8859-1').'k om '.$objekt[$i]['BOAREA'].' kvm';
			}
	
			$beskr = 'Beskrivning saknas';
			if (!empty($objekt[$i]['BESKRIVNING'])) $beskr = $objekt[$i]['BESKRIVNING'];
			$beskr = str_ireplace('&amp;', '&', $beskr);
			$objekt[$i]['BESKRIVNING'] = $beskr;
		}
	}

	
	//skriver ut parsade sfd-datan i textfiler
	function store_sfd_data($objekt)
	{
		global $config;
		
		//sorterar array efter antal rum, fallande:
		$objekt = aRSortBySecondIndex($objekt, 'RUM');
		
		//skriv till olika filer beroende på 'OBJEKTTYP':
		
		$out = array();
		
		for ($i=0; $i<count($objekt); $i++)
		{
			if (empty($objekt[$i]['OBJEKTTYP'])) $objekt[$i]['OBJEKTTYP'] = 0;
			switch ($objekt[$i]['OBJEKTTYP'])
			{
				case '0': //Villor
				case '7':	//Utland
					//echo $objekt[$i]['OBJEKTTYP'].' -> Villor<br>';
					$out[ 'villor' ][] = $objekt[$i];
					break;

				case '1':	//Fritidshus			lantställen
					//echo $objekt[$i]['OBJEKTTYP'].' -> Lantställen<br>';
					$out[ 'lantstallen' ][] = $objekt[$i];
					break;

				case '2':	//Lägenhet (BR)		våningar
				case '6': //Övrigt
					//echo $objekt[$i]['OBJEKTTYP'].' -> Våningar<br>';
					$out[ 'vaningar' ][] = $objekt[$i];
					break;

				default:
					echo '<b>Unknown OBJEKTTYP: '.$objekt[$i]['OBJEKTTYP'].', name: '.$objekt[$i]['OBJEKTTYPTEXT'].'</b><br>';
			}
		}

		foreach ($out as $key => $row) {
			//echo $key.':<br>';
			//print_r($row);
			write_sfd_data($row, $key.'.txt');
		}
		
		if (empty($out['villor']))			write_empty_sfd_data('villor.txt');
		if (empty($out['lantstallen']))	write_empty_sfd_data('lantstallen.txt');
		if (empty($out['vaningar']))		write_empty_sfd_data('vaningar.txt');
	}
	
	function write_empty_sfd_data($filename)
	{
		global $config;

		$filename = $config['server_cache_path'].$filename;

		echo '<b>Skriver tom fil '.$filename.'</b><br>';

		$data = 'antalobjekt=0&q=1';

		$fp = fopen($filename, 'w');
		fwrite($fp, $data);
		fclose($fp);
	}
	
	function write_sfd_data($objekt, $filename)
	{
		global $config;

		$filename = $config['server_cache_path'].$filename;
		
		echo '<b>Writing '.$filename.' ...</b><br>';
		
		$fp = fopen($filename, 'w');
	
		/* Visa all data */
		$cnt = count($objekt);
		if ($cnt > $config['max_objects']) $cnt = $config['max_objects'];		//Force max 15 objects to be written to disk
		
		fwrite($fp, "antalobjekt=".$cnt."&");
		for ($i=0; $i<$cnt; $i++)
		{	
			$objekt_id = $objekt[$i]['GID'];
	
			$obj = "objekt".($i+1);
			
			//fwrite($fp, "id_".$i."=".$objekt_id."\n");
			//fwrite($fp, "firma_".$i."=".$objekt[$i]['ETTOBJEKT']['attrs']['FIRMANR']."\n");
			//fwrite($fp, "typ_".$i."=".$objekt[$i]['OBJEKTTYP']['data']."\n");
			fwrite($fp, "adress_".($i+1)."=".urlencode($objekt[$i]['ADRESS'])."&");
			fwrite($fp, "storlek_".($i+1)."=".urlencode($objekt[$i]['STORLEK'])."&");
			fwrite($fp, "pris_".($i+1)."=".urlencode($objekt[$i]['PRIS'])."&");
	
			fwrite($fp, "beskr_".($i+1)."=".urlencode($objekt[$i]['BESKRIVNING'])."&");
			fwrite($fp, "url_".($i+1)."=".urlencode($objekt[$i]['URL'])."&");

			$pic_cnt = $objekt[$i]['antalbilder'];
			if ($pic_cnt > 1) $pic_cnt = 1;	//force max 1 image per object to be written to disk

			if ($pic_cnt == 0) {
				echo '<b>Image missing for object #'.($i+1).'!</b><br>';
				fwrite($fp, "images_".($i+1)."=1&");
				$thumb = $config['client_cache_path'].'noimage_thumb.jpg';
				fwrite($fp, "thumburl".($i+1)."_1=".urlencode($thumb)."&");
				
				echo '<img src="'.$config['server_cache_path'].'noimage_thumb.jpg"><br>';

			} else {			
				fwrite($fp, "images_".($i+1)."=".$pic_cnt."&");
				for ($j=0; $j<$pic_cnt; $j++) {
					//$image = "../sfd_cache/".$objekt_id."_".$j."_width".$config['image_width'].".jpg";
					//fwrite($fp, "imageurl".($i+1)."_".$j."=".urlencode($image)."&");
					//echo '<img src="'.$image.'"><br>';
		
					$thumb = $config['client_cache_path'].$objekt_id.'_'.$j.'_thumb.jpg';
					fwrite($fp, "thumburl".($i+1)."_".($j+1)."=".urlencode($thumb)."&");
	
					echo '<img src="'.$config['server_cache_path'].$objekt_id.'_'.$j.'_thumb.jpg"><br>';
				}
			}
	
			//echo 'Firma ID: '. $objekt[$i]['ETTOBJEKT']['attrs']['FIRMANR'].'<br>'; //alltid samma värde som skickades in med requesten (??)
			//echo 'ID: '.$objekt_id.'<br>';
			//echo 'Typ: '. $objekt[$i]['OBJEKTTYP']['data'].'<br>';
			//echo 'Adress: '.	$adress.'<br>';
			//echo 'Storlek: '.	$storlek.'<br>';
			//echo 'Pris: '.		$pris.'<br>';
			//echo 'Beskr.: '.	$beskr.'<br>';
			//echo '<br>';
	
			//echo 'Typ ID: '. $objekt[$i]['OBJEKTTYP']['attrs']['TYP'].'<br>';
			//echo '<img src="'. $objekt[$i]['PIC1URL'].'&sizex=400"><br>';
			//echo 'Pic1URL: '. $objekt[$i]['PIC1URL'].'<br>';
			//echo 'Pic1RelURL: '. $objekt[$i]['PICRELURL'].'<br>';
			//echo 'PDF1URL: '. $objekt[$i]['PDF1URL'].'<br>';
			//echo 'ObjektURL: '. $objekt[$i]['OBJEKTURL'].'<br>';
		}
		fwrite($fp, "q=1");
		fclose($fp);		
	}

?>