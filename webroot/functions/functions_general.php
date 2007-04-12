<?
	//returns a $pager array with some properties filled
	//reads the 'p' get parameter for current page
	//example: $pager = makePager(102, 25);		will create a pager for total of 102 items with 25 items per page
	
	//todo: gör så att den kan räkna ut LIMIT x,y för kommande sql query
	
	function makePager($_total_cnt, $_items_per_page, $_add_value = '')
	{
		$pager['page'] = 1;
		$pager['items_per_page'] = $_items_per_page;
		if (!empty($_GET['p']) && is_numeric($_GET['p'])) $pager['page'] = $_GET['p'];

		$pager['tot_pages'] = round($_total_cnt / $_items_per_page+0.5); // round to closest whole number
		$pager['head'] = 'Page '.$pager['page'].' of '.$pager['tot_pages'].' ('.$_total_cnt.' total)<br/><br/>';

		if ($pager['tot_pages'] <= 1) return $pager;

		if ($pager['page'] > 1) {
			$pager['head'] .= '<a href="'.$_SERVER['PHP_SELF'].'?p='.($pager['page']-1);
			if ($_add_value) $pager['head'] .= $_add_value;
			$pager['head'] .= '"><img src="/gfx/arrow_prev.png" alt="Previous" width="11" height="12"/></a>';
		} else {
				$pager['head'] .= '<img src="/gfx/arrow_prev_gray.png" alt="" width="11" height="12"/>';
		}

		for ($i=1; $i <= $pager['tot_pages']; $i++) {
			if ($i==$pager['page']) $pager['head'] .= '<b>';
			$pager['head'] .= ' <a href="'.$_SERVER['PHP_SELF'].'?p='.$i;
			if ($_add_value) $pager['head'] .= $_add_value;
			$pager['head'] .= '">'.$i.'</a>';
			if ($i==$pager['page']) $pager['head'] .= '</b>';
			$pager['head'] .= ' ';
		}

		if ($pager['page'] < $pager['tot_pages']) {
			$pager['head'] .= '<a href="'.$_SERVER['PHP_SELF'].'?p='.($pager['page']+1);
			if ($_add_value) $pager['head'] .= $_add_value;
			$pager['head'] .= '"><img src="/gfx/arrow_next.png" alt="Next" width="11" height="12"/></a>';
		} else {
			$pager['head'] .= '<img src="/gfx/arrow_next_gray.png" alt="" width="11" height="12"/>';
		}

		return $pager;
	}
?>