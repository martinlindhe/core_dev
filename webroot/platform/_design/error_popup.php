<?
	require(DESIGN.'top.php');
?>
</head>
<body style="background: #FFF;" class="cnt">

	<div class="popupWholeContent cnti mrg">
		<div class="leftMenuHeader">meddelande</div>
		<div class="leftMenuBodyWhite">
			<table cellspacing="0" style="width: 160px; height: 150px;"><tr><td style="vertical-align: middle; width: 160px; text-align: center; height: 150px;"><?=$msg?></td></tr></table>
			<input type="submit" onclick="self.close();" class="btn2_sml r" value="stäng!" style="margin-top: 5px;" /><br class="clr" />
		</div>
	</div>
<?
	if($url != '1') if(!empty($time)) { echo '<script type="text/javascript">'.((!empty($parent))?'window.opener.location.href = \''.$parent.'\'; ':'').'window.setTimeout(\''.((!empty($url))?'document.location.href = "'.$url.'";':((!empty($parent))?'window.opener.focus(); ':'').'self.close();').'\', '.$time.');</script>'; }
?>
</body>
</html>