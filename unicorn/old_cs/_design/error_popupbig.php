<?
	require(DESIGN.'top.php');
?>
</head>
<body style="background: #FFF;" class="cnt">
		<div class="popupWholeContent cnti mrg" style="width: 596px;">
			<div class="mainHeader2 lft"><h4>meddelande</h4></div>
			<div class="mainFilled2 wht lft">
				<p><?=$msg?></p>
				<input type="submit" onclick="self.close();" class="btn2_sml r" value="stäng!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
<?
	if($url != '1') if(!empty($time)) { echo '<script type="text/javascript">'.((!empty($parent))?'window.opener.location.href = \''.$parent.'\'; ':'').'window.setTimeout(\''.((!empty($url))?'document.location.href = "'.$url.'";':((!empty($parent))?'window.opener.focus(); ':'').'self.close();').'\', '.$time.');</script>'; }
?>
</body>
</html>