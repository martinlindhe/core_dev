<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('TheMovieDbClient.php');

//die('XXX: cant easily autotest');


$temp = TempStore::getInstance();
//$temp->disable();


$title = 'Avatar';

$movie = new TheMovieDbClient();
$movie->setApiKey('0c6598d3603824df9e50078942806320');

$hit = $movie->search($title);


//$details = $movie->getInfo( $hit->tmdb_id );
$details = $movie->getInfo( $hit->imdb_id );

d( $details );



?>

