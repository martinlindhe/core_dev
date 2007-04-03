<?php
function nicedate($date) {
	$str = strtotime($date);
	$today = date("Y-m-d", $str);
	$return = '';
	if(date("Y-m-d") == $today) $return .= '';
	else if(date("Y-m-d", strtotime("-1 day")) == $today) $return .= 'igår';
	else if(date("Y-m-d", strtotime("+1 day")) == $today) $return .= 'imorgon';
	else $return .= (strftime("%A %B %d", $str)).strftime(" %B", $str);

	$y = date('Y', $str);
	if(date('Y') != $y) $return .= ' '.$y;

	$return .= ' kl '.date('H:i', $str);
	return $return;
}
$date = '2006-01-06 15:65';
setlocale(LC_TIME, "swedish");
print nicedate($date);
setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
print nicedate($date);
setlocale(LC_ALL, 'sv_SE');
print nicedate($date);
setlocale(LC_ALL,"swedish");
print nicedate($date);
setlocale(LC_ALL,"sv_SE");
print nicedate($date);
setlocale(LC_ALL,"sv_SE.ISO8859-1");
print nicedate($date);
setlocale(LC_ALL,"sv_SE.UTF-8");
print nicedate($date);


?> 