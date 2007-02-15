<?
	include("include_all.php");
	
	$show = "";
	if (isset($_GET["id"]) && $_GET["id"] != $_SESSION["userId"]) {
		$show = $_GET["id"];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header("Location: ".$config['start_page']);
			die;
		}
	} else if ($_SESSION["userId"]) {
		$show     = $_SESSION["userId"];
		$showname = $_SESSION["userName"];
	}
	
	if (substr($showname, -1) == "s") {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname."s";
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Spanar in sina bes&ouml;kare');
	} else {
		setUserStatus($db, 'Spanar in '.$niceshowname.' bes&ouml;kare');
	}


	include('design_head.php');

		echo nameLink($show, ucfirst($niceshowname)." sida")." - Bes&ouml;kare<br><br>";

		echo "H&auml;r ser du ".nameLink($show, $niceshowname)." 20 senaste bes&ouml;kare.<br><br>";
		echo displayLastVisitors($db, $show, 20)."<br>";

	include('design_foot.php');

?>