/*
	Minimal mp3 player by Martin Lindhe, 2007

	Pass a valid mp3 in a relative url in the "s" variable of the swf file
	Pass the song title / file name in the "n" variable

	More info: http://www.metasphere.net/help/FAQ-1010.html

	Todos:
		* show mp3.position & mp3.duration while the song is playing.
			- behöver en player loop. uppdatera även därifrån ljudvolymen beroende på var slidern är

		* volume slider "onRelease" not triggering
*/

var resourceURL = '';

_level0.songTitle.text = 'Loading MP3...';

if (_level0.s) {
	resourceURL = _level0.s;
} else {
	//todo: ta bort detta. enbart för testning
	resourceURL = 'song.mp3';
}

//trace('Loading resource: ' + resourceURL);

function convertSeconds(secs)
{
	secs = Math.round(secs);

	var m = Math.round(secs/60)-1;
	if (m<0) m=0;
	var s = secs-(m*60);

	if (m<10) m = '0' + m;
	if (s<10) s = '0' + s;

	//trace('converted ' + secs + ' to ' + m + ':' + s);

	return m + ':' + s;
}


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

	_root.songPos.text = convertSeconds(mp3.position/1000) + ' / ' + convertSeconds(mp3.duration/1000);
}

mp3.onSoundComplete = function() {
	trace('Song played thru');
}

btnPlay.onRelease = function() {
	mp3.start(cue);
	paused = 0;
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


