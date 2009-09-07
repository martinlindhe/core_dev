<?php
/**
 * Modifies a table with a unique id corresponding to a unique key
 * It makes sure that no duplicate keys exist in the table
 */

class sql_id_key
{
	private $tbl_name, $id_name, $key_name;
	private $id, $key;

	function setTableName($n) { $this->tbl_name = $n; }
	function setIdName($n) { $this->id_name = $n; }
	function setKeyName($n) { $this->key_name = $n; }

	function getId() { return $this->id; }
	function getKey() { return $this->key; }
	function getTableName() { return $this->tbl_name; }
	function getIdName() { return $this->id_name; }
	function getKeyName() { return $this->key_name; }

	/**
	 * Sets key id and loads it from db
	 */
	function setId($id)
	{
		global $db;
		if (!is_numeric($id)) return false;

		$this->id = $id;

		$q = 'SELECT '.$this->key_name.' FROM '.$this->tbl_name.' WHERE '.$this->id_name.'='.$this->id;
		$this->key = $db->getOneItem($q);
	}

	/**
	 * Creates or updates a key value
	 *
	 * @return row id
	 */
	function save($key)
	{
		global $db;
		if ($this->id) {
			$q = 'UPDATE '.$this->tbl_name.' SET '.$this->key_name.'="'.$db->escape($key).'" WHERE '.$this->id_name.'='.$this->id;
			$db->update($q);
			return $this->id;
		}

		$q = 'SELECT '.$this->id_name.' FROM '.$this->tbl_name.' WHERE '.$this->key_name.'="'.$db->escape($key).'"';
		$id = $db->getOneItem($q);
		if (!$id) {
			$q = 'INSERT INTO '.$this->tbl_name.' SET '.$this->key_name.'="'.$db->escape($key).'",created=NOW()';
			$id = $db->insert($q);
		}

		$this->id  = $id;
		$this->key = $key;
		return $this->id;
	}

}

?>
