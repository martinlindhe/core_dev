<?
/*
	if ($_SERVER['REMOTE_ADDR'] != '213.80.11.162') {
		echo 'Citysurf uppdateras!<br/><br/>Under eftermiddagen onsdagen den 13:e juni genomför vi en uppdatering av citysurf.<br/><br/>';
		echo 'Passa på att njuta av en glass i solen och kika tillbaka om några timmar!<br><br>';
		die;
	}
*/

	require_once('config.php');

/*
	$action = (!empty($_GET['action'])?$_GET['action']:false);
	$id = (!empty($_GET['id'])?$_GET['id']:false);
	$key = (!empty($_GET['key'])?$_GET['key']:false);
	$l = $user->auth(@$_SESSION['data']['id_id'], true);
	if(!empty($_GET['type'])) {
		$type = $_GET['type'];
		if($type == 'main') {
			include('_modules/main/index.php');
		} elseif($type == 'text') {
			include('_modules/text/index.php');
		} elseif($type == 'macro') {
			include('_modules/macro/index.php');
		} else {
			$isAdmin = (@$_SESSION['data']['level_id'] == '10'?true:false);
			$isOk = true;
			// here's user dependent pages
			if($type == 'member') {
				include('_modules/member/index.php');
			} else {
				// heres only logged in user allowed
				if($type != 'user' && !$l) {
					loginACT();
				}
				if($type == 'user') {
					include('_modules/user/index.php');
				} else if($type == 'list') {
					include('_modules/list/index.php');
				} else if($type == 'forum') {
					include('_modules/forum/index.php');
				} else if($type == 'thought') {
					include('_modules/thought.php');
				}
			}
		}
		exit;
	}
*/

	$login_err = true;
	if(empty($l) && !empty($_POST['a']) && !empty($_POST['p'])) {
		require_once('/home/martin/www/_modules/member/auth.php');
		checkBan(1);
		$login_err = $user_auth->login($_POST['a'], $_POST['p']);
	}

	$theme_css = 'jord.css';
	require_once(DESIGN.'top.php');
?>
</head>
<body>

	<div class="mainContent">

		<center>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr><td>

				<div style="width: 596px; text-align: left;"><img src="<?=CS?>_gfx/themes/default_logo.png" alt=""/></div>
		
				<div class="bigHeader"></div>
				<div class="bigBody startPageBody">
					<table summary=""><tr>
						<td width="50%"><?=gettxt('index_text', 0, 1)?></td>
						<td align="right">
							<form name="l" action="" method="post">
								<? if ($login_err !== true) echo '<span style="font-weight:bold;font-size:16px;color: #ff2020;">'. $login_err.'</span><br/><br/>'; ?>
		
								<table summary="" cellspacing="0">
									<tr><td class="rgt" style="padding: 0 5px 3px 0;">alias: <input type="text" class="txt" name="a" style="margin-bottom: -4px;" /></td></tr>
									<tr><td class="rgt" style="padding: 2px 5px 4px 0;">lösenord: <input type="password" class="pass" style="margin-bottom: -4px;" name="p" /></td></tr>
								</table>
		
								<script type="text/javascript">if(document.l.a.value.length > 0) document.l.p.focus(); else document.l.a.focus();</script>
								<input type="button" onclick="goLoc('/member/register/');" class="btn2_min" value="bli medlem" style="margin-top: 3px;"/>
								<input type="button" onclick="goLoc('/member/forgot/');" class="btn2_min" value="glömt lösen" style="margin-top: 3px;"/>
								<input type="submit" class="btn2_min" value="logga in"/>
							</form>
							<br/>
						</td>
					</tr></table>
				</div><br/>
		<?
			echo '<div class="bigHeader" style="clear: both">senast inloggade</div>';
			echo '<div class="bigBody"><center>';
			$list = getLastLoggedIn(11);
			foreach ($list as $row) {
				echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
			}
			echo '</center></div><br/>';


			echo '<div class="bigHeader">senaste galleribilder</div>';
			echo '<div class="bigBody"><center>';
			$list = getLastGalleryUploads(5);
			foreach ($list as $row) {
				echo '<img alt="'.$db->escape($row['pht_cmt']).'" src="'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.'.$row['pht_name'].'" style="margin-right: 10px;" onerror="this.style.display = \'none\';" />';
			}
			echo '</center></div>';
		?>
				</center>
			</div>
			
		</td>
		<td width="10">&nbsp;</td>
		<td><br/><br/><br/><br/><br/><br/><br/>
<script type="text/javascript">
var bnum=new Number(Math.floor(99999999 * Math.random())+1);
document.write('<SCR'+'IPT LANGUAGE="JavaScript" ');
document.write('SRC="http://servedby.advertising.com/site=737464/size=160600/bnum='+bnum+'/optn=1"></SCR'+'IPT>');
</script>
		</td></tr></table>

<?
	require(DESIGN.'foot.php');
?>
