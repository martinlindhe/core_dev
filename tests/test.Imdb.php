<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Imdb.php');

if (!Imdb::isValidId('tt0499549'))                                             echo "FAIL 1\n";
if (!is_imdb_url('http://www.imdb.com/title/tt1837642/'))                      echo "FAIL 2\n";
if (Imdb::getIdFromUrl('http://www.imdb.com/title/tt1837642/') != 'tt1837642') echo "FAIL 3\n";

?>
