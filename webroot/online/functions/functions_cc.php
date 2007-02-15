<?	/* http://www.beachnet.com/~hstiles/cardtype.html for reference
	
		Status: Feature complete. Works with my VISA number, needs real world testing
		
		Functions:
			CCstripNumber: helper function, cleans up user inputted number (complete!)
			CCgetType: Returns a constant for the identifed card type, or false on failure (may be incomplete, based on info from '97)
			CCvalidateMod10: Validates number using the mod 10 algorithm (complete!)
	*/

	/* Cleans up a cc number entered by a user, false on invalid number */
	function CCstripNumber($number) {

		$number = str_replace(" ", "", $number);
		$number = str_replace("-", "", $number);
		$number = str_replace(".", "", $number);

		if (!is_numeric($number)) return false;
		return $number;
	}

	/* Prints out the card number with spaces between every 4:th digit so it's easier to read */	
	function CCprintNumber($number) {
		$number = CCstripNumber($number);
		
		if ($number) {
			$result = "";
			for ($i=0; $i<strlen($number); $i+=4) {
				$result .= substr($number, $i, 4)." ";
			}
			return trim($result);
		} else {
			return false;
		}
	}

	function CCgetTypeName($number) {
		global $cc_name;
		return $cc_name[CCgetType($number)];
	}

	/* Tries to figure out the card type, false on failure/invalid */
	function CCgetType($number) {

		$number = CCstripNumber($number);
		if ($number === false) return false;

		$len = strlen($number);
		if ($len < 13) return false;
		
		$pref1 = substr($number,0,1);
		if (($pref1 == 4) && ($len == 13 || $len == 16)) {
			return CC_VISA;
		}

		if (($pref1 == 3) && ($len == 16)) {
			return CC_JCB;
		}

		$pref2 = substr($number,0,2);		
		if (($pref2 >= 51) && ($pref2 <= 55) && $len == 16) {
			return CC_MASTERCARD;
		}
		
		if ((($pref2 == 34) || ($pref2 == 37)) && $len == 15) {
			return CC_AMEX;
		}
		
		$pref3 = substr($number,0,3);
		if (((($pref3 >= 300) && ($pref3 <= 305)) || ($pref2 == 36) || ($pref2 = 38)) && $len == 14) {
			return CC_DINERS;
		}
		
		$pref4 = substr($number,0,4);
		if (($pref4 == 6011) && $len == 16) {
			return CC_DISCOVER;
		}
		
		if ((($pref4 == 2131) || ($pref4 == 1800)) && $len == 15) {
			return CC_JCB;
		}
		
		return CC_INVALID;
	}

	function CCvalidateMod10($number) {
		
		$number = CCstripNumber($number);
		if ($number === false) return false;
		if (CCgetType($number) == CC_INVALID) return false;
		
		$tot=0;
		for ($i = strlen($number)-1; $i>=0; $i--) {
			$char = substr($number, $i,1);
			if (!((strlen($number)-$i) % 2)) { //Jämna nummer
				$char *= 2;
				$d1 = substr($char,0,1);
				$d2 = substr($char,1,1);
				$tot += $d1 + $d2;
			} else {
				$tot += $char;
			}
		}
			
		if (substr($tot, -1) == "0") {
			return true;
		}
		return false;
	}
?>