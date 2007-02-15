<?
	//RSS 2.0 compatible feed
	//Read more: http://en.wikipedia.org/wiki/RSS_(file_format)

	include('../include_all.php');

	$list = getPublishedNews($db, 20);

	$server_url = 'http://'.$_SERVER['SERVER_NAME'];
	if ($_SERVER['SERVER_PORT'] != 80) $server_url .= ':'.$_SERVER['SERVER_PORT'];
	$server_url .= '/comm/';

	$title = 'uReply Nyheter';
	$description = 'De 20 senaste nyheterna från uReply';
	$copyright = 'Copyright 2005-2006 Agent Interactive. All Rights Reserved';
	$language = 'sv-se';
	$publish_date = date('r', time() );

	header('Content-type: application/xml');

	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	echo "<rss version=\"2.0\">\n";
	echo "<channel>\n";
	echo "\t<title>".$title."</title>\n";
	echo "\t<link>".$server_url."</link>\n";
	echo "\t<description>".$description."</description>\n";
	echo "\t<language>".$language."</language>\n";
	echo "\t<pubDate>".$publish_date."</pubDate>\n";
	echo "\t<copyright>".$copyright."</copyright>\n";
	echo "\t<webMaster>".$config['site_admin']."</webMaster>\n";
	echo "\t<generator>uReply RSS propagator</generator>\n";

	//embeddar en bild i newsfeeden
	echo "\t<image>\n";
		echo "\t\t<title>agentinteractive.se</title>\n";
		echo "\t\t<link>http://www.agentinteractive.se/</link>\n";
		echo "\t\t<description>Gå till agentinteractive.se</description>\n";
		echo "\t\t<url>http://localhost/CMS/rss/news_icon.png</url>\n";
		echo "\t\t<width>82</width>\n";
		echo "\t\t<height>45</height>\n";
	echo "\t</image>\n";

	for ($i=0; $i<count($list); $i++) {
		if (!$list[$i]['rss_enabled']) continue;
			
		$item_url = $server_url.'news.php?id='.$list[$i]['newsId'];

		echo "\t<item>\n";
			echo "\t\t<title>".$list[$i]['title']."</title>\n";
			echo "\t\t<pubDate>".date('r', $list[$i]['timetopublish'])."</pubDate>\n";
	
			echo "\t\t<link>".$item_url."</link>\n";
			echo "\t\t<guid>".$item_url."</guid>\n";
			echo "\t\t<description>".$list[$i]['body']."</description>\n";
		echo "\t</item>\n";
	}

	echo "</channel>\n";
	echo "</rss>\n";	
?>