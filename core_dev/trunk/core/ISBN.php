<?php
/**
 * $Id$
 *
 * ISBN (International Standard Book Number) handler
 *
 * http://en.wikipedia.org/wiki/ISBN
 *
 * The 10-digit ISBN format was developed by the International Organization for Standardization and was published in 1970 as international standard ISO 2108
 *
 * Since 1 January 2007, ISBNs have contained 13 digits, a format that is compatible with Bookland EAN-13s
 *
 * 978 & 979 is the "Bookland" country code used for all books
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: recognize & validate ISBN-10: "91-501-0143-9"  (10-digit ISBN was in use until 2007), algo here: https://secure.wikimedia.org/wikipedia/en/wiki/ISBN#ISBN-10

require_once('BarcodeEan13.php');

class ISBN
{
    /**
     * @return true if input is a valid ISBN-13 code (EAN-13)
     */
    static function isValid($s)
    {
        // XXX improve using regexp who matches on n-n-n-n-n  (always 5 numbers divided by -)
        $s = str_replace('-', '', $s);
        $s = trim($s);

        if (strlen($s) != 13 || !is_numeric($s))
            return false;

        $ean13 = new BarcodeEan13($s);
        if (!$ean13->isValid())
            return false;

        return true;
    }
}

?>
