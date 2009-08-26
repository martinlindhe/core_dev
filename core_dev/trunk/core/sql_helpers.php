<?php

/**
 * Modifies a table with multiple objects connected to a owner id
 */
class sql_id_list
{
	var $tbl_name, $owner_name, $val_name;
	var $owner, $list = array(), $items;

	/**
	 * Loads a list for the owner
	 * @ return number of items in the list
	 */
	function load($owner)
	{
		global $db;
		if (!is_numeric($owner)) return false;
		$this->owner = $owner;

		$q = 'SELECT '.$this->val_name.' FROM '.$this->tbl_name.' WHERE '.$this->owner_name.'='.$this->owner;
		$this->list = $db->get1dArray($q);
		$this->items = count($this->list);

		return $this->items;
	}

	/**
	 * Adds a entry to the list
	 */
	function add($val)
	{
		global $db;

		$this->list[] = $val;
		$this->items++;

		$q = 'INSERT INTO '.$this->tbl_name.' SET '.$this->owner_name.'='.$this->owner.','.$this->val_name.'="'.$db->escape($val).'"';
		return $db->insert($q);
	}

}



/**
 * Modifies a table with a unique id corresponding to a unique key
 * It makes sure that no duplicate keys exist in the table
 */
class sql_id_key
{
	var $tbl_name, $id_name, $key_name;
	var $id, $key;

	/**
	 * Loads a key value
	 */
	function load($id)
	{
		global $db;
		if (!is_numeric($id)) return false;

		$q = 'SELECT * FROM '.$this->tbl_name.' WHERE '.$this->id_name.'='.$id;
		$res = $db->getOneRow($q);
		$this->id  = $res[$this->id_name];
		$this->key = $res[$this->key_name];
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
		} else {
			$q = 'SELECT id FROM '.$this->tbl_name.' WHERE '.$this->key_name.'="'.$db->escape($key).'"';
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

}


?>
