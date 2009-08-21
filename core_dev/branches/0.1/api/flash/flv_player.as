stop();

if(!bgcolor) bgcolor = "0x051615";
if(!fgcolor) fgcolor = "0x13ABEC";
setFills(bgcolor, fgcolor);

/* Disable the default menu */
(_root.menu = new ContextMenu()).hideBuiltInItems();

function goHome(){getURL("http://www.trenttompkins.com")}
_root.menu.customItems.push(new ContextMenuItem("Video Player by Trent Tompkins", goHome));


/* You don't have to leave the right click link or the menu item. Use this line instead
   to get rid of the link. You may NOT copyright the player, or say you created it. */
//_root.menu.customItems.push(new ContextMenuItem("Created by Trent Tompkins", new Function()));

/* Set stage properties */
Stage.align = "BL";
Stage.scaleMode = "noScale";

/* Set globals */
_global.wasPlaying = false;
_global.noTwitch = false;
_global.ready = false;

/* Embed parameters */
_global.autoplay = (autoplay == "true" || autoplay == "on") ? true : false;
_global.filename = movie;
_global.autoload = (autoload == "false" || autoload == "off") ? false : true;
_global.muteonly = (muteonly == "true" || muteonly == "on") ? true : false;
_global.loop = (loop == "true" || loop == "on") ? true : false;
_global.autorewind = (autorewind == "false" || autorewind == "off") ? false : true;
_global.mute = (mute == "true" || mute == "on") ? true : false;
_global.clickurl = clickurl;
_global.clicktarget = clicktarget;

_global.filename = _global.filename.split("&amp;").join("&"); 
trace('Loading video: ' + _global.filename);

if(_global.autoload == false)
	_global.autoplay = false;

if(!_global.clickurl)
	_root.clickArea._visible = false;

if (_global.volume = volume) _root.display.volume = volume; else _global.volume = 70;

/* Set Volume Control */
if(_global.muteonly){
	_root.panelRight.volumeSlider._visible = false;
} else {
	_root.panelRight.audioOn._visible = false;
	_root.panelRight.audioOff._visible = false;
	
	if(_global.volume){
		level = _global.volume / 10;
		if(level > 10 || level < 0)
			level = 7;

		_root.display.volume = level * 10;
		_root.panelRight.volumeSlider.volumeDown._height = (9.45/10) * level;
		_root.panelRight.volumeSlider.volumeDown._width = (17.35/10) * level;
	}
}

if(_global.mute){
	_root.display.volume = 0;
	this.panelRight.audioOn._visible = false;
	_root.panelRight.volumeSlider.volumeDown._height = _root.panelRight.volumeSlider.volumeDown._width = 0;
}

/* Error message and embed instructions */
if(!_global.filename) {
	trace('No video supplied');
	_root.gotoAndStop(2);
}

/* Converts seconds into readable time */
function formatTime(timeval){
	timeval = timeval / 60;
	integer = String(Math.floor(timeval));
	decimal = timeval - Math.floor(timeval);
	decimal *= .6;
	decimal = String(decimal);
	decimal = decimal.substr(2, 2);
	while(decimal.length < 2) decimal = "0" + decimal;
	while(integer.length < 2) integer = "0" + integer;
	
	return integer + ":" + decimal;
}

/* Events genearted by the FLV Component */
var listenerObject:Object = new Object();

listenerObject.complete = function(eventObject:Object):Void {
	if(_global.autorewind && !_global.loop){
		_root.display.playheadTime = 0;
		_root.display.pause();
		_root.playButton._visible = true;
		return;
	}
	_root.playButton._visible = !_global.loop;
	_root.display.play();
};

listenerObject.ready = function(eventObject:Object):Void {
	_root.panelRight.totalTime.text = formatTime(_root.display.totalTime);
	_global.ready = true;
	_root.washout._visible = _root.washoutplay._visible = false;
	_root.setSizes();
};

