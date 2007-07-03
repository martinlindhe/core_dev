<?
	$thispage = 'obj.php?status=abuse';

	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$sql->queryUpdate("DELETE FROM s_userabuse WHERE id = '".secureINS($_GET['del'])."'");
		header("Location: ".$thispage);
		exit;
	}

	$q = 'SELECT * FROM s_userabuse';
	$list = $sql->query($q, 0, 1);

	require("./_tpl/obj_head.php");
	
	echo 'Listar '.count($list).' anmälningar:<br/><br/>';
?>
	<form name="upd" method="post" action="./<?=$thispage?>">
		<input type="hidden" name="main_id:all" id="main_id" value="0">
		<input type="hidden" name="validate" value="1">


<?
	if(count($list)) { 
		foreach($list as $row) {
			echo '<table style="width: 500px;">';
			echo '<tr class="bg_blk wht"><td style="padding-bottom: 8px;">';
			
			$reporter = $user->getuser($row['reporterId']);
			$reported = $user->getuser($row['reportedId']);
			
			if (!$reporter) $reporter['u_alias'] = '[borttagen]';
			if (!$reported) $reported['u_alias'] = '[borttagen]';
			
			echo 'Användare '.$reporter['u_alias'].' rapporterar <a href="user.php?t&id='.$row['reportedId'].'">'.$reported['u_alias'].'</a>: '.niceDate($row['timeReported']).'<br/>';
			echo $row['msg'].'<br/>';
?>
			</td></tr>
			<tr class="bg_blk wht"> 
				<td style="padding: 8px 0 0 0;" class="nobr">
				<div style="float: right;">
					<a href="<?=$thispage?>&del=<?=$row['id']?>" onclick="return confirm('Säker ?');">RADERA</a>
					<!-- | <a href="javascript:openWin('obj_thought_answer.php?id=<?=$row['main_id']?>');">ÄNDRA/SVARA</a> -->
				</div>
				</td>
			</tr>
			</table><br/>
<?	}
	}
?>


</form>



		</td>
	</tr>
	</table>
</body>
</html>