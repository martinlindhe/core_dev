<?php
/**
 * $Id
 *
 * Views blogs (BlogEntry objects), by category or user
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: use Comments class
//TODO richedit editor

require_once('xhtml_form.php');

require_once('class.BlogEntry.php');

class Blog
{
	private $owner    = 0; ///< owner id
	private $category = 0; ///< category id

	private $enable_rating = false;

	private $tabs = array('Blog', 'BlogEdit', 'BlogDelete', 'BlogReport', 'BlogComment');
	private $allow_files = true;

	function __construct()
	{
		global $h;

		if ($h->files)
			$this->tabs[] = 'BlogFiles';
		else
			$this->allow_files = false;
	}

	function setOwner($id)
	{
		if (!is_numeric($id)) return false;
		$this->owner;
	}

	/**
	 * @param $n categoryId or literal string
	 */
	function setCategory($n)
	{
		if (!is_numeric($n)) {
			//FIXME: create tblCategory entry if name dont exist: behöver fixa class.Categories.php först
			die('blog setCategory');
		}

		$this->category = $n;
	}

	/**
	 * Returns a BlogEntry object for requested entry
	 */
	function get($id)
	{
		if (!is_numeric($id)) return false;

		$this->id = $id;

		$entry = new BlogEntry();
		$entry->setOwner($this->owner);
		$entry->setCategory($this->category);
		$entry->setId($this->id);
		return $entry->get();
	}

	function render()
	{
		global $db;
		$q = 'SELECT blogId FROM tblBlogs WHERE categoryId='.$this->category;
		if ($this->owner) $q .= ' AND ownerId='.$this->owner;
		$list = $db->get1dArray($q);

		foreach ($list as $id)
		{
			echo $this->renderEntry($id);
		}
	}

	function renderEntry($id)
	{
		global $h;

		$blog = $this->get($id);

		$current_tab = 'Blog';

		//Looks for formatted blog section commands, like: Blog:ID, BlogEdit:ID, BlogDelete:ID, BlogReport:ID, BlogComment:ID, BlogFiles:ID
		$cmd = fetchSpecialParams($this->tabs);
		if ($cmd) list($current_tab, $_id) = $cmd;

		if ($blog->isDeleted()) {
			echo 'This blog has been deleted!<br/>';
			return false;
		}

		if (($h->session->id == $blog->getOwner() || $h->session->isAdmin) && isset($_POST['blog_cat']) && isset($_POST['blog_title']) && isset($_POST['blog_body'])) {
			$blog->setCategory($_POST['blog_cat']);
			$blog->setSubject($_POST['blog_title']);
			$blog->setBody($_POST['blog_body']);
			$blog->update();
		}

		echo '<div class="blog">';

		echo '<div class="blog_head">';
		echo '<div class="blog_title">'.$blog->getSubject().'</div>';

		if ($blog->getCategory()) echo '(category <b>'.$blog['categoryName'].'</b>)<br/><br/>';
		else echo ' (no category)<br/><br/>';

		echo 'Published '. $blog->timeCreated->getRelative().' by '.Users::link($blog->getOwner()).'<br/>';

		echo '</div>'; //class="blog_head"

		$menu = array('?Blog:'.$blog->getId() => 'Show blog');
		if ($h->session->id == $blog->getId() || $h->session->isSuperAdmin) {
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogEdit:'.$blog->getId() => 'Edit blog'));
			if ($this->allow_files) {
				$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogFiles:'.$blog->getId() => 'Attachments ('.$h->files->getFileCount(FILETYPE_BLOG, $blog->getId()).')'));
			}
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogDelete:'.$blog->getId() => 'Delete blog'));
		}
		if ($h->session->id && $h->session->id != $blog->getOwner()) {
			$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogReport:'.$blog->getId() => 'Report blog'));
		}
		$menu = array_merge($menu, array($_SERVER['PHP_SELF'].'?BlogComment:'.$blog->getId() => 'Comments ('.getCommentsCount(COMMENT_BLOG, $blog->getId()).')'));

		echo xhtmlMenu($menu, 'blog_menu');

		echo '<div class="blog_body">';

		if ($current_tab == 'BlogEdit' && ($h->session->id == $blog->getOwner() || $h->session->isAdmin) ) {

			$body = trim($blog->getBody());
			//convert | to &amp-version since it's used as a special character:
			$body = str_replace('|', '&#124;', $body);
			$body = $body."\n";	//always end with a new line

			echo xhtmlForm('', $_SERVER['PHP_SELF'].'?BlogEdit:'.$blog->getId() );
			echo xhtmlInput('blog_title', $blog->getSubject(), 40, 40);

			echo ' Category: ';
			echo xhtmlSelectCategory(CATEGORY_BLOG, 0, 'blog_cat', $blog->getCategory());
			echo '<br/><br/>';

			echo xhtmlTextarea('blog_body', $body, 65, 25).'<br/><br/>';
			echo xhtmlSubmit('Save changes').'<br/>';
			echo xhtmlFormClose();

			if ($blog->isUpdated())
				echo '<div class="blog_foot">Last updated '. $blog->timeUpdated->getRelative().'</div>';

		} else if ($current_tab == 'BlogDelete' && (($h->session->id && $h->session->id == $blog->getOwner()) || $h->session->isAdmin) ) {

			if (confirmed('Are you sure you want to delete this blog?', 'BlogDelete:'.$blog->getId())) {
				//deleteBlog($blog->getId());
				die('deleteblog!!!'); //FIXME
				echo 'The blog has been deleted.<br/>';
			}

		} else if ($current_tab == 'BlogReport' && $h->session->id) {

			if (isset($_POST['blog_reportreason'])) {
				$queueId = addToModerationQueue(MODERATION_BLOG, $blog->getId());
				addComment(COMMENT_MODERATION, $queueId, $_POST['blog_reportreason']);

				echo 'Your report has been recieved<br/>';
			} else {
				echo 'Report blog - <b>'.$blog->getSubject().'</b><br/><br/>';

				echo 'Why do you want to report this:<br/>';
				echo xhtmlForm('', $_SERVER['PHP_SELF'].'?BlogReport:'.$blog->getId() );
				echo xhtmlTextarea('blog_reportreason', '', 64, 6).'<br/><br/>';

				echo xhtmlSubmit('Report');
				echo xhtmlFormClose();
			}

		} else if ($current_tab == 'BlogComment') {

			echo showComments(COMMENT_BLOG, $blog->getId());

		} else if ($current_tab == 'BlogFiles' && ($h->session->id == $blog['userId'] || $h->session->isAdmin)) {

			echo showFiles(FILETYPE_BLOG, $blog->getId());

		} else {

			echo formatUserInputText($blog->getBody());

			if ($blog->isUpdated())
				echo '<div class="blog_foot">Last updated '. $blog->timeUpdated->getRelative().'</div>';


			if ($this->enable_rating) {
				echo '<div class="news_rate">';
				if ($h->session->id != $blog->getOwner()) {
					echo ratingGadget(RATE_BLOG, $blog->getId());
				} else {
					echo showRating(RATE_BLOG, $blog->getId());
				}
				echo '</div>';
			}
		}

		echo '</div>';
		echo '</div>'; //class="blog"
	}

}

?>
