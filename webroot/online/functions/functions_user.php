<?
	/*
		functions_user.php
		todo: separera funktioner i olika filer
	*/
	
	
	/* Returns userId if everything is correct, otherwise false */
	function userLogIn($db, $userName, $password) {
		
		$userName = addslashes(trim($userName));
		$password = addslashes(trim($password));
		if (!$userName || !$password) return false;


		$sql = "SELECT userId FROM tblUsers WHERE userName='".$userName."' AND userPass=MD5('".$password."')";
		$check = dbQuery($db, $sql);
		if (!dbNumRows($check)) return false;
		
		$row = dbFetchArray($check);
		$sql = "SELECT userId FROM tblMailActivation WHERE userId=".$row["userId"];
		$check = dbQuery($db, $sql);
		if (!dbNumRows($check)) {
			/* Login successful, save login time */
			$sql = "UPDATE tblUserStats SET timeLastLogin=UNIX_TIMESTAMP(),cntLogins=cntLogins+1 WHERE userId=".$row["userId"];
			dbQuery($db, $sql);
			
			$sql = "INSERT INTO tblLoginAttempts SET userId=".$row["userId"].",IP='".gethostbyaddr($_SERVER["REMOTE_ADDR"])."',loggedin=UNIX_TIMESTAMP(),bygame=0";
			dbQuery($db, $sql);
			
			return $row["userId"];
		}

		return false;
	}
	
	/* Registers $userName, adds a timestamp and locks the database while adding the username */
	/* Returns false upon failure */
	function registerUsername($db, $userName) {

		$userName = addslashes(trim($userName));
		if (!$userName) return false;

		dbQuery($db, "LOCK TABLES tblUsers WRITE");
		$sql = "SELECT userName FROM tblUsers WHERE userName='".$userName."'";
		$check = dbQuery($db, $sql);
		
		if (dbNumRows($check)) {
			dbQuery($db, "UNLOCK TABLES");
			return false;
		}
		dbQuery($db, "INSERT INTO tblUsers SET userName='".$userName."'");
		$userId = dbInsertId();
		dbQuery($db, "UNLOCK TABLES");

		dbQuery($db, "INSERT INTO tblUserStats SET userId=".$userId.", timeCreated=".time().", timeExpires=".time() );
		return $userId;
	}
	
	function registerUserinfo($db, $userId, $password, $email, $hideemail, $newsletter, $realname, $gender, $street, $zipcode, $city, $country, $timezone, $phone) {
		
		if (!is_numeric($userId) || !is_numeric($hideemail) || !is_numeric($newsletter) || !is_numeric($gender) || !is_numeric($country) || !is_numeric($timezone) ) return false;

		$password = addslashes($password);
		$email = addslashes(strtolower(trim($email)));
		$realname = addslashes(trim($realname));
		$street = addslashes(trim($street));
		$zipcode = addslashes(trim($zipcode));
		$city = addslashes(trim($city));
		$phone = addslashes(trim($phone));

		$sql = "UPDATE tblUsers SET userPass=PASSWORD('".$password."') WHERE userId=".$userId;
		dbQuery($db, $sql);
		
		$sql  = "INSERT INTO tblUserAddress ";
		$sql .= "SET userId=".$userId.",timezone=".$timezone.",";
		$sql .= "realName='".$realname."',gender=".$gender.",userMail='".$email."',userMailSecret=".$hideemail.",newsletter=".$newsletter.",adrPhoneHome='".$phone."',";
		$sql .= "adrStreet='".$street."',adrZipcode='".$zipcode."',adrCity='".$city."',adrCountry=".$country;
		dbQuery($db, $sql);

		return true;
	}
	
	function updateUserContactInfo($db, $userId, $email, $hideemail, $newsletter, $phone, $street, $zipcode, $city, $country, $timezone) {
		if (!is_numeric($userId) || !is_numeric($hideemail) || !is_numeric($newsletter) || !is_numeric($country) || !is_numeric($timezone)) return false;

		$email = addslashes(trim($email));
		$phone = addslashes(trim($phone));
		$street = addslashes(trim($street));
		$zipcode = addslashes(trim($zipcode));
		$city = addslashes(trim($city));

		$mailerror = verifyEmail($email);
		if ($mailerror === true) {
			
			$oldmail=getUserMail($db, $userId);

			$sql = "UPDATE tblUserAddress SET userMail='".$email."',userMailSecret=".$hideemail.",newsletter=".$newsletter.",adrPhoneHome='".$phone."',adrStreet='".$street."',adrZipcode='".$zipcode."',adrCity='".$city."',adrCountry=".$country.",timezone=".$timezone." WHERE userId=".$userId;
			dbQuery($db, $sql);

			if ($oldmail != $email) {
				/* Lock account until new mail is verified! */
				mailActivationCode($db, $userId);
				$_SESSION=array();
				session_destroy();
				return "Since you have changed your e-mail address, the account have been locked. Check your mail account ".$email." for a mail describing how to unlock the account again.";
			}
			return true;
		} else {
			return $mailerror; //error message			
		}
	}
	
	function setPassword($db, $userId, $password) {
		if (!is_numeric($userId)) return false;
		$password = addslashes($password);
		
		dbQuery($db, "UPDATE tblUsers SET userPass=PASSWORD('".$password."') WHERE userId=".$userId);
		return true;
	}

	function setEmail($db, $userId, $email) {
		if (!is_numeric($userId)) return false;
		$email = addslashes($email);
		
		dbQuery($db, "UPDATE tblUserAddress SET userMail='".$email."' WHERE userId=".$userId);
		return true;
	}	
	
	/* returns true if password is accepted */
	function verifyPassword($pwd1, $pwd2, $username) {
		
		if ($pwd1 != $pwd2) return "The passwords doesn't match!";
		if (!$username) return "No username given";
		if (stristr($pwd1, $username)) return "The password is too similar to the username";
		
		//todo: regexp koll minst 6 tecken, endast a-z A-Z 0-9
		if (strlen($pwd1) < 6) return "The password is less than 6 characters!";

		return true;
	}
	
	/* Returns true if mail is correct, we cannot log in to mail server and verify since not all servers support this */
	function verifyEmail($email) {

		$email = strtolower(trim($email));
		if (!$email) return "No e-mail entered";
		
		//Regexp: Validates the format of the address
		if (!ereg(
			"^[a-z0-9]+([_\\.-][a-z0-9]+)*" .	//user
			"@" .
			"([a-z0-9]+([\.-][a-z0-9]+)*)+" .	//domain
			"\\.[a-z]{2,}" .					//sld, tld 
			"$", $email)) {
				return "Invalid format of e-mail address.";
		} else {
			return true;
		}
	}
	
	/* Returns false if a user in the system already uses this address */
	function isFreeEmail($db, $email) {
		$email = addslashes(strtolower(trim($email)));
		if (!$email) return false;
		
		$check = dbQuery($db, "SELECT userId FROM tblUserAddress WHERE userMail='".$email."'");
		if (dbNumRows($check)) {
			return false;
		} else {
			return true;
		}
	}
	
	function getRandomCode($length) {
		
    	list($usec, $sec) = explode(' ', microtime());
    	mt_srand((float) $sec + ((float) $usec * 100000));
		
		$val = "";
		for ($i=0; $i<$length; $i++) {
			$randval = mt_rand(0,10+25+25);

			if ($randval < 10) { //0-9
				$val .= chr($randval+48);
			} else if (($randval >= 10) && ($randval <= 35)) { //a-z
				$val .= chr($randval+(65-10));
			} else { //A-Z
				$val .= chr($randval+(97-36));
			}
		}

		return $val;
	}
	
	/* Returns the mail address associated with this user */
	function getUserMail($db, $userId) {
		if (!is_numeric($userId)) return false;

		$check = dbQuery($db, "SELECT userMail FROM tblUserAddress WHERE userId=".$userId);
		if (dbNumRows($check)) {
			$row = dbFetchArray($check);
			return $row["userMail"];
		} else {
			return false;
		}
	}
	
	/* Creates a activation code, then mails it to the user */
	function mailActivationCode($db, $userId) {
		if (!is_numeric($userId)) return false;

		$code = getRandomCode(20);		
		dbQuery($db, "DELETE FROM tblMailActivation WHERE userId=".$userId);
		dbQuery($db, "INSERT INTO tblMailActivation SET userId=".$userId.",activationCode='".$code."'");

		$address = getUserMail($db, $userId);
		$extraheader =	"From: Online Message <noreply@inthc.net>";
		$subject =		"Your activation code for the online community!";
		$message =		"Hello!\n".
						"\n".
						"Click on the URL below to lock up your account!\n".
						"http://217.215.191.103:14480/online/activate.php?c=".$code."\n";

		mail($address, $subject, $message, $extraheader);
	}
	
	function activateAccount($db, $code) {
		
		$code = addslashes($code);
		
		$check = dbQuery($db, "SELECT userId FROM tblMailActivation WHERE activationCode='".$code."'");
		if (dbNumRows($check)) {
			$row = dbFetchArray($check);
			
			dbQuery($db, "UPDATE tblUserStats SET timeActivated=".time()." WHERE userId=".$row["userId"]);
			dbQuery($db, "DELETE FROM tblMailActivation WHERE userId=".$row["userId"]);
			return true;
		} else {
			return false;
		}
	}

	function getUserCCInfo($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql = "SELECT * FROM tblUserBilling WHERE userId=".$userId;
		$check = dbQuery($db, $sql);

		return dbFetchArray($check);
	}
	
	function getUserContactInfo($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql  = "SELECT t1.*, t2.userName, t3.countryName FROM tblUserAddress AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.userId = t2.userId) ";
		$sql .= "INNER JOIN tblCountries AS t3 ON (t1.adrCountry = t3.countryId) ";
		$sql .= "WHERE t1.userId=".$userId;
		$check = dbQuery($db, $sql);

		return dbFetchArray($check);
	}
	
	function getUserStats($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql = "SELECT * FROM tblUserStats WHERE userId=".$userId;
		$check = dbQuery($db, $sql);

		return dbFetchArray($check);
	}
	
	function isSuperUser($db, $userId) {
		if (!is_numeric($userId)) return false;

		$sql = "SELECT userType FROM tblUsers WHERE userId=".$userId;
		$check = dbQuery($db, $sql);

		$row = dbFetchArray($check);
		if ($row["userType"] == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	//$user can be userId or userName...
	function deleteUser($db, $user) {
		$user = addslashes($user);
		
		$check = dbQuery($db, "SELECT userId FROM tblUsers WHERE userId = '".$user."' OR userName = '".$user."'");
		if (!dbNumRows($check)) {
			return false;
		} else {		
			$row = dbFetchArray($check);
			dbQuery($db, "DELETE FROM tblUserAddress WHERE userId = ".$row["userId"]);
			dbQuery($db, "DELETE FROM tblUserBilling WHERE userId = ".$row["userId"]);
			dbQuery($db, "DELETE FROM tblUserStats WHERE userId = ".$row["userId"]);
			dbQuery($db, "DELETE FROM tblMailActivation WHERE userId = ".$row["userId"]);
			dbQuery($db, "DELETE FROM tblUsers WHERE userId = ".$row["userId"]);
			return true;
		}
	}
	
	/* Span is the number of months inactive the users need to be to match */
	function getInactiveUsers($db, $span) {
		if (!is_numeric($span)) return false;
		
		$sql  = "SELECT tblUserStats.userId, tblUserStats.timeLastLogin, tblUsers.userName ";
		$sql .= "FROM tblUserStats ";
		$sql .= "INNER JOIN tblUsers ON (tblUsers.userId = tblUserStats.userId) ";
		$sql .= "WHERE timeLastLogin < ". (time()-(((($span*30)*24)*60)*60));
		
		return dbArray($db, $sql);
	}

	function getUserName($db, $userId) {
		if (!is_numeric($userId)) return false;
		
		$check = dbQuery($db, "SELECT userName FROM tblUsers WHERE userId=".$userId);
		$row = dbFetchArray($check);
		return $row["userName"];
	}
	
	function getUserId($db, $userName) {
		$userName = addslashes($userName);

		$check = dbQuery($db, "SELECT userId FROM tblUsers WHERE userName='".$userName."'");
		$row = dbFetchArray($check);
		return $row["userId"];
	}

	function getAdministrators($db) {
		return dbArray($db, "SELECT * FROM tblUsers WHERE userType=1");
	}

	function updateBillingInformation($db, $userId, $cc, $month, $year, $owner, $extracode) {
		$cc = CCstripNumber($cc);
		if (!is_numeric($userId) || !is_numeric($cc) || !is_numeric($month) || !is_numeric($year)) return false;
		if (CCvalidateMod10($cc) === false) return "Invalid credit card number!";
		$owner = addslashes($owner);
		$extracode = addslashes($extracode);
		
		$sql = "SELECT userId FROM tblUserBilling WHERE userId=".$userId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) {		
			$sql = "UPDATE tblUserBilling SET ccNumber=".$cc.",ccExpireMonth=".$month.",ccExpireYear=".$year.",ccExtraCode='".$extracode."',ccOwnerName='".$owner."' WHERE userId=".$userId;
			dbQuery($db, $sql);
		} else {
			$sql = "INSERT INTO tblUserBilling SET userId=".$userId.",ccNumber=".$cc.",ccExpireMonth=".$month.",ccExpireYear=".$year.",ccExtraCode='".$extracode."',ccOwnerName='".$owner."'";
			dbQuery($db, $sql);
		}
		return true;
	}
	
	/* Returns TRUE if password is correct, used for change password checking */
	function checkPassword($db, $userId, $password) {
		if (!is_numeric($userId)) return false;
		$password = addslashes($password);
		
		$check = dbQuery($db, "SELECT userId FROM tblUsers WHERE userId=".$userId." AND userPass=PASSWORD('".$password."')");
		if (dbNumRows($check)) {
			return true;
		} else {
			return false;
		}
	}

	function getUsersByCountry($db, $countryId) {
		if (!is_numeric($countryId)) return false;
		
		$sql  = "SELECT t1.*, t2.userName, t3.countryName FROM tblUserAddress AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.userId = t2.userId) ";
		$sql .= "INNER JOIN tblCountries AS t3 ON (t1.adrCountry = t3.countryId) ";
		$sql .= "WHERE t1.adrCountry=".$countryId;
		return dbArray($db, $sql);
	}
	
	function getUsersByCity($db, $countryId, $cityName) {
		if (!is_numeric($countryId)) return false;
		$cityName = addslashes($cityName);
		
		$sql  = "SELECT t1.*, t2.userName, t3.countryName FROM tblUserAddress AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.userId = t2.userId) ";
		$sql .= "INNER JOIN tblCountries AS t3 ON (t1.adrCountry = t3.countryId) ";
		$sql .= "WHERE t1.adrCountry=".$countryId." AND t1.adrCity='".$cityName."'";
		return dbArray($db, $sql);
	}

	function getUsersByTimezone($db, $timezoneId) {
		if (!is_numeric($timezoneId)) return false;
		
		$sql  = "SELECT t1.*, t2.userName, t3.countryName FROM tblUserAddress AS t1 ";
		$sql .= "INNER JOIN tblUsers AS t2 ON (t1.userId = t2.userId) ";
		$sql .= "INNER JOIN tblCountries AS t3 ON (t1.adrCountry = t3.countryId) ";
		$sql .= "WHERE t1.timezone=".$timezoneId;
		return dbArray($db, $sql);
	}
	
	function getUserExpireTime($db, $userId) {
		if (!is_numeric($userId)) return;
		
		$sql = "SELECT timeExpires FROM tblUserStats WHERE userId=".$userId;
		$check = dbQuery($db, $sql);
		$row = dbFetchArray($check);
		return $row["timeExpires"];
	}

?>