#chat tell

Sub Event_Chat
	/echo [${Time}] Got a tell, stopping
	/endmacro
/return

Sub CheckGM
	:GMCheck
	/if (${SpawnCount[gm]}) {
		/echo A GM or Guide is in zone, pausing for 10 minutes
		/if (${Me.Standing} /sit
		/delay 10m
		/goto :GMCheck
	}
/return

Sub MedBreak
	/if (${Me.Standing}) /sit
	:MedMore
	/if (${Me.PctMana}<100) /goto :MedMore
/return

Sub OpenPacks
	|todo: dont open new tradeskill container interfaces
	/declare i int local
	/for i 1 to 8
		/if (${InvSlot[pack${i}].Item.Container}) {
			/if (!${Window[Pack${i}].Open}) /itemnotify pack${i} rightmouseup
		}
	/next i
/return

Sub ClosePacks
	/declare i int local
	/for i 1 to 8
		/if (${InvSlot[pack${i}].Item.Container}) {
			/if (${Window[Pack${i}].Open}) /itemnotify pack${i} rightmouseup
		}
	/next i
/return

Sub SaveCursorItems
	:CheckCursor
	/if (${Cursor.ID}) {
		/echo Saving ${Cursor.Name}
		/autoinventory
		/delay 5
		/goto :CheckCursor
	}
/return
