; postnummerguide2010_win.exe, från:
;
; http://www.posten.se/i/kampanj/postnummersok/postnummersok.jspv
;
; Nyare versioner även:
; http://www.datalinesweden.se/index.php?option=com_content&id=67
;
; ändras årligen, runt den 1:a april
;


win_title = PostnummerGuide 2010

IfWinNotExist, %win_title%
{
	Run Notepad
}

WinActivate, %win_title%

i := 10000

While i <= 99999 {

	; press Clear button
	ControlClick, Button2
	Sleep, 25 ; 10ms

	; set focus on postnummer field
	ControlFocus, Edit2
	SendInput, {Backspace 5}%i%{Enter}
	Sleep, 25 ; 10ms

	ControlGet, cnt, List, Count, SysListView321
	if (cnt < 1) {
		Sleep, 120 ; 100ms
	}

	; read listview
	ControlGet, out, List, , SysListView321

	Loop, Parse, out, `n  ; Rows are delimited by linefeeds (`n).
	{
		RowNumber := A_Index
		Loop, Parse, A_LoopField, %A_Tab%  ; Fields (columns) in each row are delimited by tabs (A_Tab).
			;MsgBox Row #%RowNumber% Col #%A_Index% is %A_LoopField%.
			FileAppend, %A_LoopField%;, out.txt
		FileAppend, `n, out.txt
	}

	i := i + 1

}
