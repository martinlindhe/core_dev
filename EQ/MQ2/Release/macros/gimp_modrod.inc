|just include & call CheckModRod in main loop.

|Item ID of a mod rod
#Define MODROD_ID	3426
#Define ELEMMODROD_ID	52674

#Event UsedModRod			"You experience a mystical transvergance.#*#"
Sub Event_UsedModRod(string Line)
	/echo [${Time}] Mod rod used (+360 mana)
	/call CheckModRod
/return

#Event UsedElemModRod	"You experience an elemental transvergance.#*#"
Sub Event_UsedElemModRod(string Line)
	/echo [${Time}] Mod rod used (+656 mana)
	/call CheckModRod
/return

Sub CheckModRod
	/if (!${gimp_modrod_loaded}) {
		/declare gimp_modrod_loaded		int		outer	1
		/echo gimp_modrod.inc initialized
	}

	/if ((${Cursor.ID} == MODROD_ID || ${Cursor.ID} == ELEMMODROD_ID) && !${Window[ConfirmationDialogBox].Open}) {
		/echo Autoinventorying mod rod
		/delay 3s
		/if (${Cursor.ID} == MODROD_ID || ${Cursor.ID} == ELEMMODROD_ID) /autoinventory
	}
/return