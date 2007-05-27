//////////////////////////////////////////////////////////////
//
// JavaScript Grid Widget
// (C).2005/2006 by Kai-jens Meyer <kjm@inline.de>
//
// Version 1.00
//
// Die Verwendung dieser Sourcen ist ohne schriftliche 
// Genehmigung nicht gestattet!
// 
// Weitere Infos unter http://www.goweb.de/grid.htm
//
//////////////////////////////////////////////////////////////

// Document Shortcut
var dcm = document;

// Wrapper für getElementById
function gE(id) {return dcm.getElementById(id);};

// Wrapper für die createTextNode Funktion
function cN(tx) {return dcm.createTextNode(tx);};

// Wrapper für getElementsByTagName
function eB(o,t,e) {var r = o.getElementsByTagName(t); return (e==null)?r:r[e];};

// AppendChild wrapper
function aC(c,t) {c.appendChild(t);};

// Wrapper für die createElement Funtion
function cE(el) {return dcm.createElement(el);};

// Wrapper für setTimeout
function sT(f,v) {return setTimeout(f,v);};

// Objekt
var Grid = {

  // Aktuelle Version
  version: "1.00",

  // Interne Variablen
  loaded: false,
  gCount: 0, cellID: 0,
  ismsie: false,
  drawmode: false,
  avCellID: 0,
  overcell: null,
  overcellid: null,
  ghidden: false,

  // Editieren erlaubt?
  allowedit: false,
  changes: false,

  // Farbabhebung
  darkcol:  "#f7f7f7",
  lightcol: "#ffffff",

  // Letzte Sortierung
  lso: null,

  // Dimensionen
  rows: 0,

  // Sortierkriterium
  sortby: 0,

  // Array für die Daten
  celldata: new Array(),
  rowdata: new Array(),

  // Verstecke Spalten
  hiddencols: new Array(),

  // 
  // Event programmieren
  //
  eH: function(obj, evType, fn, useCapture) {

    // Objekt existiert nicht
    if (!obj) return;
    if (obj.addEventListener) {
      obj.addEventListener(evType, fn, useCapture);
      return true;
    } else if (obj.attachEvent) {
      var r = obj.attachEvent('on'+evType,fn);
      return r;
    } else {
      obj['on'+evType] = fn;
    }
  },

  //
  // Zellstruktur festlegen
  //
  addcell: function(desc,width,num) {
    if (desc==null) return 0;
    Grid.celldata[Grid.celldata.length] = desc+"\t"+((width==null)?0:width)+"\t"+((num==null)?0:num);
    return 1;
  },

  //
  // Neue Zelle erzeugen
  //
  makecell: function(name,width,desc,link,nr,rownr,col) {

    // Neues Element erstellen
    var d = cE("div"); Grid.setclass(d,name);
    d.id = "cellid_"+Grid.cellID; Grid.cellID++;
    d.sort = 0; if (nr!=null) d.nr = nr;
    if (width) d.style.width=width+"px";
    if (desc) d.innerHTML = desc;

    // Zeile farbig hinterlegen
    if (rownr!=null) 
      d.style.background = (rownr%2==0)?Grid.lightcol:Grid.darkcol;
    if (col != null) d.column = col;

    // Diese Zelle verlinken?
    if (link==1) {
      var a = cE("a"); Grid.setclass(a,name);
      a.href = "javascript:void(null)"; aC(a,d);
      a['onmouseover'] = Grid.EVcellover;
      a['onmouseout'] = Grid.EVcellout;
      a['onclick'] = Grid.EVcellsort;
    } else if (link==2) {
      var a = cE("a"); Grid.setclass(a,name);
      a.href = "javascript:void(null)"; aC(a,d);
      a['onclick'] = Grid.EVrowremove;
      a['onmouseover'] = Grid.EVrowover;
      a['onmouseout'] = Grid.EVrowout;
    }

    // Darf editiert werden
    if (nr==null && rownr!=null && Grid.allowedit) {
//      d['onmouseover'] = Grid.EVedit;
      d['onclick'] = Grid.EVedit; d.rownr = rownr;
      d.style.cursor = "text"; d.clickable = true;
    }

    // Neue Zelle übergeben
    return (link>0)?a:d;
  },

  //
  // Get event object - Crossbrowser version
  //
  getEvent: function(e) {
    if (typeof e == 'undefined') e = window.event;
    if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
    if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
    return e;
  },

  //
  // Zelleninhalt editieren
  //
  EVedit:function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t || !t.clickable) return;
    var c = t.innerHTML; t.innerHTML = "";
    var i = cE("input"); aC(t,i); 
    var a = Grid.celldata[t.column].split("\t");
    i.value = c; i.focus(); i.id = "i_"+t.id
    i.style.width = a[1]+"px"; i.oldvalue = c;

    // Klasse setzen
    Grid.setclass(i,"gridin");
    i['onblur'] = Grid.EVeditleave;
  },

  //
  // Editieren beendet
  //
  EVeditleave: function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    var n = t.value; var i = t.id;

    // Änderungen?
    if (n != t.oldvalue) Grid.changes = true;

    // Eingabefeld löschen
    t.parentNode.removeChild(t);
    var d = gE(i.substr(2,i.length-2));
    d.innerHTML = n; 
    var b = Grid.celldata[d.column].split("\t");
    var a = Grid.rowdata[d.rownr];
    a[d.column] = (parseInt(b[2])==1)?parseInt(n):n;
    Grid.rowdata[d.rownr] = a;
  },

  //
  // Ganze Zeile entfernen
  //
  EVrowremove:function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t) return;

    // Zeile aus dem Array entfernen
    for (i=(t.nr+1);i<Grid.rowdata.length;i++) 
      Grid.rowdata[i-1] = Grid.rowdata[i];

    // Letztes Element entfernen
    Grid.rowdata.pop();

    // Grid neu anzeigen
    var c = Grid.redraw();
    for (i=c-1;i<c+Grid.celldata.length;i++) {
      var d = gE("cellid_"+i);
      if (d) d.parentNode.removeChild(d);
    }
  },

  //
  // Mausezeiger über einer Überschrift
  //
  EVcellover:function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t) return;
    Grid.setclass(t,"gridrow0on");
    Grid.overcell = true; Grid.overcellid = t.id;
  },

  //
  // Mausezeiger verlässt eine Überschrift
  //
  EVcellout:function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t) return;
    Grid.setclass(t,"gridrow0");
    Grid.overcell = null;
  },

  //
  // Mausezeiger über einer Zeile
  //
  EVrowover:function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t) return;
    Grid.setclass(t,"gridcol0on");
  },

  //
  // Mausezeiger verlässt eine Zeile
  //
  EVrowout:function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t) return;
    Grid.setclass(t,"gridcol0");
  },

  //
  // Grid anzeigen
  //
  Draw: function() {

    // Alte Anzeige entfernen und neuen Container vorbereiten
    var d = gE("grid_"+Grid.gCount);
    if (d) d.parentNode.removeChild(d);
    Grid.gCount++;
    var d = cE("div"); d.id = "grid_"+Grid.gCount;
    aC(gE("grid"),d);

    // Zähler
    aC(d,Grid.makecell('gridrow0',20," &nbsp;",false));

    // Anzahl Zeilen und Spalten
    for (var i=0;i<Grid.celldata.length;i++) {
      var c = Grid.celldata[i].split("\t");
      aC(d,Grid.makecell('gridrow0',c[1],c[0],1,i));
    }

    // Erste verfügbare CellID
    Grid.avCellID = Grid.cellID;

    // Hard break
    var b = cE((Grid.ismsie)?"div":"br"); b.style.clear = "left"; aC(d,b);

    // Drawmode ist aktiv
    Grid.drawmode = true;

    // Existieren schon Einträge?
    if (!Grid.rowdata.length) return;

    // Daten anzeigen
    for (var i=0;i<Grid.rowdata.length;i++) Grid.drawrow(i);
  },

  //
  // Zeile im Grid anzeigen
  //
  drawrow: function(n) {

    // Grid holen
    var d = gE("grid_"+Grid.gCount);
    if (!d) return;

    // Infoelement anhängen
    aC(d,Grid.makecell('gridcol0',20,n+1,2,n));

    // Daten anzeigen
    var a = Grid.rowdata[n];
    for (var i=0;i<Grid.celldata.length;i++) {
      var g = Grid.celldata[i].split("\t");
      var e = Grid.makecell('gridcolx',g[1],a[i],false,null,n,i);
      if (g[2]==1) e.style.textAlign = "center";
      aC(d,e);
    }

    // Hard break
    var b = cE((Grid.ismsie)?"div":"br"); b.style.clear = "left"; aC(d,b);
  },

  //
  // Neue Zeile einfügen
  //
  addrow: function(r) {

    // Daten einfügen
    Grid.rowdata[Grid.rowdata.length] = r[0];

    // Zeile anzeigen
    if (Grid.drawmode) Grid.drawrow(Grid.rowdata.length-1); 
  },

  //
  // Reverse compare function
  //
  revcompareFunc: function(a1, a2) {
    return a1[Grid.sortby] < a2[Grid.sortby] ? -1 :
           a1[Grid.sortby] > a2[Grid.sortby] ? 1 : 0;
  },

  //
  // Compare function
  //
  compareFunc: function(a1, a2) {
    return a1[Grid.sortby] > a2[Grid.sortby] ? -1 :
           a1[Grid.sortby] < a2[Grid.sortby] ? 1 : 0;
  },

  //
  // Grid umsortieren nach einer bestimmten Zelle
  //
  EVcellsort: function(ev) {
    var e = Grid.getEvent(ev);
    var t = e.target?e.target:e.srcElement;
    if (!t) return;  

    // Keine Suche definiert?
    if (t.sort <= 0) {
      t.sort = 1; var sd = 0;
      var si = "down.gif";
    } else if (t.sort == 1) {
      t.sort = -1; var sd = 1;
      var si = "up.gif";
    } 

    // Alte Grafik entfernen
    if (Grid.lso && t != Grid.lso) {
      Grid.lso.style.backgroundImage="url('./empty.gif')";
      Grid.lso.sort = 0;
    }

    // Sortiermethode anzeigen
    t.style.backgroundImage="url('./"+si+"')";
    Grid.lso = t; Grid.sortby = t.nr;

    // Inhalt neu sortieren
    if (Grid.rowdata.length<=1) return;
    Grid.rowdata = Grid.rowdata.sort((sd)?Grid.revcompareFunc:Grid.compareFunc);

    // Neu anzeigen
    Grid.redraw();
  },

  //
  // Grid neu anzeigen
  //
  redraw: function() {

    // Grid neu anzeigen
    var c = Grid.avCellID+1;
    for (i=0;i<Grid.rowdata.length;i++) {
      var a = Grid.rowdata[i];
      for (var j=0;j<a.length;j++) {
        gE("cellid_"+c).innerHTML = a[j]; c++;
      }
      c++;
    }
    return c;
  },

  //
  // DOM Tree löschen
  //
  treeclear: function(r) {
    gE("ctc").parentNode.removeChild(gE("ctc")); 
    var d = cE("div"); d.id = "ctc"; d.innerHTML = "<!-- -->";
    aC(gE("contextmenu"),d);
  },

  //
  // Eventbubbling deaktivieren
  //
  nobubble: function(e) {

    // Keine 'aufsteigenden' Blasen bitte...
    if (window.event) {
      window.event.cancelBubble = true;
      window.event.returnValue = false;
    } else {
      e.stopPropagation();
      e.preventDefault();
    }
  },

  //
  // Contextmenu entfernen
  //
  ctxoff: function(ev) { 
    var c = gE("contextmenu"); if (!c) return;
    if (c.isopen) gE("contextmenu").style.display="none";
    c.isopen = false;
  },

  //
  // Link ins Kontextmenü einfügen
  //
  addlink: function(r,l,t,hr) {
    if (hr==2) aC(r,cE("hr"));
    if (l!=null && l.length==0) {
      var a = cE("b");
    } else {
      var a = cE((l==null)?"span":"a"); if (l!=null) a.href = l;
    }
    a.innerHTML = t; aC(r,a); aC(r,cE("br"));
    if (hr!=null) aC(r,cE("hr"));
  },

  //
  // Grid nicht mehr anzeigen / anzeigen
  //
  hideall: function() {
    gE("grid").style.display = (Grid.ghidden)?"block":"none";
    Grid.ghidden = (Grid.ghidden)?false:true;
  },

  //
  // Spalte verstecken
  //
  hidecol: function(c,o) {

    // Spalte verstecken
    if (c==null) {
      if (!Grid.overcellid) return;
      var d = gE(Grid.overcellid); if (!d) return;
      d.style.display = "none"; 

      // Spaltennummer holen
      var r = parseInt(d.id.substr(7,d.id.length-7));

      // Spalte speichern
      Grid.hiddencols[Grid.hiddencols.length] = r+"\t"+Grid.celldata[r-1];    
      Grid.overcellid = null; 

      // Basis ID
      var b = r+1;
    } else {
      gE("cellid_"+c).style.display = "block"; var b = parseInt(c)+1;

      // Infomationen updaten
      for (i=o+1;i<=Grid.hiddencols.length-1;i++) 
        Grid.hiddencols[i-i] = Grid.hiddencols[i];
      Grid.hiddencols.pop();
    }

    // Daten anzeigen od. verstecken
    var f = -1;
    for (i=0;i<Grid.rowdata.length;i++) {
      gE("cellid_"+(Grid.celldata.length+(i*Grid.celldata.length+b))).style.display=(c==null)?"none":"block"; 
      b++;
    }
  },

  //
  // Grid exportieren in CSV
  //
  cvsexport: function() {
    var w = window.open();
    var d = w.document; d.open();

    // Daten exportieren
    for (var i=0;i<Grid.rowdata.length;i++) {
      var a = Grid.rowdata[i];
      for (var j=0;j<a.length;j++) {
        var b = Grid.celldata[j].split("\t");
        d.write(((b[2]!=1)?"\"":"")+a[j]+((b[2]!=1)?"\"":"")+";");
      }
      d.writeln("<br>");
    }

    d.close();
  },

  //
  // Mausklick Handler
  //
  EVclick: function(ev) {

    // Eventobjekt holen
    var e = Grid.getEvent(ev); Grid.ctxoff();
    var t = e.target?e.target:e.srcElement;

    // Rechte Maustaste?
    if (gE("contextmenu") && ((e.type && e.type == "contextmenu") || (e.button && e.button == 2) || (e.which && e.which == 3))) {
      var c = gE("contextmenu"); c.style.display = "block";
      c.style.top = (e.clientY-1)+"px";
      c.style.left = (e.clientX-1)+"px";
      c.isopen = true; c.style.zIndex = "1000";

      // Links erstellen
      Grid.treeclear(r);
      var r = gE("ctc"); 

      // Links einfügen
      Grid.addlink(r,'',"Grid "+Grid.version+" - Kontextmenü",true);

      // Gibt es Änderungen?
      if (Grid.allowedit && Grid.changes) {
        try {
          var v = GridSaveChanges;
        } catch (e) { 
          var v = "";
        }

        // Funktion ist verfügbar
        if (v.length) 
          Grid.addlink(r,null,"Änderungen speichern");
        else
          Grid.addlink(r,"javascript:GridSaveChanges()","Änderungen speichern");
      }

      // Zusatzfunktionen
      Grid.addlink(r,(Grid.rowdata.length)?"javascript:Grid.cvsexport()":'',"CVS Export");
      Grid.addlink(r,"javascript:Grid.hideall()",(Grid.ghidden)?"Grid einblenden":"Grid ausblenden");
      Grid.addlink(r,(Grid.overcell)?"javascript:Grid.hidecol()":null,"Spalte verstecken");

      // Gibt es vesteckte Spalten?
      if (Grid.hiddencols.length) {
        Grid.addlink(r,'',"Versteckte Spalten",2);
        for (var i=0;i<Grid.hiddencols.length;i++) {
          var a = Grid.hiddencols[i].split("\t"); 
          Grid.addlink(r,"javascript:Grid.hidecol('"+a[0]+"',"+i+")",a[1]);
        }
      }

      // Keine 'aufsteigenden' Blasen bitte...
      Grid.nobubble(e);

      // Keine weitere Verarbeitung
      return false;
    }

    // Weiterverarbeiten
    return true;
  },

  //
  // Grid initialisieren
  //
  init: function() {

    // Keine DOM Unterstützung
    if (!dcm.getElementById) return;

    // Fehlender DIV Container
    if (!gE("grid")) {
      alert("Sie müssen noch einen DIV Container festlegen!\nBeispiel: <div id='grid'></div>");
      return;
    }

    // Contextmenü erstellen
    if (!gE("contextmenu")) {
      var d = cE("div"); d.id = "contextmenu";
      aC(eB(dcm,"body",0),d); 
    }

    // Contextmenü Inhalt
    if (!gE("ctc")) {
      var d = cE("div"); d.id = "ctc";
      aC(gE("contextmenu"),d);
    }

    // Rechte Maustaste
    Grid.eH(document,"click",Grid.EVclick,false);
    Grid.eH(document,"contextmenu",Grid.EVclick,false);

    // System geladen
    Grid.loaded = true;
  },

  //
  // Klasse zuweisen (Crossbrowser Version)
  //
  setclass: function(o,c) { 
    if (Grid.ismsie) {
      o.setAttribute("className",c); 
    } else {
      o.setAttribute("class",c); 
    }
  }
}

// Microsoft Browser?
if (!window.opera && navigator.userAgent.indexOf("MSIE")!=-1)
  Grid.ismsie = true;

//
// System nach dem laden der Seite initialisieren
//
Grid.eH(window,'load',Grid.init,false);
