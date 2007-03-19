//sfd referens client script
import flash.display.BitmapData;
import flash.geom.Matrix;
import flash.geom.Rectangle;
import flash.geom.Point;

var SFDlistener:Object = new Object();
var SFDimageLoader:MovieClipLoader = new MovieClipLoader();

var datafile_path = "sfd_cache/";


trace('sfd_referens.as inkluderad');

function SFD_readsections()
{
	trace('Script started, loading datafile: ' + datafile_path + _level0.datafile + ', section1: ' + _level0.sectionName1 + ', section2: ' + _level0.sectionName2 + ', section3: ' + _level0.sectionName3);

	_root.q = 0;
	_root.SendDataTimeout = 0;
	_root.CurrentObject = 0;

	_level0.TotNumberOfImages = 0;
	_level0.LoadedNumberOfImages = 0;


	loadVariablesNum(datafile_path + _level0.datafile, 0);
	
	//med för låg delay här så sätts q till 1 men variablerna verkar ändåp inte läsas in wtf
	param_interval = setInterval(checkParamsLoaded, 500);
}

function mappa_alla_objekt()
{
	trace('MAPPAR ALLA OBJEKT!!');
	
	if (_level0.sectionName1) mappa_objekt(_level0.sectionName1);
	if (_level0.sectionName2) mappa_objekt(_level0.sectionName2);
	if (_level0.sectionName3) mappa_objekt(_level0.sectionName3);

	_level0.antalobjekt = 0;
	if (_level0.sectionName1) _level0.antalobjekt += _level0[_level0.sectionName1+'_antalobjekt'];
	if (_level0.sectionName2) _level0.antalobjekt += _level0[_level0.sectionName2+'_antalobjekt'];
	if (_level0.sectionName3) _level0.antalobjekt += _level0[_level0.sectionName3+'_antalobjekt'];

	trace('TOTALT ANTAL OBJEKT: ' + _level0.antalobjekt);

}

function mappa_objekt(sectionName)
{
	_root.holder.stop();
	//nu är all data inladdat, och bilderna nerladdade. återstår bara att mappa ihop allt
	trace('mappa_objekt('+sectionName+') called');

	trace('rubrik 1 visible: ' + _level0.holder.main.obj1.objRubrik._visible ) ;

	for (var i=1; i<=_level0[sectionName+'_NumberOfImages']; i++) {
		_level0['holder.main.obj'+i+'.objRubrik'] = _level0[sectionName+'_adress_'+i];
		_level0['holder.main.obj'+i+'.objText'] = _level0[sectionName+'_beskr_'+i];
	
		bild_w = _level0[sectionName+'_obj'+i+'_thumb']._width;
		bild_h = _level0[sectionName+'_obj'+i+'_thumb']._height;
		trace('Kopierar '+bild_w+'x'+bild_h+' pixlar');
		
		_root.holder.tempBitmap = new BitmapData(bild_w, bild_h);
		_root.holder.tempBitmap.draw(_level0[sectionName+'_obj'+i+'_thumb'], new Matrix());
		
		// Visa bilden
		eval("_root.holder.main.obj"+i+".objPic").attachBitmap(_root.holder.tempBitmap, 2);
	}
	trace('noObj visible: ' +_level0.holder.noObj._visible );
	_level0.holder.noObj._visible = false;

	if (_level0.LoadedNumberOfImages < 3) {
		//dölj tomma objekt
		
		if (_level0.LoadedNumberOfImages < 2) {
			_level0.holder.main.obj2.gbar._visible = false;
			_level0.holder.main.obj2.link._visible = false;
			_level0.holder.main.obj2.objRubrik._visible = false;
			_level0.holder.main.obj2.objText._visible = false;
		}
		
		if (_level0.LoadedNumberOfImages < 1) {
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
	_level0.LoadedNumberOfImages++;
	
	imageClip._visible = false;		//gör bilderna osynliga så länge

	if (_level0.LoadedNumberOfImages == _level0.TotNumberOfImages) {
		trace('All images loaded, lets play()!');
		
		_root.holder.play("spela");
		
	}
}

function checkParamsLoaded()
{
	trace('checkParamsLoaded() called');

	if (_level0.q == 0) {
		SendDataTimeout++;
		if (SendDataTimeout > 1000) {	//50 millisek * 1000 = 50 sekunder innan timeout
			trace('timeout while waiting for server response - giving up');

			SendDataTimeout=0;
			_level0.q=0;
			clearInterval(param_interval);
		}
		trace('checkParamsLoaded() returning, initialization not yet done');
		return;
	}

	//all data är inläst, ladda bilder:
	
	if (_level0.sectionName1) {
		trace('Loading section 1 images: ' + _level0.sectionName1 );
		loadSectionImages(_level0.sectionName1 );
	}

	if (_level0.sectionName2) {
		trace('Loading section 2 images: ' + _level0.sectionName2 );
		loadSectionImages(_level0.sectionName2 );
	}

	if (_level0.sectionName3) {
		trace('Loading section 3 images: ' + _level0.sectionName3 );
		loadSectionImages(_level0.sectionName3 );
	}

}

function loadSectionImages(sectionName)
{
	trace('starting load of images, antalobjekt = ' + _level0[sectionName + '_antalobjekt'] );
		
	SFDimageLoader.addListener(SFDlistener);
	
	_level0[sectionName + '_NumberOfImages'] = 0;

	/* Ladda in den första thumbnailen för varje objekt*/
	for (var i=1; i<=_level0[sectionName + '_antalobjekt']; i++) {
		trace('Loading _level0.'+sectionName+'_obj'+i+'_thumb from '+_level0[sectionName+'_thumburl'+i+'_1']);

		_level0.createEmptyMovieClip( sectionName+'_obj'+i+'_thumb', _level0.getNextHighestDepth() );
		SFDimageLoader.loadClip( _level0[sectionName+'_thumburl'+i+'_1'], '_root.'+sectionName+'_obj'+i+'_thumb' );
		_level0.TotNumberOfImages++;

		_level0[sectionName + '_NumberOfImages']++;
	}

	SendDataTimeout=0;
	_level0.q=0;
	clearInterval(param_interval);
};
