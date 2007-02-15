<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (empty($_GET['option']) || !is_numeric($_GET['option'])) die;
	$option = $_GET['option'];

	include('design_head.php');

	$subscription_time_start = mktime($_SESSION['timedsubscription']['hour1'],$_SESSION['timedsubscription']['minute1'],0, $_SESSION['timedsubscription']['start_month'], $_SESSION['timedsubscription']['start_day'], $_SESSION['timedsubscription']['start_year']);


	$data = getTimedSubscriptionCategory($db, $option);
	$remind_msg = dbAddSlashes($db, $data['categoryName']);

	$remind_dest = dbAddSlashes($db, '0707308763');	//where to send reminder


	if (isset($_GET['confirmed'])) {

		switch ($_SESSION['timedsubscription']['frequency'])
		{
			case 'daily':
				//create daily reminder, remindType=1
				//remindMethod=2 = send reminder by SMS
				$sql = 'INSERT INTO tblTimedSubscriptions SET remindType=1, remindMethod=2, remindDest="'.$remind_dest.'", remindOption='.$option.', remindMsg="'.$remind_msg.'", timeCreated='.time().',timeStart='.$subscription_time_start.',userId='.$_SESSION['userId'].',userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
				dbQuery($db, $sql);
				echo 'Subscription created!';
				break;
			
			default:
				echo 'ERROR!!!!11';
		}		
		
	} else {
		echo '<b>timed subscriptions - confirmation page</b><br><br>';
		//echo 'type: '.$_SESSION['timedsubscription']['frequency'].'<br>';

		switch ($_SESSION['timedsubscription']['frequency'])
		{
			case 'daily':
				echo 'You want to be reminded every day at ';
				echo $_SESSION['timedsubscription']['hour1'].':'.$_SESSION['timedsubscription']['minute1'].'<br><br>';
				echo 'Starting from ';
				echo $_SESSION['timedsubscription']['start_day'].' '.$_SESSION['timedsubscription']['start_month'].' '.$_SESSION['timedsubscription']['start_year'].'<br><br>';

				echo 'Is this correct?<br><br>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?option='.$option.'&confirmed">Yes, create this reminder!</a><br><br>';
				echo 'No, go back';
				break;
		
			default:
				echo 'what to do?!!';
		}
	}

	include('design_foot.php');
?>