<?php
/**
 * $Id$
 *
 * EAN-13 (European Article Number / International Article Number) Barcode validator
 *
 * Used worldwide for marking products often sold at retail point of sale.
 *
 * http://en.wikipedia.org/wiki/European_Article_Number
 * http://en.wikipedia.org/wiki/List_of_GS1_country_codes
 *
 * http://en.wikipedia.org/wiki/EAN_8
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

/**
 * 13 digits (12 + check digit)
 * The numbers encoded in EAN barcodes are known as Global Trade Item Numbers (GTIN)
 *
 *    3 digits | 3 to 8 digits  | 2 to 6 digits  | 1 digit      |
 * GS1 prefix  | company number | item reference | check digit  |
 *
 * GS1 prefix = country code
 * company number = 3-8 digits depending on number of GTIN-13s required by the manufacturer to identify different product lines (in ISBN and ISSN, this component is used to identify the language in which the publication was issued and managed by a transnational agency covering several countries, or to identify the country where the legal deposits are made by a publisher registered with a national agency, and it is further subdivided any allocating subblocks for publishers; many countries have several prefixes allocated in the ISSN and ISBN registries).
 * item referernce = 2-6 digits (in ISBN and ISSN, it uniquely identifies the publication from the same publisher; it should be used and allocated by the registered publisher in order to avoid creating gaps; however it happens that a registered book or serial never gets published and sold).
 * check digit = computed modulo 10, where the weights in the checksum calculation alternate 3 and 1.
 */

//TODO: isolerad sqlite databas som innehåller GS1-country codes... sen gör en likadan för geoip (?)

//STATUS: wip

class BarcodeEan13
{
    protected $code;
    protected $gs1, $rest, $check;

    function __construct($n = '')
    {
        if ($n)
            $this->set($n);
    }

    function set($n)
    {
        $n = str_replace(' ', '', $n);

        if (!$n || !is_numeric($n))
            throw new Exception ('invalid format');

        if (strlen($n) != 13)
            throw new Exception ('invalid length');

        $this->code = $n;

        $this->gs1   = substr($this->code, 0, 3);
        $this->rest  = substr($this->code, 3, 9);
        $this->check = substr($this->code, -1, 1);

    }

    function getCountry()
    {
        if (substr($this->gs1, 0, 2) == '73') {
            // http://en.wikipedia.org/wiki/GS1_Sweden
            return 'Sweden';
        }

        //XXX lookup meaning: http://en.wikipedia.org/wiki/List_of_GS1_country_codes
        switch ($this->gs1) {
        case '729':
            return 'Israel';
        default:
            return 'XXX';
        }
    }

    private function calcCheck() //XXX: also works with EAN-8 numbers
    {
        $sum = 0;

        // make string 18 digits long, prefix with 0:s
        $code = strpre_exact($this->code, 18, '0');

        for ($i = 0; $i < strlen($code)-1; $i++)
        {
            $s = substr($code, $i, 1);
            $sum += ($i % 2) ? $s : $s * 3;
        }

        // round upwards to next ten-digit (eg: 47 => 50)
        $next_ten = ceil($sum / 10) * 10;
        return $next_ten - $sum;
    }

    function render()
    {
        if (!$this->code)
            throw new Exception ('no code loaded');


        $res =  'Barcode    : '.$this->code.ln();
        $res .= 'GS1 prefix : '.$this->gs1.' ('.$this->getCountry().')'.ln();
        $res .= 'More data  : '.$this->rest.ln();
        $res .= 'Check digit: '.$this->check;

        $calc = $this->calcCheck();
        if ($calc == $this->check)
            $res .= ' (OK)'.ln();
        else
            $res .= ', INVALID!!!! should be '.$calc.ln();

        return $res;
    }

}

?>
