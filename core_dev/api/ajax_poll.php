<?php

/**
 * $Id$
 *
 * Submit a vote
 */

require_once('find_config.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" ?>';

if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i']) || empty($_GET['o']) || !is_numeric($_GET['o'])) die('<bad/>');

addPollVote($_GET['i'], $_GET['o']);

echo '<ok/>';
?>
