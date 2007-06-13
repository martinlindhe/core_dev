<?
	/* Returns true if the passed swedish personal number is correct */
	function ValidPersNr($_persnr, $_gender = 0)//fixme: gender support
	{
		$_persnr = str_replace('-', '', $_persnr);

		//year specified in 4 digits
		if (strlen($_persnr) == 12) $_persnr = substr($_persnr, 2);

		if (strlen($_persnr) != 10) return false;

		$sum = calculateSum($_persnr);

		if (substr($_persnr,-1) == $sum) return true;
		return false;
	}

	function calculateSum($_persNr)
	{
		$d2 = 2;
		$sum = 0;

		for ($i=0; $i<=8; $i++) {
			$d1 = intval(substr($_persNr, $i, 1));
			//echo 'd1 = '.$d1.', d2 = '. $d2. ' ... res1 = '. ($d1 * $d2).'<br/>';
			$res1 = ($d1 * $d2);

			if ($res1 >= 10) {
				$x1 = intval(substr($res1, 0, 1));
				$x2 = intval(substr($res1, 1, 1));
				$res1 = $x1 + $x2;
			}
			$sum += $res1;

			if ($d2 == 2) {
				$d2 = 1;
			} else {
				$d2 = 2; //Switch between 212121-212
			}
		}

		//Substract the ones place digit from 10
		$sum = 10 - intval(substr($sum, -1, 1));
		If ($sum == 10) $sum = 0;

		return $sum;
	}

	//Randomizes the last 3 digits and creates a valid control digit
	function generateLastDigits($_year, $_month, $_day, $_gender)
	{
    $persNr = substr($_year, -2) . $_month . $_day;
    if (strlen($persNr) != 6) die;

    //Randomizes the 2 first of the control digits, between 00 and 99
    $randNums = mt_rand(0, 99);
    if ($randNums < 10) $randNums = '0'.$randNums;

		//An odd number is assigned to men, an even number to women.
		$randGender = mt_rand(0, 9);
    if ($randGender % 2) { //odd number
    	if ($_gender == 2) $randGender++;		//woman have even numbers,add 1
		} else {
			if ($_gender == 1) $randGender++;		//men have odd numbers,add 1
		}
		if ($randGender > 9) $randGender = 0;

		//generate a valid checksum
		$sum = calculateSum($persNr . $randNums . $randGender);

		return $randNums . $randGender . $sum;
	}
?>