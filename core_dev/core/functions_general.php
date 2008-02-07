<?
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	function URLadd($_key, $_val = '', $_extra = '')
	{
		$curr_url = 'http://localhost'.$_SERVER['REQUEST_URI'];
		
		$arr = parse_url($curr_url);

		$wiki_link = false;
		$pos = strpos($_key, ':');
		if ($pos !== false) $wiki_link = substr($_key, $pos+1);

		if ($_val) {
			$keyval = $_key.'='.$_val;
		} else {
			$keyval = $_key;
		}

		if (empty($arr['query'])) return $arr['path'].'?'.$keyval.$_extra;

		$args = explode('&', $arr['query']);

		$out_args = '';

		for ($i=0; $i<count($args); $i++) {		//fixme: use foreach
			
			$vals = explode('=', $args[$i]);

			//Skip it here, $keyval will be added later
			if ($vals[0] == $_key) continue;

			//Wiki:Style links
			if ($wiki_link && strpos($vals[0], ':')) {
				if (substr($vals[0], strpos($vals[0], ':')+1) == $wiki_link) {
					$out_args .= $keyval.'&amp;';	//Replaces wiki link with current wiki link
					$keyval = '';
					continue;
				}
			}

			if (isset($vals[1])) {
				$out_args .= $vals[0].'='.urlencode($vals[1]).'&amp;';
			} else {
				$out_args .= $vals[0].'&amp;';
			}
		}

		if ($out_args && !$keyval && !$_extra) $out_args = substr($out_args, 0, -strlen('&amp;'));

		if ($out_args) {
			return $arr['path'].'?'.$out_args.$keyval.$_extra;
		} else {
			return $arr['path'].'?'.$keyval.$_extra;
		}
	}

	/* Helper function used to create "are you sure?" pages
	
		Example use:
		
		if (confirmed('Are you sure you want to delete this rule?', 'id', $_GET['id'])) {
			deleteItem($_GET['id']);
		}
		
		Wiki-style link example use:
		
		if (confirmed('Are you sure you want to delete this blog?', 'BlogDelete:'.$_id)) {
			deleteBlog($_GET['BlogDelete:'.$_id]);
		}
	*/
	function confirmed($text, $_var, $_id = 0)
	{
		global $project;	//path to design includes
		global $config, $db, $session, $time_start;

		if (!$_var || !is_numeric($_id) || isset($_GET['confirmed'])) return true;

		require_once($project.'design_head.php');

		echo $text.'<br/><br/>';
		if ($_id) {
			//Normal links
			echo '<a href="'.URLadd('confirmed&amp;'.$_var, $_id).'">Yes, I am sure</a><br/><br/>';
		} else {
			//Wiki-style links
			//fixme: use URLadd() here
			echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_var.'&amp;confirmed'.getProjectPath().'">Yes, I am sure</a><br/><br/>';
		}
		echo '<a href="javascript:history.go(-1);">No, wrong button</a><br/>';

		require_once($project.'design_foot.php');
		die;
	}

	/* Takes an array of menu entries and creates a <ul><li>-style menu */
	function createMenu($menu_arr, $class = 'ulli_menu', $current_class = 'ulli_menu_current')
	{
		$cur = basename($_SERVER['SCRIPT_NAME']);
		
		echo '<ul class="'.$class.'">';
			foreach($menu_arr as $url => $text) {

				//if ($cur == $url || isset($_GET[str_replace('?','',$url)])) echo '<li class="'.$current_class.'">';
				//fixme: highlitar inte wiki parametrar t.ex "Wiki:Hello", "Blog:243"
				if ($cur == $url) echo '<li class="'.$current_class.'">';
				else echo '<li>';

				echo '<a href="'.($url[0] != '/' ? getProjectPath(3) : '').$url.'">'.$text.'</a>';
				echo '</li>';
			}
		echo '</ul>';
	}

	/* Looks for formatted wiki section commands, like: Wiki:Page, WikiEdit:Page, WikiHistory:Page, WikiFiles:Page
		used by functions_wiki.php, functions_blogs.php for special url creation to allow these modules to be embedded in other pages
	*/
	function fetchSpecialParams($allowed_tabs)
	{
		$paramName = '';
		$current_tab = '';

		foreach($_GET as $key => $val) {
			$arr = explode(':', $key);
			if (empty($arr[1]) || !in_array($arr[0], $allowed_tabs)) continue;
			$arr[1] = trim($arr[1]);
			return $arr;
		}

		return false;
	}

	/* Creates a complete XHTML header, showing rss feeds if available, etc
		Uses the following global variables, if they are set:

		$title			- <title> of current page. set the default title with $config['session']['default_title']		FIXME rename
		$meta_rss[]		- array of rss feeds to expose for current page
		$meta_js[]		- array of javascript files that needs to be included for current page
		$body_onload[] - array of js function(s) to call on load
	*/
	function createXHTMLHeader()
	{
		global $config, $session, $title, $meta_rss, $meta_js, $meta_search, $body_onload;

		if (!$title) $title = $config['default_title'];

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
		echo '<head>';
			echo '<title>'.$title.'</title>';
			echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';
			echo '<link rel="stylesheet" href="'.$config['core_web_root'].'css/core.css" type="text/css"/>';
			//echo '<link rel="stylesheet" href="'.$config['core_web_root'].'css/os3grid.css" type="text/css"/>';
			if (!empty($config['my_themes'])) $theme_dir = $config['my_themes'];
			else $theme_dir = $config['core_web_root'].'css/themes/';
			if (!empty($session)) echo '<link rel="stylesheet" href="'.$theme_dir.$session->theme.'" type="text/css"/>';
			echo '<link rel="stylesheet" href="'.$config['web_root'].'css/site.css" type="text/css"/>';

			if ($meta_rss) {
				foreach ($meta_rss as $feed) {
					if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'].getProjectPath();
					else $extra = getProjectPath(0);
					echo "\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="'.$config['core_web_root'].'api/rss_'.$feed['name'].'.php'.$extra.'"/>'."\n";
				}
			}

			if ($meta_search) {
				foreach ($meta_search as $search) {
					echo '<link rel="search" type="application/opensearchdescription+xml" href="'.$search['url'].'" title="'.$search['name'].'"/>';
				}
			}

			//echo '<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>';
			echo '<script type="text/javascript" src="'.$config['core_web_root'].'js/ajax.js"></script>';
			//echo '<script type="text/javascript" src="'.$config['core_web_root'].'js/drag_drop.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core_web_root'].'js/swfobject.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core_web_root'].'js/functions.js"></script>';
			//echo '<script type="text/javascript" src="'.$config['core_web_root'].'js/os3grid.js"></script>';
			if ($meta_js) {
				foreach ($meta_js as $script) {
					echo '<script type="text/javascript" src="'.$script.'"></script>';
				}
			}

		echo '</head>';
		if ($body_onload) {

			echo '<body onload="';
			foreach ($body_onload as $row) {
				echo $row;
			}
			echo '">';

		} else {
			echo '<body>';
		}
		echo '<script type="text/javascript">';
		echo 'var _ext_ref="'.getProjectPath(2).'",_ext_core="'.$config['core_web_root'].'api/";';
		echo '</script>';
	}

	//used by lyrics project only - maybe remove?
	function cleanupText($text)
	{
		global $db;

		$text = trim($text);

		do { /* Remove chunks of whitespace */
			$temp = $text;
			$text = str_replace('  ', ' ', $text);
		} while ($text != $temp);
		
		$text = str_replace('\n', "\n", $text);	//ersätter strängen '\n' mot en linefeed
		$text = str_replace('\r', "\r", $text);	//ersätter strängen '\r' mot en carriage return

		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace(" \n", "\n", $text);
		$text = str_replace("\n ", "\n", $text);

		return $text;
	}
	
	/* Executes $c and returns the time it took */
	function exectime($c)
	{
		$exec_start = microtime(true);
		exec($c);
		return microtime(true) - $exec_start;
	}
?>