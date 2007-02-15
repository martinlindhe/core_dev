<?

	$image_google 		= '<img src="design/search_google.png" width=37 height=14 alt="Google" title="Google" align="top">';
	$image_msn				= '<img src="design/search_msn.png" width=37 height=14 alt="MSN" title="MSN" align="top">';
	$image_yahoo			= '<img src="design/search_yahoo.png" width=37 height=14 alt="Yahoo" title="Yahoo" align="top">';
	$image_altavista	= '<img src="design/search_altavista.png" width=37 height=14 alt="Altavista" title="Altavista" align="top">';
	$image_aol				= '<img src="design/search_aol.png" width=37 height=14 alt="AOL" title="AOL" align="top">';
	$image_eniro			= '<img src="design/search_eniro.png" width=37 height=14 alt="Eniro" title="Eniro" align="top">';
	$image_spray			= '<img src="design/search_spray.png" width=37 height=14 alt="Spray" title="Spray" align="top">';
	$image_sesam			= '<img src="design/search_sesam.png" width=37 height=14 alt="Sesam" title="Sesam" align="top">';

/*
Google Samples:
	http://www.google.com/search?hl=en&lr=&q=www.agentinteractive.se
	http://www.google.se/search?hl=sv&q=agent interactive&btnG=Google-sökning&meta=
	http://www.google.no/search?hl=no&q=agentinteractive&btnG=Google-søk&meta=
	http://www.google.dk/search?hl=da&q=agentinteractive.se&btnG=Søg&meta=
	http://www.google.com/ie?q=telge stadsnät&hl=en&lr=
	http://www.google.com/xhtml?client=ms-opera_mb_no&channel=bh&q=telge nät
	http://wap.google.com/search?hl=sv&ie=ISO-8859-1&q=Www.agentinteractive.se&btnG=Google-sökning&lr=
	http://www.google.com/custom?q=jul%20p%E5%20herrg%E5rd&sa=S%F6k&client=pub-9087313366120704&forid=1&ie=ISO-8859-1&oe=ISO-8859-1&cof=GALT%3A%23008000%3BGL%3A1%3BDIV%3A%233386C4%3BVLC%3A663399%3BAH%3Acenter%3BBGC%3AFFFFFF%3BLBGC%3A3386C4%3BALC%3A0000AA%3BLC%3A0000AA%3BT%3A000000%3BGFNT%3A0000FF%3BGIMP%3A0000FF%3BLH%3A0%3BLW%3A0%3BL%3Ahttp%3A%2F%2Fwww.leta.se%2Fimg05%2Fgl3.gif%3BS%3Ahttp%3A%2F%2Fwww.leta.se%3BFORID%3A11&hl=sv&ad=w9&num=10

	as_q:			- ingen aning, som en vanlig query ?
	http://www.google.se/search?as_q=dialect itservice
	http://www.google.se/search?as_q=dialect itservice&num=10&hl=sv&btnG=Google-sökning&as_epq=&as_oq=&as_eq=&lr=&as_ft=i&as_filetype=&as_qdr=all&as_occt=any&as_dt=i&as_sitesearch=&as_rights=

	as_epq:		- "exact query" ??. samma sak som q="mäster samuelsgatan 38"
	http://www.google.se/search?as_epq=mäster samuelsgatan 38
	http://www.google.se/search?as_q=&num=10&hl=sv&btnG=Google-sökning&as_epq=mäster samuelsgatan 38&as_oq=&as_eq=&lr=&as_ft=i&as_filetype=&as_qdr=all&as_occt=any&as_dt=i&as_sitesearch=&as_rights=
	http://www.google.com/search?as_q=&num=10&hl=sv&btnG=Google-sökning&as_epq=IT Service AB&as_oq=&as_eq=&lr=&as_ft=i&as_filetype=&as_qdr=all&as_occt=any&as_dt=i&as_sitesearch=&as_rights=
				
	ie:				- code page (default is UTF-8 if none is specified)
*/
	function referrer_google_cleanup(&$query)
	{
		global $config;

		//Default value: UTF-8
		$decode_fmt = 'UTF-8';

		if (!empty($query['ie']))
		{
			switch (strtoupper($query['ie']))
			{
				case 'UTF8':
				case 'UTF-8':
					$decode_fmt = 'UTF-8';
					break;

				case 'ISO':
				case 'ISO-8859-1':
					$decode_fmt = 'ISO-8859-1';
					break;

				case 'ISO-8859-2':
					$decode_fmt = 'ISO-8859-2';
					break;

				default:
					echo 'Unrecognized Google codepage: '.$query['ie'].'<br>';
					echo '<pre>'; print_r($query); echo '</pre>';
			}
		}

		if (!empty($query['q'])) {
			$query['q'] = trim(mb_convert_encoding($query['q'], 'UTF-8', $decode_fmt));
			if ($config['lowercase_queries']) $query['q'] = mb_strtolower($query['q']);
		}
		else if (!empty($query['as_q'])) {
			$query['as_q'] = trim(mb_convert_encoding($query['as_q'], 'UTF-8', $decode_fmt));
			if ($config['lowercase_queries']) $query['as_q'] = mb_strtolower($query['as_q']);

			$query['q'] = $query['as_q'];
		}
		else if (!empty($query['as_epq'])) {
			$query['as_epq'] = trim(mb_convert_encoding($query['as_epq'], 'UTF-8', $decode_fmt));
			if ($config['lowercase_queries']) $query['as_epq'] = mb_strtolower($query['as_epq']);

			$query['q'] = $query['as_epq'];
		}

		if (empty($query['q'])) return false;
		
		return $query['q'];
	}
