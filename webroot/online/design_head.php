<?
	header("Cache-Control: no-cache");

	$box_head = "<table width=\"100%\" cellpadding=0 cellspacing=1 border=0 bgcolor=\"#000000\"><tr><td><table width=\"100%\" cellpadding=2 cellspacing=0 border=0 bgcolor=\"#FFFFFF\"><tr><td>";
	$box_foot = "</td></tr></table></td></tr></table>";

?>

<html><head>
<title>online</title>
<link rel="stylesheet" href="design.css" type="text/css" media="screen">
</head>
<body marginheight=0 marginwidth=0 topmargin=0 leftmargin=0 bottommargin=5>

<table width="100%" cellpadding=0 cellspacing=0 border=0><tr><td>
<img src="gfx/logo_head1.png" width=700 height=57 border=0><br>
</td></tr></table>

<table width=700 cellpadding=0 cellspacing=0 border=0>
	<tr>	
		<td width=100 valign="top">
			<!-- left menu -->
			<table width="100%" cellpadding=0 cellspacing=0 border=0>
			<tr><td colspan=2>
				<img src="gfx/logo_head2.png" width=44 height=36><br>
				<img src="gfx/blank.gif" height=5><br>
			</td></tr>
			<tr><td width=5><img src="gfx/blank.gif" width=5></td>
			<td>
				<?
				echo $box_head;

				echo "<a href=\"index.php\">&raquo; News</a><br>";
				echo "<a href=\"forum.php\">&raquo; Forum</a><br>";
				echo "<a href=\"server_status.php\">&raquo; Server status</a><br>";
				echo "<br>";

				if ($_SESSION["loggedIn"] == true) {
					echo "<a href=\"settings.php\">&raquo; My settings</a><br>";
					echo "<a href=\"show_user.php\">&raquo; My page</a><br>";

					if ($_SESSION["superUser"] === true) {
						echo "<a href=\"admin.php\">&raquo; Adminstration</a><br>";
					} else {
						echo "<a href=\"bugreport.php\">&raquo; Report a bug</a><br>";
					}
					echo "<a href=\"logout.php\">&raquo; Log out</a><br>";
				} else {
					echo "<a href=\"login.php\">&raquo; Log in</a><br>";
					echo "<a href=\"register.php\">&raquo; Register</a><br>";
					echo "<a href=\"conditions.php\">&raquo; Conditions</a><br>";
				}
				
				echo $box_foot;
				?>
			</td>
			</tr></table>
		</td>
		<td width=5><img src="gfx/blank.gif" width=5></td>
		<td width="*" valign="top">
			<table width="100%" height=460 cellpadding=0 cellspacing=1 border=0 bgcolor="#000000"><tr><td>
				<table width="100%" height="100%" cellpadding=0 cellspacing=0 border=0 bgcolor="#FFFFFF"><tr><td valign="top">
					<table width="100%" cellpadding=0 cellspacing=2 border=0 bgcolor=#C0C0C0><tr><td>
<?
	if ($_SESSION["loggedIn"] === true) {
		echo "You're logged in as <b>".$_SESSION["userName"].". </b>";
		if ($_SESSION["superUser"] === true) {
			echo "You are a super user. ";
		} else {
			echo "You are a normal user.";
		}
		echo "<br>";
	} else {
		echo "You are not logged in.<br>";
	}
?>
					</td></tr></table>
<img src="gfx/blank.gif" height=5><br>

<table width="100%" cellpadding=0 cellspacing=2 border=0 bgcolor=#FFFFFF><tr><td>
<!-- body start -->
