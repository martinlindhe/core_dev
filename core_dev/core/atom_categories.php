<?php
/**
 * $Id$
 *
 * Set of functions to implement categories, used by various modules
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//tblCategory.categoryType: System categories. Reserved 1-50. Use a number above 50 for your own category types
define('CATEGORY_USERFILE',			1);		///< normal, public userfile
define('CATEGORY_WIKIFILE',			4);		///< category for wiki file attachments, to allow better organization if needed
define('CATEGORY_TODOLIST',			5);		///< todo list categories

define('CATEGORY_BLOG', 			10);	///< normal, personal blog category
define('CATEGORY_CONTACT',			11);	///< friend relation category, like "Old friends", "Family"
define('CATEGORY_USERDATA',			12);	///< used for multi-choice userdata types. tblCategories.ownerId = tblUserdata.fieldId
define('CATEGORY_POLL',				13);	///< used for multi-choice polls. tblCategories.ownerId = tblPolls.pollId

define('CATEGORY_NEWS',				20);	///< news categories
define('CATEGORY_LANGUAGE',			51);	///< represents a language, for multi-language features & used by "lang" project

//tblCategory.permissions:
define('CAT_PERM_PUBLIC',	0x01);	///< public category
define('CAT_PERM_PRIVATE',	0x02);	///< owner and owner's friends can see the content
define('CAT_PERM_HIDDEN',	0x04);	///< only owner can see the content

define('CAT_PERM_USER',		0x40);	///< category is created by user
define('CAT_PERM_GLOBAL',	0x80);	///< category is globally available to all users

	/**
	 * Adds a new category
	 *
	 * \param $_type type of category
	 * \param $_name name of category
	 * \param $_owner object owning category, the meaning depends on $_type
	 * \param $_flags boolean AND flags (personal/global) & (public/private/hidden)
	 */
	function addCategory($_type, $_name, $_owner = 0, $_flags = 0)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_type) || !is_numeric($_owner) || !is_numeric($_flags)) return false;

		$_name = $db->escape(trim($_name));
		if (!$_name) return false;

		$q = 'SELECT categoryId FROM tblCategories WHERE categoryType='.$_type.' AND categoryName="'.$_name.'" AND ownerId='.$_owner;
		$q .= ' AND creatorId='.$session->id;
		$check = $db->getOneItem($q);
		if ($check) return false;

		$q = 'INSERT INTO tblCategories SET categoryType='.$_type.',categoryName="'.$_name.'",ownerId='.$_owner;
		$q .= ',timeCreated=NOW(),creatorId='.$session->id.',permissions='.$_flags;

		return $db->insert($q);
	}

	/**
	 *
	 */
	function updateCategory($_type, $_id, $name)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'UPDATE tblCategories SET categoryName="'.$db->escape($name).'" WHERE categoryType='.$_type.' AND categoryId='.$_id;
		$db->query($q);
	}

	/**
	 *
	 */
	function removeCategory($_type, $_id)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'DELETE FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;
		return $db->delete($q);
	}

	/**
	 *
	 */
	function getCategory($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;

		return $db->getOneRow($q);
	}

	/**
	 * Returns the name of the specified category
	 *
	 * \param $_type type of category
	 * \param $_id category id
	 */
	function getCategoryName($_type, $_id)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q  = 'SELECT categoryName FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;

		return $db->getOneItem($q);
	}

	/**
	 * Returns the permissions of the specified category
	 *
	 * \param $_type type of category
	 * \param $_id category id
	 */
	function getCategoryPermissions($_type, $_id)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q  = 'SELECT permissions FROM tblCategories WHERE categoryType='.$_type.' AND categoryId='.$_id;

		return $db->getOneItem($q);
	}

	/**
	 * Returns the id of the specified category
	 *
	 * \param $_type type of category
	 * \param $_name category name
	 * \param $_flags optional matching permissions flags
	 */
	function getCategoryByName($_type, $_name, $_flags = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_flags)) return false;

		$q = 'SELECT categoryId FROM tblCategories WHERE categoryType='.$_type.' AND categoryName="'.$db->escape($_name).'"';
		if ($_flags) $q .= ' AND (permissions & '.$_flags.')';
		return $db->getOneItem($q);
	}

	/**
	 * Returns all categories of specified type belonging to specified owner
	 *
	 * \param $_type type of category
	 * \param $_owner object owning the categories (meaning depends on category type)
	 *
	 * \todo merge with getCategories(), add sort order optional parameter
	 */
	function getCategoriesByOwner($_type, $_owner)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' AND ownerId='.$_owner;

		return $db->getArray($q);
	}

	/**
	 *
	 */
	function getCategories($_type, $_owner = 0)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		$q  = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' ';
		if ($_owner) $q .= 'AND ownerId='.$_owner.' ';
		$q .= 'ORDER BY categoryName ASC';

		return $db->getArray($q);
	}

	/**
	 * Returns all global categories of specified type
	 *
	 * \param $_type type of categories
	 */
	function getGlobalCategories($_type)
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q = 'SELECT * FROM tblCategories WHERE categoryType='.$_type.' AND (permissions & '.CAT_PERM_GLOBAL.')';
		return $db->getArray($q);
	}

	/**
	 * Returns own categories & global categories (categoryPermissions==10)
	 *
	 * some category types (like CATEGORY_USERDATA) uses the $_owner parameter, to specify what userdata field this category belongs to
	 */
	function getGlobalAndUserCategories($_type, $_owner = 0)
	{
		global $db, $session;
		if (!is_numeric($_type) || !is_numeric($_owner)) return false;

		switch ($_type)
		{
			case CATEGORY_USERFILE:
				if (!$session->id) return false;
				$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR permissions & '.CAT_PERM_GLOBAL.') AND categoryType='.$_type.' ORDER BY permissions DESC';
				break;

			case CATEGORY_BLOG:
				if (!$session->id) return false;
				$q = 'SELECT * FROM tblCategories WHERE (creatorId='.$session->id.' OR permissions & '.CAT_PERM_GLOBAL.') AND categoryType='.$_type.' ORDER BY permissions DESC';
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

	/**
	 * Default "create a new category" dialog, used by "create blog category" and "create category in personal file area"
	 * also allows for managing and deleting categories
	 *
	 * \param $_type 
	 */
	function manageCategoriesDialog($_type)
	{
		global $config, $session;

		if (!$session->id) return getCategoriesSelect($_type);

		if (($session->isAdmin || $_type==CATEGORY_USERFILE) && !empty($_POST['new_file_category']))
		{
			//Create new category. Only allow categories inside root level
			$cat_type = $_type;
			if (!empty($_POST['new_file_category_type']) && is_numeric($_POST['new_file_category_type'])) $cat_type = $_POST['new_file_category_type'];

			$flags = $_POST['new_file_category_scope'] + $_POST['new_file_category_perm'];
			addCategory($cat_type, $_POST['new_file_category'], 0, $flags);
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

		echo 'Existing categories: '.getCategoriesSelect($_type, 0, '', 0, URLadd('cat_edit_id')).'<br/>';

		echo 'Select one from the dropdown list to edit it.<br/><br/>';

		echo '<form name="new_file_category" method="post" action="">';
		echo 'Create new category:<br/>';
		echo '<input type="text" name="new_file_category"/> ';
		echo '<input type="hidden" value="'.$_type.'" name="new_file_category_type"/>';

		if ($_type == CATEGORY_USERFILE) {
			echo '<br/>';
			echo '<input type="radio" value="'.CAT_PERM_USER.'" name="new_file_category_scope" id="l_normal" checked="checked"/> ';
			echo '<label for="l_normal">Personal category</label> ';

			echo '<input type="radio" value="'.CAT_PERM_GLOBAL.'" name="new_file_category_scope" id="l_global"/> ';
			echo '<label for="l_global" class="okay">Super admin: Global category</label><br/><br/>';


			echo '<input type="radio" value="'.CAT_PERM_PUBLIC.'" name="new_file_category_perm" id="l_public" checked="checked"/> ';
			echo '<label for="l_public">Public (visible to all)</label><br/>';

			echo '<input type="radio" value="'.CAT_PERM_PRIVATE.'" name="new_file_category_perm" id="l_private"/> ';
			echo '<label for="l_private">Make this category private (only for your friends)</label><br/>';

			echo '<input type="radio" value="'.CAT_PERM_HIDDEN.'" name="new_file_category_perm" id="l_hidden"/> ';
			echo '<label for="l_hidden">Make this category hidden (only for you)</label><br/>';
		} else if ($_type == CATEGORY_BLOG) {
			die('FIXME blog cat is broken');
			/*
			echo '<br/>';
			echo '<input type="radio" value="'.CATEGORY_BLOG.'" name="new_file_category_type" id="l_normal" checked="checked"/> ';
			echo '<label for="l_normal">Your personal blog category</label><br/><br/>';
			echo '<br/>';
			echo '<input type="radio" value="global" name="new_file_category_type" id="l_global"/> ';
			echo '<label for="l_global" class="okay">Super admin: Make this category globally available</label><br/><br/>';
			*/
		} else if ($_type != CATEGORY_NEWS && $_type != CATEGORY_CONTACT && $session->isSuperAdmin) {
			die('FIXME news cat is broken');
			/*
			echo '<br/>';
			echo '<input type="radio" value="global" name="new_file_category_type" id="l_global"/> ';
			echo '<label for="l_global" class="okay">Super admin: Make this category globally available</label><br/><br/>';
			*/
		}

		echo '<input type="submit" class="button" value="Create"/> ';
		echo '</form>';
	}
?>
