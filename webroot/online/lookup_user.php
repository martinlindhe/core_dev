<?
	include_once("functions/include_all.php");

	if (!isset($_POST["user"]) || !$_POST["user"]) {
		header("Location: index.php"); die;
	}
	
	$user = $_POST["user"];
	if (!getUserName($db, $user)) {
	
		if (!getUserId($db, $user)) {
		
			include("design_head.php");
		
			echo "User ".$user." not found.<br><br>";
			echo "<a href=\"admin.php\">&raquo; Go back to Administration screen</a><br>";
		
			include("design_foot.php");
			die;
		} else {
			$id = getUserId($db, $user);
		}
	} else {
		$id = $user;
	}

	header("Location: show_user.php?id=".$id);
	
?>