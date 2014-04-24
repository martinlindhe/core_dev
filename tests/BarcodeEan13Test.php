<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('BarcodeEan13.php');

class BarcodeEan13Test extends \PHPUnit_Framework_TestCase
{
    public function testIsValid()
    {
        $ean13 = array(
        '7310070030603', // pripps blå 3.5% burk (060 ??)
        '7310070130402', // pripps blå 3.5% sexpack (040 ??)
        '7310400021554', // norrlands guld 3.5% burk (155)
        '7310400021561', // norrlands guld 3.5% sexpack (156)
        '7310500078045', // Findus Lövtunn bit (804)
        '4000339039029', // Kraft Foods fejk-ost cheddar
        '5000159382731', // Snickers 10-pack (10x50 g) tillverkare = Mars
        );

        foreach ($ean13 as $e)
        {
            $x = new BarcodeEan13($e);
            $this->assertEquals($x->isValid(), true);
        }
    }
}
