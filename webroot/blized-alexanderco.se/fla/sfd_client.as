//sfd client script
import flash.display.BitmapData;
import flash.geom.Matrix;
import flash.geom.Rectangle;
import flash.geom.Point;


var listener:Object = new Object();
var imageLoader:MovieClipLoader = new MovieClipLoader();

var q = 0;
var SendDataTimeout = 0;

var TotNumberOfImages = 0;
var LoadedNumberOfImages = 0;

var CurrentObject = 0;

//var datafile_path = "http://195.189.182.112/sfd_cache/";
var datafile_path = "../sfd_cache/";



trace('script started');
	
loadCurrentSFDObjects();

//interval i millisek (1000 = 1 sek), 3600000 = anropa 1 gång i timmen
regular_update = setInterval(loadCurrentSFDObjects, 3600000);



function visa_slingan():Void
{
	trace('visa_slingan() called');

	_root.objekt1.bild1._visible = false;

	_root.objekt2.bild1._visible = false;
	_root.objekt2.bild2._visible = false;

	_root.objekt3.bild1._visible = false;
	_root.objekt3.bild2._visible = false;
	_root.objekt3.bild3._visible = false;

	_root.objekt4.bild1._visible = false;
	_root.objekt4.bild2._visible = false;
	_root.objekt4.bild3._visible = false;
	_root.objekt4.bild4._visible = false;

	_root.objekt5.bild1._visible = false;
	_root.objekt5.bild2._visible = false;
	_root.objekt5.bild3._visible = false;
	_root.objekt5.bild4._visible = false;
	_root.objekt5.bild5._visible = false;

	_root.objekt6.bild1._visible = false;
	_root.objekt6.bild2._visible = false;
	_root.objekt6.bild3._visible = false;
	_root.objekt6.bild4._visible = false;
	_root.objekt6.bild5._visible = false;
	_root.objekt6.bild6._visible = false;

	_root.objekt7.bild1._visible = false;
	_root.objekt7.bild2._visible = false;
	_root.objekt7.bild3._visible = false;
	_root.objekt7.bild4._visible = false;
	_root.objekt7.bild5._visible = false;
	_root.objekt7.bild6._visible = false;
	_root.objekt7.bild7._visible = false;

	_level0.CurrentObject++;
	
	/* Börja om från början när alla har visats */
	if (_level0.CurrentObject > _level0.antalobjekt) {
		_level0.CurrentObject = 1;

		//todo: hoppa till utro
	}
	
	
	trace('Spelar upp objekt' + _level0.CurrentObject + ', innehållande ' + _level0['images_'+_level0.CurrentObject] + ' bilder');
	
	switch (_level0['images_'+_level0.CurrentObject])
	{
		case "1":
			trace('hoppar till objekt1...');
			_root.gotoAndStop('objekt1');
			
			//mappa aktuell data till movieclip "objekt1"
			_root.objekt1.beskriv = _level0['beskr_'+_level0.CurrentObject];
			_root.objekt1.pris = _level0['pris_'+_level0.CurrentObject];
			_root.objekt1.storlek = _level0['storlek_'+_level0.CurrentObject];
			_root.objekt1.adress = _level0['adress_'+_level0.CurrentObject];

			bild1_w = _level0['obj'+_level0.CurrentObject+'_img1']._width;
			bild1_h = _level0['obj'+_level0.CurrentObject+'_img1']._height;
			trace('kopierar '+bild1_w+'x'+bild1_h+' pixlar');

			_root.tempBitmap = new BitmapData(bild1_w, bild1_h);
			_root.tempBitmap.draw(_level0['obj'+_level0.CurrentObject+'_img1'], new Matrix());
			_root.objekt1.bild1.attachBitmap(tempBitmap, 2);

			_root.objekt1.bild1._visible = true;

			_root.objekt1.gotoAndPlay(1);
			break;
			
		case "2":
			trace('hoppar till objekt2...');
			_root.gotoAndStop('objekt2');
			
			//mappa aktuell data till movieclip "objekt2"
			_root.objekt2.beskriv = _level0['beskr_'+_level0.CurrentObject];
			_root.objekt2.pris = _level0['pris_'+_level0.CurrentObject];
			_root.objekt2.storlek = _level0['storlek_'+_level0.CurrentObject];
			_root.objekt2.adress = _level0['adress_'+_level0.CurrentObject];
			
			trace('storlek:'+_level0['storlek_'+_level0.CurrentObject]);
			trace('adress:'+_level0['adress_'+_level0.CurrentObject]);

			bild1_w = _level0['obj'+_level0.CurrentObject+'_img1']._width;
			bild1_h = _level0['obj'+_level0.CurrentObject+'_img1']._height;

			bild2_w = _level0['obj'+_level0.CurrentObject+'_img2']._width;
			bild2_h = _level0['obj'+_level0.CurrentObject+'_img2']._height;

			_root.tempBitmap = new BitmapData(bild1_w, bild1_h);
			_root.tempBitmap.draw(_level0['obj'+_level0.CurrentObject+'_img1'], new Matrix());
			_root.objekt2.bild1.attachBitmap(tempBitmap, 2);

			_root.tempBitmap = new BitmapData(bild2_w, bild2_h);
			_root.tempBitmap.draw(_level0['obj'+_level0.CurrentObject+'_img2'], new Matrix());
			_root.objekt2.bild2.attachBitmap(tempBitmap, 4);


			_root.objekt2.bild1._visible = true;
			_root.objekt2.bild2._visible = true;	//visas senare i objekt2-movieclipet

			_root.objekt2.gotoAndPlay(1);
			break;
			
		default:
			trace('OOMG vad ska vi göra vi har ' + _level0['images_'+_level0.CurrentObject] + ' bilder att visa.. hhjllx');
	}

}



