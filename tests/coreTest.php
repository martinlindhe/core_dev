<?php

namespace cd;

class coreTest extends \PHPUnit_Framework_TestCase
{
    public function testStrpreExact1()
    {
        $this->assertEquals( strpre_exact('4523', 8, '0'), '00004523');
        $this->assertEquals( strpre_exact('4523', 3, '0'), '523');
        $this->assertEquals( strpad_exact('1234', 8, ' '), '1234    ');
        $this->assertEquals( strpad_exact('1234', 3, ' '), '123');
    }

    public function testIsAlphanumeric1()
    {
        $this->assertEquals( is_alphanumeric('2'),               true);
        $this->assertEquals( is_alphanumeric('2.0'),             true);
        $this->assertEquals( is_alphanumeric('%'),               false);  // important not to allow control characters
        $this->assertEquals( is_alphanumeric('/'),               false);  // important not to allow control characters
        $this->assertEquals( is_alphanumeric('x\"x'),            false);  //  " is NOT ok
        $this->assertEquals( is_alphanumeric("x'x"),             false);  //  ' is NOT ok
        $this->assertEquals( is_alphanumeric('abc 123'),         false);  // space is NOT ok
        $this->assertEquals( is_alphanumeric('abc/123'),         false);  // slash is NOT ok
        $this->assertEquals( is_alphanumeric('abc123'),          true);
        $this->assertEquals( is_alphanumeric('a-1'),             true);
        $this->assertEquals( is_alphanumeric('a_2'),             true);
        $this->assertEquals( is_alphanumeric('2öäåaÄÄÖÅ'),       true);
        $this->assertEquals( is_alphanumeric('日本語'),          true);   // utf8 is ok
        $this->assertEquals( is_alphanumeric('한국어'),           true);
        $this->assertEquals( is_alphanumeric('لقمة'),            true);
        $this->assertEquals( is_alphanumeric(''),                true);
    }

    public function testByteCount1()
    {
        $this->assertEquals( byte_count(1024*2),                 '2 KiB');
        $this->assertEquals( byte_count(1024*1024*2),           '2 MiB');
        $this->assertEquals( byte_count(1024*1024*1024*2),       '2 GiB');
        $this->assertEquals( byte_count(1024*1024*1024*1024*2),  '2 TiB');
    }

    public function testInstr1()
    {
        $this->assertEquals( instr('abc 123', 'bc'),             true);
        $this->assertEquals( instr('abc 123', 'cb'),             false);
        $this->assertEquals( instr('abc', 'aa'),                 false);
        $this->assertEquals( instr('a', 'aa'),                   false);
        $this->assertEquals( instr('aa', 'a'),                   true);
    }

    public function testStripSpaces1()
    {
        $this->assertEquals( strip_spaces(' h  ell o  '),        'hello');
    }

    public function testSBool1()
    {
        $this->assertEquals( sbool(true),                        'true');
        $this->assertEquals( sbool(false),                       'false');
    }

    public function testStringToBool1()
    {
        $this->assertEquals( string_to_bool('true'),             true);
        $this->assertEquals( string_to_bool('false'),            false);
    }

    public function testBoolToInt1()
    {
        $this->assertEquals( bool_to_int(true),                  1);
        $this->assertEquals( bool_to_int(false),                 0);
    }

    public function testNumbersOnly1()
    {
        $this->assertEquals( numbers_only('123'),                true);
        $this->assertEquals( numbers_only('0'),                  true);
        $this->assertEquals( numbers_only('12a'),                false);
        $this->assertEquals( numbers_only('12.0'),               false);
        $this->assertEquals( numbers_only(''),                   false);
    }

    public function testIsNumberRange1()
    {
        $this->assertEquals( is_number_range('2-0'),             true);
        $this->assertEquals( is_number_range('2000-3000'),       true);
    }

    public function testStrBetween1()
    {
        $this->assertEquals( str_between('--abcxx', '--', 'xx'), 'abc');
        $this->assertEquals( str_between('-- abc xx', '--', 'xx'), ' abc ');
        $this->assertEquals( str_between('-1-', '-', '-'),       '1');
        $this->assertEquals( str_between('a1aa', 'a', 'a'),      '1');
    }

    public function testStrRemaining1()
    {
        $this->assertEquals( str_remaining(' two two three', ' two '),    'three');
        $this->assertEquals( str_remaining('one two three', ' two '),    'three');
        $this->assertEquals( str_remaining('one TWO three', ' two '),    false);
    }

    public function testFormatMSID1()
    {
        $this->assertEquals( formatMSID('0707123456', '46'),     '46707123456');
        $this->assertEquals( formatMSID('0707-123 456', '46'),   '46707123456');
        $this->assertEquals( formatMSID('46707123456', '46'),    '46707123456');
        $this->assertEquals( formatMSID('0046707123456', '46'),  '46707123456'); // 46 is country code for Sweden
        $this->assertEquals( formatMSID('04612345', '46'),       '464612345');   // 046 is area code for Lund, Sweden
        $this->assertEquals( formatMSID('0044', '46'),           '0044');        // dont touch short special codes
    }

    public function testIsUpperChar1()
    {
        $this->assertEquals( is_upper_char('A'),                 true);
        $this->assertEquals( is_upper_char('Å'),                 true);
        $this->assertEquals( is_upper_char('a'),                 false);
        $this->assertEquals( is_upper_char('å'),                 false);
        $this->assertEquals( is_upper_char('Z'),                 true);
        $this->assertEquals( is_upper_char('z'),                 false);
    }

    public function testIsLowerChar1()
    {
        $this->assertEquals( is_lower_char('A'),                 false);
        $this->assertEquals( is_lower_char('a'),                 true);
        $this->assertEquals( is_lower_char('Z'),                 false);
        $this->assertEquals( is_lower_char('z'),                 true);
    }

    public function testIsUpperStr1()
    {
        $this->assertEquals( is_upper_str("AAA"),              true);
        $this->assertEquals( is_upper_str("AaA"),              false);
        $this->assertEquals( is_upper_str("AAa"),              false);
        $this->assertEquals( is_upper_str("ÅAA"),              true);
        $this->assertEquals( is_upper_str("Åaa"),              false);
        $this->assertEquals( is_upper_str("AAå"),              false);
        $this->assertEquals( is_upper_str("PÅ"),               true);
    }

    public function testIsLowerStr1()
    {
        $this->assertEquals( is_lower_str("på"),               true);
        $this->assertEquals( is_lower_str("pÅ"),               false);
    }

    public function testIsUcFirstStr1()
    {
        $this->assertEquals( is_ucfirst_str("Hallå"),          true);
        $this->assertEquals( is_ucfirst_str("HallÅ"),          false);
        $this->assertEquals( is_ucfirst_str("HALLÅ"),          false);
    }

}
