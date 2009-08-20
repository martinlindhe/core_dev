<?php

require_once('find_config.php');
$h->session->requireLoggedIn();

if (!isset($_GET['goto'])) {
	header('Location: index.php');
}

$tourl = $_GET['goto'];

// Start chat between two users
if (isset($_GET['otherid']) && is_numeric($_GET['otherid'])) {
	addSubscription(SUBSCRIPTION_USER_CHATREQ, $h->session->id, $_GET['otherid']);
}

//echo $tourl;
header('Location: '.$tourl);

?>
