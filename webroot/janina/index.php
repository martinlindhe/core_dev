<?
	require_once('config.php');
	require('design_head.php');

	$files->showThumbnails($showCategory);
?>

<div id="menu">
	<table width="100%" cellpadding=0 cellspacing=4 border=0>
		<tr>
			<td width=120>&nbsp;</td>
			<td width=250>
			<b><a href="./?c=1">In Front</a><br>
			&nbsp;&nbsp;&nbsp;<a href="./?c=2">Behind</a><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?c=3">Projects</a><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?c=4">Wallpaper</a><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?c=5">My Art</a></b>
			</td>
			<td>
				Residence: Stockholm, Sweden<br>
				Hair color: Red<br>
				Eyes: Blue<br>
				Height: 5"8.5 / 174 cm<br>
			</td>
			<td>
				Weight: 132 lb / 60 kg<br>
				Bust: 36 / 92 cm<br>
				Bra: 34 B / 75 B<br>
				Waist: 27.5 / 70 cm<br>
			</td>
			<td>
				Hips: 35.5 / 90 cm<br>
				Rump: 37 / 99 cm<br>
				Clothing size: S-M 36-38<br>
				Shoe size: 37-38<br>
			</td>
		</tr>
	</table>
</div>

<?
	require('design_foot.php');
?>