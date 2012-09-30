<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Md5Hash.php');
require_once('Sha1Hash.php');
require_once('VideoHash.php');


$f = '/etc/fstab';
echo '      md5: '.Md5Hash::CalcFile($f)."\n";
echo '     sha1: '.Sha1Hash::CalcFile($f)."\n";
echo 'videohash: '.VideoHash::CalcFile($f)."\n";
die;

// https://secure.wikimedia.org/wikipedia/en/wiki/Examples_of_SHA_digests
if (Sha1Hash::CalcString('The quick brown fox jumps over the lazy dog') != '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12') echo "FAIL 1\n";
if (Sha1Hash::CalcString('') != 'da39a3ee5e6b4b0d3255bfef95601890afd80709') echo "FAIL 2\n";

// https://secure.wikimedia.org/wikipedia/en/wiki/MD5
if (Md5Hash::CalcString('The quick brown fox jumps over the lazy dog') != '9e107d9d372bb6826bd81d3542a419d6') echo "FAIL 3\n";
if (Md5Hash::CalcString('') != 'd41d8cd98f00b204e9800998ecf8427e') echo "FAIL 4\n";


//echo 'videohash: '.VideoHash::CalcString($s)."\n";
?>
