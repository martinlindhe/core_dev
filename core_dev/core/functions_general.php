<?php
/**
 * $Id$
 *
 * \disclaimer This file is a required component of core_dev
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * XXX
 */
function URLadd($_key, $_val = '', $_extra = '')	//FIXME: is this function even required???
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

/**
 * Helper function used to create "are you sure?" pages
 *
 * Example use:
 *	
 * if (confirmed('Are you sure you want to delete this rule?', 'id', $_GET['id'])) {
 *		deleteItem($_GET['id']);
 * }
 *	
 * Wiki-style link example use:
 * 
 * if (confirmed('Are you sure you want to delete this blog?', 'BlogDelete:'.$_id)) {
 *		deleteBlog($_GET['BlogDelete:'.$_id]);
 * }
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

/**
 * Looks for formatted wiki section commands, like: Wiki:Page, WikiEdit:Page, WikiHistory:Page, WikiFiles:Page
 * used by functions_wiki.php, functions_blogs.php for special url creation to allow these modules to be embedded in other pages
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

//FIXME: used by lyrics project only - maybe remove?
function cleanupText($text)
{
	global $db;

	$text = trim($text);

	do { //Remove chunks of whitespace
		$temp = $text;
		$text = str_replace('  ', ' ', $text);
	} while ($text != $temp);
	
	$text = str_replace('\n', "\n", $text);
	$text = str_replace('\r', "\r", $text);

	$text = str_replace("\r\n", "\n", $text);
	$text = str_replace(" \n", "\n", $text);
	$text = str_replace("\n ", "\n", $text);

	return $text;
}

?>
