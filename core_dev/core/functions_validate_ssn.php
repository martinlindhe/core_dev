<?php
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
	 * Cleans up user-inputted ssn
	 *
	 * \param $_ssn ssn to clean up
	 * \return cleaned up ssn
	 */
	function SsnCleanInput($_ssn)
	{
		$_ssn = str_replace('-', '', $_ssn);
		$_ssn = str_replace(' ', '', $_ssn);
	
		return $_ssn;
	}

	/**
	 * Validates a swedish social security number (personnummer)
	 *
	 * \param $_ssn a swedish social security number (personnummer) in the format "YYYYMMDD-XXXX" or "YYMMDD-XXXX"
	 * \param $_gender 0=unknown, 1=male, 2=female
	 * \return true if the passed swedish personal number is correct, else a SSN_* error code
	 */
	function SsnValidateSwedish($_ssn, $_gender = SSN_GENDER_UNKNOWN)
	{
		$_ssn = SsnCleanInput($_ssn);

		//year specified in 4 digits
		if (strlen($_ssn) == 12) $_ssn = substr($_ssn, 2);

		if (strlen($_ssn) != 10) return SSN_INVALID_INPUT;

		//validate if the date existed, for example 19810230 is invalid
		$yr = substr($_ssn, 0, 2);
		$yr = ($yr > date('y')) ? '19'.$yr : '20'.$yr;	//years below curryear is considered to be 2000-20xx, otherwise its 1900-19xx
		$mn = intval(substr($_ssn, 2, 2));
		$dy = intval(substr($_ssn, 4, 2));
		return SsnValidateSwedishNum($yr, $mn, $dy, substr($_ssn, -4));
	}

	function SsnValidateSwedishNum($_yr, $_mn, $_dy, $_last4, $_gender = SSN_GENDER_UNKNOWN)
	{
		if (!checkdate($_mn, $_dy, $_yr)) return SSN_INVALID_DATE;

		if (strlen($_yr) == 4) $_yr = substr($_yr, -2);
		$ssn = $_yr . (strlen($_mn)==1?'0'.$_mn:$_mn) . (strlen($_dy)==1?'0'.$_dy:$_dy) . $_last4;

		if (substr($_last4, -1) != SsnCalcSumSwedish($ssn)) return SSN_WRONG_CHECKSUM;

		$ssn_gender = intval(substr($ssn, 8, 1));
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
	 * \param $_ssn a swedish social security number
	 * \return the calculated checksum for $_persnr
	 */
	function SsnCalcSumSwedish($_ssn)
	{
		$d2 = 2;
		$sum = 0;

		for ($i=0; $i<=8; $i++) {
			$d1 = intval(substr($_ssn, $i, 1));
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
    $ssn = substr($_year, -2).$_month.$_day;
    if (strlen($ssn) != 6) die;

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

		$sum = SsnCalcSumSwedish($ssn.$randNums.$randGender);
		return $randNums.$randGender.$sum;
	}
?>
