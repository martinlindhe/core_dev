<?

	function createContentCodes($db, $ammount, $months) {
		if (!is_numeric($ammount) || !is_numeric($months)) return false;

		for ($count=0; $count<$ammount; $count++) {
			$code = "";
		
			for ($i=0; $i<12; $i++) {
				$code .= mt_rand(0,9);
			}
			
			$sql = "SELECT code FROM tblContentCodes WHERE code=".$code;
			$check = dbQuery($db, $sql);
			if (dbNumRows($check)) {
				$count--;
			} else {
				$sql = "INSERT INTO tblContentCodes SET code=".$code.",months=".$months.",timestamp=".time();
				dbQuery($db, $sql);
			}
		}
	}
	
	function getContentCodeStats($db) {
		/* unused */
		$sql = "SELECT COUNT(code) FROM tblContentCodes WHERE months=1 AND used=0";
		$check = dbQuery($db, $sql); $row = dbFetchArray($check); $result[1]["unused"] = $row[0];

		$sql = "SELECT COUNT(code) FROM tblContentCodes WHERE months=3 AND used=0";
		$check = dbQuery($db, $sql); $row = dbFetchArray($check); $result[3]["unused"] = $row[0];
		
		$sql = "SELECT COUNT(code) FROM tblContentCodes WHERE months=6 AND used=0";
		$check = dbQuery($db, $sql); $row = dbFetchArray($check); $result[6]["unused"] = $row[0];

		/* used */
		$sql = "SELECT COUNT(code) FROM tblContentCodes WHERE months=1 AND used=1";
		$check = dbQuery($db, $sql); $row = dbFetchArray($check); $result[1]["used"] = $row[0];

		$sql = "SELECT COUNT(code) FROM tblContentCodes WHERE months=3 AND used=1";
		$check = dbQuery($db, $sql); $row = dbFetchArray($check); $result[3]["used"] = $row[0];
		
		$sql = "SELECT COUNT(code) FROM tblContentCodes WHERE months=6 AND used=1";
		$check = dbQuery($db, $sql); $row = dbFetchArray($check); $result[6]["used"] = $row[0];

		return $result;
	}
	
	/* Returns the number of months that got unlocked, and marks the code as used */
	function unlockContentCode($db, $userId, $code) {
		$code = trim($code);
		$code = str_replace(" ", "", $code);
		if (!is_numeric($code) || !is_numeric($userId)) return false;
		if (strlen($code) != 12) return false;


		//Vid det här laget är det enbart korrekta koder/tappra försök som kvarstår,
		//så vi loggar allt för att kunna spåra folk som försöker bruteforca sej in
		$logentry=date("Y-m-d H:i")." ".$_SERVER["REMOTE_ADDR"]." userId ".$_SESSION["userId"]." tried code ".$code.", ";
		$fp = fopen(LOGFILE_CONTENTCODES, "a");
		fwrite($fp, $logentry);

		$expiresecs = ((((60*60)*24)*365)*2); //2 years
		$sql  = "SELECT months ";
		$sql .= "FROM tblContentCodes ";
		$sql .= "WHERE code=".$code." AND used=0 AND ".time()." < (timestamp + ".$expiresecs.")";
		$check = dbQuery($db, $sql);
		if (!dbNumRows($check)) {
			fwrite($fp, "which was invalid.\n"); fclose($fp);
			return false;
		}
		fwrite($fp, "which was correct.\n"); fclose($fp);
		$months_row = dbFetchArray($check);

		dbQuery($db, "UPDATE tblContentCodes SET used=1,userId=".$userId.",usedTimestamp=".time()." WHERE code=".$code);
		
		
		/* om expire time är i dåtiden, sätt den till NUTID, SEN uppdatera */
		$check = dbQuery($db, "SELECT timeExpires FROM tblUserStats WHERE userId=".$userId);
		$row = dbFetchArray($check);
		if ($row["timeExpires"] < time()) {
			/* Starts fresh */
			dbQuery($db, "UPDATE tblUserStats SET timeExpires=".time()." WHERE userId=".$userId);
		}
		/* Increases expire time */
		$months_secs = (((60*60)*24)*30)*$months_row["months"];
		dbQuery($db, "UPDATE tblUserStats SET timeExpires=(timeExpires + ".$months_secs.") WHERE userId=".$userId);

		return $months_row["months"];
	}
	
?>