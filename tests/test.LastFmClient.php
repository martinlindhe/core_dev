<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('LastFmClient.php');

$temp = TempStore::getInstance();
$temp->debug();

$client = LastFmClient::getInstance();


/*
$res = $client->getAlbumCover('Eminem', 'Encore');
d($res);
*/


$res = $client->getArtistInfo('dsfkghsdgskhdfgkjsdhfkghsdfg');
if ($res) echo "FAIL 1\n"; //XXX ska inte returnera någon artist-info


$res = $client->getArtistInfo('Eminem');
d( LastFmClient::getBestImage( $res->images ));

?>
