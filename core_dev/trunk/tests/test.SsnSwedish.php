<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('SsnSwedish.php');
require_once('core.php');

if (!SsnSwedish::isValid('811218-9876')) echo "FAIL 1\n";  //known correct
if (!SsnSwedish::isValid('800726-2010')) echo "FAIL 2\n";  //known correct
if (SsnSwedish::isValid('800726-2222'))  echo "FAIL 3\n";  //known fake
if (!SsnSwedish::isValid('800222-0138')) echo "FAIL 4\n";  //known correct

if (!SsnSwedish::isValid('19800726-2010')) echo "FAIL 5\n";  //known correct
if (SsnSwedish::isValid('20800726-2010')) echo "FAIL 6\n";  // BAD, in the future!

if (!SsnSwedish::isValid('800726-2010', SsnSwedish::MALE)) echo "FAIL 7\n"; // known to be a male ssn

if (sql_date( SsnSwedish::getTimestamp('800726-2010') ) != '1980-07-26') echo "FAIL8\n";

if (SsnSwedish::getGender('800726-2010') != 'M') echo "FAIL 9\n";

if (!OrgNoSwedish::isValid('556684-8635')) echo "FAIL 10\n"; //Kryptonit AB
if (!OrgNoSwedish::isValid('556455-4656')) echo "FAIL 11\n"; //Unicorn Communications AB
if (!OrgNoSwedish::isValid('556632-0221')) echo "FAIL 12\n"; //Unicorn Interactive AB
if (!OrgNoSwedish::isValid('556539-1355')) echo "FAIL 13\n"; //Unicorn Telecom AB


?>
