/*http://pajhome.org.uk/crypt/md5/sha1.js*/
var cl=8;/*8-ASCII;16-Unicode*/
function hex_sha1(s){return binb2hex(core_sha1(str2binb(s),s.length*cl));}
function sha1_ft(t,b,c,d){if(t<20) return (b&c)|((~b)&d);if(t<40) return b^c^d;if(t<60) return (b&c)|(b&d)|(c&d);return b^c^d;}
function sha1_kt(t){return (t<20)? 1518500249:(t<40)?1859775393:(t<60)?-1894007588:-899497514;}
function safe_add(x,y){var lsw=(x&0xFFFF)+(y&0xFFFF);var msw=(x>>16)+(y>>16)+(lsw>>16);return (msw<<16)|(lsw&0xFFFF);}
function rol(n,c){return (n<<c)|(n>>>(32-c));}
function core_sha1(x,l){x[l>>5]|=0x80<<(24-l%32);x[((l+64>>9)<<4)+15]=l;var w=Array(80);var a=1732584193;var b=-271733879;var c=-1732584194;var d=271733878;var e=-1009589776;for(var i=0;i<x.length;i+=16){var olda=a;var oldb=b;var oldc=c;var oldd=d;var olde=e;for(var j=0;j<80;j++){if(j<16)w[j]=x[i+j];else w[j]=rol(w[j-3]^w[j-8]^w[j-14]^w[j-16],1);var t=safe_add(safe_add(rol(a,5),sha1_ft(j,b,c,d)),safe_add(safe_add(e,w[j]),sha1_kt(j)));e=d;d=c;c=rol(b,30);b=a;a=t;}a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd);e=safe_add(e,olde);}return Array(a,b,c,d,e);}
function str2binb(s){var b=Array();var m=(1<<cl)-1;for(var i=0;i<s.length*cl;i+=cl)b[i>>5]|=(s.charCodeAt(i/cl)&m)<<(32-cl-i%32);return b;}
function binb2hex(a){var t="0123456789abcdef";var s="";for(var i=0;i<a.length*4;i++)s+=t.charAt((a[i>>2]>>((3-i%4)*8+4))&0xF)+t.charAt((a[i>>2]>>((3-i%4)*8))&0xF);return s;}