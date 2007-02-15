<?
	include('include_all.php');

	include('design_head.php');


	if ($_SESSION['loggedIn']) {

		$list = getBlogsByCategory($db, $_SESSION['userId']);
	
		echo 'Publicerade bloggar:<br>';
	
		$shown_category = false;
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['categoryId'] != $shown_category) {
				$catName = $list[$i]['categoryName'];
				if (!$catName) $catName = 'Okategoriserade bloggar';
				echo '<br><b>'.$catName.'</b><br>';
				$shown_category = $list[$i]['categoryId'];
			}


			$title = $list[$i]['blogTitle'];
			if (!$title) $title = '(inget namn)';

			echo $list[$i]['timeCreated'].' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$title.'</a><br>';
		}
		echo '<br>';
			
		echo 'Senast publicerade bloggar:<br>';
	
		$list = getBlogsNewest($db, 5);
		for ($i=0; $i<count($list); $i++) {
			$title = $list[$i]['blogTitle'];
			if (!$title) $title = '(inget namn)';

			echo $list[$i]['timeCreated'].' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$title.'</a> ';
			echo 'av '.$list[$i]['userName'].'<br>';
		}

	}

	include('design_foot.php');
?>