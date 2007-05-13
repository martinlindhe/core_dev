<?
	/*
		atom_categories.php - set of functions to implement categories, used by various modules

		By Martin Lindhe, 2007
	*/

	define('CATEGORY_USERFILE',		1);	//normal, public userfile
	define('CATEGORY_USERFILE_PRIVATE', 2);	//private userfile, only visible for the users friends / invited ppl
	define('CATEGORY_USERFILE_HIDDEN', 3);	//files here are only visible by the owner

	define('CATEGORY_BLOG', 				10);		//normal, personal blog category
	define('CATEGORY_CONTACT',			11);		//friend relation category, like "Old friends", "Family"
	define('CATEGORY_USERDATA',			12);		//used for multi-choice userdata types

	define('CATEGORY_NEWS',				20);

	define('CATEGORY_LANGUAGE',		50);	//represents a language, for multi-language features & used by "lang" project

	function addCategory($_type, $_name, $_owner = 0, $_global = false)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type) || !is_numeric($_owner)) return false;

		$_name = $db->escape(trim($_name));
		if (!$_name) return false;

		$q = 'SELECT categoryId FROM tblCategories WHERE categoryType='.$_type.' AND categoryName="'.$_name.'" AND ownerId='.$_owner;
		if (!$_global) $q .= ' AND creatorId='.$session->id;
		$check = $db->getOneItem($q);
		if ($check) return false;

		$q = 'INSERT INTO tblCategories SET categoryType='.$_type.',categoryName="'.$_name.'",ownerId='.$_owner.',timeCreated=NOW(),creatorId='.$session->id;
		if ($session->isAdmin && $_global) $q .= ',categoryPermissions=10';
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

	function getCategoriesByOwner($_type, $_owner)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' AND ownerId='.$_owner;

		return $db->getArray($q);
	}

	function getCategoryName($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q  = 'SELECT categoryName FROM tblCategories WHERE categoryId='.$_id;

		return $db->getOneItem($q);
	}

	function getCategories($_type, $_owner)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_owner)) return false;
		
		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' ';
		if ($_owner) $q .= 'AND ownerId='.$_owner.' ';
		$q .= 'ORDER BY categoryName ASC';

		return $db->getArray($q);
	}

	//returns own categories & global categories (categoryPermissions==10)
	function getGlobalAndUserCategories($_type)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type)) return false;

		switch ($_type)
		{
			case CATEGORY_USERFILE:
				$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR categoryPermissions=10) AND categoryType BETWEEN 1 AND 3 ORDER BY categoryPermissions DESC';
				break;

			case CATEGORY_BLOG:
				$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR categoryPermissions=10) AND categoryType='.$_type.' ORDER BY categoryPermissions DESC';
				break;

			case CATEGORY_CONTACT:
			case CATEGORY_NEWS:
				$q = 'SELECT * FROM tblCategories WHERE categoryType='.$_type;
				break;

			default:
				die('bleek');
		}
		return $db->getArray($q);
	}

	function getCategoriesSelect($_type, $selectName = '', $selectedId = 0, $url = '')
	{
		global $config;

		if (!is_numeric($_type)) return false;

		if (!$selectName) $selectName = 'default';
		$content = '<select name="'.strip_tags($selectName).'">';

		if ($url) {
			$content .= '<option value="0" onclick="location.href=\'?'.$url.'=0\'">&nbsp;</option>';
		} else {
			$content .= '<option value="0">&nbsp;</option>';
		}

		$shown_global_cats = false;
		$shown_my_cats = false;

		$list = getGlobalAndUserCategories($_type);
		foreach ($list as $row)
		{
			if ($_type != CATEGORY_CONTACT && !$shown_global_cats && $row['categoryPermissions']==10) {
				$content .= '<optgroup label="Global categories">';
				$shown_global_cats = true;
			}
			if ($_type != CATEGORY_CONTACT && !$shown_my_cats && $row['categoryPermissions']!=10) {
				$content .= '</optgroup>';
				$content .= '<optgroup label="Your categories">';
				$shown_my_cats = true;
			}
			$content .= '<option value="'.$row['categoryId'].'"';
			if ($selectedId == $row['categoryId']) $content .= ' selected="selected"';
			else if ($url) $content .= ' onclick="location.href=\'?'.$url.'='.$row['categoryId'].'\'"';

			$content .= '>'.$row['categoryName'];
			if ($row['categoryType'] == CATEGORY_USERFILE_PRIVATE) $content .= ' (PRIVATE)';
			if ($row['categoryType'] == CATEGORY_USERFILE_HIDDEN) $content .= ' (HIDDEN)';
			$content .= '</option>';
		}
		if ($shown_global_cats || $shown_my_cats) $content .= '</optgroup>';

		$content .= '</select>';

		return $content;
	}

	/* Default "create a new category" dialog, used by "create blog category" and "create category in personal file area" */
	function makeNewCategoryDialog($_type)
	{
		global $config, $session;

		if ($session->id && ($session->isAdmin || $_type==CATEGORY_USERFILE) && !empty($_POST['new_file_category']))
		{
			$global = false;
			//Create new category. Only allow categories inside root level

			$cat_type = $_type;
			if (!empty($_POST['new_file_category_type'])) {
				if (is_numeric($_POST['new_file_category_type'])) $cat_type = $_POST['new_file_category_type'];
				else if ($_POST['new_file_category_type'] == 'global') $global = true;
			}
			addCategory($cat_type, $_POST['new_file_category'], 0, $global);
		}

		echo '<form name="new_file_category" method="post" action="">';
		echo 'Category name: <input type="text" name="new_file_category"/> ';
		echo '<br/>';

		if ($_type == CATEGORY_USERFILE) {
			echo '<input type="radio" value="'.CATEGORY_USERFILE.'" name="new_file_category_type" id="l_normal" checked="checked"/> ';
			echo '<label for="l_normal">Normal category - everyone can see the content</label><br/><br/>';
			echo '<input type="radio" value="'.CATEGORY_USERFILE_PRIVATE.'" name="new_file_category_type" id="l_private"/> ';
			echo '<label for="l_private">Make this category private (only for your friends)</label><br/><br/>';

			echo '<input type="radio" value="'.CATEGORY_USERFILE_HIDDEN.'" name="new_file_category_type" id="l_hidden"/> ';
			echo '<label for="l_hidden">Make this category hidden (only for you)</label><br/><br/>';
		} else if ($_type == CATEGORY_BLOG) {
			echo '<input type="radio" value="'.CATEGORY_BLOG.'" name="new_file_category_type" id="l_normal" checked="checked"/> ';
			echo '<label for="l_normal">Your personal blog category</label><br/><br/>';
		}
		if ($_type != CATEGORY_NEWS && $_type != CATEGORY_CONTACT && $session->isSuperAdmin) {
			echo '<input type="radio" value="global" name="new_file_category_type" id="l_global"/> ';
			echo '<label for="l_global" class="okay">Super admin: Make this category globally available</label><br/><br/>';
		}

		echo '<input type="submit" class="button" value="Create"/> ';
		echo '<input type="button" class="button" value="Cancel" onclick="show_element_by_name(\'file_gadget_upload\'); hide_element_by_name(\'file_gadget_category\');"/>';
		echo '</form>';
	}
?>