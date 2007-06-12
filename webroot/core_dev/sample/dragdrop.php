
<STYLE>
.DragContainer {
	BORDER-RIGHT: #669999 2px solid; PADDING-RIGHT: 5px; BORDER-TOP: #669999 2px solid; PADDING-LEFT: 5px; FLOAT: left; PADDING-BOTTOM: 0px; MARGIN: 3px; BORDER-LEFT: #669999 2px solid; WIDTH: 100px; PADDING-TOP: 5px; BORDER-BOTTOM: #669999 2px solid
}
.OverDragContainer {
	BORDER-RIGHT: #669999 2px solid; PADDING-RIGHT: 5px; BORDER-TOP: #669999 2px solid; PADDING-LEFT: 5px; FLOAT: left; PADDING-BOTTOM: 0px; MARGIN: 3px; BORDER-LEFT: #669999 2px solid; WIDTH: 100px; PADDING-TOP: 5px; BORDER-BOTTOM: #669999 2px solid
}
.OverDragContainer {
	BACKGROUND-COLOR: #eee
}
.DragBox {
	BORDER-RIGHT: #000 1px solid; PADDING-RIGHT: 2px; BORDER-TOP: #000 1px solid; PADDING-LEFT: 2px; FONT-SIZE: 10px; MARGIN-BOTTOM: 5px; PADDING-BOTTOM: 2px; BORDER-LEFT: #000 1px solid; WIDTH: 94px; CURSOR: pointer; PADDING-TOP: 2px; BORDER-BOTTOM: #000 1px solid; FONT-FAMILY: verdana, tahoma, arial; BACKGROUND-COLOR: #eee
}
.OverDragBox {
	BORDER-RIGHT: #000 1px solid; PADDING-RIGHT: 2px; BORDER-TOP: #000 1px solid; PADDING-LEFT: 2px; FONT-SIZE: 10px; MARGIN-BOTTOM: 5px; PADDING-BOTTOM: 2px; BORDER-LEFT: #000 1px solid; WIDTH: 94px; CURSOR: pointer; PADDING-TOP: 2px; BORDER-BOTTOM: #000 1px solid; FONT-FAMILY: verdana, tahoma, arial; BACKGROUND-COLOR: #eee
}
.DragDragBox {
	BORDER-RIGHT: #000 1px solid; PADDING-RIGHT: 2px; BORDER-TOP: #000 1px solid; PADDING-LEFT: 2px; FONT-SIZE: 10px; MARGIN-BOTTOM: 5px; PADDING-BOTTOM: 2px; BORDER-LEFT: #000 1px solid; WIDTH: 94px; CURSOR: pointer; PADDING-TOP: 2px; BORDER-BOTTOM: #000 1px solid; FONT-FAMILY: verdana, tahoma, arial; BACKGROUND-COLOR: #eee
}
.miniDragBox {
	BORDER-RIGHT: #000 1px solid; PADDING-RIGHT: 2px; BORDER-TOP: #000 1px solid; PADDING-LEFT: 2px; FONT-SIZE: 10px; MARGIN-BOTTOM: 5px; PADDING-BOTTOM: 2px; BORDER-LEFT: #000 1px solid; WIDTH: 94px; CURSOR: pointer; PADDING-TOP: 2px; BORDER-BOTTOM: #000 1px solid; FONT-FAMILY: verdana, tahoma, arial; BACKGROUND-COLOR: #eee
}
.OverDragBox {
	BACKGROUND-COLOR: #ffff99
}
.DragDragBox {
	BACKGROUND-COLOR: #ffff99
}
.DragDragBox {
	FILTER: alpha(opacity=50); BACKGROUND-COLOR: #ff99cc
}
LEGEND {
	FONT-WEIGHT: bold; FONT-SIZE: 12px; COLOR: #666699; FONT-FAMILY: verdana, tahoma, arial
}
FIELDSET {
	PADDING-RIGHT: 3px; PADDING-LEFT: 3px; PADDING-BOTTOM: 3px; PADDING-TOP: 3px
}
.History {
	FONT-SIZE: 10px; OVERFLOW: auto; WIDTH: 100%; FONT-FAMILY: verdana, tahoma, arial; HEIGHT: 82px
}
.miniDragBox {
	FLOAT: left; MARGIN: 0px 5px 5px 0px; WIDTH: 20px; HEIGHT: 20px
}
</STYLE>


<FIELDSET>
<LEGEND>Demo - Drag and Drop any item</LEGEND>
<DIV>
<DIV class=DragContainer id=itemholder_1 overClass="OverDragContainer">
<DIV class=DragBox id=item_1 overClass="OverDragBox" dragClass="DragDragBox">Item #1</DIV>
<DIV class=DragBox id=item_2 overClass="OverDragBox" dragClass="DragDragBox">Item #2</DIV>
<DIV class=DragBox id=item_3 overClass="OverDragBox" dragClass="DragDragBox">Item #3</DIV>
<DIV class=DragBox id=item_4 overClass="OverDragBox" dragClass="DragDragBox">Item #4</DIV></DIV>

<DIV class=DragContainer id=itemholder_2 overClass="OverDragContainer">
<DIV class=DragBox id=item_5 overClass="OverDragBox" dragClass="DragDragBox">Item #5</DIV>
<DIV class=DragBox id=item_6 overClass="OverDragBox" dragClass="DragDragBox">Item #6</DIV>
<DIV class=DragBox id=item_7 overClass="OverDragBox" dragClass="DragDragBox">Item #7</DIV>
<DIV class=DragBox id=item_8 overClass="OverDragBox" dragClass="DragDragBox">Item #8</DIV></DIV>
<DIV class=DragContainer id=itemholder_3 overClass="OverDragContainer">
<DIV class=DragBox id=item_9 overClass="OverDragBox" dragClass="DragDragBox">Item #9</DIV>
<DIV class=DragBox id=item_10 overClass="OverDragBox" dragClass="DragDragBox">Item #10</DIV>
<DIV class=DragBox id=item_11 overClass="OverDragBox" dragClass="DragDragBox">Item #11</DIV>
<DIV class=DragBox id=item_12 overClass="OverDragBox" dragClass="DragDragBox">Item #12</DIV>
</DIV>
</DIV>
</FIELDSET>
