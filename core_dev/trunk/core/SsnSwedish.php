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
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

class OrgNoSwedish extends SsnSwedish
{
    static function isValid()
    {
        // XXX validate how?
        return true;
    }
}

class SsnSwedish
{
    const MALE   = 1;
    const FEMALE = 2;

    protected $valid = false;

    static function isValid($ssn, $gender = 0)
    {
        $ssn = str_replace('-', '', $ssn);
        $ssn = str_replace(' ', '', $ssn);

        //"YYYY" is converted to "YY"
        if (strlen($ssn) == 12)
            $ssn = substr($ssn, 2);

        if (strlen($ssn) != 10 || !is_numeric($ssn))
            throw new Exception ('SsnSwedish set to invalid ssn: '.$ssn);

        //years below current year is considered to be 2000-20xx, otherwise its 1900-19xx
        $yr = substr($ssn, 0, 2);
        $yr = ($yr > date('y')) ? '19'.$yr : '20'.$yr;

        $mn = substr($ssn, 2, 2);
        $dy = substr($ssn, 4, 2);

        //validate if the date existed, for example 19810230 is invalid
        if (!checkdate($mn, $dy, $yr))
            return false;

        $ssn_gender = substr($ssn, 8, 1);

        //Error: odd (male) ssn found but user thinks its a female ssn
        if ($gender && ($ssn_gender % 2) && $gender == SsnSwedish::FEMALE)
            throw new Exception ('Wrong gender specified, this ssn belongs to a male');

        //Error: even (female) ssn found but user thinks its a male ssn
        if ($gender && !($ssn_gender % 2) && $gender == SsnSwedish::MALE)
            throw new Exception ('Wrong gender specified, this ssn belongs to a female');

        if (self::calcLunh($ssn))
            return true;

        return false;
    }

    /**
     * Calculates the checksum of a swedish social security number (personnummer)
     *
     * Using the Luhn algorithm
     *
     * @param $ssn a swedish SSN in the format "YYYYMMDD-XXXX" or "YYMMDD-XXXX"
     * @return true if checksum is correct
     */
    private static function calcLunh($ssn, $gender = 0)
    {
        $sum = 0;

        for ($i = 0; $i < strlen($ssn)-1; $i++)
        {
            $tmp = substr($ssn, $i, 1) * (2 - ($i & 1)); //Switch between 212121212
            if ($tmp > 9) $tmp -= 9;
            $sum += $tmp;
        }

        //Substract the ones place digit from 10
        $sum = (10 - ($sum % 10)) % 10;

        return substr($ssn, -1, 1) == $sum;
    }

    static function render($ssn)
    {
        return substr($ssn, 0, 6).'-'.substr($ssn, 6);
    }

}

?>
