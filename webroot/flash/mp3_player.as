/*
	Minimal mp3 player by Martin Lindhe, 2007

	Pass a valid mp3 in a relative url in the "s" variable of the swf file
	
	More info: http://www.metasphere.net/help/FAQ-1010.html

	Todos:
		* show mp3.position & mp3.duration while the song is playing.
			- visa i formatet 00:13 / 03:49
			- behöver en player loop. uppdatera även därifrån ljudvolymen beroende på var slidern är
*/

var resourceURL = '';

if (_level0.s) {
	resourceURL = _level0.s;
} else {
	//todo: ta bort detta. enbart för testning
	resourceURL = 'song.mp3';
}

//trace('Loading resource: ' + resourceURL);

cue = 0;
paused = 0;
mp3 = new Sound();
mp3.setVolume(50);
mp3.loadSound(resourceURL, true);

mp3.onLoad = function() {
	if (!mp3.duration) {
		_level0.songTitle.text = 'Failed to load MP3';
		return;
	}
	trace(resourceURL + ' loaded');
	
	_level0.songTitle.text = _level0.n;	//Display song title

	_root.songPos.text = (mp3.position/1000) + ' / ' + (mp3.duration/1000);
}

mp3.onSoundComplete = function() {
	trace('Song played thru');
}

btnPlay.onRelease = function() {
	mp3.start(cue);
}

btnPause.onRelease = function() {
	if (!paused) {
		cue = Math.round(mp3.position/1000);
		mp3.stop();
		paused = 1;
	} else {
		mp3.start(cue);
		paused = 0;
	}
}

btnStop.onRelease = function() {
	cue = 0;
	mp3.stop();
}

slider.onPress = function() {
	startDrag('slider', true, sliderBG._x, slider._y, (sliderBG._x+sliderBG._width-slider._width+1), slider._y);
}

//todo: denna funktion triggar inte när man släpper musen, wtf?!?!
slider.onRelease = function() {
	stopDrag();
}


