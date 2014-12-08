<?php

namespace cd;

class XhtmlComponentInputTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $input = new XhtmlComponentInput();
        $input->name  = "hej";
        $input->value = 555;
        $input->size  = 10;

        $this->assertEquals(
            $input->render(),
            '<input type="text" name="hej" id="hej" value="555" size="10"/>'
        );
    }
}
