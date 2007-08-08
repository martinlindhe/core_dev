<?
	require_once('config.php');

	$_cat = 1;
	if (!empty($_GET['c']) && is_numeric($_GET['c'])) $_cat = $_GET['c'];

	$title = $config['default_title'].' - '.getCategoryName(CATEGORY_USERFILE, $_cat);
	require('design_head.php');

	$files->showThumbnails(FILETYPE_USERFILE, $_cat);
?>

<div id="stingray_logo"></div>
<div id="corvette_emblem"></div>

<div id="menu">
	<table width="100%" cellpadding="0" cellspacing="4" border="0">
		<tr>
			<td width="120">&nbsp;</td>
			<td width="250">
			<b><a href="./?c=1">In Front</a><br />
			&nbsp;&nbsp;&nbsp;<a href="./?c=2">Behind</a><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?c=3">Projects</a><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?c=4">Wallpaper</a><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?c=5">My Art</a></b>
			</td>
			<td>
				Residence: Stockholm, Sweden<br />
				Paint color: Lamborghini Red<br />
				Engine: 350 small block<br />
				Horsepower: 374 bhp<br />
			</td>
			<td>
				Torque: 549 Nm<br />
				Year: 1974<br />
				Gearbox: TH400<br />
				Owner: Thomas Lönn<br />
			</td>
			<td>
				Hips: 35.5 / 90 cm<br />
				Rump: 37 / 99 cm<br />
				Clothing size: S-M 36-38<br />
				Shoe size: 37-38<br />
			</td>
		</tr>
	</table>
</div>

<?
	require('design_foot.php');
?>