<?php
/**
 * Creates a SQLite3 database of Swedish post numbers & addresses
 *
 * Creation of the db file takes ~15s and results in a 1.1 MiB big file
 *
 * The input file is created by db_postnr_export.ahk
 */

$in_file = 'norway_zipcodes-2011.01.21.csv';
$db_file = 'zipcodes_nor.db';

require_once('/devel/core_dev/trunk/core/core.php');
require_once('/devel/core_dev/trunk/core/CsvReader.php');

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
    // "1003 OSLO","Lindebergåsen Postboks 1-29","Postboksadresse","Oslo","Oslo"
    $r = CsvReader::parseRow($buf);

    // 0: Postnr / Postort
    // 1: Namn
    // 2: Addresstyp (gatuaddress, postbox)
    // 3: Kommune
    // 4: Fylke

    $postnr = substr($r[0], 0, 4);
    $postort = substr($r[0], 5);

    if (!is_numeric($postnr) || strlen($postnr) != 4)
        throw new Exception ('bad data');

    if (!in_array($postort, $cities))
        $cities[] = $postort;

    $city = array_search($postort, $cities);

    if (!in_array($r[4], $lans))
        $lans[] = $r[4];

    $lan = array_search($r[4], $lans);

    if (!in_array($r[3], $communes))
        $communes[] = $r[3];

    $commune = array_search($r[3], $communes);

    $q = 'INSERT INTO postnr (street,zipcode,city,lan,commune) VALUES ("'.$r[1].'",'.$postnr.','.$city.','.$lan.','.$commune.')';

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
