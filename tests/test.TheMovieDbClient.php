<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('TheMovieDbClient.php');

//die('XXX: cant easily autotest');


$temp = TempStore::getInstance();
//$temp->disable();


$title = 'Avatar';


$hit = TheMovieDbClient::search($title);
d($hit[0]);die;


/*
$details = TheMovieDbClient::getInfo( $hit[0]->tmdb_id );
d( $details );
*/


?>

