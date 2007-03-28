//Minimal mp3 player by Martin Lindhe, 2007

//pass a valid mp3 in a relative url in the "s" variable of the swf file

var resourceURL;

if (_level0.s) {
	trace('Loading web resource from ' + _level0.s);
	resourceURL = _level0.s;
} else {
	//todo: ta bort detta. enbart för testning
	trace('Loading local resource!');
	resourceURL = 'song.mp3';
}

