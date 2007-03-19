trace('scrollbar-koden..');

fscommand("allowscale", "false");
bar.useHandCursor = dragger.useHandCursor=false;
space = 0;
friction = 0.9;
speed = 4;
y = dragger._y;
top = main._y;

//orginal:
//bottom = main._y+mask_mc._height-main._height-space;

//fix för att visa X antal objekt
antalobj = _root.antalobjekt;
//if (antalobj > 50) antalobj = 50;

bottom = main._y+mask_mc._height-(antalobj*211)-space;

//dölj scrollbar om det är 3 eller färre objekt
if (antalobj < 3) {
	trace('dragger visible: ' + _level0.holder.dragger._visible ) ;
	_level0.holder.dragger._visible = false;
	_level0.holder.bar._visible = false;
	_level0.holder.down_btn._visible = false;
	_level0.holder.up_btn._visible = false;
}



trace('top = '+top+', bottom = '+bottom);


dragger.onPress = function() {
	drag = true;
	this.startDrag(false, this._x, this._parent.y, this._x, this._parent.y+this._parent.bar._height-this._height);
	dragger.scrollEase();
};
dragger.onMouseUp = function() {
	this.stopDrag();
	drag = false;
};

bar.onPress = function() {
	drag = true;
	if (this._parent._ymouse>this._y+this._height-this._parent.dragger._height) {
		this._parent.dragger._y = this._parent._ymouse;
		this._parent.dragger._y = this._y+this._height-this._parent.dragger._height;
	} else {
		this._parent.dragger._y = this._parent._ymouse;
	}
	dragger.scrollEase();
};
bar.onMouseUp = function() {
	drag = false;
};

moveDragger = function (d) {
	if ((dragger._y>=y+bar._height-dragger._height && d == 1) || (dragger._y<=y && d == -1)) {
		clearInterval(draggerInterval);
	} else {
		dragger._y += d;
		dragger.scrollEase();
		updateAfterEvent();
	}
};

up_btn.onPress = function() {
	draggerInterval = setInterval(moveDragger, 5, -1);
};
down_btn.onPress = function() {
	draggerInterval = setInterval(moveDragger, 5, 1);
};
up_btn.onMouseUp = down_btn.onMouseUp=function () {
	clearInterval(draggerInterval);
};

MovieClip.prototype.scrollEase = function() {
	this.onEnterFrame = function() {
		if (Math.abs(dy) == 0 && drag == false) {
			delete this.onEnterFrame;
		}
		r = (this._y-y)/(bar._height-this._height);
		dy = Math.round((((top-(top-bottom)*r)-main._y)/speed)*friction);
		main._y += dy;
	};
};
