; http://www.postnummerservice.se/information/postnummerguide
;
; ändras årligen, runt den 1:a april
;
; senast Ver 2011.01
; http://www.datalinesweden.se/index.php?option=com_content&id=67
; 2012-06-08: posten har slutat släppa uppdateringar av detta program


win_title = PostnummerGuide 2011

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
            FileAppend, %A_LoopField%;, C:\Users\vbox\out.txt
        FileAppend, `n, C:\Users\vbox\out.txt
    }

    i := i + 1

}
