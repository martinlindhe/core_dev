<?
	/*
		This is a sample script describing how to integrate uReply with a separate user database.
		See login_remote_example.txt for more information
	
	*/

/*
	//fixme: non-working example code
	if (!empty($_POST['id']) && is_numeric($_POST['id']))
	{
		$user_id = $_POST['id'];
		$name = $_POST['name'];

		$server_url = 'http://213.100.91.161:16200/CMS/';

		$random_url = $server_url.'get_login_code.php?id='.$user_id.'&name='.urlencode($name);
		$random_password = file_get_contents($random_url);
		$login_url = $server_url.'login_remote.php?id='.$user_id.'&r='.$random_password;
		
		echo '<a href="'.$login_url.'">Log in</a>';
		die;
	}*/

	if (!empty($_POST['guid'])) {
		$user_guid = $_POST['guid'];
		$name = $_POST['name'];
		$age = $_POST['age'];

		$server_url = 'http://localhost/CMS/';
		$server_url = 'http://localhost:16200/CMS/';
		//$server_url = 'http://213.100.91.161:16200/CMS/';
		//$server_url = 'http://esp-reply01.osl.basefarm.net:8010/';

		$random_url = $server_url.'get_login_code.php?guid='.$user_guid.'&name='.urlencode($name).'&age='.urlencode($age);

		//echo 'random url: '.$random_url.'<br>';

		$random_password = file_get_contents($random_url);
		$login_url = $server_url.'login_remote.php?guid='.$user_guid.'&r='.$random_password;

		//echo 'guid: '.$user_guid.'<br>';
		//echo 'pwd: '.$random_password.'<br><br>';

		echo '<a href="'.$login_url.'">Log in</a>';

		die;
	}
?>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
	Enter user GUID: <input type="text" name="guid" size=40><br>
	Enter user name: <input type="text" name="name"><br>
	Enter user age: <input type="text" name="age"><br>	
	<input type="submit" class="button" value="Log in">
</form>