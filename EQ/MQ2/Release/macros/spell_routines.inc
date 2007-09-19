|http://www.macroquest2.com/phpBB2/viewtopic.php?t=7568
| spell_routines.inc
|
| Written by Rusty~
| Last Modified 11/07/2004
|
| Features:
| - Casts spells, clicks items, or uses AA abilities for you
| - Allows back to back casting without waiting on spell gems to pop all the way up
| - Will interrupt spell if target dies while casting. If on a mount, it will dismount and duck if the time left
|   is greater than 7 seconds, else it will move forward a bit to interrupt, then move you back
|    ** IMPORTANT: if you don't want to interrupt a spell while mounted, put this at the top of your macro: **
|    **   /declare noInterrupt int outer 1                                                                  **
| - Allows you to use items in bags. Equips item, clicks it, then returns it to its previous location
| - Lets you set how long you want to keep trying to cast the spell (defaults to 0)
|   If the spell is interrupted before the given time, it will recast, else it will return CAST_INTERRUPTED
| - Lets you call a custom subroutine while waiting for spell to finish casting
|   Try to keep custom subroutines very small. A common use would be to interrupt the spell if a certain condition is true
| - This file also includes a sub named Interrupt. You can call this to interrupt any spell you're casting instantly.
| - You can also use the SwapItem sub included in this to swap items to certain slots
| - Added EquipItem sub to easily equip items in your main Inventory slots.
| - Note: if you don't want this to cast spells while you're invis, in your main macro have this at the top:
|      /declare noInvis int outer 1
|   This will make it return CAST_INVIS if you're invis
|
|  Below is a list of outer scope variables you can access in your macros:
|      refreshTime        - How much time is left till you're done recovering from casting
|      castEndTime        - How much time left till you're done casting the current spell... usable in custom spell Subs
|      spellNotHold       - 1 if your last spell didn't take hold, 0 otherwise
|      spellRecastTime1-9 - How much time left till that spell is back up
|
|======================================================================================================================
|  EquipItem:  An easier way to equip items you have in bags ( useful for weapons or focus items )
|              slot name is optional. If not given, it will equip it in the first possible spot
|
|    Usage:   
|        /call EquipItem "item name|slotname"
|
|        Returns: "old item name|slotname"
|    Examples:
|   
|    To Equip Sharp Ended Broken Lever when you have Serpent of Vindication equiped:
|        /call EquipItem "Sharp Ended Broken Lever"
|    It will return "Staff of Vindication|mainhand"
|    To reequip the original item, you can save the return in a variable, and then use it later like this:
|       /varset oldPrimary ${Macro.Return}
|       | ... do stuff here with your new item equiped
|       /call EquipItem ${oldPrimary}
|
|======================================================================================================================
|  SwapItem:  a subroutine which is used in the Cast sub itself. You don't need to do this to cast an item in a bag
|             but you can call it in your macro to SwapItems (such as weapons or focus items)
|    Usage:   
|        /call SwapItem "item name" slotname|slotID
|    Examples:
|   
|    To swap Darkblade of the Warlord to your main hand:
|        /call SwapItem "Darkblade of the Warlord" mainhand
|
|    To swap stat food in one bag with other food in another bag:
|        /call SwapItem "Bristlebanes Party Platter" ${FindItem[halas 10lb meat pie].InvSlot}
|
|======================================================================================================================
|  Cast: the main subroutine that casts spells or items for you
|     Usage:
|        /call Cast "spellname|itemname|AAname|AA#" [item|alt|gem#] [give up time][s|m] [custom subroutine name]
|     Examples:
|
|     To cast Howl of Tashan and mem it in slot 3 if not memmed:
|       /call Cast "Howl of Tashan" gem3
|
|     To cast Arcane Rune and keep trying for 7 seconds, in case of interrupts.
|       /call Cast "Arcane Rune" gem5 7s
|
|     To click Grim Aura earring that's in a bag:
|       /call Cast "Shrunken Goblin Skull Earring" item
|
|     To use AA ability Eldritch Rune:
|       /call Cast "Eldritch Rune" alt
|         or
|       /call Cast "173" alt
|
|     To call a subroutine that interrupts CH if target gets healed before it lands:
|       /call Cast "Complete Healing" gem1 0 CheckHP
|     Then in your macro have somewhere:
|       Sub CheckHP
|          /if ( ${Target.PctHPs}>=80 ) /call Interrupt
|       /return
|
| Returns these values:
|----------------------+----------------------------------------------------------------------+
| CAST_CANCELLED       | Spell was cancelled by ducking (either manually or because mob died) |
| CAST_CANNOTSEE       | You can't see your target                                            |
| CAST_IMMUNE          | Target is immune to this spell                                       |
| CAST_INTERRUPTED     | Casting was interrupted and exceeded the given time limit            |
| CAST_INVIS           | You were invis, and noInvis is set to true                           |
| CAST_NOTARGET        | You don't have a target selected for this spell                      |
| CAST_NOTMEMMED       | Spell is not memmed and you gem to mem was not specified             |
| CAST_NOTREADY        | AA ability or spell is not ready yet                                 |
| CAST_OUTOFMANA       | You don't have enough mana for this spell!                           |
| CAST_OUTOFRANGE      | Target is out of range                                               |
| CAST_RESISTED        | Your spell was resisted!                                             |
| CAST_SUCCESS         | Your spell was cast successfully! (yay)                              |
| CAST_UNKNOWNSPELL    | Spell/Item/Ability was not found                                     |
|----------------------+----------------------------------------------------------------------+

#event BeginCast "You begin casting#*#"
#event Collapse "Your gate is too unstable, and collapses.#*#"
#event FDFail "#1# has fallen to the ground.#*#"
#event Fizzle "Your spell fizzles#*#"
#event Immune "Your target is immune to changes in its attack speed#*#"
#event Immune "Your target is immune to changes in its run speed#*#"
#event Immune "Your target cannot be mesmerized#*#"
#event Interrupt "Your casting has been interrupted#*#"
#event Interrupt "Your spell is interrupted#*#"
#event NoHold "Your spell did not take hold#*#"
#event NoLOS "You cannot see your target.#*#"
#event NoTarget "You must first select a target for this spell!#*#"
#event NotReady "Spell recast time not yet met.#*#"
#event OutOfMana "Insufficient Mana to cast this spell!#*#"
#event OutOfRange "Your target is out of range, get closer!#*#"
#event Recover "You haven't recovered yet...#*#"
#event Recover "Spell recovery time not yet met#*#"
#event Resisted "Your target resisted the #1# spell#*#"
#event Resisted2 "You resist the #1# spell#*#"
#event Standing "You must be standing to cast a spell#*#"
#event Stunned "You are stunned#*#"
#event Stunned "You can't cast spells while stunned!#*#"
#event Stunned "You *CANNOT* cast spells, you have been silenced!#*#"


Sub Cast(string spellName,string spellType,timer giveUpTimer,string mySub)
   /declare castTime float local
   /if ( ${spellType.Equal[item]} ) {
      /varset castTime ${FindItem[${spellName}].CastTime}
   } else /if ( ${spellType.Equal[alt]} ) {
      /varset castTime ${Me.AltAbility[${spellName}].Spell.MyCastTime}
   } else {
      /varset castTime ${Spell[${spellName}].MyCastTime}
   }
   /if ( ${Me.Invis} && ${noInvis} ) {
      /return
   }
   :wait_for_stop
   /if ( !${Defined[spellType]} ) /declare spellType string local spell
   /if ( ${Me.Casting.ID} ) {
      /if ( ${Defined[mySub]} ) /call ${mySub}
      /goto :wait_for_stop
   } 
   /if ( ${Me.Speed} ) {
   :wait_for_stop2
      /if ( ${Defined[mySub]} ) /call ${mySub}
      /if ( ${Me.Moving} && ${castTime}>0.1 ) /goto :wait_for_stop2
   }
   /if ( !${Defined[giveUpTimer]} ) /declare giveUpTimer timer local 0
   /if ( !${Defined[spellRecastTime1]} ) {
      /if ( !${Defined[noInterrupt]} ) /declare noInterrupt int outer 0
      /declare moveBack bool outer false
      /declare selfResist int outer
      /declare selfResistSpell string outer
      /declare castEndTime timer outer
      /declare refreshTime timer outer 0
      /declare itemRefreshTime float outer 0
      /declare startCastingLoc string outer
      /declare i int local
      /declare castReturn string outer
      /declare spellNotHold string outer
      /delay 5
      /for i 1 to 9
         /declare spellRecastTime${i} timer outer
         /if ( ${Me.SpellReady[${i}]} ) {
            /varset spellRecastTime${i} 0
         } else {
            /varcalc spellRecastTime${i} 10*${Me.Gem[${i}].RecastTime}
         }
      /next i
   }
   /varset spellNotHold 0
   /varset selfResist 0
   /declare delayTime timer local
   /declare swapItemBack bool local false
   /declare slotName int local
   /declare oldItemName string local
   /declare slotID int local
   /declare oldSlotID int local
   /declare spellID int local
   /declare charges int local

   /if ( ${Window[SpellBookWnd].Open} ) /keypress spellbook
   /if ( ${Me.Ducking} ) /keypress duck
   /if ( !${Me.Standing} ) /stand

   /doevents Recover
   /doevents BeginCast
   /doevents Fizzle
   /doevents Interrupt
   /doevents Standing
   /doevents FDFail
   /doevents OutOfRange
   /doevents OutOfMana
   /doevents NoLOS
   /doevents Resisted2
   /doevents Resisted
   /doevents Immune
   /doevents Stunned
   /doevents Collapse
   /doevents NoTarget
   /doevents NotReady
   /varset castReturn CAST_SUCCESS
   
      /if ( ${spellType.Equal[item]} ) /goto :cast_item
   /if ( ${spellType.Equal[alt]} ) /goto :cast_alt

:cast_spell
   /if ( !${Int[${Me.Book[${spellName}]}]} ) {
      /echo Spell: "${spellName}" was not found in your book
      /return CAST_UNKNOWNSPELL
   }
   /if ( !${Me.Gem[${spellName}]} ) {
   :mem_spell
      /if ( ${Cursor.ID} ) {
         /autoinventory
         /delay 5
         /goto :mem_spell 
      }
      /if ( ${spellType.Left[3].Equal[gem]} ) {
         /memspell ${spellType.Right[1]} "${spellName}"
      } else {
         /return CAST_NOTMEMMED
      }
      /delay 6s ( ${Me.Gem[${spellName}]} || !${Window[SpellBookWnd].Open} )
      :close_spell_book
      /if ( ${Window[SpellBookWnd].Open} ) {
         /echo *** SpellBookWnd Stuck Open ***
         /notify SpellBookWnd DoneButton leftmouseup
         /goto :close_spell_book
      }
      /if ( ${Me.Sitting} ) {
         /sit
      }
      /if ( !${Me.Gem[${spellName}]} ) {
         /echo Spell mem interrupted...
         /return CAST_INTERRUPTED
      }
      :wait_for_mem
     
      /delay 15s ${Me.SpellReady[${spellName}]}
      /if ( !${Me.SpellReady[${spellName}]} ) {
         /if ( ${giveUpTimer} ) /goto :wait_for_mem
         /return CAST_NOTREADY
      }
   }
   /varset spellType spell
   /if ( ${spellName.Find[illusion: ]} && ${Me.AltAbilityReady[project illusion]} ) /call Cast "project illusion" alt

   /varset giveUpTimer ${giveUpTimer.OriginalValue}
   /declare recoverWaitTime timer local 30

:cast_spell_loop
:wait_for_spell
   /if ( ${Me.SpellReady[${spellName}]} ) {
      /varset spellRecastTime${Me.Gem[${spellName}]} 0
      /goto :skip_delay 
   }
   /if ( ${spellRecastTime${Me.Gem[${spellName}]}}  ) {
      /if ( !${giveUpTimer} ) /return CAST_NOTREADY
      /if ( ${Defined[mySub]} ) /call ${mySub}
      /goto :wait_for_spell
   }
   /if ( !${refreshTime} ) /goto :skip_delay
:wait_on_refresh
   /if ( ${Me.SpellReady[${spellName}]} ) /varset refreshTime 0
   /if ( ${refreshTime}>0 ) {
      /if ( ${Defined[mySub]} ) /call ${mySub}
      /goto :wait_on_refresh
   }
   /varcalc delayTime 10*(1.5-${Spell[${spellName}].MyCastTime})
:cast_delay_loop
   /if ( ${delayTime}>0 ) {
      /if ( ${Defined[mySub]} ) /call ${mySub}
      /goto :cast_delay_loop
   }
:skip_delay
   /varset startCastingLoc ${Me.X} ${Me.Y} ${Me.Z}
   /cast "${spellName}"

   /if ( ${Me.Casting.ID} ) {
      /varset spellID ${Me.Casting.ID}
      /varcalc castEndTime ${Me.Casting.MyCastTime}*10
      /if ( ${castEndTime}<${Math.Calc[${Me.Casting.CastTime}*5]} ) /varcalc castEndTime ${Me.Casting.CastTime}*5
      /echo Casting: ${Me.Casting.Name}${If[!${Me.Casting.TargetType.Equal[PB AE]} && !${Me.Casting.TargetType.Equal[self]} && ${Target.ID}, on >> ${Target.CleanName} <<,]}
   }

   /varset moveBack false

   /call WaitCast ${mySub}

   /if ( ${moveBack} ) {
      /keypress back hold
      /delay 4
      /keypress back
      /delay 15 !${Me.Moving}
   }

   /if ( ${castReturn.Equal[CAST_CANCELLED]} ) {
      /echo Spell was cancelled...
      /return CAST_CANCELLED
   }

   /doevents Recover
   /doevents BeginCast
   /doevents Fizzle
   /doevents Interrupt
   /doevents Standing
   /doevents FDFail
   /doevents OutOfRange
   /doevents OutOfMana
   /doevents NoLOS
   /doevents Resisted2
   /doevents Resisted
   /doevents Immune
   /doevents Stunned
   /doevents Collapse
   /doevents NoTarget
   /doevents NotReady
   
   /if ( !${spellID} ) /varset spellID ${Spell[${spellName}].ID}
   /if ( !${castReturn.Equal[CAST_SUCCESS]} ) {
      /if ( ${castReturn.Equal[CAST_NOTREADY]} ) /return CAST_NOTREADY
      /if ( ${castReturn.Equal[CAST_FIZZLE]} ) {
         /echo Spell Fizzled. Recasting...
         /goto :cast_spell_loop
      }
      /if ( ${castReturn.Equal[CAST_RECOVER]} ) {
         /if ( !${recoverWaitTime} ) {
            /varcalc spellRecastTime${Me.Gem[${spellName}]} 10*${Spell[${spellID}].RecastTime}
            /if ( !${giveUpTimer} ) /return CAST_NOTREADY
         }
         /goto :cast_spell_loop
      }
      /if ( ${castReturn.Equal[CAST_RESTART]} ) /goto :cast_spell_loop
      /if ( ${castReturn.Equal[CAST_STUNNED]} ) {
         /if ( ${Me.Stunned} ) {
            /delay 3s !${Me.Stunned}
         } else {
            /delay 7
         }
         /goto :cast_spell_loop
      }
      /if ( ${castReturn.Equal[CAST_INTERRUPTED]} ) {
         /if ( ${giveUpTimer} ) {
            /echo Spell was interrupted. Recasting...
            /goto :cast_spell_loop
         }
         /echo Spell was interrupted...
         /return CAST_INTERRUPTED
      }
   }
   /if ( !${castReturn.Equal[CAST_CANNOTSEE]} && !${castReturn.Equal[CAST_OUTOFRANGE]} && !${castReturn.Equal[CAST_OUTOFMANA]} && !${castReturn.Equal[CAST_NOTARGET]} ) {
      /varcalc refreshTime 10*${Spell[${spellID}].RecoveryTime}
      /varcalc spellRecastTime${Me.Gem[${spellName}]} 10*${Spell[${spellID}].RecastTime}
   }
   /if ( ${castReturn.Equal[CAST_COLLAPSE]} ) {
      /varset giveUpTimer 200
      /goto :cast_spell_loop
   }
/return ${castReturn}


:cast_item
	|gimp added check:
	/if (${Cursor.ID} && ${Cursor.NoDrop}) {
		/echo You have a no-drop item on cursor, please bag it first.
		/return CAST_CANCELLED
	}

   /if ( !${FindItem[${spellName}].InvSlot} ) {
      /echo Cannot find item: ${spellName}
      /return CAST_UNKNOWNSPELL
   } 
   /if ( ${FindItem[${spellName}].InvSlot}>21 ) {
      /varset swapItemBack true
      /if ( ${FindItem[${spellName}].WornSlot[1]} && ${FindItem[${spellName}].EffectType.Find[worn]} ) {
         /varset slotName ${FindItem[${spellName}].WornSlot[1]}
      } else /if ( ${FindItem[${spellName}].InvSlot}>29 ) {
         /varset slotName 29
      } else {
         /varset slotName ${FindItem[${spellName}].InvSlot}
      }
      /varset slotID ${InvSlot[${slotName}].ID}     
      /varset oldSlotID ${FindItem[${spellName}].InvSlot.ID}
      /varset oldItemName ${InvSlot[${slotID}].Item.Name}
      /call SwapItem "${spellName}" ${slotID}
   }
:cast_item_loop
:wait_item_loop
   /if ( ${itemRefreshTime} > ${MacroQuest.Running} ) {
      /delay 1
      /goto :wait_item_loop
   }
   /varset itemRefreshTime ${Math.Calc[${MacroQuest.Running}+000]}
   /varset charges ${FindItem[${spellName}].Charges}
   /varset startCastingLoc ${Me.X} ${Me.Y} ${Me.Z}
   /cast item "${spellName}"
   /if ( ${Me.Casting.ID} ) {
      /varcalc castEndTime ${FindItem[${spellName}].CastTime}*10
      /echo Casting: ${FindItem[${spellName}].Spell.Name}${If[!${FindItem[${spellName}].Spell.TargetType.Equal[PB AE]} && !${FindItem[${spellName}].Spell.TargetType.Equal[self]} && ${Target.ID}, on >> ${Target.CleanName} <<,]}
   }
   /if ( ${charges}>0 ) /delay 1s ${FindItem[${spellName}].Charges}!=${charges}

   /call WaitCast ${mySub}
   
   /if ( ${swapItemBack} ) {
      /if ( ${FindItem[${spellName}].ID} ) {
         /call SwapItem "${spellName}" ${oldSlotID}
      } else /if ( ${FindItem[${oldItemName}].ID} ) {
         /call SwapItem "${oldItemName}" ${slotID}         
      }
      /if ( ${Cursor.Container} ) /autoinventory
   }

   /if ( ${castReturn.Equal[CAST_CANCELLED]} ) {
      /echo Spell was cancelled...
      /return CAST_CANCELLED
   }

   /doevents BeginCast
   /doevents Interrupt
   /doevents Standing
   /doevents FDFail
   /doevents OutOfRange
   /doevents NoLOS
   /doevents Resisted2
   /doevents Resisted
   /doevents Immune
   /doevents Stunned
   /doevents Collapse
   /doevents NoTarget
   /doevents NotReady

   /if ( !${castReturn.Equal[CAST_SUCCESS]} ) {
      /if ( ${castReturn.Equal[CAST_NOTREADY]} ) /return CAST_NOTREADY
      /if ( ${castReturn.Equal[CAST_RESTART]} ) /goto :cast_item
      /if ( ${castReturn.Equal[CAST_STUNNED]} ) {
         /if ( ${Me.Stunned} ) {
            /delay 3s !${Me.Stunned}
         } else {
            /delay 7
         }
         /goto :cast_item
      }
      /if ( ${castReturn.Equal[CAST_INTERRUPTED]} ) {
         /if ( ${giveUpTimer} ) {
            /echo Spell was interrupted. Recasting...
            /goto :cast_item
         }
         /echo Spell was interrupted...
         /return CAST_INTERRUPTED
      }
   }
   /if ( ${castReturn.Equal[CAST_COLLAPSE]} ) {
      /varset giveUpTimer 200
      /goto :cast_item
   }
/return ${castReturn}


:cast_alt
   /if ( !${Me.AltAbilityReady[${spellName}]} ) /return CAST_NOTREADY
   /echo Using AA Ability: ${Me.AltAbility[${spellName}].Name}
   /alt activate ${Me.AltAbility[${spellName}].ID}
   /if ( ${Me.AltAbility[${spellName}].Spell.MyCastTime}>=0.5 ) /delay 1s ${Me.Casting.ID}
   /call WaitCast ${mySub}
   
   /if ( ${castReturn.Equal[CAST_CANCELLED]} ) {
      /echo Spell was cancelled...
      /return CAST_CANCELLED
   }
   
   /doevents BeginCast
   /doevents Interrupt
   /doevents Standing
   /doevents FDFail
   /doevents OutOfRange
   /doevents NoLOS
   /doevents Resisted2
   /doevents Resisted
   /doevents Immune
   /doevents Stunned
   /doevents NoTarget
     
   /if ( !${castReturn.Equal[CAST_SUCCESS]} ) {
      /if ( ${castReturn.Equal[CAST_RESTART]} ) /goto :cast_alt
      /if ( ${castReturn.Equal[CAST_STUNNED]} ) {
         /if ( ${Me.Stunned} ) {
            /delay 3s !${Me.Stunned}
         } else {
            /delay 7
         }
         /goto :cast_alt
      }
      /if ( ${castReturn.Equal[CAST_INTERRUPTED]} ) {
         /if ( ${giveUpTimer} ) {
            /echo Spell was interrupted. Recasting...
            /goto :cast_alt
         }
         /echo Spell was interrupted...
         /return CAST_INTERRUPTED
      }
   }
/return ${castReturn}


Sub EquipItem(string ItemName)
   /declare DestName string local
   /declare SlotName string local
   |/if (${SlotName.Equal[NULL]}) 
   /varset SlotName ${InvSlot[${FindItem[=${ItemName}].WornSlot[1]}].Name}

	/echo #itemname: ${ItemName}
	/echo #slotname: ${SlotName}

   /echo # itemname invslot: ${FindItem[=${ItemName}].InvSlot}
   /echo # itemname wornslot: ${FindItem[=${ItemName}].WornSlot[${SlotName}]}, slotname: ${SlotName}

   /if (${FindItem[=${ItemName}].InvSlot}<22 || !${FindItem[=${ItemName}].WornSlot[${SlotName}]}) /return
   /if (!${InvSlot[${SlotName}].Item.Name.Equal[NULL]}) /varset DestName "${InvSlot[${SlotName}].Item.Name}"
   /call SwapItem "${ItemName}" "${SlotName}"
/return "${DestName}"

| Sub EquipItem(string itemName,string slotName)
   /if ( !${Defined[slotName]} ) /declare slotName string local ${FindItem[${itemName}].WornSlot[1]}
   /declare oldItem string local ${Me.Inventory[${slotName}].Name}
   /call SwapItem "${itemName}" ${slotName}
/return "${oldItem}" ${slotName}

Sub SwapItem(string itemName,string slotName)

   /if ( ${InvSlot[${slotName}].Item.Name.Equal[${itemName}]} ) /return
   /declare slotID int local
   /declare oldSlotID int local
   /declare oldItem string local
   /declare i int local
   /varset slotID ${InvSlot[${slotName}].ID}           | slotID = slot you're swapping item to
   /varset oldSlotID ${FindItem[${itemName}].InvSlot.ID} | oldSlotID = slot the item was originally in
   /varset oldItem ${InvSlot[${slotName}].Item.Name}     | oldItem = name of item in the slot you're swapping WITH
   /if ( !${slotID} ) {
      /echo Invalid slot name: ${slotName}
      /return
   }
   /if ( !${oldSlotID} ) {
      /echo Could not find item ${itemName}
      /return
   }
:auto_inv
   /if ( ${Cursor.ID} && !${Cursor.Name.Equal[${itemName}]} ) {
      /if ( ${Cursor.Container} ) {
         /for i 1 to 8
            /if ( !${InvSlot[pack${i}].Item.Container} ) {
               /nomodkey /itemnotify pack${i} leftmouseup
            }
         /next i
      } else {
         /autoinventory
      }
      /goto :auto_inv
   }
   

:pick_up_item
 | if the item is in a bag and it's not open, then open it!
   /if ( ${InvSlot[${oldSlotID}].Pack} && !${Window[${InvSlot[${oldSlotID}].Pack.Name}].Open} ) /nomodkey /itemnotify ${InvSlot[${oldSlotID}].Pack} rightmouseup

 | if the slot you're putting it in is inside a bag and it's not open, then open it!
   /if ( ${InvSlot[${slotID}].Pack} && !${Window[${InvSlot[${slotID}].Pack.Name}].Open} ) /nomodkey /itemnotify ${InvSlot[${slotID}].Pack} rightmouseup

 | ok.. pick up the item now!
   /squelch /nomodkey /shiftkey /itemnotify ${InvSlot[${oldSlotID}]} leftmouseup

 | if item isn't on your cursor, try again!
   /if ( !${Cursor.Name.Equal[${itemName}]} ) {
      /if ( ${Cursor.ID} && !${Cursor.Container} ) /autoinventory
      /goto :pick_up_item
   }
:exchange_items
   /if ( ${InvSlot[${oldSlotID}].Pack} && !${Window[${InvSlot[${oldSlotID}].Pack.Name}].Open} ) /nomodkey /itemnotify ${InvSlot[${oldSlotID}].Pack} rightmouseup
   /if ( ${InvSlot[${slotID}].Pack} && !${Window[${InvSlot[${slotID}].Pack.Name}].Open} ) /nomodkey /itemnotify ${InvSlot[${slotID}].Pack} rightmouseup

 | put the item in the new slot, and pick up whatever item was there
   /squelch /nomodkey /shiftkey /itemnotify ${slotID} leftmouseup

 | if it didnt get exchanged, try again!
   /if ( ${Cursor.ID} && !${Cursor.Name.Equal[${oldItem}]} && !${Cursor.Name.Equal[${itemName}]} && !${Cursor.Container} ) /autoinventory
   /if ( !${InvSlot[${slotID}].Item.Name.Equal[${itemName}]} ) /goto :exchange_items
:drop_item
   /if ( !${Cursor.Container} || ( ${InvSlot[${oldSlotID}]}<30 && ${InvSlot[${oldSlotID}]}>=22 ) ) {
      /if ( ${InvSlot[${oldSlotID}].Pack} && !${Window[${InvSlot[${oldSlotID}].Pack.Name}].Open} ) /nomodkey /itemnotify ${InvSlot[${oldSlotID}].Pack} rightmouseup
      /if ( ${InvSlot[${slotID}].Pack} && !${Window[${InvSlot[${slotID}].Pack.Name}].Open} ) /nomodkey /itemnotify ${InvSlot[${slotID}].Pack} rightmouseup
      /squelch /nomodkey /itemnotify ${oldSlotID} leftmouseup
      /if ( ${Cursor.ID} ) {
         /if ( !${Cursor.Name.Equal[${oldItem}]} ) /autoinventory
         /goto :drop_item
      }
   }
:close_pack
   /if ( ${InvSlot[${oldSlotID}].Pack} && ${Window[${InvSlot[${oldSlotID}].Pack.Name}].Open} && ( ${Cursor.Name.Equal[${itemName}]} || ${FindItem[${itemName}].ID} ) ) {
      /squelch /nomodkey /itemnotify ${InvSlot[${oldSlotID}].Pack} rightmouseup
      /goto :close_pack
   }
   /if ( ${InvSlot[${slotID}].Pack} && ${Window[${InvSlot[${slotID}].Pack.Name}].Open} && ( ${Cursor.Name.Equal[${itemName}]} || ${FindItem[${itemName}].ID} ) ) {
      /squelch /nomodkey /itemnotify ${InvSlot[${slotID}].Pack} rightmouseup
      /goto :close_pack
   }
/return

Sub Interrupt
   /if ( ${Me.Mount.ID} ) /dismount
   /stopcast
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_CANCELLED
/return ${Macro.Return}


Sub WaitCast(string mySub)
   /declare currentTarget int local ${Target.ID}
   /declare currentTargetType string local ${Target.Type}
:wait_cast_loop
   /if ( ${Bool[${mySub}]} ) /call ${mySub}
   /if ( ${Me.Casting.ID} ) {
      /if ( ${currentTarget} && !${Spawn[${currentTarget}].Type.Equal[${currentTargetType}]} ) {
         /if ( !${Me.Casting.TargetType.Equal[PB AE]} && !${Me.Casting.TargetType.Equal[self]} && !${moveBack} && ( !${Me.Mount.ID} || !${noInterrupt} ) ) {
            /if ( !${Me.Mount.ID} || ${castEndTime}>70 ) {
               /call Interrupt
            } else /if ( ${Me.Casting.RecastTime}>3 ) {
               /varset castReturn CAST_CANCELLED
               /keypress forward hold
               /delay 6
               /keypress forward
               /varset moveBack true
            }
         }
      }
      /if ( ${Me.State.Equal[DUCK]} ) /varset castReturn CAST_CANCELLED
      /delay 1
      /goto :wait_cast_loop
   }
/return

Sub Event_Fizzle
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_FIZZLE
/return

Sub Event_Resisted(string line,string name)
   /if ( ${selfResist} && ${name.Equal[${selfResistSpell}]} ) {
      /varset selfResist 0
      /return   
   }
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_RESISTED
/return

Sub Event_Resisted2(string line,string name)
   /if ( ${Defined[selfResist]} ) {
      /varset selfResist 1
      /varset selfResistSpell ${name}
   }
/return

Sub Event_Interrupt
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_INTERRUPTED
/return

Sub Event_Recover
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_RECOVER
/return

Sub Event_Immune
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_IMMUNE
/return

Sub Event_Stunned
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_STUNNED
/return

Sub Event_NoLOS
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_CANNOTSEE
/return

Sub Event_Standing
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_RESTART
/return

Sub Event_Collapse
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_COLLAPSE
/return

Sub Event_OutOfMana
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_OUTOFMANA
/return

Sub Event_OutOfRange
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_OUTOFRANGE
/return

Sub Event_NoTarget
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_NOTARGET
/return

Sub Event_NotReady
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_NOTREADY
/return

Sub Event_NoHold
   /if ( ${Defined[spellNotHold]} ) /varset spellNotHold 1
/return

Sub Event_BeginCast
   /if ( ${Defined[castReturn]} ) /varset castReturn CAST_SUCCESS
/return

Sub Event_FDFail(string line,string name)
   /if ( ${name.Equal[${Me.Name}]} && ${Defined[castReturn]} ) {
      /if ( !${Me.Standing} ) /stand
      /varset castReturn CAST_RESTART
   }
/return