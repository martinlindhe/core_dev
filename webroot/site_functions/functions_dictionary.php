<?
	$config['dict'] = array(
						'IP-nummer'			=> 'en unik siffra som identifierar en dator p&aring; internet',
					);


	/* Returns $string, with html inserted for recognized words */
	/* Optimization done: Only includes javascripts if the text contained any words from the dictionary */
	function dictExplainWords($org_string)
	{
		global $config;

		$string = $org_string;

		foreach ($config['dict'] as $word => $desc) {
			$new = '<dfn title="'.$desc.'">'.$word.'</dfn>';
			//$new = '<a href="#" class="definition" onmouseover="domTT_activate(this,event,\'caption\',\'dictionary:: <b>'.$word.'</b>\',\'content\',\''.$desc.'\', \'trail\', \'x\');">'.$word.'</a>';
			$string = str_replace($word, $new, $string);
		}

		if ($string != $org_string)
		{
/*
			$js = '<script type="text/javascript" language="javascript" src="js/domLib.js"></script>'.
						'<script type="text/javascript" language="javascript" src="js/domTT.js"></script>'.
						'<script type="text/javascript" language="javascript">'."\nvar domTT_styleClass='domTTstyle';\n".'</script>';
*/
			$js = '';
			
			return $js.$string;
		}
	
		/* No words were explained */
		return $org_string;
	}
?>