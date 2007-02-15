<?
	session_start();		//Resume the session
	$_SESSION = array();	//Unset all of it's variables
	session_destroy();		//Destroy it

	header("Location: index.php");
?>