|*
 	Use: Just include this file & /call CheckFood from your programs main loop
	code borrowed from A_Druid_0 "feedme.inc" macro

	example INI_FILE entry:

[Playername]
Food=Water Flask
Water=Iron Ration

 hunger/thirst
 dont auto eat at 4532
 autodrinks not at 3096, but around 2900
 hunger: 6292-8660 = super fed
 thirst: 6134-8526 = super fed

*|

Sub Init_Metabolism
	/declare gimp_metabolism_loaded		int		outer	1
	/declare gimp_metabolism_activated	int		outer	1
	/declare eatFood					string	outer	${Ini[INI_FILE, ${Me.Name}, Food, NOT_FOUND]}
	/declare eatWater					string	outer	${Ini[INI_FILE, ${Me.Name}, Water, NOT_FOUND]}

	/if ((${eatFood.Length}<2) || (${eatWater.Length}<2)) {
		/echo # Error: You need to specify name of food/drink to force feed in INI_FILE
		/varset gimp_metabolism_activated 0
		/echo gimp_metabolism.inc disabled
		/return
	}

	/squelch /alias /metabolism /echo metabolism
	/echo gimp_metabolism.inc initialized (Hunger: ${Me.Hunger}, Thirst: ${Me.Thirst})
	/echo * Eating ${eatFood}, drinking ${eatWater}
/return

#Event ToggleMetabolism		"[MQ2] metabolism"
Sub Event_ToggleMetabolism
	/if (!${gimp_metabolism_loaded}) /call Init_Metabolism
	/varset gimp_metabolism_activated ${If[${gimp_metabolism_activated},0,1]}
	/echo gimp_metabolism.inc is ${If[${gimp_metabolism_activated},ACTIVATED,DEACTIVATED]}
/return

Sub CheckFood
	/if (!${gimp_metabolism_loaded}) /call Init_Metabolism
	|To stop the script from spamming cant find food when you are naked
	/if (!${InvSlot[chest].Item.ID}) /return

	|251 = first bag, upper left slot, 252 = first bag, upper right slot
	/declare tmp int local 0
	
	/varset tmp ${FindItem[${eatFood}].InvSlot}
	/if ((${Me.Hunger} < 3250) && (${tmp} != 251) && (${tmp} != 252) ) {
		/call ConsumeItem "${eatFood}"
		/return
	}

	/varset tmp ${FindItem[${eatWater}].InvSlot}
	/if ((${Me.Thirst} < 3250) && (${tmp} != 251) && (${tmp} != 252) ) /call ConsumeItem "${eatWater}"
/return

|ConsumeItem sub by someone else on mq2 forums
Sub ConsumeItem(string item)

	|/echo ConsumeItem ${item} called

	/if (!${gimp_metabolism_activated}) /return
	/if (${Cursor.ID} || ${Me.Casting.ID} || ${Window[LootWnd].Open} || ${Me.Moving}) /return

	/declare mypack string local ${FindItem[=${item}].InvSlot.Pack}
	/declare myslot string local ${FindItem[=${item}].InvSlot}

	/if (!${myslot}) {
		/echo # Error: Out of ${item}
		/return 
	}

	/echo Consuming ${item}

	:ClearCursor
	/autoinventory
	/if (${Cursor.ID}) /goto :ClearCursor

	/if (${mypack}) {
		/if (!${Window[${InvSlot[${mypack}].Name}].Open}) /nomodkey /itemnotify ${mypack} rightmouseup
		:WaitForBagOpen
		/nomodkey /ctrl /itemnotify ${myslot} leftmouseup
		/delay 1s
		/if (!${Cursor.ID}) /goto :WaitForBagOpen
	}

	/if (${mypack}) {
		|Item inside a bag
		/nomodkey /itemnotify ${mypack} leftmouseup
		/delay 1s
		/nomodkey /itemnotify ${mypack} rightmouseup
		/delay 1s
		/nomodkey /itemnotify ${mypack} leftmouseup
	} else {
		|Item in top level inventory slot
		/nomodkey /itemnotify ${myslot} rightmouseup
	}
/return