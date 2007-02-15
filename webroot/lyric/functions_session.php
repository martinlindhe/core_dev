<?
	/*	
	 	functions_session.php - Funktioner för sessionshantering

		2002.10.15
			* Skapad
	*/

	define("COOKIE_REMEMBER_USERNAME_COOKIENAME",	"username");
	define("COOKIE_REMEMBER_USERNAME_LIMIT",		2592000);  //30 days


	/* Sets a cookie to remember the last username used to login */
	function setUsernameCookie($username) {
		setcookie(COOKIE_REMEMBER_USERNAME_COOKIENAME, $username, time()+COOKIE_REMEMBER_USERNAME_LIMIT);
	}
	
	/* returns the last used username */
	function getUsernameCookie() {
		if (isset($_COOKIE[COOKIE_REMEMBER_USERNAME_COOKIENAME])) {
			return $_COOKIE[COOKIE_REMEMBER_USERNAME_COOKIENAME];
		} else {
			return "";
		}
	}
?>