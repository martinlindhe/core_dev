|   gimp_selfbuffs.inc
|   5 Janurary 2006 by Harden
|		+ updates by gimp
|   Recast your self buffs using one command.
|   Requires Spell names / how many spells / which spell gems to be modified
|todo: use all free possible buff slots instead of just slot 7
|todo: memorize all buffs needed to cast before casting

|ripped & modified from some old spell_routines.inc, written by ???
Sub GimpEquipItem(string ItemName)
	/declare DestName string local
	/declare SlotName string local

	/varset SlotName ${InvSlot[${FindItem[=${ItemName}].WornSlot[1]}].Name}

	|/echo #itemname: ${ItemName}
	|/echo #slotname: ${SlotName}
	|/echo # itemname invslot: ${FindItem[=${ItemName}].InvSlot}
	|/echo # itemname wornslot: ${FindItem[=${ItemName}].WornSlot[${SlotName}]}, slotname: ${SlotName}

	/if (${FindItem[=${ItemName}].InvSlot}<22 || !${FindItem[=${ItemName}].WornSlot[${SlotName}]}) /return
	/if (!${InvSlot[${SlotName}].Item.Name.Equal[NULL]}) /varset DestName "${InvSlot[${SlotName}].Item.Name}"
	/call SwapItem "${ItemName}" "${SlotName}"
/return "${DestName}"

#Event BuffSelf            "[MQ2] buffself"
Sub Event_BuffSelf
	|Useage /buffself
   
	/declare i int local 0
	/declare oldId int local 0
	/declare nValues		int local 1
	/declare buffname			string local
	/declare OldSpell			string	local
	/declare FocusItem		string local ${Ini[INI_FILE, ${Me.Name},BuffFocusItem,]}
	/declare OldItem			string local
	
	/if (${FocusItem.Length} && ${FindItem[=${FocusItem}].ID}) {
		/echo # Using buff focus item: ${FocusItem}
		|Equip item (from spell_routines.inc):
		
		/call GimpEquipItem "${FocusItem}"
		/varset OldItem ${Macro.Return}
		/echo # Old item: ${OldItem}
		/delay 1s
	} else {
		/if (${FocusItem.NotEqual[NULL]}) {
			/echo # Invalid buff item specified in INI_FILE: ${FocusItem}
			|disables use of invalid item names
			/varset FocusItem
		}
	}

	/if (${Target.ID} && (${Target.ID} != ${Me.ID})) /varset oldId ${Target.ID}
	/echo # Buffing self buffs

	/if (${Ini[INI_FILE, ${Me.Class}_SelfBuffs,1,NOTFOUND].Equal[NOTFOUND]}) {
		/echo # Debug: [${Me.Class}_SelfBuffs] is undefined in INI_FILE
		/return
	}

	:CounterLoop
	/if (${Ini[INI_FILE, ${Me.Class}_SelfBuffs,${nValues},NOTFOUND].Equal[NOTFOUND]}) {
		/varcalc nValues ${nValues}-1
		/goto :CastBuffs
	}
	/varcalc nValues ${nValues}+1
	/doevents
	/goto :CounterLoop 

	:CastBuffs
	/varset OldSpell ${Me.Gem[DEFAULT_SPELL_SLOT]}

	/for i 1 to ${nValues}
		/varset buffname ${Ini[INI_FILE, ${Me.Class}_SelfBuffs, ${i}]}
		
		/if (!${Me.FreeBuffSlots}) {
			/echo # No more free buff slots
			/goto :DoneBuffing
		}
		
		/if (${Me.Book["${buffname}"]}) {

			/if (${Spell["${buffname}"].Stacks[20]} {

				/if (!${Me.Gem["${buffname}"]}) {
					|memorize spell
					/echo # Memorizing ${buffname}
					/memspell DEFAULT_SPELL_SLOT "${buffname}"
					/delay 10s ${Me.SpellReady["${buffname}"]}
				}
				/target myself
				/call Cast "${buffname}"
			} else {
				/if (${Me.Buff["${buffname}"].Duration}) {
					/echo # Skipping ${buffname}, current buff duration is ${Me.Buff["${buffname}"].Duration} ticks
				} else {
					/echo # Cannot buff with ${buffname}, spell doesnt stack
				}
			}
		} else {
			/if (!${Me.AltAbilityReady[${buffname}]}) {
				|is it a item in my inventory?
				/if (${FindItem[=${buffname}].ID}) {

					/if (${Spell[${FindItem[=${buffname}].Spell}].Stacks[20]}) {
						/echo # Clicking item ${buffname}
						/target myself
						/call Cast "${buffname}" item
					} else {
						/if (${Me.Buff[${FindItem[=${buffname}].Spell}].Duration}) {
							/echo # Skipping ${buffname}, current buff duration is ${Me.Buff[${FindItem[=${buffname}].Spell}].Duration} ticks
						} else {
							/echo # Cannot buff with clicky ${buffname}, spell doesnt stack
						}
					}
				} else {
					/if (${Me.AltAbility[${buffname}]}) {
						/echo # Skipping ${buffname}, AA is not ready
					} else {
						/echo # Error: Buff ${buffname} not found (checked spells, AA's & items)
					}
				}
			} else {
				/if (${Me.Buff["${buffname}"].Duration} < 150) {
					/target myself
					/echo # Activating AA ability ${buffname}
					/docommand /aa act ${buffname}

					|fixme, detta funkar inte:
					|/delay 10s !${Me.Casting.ID}
					:selfbuffloop
					/if (${Me.Casting.ID}) {
						/goto :selfbuffloop
					}
					/delay 1s
				} else {
					/echo # Skipping ${buffname}, current buff duration is ${Me.Buff["${buffname}"].Duration} ticks
				}
			}
		}
	/next i

	:DoneBuffing
	/echo # Done buffing self
	
	|restore old spell
	/if (${OldSpell.NotEqual[NULL]} && ${Me.Gem[DEFAULT_SPELL_SLOT].Name.NotEqual[${OldSpell}]}) {
		/delay 2s
		/echo # Restoring old spell ${OldSpell} in slot DEFAULT_SPELL_SLOT
		/memspell DEFAULT_SPELL_SLOT "${OldSpell}"
	}

	/if (${oldId}) /target id ${oldId}

	|Restore old equipped item instead of focus item
	/if (${FocusItem.NotEqual[NULL]}) {
		/delay 1s
		/echo # Equipping ${OldItem}
		/call GimpEquipItem "${OldItem}"
	}
/return