listenerObject.playheadUpdate = function(eventObject:Object):Void {
	if(!_global.noTwitch){
		_root.playingBar._width = (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);
		_root.panelRight.currentTime.text = formatTime(_root.display.playheadTime);
		_root.slider._x = 48 + (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);
	} else
		_global.noTwitch = false;
};

listenerObject.stateChange = function(eventObject:Object):Void {
	if(_root.display.state == 'connectionError') {
		trace('Connection error');
		_root.gotoAndStop(2);
	}
};


listenerObject.progress = function(eventObj){
	_root.blankBar._width = ((Stage.width - 188) * (1-(_root.display.bytesLoaded / _root.display.bytesTotal))) ;
	_root.blankBar._x = (Stage.width - 188 + 51) - _root.blankBar._width;
}

_root.display.addEventListener("ready", listenerObject);
_root.display.addEventListener("complete", listenerObject);
_root.display.addEventListener("playheadUpdate", listenerObject);
_root.display.addEventListener("stateChange", listenerObject);
_root.display.addEventListener("progress", listenerObject);

/* Sets the sie of all root objects */
_root.setSizes = function(){
	if(Stage.width < 200)
		return false;
		
	_root.playingBar._width = (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);
	_root.panelRight.currentTime.text = formatTime(_root.display.playheadTime);
	_root.slider._x = 48 + (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);
		
	_root.washout._width = _root.panel._width = Stage.width;
	_root.panelRight._x = Stage.width - 137;
	_root.bar._width = Stage.width - 188;
	_root.loadedBar._width = Stage.width - 188;

	_root.blankBar._width = ((Stage.width - 188) * (1-(_root.display.bytesLoaded / _root.display.bytesTotal))) ;
	_root.blankBar._x = (Stage.width - 188 + 51) - _root.blankBar._width;
	
	_root.washoutplay._width = _root.bigPlay._width = Stage.width / 4;
	_root.washoutplay._x =_root.bigPlay._x = (Stage.width-_root.bigPlay._width)/2;
	_root.washoutplay._height =_root.bigPlay._height = _root.bigPlay._width * (84/127);
	_root.washoutplay._y =_root.bigPlay._y = (400-Stage.height)+((Stage.height-40)-_root.bigPlay._height)/2;
	
	_root.usage._y = 200-(Stage.height/2);
	_root.usage._x = (Stage.width-550)/2;

	aspect = _root.display.preferredWidth / _root.display.preferredHeight;

	if( ( (Stage.height-40) * aspect) <= Stage.width){
		_root.display._height = Stage.height-40;	
		_root.display._width = _root.display._height * aspect;
		_root.display._x = (Stage.width - _root.display._width) / 2;
	} else {
		_root.display._width = Stage.width;
		_root.display._height = ( Stage.width * (1/aspect));
		_root.display._x = (Stage.width - _root.display._width) / 2;
	}
	
	_root.clickArea._width = Stage.width;
	_root.clickArea._height = Stage.height - 40;
	_root.clickArea._y = 400 - Stage.height;
	_root.display._y = 400 - Stage.height + (((Stage.height-40) - _root.display._height) / 2);
	_root.playingBar._width = (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);
	return true;
}

sizeListener = new Object();
sizeListener.onResize = function() {
	_root.setSizes();
};
sizeListener.onFullScreen = function() {
	_root.setSizes();
}
Stage.addListener(sizeListener);

var listenerObject:Object = new Object();

if(_global.autoload)
	_root.display.contentPath = _global.filename;

if(_global.autoplay){
	_root.display.play();
	_root.bigPlay._visible = false;
	_root.playButton._visible = false;
	sizeListener.onResize();
}

_root.playingBar.onRelease = function(){
	percentage = (_root._xmouse-52) / (Stage.width-188);
	_root.display.playheadTime = percentage * _root.display.totalTime;
	_root.bigPlay._visible = false;
	_root.slider._x = 48 + (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);

	if(_global.wasPlaying) _root.display.play();
}