// The onLoadInit() method is called once the image loads.
listener.onLoadInit = function(imageClip:MovieClip):Void
{
	trace(imageClip._url + ' loaded');
	_level0.LoadedNumberOfImages++;
	
	imageClip._visible = false;		//gör bilderna osynliga så länge

	if (_level0.LoadedNumberOfImages == _level0.TotNumberOfImages) {
		trace('All images loaded, lets play()!');
		
		visa_slingan();
	}
}

function checkParamsLoaded()
{
	trace('checkParamsLoaded() called ');

	if (_level0.q == 0) {
		SendDataTimeout++;
		if (SendDataTimeout > 1000) {	//50 millisek * 1000 = 50 sekunder innan timeout
			trace('timeout while waiting for server response');

			SendDataTimeout=0;
			_level0.q=0;
			clearInterval(param_interval);
			
			//gör ett nytt försök:
			loadCurrentSFDObjects();
		}
		trace('checkParamsLoaded() returning, initialization not yet done');
		return;
	} else {
		//all data är inläst, ladda bilder:
		trace('starting load of images, antalobjekt = ' + _level0.antalobjekt );
		
		//_level0.createEmptyMovieClip("image_1", this.getNextHighestDepth());
		imageLoader.addListener(listener);

		/* Ladda in alla bilder för varje objekt*/
		for (var i=1; i<=_level0.antalobjekt; i++) {
			var images = _level0['images_'+i];
			//trace('Loading ' + images + ' images into objekt' + i);
			for (var j=1; j<=images; j++) {
				trace('Loading _level0.obj'+i+'_img'+j+' from _level0.thumburl'+i+'_'+j);
				trace('loading url ' + _level0['thumburl'+i+'_'+j] );

				_level0.createEmptyMovieClip( 'obj'+i+'_img'+j, _level0.getNextHighestDepth() );
				imageLoader.loadClip( _level0['thumburl'+i+'_'+j], 'obj'+i+'_img'+j );
				_level0.TotNumberOfImages++;
			}
		}
	}

	SendDataTimeout=0;
	_level0.q=0;
	clearInterval(param_interval);
	trace('Leaving checkParamsLoaded()');
};

function loadCurrentSFDObjects()
{
	trace('loadCurrentSFDObjects() called');
	_level0.q=0;

	loadVariablesNum(datafile_path + 'skeppsholmen.txt', 0);
	
	//med för låg delay här så sätts q till 1 men variablerna verkar ändåp inte läsas in wtf
	param_interval = setInterval(checkParamsLoaded, 500);
};

