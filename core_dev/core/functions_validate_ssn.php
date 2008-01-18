<?
/**
 * $Id$
 *
 * Functions for validating / generating swedish social security numbers
 *
 * \todo validation of ssn's for other countries
 *
 * References:
 * http://sv.wikipedia.org/wiki/Personnummer_i_Sverige (in swedish)
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	define('SSN_INVALID_INPUT', 1);
	define('SSN_INVALID_DATE', 2);
	define('SSN_WRONG_CHECKSUM', 3);
	define('SSN_GENDER_IS_MALE',	4);
	define('SSN_GENDER_IS_FEMALE',	5);

	define('SSN_GENDER_UNKNOWN', 0);
	define('SSN_GENDER_MALE', 1);
	define('SSN_GENDER_FEMALE', 2);

	$ssn_error[SSN_INVALID_INPUT] = 'Invalid input';
	$ssn_error[SSN_INVALID_DATE] = 'Invalid date';	
	$ssn_error[SSN_WRONG_CHECKSUM] = 'Wrong checksum';
	$ssn_error[SSN_GENDER_IS_MALE] = 'Wrong gender specified, this ssn belongs to a male';
	$ssn_error[SSN_GENDER_IS_FEMALE] = 'Wrong gender specified, this ssn belongs to a female';

	/**
	 * Validates a swedish social security number (personnummer)
	 *
	 * \param $_persnr a swedish social security number (personnummer) in the format "YYYYMMDD-XXXX" or "YYMMDD-XXXX"
	 * \param $_gender 0=unknown, 1=male, 2=female
	 * \return true if the passed swedish personal number is correct, else a SSN_* error code
	 */
	function SsnValidateSwedish($_persnr, $_gender = SSN_GENDER_UNKNOWN)
	{
		$_persnr = str_replace('-', '', $_persnr);
		$_persnr = str_replace(' ', '', $_persnr);

		//year specified in 4 digits
		if (strlen($_persnr) == 12) $_persnr = substr($_persnr, 2);

		if (strlen($_persnr) != 10) return SSN_INVALID_INPUT;

		//validate if the date existed, for example 19810230 is invalid
		$yr = substr($_persnr, 0, 2);
		$yr = ($yr > date('y')) ? '19'.$yr : '20'.$yr;	//years below curryear is considered to be 2000-20xx, otherwise its 1900-19xx
		$mn = intval(substr($_persnr, 2, 2));
		$dy = intval(substr($_persnr, 4, 2));
		if (!checkdate($mn, $dy, $yr)) return SSN_INVALID_DATE;

		if (substr($_persnr, -1) != SsnCalcSumSwedish($_persnr)) return SSN_WRONG_CHECKSUM;

		$ssn_gender = intval(substr($_persnr, 8, 1));
		if (($ssn_gender % 2) && $_gender == SSN_GENDER_FEMALE) {
			//Error: odd (male) ssn found but user thinks its a female ssn
			return SSN_GENDER_IS_MALE;
		}
		if (!($ssn_gender % 2) && $_gender == SSN_GENDER_MALE) {
			//Error: even (female) ssn found but user thinks its a male ssn
			return SSN_GENDER_IS_FEMALE;
		}

		return true;
	}

	/**
	 * Calculates the checksum of a swedish social security number (personnummer)
	 *
	 * The personal identity number consists of 10 digits and a hyphen.
	 * The first six correspond to the person's birthday, in YYMMDD form.
	 * They are followed by a hyphen. The seventh through ninth are a serial number.
	 * An odd number is assigned to men, an even number to women.
	 * The tenth digit is a checksum which was introduced in 1967 when the system was computerized.
	 *
	 * To calculate the checksum, multiply the individual digits in the identity number with 212121-212.
	 * The resulting products (a two digit product, such as 16, would be converted to 1 + 6) are
	 * added together. The checksum is 10 minus the ones place digit in this sum.
	 *
	 * \param $_persnr a swedish social security number
	 * \return the calculated checksum for $_persnr
	 */
	function SsnCalcSumSwedish($_persnr)
	{
		$d2 = 2;
		$sum = 0;

		for ($i=0; $i<=8; $i++) {
			$d1 = intval(substr($_persnr, $i, 1));
			$res = $d1 * $d2;

			if ($res > 9) {
				$x1 = intval(substr($res, 0, 1));
				$x2 = intval(substr($res, 1, 1));
				$res = $x1 + $x2;
			}
			$sum += $res;
			$d2 = ($d2 == 2) ? 1 : 2; //Switch between 212121-212
		}

		//Substract the ones place digit from 10
		$sum = 10 - intval(substr($sum, -1, 1));
		if ($sum == 10) $sum = 0;

		return $sum;
	}

	/**
	 * Randomizes the last 3 digits and creates a valid control digit
	 *
	 * \param $_year number in "YY" or "YYYY" format
	 * \param $_month number in "MM" format
	 * \param $_day numbre in "DD" format
	 * \param $_gender SSN_GENDER_MALE or SSN_GENDER_FEMALE
	 * \return the last 4 digits of a swedish ssn
	 */
	function SsnRandomizeSwedish($_year, $_month, $_day, $_gender)
	{
    $persNr = substr($_year, -2).$_month.$_day;
    if (strlen($persNr) != 6) die;

    //Randomizes the 2 first of the control digits, between 00 and 99
    $randNums = substr('0'.mt_rand(0, 99), -2);

		//An odd number is assigned to men, an even number to women
		$randGender = mt_rand(0, 9);
    if ($randGender % 2) { //odd number
    	if ($_gender == 2) $randGender++;		//woman have even numbers,add 1
			if ($randGender > 9) $randGender = 0;
		} else {
			if ($_gender == 1) $randGender++;		//men have odd numbers,add 1
		}

		$sum = SsnCalcSumSwedish($persNr.$randNums.$randGender);
		return $randNums.$randGender.$sum;
	}
?>