_root.blankBar.onRelease = function(){
	if(_global.ready){
		percentage = _root.display.bytesLoaded / _root.display.bytesTotal;
		_global.noTwitch = false;
		_root.display.seekSeconds(Math.floor(percentage * _root.display.totalTime));
		if(_global.wasPlaying) _root.display.play();
		_root.bigPlay._visible = false;
		if(_global.wasPlaying) _root.display.play();
	}
}

_root.loadedBar.onRelease = function(){
	if(_global.ready){
		percentage = (_root._xmouse-52) / (Stage.width-188);
		_root.display.playheadTime = percentage * _root.display.totalTime;
		_root.bigPlay._visible = false;
	
		_root.slider._x = 48 + (Stage.width - 188) * (_root.display.playheadTime / _root.display.totalTime);
		_root.bigPlay._visible = false;
	}
}

_root.pauseButton.onRelease = function(){
	_root.display.pause();
	_root.playButton._visible = true;
}

_root.playButton.onRelease = function(){

	if(!_global.autoload)
		if(!_root.display.contentPath){
			_root.display.contentPath = _global.filename;
			_global.autoload = true;
			_root.display.play();
			_root.playButton._visible = false;
			_root.bigPlay._visible = false;
		}
	
	if(_global.ready){		
		_root.display.play();
		_root.playButton._visible = false;
		_root.bigPlay._visible = false;
	}
}

_root.bigPlay.onRelease = function(){
	if(!_global.autoload)
		if(!_root.display.contentPath){
			_root.display.contentPath = _global.filename;
			_global.autoload = true;
			_root.display.play();
			_root.playButton._visible = false;
			_root.bigPlay._visible = false;
		}
	
	if(_global.ready){		
		_root.display.play();
		_root.playButton._visible = false;
		_root.bigPlay._visible = false;
	}
}

_root.usage._width = Stage.width;
_root.usage._height = Stage.height;
_root.usage._y = 400 - Stage.height;

/* Sets the starting size of the objects */
_root.onEnterFrame = function(){
	if(_root.setSizes())
		_root.onEnterFrame = null;
}

/* Turn on video smoothing */
MovieClip(_root.display.getVideoPlayer(_root.display.activeVideoPlayerIndex))._video.smoothing = true;

/* Slider */
_root.slider.onPress = function(){
	if(_global.ready){
		_global.wasPlaying = _root.display.playing;
		_root.display.pause();
		_root.slider.onEnterFrame = Sliding;
	}
}

function Sliding(){

	percentage = (_root._xmouse-52) / (Stage.width-188);
	
	if(percentage <= 0){
		_root.slider._x = 48;
		_root.panelRight.currentTime.text = "00:00";
		_root.playingBar._width = 0;
		return;
	}
	if(percentage >= 1){
		_root.slider._x = Stage.width-140;
		_root.panelRight.currentTime.text = formatTime(_root.display.totalTime);
		_root.playingBar._width = (Stage.width - 188);
		return;
	}
	
	_root.slider._x = 48 + ((Stage.width-138)-48) * percentage;
	_root.panelRight.currentTime.text = formatTime(_root.display.totalTime * percentage);
	_root.playingBar._width = (Stage.width - 188) * percentage;
}

function Release(){
	_root.slider.onEnterFrame = null;

	percentage = (_root._xmouse-52) / (Stage.width-188);
	loadedPercentage = _root.display.bytesLoaded / _root.display.bytesTotal;
	
	if(percentage < 0)
		percentage = 0;
	if(percentage > 1)
		percentage = 1;
	if(percentage > loadedPercentage)
		percentage = loadedPercentage - .01;
	
	_root.display.playheadTime = Math.floor(percentage * _root.display.totalTime);
	
	_root.slider._x = 48 + ((Stage.width-138)-48) * percentage;
	_root.panelRight.currentTime.text = formatTime(_root.display.totalTime * percentage);
	_root.playingBar._width = (Stage.width - 188) * percentage;
	
	if(_global.wasPlaying) _root.display.play();
	_root.bigPlay._visible = false;
	_global.noTwitch = true;
}

