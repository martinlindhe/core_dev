<?
	//returns a $pager array with some properties filled
	//reads the 'p' get parameter for current page
	//example: $pager = makePager(102, 25);		will create a pager for total of 102 items with 25 items per page
	
	//$pager['limit'] är för-genererad LIMIT sql för att använda tillsammans med query som hämtar en del av en lista
	//  alternativt kan $pager['index'] och $pager['items_per_page'] användas för samma syfte
	function makePager($_total_cnt, $_items_per_page, $_add_value = '')
	{
		$pager['page'] = 1;
		$pager['items_per_page'] = $_items_per_page;
		if (!empty($_GET['p']) && is_numeric($_GET['p'])) $pager['page'] = $_GET['p'];

		$pager['tot_pages'] = round($_total_cnt / $_items_per_page+0.4); // round to closest whole number
		$pager['head'] = 'Page '.$pager['page'].' of '.$pager['tot_pages'].' ('.$_total_cnt.' total)<br/><br/>';

		$pager['index'] = ($pager['page']-1) * $pager['items_per_page'];
		$pager['limit'] = ' LIMIT '.$pager['index'].','.$pager['items_per_page'];

		if ($pager['tot_pages'] <= 1) return $pager;

		if ($pager['page'] > 1) {
			$pager['head'] .= '<a href="'.URLadd('p', $pager['page']-1, $_add_value).'">';
			$pager['head'] .= '<img src="/gfx/arrow_prev.png" alt="Previous" width="11" height="12"/></a>';
		} else {
			$pager['head'] .= '<img src="/gfx/arrow_prev_gray.png" alt="" width="11" height="12"/>';
		}

		for ($i=1; $i <= $pager['tot_pages']; $i++) {
			if ($i==$pager['page']) $pager['head'] .= '<b>';
			$pager['head'] .= ' <a href="'.URLadd('p', $i, $_add_value).'">'.$i.'</a> ';
			if ($i==$pager['page']) $pager['head'] .= '</b>';
		}

		if ($pager['page'] < $pager['tot_pages']) {
			$pager['head'] .= '<a href="'.URLadd('p', $pager['page']+1, $_add_value).'">';
			$pager['head'] .= '<img src="/gfx/arrow_next.png" alt="Next" width="11" height="12"/></a>';
		} else {
			$pager['head'] .= '<img src="/gfx/arrow_next_gray.png" alt="" width="11" height="12"/>';
		}

		return $pager;
	}
	
	/* Returns the project's path as a "project name" identifier. in a webroot hierarchy if scripts are
			run from the / path it will return nothing, else the directory name of the directory script are run from */
	function getProjectPath()
	{
		$project_path = dirname($_SERVER['SCRIPT_NAME']);
	
		$pos = strrpos($project_path, '/');
		$proj_name = substr($project_path, $pos+1);
		if ($proj_name) return '&amp;pr='.$proj_name;
		return '';
	}


	function wikiURLadd($_page, $_section, $_extra = '')
	{
		$_wikiURL = $_page.':'.urlencode($_section);
		
		$arr = parse_url($_SERVER['REQUEST_URI']);
		if (empty($arr['query'])) return $arr['path'].'?'.$_wikiURL;

		$args = explode('&', $arr['query']);
		
		$out_args = '';

		for ($i=0; $i<count($args); $i++) {
			$vals = explode('=', $args[$i]);
			
			$skipit = explode(':', $vals[0]);
			
			if (!isset($skipit[1]) && isset($vals[1])) {
				$out_args .= $vals[0].'='.urlencode($vals[1]).'&amp;';
			}
		}

		if ($out_args) {
			return $arr['path'].'?'.$out_args.'&amp;'.$_wikiURL.$_extra;
		}
		return $arr['path'].'?'.$_wikiURL.$_extra;
	}

	function URLadd($_key, $_val, $_extra)
	{
		$arr = parse_url($_SERVER['REQUEST_URI']);

		if ($_val) {
			$keyval = $_key.'='.$_val;
		} else {
			$keyval = $_key.'='.$_val;
		}

		if (empty($arr['query'])) return $arr['path'].'?'.$keyval.$_extra;

		$args = explode('&', $arr['query']);
		
		$out_args = '';

		for ($i=0; $i<count($args); $i++) {
			$vals = explode('=', $args[$i]);
			if ($vals[0] == $_key) continue;
			if (isset($vals[1])) {
				$out_args .= $vals[0].'='.urlencode($vals[1]).'&amp;';
			} else {
				$out_args .= $vals[0].'&amp;';
			}
		}

		if ($out_args) {
			return $arr['path'].'?'.$out_args.$keyval.$_extra;
		} else {
			return $arr['path'].'?'.$keyval.$_extra;
		}
	}
?>