/*
MSN Samples:
	http://search.msn.com/results.aspx?q=agentinteractive.se&Form=MSNH
	http://search.msn.se/results.aspx?FORM=MSNH&CP=1252&q=agent interactive
	http://search.msn.se/spresults.aspx?q=energimätning&first=31&count=10&FORM=POPR	
	http://search.msn.se/results.asp?FORM=AS35&srch=5&q=stadsnätet
	
	http://search.live.com/results.aspx?q=herrgård&first=11&FORM=PERE
				
	CP - code page. 1252 är ascii enkodad query, om den saknas är queryn UTF-8
*/
	function referrer_msn_cleanup(&$query)
	{
		global $config;

		//Default value: UTF-8
		$decode_fmt = 'UTF-8';

		if (!empty($query['CP']))
		{
			switch (strtoupper($query['CP']))
			{
				case '1252':	$decode_fmt = 'ASCII'; break;
				default:			echo 'Unrecognized MSN codepage: '.$query['CP'].'<br>'; break;
			}
		}
		
		$query['q'] = trim(mb_convert_encoding($query['q'], 'UTF-8', $decode_fmt));
		if ($config['lowercase_queries']) $query['q'] = mb_strtolower($query['q']);

		if (empty($query['q'])) return false;
		
		return $query['q'];
	}

