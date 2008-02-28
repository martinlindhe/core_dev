<?
	/*
		Intended to be called remotely.
		Pass work order "id" (returned by a successful SOAP command $client->newOrder() ...)

		Return values:
			1 = the order has been processed and completed
			0 = the order has not been processed yet
			empty return = the order doesnt exist
	*/

	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die('x');

	require_once('config.php');
	$session->requireLoggedIn();

	echo getWorkOrderStatus($_GET['id']);
?>