<?php

namespace cd;

require_once __DIR__.'/../core/DifferenceEngine.php';

class DifferenceEngineTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $x1 = "din hatt har en kant\nDin med!\n";
        $x2 = "min hatt har en kant\nDin med!\n";

        $df  = new Diff( $x1, $x2 );

        //$form = new TableDiffFormatter();
        $form = new UnifiedDiffFormatter();

        $expected =
        "@@ -1,3 +1,3 @@\n".
        "- din hatt har en kant\n".
        "+ min hatt har en kant\n".
        "  Din med!\n".
        "  \n";

        $this->assertEquals($expected, $form->format($df));
    }
}
