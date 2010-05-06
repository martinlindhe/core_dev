<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SsnSwedish.php');

$ssn = new SsnSwedish('811218-9876'); //known correct
if (!$ssn->isValid()) echo "FAIL 1\n";


$ssn = new SsnSwedish('800726-2010'); //known correct
if (!$ssn->isValid()) echo "FAIL 2\n";

$ssn = new SsnSwedish('800726-2222'); //known fake
if ($ssn->isValid()) echo "FAIL 3\n";

$ssn = new SsnSwedish('800222-0138'); //known correct
if (!$ssn->isValid()) echo "FAIL 4\n";

$ssn = new SsnSwedish('800726-2010', SsnSwedish::MALE);
if (!$ssn->isValid()) echo "FAIL 5\n";



$ssn = new OrgNoSwedish('556684-8635'); //Kryptonit AB
if (!$ssn->isValid()) echo "FAIL 6\n";

$ssn = new OrgNoSwedish('556455-4656'); //Unicorn Communications AB
if (!$ssn->isValid()) echo "FAIL 7\n";

$ssn = new OrgNoSwedish('556632-0221');  //Unicorn Interactive AB
if (!$ssn->isValid()) echo "FAIL 8\n";

$ssn = new OrgNoSwedish('556539-1355'); //Unicorn Telecom AB
if (!$ssn->isValid()) echo "FAIL 9\n";


?>
