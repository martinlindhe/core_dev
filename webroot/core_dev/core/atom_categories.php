<?
	/*
		atom_categories.php - set of functions to implement categories, used by various modules

		By Martin Lindhe, 2007
	*/

	//System categories. Reserved 1-50. Use a number above 50 for your own category types
	define('CATEGORY_USERFILE',					1);	//normal, public userfile
	define('CATEGORY_USERFILE_PRIVATE',	2);	//private userfile, only visible for the users friends / invited ppl
	define('CATEGORY_USERFILE_HIDDEN',	3);	//files here are only visible by the owner
	define('CATEGORY_WIKIFILE',					4);	//category for wiki file attachments, to allow better organization if needed
	define('CATEGORY_TODOLIST',					5);	//todo list categories

	define('CATEGORY_BLOG', 				10);		//normal, personal blog category
	define('CATEGORY_CONTACT',			11);		//friend relation category, like "Old friends", "Family"
	define('CATEGORY_USERDATA',			12);		//used for multi-choice userdata types. tblCategories.ownerId = tblUserdata.fieldId
	define('CATEGORY_POLL',					13);		//used for multi-choice polls. tblCategories.ownerId = tblPolls.pollId

	define('CATEGORY_NEWS',					20);

	define('CATEGORY_LANGUAGE',			51);		//represents a language, for multi-language features & used by "lang" project

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

		return $db->insert($q);
	}

	function updateCategory($_type, $_id, $name)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'UPDATE tblCategories SET categoryName="'.$db->escape($name).'" WHERE categoryType='.$_type.' AND categoryId='.$_id;
		$db->query($q);
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

	function getCategoryName($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q  = 'SELECT categoryName FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;

		return $db->getOneItem($q);
	}

	function getCategoriesByOwner($_type, $_owner)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' AND ownerId='.$_owner;

		return $db->getArray($q);
	}

	function getCategories($_type, $_owner = false)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' ';
		if ($_owner !== false) $q .= 'AND ownerId='.$_owner.' ';
		$q .= 'ORDER BY categoryName ASC';

		return $db->getArray($q);
	}

	//returns own categories & global categories (categoryPermissions==10)
	//some category types (like CATEGORY_USERDATA) uses the $_owner parameter, to specify what userdata field this category belongs to
	function getGlobalAndUserCategories($_type, $_owner = 0)
	{
		global $db, $session;

		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		switch ($_type)
		{
			case CATEGORY_USERFILE:
				if (!$session->id) return false;
				$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR categoryPermissions=10) AND categoryType BETWEEN 1 AND 3 ORDER BY categoryPermissions DESC';
				break;

			case CATEGORY_BLOG:
				if (!$session->id) return false;
				$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR categoryPermissions=10) AND categoryType='.$_type.' ORDER BY categoryPermissions DESC';
				break;

			case CATEGORY_NEWS:
			case CATEGORY_POLL:
			case CATEGORY_CONTACT:
			case CATEGORY_USERDATA:
			case CATEGORY_WIKIFILE:
			case CATEGORY_LANGUAGE:
			case CATEGORY_TODOLIST:
				$q = 'SELECT * FROM tblCategories WHERE categoryType='.$_type;
				if ($_owner) $q .= ' AND ownerId='.$_owner;
				break;

			default:
				die('bleek');
		}

		return $db->getArray($q);
	}

	function getCategoriesSelect($_type, $_owner = 0, $selectName = '', $selectedId = 0, $url = '', $varName = '', $extra = '')
	{
		global $config;

		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		if (!$selectName) $selectName = 'default';
		$content = '<select name="'.strip_tags($selectName).'">';

		if ($_type == CATEGORY_USERFILE) {
			$content .= '<option value="0" onclick="location.href=\'?file_category_id=0\'">&nbsp;</option>';
		} else {
			$content .= '<option value="0">&nbsp;</option>';
		}

		$shown_global_cats = false;
		$shown_my_cats = false;

		$list = getGlobalAndUserCategories($_type, $_owner);

		foreach ($list as $row)
		{
			if ($_type != CATEGORY_CONTACT && $_type != CATEGORY_USERDATA && $_type != CATEGORY_NEWS && $_type != CATEGORY_LANGUAGE && !$shown_global_cats && $row['categoryPermissions']==10) {
				$content .= '<optgroup label="Global categories">';
				$shown_global_cats = true;
			}
			if ($_type != CATEGORY_CONTACT && $_type != CATEGORY_USERDATA && $_type != CATEGORY_NEWS && $_type != CATEGORY_LANGUAGE && !$shown_my_cats && $row['categoryPermissions']!=10) {
				$content .= '</optgroup>';
				$content .= '<optgroup label="Your categories">';
				$shown_my_cats = true;
			}

			/* If text is formatted like "123|Text" then 123 will be used as value for this option */
			$data = explode('|', $row['categoryName']);
			if (!empty($data[1])) {
				$val = $data[0];
				$text = $data[1];
			} else {
				$val = $row['categoryId'];
				$text = $data[0];
			}

			$content .= '<option value="'.$val.'"';
			if ($selectedId == $val) $content .= ' selected="selected"';
			else if ($url) {
				if ($varName) {
					$content .= ' onclick="location.href=\''.$url.'?'.$varName.'='.$row['categoryId'].$extra.'\'"';
				} else {
					$content .= ' onclick="location.href=\''.$url.'='.$row['categoryId'].$extra.'\'"';
				}
			}
			$content .= '>'.$text;
			if ($row['categoryType'] == CATEGORY_USERFILE_PRIVATE) $content .= ' (PRIVATE)';
			if ($row['categoryType'] == CATEGORY_USERFILE_HIDDEN) $content .= ' (HIDDEN)';
			$content .= '</option>';
		}
		if ($shown_global_cats || $shown_my_cats) $content .= '</optgroup>';

		$content .= '</select>';

		return $content;
	}

	/* Default "create a new category" dialog, used by "create blog category" and "create category in personal file area"
		also allows for managing and deleting categories				*/
	function manageCategoriesDialog($_type)
	{
		global $config, $session;
		
		if (!$session->id) return getCategoriesSelect($_type);

		if (($session->isAdmin || $_type==CATEGORY_USERFILE) && !empty($_POST['new_file_category']))
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

		if (!empty($_GET['cat_del_id']) && is_numeric($_GET['cat_del_id'])) {
			removeCategory($_type, $_GET['cat_del_id']);
			echo 'Category removed!';
			return;
		}
		
		$edit_id = 0;
		if (!empty($_GET['cat_edit_id']) && is_numeric($_GET['cat_edit_id'])) $edit_id = $_GET['cat_edit_id'];

		if ($edit_id && !empty($_POST['cat_name'])) {
			updateCategory($_type, $edit_id, $_POST['cat_name']);
		} else if ($edit_id) {
			
			$data = getCategory($_type, $edit_id);
			if (!$data) die;

			echo '<form method="post" action="">';
			echo '<h2>Edit category</h2>';
			echo 'Current name: '.$data['categoryName'].'<br/>';
			echo 'New name: <input type="text" name="cat_name" value="'.$data['categoryName'].'"/> ';
			echo '<input type="submit" class="button" value="Save"/>';
			echo '</form>';
			echo '<a href="'.URLadd('cat_del_id', $edit_id).'">Delete category</a>';
			return;
		}

		if ($_type == CATEGORY_USERFILE) {
			echo 'Existing categories: '.getCategoriesSelect($_type).'<br/>';
		} else {
			echo 'Existing categories: '.getCategoriesSelect($_type, 0, '', 0, URLadd('cat_edit_id')).'<br/>';
		}

		echo 'Select one from the dropdown list to edit it.<br/><br/>';

		echo '<form name="new_file_category" method="post" action="">';
		echo 'Create new category:<br/>';
		echo '<input type="text" name="new_file_category"/> ';

		if ($_type == CATEGORY_USERFILE) {
			echo '<br/>';
			echo '<input type="radio" value="'.CATEGORY_USERFILE.'" name="new_file_category_type" id="l_normal" checked="checked"/> ';
			echo '<label for="l_normal">Normal category - everyone can see the content</label><br/><br/>';
			echo '<input type="radio" value="'.CATEGORY_USERFILE_PRIVATE.'" name="new_file_category_type" id="l_private"/> ';
			echo '<label for="l_private">Make this category private (only for your friends)</label><br/><br/>';

			echo '<input type="radio" value="'.CATEGORY_USERFILE_HIDDEN.'" name="new_file_category_type" id="l_hidden"/> ';
			echo '<label for="l_hidden">Make this category hidden (only for you)</label><br/><br/>';
		} else if ($_type == CATEGORY_BLOG) {
			echo '<br/>';
			echo '<input type="radio" value="'.CATEGORY_BLOG.'" name="new_file_category_type" id="l_normal" checked="checked"/> ';
			echo '<label for="l_normal">Your personal blog category</label><br/><br/>';
		}
		if ($_type != CATEGORY_NEWS && $_type != CATEGORY_CONTACT && $session->isSuperAdmin) {
			echo '<br/>';
			echo '<input type="radio" value="global" name="new_file_category_type" id="l_global"/> ';
			echo '<label for="l_global" class="okay">Super admin: Make this category globally available</label><br/><br/>';
		}

		echo '<input type="submit" class="button" value="Create"/> ';
		//echo '<input type="button" class="button" value="Cancel" onclick="show_element_by_name(\'file_gadget_upload\'); hide_element_by_name(\'file_gadget_category\');"/>';
		echo '</form>';
	}
?>