/*
	Yahoo samples:
	http://search.yahoo.com/search?p=agentinteractive.se&fr=FP-tab-web-t400&toggle=1&cop=&ei=UTF-8
	http://se.search.yahoo.com/search?fr=fp-tab-web-t-1&ei=ISO-8859-1&p=agent interactive&meta=vc=
	http://search.yahoo.com/bin/search?p=kamerabatteri canon
	http://search.yahoo.com/search/msie?p=herrgård&ei=UTF-8&b=211
	
	http://search.yahoo.com/search;_ylt=A0geuq2mylpFReYAe39XNyoA?p=Davis%20cup%20Sverige&ei=UTF-8&fr=yfp-t-501&x=wrt	
	http://search.yahoo.com/search;_ylt=A0oGkmy78U1FLxUAjxlXNyoA?p=tv%20aik%20malm%C3%B6%20&ei=UTF-8&fr=yfp-t-501&x=wrt&meta=vl%3Dlang_sv&_adv_prop=web
	http://search.yahoo.com/search;_ylt=A0geuscEOl9FHH4BPnxXNyoA?p=nu%20robert%20pires&prssweb=Search&ei=UTF-8&fr=yfp-t-501&x=wrt
*/
	function referrer_yahoo_cleanup(&$query)
	{
		global $config;

		//Default value: UTF-8
		$decode_fmt = 'UTF-8';

		if (!empty($query['ei']))
		{
			switch (strtoupper($query['ei']))
			{
				case 'ISO-8859-1':	$decode_fmt = 'ISO-8859-1'; break;
				case 'UTF-8':				$decode_fmt = 'UTF-8'; break;
				case 'BIG5':				$decode_fmt = 'BIG-5'; break;
				case 'EUC-JP':			$decode_fmt = 'EUC-JP'; break;

				default:
					echo 'Unrecognized Yahoo codepage: '.strtoupper($query['ei']).'<br>';
			}
		}

		if (empty($query['p'])) return false;
		
		//if ($decode_fmt == 'EUC-JP') echo 'Orginal EUC-JP: '.$query['p'].'<br>';

		$query['p'] = trim(mb_convert_encoding($query['p'], 'UTF-8', $decode_fmt));
		
		//if ($decode_fmt == 'EUC-JP') echo 'Converted to utf8: '.$query['p'].'<br>';
		

		if ($config['lowercase_queries']) $query['p'] = mb_strtolower($query['p']);

		if (empty($query['p'])) return false;
		
		return $query['p'];
	}
	
/*
Altavista Samples:
	q parameter:
	http://se.altavista.com/web/results?itag=ody&q=itservice&kgs=1&kls=1
	http://www.altavista.com/web/results?itag=ody&q=IT Service AB&kgs=1&kls=1
				
	aqa parameter:
	http://se.altavista.com/web/results?itag=ody&dt=tmperiod&d2=0&filetype=&rc=dmn&swd=&nbq=10&pg=aq&aqmode=s&aqa=philips tv service meny&aqp=&aqo=&aqn=&kgs=0&kls=1
*/
	function referrer_altavista_cleanup(&$query)
	{
		//Note: Altavista verkar inte ha någon codepage parameter, vi konverterar ingenting (det är UTF8 i URL:en)
		global $config;

		if (!empty($query['q'])) {
			if ($config['lowercase_queries']) $query['q'] = trim(mb_strtolower($query['q']));
		} else if (!empty($query['aqa'])) {
			if ($config['lowercase_queries']) $query['q'] = trim(mb_strtolower($query['aqa']));
		}

		if (empty($query['q'])) return false;

		//echo 'Altavista search: '.$query['q'].'<br>';
		
		return $query['q'];
	}


/*
AOL Samples:
	http://www.aolrecherche.aol.fr/search?enc=iso&service=WebMondial&first=1&last=10&p=wf&query=telge nät
	http://www.aolrecherche.aol.fr/search?query=telge n?t&first=11&last=20&safe=off
	http://search.aol.com/aolcom/search?query=agent+interactive&page=2&nt=SG2_SI0&userid=3780858430765157931&encquery=4b0c56330d59bf6630d245c79c802a24bb8e8111665632cc&ie=UTF-8&invocationType=aolcomsearch&clickstreamid=3780858430765157929	
	http://aolsearch.aol.co.uk/web?query=SPORTAL&page=2&ov=1&lr=&restrict=wholeweb&topQuery=SPORTAL
*/
	function referrer_aol_cleanup(&$query)
	{
		global $config;

		//Default value: ASCII
		$decode_fmt = 'ASCII';

		//Some URL's use enc=iso to denote a UTF-8 string, while others use ie=UTF-8
		if (!empty($query['ie']))
		{
			switch (strtoupper($query['ie']))
			{
				case 'UTF-8':
					$decode_fmt = 'UTF-8';
					break;

				default:
					echo 'Unrecognized AOL "ie" codepage: '.$query['ie'].'<br>';
					break;
			}
		} else if (!empty($query['enc']))
		{
			switch (strtoupper($query['enc']))
			{
				case 'ISO':
					$decode_fmt = 'UTF-8';
					break;
				
				default:
					echo 'Unrecognized AOL "enc" codepage: '.$query['enc'].'<br>';
					break;
			}
		}
		
		$query['query'] = trim(mb_convert_encoding($query['query'], 'UTF-8', $decode_fmt));
		if ($config['lowercase_queries']) $query['query'] = mb_strtolower($query['query']);

		if (empty($query['query'])) return false;

		//echo 'AOL search (UTF8) from ('.$decode_fmt.'): '.$query['query'].'<br>';
		
		return $query['query'];
	}

