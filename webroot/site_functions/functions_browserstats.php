<?
	$web_crawlers = array(
		'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
		'Mediapartners-Google/2.1',
		'Googlebot/2.1 (+http://www.google.com/bot.html)',

		'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',

		'msnbot/1.0 (+http://search.msn.com/msnbot.htm)',
		'msnbot-media/1.0 (+http://search.msn.com/msnbot.htm)',

		'SUNET WWW Index check program',
		'proodleBot (www.proodle.com)',
		'ia_archiver',
		'Jyxobot/1',
		'Gigabot/2.0 (http://www.gigablast.com/spider.html)',
		'findlinks/1.1.3-beta9 (+http://wortschatz.uni-leipzig.de/findlinks/)',
		'findlinks/1.1.4-beta1 (+http://wortschatz.uni-leipzig.de/findlinks/)',
		'Shim-Crawler(Mozilla-compatible; http://www.logos.ic.i.u-tokyo.ac.jp/crawler/; crawl@logos.ic.i.u-tokyo.ac.jp)',
		'CJNetworkQuality; http://www.cj.com/networkquality',
		'oegp v. 1.3.0',
		'Pingdom GIGRIB v1.1 (http://www.pingdom.com)',
		'Mozilla/4.0 compatible ZyBorg/1.0 (wn-14.zyborg@looksmart.net; http://www.WISEnutbot.com)',
		'Mozilla/5.0 (compatible; heritrix/1.8.0 +http://crawlerx51.com)',
		'psbot/0.1 (+http://www.picsearch.com/bot.html)',
		'sproose/1.0beta (sproose bot; http://www.sproose.com/bot.html; crawler@sproose.com)',
		'ichiro/2.0 (http://help.goo.ne.jp/door/crawler.html)',
		'PlantyNet_WebRobot_V1.9 dhkang@plantynet.com',
		'Mozilla/3.0 (compatible; ScollSpider; http://www.webwobot.com)',
		'Mozilla/4.0 compatible ZyBorg/1.0 Dead Link Checker (wn.dlc@looksmart.net; http://www.WISEnutbot.com)',
		'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
		'schibstedsokbot (compatible; Mozilla/5.0; MSIE 5.0; FAST FreshCrawler 6; +http://www.schibstedsok.no/bot/)',
		'MJ12bot/v1.1.0 (http://majestic12.co.uk/bot.php?+)',
		'MJ12bot/v1.1.1 (http://majestic12.co.uk/bot.php?+)',
		'Mozilla/4.0 (compatible; NaverBot/1.0; http://help.naver.com/delete_main.asp)',
		'Exabot/3.0',
		'miniRank/2.0 (miniRank; http://minirank.com/; website ranking engine)',
		'Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://about.ask.com/en/docs/about/webmasters.shtml)',

		'PycURL/7.15.5',
		'Python-urllib/1.16',
		'Python-urllib/2.1',
		'Java/1.4.1_02',
		'Java/1.4.1_04',
		'Java/1.4.2_01',
		'Java/1.4.2_03',
		'Java/1.4.2_12',
		'libwww-perl/5.65',
		'libwww-perl/5.805',
		
		"<a href=\'http://www.netforex.org\'> Forex Trading Network Organization </a> info@netforex.org"
	);


	/* Extracts browser info from $_SERVER['HTTP_USER_AGENT'] */
	//todo: remove $db parameter its not used
	function GetBrowser($user_agent = '')
	{
		//Good user-agent resource: http://en.wikipedia.org/wiki/User_agent
		global $config, $web_crawlers;
		
		if (!$user_agent) $user_agent = $_SERVER['HTTP_USER_AGENT'];

		$browser['name'] = '';
		$browser['version'] = '';
		$browser['OS'] = '';
		$browser['platform'] = '';
		$browser['fully_detected'] = true;

		$browser['width'] = intval(ReadCookie('BrowserWidth', 0));
		$browser['height'] = intval(ReadCookie('BrowserHeight', 0));
		if ($browser['width']) DeleteCookie('BrowserWidth');
		if ($browser['height']) DeleteCookie('BrowserHeight');

		if (empty($user_agent)) return $browser;
		
		
		/* Web crawler detection */
		if (in_array($user_agent, $web_crawlers)) {
			$browser['OS'] = 'Other';
			$browser['platform'] = 'Other';
			$browser['name'] = 'Web crawler';
			$browser['version'] = 'bot ?.??';

			return $browser;
		}

		/* OS detection */
		if (strpos($user_agent, 'Windows 95'))			{ $browser['OS'] = 'Windows 95';					$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'Win95'))						{ $browser['OS'] = 'Windows 95';					$browser['platform'] = 'x86'; }	//Example: Mozilla/5.0 (Windows; U; Win95; en-US; rv:1.8.0.7) Gecko/20060909 Firefox/1.5.0.7

		if (strpos($user_agent, 'Win98'))						{ $browser['OS'] = 'Windows 98';					$browser['platform'] = 'x86'; }	//Used by Firefox
		if (strpos($user_agent, 'Windows 98'))			{ $browser['OS'] = 'Windows 98';					$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'Win 9x 4.90'))			{ $browser['OS'] = 'Windows 98';					$browser['platform'] = 'x86'; }	//Windows ME? Example: Mozilla/5.0 (Windows; U; Win 9x 4.90; en-US; rv:1.8.0.5) Gecko/20060719 Firefox/1.5.0.5

		if (strpos($user_agent, 'WinNT'))						{ $browser['OS'] = 'Windows NT';					$browser['platform'] = 'x86'; }	//Example: Mozilla/4.5 [en] (WinNT; I)
		if (strpos($user_agent, 'WinNT4.0'))				{ $browser['OS'] = 'Windows NT';					$browser['platform'] = 'x86'; }	//Example: Mozilla/5.0 (Windows; U; WinNT4.0; sv-SE; rv:1.8.0.6) Gecko/20060728 Firefox/1.5.0.6
		if (strpos($user_agent, 'Windows NT'))			{ $browser['OS'] = 'Windows NT';					$browser['platform'] = 'x86'; }	//Example: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT; Posten Sverige AB F211)
		if (strpos($user_agent, 'Windows NT 4.0'))	{ $browser['OS'] = 'Windows NT';					$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'Windows NT 5.0'))	{ $browser['OS'] = 'Windows 2000';				$browser['platform'] = 'x86'; }
		
		if (strpos($user_agent, 'Windows ME'))			{ $browser['OS'] = 'Windows ME';					$browser['platform'] = 'x86'; }	//Example: Mozilla/4.0 (compatible; MSIE 6.0; Windows ME; sv) Opera 8.53

		if (strpos($user_agent, 'Windows XP'))			{ $browser['OS'] = 'Windows XP';					$browser['platform'] = 'x86'; }	//Example: Mozilla/4.0 (compatible; MSIE 5.0; Windows XP) Opera 6.05 [sv]
		if (strpos($user_agent, 'Windows NT 5.1'))	{ $browser['OS'] = 'Windows XP';					$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'Windows NT 5.2'))	{ $browser['OS'] = 'Windows Server 2003';	$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'Windows NT 6.0'))	{ $browser['OS'] = 'Windows Vista';				$browser['platform'] = 'x86'; }

		if (strpos($user_agent, 'Macintosh'))				{ $browser['OS'] = 'Mac OS';		$browser['platform'] = 'PPC'; }	//Example: Mozilla/5.0 (Macintosh; U; PPC; en-US; rv:1.0.2) Gecko/20021216
		if (strpos($user_agent, 'Mac_PowerPC'))			{ $browser['OS'] = 'Mac OS';		$browser['platform'] = 'PPC'; }	//Example: Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)
		if (strpos($user_agent, 'PPC Mac OS X'))		{ $browser['OS'] = 'Mac OS X';	$browser['platform'] = 'PPC'; }
		if (strpos($user_agent, 'Intel Mac OS X'))	{ $browser['OS'] = 'Mac OS X';	$browser['platform'] = 'x86'; }

		if (strpos($user_agent, 'Linux'))						{ $browser['OS'] = 'Linux';			$browser['platform'] = 'Unknown'; }
		if (strpos($user_agent, 'Linux i686'))			{ $browser['OS'] = 'Linux';			$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'OpenBSD i386'))		{ $browser['OS'] = 'OpenBSD';		$browser['platform'] = 'x86'; }	//Example: Mozilla/5.0 (X11; U; OpenBSD i386; en-US; rv:1.8.0.4) Gecko/20060605 Firefox/1.5.0.4

		if (strpos($user_agent, 'OpenBSD amd64'))		{ $browser['OS'] = 'OpenBSD';		$browser['platform'] = 'x64'; }
		if (strpos($user_agent, 'NetBSD i386'))			{ $browser['OS'] = 'NetBSD';		$browser['platform'] = 'x86'; }	//Example: Mozilla/5.0 (X11; U; NetBSD i386; en-US; rv:1.8) Gecko/20060103 Firefox/1.5

		if (strpos($user_agent, 'FreeBSD i386'))		{ $browser['OS'] = 'FreeBSD';		$browser['platform'] = 'x86'; }
		if (strpos($user_agent, 'FreeBSD 5 i386'))	{ $browser['OS'] = 'FreeBSD';		$browser['platform'] = 'x86'; }	//Example: Opera/9.02 (X11; FreeBSD 5 i386; U; en)
		if (strpos($user_agent, 'FreeBSD 6 i386'))	{ $browser['OS'] = 'FreeBSD';		$browser['platform'] = 'x86'; }	//Example: Opera/9.02 (X11; FreeBSD 6 i386; U; en)

		if (strpos($user_agent, 'FreeBSD amd64'))		{ $browser['OS'] = 'FreeBSD';		$browser['platform'] = 'x64'; }	//Example: Mozilla/5.0 (X11; U; FreeBSD amd64; en-US; rv:1.8.0.3) Gecko/20060610 Firefox/1.5.0.3

		if (strpos($user_agent, 'BeOS BePC'))				{ $browser['OS'] = 'BeOS';			$browser['platform'] = 'x86'; }	//Example: Mozilla/5.0 (BeOS; U; BeOS BePC; en-US; rv:1.9a1) Gecko/20060627 Minefield/3.0a1

		if (strpos($user_agent, 'SunOS'))						{ $browser['OS'] = 'SunOS';			$browser['platform'] = 'Unknown'; }	//Opera/9.00 (X11; SunOS sun4u; U; en)

		//Cellphones etc:
		if (strpos($user_agent, 'Windows CE'))			{ $browser['OS'] = 'Windows CE';$browser['platform'] = 'Windows CE'; }	//Example: Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320)
		if (strpos($user_agent, 'SymbianOS'))				{ $browser['OS'] = 'Symbian OS';$browser['platform'] = 'Symbian'; }	//Example: Mozilla/5.0 (SymbianOS/9.1; U; en-us) AppleWebKit/413 (KHTML, like Gecko) Safari/413
		if (strpos($user_agent, 'Symbian OS'))			{ $browser['OS'] = 'Symbian OS';$browser['platform'] = 'Symbian'; }
		if (strpos($user_agent, 'J2ME/MIDP'))				{ $browser['OS'] = 'Java ME';		$browser['platform'] = 'Java ME'; }	//Example: Opera/8.01 (J2ME/MIDP; Opera Mini/2.0.4509; sv; U; ssr)
		if (strpos($user_agent, 'ARM7+ARM9-DS'))		{ $browser['OS'] = 'Nintendo DS';$browser['platform'] = 'Nintendo DS'; }	//Example: Mozilla/5.0 (Nintendo; ARM7+ARM9-DS; en-US) Gecko/20060426 Firefox/1.5.0.3


		/* Browser detection */
		if (strpos($user_agent, 'Firefox') !== FALSE)
		{
			//Firefox 1.0.7		= Mozilla/5.0 (Windows; U; Windows NT 5.1; sv-SE; rv:1.7.12) Gecko/20050919 Firefox/1.0.7
			//Firefox 1.5.0.1	=	Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1
			//Firefox 1.5.0.4 = Mozilla/5.0 (Windows; U; Windows NT 5.1; sv-SE; rv:1.8.0.4) Gecko/20060508 Firefox/1.5.0.4
			//Firefox 1.5.0.4	= Mozilla/5.0 (Windows; U; Win98; sv-SE; rv:1.8.0.4) Gecko/20060508 Firefox/1.5.0.4
			//Firefox 1.5.0.4 = Mozilla/5.0 (X11; U; Linux i686; sv-SE; rv:1.8.0.4) Gecko/20060608 Ubuntu/dapper-security Firefox/1.5.0.4
			//Firefox 2.0b1		= Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1b1) Gecko/20060710 Firefox/2.0b1
			$browser['name'] = 'Firefox';

			//Cut out version number from 'Firefox/1.5.0.1'. Should work with all versions of Firefox
			$pos = strpos($user_agent, 'Firefox/');
			if ($pos !== FALSE) $browser['version'] = trim(substr($user_agent, $pos + strlen('Firefox/')));
			$pos = strpos($browser['version'], ' ');
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	//Removes ending ' (ax)' if exists

		} else if (strpos($user_agent, 'Netscape') !== FALSE)
		{
			//Netscape 6.2.3	= Mozilla/5.0 (Macintosh; U; PPC; en-US; rv:0.9.4.1) Gecko/20020508 Netscape6/6.2.3
			//Netscape 7.2		= Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.2) Gecko/20040804 Netscape/7.2 (ax)
			$browser['name'] = 'Netscape';
			
			//Cut out version number from 'Netscape/7.2 (ax)'
			$pos = strpos($user_agent, 'Netscape/');
			if ($pos !== FALSE) {
				$browser['version'] = trim(substr($user_agent, $pos + strlen('Netscape/')));
			} else {
				//Detects Netscape 6 version format
				$pos = strpos($user_agent, 'Netscape6/');
				if ($pos !== FALSE) $browser['version'] = trim(substr($user_agent, $pos + strlen('Netscape6/')));
			}

			if ($pos !== FALSE) {
				$pos = strpos($browser['version'], ' ');
				if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	//Removes ending ' (ax)' if exists
			}

		} else if (strpos($user_agent, 'Konqueror') !== FALSE)
		{
			//Konqueror 3.0		= Mozilla/5.0 (compatible; Konqueror/3.0)
			//Konqueror 3.4		= Mozilla/5.0 (compatible; Konqueror/3.4; Linux) KHTML/3.4.2 (like Gecko)
			$browser['name'] = 'Konqueror';

			//Cut out version number from 'Konqueror/3.4;'
			$pos = strpos($user_agent, 'Konqueror/');
			if ($pos !== FALSE) $browser['version'] = trim(substr($user_agent, $pos + strlen('Konqueror/')));
			$pos = strpos($browser['version'], ';');	//Removes everything after ';' if exists
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	

			$pos = strpos($browser['version'], ')');	//Removes everything after ')' if exists
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));

		} else if (strpos($user_agent, 'Camino') !== FALSE)
		{
			//Camino 1.0.2		= Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.0.4) Gecko/20060613 Camino/1.0.2 (MultiLang)
			$browser['name'] = 'Camino';

			//Cut out version number from 'Camino/1.0.2 (MultiLang)'
			$pos = strpos($user_agent, 'Camino/');
			if ($pos !== FALSE) $browser['version'] = trim(substr($user_agent, $pos + strlen('Camino/')));
			$pos = strpos($browser['version'], ' ');
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	//Removes everything after ' ' if exists

		} else if (strpos($user_agent, 'Opera Mini') !== FALSE)
		{
			//Pocket PC browser
			//Opera Mini 1.1.2421	= Opera/8.01 (J2ME/MIDP; Opera Mini/1.1.2421/hifi/nordic/se; SonyEricsson K600i; sv; U; ssr)
			//Opera Mini 2.0.4509	= Opera/8.01 (J2ME/MIDP; Opera Mini/2.0.4509; sv; U; ssr)
			$browser['name'] = 'Opera Mini';

			//Cut out version number from 'Opera Mini/2.0.4509;'
			$pos = strpos($user_agent, 'Opera Mini/');
			if ($pos !== FALSE) $browser['version'] = trim(substr($user_agent, $pos + strlen('Opera Mini/')));

			$pos = strpos($browser['version'], '/');
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	//Removes everything after '/' if exists

			$pos = strpos($browser['version'], ';');
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	//Removes everything after ';' if exists

		} else if (strpos($user_agent, 'Opera') !== FALSE) //must happen before MSIE detection, else they are identified as MSIE-browsers
		{
			//Note: I think older versions of opera identified themselves as MSIE6, but who cares about old browsers
			//Opera 7.20			= Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0) Opera 7.20 [en]
			//Opera 8.51			= Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; en) Opera 8.51
			//Opera 8.60(symb)= Mozilla/4.0 (compatible; MSIE 6.0; Symbian OS; 246) Opera 8.60 [sv]SonyEricssonM600i/R100
			//Opera 9.00			= Opera/9.00 (Windows NT 5.1; U; en)
			$browser['name'] = 'Opera';

			//Cut out version number from 'Opera 8.51'. Should work with all 'recent' versions of Opera
			$pos = strpos($user_agent, 'Opera ');											//Handle "Opera 8.51" format
			if ($pos === FALSE) $pos = strpos($user_agent, 'Opera/');	//Handle "Opera/9.00" format

			if ($pos !== FALSE) {
				$browser['version'] = trim(substr($user_agent, $pos + strlen('Opera ')));
				$pos = strpos($browser['version'], ' ');
				if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));		//cut out everything after the first whitespace
			}

		} else if (strpos($user_agent, 'MSIE') !== FALSE)
		{
			//IE 4.01					= Mozilla/4.0 (compatible; MSIE 4.01; Windows 98)
			//IE 5.0					= Mozilla/4.0 (compatible; MSIE 5.0; Windows 95; DigExt; Hotbar 2.0)
			//IE 5.01					= Mozilla/4.0 (compatible; MSIE 5.01; Windows NT; Posten Sverige AB F211)
			//IE 5.12 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.12; Mac_PowerPC)
			//IE 5.13 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.13; Mac_PowerPC)
			//IE 5.14 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.14; Mac_PowerPC)
			//IE 5.15 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.15; Mac_PowerPC)
			//IE 5.16 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.16; Mac_PowerPC)
			//IE 5.17 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.17; Mac_PowerPC)
			//IE 5.2 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.2; Mac_PowerPC)
			//IE 5.21 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.21; Mac_PowerPC)
			//IE 5.22 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.22; Mac_PowerPC)
			//IE 5.23 (Mac)		= Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)
			//IE 5.5					= Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Win 9x 4.90)
			//IE 6.0 (win98)	= Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)
			//IE 6.0					=	Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)
			//IE 7.0 PB2			= Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)
			$browser['name'] = 'Internet Explorer';

			if (strpos($user_agent, 'MSIE 4.01'))	$browser['version'] = '4.01';
			if (strpos($user_agent, 'MSIE 5.0'))	$browser['version'] = '5.0';
			if (strpos($user_agent, 'MSIE 5.01'))	$browser['version'] = '5.01';
			if (strpos($user_agent, 'MSIE 5.12')) $browser['version'] = '5.12';	//Mac only
			if (strpos($user_agent, 'MSIE 5.13')) $browser['version'] = '5.13';	//Mac only
			if (strpos($user_agent, 'MSIE 5.14')) $browser['version'] = '5.14';	//Mac only
			if (strpos($user_agent, 'MSIE 5.15')) $browser['version'] = '5.15';	//Mac only
			if (strpos($user_agent, 'MSIE 5.16')) $browser['version'] = '5.16';	//Mac only
			if (strpos($user_agent, 'MSIE 5.17')) $browser['version'] = '5.17';	//Mac only
			if (strpos($user_agent, 'MSIE 5.2;')) $browser['version'] = '5.20';	//Mac only
			if (strpos($user_agent, 'MSIE 5.21')) $browser['version'] = '5.21';	//Mac only
			if (strpos($user_agent, 'MSIE 5.22')) $browser['version'] = '5.22';	//Mac only
			if (strpos($user_agent, 'MSIE 5.23')) $browser['version'] = '5.23';	//Mac only
			if (strpos($user_agent, 'MSIE 5.5'))	$browser['version'] = '5.5';
			if (strpos($user_agent, 'MSIE 6.0'))	$browser['version'] = '6.0';
			if (strpos($user_agent, 'MSIE 7.0'))	$browser['version'] = '7.0';

		} else if (strpos($user_agent, 'Safari') !== FALSE)
		{
			//Maps Safari build number to Safari version number according to this document:
			//http://developer.apple.com/internet/safari/uamatrix.html

			//Safari 1.0			= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/85.8.5 (KHTML, like Gecko) Safari/85
			//Safari 1.0.3		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/85.8.5 (KHTML, like Gecko) Safari/85.8.1
			//Safari 1.1			= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/103u (KHTML, like Gecko) Safari/100
			//Safari 1.2.2		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/312.8 (KHTML, like Gecko) Safari/125.7
			//Safari 1.2.2		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/125.2 (KHTML, like Gecko) Safari/125.8
			//Safari 1.2.3		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/125.4 (KHTML, like Gecko) Safari/125.9
			//Safari 1.2.4		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/125.5.6 (KHTML, like Gecko) Safari/125.12
			//Safari 1.3			= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/312.1 (KHTML, like Gecko) Safari/312
			//Safari 1.3.1		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/312.5 (KHTML, like Gecko) Safari/312.3
			//Safari 1.3.1		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/312.5.1 (KHTML, like Gecko) Safari/312.3.1
			//Safari 1.3.2		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/312.8 (KHTML, like Gecko) Safari/312.6
			//Safari 2.0			= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/412 (KHTML, like Gecko) Safari/412
			//Safari 2.0.2		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/416.11 (KHTML, like Gecko) Safari/416.12
			//Safari 2.0.2		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; nb-no) AppleWebKit/416.12 (KHTML, like Gecko) Safari/416.13
			//Safari 2.0.3		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/417.9 (KHTML, like Gecko) Safari/417.8			
			//Safari 2.0.3		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/418 (KHTML, like Gecko) Safari/417.9.2
			//Safari 2.0.3		= Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/418 (KHTML, like Gecko) Safari/417.9.3
			//Safari 2.0.4		= Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en) AppleWebKit/418.8 (KHTML, like Gecko) Safari/419.3
			//-----------
			/*
cp = Mozilla/5.0 (Macintosh; U; PPC Mac OS X; sv-se) AppleWebKit/312.8 (KHTML, like Gecko) Safari/125
cp	= Mozilla/5.0 (SymbianOS/9.1; U; en-us) AppleWebKit/413 (KHTML, like Gecko) Safari/413
			*/
			$browser['name'] = 'Safari';
			
			//Jaguar - Mac OS 10.2.x
			if (strpos($user_agent, 'Safari/85'))			$browser['version'] = '1.0';
			if (strpos($user_agent, 'Safari/85.8.1'))	$browser['version'] = '1.0.3';
			
			//Panther - Mac OS 10.3.x
			if (strpos($user_agent, 'Safari/100'))		$browser['version'] = '1.1';
			if (strpos($user_agent, 'Safari/125.7'))	$browser['version'] = '1.2.2';
			if (strpos($user_agent, 'Safari/125.8'))	$browser['version'] = '1.2.2';
			if (strpos($user_agent, 'Safari/125.9'))	$browser['version'] = '1.2.3';
			if (strpos($user_agent, 'Safari/125.12'))	$browser['version'] = '1.2.4';
			if (strpos($user_agent, 'Safari/312'))		$browser['version'] = '1.3';
			if (strpos($user_agent, 'Safari/312.3'))	$browser['version'] = '1.3.1';
			if (strpos($user_agent, 'Safari/312.3.1'))$browser['version'] = '1.3.1';
			if (strpos($user_agent, 'Safari/312.6'))	$browser['version'] = '1.3.2';

			//Tiger - Mac OS 10.4.x
			if (strpos($user_agent, 'Safari/412'))			$browser['version'] = '2.0';
			if (strpos($user_agent, 'Safari/416.12'))		$browser['version'] = '2.0.2';
			if (strpos($user_agent, 'Safari/416.13'))		$browser['version'] = '2.0.2';
			if (strpos($user_agent, 'Safari/417.8'))		$browser['version'] = '2.0.3';
			if (strpos($user_agent, 'Safari/417.9.2'))	$browser['version'] = '2.0.3';
			if (strpos($user_agent, 'Safari/417.9.3'))	$browser['version'] = '2.0.3';
			if (strpos($user_agent, 'Safari/419.3'))		$browser['version'] = '2.0.4';

		} else if (strpos($user_agent, 'Mozilla') !== FALSE)
		{
			//Old mozilla User Agent strings: http://www.pgts.com.au/pgtsj/pgtsj0208k.html
			//Mozilla 1.0.2		= Mozilla/5.0 (Macintosh; U; PPC; en-US; rv:1.0.2) Gecko/20021216
			//Mozilla 1.7.10	= Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.10) Gecko/20050716
			//Mozilla 1.7.12	= Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915
			//Mozilla 1.7.13	= Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.13) Gecko/20060414
			$browser['name'] = 'Mozilla';
			
			//Cuts out version number from "rv:1.7.13"
			$pos = strpos($user_agent, 'rv:');
			if ($pos !== FALSE) $browser['version'] = trim(substr($user_agent, $pos + strlen('rv:')));
			$pos = strpos($browser['version'], ')');
			if ($pos !== FALSE) $browser['version'] = trim(substr($browser['version'], 0, $pos));	//Removes everything after ')' if exists
		}


		if (!$browser['name'] || !$browser['version'] || !$browser['OS'] || !$browser['platform'])
		{
			if (!$browser['name'])			$browser['name'] = 'Unknown browser';
			if (!$browser['version'])		$browser['version'] = '?.?';
			if (!$browser['OS'])				$browser['OS'] = 'Unknown OS';
			if (!$browser['platform'])	$browser['platform'] = 'Unknown platform';

			$browser['fully_detected'] = false;
		}
		
		//Format version numbers ending: .00 -> .0
		//Fix because some Opera's report version 9.0, while some report 9.00
		if (substr($browser['version'], -3) == '.00') $browser['version'] = substr($browser['version'], 0, -1);

		return $browser;
	}

	function FormatBrowserInfo(&$db, $user_agent)
	{
		$browser = GetBrowser($user_agent);
		
		$browser_name = $browser['name'];
		if ($browser_name == 'Internet Explorer') $browser_name = 'MSIE';
		if ($browser_name == 'Opera Mini') $browser_name = 'Opera';
		
		$logo = 'browser_'.$browser_name.'.png';

		$string = '<img src="design/'.$logo.'" width=16 height=16 alt="'.$browser['name'].' '.$browser['version'].'" title="'.$browser['name'].' '.$browser['version'].'" align="top">';

		return $string;
	}

	/* Returns the preferred locale according to the browser, or IP orgin */
	//fixme: kolla även om det finns en cookie lagrad med locale
	function GetPreferredLanguage(&$db)
	{
		if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return '';
		
		$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);			//Syntax: da, en-gb;q=0.8, en;q=0.7

		for ($i=0; $i<count($langs); $i++) {
			//turn 'en-gb;q=0.8' into 'en-gb':
			$pos = strpos($langs[$i], ';');
			if ($pos !== FALSE) $langs[$i] = substr($langs[$i], 0, $pos);

			/* Reduce locales */
			$langs[$i] = trim(strtolower($langs[$i]));
			if ($langs[$i] == 'en-gb') $langs[$i] = 'en';
			if ($langs[$i] == 'en-us') $langs[$i] = 'en';
		}
		//Remove duplicate entries from array
		$langs = array_unique($langs);

		$browser_prefer_language = $langs[0];	//The first entry has highest priority
		$ip_cc = strtolower(getGeoIPCountryShort($_SERVER['REMOTE_ADDR']));	//fixme: kolla upp detta vid login

		//fixme: lägg till locale-koder i country-tablen
		if ($ip_cc == 'se') $ip_cc = 'sv';
		if ($ip_cc == 'us') $ip_cc = 'en';
		if ($ip_cc == 'uk') $ip_cc = 'en';

		if ($ip_cc != $browser_prefer_language)
		{
			/* IP orgin have higest priority in auto detection */
			//logEntry($db, 'GetPreferredLanguage() WARNING! Browser prefers "'.$browser_prefer_language.'", but IP suggests "'.$ip_cc.'"');
			return $ip_cc;
		}

		return $browser_prefer_language;
	}

	function parseBrowserStats($list)
	{
		//fixme: ta bort hela 'name' arrayen ??

		if (!$list) return array();

		$browser_stats = array();
		
		for ($i=0; $i<count($list); $i++)
		{
			$browser = GetBrowser($list[$i]['userAgent']);
	
			if (!isset($browser_stats['name'][ $browser['name'] ][ $browser['version'] ])) $browser_stats['name'][ $browser['name'] ][ $browser['version'] ] = 0;
			$browser_stats['name'][ $browser['name'] ][ $browser['version'] ]++;
	
			if (!isset($browser_stats['OS'][ $browser['OS'] ])) $browser_stats['OS'][ $browser['OS'] ] = 0;
			$browser_stats['OS'][ $browser['OS'] ]++;
	
			if (!$browser['fully_detected']) {
				$browser_stats['unknown'][] = trim($list[$i]['userAgent']);
			}
		}
		
		$all_cnt = 0;
		foreach ($browser_stats['name'] as $browser => $versions)
		{
			//Hitta populäraste versionen av denna webbläsare
			$top_ver = 0;
			$top_cnt = 0;
			$tot_cnt = 0;
			foreach ($browser_stats['name'][$browser] as $ver => $cnt)
			{
				if ($cnt > $top_cnt) {
					$top_cnt = $cnt;
					$top_ver = $ver;
				}
				$tot_cnt += $cnt;
			}
			$all_cnt += $tot_cnt;

			$browser_stats['stats'][$browser]['top_ver'] = $top_ver;
			$browser_stats['stats'][$browser]['top_cnt'] = $top_cnt;

			$browser_stats['stats'][$browser]['tot_cnt'] = $tot_cnt;
		}

		foreach ($browser_stats['stats'] as $browser_key => $browser_val)
		{
			$browser_stats['stats'][$browser_key]['tot_pct'] = round($browser_stats['stats'][$browser_key]['tot_cnt'] / $all_cnt * 100, 2);
		}


		//Sorterar ['stats'] efter populärast webbläsare i fallande ordning
		$browser_stats['stats'] = aRSortBySecondIndex($browser_stats['stats'], 'tot_cnt');


		arsort($browser_stats['OS']);


		//echo '<pre>'; print_r($browser_stats); die;


		return $browser_stats;
	}
?>