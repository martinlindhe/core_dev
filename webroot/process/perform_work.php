<?
	/* perform_work.php - körs regelbundet
	
		detta script plockar fram de 10 äldsta arbetsuppgifterna från databasen och utför dessa en i taget
	*/

	require_once('config.php');
	
	require_once('design_head.php');

	performWorkOrders(10);

	require_once('design_foot.php');
?>