_root.slider.onRelease = function(){
	Release();
}

_root.slider.onReleaseOutside = function(){
	Release();
}

/* Old-Style Volume Controls */
_root.panelRight.audioOn.onRelease = function(){
	_root.panelRight.audioOn._visible = false;
	_root.display.volume = 0;
}

_root.panelRight.audioOff.onRelease = function(){
	_root.panelRight.audioOn._visible = true;
	_root.display.volume = _global.volume;
}

/* New-Style Volume Controls */
_root.panelRight.volumeSlider.volumeDown.onRelease = function(){
	level = (16-(Stage.width-_root._xmouse-21)) * (10/16);
	_root.display.volume = level * 10;

	this._height = (9.45/10) * level;
	this._width = (17.35/10) * level;
}

_root.panelRight.volumeSlider.volumeUp.onRelease = function(){
	level = (16-(Stage.width-_root._xmouse-21)) * (10/16);
	_root.display.volume = level * 10;

	_root.panelRight.volumeSlider.volumeDown._height = (9.45/10) * level;
	_root.panelRight.volumeSlider.volumeDown._width = (17.35/10) * level;
}

/* Dynamic Foreground and Background Colors */
function setFills(bgcolor, fgcolor){
	_root.panel.barbase.beginFill(bgcolor);
	_root.panel.barbase.moveTo(0, 0);
	_root.panel.barbase.lineTo(550, 0);
	_root.panel.barbase.lineTo(550, 40);
	_root.panel.barbase.lineTo(0, 40);
	_root.panel.barbase.lineTo(0, 0);
	_root.panel.barbase.endFill();
	
	_root.playButton.beginFill(fgcolor);
	_root.playButton.moveTo(0, 0);
	_root.playButton.lineTo(31, 0);
	_root.playButton.lineTo(31, 24);
	_root.playButton.lineTo(0, 24);
	_root.playButton.lineTo(0, 0);
	_root.playButton.endFill();
	
	_root.bigPlay.beginFill(fgcolor);
	_root.bigPlay.moveTo(0, 0);
	_root.bigPlay.lineTo(127, 0);
	_root.bigPlay.lineTo(127, 84);
	_root.bigPlay.lineTo(0, 84);
	_root.bigPlay.lineTo(0, 0);
	_root.bigPlay.endFill();
	
	_root.pauseButton.beginFill(fgcolor);
	_root.pauseButton.moveTo(0, 0);
	_root.pauseButton.lineTo(31, 0);
	_root.pauseButton.lineTo(31, 24);
	_root.pauseButton.lineTo(0, 24);
	_root.pauseButton.lineTo(0, 0);
	_root.pauseButton.endFill();
	
	_root.panelRight.volumeSlider.beginFill(fgcolor);
	_root.panelRight.volumeSlider.moveTo(0, 11);
	_root.panelRight.volumeSlider.lineTo(19, 0);
	_root.panelRight.volumeSlider.lineTo(19, 11);
	_root.panelRight.volumeSlider.lineTo(0, 11);
	_root.panelRight.volumeSlider.lineTo(0,11);
	_root.panelRight.volumeSlider.endFill();
	
	_root.loadedBar.beginFill(fgcolor);
	_root.loadedBar.moveTo(0, 0);
	_root.loadedBar.lineTo(363, 0);
	_root.loadedBar.lineTo(363, 6);
	_root.loadedBar.lineTo(0, 6);
	_root.loadedBar.lineTo(0, 0);
	_root.loadedBar.endFill();
}

if(_global.clickurl){
	_root.clickArea.onPress = function(){
		if(_global.clicktarget)
			getURL(_global.clickurl, _global.clicktarget, "GET");
		else
			getURL(_global.clickurl, '_self', "GET");
	}
}