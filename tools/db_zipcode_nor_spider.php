<?php
/**
 * Fylke = som svensk län typ?   http://no.wikipedia.org/wiki/Fylke_(Norge)
 * norska postnummer är 4 siffror
 *
 */

// script ran 2011.01.21 and took ~ 11 minutes

require_once('/devel/core_dev/trunk/core/HttpClient.php');


$url = 'http://epab.posten.no/Norsk/searchchk.asp';


$out_file = 'out.csv';

$fp = fopen($out_file, 'w');

for ($digit = 0; $digit < 100; $digit++)
{
    $http = new HttpClient($url);

    $query = $digit;
    if ($query < 10)
        $query = '0'.$query;

    $res = $http->post( array('SearchCriteria' => 1, 'SearchType' => 2, 'SearchWord' => $query, 'btnSearch' => '    Søk') );

    $x = strip_tags($res);

    $key1 = 'Fylke';
    $key2 = 'Hvis du ikke finner svar';

    $p1 = strpos($x, $key1);
    $p2 = strpos($x, $key2);

    $x = substr($x, $p1 + strlen($key1), $p2 - $p1 - strlen($key1) );
    $x = trim($x);

    $rows = explode("\n", $x);

    for ($i = 0; $i < count($rows); $i++) {
        $rows[$i] = mb_convert_encoding($rows[$i], 'UTF-8', 'Windows-1252');  //convert to UTF8
        $rows[$i] = trim($rows[$i]);
    }

    for ($i = 0; $i < count($rows); $i += 8)
    {
        $res =
        '"'.$rows[$i].'",'.   // Postnr / Postort
        '"'.$rows[$i+1].'",'. // Namn
        '"'.$rows[$i+2].'",'. // Addresstyp (gatuaddress, postbox)
        '"'.$rows[$i+3].'",'. // Kommune
        '"'.$rows[$i+4].'"'.  // Fylke
        "\n";

        fputs($fp, $res);

        echo $res;
    }

    echo "sleep!\n";

    usleep(2000000); //2s pause
}

fclose($fp);


?>
