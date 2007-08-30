<?
define("NRMSTR", '<strong><b><blockquote><font><p><br><hr><a><img><span><li><ol><ul><u><em><strike><b>');
define("ADMNRMSTR", '<strong><b><blockquote><font><p><br><hr><a><img><li><ol><ul><u><em><strike><b>');
function makeNR($str, $alias, $date, $to) {
	return "<br><br>>Från: ".$alias."<br>>Till: ".$to."<br>>Skickat: ".$date."<br>>".str_replace("<BR>", "<BR>>", trim($str));
}
function formatText($str, $isVip = true) {
	if($isVip) {
		
		$arr = array('&lt;', '&gt;');
		//$rep = array('&amp;#60;', '&amp;#62;');
		//$str = str_replace($arr, $rep, $str);
		$str = preg_replace("#javascript\:#is", "java script:", $str);
		$str = preg_replace("#vbscript\:#is", "vb script:", $str);
		$str = str_replace("`", "`", $str);
		$str = preg_replace("#moz\-binding:#is", "moz binding:", $str);

		$str = preg_replace('#(&\#*\w+)[\x00-\x20]+;#U',"$1;",$str);
		#$str = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$str);
		$str = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|xmlns)[^>]*>#iU',"$1>",$str);
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU','$1=$2nojavascript',$str);
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iU','$1=$2novbscript',$str);
		$str = preg_replace('#</*\w+:\w[^>]*>#i',"",$str);
		
		do {
			$oldstr = $str;
			$str = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$str);
		} while ($oldstr != $str);
		$match = array("#\<img#");
		$replace = array('<img onload="if(this.width > 658) this.width = 658;" ');
		$str = strip_tags($str, NRMSTR);
		$str = preg_replace($match, $replace, $str);
		$str = stripslashes($str);
		$str = trim($str, "\xA0");
	} else {
		$str = nl2br(secureOUT($str));
	}

	//fulhack av martin för att ordna till hårdkodade kompislänkar, suck
	//ändrar från href="http://www.citysurf.tv/user/view/9028">
	//till href="user_view.php?id=9028">

	do {
		$oldstr = $str;

		$pos1 = strpos($str, '<a href="http://www.citysurf.tv/user/view/');
		if ($pos1 !== false) {
			$pos2 = strpos($str, '">', $pos1);
			if ($pos2 !== false) {
			
				$x1 = substr($str, 0, $pos1);
				$mid = substr($str, $pos1, $pos2-$pos1);
				
				//echo $mid;
				$pos3 = strrpos($mid, '/');
				$id = substr($mid, $pos3+1);
				//echo ' id = '.$id;
				
				$x2 = substr($str, $pos2+2);

				$str = $x1.'<a href="user_view.php?id='.$id.'"><img src="thumb.php?id='.$id.'"></a>'.$x2;
			}
		}

	} while ($oldstr != $str);	

	return $str;
}

function secureFormat($str) {
	$str = secureOUT($str);
	$str = preg_replace("#javascript\:#is", "java script:", $str);
	$str = preg_replace("#vbscript\:#is", "vb script:", $str);
	$str = str_replace("`"               , "`"       , $str);
	$str = preg_replace("#moz\-binding:#is", "moz binding:", $str);
	$str = preg_replace('#iframe#is', '&#105;frame', $str);
	return $str;
}
?>
