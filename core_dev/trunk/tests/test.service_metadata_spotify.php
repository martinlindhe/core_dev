<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('service_metadata_spotify.php');

if (!is_spotify_uri('spotify:album:5fMriFQESKP2AWddR4jypS')) echo "FAIL 1\n";
if (!is_spotify_uri('spotify:artist:4YrKBkKSVeqDamzBPWVnSJ')) echo "FAIL 2\n";
if (!is_spotify_uri('spotify:track:3zBhJBEbDD4a4SO1EaEiBP')) echo "FAIL 3\n";

if (is_spotify_uri('spotify:album:')) echo "FAIL 4\n";
if (is_spotify_uri('spotify:album:sö')) echo "FAIL 5\n";
if (is_spotify_uri('spotify::sö')) echo "FAIL 6\n";
if (is_spotify_uri('spotify:xx:sö')) echo "FAIL 7\n";
if (is_spotify_uri('xxx:xxx:xxxx')) echo "FAIL 8\n";
if (is_spotify_uri('xxx')) echo "FAIL 9\n";
if (is_spotify_uri('::')) echo "FAIL 10\n";


//1. input: release namn & typ
//$name = 'Guns_N_Roses-Chinese_Democracy-RETAIL-2008-ESC';
$name = 'Mohammed Ali - 2009 - Processen';

//2. XXX rensa uipp input-namn, utifrån input typ
$artist = 'Mohammed Ali';
$album = 'Processen';


$spot = new SpotifyMetadata();

$id = $spot->getAlbumId($artist, $album);
d($id);

//5. XXX lookup album information, cache for 7 days
//http://ws.spotify.com/lookup/1/?uri=spotify:album:6G9fHYDCoyEErUkHrFYfs4&extras=trackdetail




?>
