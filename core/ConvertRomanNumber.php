<?php
/**
 * @author Martin Lindhe, 2010-2014 <martin@ubique.se>
 */

//TODO: reuse lookup array in getAsRoman() for decoding numbers, drop expand()

namespace cd;

require_once('IConvert.php');

class ConvertRomanNumber implements IConvert
{
    private $value; ///< holds "arabic" (integer) representation of roman number

    function __construct($s = 0)
    {
        $this->set($s);
    }

    public static function isValid($s)
    {
        preg_match('/(?<roman>[MDCLXVI]+)/i', $s, $x);
        if (isset($x['roman']) && $x['roman'] == $s)
            return true;

        return false;
    }

    function set($s)
    {
        if (is_numeric($s)) {
            $this->value = $s;
            return;
        }

        $s = strtoupper($s);

        if (!self::isValid($s))
            throw new \Exception ('invalid roman number: '.$s);

        // Expand subtractive notation in Roman numerals
        $s = str_replace('CM', 'DCCCC', $s);
        $s = str_replace('CD', 'CCCC', $s);
        $s = str_replace('XC', 'LXXXX', $s);
        $s = str_replace('XL', 'XXXX', $s);
        $s = str_replace('IX', 'VIIII', $s);
        $s = str_replace('IV', 'IIII', $s);

        $val = 0;

        $val += substr_count($s, 'M') * 1000;
        $val += substr_count($s, 'D') * 500;
        $val += substr_count($s, 'C') * 100;
        $val += substr_count($s, 'L') * 50;
        $val += substr_count($s, 'X') * 10;
        $val += substr_count($s, 'V') * 5;
        $val += substr_count($s, 'I');

        $this->value = $val;
    }

    function getAsInteger() { return $this->value; }

    function getAsRoman()
    {
        if (intval($this->value) != $this->value)
            throw new \Exception ('only handes integer values');

        $n = $this->value;

        if ($n > 4999)
            throw new \Exception ('Cannot represent numbers larger than 4999 in plain ASCII');

        $lookup = array(
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        );

        $res = '';

        foreach ($lookup as $roman => $value)
        {
            // Determine the number of matches
            $matches = intval($n / $value);

            // Store that many characters
            $res .= str_repeat($roman, $matches);

            // Substract that from the number
            $n = $n % $value;
        }

        return $res;
    }

    public static function convert($from, $to, $val)
    {
        if ($from != 'roman')
            throw new \Exception ("ep");

        if (!in_array($to, array('arabic', 'decimal')))
            throw new \Exception ("ep");

        $x = new ConvertRomanNumber($val);
        return $x->getAsInteger();
    }

}
