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
	<br/>
	<table width="100%" cellpadding="0" cellspacing="4" border="0">
		<tr>
			<td width="120">&nbsp;</td>
			<td width="250">
				<b>
<?
	$list = getCategories(CATEGORY_USERFILE);

	foreach ($list as $row) {
		echo '<a href="?c='.$row['categoryId'].'">'.$row['categoryName'].'</a><br/>';
	}
//				<a href="./?c=1">Renovations begun 2006</a><br />
//				<a href="./?c=2">Renovations begun 2007</a><br />
?>
				</b>
			</td>
			<td>
				Owner: Thomas Lönn<br />
				Residence: Stockholm, Sweden<br />
				Paint color: Lamborghini Red<br />
			</td>
			<td>
				Horsepower: 374 bhp<br />
				Torque: 549 Nm<br />
				Best ET: 11.9 s<br />
			</td>
			<td>
				Year: 1974<br />
				Engine: 350 small block<br />
				Gearbox: TH400<br />
			</td>
		</tr>
	</table>
</div>

<?
	require('design_foot.php');
?>