<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('time.php');

if (elapsed_seconds(30) != '30 seconds')                  echo "FAIL 1\n";
if (elapsed_seconds(60 * 30) != '30 minutes')             echo "FAIL 2\n";
if (elapsed_seconds(60 * 90) != '1.5 hours')              echo "FAIL 3\n";
if (elapsed_seconds(60 * 60 * 24 * 2) != '2 days')        echo "FAIL 4\n";
if (elapsed_seconds(60 * 60 * 24 * 14) != '2 weeks')      echo "FAIL 5\n";
if (elapsed_seconds(60 * 60 * 24 * 30 * 3) != '3 months') echo "FAIL 6\n";
if (elapsed_seconds(60 * 60 * 24 * 365 * 2) != '2 years') echo "FAIL 7\n";

if (in_seconds('00:00:00') != 0)                          echo "FAIL 8\n";
if (in_seconds('18:13:45') != 65625)                      echo "FAIL 9\n";
if (in_seconds('23:59:59') != 86399)                      echo "FAIL 10\n";
if (in_seconds('0:49:47.53') != 2987.53)                  echo "FAIL 11\n";

if (parse_duration('4h') != 14400)                        echo "FAIL 12\n";

if (!is_duration('2d'))                                   echo "FAIL 13\n";
if (is_duration('abc'))                                   echo "FAIL 14\n";
if (is_duration('1a2d'))                                  echo "FAIL 15\n";
if (!is_duration( 500 ))                                  echo "FAIL 16\n";

if ( sql_date(ts('2011-05-08')) != '2011-05-08')          echo "FAIL 17\n";
if ( sql_date(ts('20110508')) != '2011-05-08')            echo "FAIL 18\n";
if ( sql_date(ts('5/8/2011')) != '2011-05-08')            echo "FAIL 19\n";
if ( sql_date(ts('05/08/2011')) != '2011-05-08')          echo "FAIL 20\n";

?>
