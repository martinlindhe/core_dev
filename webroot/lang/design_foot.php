<!-- foot start -->
	</div>	<!-- menu_middle -->

	<div id="menu_footer">
		lang 1.0-dev<br/>
<?
	if ($session->isAdmin) $db->showProfile($time_start);
?>
	</div>

</div> <!-- menu_holder -->

</body>
</html>