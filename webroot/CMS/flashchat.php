<?
	/*
		Note: Kräver UTF-8, annars hanteras varken svenska tecken eller ?-tecknet korrekt i user input.

		fixme: enkoda : tecken
		
		fixme: chat-texten skrollar inte, om man skriver in massa långa meningar försvinner de sista

		
		todo: tillåt inte att skicka tomma textsträngar (enter i tomt inputfält)
		todo: ignorera ENTER om inte input fokus är i textfältet
		
		todo: kan username bli klickbart & leda till ett popupfönster?
		
		todo: visa namn på alla i chattrummet

		todo: visa lista på chattrum & antal personer i varje chattrum först
		
		todo: spara allt i databasen istället, samt associera varje textrad med ett chatroomId/userId etc

		hur man inkluderar extern actionscript:
		$strAction = join("", file("filename.as"));
		$movie->add(new SWFAction(str_replace("\r", "", $strAction)));
	*/


	$width = 500;
	$height = 300;
	if (!empty($_GET['w']) && is_numeric($_GET['w'])) $width = $_GET['w'];
	if (!empty($_GET['h']) && is_numeric($_GET['h'])) $height = $_GET['h'];

	ming_useswfversion(7);

  function makeRect($r, $g, $b, $w=80, $h=16) {
		$s = new SWFShape();
		$s->setRightFill($s->addFill($r, $g, $b));
		$s->movePenTo(0, 0);
		$s->drawLineTo($w, 0);
		$s->drawLineTo($w, $h);
		$s->drawLineTo(0, $h);
		$s->drawLineTo(0, 0);
		return $s;
  }

	session_name('ABsessID');
	session_start();

	//$font = new SWFFont('_verdana');
	$font = new SWFFont('e:/webroot/adblock/fdb/Verdana.fdb');

	$text1 = new SWFText();		//fixme: gör en maketext-helperfunktion
  $text1->setFont($font);
  $text1->moveTo(10, 18);
  $text1->setColor(0x00, 0x00, 0x00);
  $text1->setHeight(12);
  $userName = 'Guest';
  if (!empty($_SESSION['userName'])) $userName = $_SESSION['userName'];
  $text1->addUTF8String('Your username: '. $userName);

	$text2 = new SWFText();
  $text2->setFont($font);
  $text2->moveTo(355, 252);
  $text2->setColor(0x00, 0x00, 0x00);
  $text2->setHeight(12);
  $text2->addUTF8String('Send!');

	/* textfield - textarean där chattloggen visas */
	$tfChatLog = new SWFTextField(SWFTEXTFIELD_DRAWBOX | SWFTEXTFIELD_NOEDIT | SWFTEXTFIELD_NOSELECT | SWFTEXTFIELD_WORDWRAP | SWFTEXTFIELD_HTML);
	$tfChatLog->setFont($font);
  $tfChatLog->setColor(0x00, 0x00, 0x00);
  $tfChatLog->setBounds(400, 200);	//width, height
  $tfChatLog->setName('chatLog');

	/* inputfält för chat */
	$tfChatInput = new SWFTextField(SWFTEXTFIELD_DRAWBOX);
	$tfChatInput->setFont($font);
  $tfChatInput->setColor(0x00, 0x00, 0x00);
  $tfChatInput->setBounds(300, 14);	//width, height
  $tfChatInput->setName('chatInput');
  
	/* Knapp som skickar chat-text */
  $button1 = new SWFButton();
  $button1->setUp(		makeRect(0xFF, 0x00, 0x00) );
  $button1->setOver(	makeRect(0xCC, 0x00, 0x00) );
  $button1->setDown(	makeRect(0x00, 0x00, 0xAA) );
  $button1->setHit(		makeRect(0x00, 0x00, 0x00) );

	/* Skapa movieobjekt */
	$m = new SWFMovie();
	$m->setBackground(0xC0, 0xF0, 0xA0);
	$m->setDimension($width, $height);

	$m->add($text1);

	$i = $m->add($tfChatLog);
	$i->moveTo(10, 30);

	$i = $m->add($tfChatInput);
	$i->moveTo(10, 240);

	$i = $m->add($button1);
  $i->setName('btnSendChat');
  $i->moveTo(330, 240);
 	$m->add($text2);
	
	$m->add(new SWFAction("
			function checkParamsLoaded() {
				if (_level0.q == 0) {
					SendDataTimeout++;
					if (SendDataTimeout>100) {	//10 sec ?
						chatLog = 'timeout while waiting for server response';
						SendDataTimeout=0;
						_level0.q=0;
						clearInterval(param_interval);
					}
					return;
				}
				chatLog = c;
				SendDataTimeout=0;
				_level0.q=0;
				clearInterval(param_interval);
			};

			function loadCurrentChat() {
				_level0.q=0;
				loadVariablesNum('flashchat_getchatlog.php', 0);
				param_interval = setInterval(checkParamsLoaded, 100);
			};

			btnSendChat.onRelease = function() {
				_level0.q=0;
				loadVariablesNum('flashchat_recievemessage.php?v='+chatInput, 0);
				chatInput = '';
				param_interval = setInterval(checkParamsLoaded, 100);
			};

			keyListener = new Object();
			Key.addListener(keyListener);
			keyListener.onKeyDown = function() {
				if (Key.getCode() == Key.ENTER) {
					_level0.q=0;
					loadVariablesNum('flashchat_recievemessage.php?v='+chatInput, 0);
					chatInput = '';
					param_interval = setInterval(checkParamsLoaded, 100);
				}
			};

			q=0;
			SendDataTimeout=0;

			loadCurrentChat();
			regular_update = setInterval(loadCurrentChat, 1000);
		"));

	header('Content-type: application/x-shockwave-flash');
	$m->output();
//	$m->save('flashchat.swf', 9);
?>