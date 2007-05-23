<?
	/* perform_work.php - execute scheduled work orders

		This script takes the 10 olderst work orders that's still uncompleted and performs them one by one
	*/

	require_once('config.php');

	require_once('design_head.php');

	performWorkOrders(10);

	require_once('design_foot.php');
?>