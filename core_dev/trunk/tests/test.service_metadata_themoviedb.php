<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('service_metadata_themoviedb.php');


$title = 'Avatar';

$movie = new TheMovieDbMetadata();
$movie->setApiKey('0c6598d3603824df9e50078942806320');

$x = $movie->search($title);

?>

