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


?>
