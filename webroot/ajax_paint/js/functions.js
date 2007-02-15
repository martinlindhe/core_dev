//sends debug output to firebug
function trace(s)
{
	if (this.firebug) console.debug(s);
}

function set_class(e,c){
	var o=document.getElementById(e);
	o.className=c;
}


/* draw tile named "cell$x_$y" in the color of colorselector.style.backgroundColor */
function draw_tile(x,y)
{
	var e=document.getElementById('colorselector');
	var col = e.style.backgroundColor;

	e=document.getElementById('c' + x + '_' + y);
	e.style.backgroundColor = col;

}

/* ritar alla tiles i samma färg */
function flood_fill()
{
	var e,col;

	trace('fill_tiles() called');

	e = document.getElementById('colorselector');
	col = e.style.backgroundColor;

	for (y=0; y<16; y++) {
		for (x=0; x<16; x++) {
			e = document.getElementById('c' + x + '_' + y);
			e.style.backgroundColor = col;
		}
	}
	
}

//enkodar bilden, sen skickar den till servern via ajax
function save_image()
{
	var e,col;
	
	var pal = new Array(); //color lookup palette

	trace('save_image() called');

	//create palette datablock
	for (y=0; y<16; y++) {
		for (x=0; x<16; x++) {
			e = document.getElementById('c' + x + '_' + y);
			col = e.style.backgroundColor;
			
			//kolla om col finns i pal arrayen
			var found = false;
			for (i in pal)
			{
				if (pal[i] == col) found = true;
			}

			if (!found) {
				//trace('adding ' + col + ' to pal array');
				pal.push(col);
			}
		}
	}

	trace('array is ' + pal.length + ' long');
	
	var pal_block = null;
	do {
		col = pal.pop();
		//trace( Chr(col.r) );
		//trace('popped ' + col[0] + ',' + col[1] + ',' + col[2]);
		trace('popped ' + col);
	} while (pal.length);
	
	trace('done:' + pal_block);
}

function calc_color()
{
	//calculates color from input boxes and sets it to div 'colorselector' background color

	var col = RGBtoHex(document.colors.r.value, document.colors.g.value, document.colors.b.value);

	var e=document.getElementById('colorselector');
	
	e.style.backgroundColor = col;

	trace('Selecting color ' + col);
}


function RGBtoHex(R,G,B)
{
	return '#' + toHex(R) + toHex(G) + toHex(B);
}

function toHex(n)
{
 if (n==null) return '00';
 n = parseInt(n);
 if (n==0 || isNaN(n)) return '00';

 n = Math.max(0, n);
 n = Math.min(n, 255);
 n = Math.round(n);

 return '0123456789ABCDEF'.charAt((n-n%16)/16)
      + '0123456789ABCDEF'.charAt(n%16);
}  