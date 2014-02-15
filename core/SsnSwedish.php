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
 * Documentation:
 * http://en.wikipedia.org/wiki/Personal_identity_number_%28Sweden%29
 * http://sv.wikipedia.org/wiki/Organisationsnummer
 * Organisationsnummer, broschyr SKV 709 från Skatteverket:
 * http://www.skatteverket.se/download/18.70ac421612e2a997f85800040284/70909.pdf
 *
 * @author Martin Lindhe, 2007-2013 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('Luhn.php');

class OrgNoSwedish extends SsnSwedish
{
    protected static function cleanSsn($ssn)
    {
        $ssn = str_replace('-', '', $ssn);
        $ssn = str_replace(' ', '', $ssn);
        $ssn = trim($ssn);
        return $ssn;
    }

    static function isValid($ssn, $gender = 0)
    {
        $ssn = self::cleanSsn($ssn);

        if (strlen($ssn) != 10)
            throw new \Exception ('odd length');

        if (parent::calcLunh($ssn))
            return true;

        return false;
    }
}

class SsnSwedish
{
    const MALE   = 1;
    const FEMALE = 2;

    protected $valid = false;

    /* @return 12-digit ssn YYYYMMDDXXXX */
    protected static function cleanSsn($ssn)
    {
        $ssn = str_replace('-', '', $ssn);
        $ssn = str_replace(' ', '', $ssn);
        $ssn = trim($ssn);

        $last4 = '';

        switch (strlen($ssn)) {
        case 8:
            // YYYYMMDD
            $yr = substr($ssn, 0, 4);
            $mn = substr($ssn, 4, 2);
            $dy = substr($ssn, 6, 2);
            $last4 = '0000';
            break;

        case 10:
            // "YY" is converted to "YYYY"
            // years below current year is considered to be 2000-20xx, otherwise its 1900-19xx
            $yr = substr($ssn, 0, 2);
            $mn = substr($ssn, 2, 2);
            $dy = substr($ssn, 4, 2);

            $yr = ($yr > date('y')) ? '19'.$yr : '20'.$yr;

            $last4 = substr($ssn, 6, 4);
            break;

        case 12:
            $yr = substr($ssn, 0, 4);
            $mn = substr($ssn, 4, 2);
            $dy = substr($ssn, 6, 2);
            $last4 = substr($ssn, 8, 4);
            break;

        default:
            throw new \Exception ('uhw odd length of '.$ssn);
        }

        return $yr.$mn.$dy.$last4;
    }

    /**
     * @param $ssn a swedish SSN in the format "YYYYMMDD-XXXX" or "YYMMDD-XXXX"
     * @return true if checksum is correct
     */
    static function isValid($ssn, $gender = 0)
    {
        $ssn = self::cleanSsn($ssn);

        $yr = substr($ssn, 0, 4);
        $mn = substr($ssn, 4, 2);
        $dy = substr($ssn, 6, 2);

        // years in the future cant be valid ssn
        if ($yr > date('Y'))
            return false;

        // validate if the date existed, for example 19810230 is invalid
        if (!checkdate($mn, $dy, $yr))
            return false;

        if ($gender) {
            $ssn_gender = substr($ssn, 10, 1);

            // Error: odd (male) ssn found but user thinks its a female ssn
            if (($ssn_gender % 2) && $gender == SsnSwedish::FEMALE)
                throw new \Exception ('Wrong gender specified, this ssn belongs to a male');

            // Error: even (female) ssn found but user thinks its a male ssn
            if (!($ssn_gender % 2) && $gender == SsnSwedish::MALE)
                throw new \Exception ('Wrong gender specified, this ssn belongs to a female');
        }

        if (self::calcLunh($ssn))
            return true;

        return false;
    }

    /**
     * Calculates the checksum of a swedish social security number (personnummer)
     *
     * Using the Luhn algorithm
     */
    public static function calcLunh($ssn)
    {
        // remove first 2 digits of YYYY
        if (strlen($ssn) == 12)
            $ssn = substr($ssn, 2);

        if (strlen($ssn) != 10)
            throw new \Exception ('XXX should not happen');

        $sum = Luhn::Calculate(substr($ssn, 0, -1) );

        return substr($ssn, -1, 1) == $sum;
    }

    static function getTimestamp($ssn)
    {
        $ssn = self::cleanSsn($ssn);

        if (strlen($ssn) != 12 || !is_numeric($ssn))
            throw new \Exception ('isValid invalid ssn: '.$ssn);

        $yr = substr($ssn, 0, 4);
        $mn = substr($ssn, 4, 2);
        $dy = substr($ssn, 6, 2);

        return mktime(0, 0, 0, $mn, $dy, $yr);
    }

    static function getGender($ssn)
    {
        $ssn = self::cleanSsn($ssn);

        if (!$ssn)
            return false;

        if (!self::calcLunh($ssn))
            return false;

        $gender = substr($ssn, 10, 1);

        if ($gender % 2)
            return 'M';

        if (!$gender % 2)
            return 'F';
    }

    static function render($ssn)
    {
        return substr($ssn, 0, 6).'-'.substr($ssn, 6);
    }

}

?>
