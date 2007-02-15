<?
    //header("Content-type: image/png");

    $string = "HEJ";
    $im    = imagecreatefrompng("random.png");
    $color = imagecolorallocate($im, 0xB0, 0x40, 0x60);
    //imagestring($im, 5, 2, 2, $string, $color);
    
	imagettftext($im, 20, 0, 5, 16, $color, "c:/winnt/fonts/arial.ttf", $string);

    imagepng($im);
?>