/*
Eniro Samples:
	http://www.eniro.se/query?q=it service&what=se&hpp=&ax=
	http://www.eniro.se/query?stq=0&q=IT Service AB&what=se
	http://www.eniro.se/query?q=m?ster samuelsgatan it service&what=se&hpp=&ax=
	http://www.eniro.se/query?as_q=mikael%20hellstr%F6m%20hammarby%20&as_epq=&as_oq=&as_eq=&lr=&as_ft=i&as_filetype=&as_occt=any&as_dt=i&as_sitesearch=&as_qdr=all&hpp=10&safe=off&what=web&advanced=1
	
	"q" normal query
	"as_q" another form of query
*/
	function referrer_eniro_cleanup(&$query)
	{
		//Note: verkar inte finnas någon codepage parameter, vi konverterar allt till UTF8 (det är ASCII i URL:en)
		global $config;

		if (!empty($query['q'])) $query['q'] = trim($query['q']);
		if (empty($query['q'])) {
			if (!empty($query['as_q'])) $query['q'] = trim($query['as_q']);
			else return false;
		}
		
		//Converts eniro query to UTF-8 format for internal handling
		//echo 'Eniro search (ASCII): '.$query['q'].'<br>';
		$query['q'] = trim(mb_convert_encoding($query['q'], 'UTF-8', 'ASCII'));

		if ($config['lowercase_queries']) $query['q'] = mb_strtolower($query['q']);
		if (empty($query['q'])) return false;

		//echo 'Eniro search (UTF8): '.$query['q'].'<br>';
		
		return $query['q'];
	}


/*
Spray samples:

http://lycossvar.spray.se/cgi-bin/pursuit?query=jordärtskocka soppa&enc=utf-8&ca...=loc&x=13&y=6
http://lycossvar.spray.se/cgi-bin/pursuit?cat=web&query=agent interactive
http://lycossvar.spray.se/cgi-bin/pursuit?query=telge n?t&cat=web&matchmode=and&mte...loc=searchbox

enc=utf-8, annars är default ascii
*/
	function referrer_spray_cleanup(&$query)
	{
		global $config;

		//Default value: ASCII
		$decode_fmt = 'ASCII';

		if (!empty($query['enc'])) {
			switch (strtoupper($query['enc'])) {
				case 'UTF-8':
					$decode_fmt = 'UTF-8';
					break;
					
				default:
					echo '<b>ERROR - unknown spray "enc" codepage: '.$query['enc'].'</b><br>';
			}
		}


		if (!empty($query['query'])) $query['query'] = trim($query['query']);
		if (empty($query['query'])) return false;
		
		//Converts eniro query to UTF-8 format for internal handling
		//echo 'Spray search (ASCII): '.$query['query'].'<br>';
		$query['query'] = trim(mb_convert_encoding($query['query'], 'UTF-8', $decode_fmt));

		if ($config['lowercase_queries']) $query['query'] = mb_strtolower($query['query']);
		if (empty($query['query'])) return false;

		//echo 'Spray search (UTF8): '.$query['query'].'<br>';
		
		return $query['query'];
	}




