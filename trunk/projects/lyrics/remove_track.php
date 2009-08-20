<?php

require_once('config.php');

$session->requireLoggedIn();

if (empty($_GET['record']) || empty($_GET['track']) || !is_numeric($_GET['record']) || !is_numeric($_GET['track'])) die;

removeTrack($_GET['record'], $_GET['track']);

header('Location: show_record.php?id='.$_GET['record']);
die;
?>
