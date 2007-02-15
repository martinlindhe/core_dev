<?
	include_once('include_all.php');

	include('design_head.php');

?>
<noscript>
<span style="background-color:#FF6666">Warning:</span> You have Javascript disabled/blocked for this site. This means the login<br>
mechanism will default to sending your password unencrypted to the server.<br>
<b>To log in safely, you should enable javascript for this site, and then reload the page before proceeding.</b><br><br>
</noscript>
<?

	echo getInfoField($db, 'page_index');
	
	echo '<br>';
	
	echo displayLatestNews($db, 5);

	include('design_foot.php');
?>