<?
class sql {
	var $result, $connected, $t, $db;
	function sql() {
		$this->connected = false;
		$this->t = T;
	}
	function connect() {
		try {
			$link = @mysql_connect(SQL_H, SQL_U, SQL_P);
			if (!$link) die('databasproblem '.mysql_error());
			@mysql_select_db(SQL_D);
			$this->db = SQL_D;
		} catch(Exception $e) {
			return false;
		}
		if($link) {
			$this->connected = true;
			return true;
		} else return false;
	}
	function checkconnected() {
		if(!$this->connected) {
			if(!$this->connect()) { splashACT('Could not connect do database.'); }
		}
	}
	function query($query, $debug = false, $assoc = false, $error = false) {
		$this->checkconnected();
		if($debug) print $query;
		$result = mysql_query($query);
		$return = array();
		#if($error)
		echo mysql_error();
		if($assoc) {
			while($row = mysql_fetch_assoc($result))
				$return[] = $row;
		} else {
			while($row = mysql_fetch_row($result))
				$return[] = $row;
		}
		return $return;
	}

	function querybycontent($query, $debug = false, $assoc = false, $name = 'content_type') {
		$this->checkconnected();
		if($debug) print $query;
		$result = mysql_query($query);
		$return = array();
		if($assoc) {
			while($row = mysql_fetch_assoc($result))
				$return[$row[$name]] = $row;
		} else {
			while($row = mysql_fetch_row($result))
				$return[$row[0]] = $row;
		}
		return $return;
	}
	function db($db) {
		$this->checkconnected();
		$this->db = $db;
		if(!@mysql_select_db($db)) die('Could not change db');
	}
	function queryLine($query, $assoc = false) {
		$this->checkconnected();
		$result = mysql_query($query);
		if($assoc)
			return mysql_fetch_assoc($result);
		else
			return mysql_fetch_row($result);
	}

	function queryResult($query, $debug = false) {
		$this->checkconnected();
		if($debug) print $query;
		$result = @mysql_query($query);
		return @mysql_result($result, 0);
	}
	function queryInsert($query) {
		$this->checkconnected();
		@mysql_query($query);
		return(mysql_insert_id());
	}
	function queryNumrows($query) {
		$this->checkconnected();
		@mysql_query($query);
		return(mysql_num_rows());
	}
	function queryUpdate($query, $debug = false) {
		$this->checkconnected();
		if($debug) print $query;
		@mysql_query($query);
		return(mysql_affected_rows());
	}

}
?>