<?
/*
function doMail($to, $title, $text)
{
	$headers =
		'From: City Surf <martin@cs.icn.se>' . "\r\n" .
		'Content-Type: text/plain; charset=ISO-8859-1';

	if (mail($to, $title, $text, $headers)) return true;
	return false;
}
*/
	require_once('class.phpmailer.php');
	function doMail($to, $subject, $body)		//används inte
	{
		global $config;

		set_time_limit(60*5);
		
		$mail = new PHPMailer();

		$mail->IsSMTP();                    	// send via SMTP
		$mail->Host     = 'mail.unicorn.tv';	// SMTP servers
		$mail->SMTPAuth = true;								// turn on SMTP authentication
		$mail->Username = 'cs@unicorn.tv';						// SMTP username
		$mail->Password = '1234';						// SMTP password
		$mail->CharSet  = 'ISO-8859-1';

		$mail->From     = 'info@unicorn.tv';
		$mail->FromName = 'citysurf';

		$mail->AddAddress($to);

		$mail->IsHTML(false);                	// send as HTML?

		$mail->Subject  = $subject;
		$mail->Body     = $body;

		if (!$mail->Send()) {
			//echo 'Failed to send mail to '.$to.', error:'.$mail->ErrorInfo.'<br/>';
			return false;
		}

		return true;
	}



function valiDate($year, $month, $day) {
	if(!checkdate($month, $day, $year)) return false; else return true;
}

function valiNum($var) {
	$c=0;
	for($i=0; $i<strlen($var); $i++) {
		$asc=ord($var[$i]);
		if($asc>=48 && $asc<=57) {
			$c++;
		}
	}
	if($c == strlen($var)) {
		return true;
	} else {
		return false;
	}
}
function valiPnr($var, $gender = '') {
	$var = str_replace('-','',$var);
	$sum = 0;
	if(strlen($var) != 10) return false;
	if(!valiNum($var)) return false;
	$n = 2;
	for($i=0; $i<9; $i++) { 
		$tmp = $var[$i] * $n; 
		($tmp > 9) ? $sum += 1 + ($tmp % 10) : $sum += $tmp; ($n == 2) ? $n = 1 : $n = 2; 
	}
	if(!empty($gender)) {
		if($gender == 'M' && !($var[8] % 2)) return false;
		elseif($gender == 'F' && ($var[8] % 2)) return false;
	}
 
	return !( ($sum + $var[9]) % 10);
}
function valiSex($var) {
	$var = str_replace('-','',$var);
	$sum = 0;
	if(strlen($var) != 10) return false;
	if(!valiNum($var)) return false;
	$n = 2;
	for($i=0; $i<9; $i++) { 
		$tmp = $var[$i] * $n; 
		($tmp > 9) ? $sum += 1 + ($tmp % 10) : $sum += $tmp; ($n == 2) ? $n = 1 : $n = 2; 
	}
	if(!($var[8] % 2)) return 'F';
	elseif(($var[8] % 2)) return 'M';

	return 0;
}
function valiField($str, $type='normal', $val = '0') {
	if($type == 'normal')
		$pattern = "^[A-ZÁÀÉÈËÊÍÌÑÛÜÚÙİÅÄÖ´`a-záàéèëêóòíìñûüúùıÿåäöÅÄÖ .\/-]+$";
	elseif($type == 'user')
		$pattern = "^[a-zA-ZåäöÅÄÖ][a-zA-ZåäöÅÄÖ0-9_\-]{1,13}[a-zA-ZåäöÅÄÖ0-9]$";
	elseif($type == 'password')
		$pattern = "^[a-zåäöA-ZÅÄÖ0-9_@\-.]{3,20}$";
	elseif($type == 'mms')
		$pattern = "^[a-zåäöA-ZÅÄÖ0-9]{3,10}$";
	elseif($type == 'email')
		$pattern = "^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$";
	elseif($type == 'street')
		$pattern = "^[A-Z0-9ÁÀÉÈËÊÍÌÛÜÚÙİÅÄÖ´`a-záàéèëêíìóòûüúùıÿåäöÅÄÖ ,.\/:-]+$";
	elseif($type == 'postnr')
		$pattern = "^[0-9]{5}$";
	elseif($type == 'cell')
		$pattern = "^(07)[0-9]{8}$";
	elseif($type == 'birth')
		$pattern = "^[0-9]{8}(-)[0-9]{4}$";
	elseif($type == 'digit')
	    if($val!='0' && !empty($str))
	        $pattern = "^[0-9/'+]{".$val."}$";
	    else
	        $pattern = "^[0-9'+]+$";

	if(ereg($pattern, $str))
		return $str;
	else
		return false;
}
?>