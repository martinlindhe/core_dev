<?
	//note: these functions assumes that the game server database is in the same server as the website database

	function getGameServerCharacters($db, $userId) { //fixad
		if (!is_numeric($userId)) return false;
		
		$sql  = "SELECT charId,charName ";
		$sql .= "FROM online_game.tblCharacters ";
		$sql .= "WHERE userId=".$userId." ";
		$sql .= "ORDER BY charName ASC";

		return dbArray($db, $sql);
	}

	function getGameServerCharacter($db, $charId) {
		if (!is_numeric($charId)) return false;
		
		$sql  = "SELECT * ";
		$sql .= "FROM online_game.tblCharacters ";
		$sql .= "WHERE charId=".$charId;

		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
	/* STR, DEX, CON etc */
	function getGameServerCharacterAbilityScores($db, $charId) {
		if (!is_numeric($charId)) return false;
		
		$sql = "SELECT * FROM online_game.tblCharacterAbilityScores WHERE charId=".$charId;
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
	/* Gender, race etc */
	function getGameServerCharacterInfo($db, $charId) {
		if (!is_numeric($charId)) return false;
		
		$sql = "SELECT * FROM online_game.tblCharacterInfo WHERE charId=".$charId;
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
	
	/* Returns the same data as getGameServerCharacter(), getGameServerCharacterAbilityScores() and getGameServerCharacterInfo() together */
	function getGameServerCharacterCombinedInfo($db, $charId) { //fixad
		if (!is_numeric($charId)) return false;

		$sql  = "SELECT t1.*,t3.userName,t4.*,t5.guildId,t5.timeJoinedGuild,t5.guildMemberType,t6.guildName ";
		$sql .= "FROM online_game.tblCharacters AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t3 ON (t1.userId=t3.userId) ";
		$sql .= "INNER JOIN online_game.tblCharacterAbilityScores AS t4 ON (t1.charId=t4.charId) ";
		$sql .= "LEFT OUTER JOIN online_game.tblGuildMembers AS t5 ON (t1.charId=t5.charId) ";
		$sql .= "LEFT OUTER JOIN online_game.tblGuilds AS t6 ON (t5.guildId=t6.guildId) ";
		$sql .= "WHERE t1.charId=".$charId;

		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
	function getGameServerGuildInfo($db, $guildId) {
		if (!is_numeric($guildId)) return false;
		
		$sql  = "SELECT t1.*,t2.charName AS creatorName FROM online_game.tblGuilds AS t1 ";
		$sql .= "INNER JOIN online_game.tblCharacters AS t2 ON (t1.creatorId=t2.charId) ";
		$sql .= "WHERE guildId=".$guildId;
		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}
	
	function getGameServerGuildMembers($db, $guildId) {
		if (!is_numeric($guildId)) return false;
		
		$sql  = "SELECT t1.*,t2.charName FROM online_game.tblGuildMembers AS t1 ";
		$sql .= "INNER JOIN online_game.tblCharacters AS t2 ON (t1.charId=t2.charId) ";
		$sql .= "WHERE guildId=".$guildId." ";
		$sql .= "ORDER BY t1.guildMemberType DESC, t2.charName ASC";
		
		return dbArray($db, $sql);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/* SKA VA I EN NY FIL: */
	function getGameServers($db) {
		return dbArray($db, "SELECT * FROM online_game.tblGameServers ORDER by serverName ASC");
	}
	
	function turnOnGameServer($db, $serverId) {//todo: MUST BE REDONE!
		if (!is_numeric($serverId)) return false;
		
		dbQuery($db, "UPDATE online_game.tblGameServers SET serverOnline=1 WHERE serverId=".$serverId);
	}

	function turnOffGameServer($db, $serverId) {//todo: MUST BE REDONE!
		if (!is_numeric($serverId)) return false;
		
		dbQuery($db, "UPDATE online_game.tblGameServers SET serverOnline=0 WHERE serverId=".$serverId);
	}
	
	function updateGameServer($db, $serverId, $serverName, $serverIP) {//todo: MUST BE REDONE!

		if (!is_numeric($serverId)) return false;

		$serverName = addslashes($serverName);
		$serverIP = addslashes($serverIP);

		$sql = "UPDATE online_game.tblGameServers SET serverName='".$serverName."',serverIP='".$serverIP."' WHERE serverId=".$serverId;
		dbQuery($db, $sql);
	}

	function addGameServer($db, $serverName, $serverIP) {

		$serverName = addslashes($serverName);
		$serverIP = addslashes($serverIP);

		$sql = "INSERT INTO online_game.tblGameServers SET serverName='".$serverName."',serverIP='".$serverIP."',serverOnline=0";
		dbQuery($db, $sql);
	}
	/* Return server name etc, and how many characters registered too */
	function getGameServerInfo($db, $serverId) {	//todo: MUST BE REDONE!
		if (!is_numeric($serverId)) return false;
		
		$sql  = "SELECT t1.*, COUNT(DISTINCT t2.charId) AS characters, COUNT(DISTINCT t2.userId) AS users ";
		$sql .= "FROM online_game.tblGameServers AS t1 ";
		$sql .= "LEFT OUTER JOIN online_game.tblCharacters AS t2 ON (t1.serverId = t2.serverId) ";
		$sql .= "WHERE t1.serverId=".$serverId." ";
		$sql .= "GROUP BY t1.serverId";

		$check = dbQuery($db, $sql);
		return dbFetchArray($check);
	}

	
?>