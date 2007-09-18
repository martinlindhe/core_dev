<?
	//Optional content type. makes debugging easier as XML is shown in Firefox/IE
	header('Content-type: text/xml');

	echo "﻿<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
?>

<!DOCTYPE vxml PUBLIC "-//W3C//DTD VOICEXML 2.0//EN" "http://www.w3.org/TR/voicexml20/vxml.dtd">

<vxml version="2.0" xmlns="http://www.w3.org/2001/vxml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

	<!-- document scope variables -->
	<var name="service"					expr="'<?=$config['vxml']['service']?>'"/>	<!-- ID of service currently used -->
	<var name="callLocal"				expr="'call://' + connection.psems.callID"/>
	<var name="URLmain"					expr="'./main.php'"/>
	<var name="URLvideochat"		expr="'./videochat.php'"/>
	<var name="URLhangup"				expr="'./hangup.php?id=' + connection.psems.callID"/>
	<var name="URLusersOnline"	expr="'./users_online.php'"/>

	<!-- catches CLIENT disconnects so we can update "users online" table -->
	<catch event="connection.disconnect.hangup">
		<goto expr="URLhangup"/>
	</catch>
