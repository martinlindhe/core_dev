<?
	/*
		atom_categories.php - set of functions to implement categories, used by various modules

		By Martin Lindhe, 2007
	*/

	define('CATEGORY_USERFILE',		1);	//normal, public userfile
	define('CATEGORY_USERFILE_PRIVATE', 2);	//private userfile, only visible for the users friends / invited ppl
	define('CATEGORY_USERFILE_HIDDEN', 3);	//files here are only visible by the owner
	define('CATEGORY_USERFILE_GLOBAL',	4);	//can only be made by admins, global so it can be used by everyone

	define('CATEGORY_BLOG', 				10);		//normal, personal blog category
	define('CATEGORY_BLOG_GLOBAL',	11);		//can only be made by admins, global so it can be used by everyone

	define('CATEGORY_NEWS',				20);

	define('CATEGORY_LANGUAGE',		50);	//represents a language, for multi-language features & used by "lang" project

	function addCategory($_type, $_name)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type)) return false;

		$_name = $db->escape(trim($_name));
		if (!$_name) return false;

		$q = 'SELECT categoryId FROM tblCategories WHERE categoryType='.$_type.' AND categoryName="'.$_name.'" AND creatorId='.$session->id;
		$check = $db->getOneItem($q);
		if ($check) return false;

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
		
		if ($_type == CATEGORY_USERFILE) {
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

		if (!$selectName) $selectName = 'default';
		$content = '<select name="'.strip_tags($selectName).'">';

		$content .= '<option value="0">&nbsp;</option>';
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

	/* Default "create a new category" dialog, used by "create blog category" and "create category in personal file area" */
	function makeNewCategoryDialog($_type)
	{
		global $config, $session;

		if ($session->id && ($session->isAdmin || $_type==FILETYPE_USERFILE) && !empty($_POST['new_file_category']) && is_numeric($_POST['new_file_category_type']))
		{
			//Create new category. Only allow categories inside root level
			addCategory($_POST['new_file_category_type'], $_POST['new_file_category']);
		}

		echo '<form name="new_file_category" method="post" action="">';
		echo 'Category name: <input type="text" name="new_file_category"/> ';
		echo '<br/>';

		if ($_type == CATEGORY_USERFILE) {
			echo '<input type="radio" value="'.CATEGORY_USERFILE.'" name="new_file_category_type" id="_normal" checked="checked"/> ';
			echo '<label for="_normal">Normal category - everyone can see the content</label><br/><br/>';
			echo '<input type="radio" value="'.CATEGORY_USERFILE_PRIVATE.'" name="new_file_category_type" id="_private"/> ';
			echo '<label for="_private">Make this category private (only for your friends)</label><br/><br/>';

			echo '<input type="radio" value="'.CATEGORY_USERFILE_HIDDEN.'" name="new_file_category_type" id="_hidden"/> ';
			echo '<label for="_hidden">Make this category hidden (only for you)</label><br/><br/>';

			if ($session->isSuperAdmin) {
				echo '<input type="radio" value="'.CATEGORY_USERFILE_GLOBAL.'" name="new_file_category_type" id="_global"/> ';
				echo '<label for="_global" class="okay">Super admin: Make this category globally available</label><br/><br/>';
			}
		} else if ($_type == CATEGORY_BLOG) {
			echo '<input type="radio" value="'.CATEGORY_BLOG.'" name="new_file_category_type" id="_normal" checked="checked"/> ';
			echo '<label for="_normal">Normal category - everyone can see the content</label><br/><br/>';
			if ($session->isSuperAdmin) {
				echo '<input type="radio" value="'.CATEGORY_BLOG_GLOBAL.'" name="new_file_category_type" id="_global"/> ';
				echo '<label for="_global" class="okay">Super admin: Make this category globally available</label><br/><br/>';
			}
		}

		echo '<input type="submit" class="button" value="Create"/> ';
		echo '<input type="button" class="button" value="Cancel" onclick="show_element_by_name(\'file_gadget_upload\'); hide_element_by_name(\'file_gadget_category\');"/>';
		echo '</form>';
	}
?>