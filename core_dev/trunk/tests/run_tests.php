<?php
/**
 * $Id$
 *
 * This script executes all files in current directory matching "test.*.php" and prints their output
 *
 *
 * Syntax check all scripts:
 *    for filnamn in ./test.*.php; do php -l "$filnamn"; done
 *
 */

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('files.php');

$tests = expand_arg_files('./', array('test*.php') );

$current = 0;
foreach ($tests as $test)
{
    $current++;
    echo "Running ".$current."/".count($tests).": ".$test." ...\n";

    $c = 'php '.$test;
    $ret = passthru($c);
    echo "------------------------------------------\n";
}

//XXX: räkna output
//XXX: summera output


?>
