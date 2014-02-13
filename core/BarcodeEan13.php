<?php
/**
 * EAN-13 (European Article Number / International Article Number) Barcode validator
 *
 * Used worldwide for marking products often sold at retail point of sale.
 *
 * http://en.wikipedia.org/wiki/European_Article_Number
 * http://en.wikipedia.org/wiki/List_of_GS1_country_codes
 *
 * http://en.wikipedia.org/wiki/EAN_8
 *
 * @author Martin Lindhe, 2010-2014 <martin@ubique.se>
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
 * item reference = 2-6 digits (in ISBN and ISSN, it uniquely identifies the publication from the same publisher; it should be used and allocated by the registered publisher in order to avoid creating gaps; however it happens that a registered book or serial never gets published and sold).
 * check digit = computed modulo 10, where the weights in the checksum calculation alternate 3 and 1.
 */

//STATUS: wip

//XXX rework internals, make isValid() static!

//TODO: isolerad sqlite databas som innehåller GS1-country codes
 //XXX: calcCheck() also works with EAN-8 numbers.. fix by extending EAN-8 class when it is written

namespace cd;

require_once('Image.php');

class BarcodeEan13
{
    protected $code;
    protected $gs1, $company, $product, $check;
    protected $unknown; // holds unparsed company/product data

    protected $gs1_name, $company_name;

    function __construct($n = '')
    {
        if ($n)
            $this->set($n);
    }

    function ToString()
    {
        return $this->code;
    }

    function set($n)
    {
        $n = str_replace(' ', '', $n);

        if (!$n || !is_numeric($n))
            throw new \Exception ('invalid format');

        if (strlen($n) != 13)
            throw new \Exception ('wrong length: '.strlen($n));

        $this->code = $n;

        $this->gs1   = substr($this->code, 0, 3);
        $this->check = substr($this->code, -1, 1);

        //XXX lookup meaning: http://en.wikipedia.org/wiki/List_of_GS1_country_codes
        $c2 = substr($this->gs1, 0, 2);
        if ($c2 == 50)
            return $this->parseUK();

        if ($c2 >= 40 && $c2 <= 44)
            return $this->parseGermany();

        if ($c2 == '73')
            return $this->parseSweden();

        switch ($this->gs1) {
        case '729': $this->country = 'Israel'; break;
        default:
            $this->country = 'unknown country code '.$this->gs1;
        }
    }

    function getGs1Name() { return $this->gs1_name; }
    function getCompany() { return $this->company; }
    function getCompanyName() { return $this->company_name; }
    function getProduct() { return $this->product; }

    function isValid()
    {
        if ($this->calcCheck() == $this->check)
            return true;

        return false;
    }

    /**
     * Calculate checksum for EAN-13 or EAN-8 barcode numbers
     */
    private function calcCheck()
    {
        if (!is_numeric($this->code))
            throw new \Exception ('invalid barcode');

        $sum = 0;

        for ($i = strlen($this->code)-2; $i >= 0; $i--)
        {
            $s = substr($this->code, $i, 1);
            // echo $i.": ".$s." * ". (($i % 2) ? '3' : '1') ."\n";
            $sum += ($i % 2) ? $s * 3 : $s;
        }

        // echo "sum = ".$sum."\n";

        // round upwards to next ten-digit (eg: 47 => 50)
        $next_ten = ceil($sum / 10) * 10;
        $res = $next_ten - $sum;
        // echo "res = ".$res."\n";

        return $res;
    }

    /**
     * http://en.wikipedia.org/wiki/GS1_Sweden
     * http://www.gs1.se/sv/GS1-systemet/GEPIR---Nummerupplysning/
     */
    private function parseSweden()
    {
        $this->gs1_name = 'Sweden';

        //GS1-13 code structure: GS1-YYYYYY-ZZZ-C    3|6|3|1 = 13 digits where Y = company, Z = product
        $this->company = substr($this->code, 3, 6);
        $this->product = substr($this->code, 9, 3);

        switch ($this->company)
        {
        case '007003': case '007013': $name = 'Carlsberg Sverige AB'; break;
        case '040002': $name = 'Spendrups Bryggeriaktiebolag'; break;
        case '050007': $name = 'Findus Sverige AB'; break;
        default: $name = 'Unknown '.$this->company; break;
        }

        $this->company_name = $name;
    }

    private function parseGermany()
    {
        $this->gs1_name = 'Germany';
//        $this->unknown = substr($this->code, 3, -1);

        $this->company = substr($this->code, 3, 4); //XXX osäker
        $this->product = substr($this->code, 7, 5); //XXX osäker

        switch ($this->company) {
        case '0339': $name = 'Kraft Foods Deutschland GmbH'; break;
        default: $name = 'Unknown '.$this->company; break;
        }

        $this->company_name = $name;
    }

    private function parseUK()
    {
        $this->gs1_name = 'United Kingdom';
        $this->unknown = substr($this->code, 3, -1);
    }

    function renderDetails()
    {
        if (!$this->code)
            throw new \Exception ('no code loaded');

        $res  = 'Barcode : '.$this->code.ln();
        $res .= 'GS1     : '.$this->getGs1Name().' ('.$this->gs1.')'.ln();

        if ($this->company)
            $res .= 'Company : '.$this->getCompanyName().' ('.$this->company.')'.ln();

        if ($this->product)
            $res .= 'Product : '.$this->product.ln();

        if ($this->unknown)
            $res .= 'UNKNOWN : '.$this->unknown.ln();

        $res .= 'Checksum: '.$this->check;

        $calc = $this->calcCheck();
        if ($calc == $this->check)
            $res .= ' (OK)'.ln();
        else
            $res .= ', INVALID!!!! should be '.$calc.ln();

        return $res;
    }
}
