<?php
/**
 * $Id
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('functions_blogs.php');

require_once('xhtml_form.php');

die('WIP');

class Blog
{
	var $id = 0;


	function __construct($id = 0)
	{
		if ($id) {
			$this->id = $id;
			return getBlog($id); //XXX: map values to BlogEntry class (?)
		}
	}

	/**
	 *
	 */
	function edit()
	{
		$x = new xhtml_form('blogedit');

		$x->handler('blogSaveHandler');
		$x->hidden(  'blogid',    $this->id);
		$x->input(   'blogtitle', 'Title:', 'xx');
		$x->textarea('blogbody',  'Body:',  'xx');
		$x->submit('Save');

		$x->render();
	}

	function delete()
	{
		if (!$this->id) return false;
		deleteBlog($this->id);
		$this->id = 0;
	}


}

function blogSaveHandler($p)
{
	print_r($p);
/*
	if (!$this->id) {
		addBlog($categoryId, $title, $body, $isPrivate);
	}
*/
}

?>
