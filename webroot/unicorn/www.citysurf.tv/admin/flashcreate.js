function createFlash(swf, swfName, swfW, swfH, swfVar) {
if(!swfVar) swfVar = '';
document.write('<' + 'object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="' + swfName + '" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="' + swfW + '" height="' + swfH + '" align="middle">' +
'<' + 'param name="allowScriptAccess" value="sameDomain" />' +
'<' + 'param name="FlashVars" value="' + swfVar + '" />' +
'<' + 'param name="menu" value="false" />' +
'<' + 'param name="movie" value="' + swf + '" />' +
'<' + 'param name="quality" value="high" />' +
'<' + 'embed src="' + swf + '" menu="false" FlashVars="' + swfVar + '" quality="high" width="' + swfW + '" height="' + swfH + '" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />' +
'</' + 'object>');
}
//'<' + 'param name="bgcolor" value="#ffffff" />' +bgcolor="#ffffff" 