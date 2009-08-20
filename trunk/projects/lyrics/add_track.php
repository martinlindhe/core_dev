<?php

require_once('config.php');

$session->requireLoggedIn();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

$record_id = $_GET['id'];
addTrack($record_id);

header('Location: show_record.php?id='.$record_id);
?>
