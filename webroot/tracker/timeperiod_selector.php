<?
	if (!empty($_GET['d']) && is_numeric($_GET['d']))
	{
		//Sets $time_from to specified day, at 00:00
		$time_from = mktime(0, 0, 0, date('n', $_GET['d']), date('j', $_GET['d']), date('Y', $_GET['d']));
		$time_to = $time_from + (3600 * 24) - 1;

		$time_selected = 'd='.$_GET['d'];
	}
	else if (!empty($_GET['w']) && is_numeric($_GET['w']))
	{
		//Sets $time_from to specified day, at 00:00
		//todo: sätt $time_from till måndagen samma vecka som time_from är
		$time_from = mktime(0, 0, 0, date('n', $_GET['w']), date('j', $_GET['w']), date('Y', $_GET['w']));
		$time_to = $time_from + (3600 * 24 * 7) - 1;

		$time_selected = 'w='.$_GET['w'];
	}
	else if (!empty($_GET['m']) && is_numeric($_GET['m']))
	{
		//Sets $time_from to specified month, the 1:st day of month at 00:00
		$time_from = mktime(0, 0, 0, date('n', $_GET['m']), 1, date('Y', $_GET['m']));
		$days = date('t', $time_from);
		$time_to = $time_from + (3600 * 24 * $days) - 1;

		$time_selected = 'm='.$_GET['m'];
	}
	else if (!empty($_GET['f']))
	{
		$free_span = $_GET['f'];

		$pos = strpos($free_span, '_');
		if ($pos === false) die;

		$arr1 = explode('-', substr($free_span, 0, $pos) );
		$arr2 = explode('-', substr($free_span, $pos + 1) );

		if (sizeof($arr1) != 3 || sizeof($arr2) != 3) die;

		//todo: om man har valt den 31:a t.ex i en månad med bara 30 dagar, så ska den sista dagen väljas
		$time_from = mktime(0, 0, 0, $arr1[1], $arr1[2], $arr1[0]);
		$time_to = mktime(0, 0, 0, $arr2[1], $arr2[2], $arr2[0]);
		
		$time_selected = 'f='.$_GET['f'];
	}
	else {
		$time_from = 0;
		$time_to = 0;
		$time_selected = '';
	}
?>