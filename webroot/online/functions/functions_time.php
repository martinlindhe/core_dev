<?
	/* Calculates the local time from local GMT difference */
	function makeLocalTime($gmt_diff) {
		global  $long_date;
		if (strlen($gmt_diff) != 5) return false;

		$gmt_current = strtotime(gmdate("Y-n-d H:i:s"));

		$sign=substr($gmt_diff,0,1);
		$hour=substr($gmt_diff,1,2);
		$min =substr($gmt_diff,3,2);

		if ($sign == "+") {
			$result = $gmt_current + ($hour*60*60) + ($min*60);
		} else {
			$result = $gmt_current - ($hour*60*60) - ($min*60);
		}
		return $result;
	}
	
	/* Returns a sting like: 4 hours, 10 minutes and 3 seconds */
	function makeTimePeriod($seconds) {
		$retval="";

		//år
		$a=date("Y",$seconds)-1970;
		if($a==1) $retval=$a." year, ";
		else if($a>0) $retval=$a." years, ";
		$seconds -= (((($a*60)*60)*24)*30)*365;
		
		//månader
		$a=date("n",$seconds)-1;
		if($a==1) $retval.=$a." month, ";
		else if($a>0) $retval.=$a." months, ";
		$seconds -= ((($a*60)*60)*24)*30;

		//dagar
		$a=date("j",$seconds)-1;
		if($a==1) $retval.=$a." day, ";
		else if ($a>0) $retval.=$a." days, ";
		$seconds -= (($a*60)*60)*24;

		//timmar
		$a=date("H",$seconds)-1;
		if($a==1) $retval.=$a." hour, ";
		else if ($a>0) $retval.=$a." hours, ";
		$seconds -= ($a*60)*60;

		//minuter
		$a=date("i",$seconds);
		if($a==1) $retval.=$a." minute, ";
		else if ($a>0) $retval.=$a." minutes, ";
		$seconds -= $a*60;

		//seconds
		$a=date("s",$seconds)-0; //translate from 09 to 9 quickly ;)
		if($a==1) $retval.=$a." second";
		else $retval.=$a." seconds";
			
		return $retval;
	}
	
?>