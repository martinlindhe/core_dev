/*
	Minimal mp3 player by Martin Lindhe, 2007

	Pass a valid mp3 in a relative url in the "s" variable of the swf file
	
	More info: http://www.metasphere.net/help/FAQ-1010.html

	Todos:
		* show mp3.position & mp3.duration while the song is playing

		* volume slider
	
		* show song name (flash Sound object dont seem to know about ID3 tags etc, take a 2nd parameter?)

		* visa button-pressed grafik när man trycker på knapparna
*/

var resourceURL = '';

if (_level0.s) {
	resourceURL = _level0.s;
} else {
	//todo: ta bort detta. enbart för testning
	resourceURL = 'song.mp3';
}

trace('Loading resource: ' + resourceURL);

cue = 0;
paused = 0;
mp3 = new Sound();
mp3.setVolume(50);
mp3.loadSound(resourceURL, true);

mp3.onLoad = function() {
	if (!mp3.duration) {
		_level0.songTitle.text = 'Failed to load MP3';
	}
	trace('song loaded, duration: ' + mp3.duration);

	_root.songPos.text = (mp3.position/1000) + ' / ' + (mp3.duration/1000);
}

mp3.onSoundComplete = function() {
	trace('song played thru');
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
