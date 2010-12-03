<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Token.php');

require_once('/home/ml/dev/fmf/textfeed/config.php'); //XXX for database

$tok = new Token();

$user_id = $tok->getOwner('priv_feed', '95110c8f190ddabf300b31ed67b5db82046cfaf8');

$tok->setOwner($user_id);

$tok->generate('bajs');




d($db);

?>
