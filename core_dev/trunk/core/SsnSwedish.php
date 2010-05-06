<?php
/**
 * $Id$
 *
 * Class to validate a swedish social security number (SSN)
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
 * Documentation:
 * http://sv.wikipedia.org/wiki/Personnummer_i_Sverige
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip
//TODO: make class for org-number

class SsnSwedish
{
    const MALE   = 1;
    const FEMALE = 2;

    private $ssn;
    var     $valid = false;

    function __construct($ssn = '', $gender = 0)
    {
        $this->set($ssn, $gender);
    }

    /**
     * @param $ssn a swedish SSN in the format "YYYYMMDD-XXXX" or "YYMMDD-XXXX"
     */
    function set($ssn, $gender = 0)
    {
        $ssn = str_replace('-', '', $ssn);
        $ssn = str_replace(' ', '', $ssn);

        //validate if the date existed, for example 19810230 is invalid
        if (strlen($ssn) == 10) { //2 digit year
            //years below current year is considered to be 2000-20xx, otherwise its 1900-19xx
            $yr = substr($ssn, 0, 2);
            $ssn = ($yr > date('y')) ? '19'.$ssn : '20'.$ssn;
        }

        $yr = substr($ssn, 0, 4);
        $mn = substr($ssn, 4, 2);
        $dy = substr($ssn, 6, 2);

        if (!checkdate($mn, $dy, $yr))
            throw new Exception ('Invalid date');

        //"YYYY" is converted to "YY"
        $ssn = substr($ssn, 2);

        if (strlen($ssn) != 10 || !is_numeric($ssn))
            throw new Exception ('Invalid input');

        $this->valid = false;
        $this->ssn   = $ssn;

        if (substr($this->ssn, -1) != $this->calcSum())
            throw new Exception ('Wrong checksum');

        $ssn_gender = substr($this->ssn, 8, 1);

        //Error: odd (male) ssn found but user thinks its a female ssn
        if (($ssn_gender % 2) && $gender == SsnSwedish::FEMALE)
            throw new Exception ('Wrong gender specified, this ssn belongs to a male');

        //Error: even (female) ssn found but user thinks its a male ssn
        if (!($ssn_gender % 2) && $gender == SsnSwedish::MALE)
            throw new Exception ('Wrong gender specified, this ssn belongs to a female');

        $this->valid = true;
    }

    /**
     * Calculates the checksum of a swedish social security number (personnummer)
     *
     * @return The calculated checksum for $_persnr
     */
    private function calcSum()
    {
        $d2  = 2;
        $sum = 0;

        for ($i = 0; $i <= 8; $i++) {
            $d1 = intval(substr($this->ssn, $i, 1));
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

    function render()
    {
        return substr($this->ssn, 0, 6).'-'.substr($this->ssn, 6);
    }

}




/**
 * Validates a Swedish ORG SSN.
 * Organization numbers dont need to be valid dates. Example: 556455-4656 (Unicorn Communications AB)
 *
 * @param $_yr year
 * @param $_mn month
 * @param $_dy day
 * @param $_last4 last 4 digits
 * @return True if valid, or a SSN error
 */
/*
function SsnValidateSwedishOrgNum($_yr, $_mn, $_dy, $_last4)
{
    if (strlen($_yr) == 4) $_yr = substr($_yr, -2);
    $ssn = $_yr . (strlen($_mn)==1?'0'.$_mn:$_mn) . (strlen($_dy)==1?'0'.$_dy:$_dy) . $_last4;

    if (substr($_last4, -1) != SsnCalcSumSwedish($ssn)) return SSN_ERR_WRONG_CHECKSUM;

    return true;
}
*/

/**
 * Randomizes the last 3 digits and creates a valid control digit
 *
 * @param $_year number in "YY" or "YYYY" format
 * @param $_month number in "MM" format
 * @param $_day number in "DD" format
 * @param $_gender SSN_GENDER_MALE or SSN_GENDER_FEMALE
 * @return the last 4 digits of a swedish ssn
 */
/*
function SsnRandomizeSwedish($_year, $_month, $_day, $_gender)
{
    $ssn = substr($_year, -2).$_month.$_day;
    if (strlen($ssn) != 6) return false;

    //Randomizes the 2 first of the control digits, between 00 and 99
    $randNums = substr('0'.mt_rand(0, 99), -2);

    //An odd number is assigned to men, an even number to women
    $randGender = mt_rand(0, 9);
    if ($randGender % 2) { //odd number
        if ($_gender == 2) $randGender++;        //woman have even numbers,add 1
        if ($randGender > 9) $randGender = 0;
    } else {
        if ($_gender == 1) $randGender++;        //men have odd numbers,add 1
    }

    $sum = SsnCalcSumSwedish($ssn.$randNums.$randGender);
    return $randNums.$randGender.$sum;
}
*/

?>
