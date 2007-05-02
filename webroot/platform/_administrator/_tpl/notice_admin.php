<script type="text/javascript">
	msg = '<?=(!empty($msg))?$msg:''?>';
	mv = '<?=(!empty($js_mv))?$js_mv:''?>';
	if(msg != '') {
		alert(msg);
		if(mv != '') {
			document.location.href = mv;
		}
	}
	<?=(!empty($js_ex))?$js_ex:''?>;
</script>