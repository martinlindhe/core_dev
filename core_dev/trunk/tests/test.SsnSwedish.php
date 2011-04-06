<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SsnSwedish.php');

if (!SsnSwedish::isValid('811218-9876')) echo "FAIL 1\n";  //known correct
if (!SsnSwedish::isValid('800726-2010')) echo "FAIL 2\n";  //known correct
if (SsnSwedish::isValid('800726-2222'))  echo "FAIL 3\n";  //known fake
if (!SsnSwedish::isValid('800222-0138')) echo "FAIL 4\n";  //known correct

if (!SsnSwedish::isValid('800726-2010', SsnSwedish::MALE)) echo "FAIL 5\n"; // known to be a male ssn


if (!OrgNoSwedish::isValid('556684-8635')) echo "FAIL 6\n"; //Kryptonit AB
if (!OrgNoSwedish::isValid('556455-4656')) echo "FAIL 7\n"; //Unicorn Communications AB
if (!OrgNoSwedish::isValid('556632-0221')) echo "FAIL 8\n"; //Unicorn Interactive AB
if (!OrgNoSwedish::isValid('556539-1355')) echo "FAIL 9\n"; //Unicorn Telecom AB


echo SsnSwedish::getTimestamp('800726-2222');

?>
