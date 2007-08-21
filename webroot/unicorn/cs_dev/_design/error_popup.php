<?
	require(DESIGN.'top.php');
?>
</head>
<body class="cnt">

	<div class="popupWholeContent mrg">
		<div class="smallHeader">meddelande</div>
		<div class="smallBody">
			<?=$msg?><br/>
			<input type="submit" onclick="self.close();" class="btn2_sml r" value="stäng!" /><br class="clr"/>
		</div>
	</div>
<?
	if($url != '1') if(!empty($time)) { echo '<script type="text/javascript">'.((!empty($parent))?'window.opener.location.href = \''.$parent.'\'; ':'').'window.setTimeout(\''.((!empty($url))?'document.location.href = "'.$url.'";':((!empty($parent))?'window.opener.focus(); ':'').'self.close();').'\', '.$time.');</script>'; }
?>
</body>
</html>
