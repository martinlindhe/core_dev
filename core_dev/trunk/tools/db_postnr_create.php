<?php
/**
 * Creates a SQLite3 database of Swedish post numbers & addresses
 *
 * Creation of the db file takes ~2m20s and results in a 22 MiB big file
 *
 * The input file is created by db_postnr_export.ahk
 */

$in_file = 'postnr.txt';
$db_file = 'postnr.db';

require_once('/devel/core_dev/trunk/core/core.php');

if (!class_exists('SQLite3'))
    throw new Exception ('sudo apt-get install php5-sqlite');

if (file_exists($db_file))
    unlink($db_file);

$db = new SQLite3($db_file);

$db->exec('CREATE TABLE postnr (id INTEGER PRIMARY KEY, street STRING, zipcode INTEGER, city INTEGER, lan INTEGER, commune STRING)');

$handle = fopen($in_file, 'r');
if (!$handle)
    die('EEEP');

$cnt = 0;

$db->exec('BEGIN');

$cities = array('');
$lans = array('');
$communes = array('');

while (($buf = fgets($handle, 4096)) !== false)
{
    // BERGHOLMEN;;;10005;STOCKHOLM;018601;STOCKHOLM;LIDINGÖ;LIDINGÖ;;01;
    $r = explode(';', $buf);

    // 0: Street
    // 1: Street number (from)
    // 2: Street number (to)
    // 3: Zipcode
    // 4: City
    // 5: LKF   --- ??? numerisk
    // 6: Län
    // 7: Commune (Kommun)
    // 8: Parish (Församling)
    // 9: Kommundel
    // 10: A-Region

    if (!in_array($r[4], $cities))
        $cities[] = $r[4];

    $city = array_search($r[4], $cities);

    if (!in_array($r[6], $lans))
        $lans[] = $r[6];

    $lan = array_search($r[6], $lans);

    if (!in_array($r[7], $communes))
        $communes[] = $r[7];

    $commune = array_search($r[7], $communes);

    $q = 'INSERT INTO postnr (street,zipcode,city,lan,commune) VALUES ("'.$r[0].'",'.$r[3].','.$city.','.$lan.','.$commune.')';

    $db->exec($q);
    $cnt++;

    if (!($cnt % 2000))
        echo $cnt." rows created\n";
}

$db->exec('COMMIT');


$db->exec('CREATE TABLE cities (id INTEGER PRIMARY KEY, name STRING)');
$db->exec('BEGIN');
foreach ($cities as $id => $name) {
    $q = 'INSERT INTO cities (id,name) VALUES ('.$id.',"'.$name.'")';
    $db->exec($q);
}
$db->exec('COMMIT');


$db->exec('CREATE TABLE lans (id INTEGER PRIMARY KEY, name STRING)');
$db->exec('BEGIN');
foreach ($lans as $id => $name) {
    $q = 'INSERT INTO lans (id,name) VALUES ('.$id.',"'.$name.'")';
    $db->exec($q);
}
$db->exec('COMMIT');


$db->exec('CREATE TABLE communes (id INTEGER PRIMARY KEY, name STRING)');
$db->exec('BEGIN');
foreach ($communes as $id => $name) {
    $q = 'INSERT INTO communes (id,name) VALUES ('.$id.',"'.$name.'")';
    $db->exec($q);
}
$db->exec('COMMIT');


if (!feof($handle))
    echo "Error: unexpected fgets() fail\n";

fclose($handle);

echo "Created ".$cnt." rows\n";

?>
