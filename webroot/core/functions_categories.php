<?
	define('CATEGORY_USERFILES', 1);	//special type that represents 1-10
	$config['categories']['files_types'] = array(1 => 'normal', 2 => 'private', 3 => 'hidden', 10 => 'global');

	define('CATEGORY_NEWS',		20);

	function addCategory($_type, $_name)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type)) return false;

		$_name = $db->escape(trim($_name));
		if (!$_name) return false;

		$q = 'INSERT INTO tblCategories SET categoryType='.$_type.',categoryName="'.$_name.'",timeCreated=NOW(),creatorId='.$session->id;

		$db->query($q);
		return $db->insert_id;
	}
	
	function removeCategory($_type, $_id)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'DELETE FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;
		return $db->delete($q);
	}

	function getCategory($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;

		return $db->getOneRow($q);
	}

	function getCategoryName($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q  = 'SELECT categoryName FROM tblCategories WHERE categoryId='.$_id;

		return $db->getOneItem($q);
	}

	function getCategories($_type)
	{
		global $db;

		if (!is_numeric($_type)) return false;
		
		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' ';
		$q .= 'ORDER BY categoryName ASC';

		return $db->getArray($q);
	}

	//returns own categories & global categories (categoryPermissions==10)
	function getGlobalAndUserCategories($_type)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type)) return false;
		
		if ($_type == CATEGORY_USERFILES) {
			$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR categoryPermissions=10) AND categoryType>=1 AND categoryType<=10';
		} else {		
			$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR categoryPermissions=10) AND categoryType='.$_type;
		}
		return $db->getArray($q);
	}

	function getCategoriesSelect($_type, $selectName = '', $selectedId = 0)
	{
		global $config;

		if (!is_numeric($_type)) return false;

		$content = '<select name="'.strip_tags($selectName).'">';

		$content .= '<option value="0"></option>';
		$list = getGlobalAndUserCategories($_type);
		$shown_global_grop = 0;

		foreach ($list as $row) {
			if (!$shown_global_grop && $row['categoryType']==10) {
				$content .= '<optgroup label="Global categories">';
				$shown_global_grop = 1;
			}
			if ($shown_global_grop && !$row['globalCategory']) {
				$content .= '</optgroup>';
				$content .= '<optgroup label="Your categories">';
				$shown_global_grop = 0;
			}
			$content .= '<option value="'.$row['categoryId'].'"';
			if ($selectedId == $row['categoryId']) $content .= ' selected="selected"';
			$content .= '>'.$row['categoryName'].'</option>';
		}
		if ($shown_global_grop) $content .= '</optgroup>';

		$content .= '</select>';

		return $content;
	}
?>