/*
Sesam samples:

http://sesam.se/search/?q=brommapojkarna&newscountry=Sverige&c=m
http://www.sesam.se/search/?c=d&q=z%E4ta&abb=ab
http://www.sesam.se/search/?q=s%C3%A4llskapslekar&c=p&abb=ab
http://sesam.se/search/?q=herrg%C3%A5rd&c=d&offset=10
http://sidewalk.sesam.se/search/?newscountry=Sverige&c=m&q=hedman%20chelsea

queries är antingen ISO-8859-1 eller UTF-8, detectar format med mb_detect_encoding()
*/
	function referrer_sesam_cleanup(&$query)
	{
		global $config;

		if (!empty($query['q'])) $query['q'] = trim($query['q']);
		if (empty($query['q'])) return false;
		
		if (mb_detect_encoding($query['q'], 'UTF-8, ISO-8859-1') == 'ISO-8859-1') {		
			//Converts query to UTF-8 format for internal handling
			//echo 'Sesam search (ISO-8859-1): '.$query['q'].'<br>';
			$query['q'] = trim(mb_convert_encoding($query['q'], 'UTF-8', 'ISO-8859-1'));
		}

		if ($config['lowercase_queries']) $query['q'] = mb_strtolower($query['q']);
		if (empty($query['q'])) return false;

		//echo 'Sesam search (UTF8): '.$query['q'].'<br>';
		
		return $query['q'];
	}


	//this is the main function, takes a $list array with referrers
	//Returns an array with parsed search queries.
	//also modifies $list, adding 'search_engine', 'search_phrase' tags
	
	//todo: ta bort for-loopen i funktionen, acceptera enbart en user agent som parameter
	function parseReferrers(&$list)
	{
		$domains_google			= array(
			'google.com', 'wap.google.com', 'google.co.uk', 'google.co.il', 'google.se', 'google.no',
			'google.dk', 'google.fi', 'google.cn', 'google.lv', 'google.de', 'google.it', 'google.com.sv',
			'google.com.tr', 'google.fr', 'google.co.ve', 'google.sk', 'google.com.sg', 'google.com.au',
			'google.nu', 'google.nl', 'google.ru', 'google.lt', 'google.si', 'google.com.tw', 'google.co.ma',
			'google.co.in', 'google.cl', 'google.co.th', 'google.ch', 'google.is', 'google.pl', 'google.es',
			'google.be', 'google.ro', 'google.co.jp', 'google.com.ar', 'google.com.co', 'google.com.mx',
			'google.com.br', 'google.hu', 'google.ca', 'google.ee', 'google.hr', 'google.co.id', 'google.gr',
			'google.mn', 'google.ae', 'google.cz', 'google.ba', 'google.bg', 'google.com.vn', 'google.com.ua',
			'google.com.mt', 'google.com.hk', 'google.at', 'google.com.sa', 'google.jo', 'google.com.ph', 'google.pt'
		);
		$paths_google				= array('/search', '/ie', '/custom', '/xhtml', '/xhtml/search', '/hws/search');


		$domains_msn				= array(
			'search.msn.com', 'search.live.com',
			'search.msn.se', 'search.msn.no', 'search.msn.dk', 'search.msn.fi', 'search.msn.co.jp', 'search.msn.ch',
			'search.msn.fr', 'search.msn.nl', 'search.msn.de', 'search.msn.it'
		);
		$paths_msn					= array('/results.aspx', '/results.asp', '/spresults.aspx', '/previewx.aspx');

		$domains_yahoo			= array(
			'search.yahoo.com', 'cade.search.yahoo.com', 'search.yahoo.co.jp',
			'se.search.yahoo.com', 'au.search.yahoo.com', 'br.search.yahoo.com',
			'tw.search.yahoo.com', 'uk.search.yahoo.com', 'hk.search.yahoo.com',
			'fr.search.yahoo.com', 'it.search.yahoo.com', 'de.search.yahoo.com',
			'nl.search.yahoo.com', 'ca.search.yahoo.com', 'ar.search.yahoo.com',
			'ru.search.yahoo.com', 'us.search.yahoo.com'
		);
		$paths_yahoo				= array('/search', '/search/msie', '/bin/search');

		$domains_altavista	= array('altavista.com', 'se.altavista.com');
		$paths_altavista		= array('/web/results');

		$domains_aol				= array(
			'search.aol.com', 'aolsearch.aol.co.uk',
			'aolrecherche.aol.fr'
		);
		$paths_aol					= array('/aolcom/search', '/search', '/web');

		$domains_eniro			= array('eniro.se');
		$paths_eniro				= array('/query');

		$domains_spray			= array('lycossvar.spray.se');
		$paths_spray				= array('/cgi-bin/pursuit');
		
		$domains_sesam			= array('sesam.se', 'sidewalk.sesam.se', 'sesam.no');
		$paths_sesam				= array('/search/');
	
		$search['engines']['google'] = 0;
		$search['engines']['msn'] = 0;
		$search['engines']['yahoo'] = 0;
		$search['engines']['altavista'] = 0;
		$search['engines']['aol'] = 0;
		$search['engines']['eniro'] = 0;
		$search['engines']['spray'] = 0;
		$search['engines']['sesam'] = 0;
		
		$search['engine_cnt'] = 0;
		$search['referrer_cnt'] = 0;
		
		$search['queries'] = array();
		
		//extrahera sökmotor-queries från referrer-datan:
		for ($i=0; $i<count($list); $i++) {
			$search['referrer_cnt'] += $list[$i]['cnt'];
			
			$list[$i]['search_engine'] = '';
	
			//1. splitta upp URL'en
			if (empty($list[$i]['referrer'])) continue;
			$url = parse_url($list[$i]['referrer']);
			if (empty($url['host'])) continue;
			
			//2. Konverterar www.google.com till google.com
			//	Eller ww.google.se till google.se (ww.google.se fungerar faktiskt)
			if (substr($url['host'], 0, 3) == 'ww.') $url['host'] = substr($url['host'], 3);
			if (substr($url['host'], 0, 4) == 'www.') $url['host'] = substr($url['host'], 4);
	
			//3. Parse the URL query
			if (empty($url['query'])) continue;
			parse_str($url['query'], $query);	//important: magic_quotes_gpc must be turned OFF! or else "phrase" will show up as \"phrase\"
			
			/*	4. Parse Google searches 	*/
			if (in_array($url['host'], $domains_google) && in_array($url['path'], $paths_google)) {
				$q = referrer_google_cleanup($query);
	
				if ($q !== false) {
					$list[$i]['search_engine'] = 'Google';
					$list[$i]['search_query']  = $q;
	
					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['google'])) $search['queries'][ $q ]['google'] = 0;
	
					//Counters
					$search['queries'][ $query['q'] ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $query['q'] ]['google'] += $list[$i]['cnt'];
	
					$search['engines']['google'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}
			
			/*	5. Parse MSN searches		*/
			if (in_array($url['host'], $domains_msn) && in_array($url['path'], $paths_msn)) {
				$q = referrer_msn_cleanup($query);
				
				if ($q !== false)
				{
					//echo 'MSN search CP decoded from '.$decode_fmt.': '.$query['q'].'<br>';		
					$list[$i]['search_engine'] = 'MSN';
					$list[$i]['search_query']  = $q;

					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['msn'])) $search['queries'][ $q ]['msn'] = 0;
	
					//Counters
					$search['queries'][ $query['q'] ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $query['q'] ]['msn'] += $list[$i]['cnt'];
	
					$search['engines']['msn'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}
	
			/*	6. Parse Yahoo searches		*/
			if (in_array($url['host'], $domains_yahoo)) {
				
				//Special case: Script name has unique ID:
				//http://search.yahoo.com/search;_ylt=A0geuscEOl9FHH4BPnxXNyoA?p=nu%20robert%20pires&prssweb=Search&ei=UTF-8&fr=yfp-t-501&x=wrt
				if (strpos($url['path'], '/search;') === false && !in_array($url['path'], $paths_yahoo)) {
					continue;
				}

				$q = referrer_yahoo_cleanup($query);
	
				if ($q !== false)
				{
					//echo 'Yahoo decoded query (from '.$decode_fmt.'): '.$query['p'].'<br>';
					$list[$i]['search_engine'] = 'Yahoo';
					$list[$i]['search_query']  = $q;
	
					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['yahoo'])) $search['queries'][ $q ]['yahoo'] = 0;
	
					$search['queries'][ $query['p'] ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $query['p'] ]['yahoo'] += $list[$i]['cnt'];
	
					$search['engines']['yahoo'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}
	
			/* 7. Parse Altavista searches		*/
			if (in_array($url['host'], $domains_altavista) && in_array($url['path'], $paths_altavista)) {
				$q = referrer_altavista_cleanup($query);
	
				if ($q !== false)
				{
					$list[$i]['search_engine'] = 'Altavista';
					$list[$i]['search_query']  = $q;
			
					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['altavista'])) $search['queries'][ $q ]['altavista'] = 0;
	
					$search['queries'][ $q ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $q ]['altavista'] += $list[$i]['cnt'];
	
					$search['engines']['altavista'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}
			
			/* 8. Parse AOL searches */
			if (in_array($url['host'], $domains_aol) && in_array($url['path'], $paths_aol)) {
				//echo 'aol query: '.$list[$i]['referrer'].'<br>';
				$q = referrer_aol_cleanup($query);
	
				if ($q !== false)
				{
					$list[$i]['search_engine'] = 'AOL';
					$list[$i]['search_query']  = $q;

					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['aol'])) $search['queries'][ $q ]['aol'] = 0;
	
					$search['queries'][ $q ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $q ]['aol'] += $list[$i]['cnt'];
	
					$search['engines']['aol'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}
	
			/* 9. Parse Eniro searches	*/
			if (in_array($url['host'], $domains_eniro) && in_array($url['path'], $paths_eniro)) {
				//echo 'Eniro query: '.$list[$i]['referrer'].'<br>';
				$q = referrer_eniro_cleanup($query);
	
				if ($q !== false)
				{
					$list[$i]['search_engine'] = 'Eniro';
					$list[$i]['search_query']  = $q;
	
					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['eniro'])) $search['queries'][ $q ]['eniro'] = 0;
	
					$search['queries'][ $q ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $q ]['eniro'] += $list[$i]['cnt'];
	
					$search['engines']['eniro'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}

			/* 10. Parse Spray (lycos) searches	*/
			if (in_array($url['host'], $domains_spray) && in_array($url['path'], $paths_spray)) {
				//echo 'Spray query: '.$list[$i]['referrer'].'<br>';
				$q = referrer_spray_cleanup($query);
	
				if ($q !== false)
				{
					$list[$i]['search_engine'] = 'Spray';
					$list[$i]['search_query']  = $q;
	
					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['spray'])) $search['queries'][ $q ]['spray'] = 0;
	
					$search['queries'][ $q ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $q ]['spray'] += $list[$i]['cnt'];
	
					$search['engines']['spray'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}


			/* 11. Parse Sesam searches	*/
			if (in_array($url['host'], $domains_sesam) && in_array($url['path'], $paths_sesam)) {
				//echo 'Sesam query: '.$list[$i]['referrer'].'<br>';
				$q = referrer_sesam_cleanup($query);
	
				if ($q !== false)
				{
					$list[$i]['search_engine'] = 'Sesam';
					$list[$i]['search_query']  = $q;
	
					if (empty($search['queries'][ $q ])) $search['queries'][ $q ] = array();
					if (empty($search['queries'][ $q ]['cnt'])) $search['queries'][ $q ]['cnt'] = 0;
					if (empty($search['queries'][ $q ]['sesam'])) $search['queries'][ $q ]['sesam'] = 0;
	
					$search['queries'][ $q ]['cnt'] += $list[$i]['cnt'];
					$search['queries'][ $q ]['sesam'] += $list[$i]['cnt'];
	
					$search['engines']['sesam'] += $list[$i]['cnt'];
					$search['engine_cnt'] += $list[$i]['cnt'];
				}
	
				continue;
			}


		}
		
		//Sort the results
		arsort($search['engines']);	//sort search engine popularity, descending

		$search['queries'] = aRSortBySecondIndex($search['queries'], 'cnt');	//sort query popularity, descending
		
		return $search;
	}
?>