//sfd referens client script
import flash.display.BitmapData;
import flash.geom.Matrix;
import flash.geom.Rectangle;
import flash.geom.Point;

var SFDlistener:Object = new Object();
var SFDimageLoader:MovieClipLoader = new MovieClipLoader();

var datafile_path = "http://php5.web-3.crystone.se/alexanderco.se/sfd_cache/";
//var datafile_path = "../sfd_cache/";


trace('sfd_referens.as inkluderad');

function SFD_main()
{
	trace('Script started, datafile: ' + _level0.datafile);

	_root.q = 0;
	_root.SendDataTimeout = 0;
	_root.CurrentObject = 0;

	_root.holder.TotNumberOfImages = 0;
	_root.holder.LoadedNumberOfImages = 0;

	loadCurrentSFDObjects();
}


function mappa_referensobjekt()
{
	_root.holder.stop();
	//nu är all data inladdat, och bilderna nerladdade. återstår bara att mappa ihop allt
	trace('mappa_referensobjekt() called');

	trace('rubrik 1 visible: ' + _level0.holder.main.obj1.objRubrik._visible ) ;

	for (var i=1; i<=_root.holder.LoadedNumberOfImages; i++) {
		_level0['holder.main.obj'+i+'.objRubrik'] = _level0['adress_'+i];
		_level0['holder.main.obj'+i+'.objText'] = _level0['beskr_'+i];
	
		bild_w = _level0['obj'+i+'_thumb']._width;
		bild_h = _level0['obj'+i+'_thumb']._height;
		trace('Kopierar '+bild_w+'x'+bild_h+' pixlar');
		
		_root.holder.tempBitmap = new BitmapData(bild_w, bild_h);
		_root.holder.tempBitmap.draw(_level0['obj'+i+'_thumb'], new Matrix());
		
		// Visa bilden
		eval("_root.holder.main.obj"+i+".objPic").attachBitmap(_root.holder.tempBitmap, 2);
	}
	trace('noObj visible: ' +_level0.holder.noObj._visible );
	_level0.holder.noObj._visible = false;

	if (_root.holder.LoadedNumberOfImages < 3) {
		//dölj tomma objekt
		
		if (_root.holder.LoadedNumberOfImages < 2) {
			_level0.holder.main.obj2.gbar._visible = false;
			_level0.holder.main.obj2.link._visible = false;
			_level0.holder.main.obj2.objRubrik._visible = false;
			_level0.holder.main.obj2.objText._visible = false;
		}
		
		if (_root.holder.LoadedNumberOfImages < 1) {
			//dölj objekt 1, visa noObj-texten
			_level0.holder.main.obj1.gbar._visible = false;
			_level0.holder.main.obj1.link._visible = false;
			_level0.holder.main.obj1.objRubrik._visible = false;
			_level0.holder.main.obj1.objText._visible = false;
			
			_level0.holder.noObj._visible = true;
		}

		_level0.holder.main.obj3.gbar._visible = false;
		_level0.holder.main.obj3.link._visible = false;
		_level0.holder.main.obj3.objRubrik._visible = false;
		_level0.holder.main.obj3.objText._visible = false;

	}
	
	_root.holder.play();
}


// The onLoadInit() method is called once the image loads.
SFDlistener.onLoadInit = function(imageClip:MovieClip):Void
{
	trace(imageClip._url + ' loaded');
	_root.holder.LoadedNumberOfImages++;
	
	imageClip._visible = false;		//gör bilderna osynliga så länge

	if (_root.holder.LoadedNumberOfImages == _root.holder.TotNumberOfImages) {
		trace('All images loaded, lets play()!');
		
		_root.holder.play("spela");
		
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
		
		SFDimageLoader.addListener(SFDlistener);

		/* Ladda in den första thumbnailen för varje objekt*/
		for (var i=1; i<=_level0.antalobjekt; i++) {
			trace('Loading _level0.obj'+i+'_thumb from '+_level0['thumburl'+i+'_1']);

			_level0.createEmptyMovieClip( 'obj'+i+'_thumb', _level0.getNextHighestDepth() );
			SFDimageLoader.loadClip( _level0['thumburl'+i+'_1'], '_root.obj'+i+'_thumb' );
			_root.holder.TotNumberOfImages++;
		}

	}

	SendDataTimeout=0;
	_level0.q=0;
	clearInterval(param_interval);
};

function loadCurrentSFDObjects()
{
	trace('loadCurrentSFDObjects() called');
	_level0.q=0;

	trace("datafile is: " + _level0.datafile);
	loadVariablesNum(datafile_path + _level0.datafile, 0);
	
	//med för låg delay här så sätts q till 1 men variablerna verkar ändåp inte läsas in wtf
	param_interval = setInterval(checkParamsLoaded, 500);
};

