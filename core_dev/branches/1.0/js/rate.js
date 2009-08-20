/* script from http://www.nofunc.com/AJAX_Star_Rating/ */
/* AJAX Star Rating v1.0.2, Programming by Ulyses */
/* Updated February 7th, 2007 */

function $idPtr(o) { return((typeof(o)=='object'?o:document).getElementById(o)); }
function $S(o) { return($idPtr(o).style); }
function agent(v) { return(Math.max(navigator.userAgent.toLowerCase().indexOf(v),0)); }
function abPos(o) { var o=(typeof(o)=='object'?o:$idPtr(o)), z={X:0,Y:0}; while(o!=null) { z.X+=o.offsetLeft; z.Y+=o.offsetTop; o=o.offsetParent; }; return(z); }
function XY(e,v) { var o=agent('msie')?{'X':event.clientX+document.body.scrollLeft,'Y':event.clientY+document.body.scrollTop}:{'X':e.pageX,'Y':e.pageY}; return(v?o[v]:o); }

var star={
	/* Mouse Events */
	'cur':function(e,o) { if(star.stop) { star.stop=0;
		document.onmousemove=function(e) { var n=star.num;
			var p=abPos($idPtr('star'+n)), x=XY(e), oX=x.X-p.X, oY=x.Y-p.Y; star.num=o.id.substr(4);
			if(oX<1 || oX>84 || oY<0 || oY>19) { star.stop=1; star.revert(); }
			else {
				$S('starCur'+n).width=oX+'px';
				$S('starUser'+n).color='#111';
				$idPtr('starUser'+n).innerHTML=Math.round(oX/84*100)+'%';
			}
		};
	} },
	'update':function(e,o,t,id) { var n=star.num, v=parseInt($idPtr('starUser'+n).innerHTML);
		n=o.id.substr(4); $idPtr('starCur'+n).title=v;
		//req=new XMLHttpRequest(); req.open('GET','/AJAX_Star_Vote.php?vote='+(v/100),false); req.send(null);
		ajax_rate(t,id,v);
	},
	'revert':function() {
		var e=document.getElementById('starCur'+n);
		if (!e) return;
		var n=star.num, v=parseInt(e.title);
		e.style.width=Math.round(v*84/100)+'px';
		$idPtr('starUser'+n).innerHTML=(v>0?Math.round(v)+'%':'');
		$idPtr('starUser'+n).style.color='#888';
		document.onmousemove='';
	},
	/* Data */
	'stop':1,
	'num':0
};
