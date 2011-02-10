<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('LastFmClient.php');

$client = LastFmClient::getInstance();


/*
$res = $client->getAlbumCover('Eminem', 'Encore');
d($res);
*/



$res = $client->getArtistInfo('Eminem');
d($res);

d( LastFmClient::getBestImage( $res->images ));

?>
