<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('time.php');

if (elapsed_seconds(30) != '30 seconds')                  echo "FAIL 1\n";
if (elapsed_seconds(60 * 30) != '30 minutes')             echo "FAIL 2\n";
if (elapsed_seconds(60 * 90) != '1.5 hours')              echo "FAIL 3\n";
if (elapsed_seconds(60 * 60 * 24 * 2) != '2 days')        echo "FAIL 4\n";
if (elapsed_seconds(60 * 60 * 24 * 12) != '12 days')      echo "FAIL 5\n";
if (elapsed_seconds(60 * 60 * 24 * 14) != '2 weeks')      echo "FAIL 6\n";
if (elapsed_seconds(60 * 60 * 24 * 30 * 3) != '3 months') echo "FAIL 7\n";
if (elapsed_seconds(60 * 60 * 24 * 365 * 2) != '2 years') echo "FAIL 8\n";
if (elapsed_seconds(60 * 60 * 24 * 1) != '1 day')         echo "FAIL 9\n";

if (in_seconds('00:00:00') != 0)                          echo "FAIL 10\n";
if (in_seconds('18:13:45') != 65625)                      echo "FAIL 11\n";
if (in_seconds('23:59:59') != 86399)                      echo "FAIL 12\n";
if (in_seconds('0:49:47.53') != 2987.53)                  echo "FAIL 13\n";
if (in_seconds('00:26:36,595') != 1596.595)               echo "FAIL 14\n";
if (in_seconds('18:40:22')    != 67222)                   echo "FAIL 15\n";
if (in_seconds('18:40:22.11') != 67222.11)                echo "FAIL 16\n";
if (in_seconds('18:40:22,11') != 67222.11)                echo "FAIL 17\n";

if (seconds_to_hms(65625)   != '18:13:45')                          echo "FAIL 20\n";
if (seconds_to_hms(0)       != '00:00:00')                          echo "FAIL 21\n";
if (seconds_to_hms(86399)   != '23:59:59')                          echo "FAIL 22\n";
if (seconds_to_hms(2987.53) != '0:49:47.53')                        echo "FAIL 23\n";
if (seconds_to_hms(1596.595, true, 3, ',')       !=  '0:26:36,595') echo "FAIL 24\n";
if (seconds_to_hms(1596.595, true, 3, ',', true) != '00:26:36,595') echo "FAIL 25\n";

if (!is_duration('2d'))                                   echo "FAIL 30\n";
if (is_duration('abc'))                                   echo "FAIL 31\n";
if (is_duration('1a2d'))                                  echo "FAIL 32\n";
if (!is_duration( 500 ))                                  echo "FAIL 33\n";

if ( sql_date(ts('2011-05-08')) != '2011-05-08')          echo "FAIL 40\n";
if ( sql_date(ts('20110508')) != '2011-05-08')            echo "FAIL 41\n";
if ( sql_date(ts('5/8/2011')) != '2011-05-08')            echo "FAIL 42\n";
if ( sql_date(ts('05/08/2011')) != '2011-05-08')          echo "FAIL 43\n";

if (!is_hms('12:44:11.21'))                               echo "FAIL 50\n";
if (!is_hms('00:00:00'))                                  echo "FAIL 51\n";
if (!is_hms('00:00:00.00'))                               echo "FAIL 52\n";
if (is_hms('123.123.123'))                                echo "FAIL 53\n";
if (!is_hms('12:44:11,21'))                               echo "FAIL 54\n";
if (!is_hms('00:26:36,595'))                              echo "FAIL 55\n";
if (!is_hms('00:00:0,500'))                               echo "FAIL 56\n";

if (parse_duration('4h') != 14400)                        echo "FAIL 60\n";
if (parse_duration('-4h') != -14400)                      echo "FAIL 61\n";

if (num_days('2010-03-04', '2010-03-04') !=  1)           echo "FAIL 70\n";
if (num_days('2010-03-04', '2010-03-06') !=  3)           echo "FAIL 71\n";
if (num_days('2010-01-01', '2010-02-24') != 55)           echo "FAIL 72\n";
if (num_days('2010-03-01', '2010-03-31') != 31)           echo "FAIL 73\n";
if (num_years('1980-05-26', '1990-02-12') != 9)           echo "FAIL 74\n";

if (ts(0)  != 0)    echo "FAIL 80\n";
if (ts('') != 0)    echo "FAIL 81\n";

if (!is_ymd('2011-12-31')) echo "FAIL 90\n";
if (is_ymd('2011-12-32'))  echo "FAIL 91\n";
if (is_ymd('2011-13-01'))  echo "FAIL 92\n";

if (!is_year_period('1945-53'))   echo "FAIL 100\n";
if (!is_year_period('1993-2008')) echo "FAIL 101\n";

if (!is_dm('1/1'))          echo "FAIL 110\n";
if (!is_dm('31/12'))        echo "FAIL 111\n"; // december 31:st
if (is_dm('32/12'))         echo "FAIL 112\n";
if (is_dm('12/13'))         echo "FAIL 113\n";
if (is_dm('0/1'))           echo "FAIL 114\n";
if (is_dm('1/0'))           echo "FAIL 115\n";

?>
