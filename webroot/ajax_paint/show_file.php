<?
	include('config.php');

	function hexcol($col)
	{
		//converts a 32-bit rgb value returned from imagecolorat() to a html hex color code

		$r = ($col >> 16) & 0xFF;
		$g = ($col >> 8) & 0xFF;
		$b = $col & 0xFF;

		//todo: behövs allt detta?:
		return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT).str_pad(dechex($g), 2, '0', STR_PAD_LEFT).str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
	}

	$filename = $config['files']['upload_path'].'tile1.png';
	
	$im = imagecreatefrompng($filename);
	if (!$im) {
		echo 'failed to open file: '.$filename;
		die;
	}
	
	echo '<table cellpadding=0 cellspacing=0 border=0>';
	for ($y=0; $y<imagesy($im); $y++) {
		echo '<tr>';
		for ($x=0; $x<imagesx($im); $x++) {
			$rgb = imagecolorat($im, $x, $y);
			echo '<td bgcolor="'.hexcol($rgb).'" width=10 height=10></td>';
		}
		echo '</tr>';
	}
	echo '</table>';

?>