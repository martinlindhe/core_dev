| requires MQ2Cast & MQ2Exchange

#event Heal "You have healed #1# for #2# points of damage."
Sub Event_Heal(string line, string name, int value)
	/echo Heal: ${name} -> ${value} HP
/return

#event TargetCured		"Your target has been cured.#*#"
Sub Event_TargetCured
	/if (${Target.ID} && ${Target.Type.Equal["PC"]}) {
		|Dont know for sure who I actually cured
		/echo # Debug: ${Target.Name} (?) cured
	} else {
		/echo # Debug: Group member cured.
	}
/return


|Raid: DK Event 2 - Guests
#event SomeoneVertigo "#1# looks as if they are going to lose their balance!"
Sub Event_SomeoneVertigo(string Line, string Name)
	/echo ${Name} needs RGC! (Vertigo)
	/if (${Zone.ShortName.Equal["theatera_raid"]} && ${Me.Book["Remove Greater Curse"]}) {
		/target pc ${Name}
		| delay here so ppl dont get suspicious
		/delay 2s
		/call BotCast "Remove Greater Curse"
	}
/return

#event MyselfVertigo "You feel as if you are going to lose your balance!"
Sub Event_MyselfVertigo(string Line)
	/echo I need RGC! (Vertigo)
	/if (${Zone.ShortName.Equal["theatera_raid"]} && ${Me.Book["Remove Greater Curse"]}) {
		/target myself
		/call BotCast "Remove Greater Curse"
	}
/return


| Casts a spell / AA ability using MQ2Cast
Sub BotCast(string spellName, string mySub)

	/if (!${Defined[defaultGem]}) /declare defaultGem string outer gem1
	/if (!${Defined[spellType]}) /declare spellType string local

	/if (!${Defined[interruptFlag]}) {
		/declare interruptFlag bool outer FALSE	
	} else {
		/varset interruptFlag FALSE
	}

	/if (${Window[SpellBookWnd].Open}) /keypress spellbook
	/if (${Corpse.Open}) /notify LootWnd DoneButton leftmouseup

:wait_for_stop
	/if (${Me.Casting.ID}) /goto :wait_for_stop
		
:cast_spell
	/if (${Me.AltAbility[${spellName}].ID}) {
		/varset spellType alt
	} else /if (${Spell[${spellName}].ID}) {
		|/if (${spellType.Find[gem]}) /varset spellType ${defaultGem}
	}

	| /echo spell: ${spellName} type: ${spellType}
	/casting "${spellName}" ${spellType}

:cast_spell_pending
	/if (${Cast.Status.Find[C]}) {
		/if (!${interruptFlag} && ${mySub.Length}) /call ${mySub}
		/goto :cast_spell_pending
	}

	/if (${Cast.Result.Equal[CAST_FIZZLE]}) {
		/echo Fizzled, recasting...
		/goto :wait_for_stop
	}
	
	/doevents

/return ${Cast.Result}


| Callback function for nukes
Sub NukeCallback
	/if (!${Target.ID} || (${Target.PctHPs} == 0) || ${Target.Type.Equal["Corpse"]}) {
		/interrupt
		/varset interruptFlag TRUE
		| /echo Mob died, cancelling nuke.
	} else /if (${Krust.Healer} && ${Me.LAHoTT} && ${Me.TargetOfTarget.Type.Equal[PC]} && (${Me.TargetOfTarget.PctHPs} <= 40)) {
		/interrupt
		/varset interruptFlag TRUE
		/echo Player in HoTT need heal, cancelling nuke.
	} else /if (${Me.Moving} || ${Me.Stunned}) {
		/interrupt
		/varset interruptFlag TRUE
		/echo STUN/MOVE - INTERRUPT NUKE ${Target.CleanName}
	}
/return

| Callback function for heals
Sub HealCallback
	/if (!${Target.ID} || ${Target.Type.Equal["Corpse"]}) {
		/interrupt
		/varset interruptFlag TRUE
		/echo Player died, cancelling heal.
	} else /if (${Me.Casting.ID} && ${Target.PctHPs} >= 80) {
		/interrupt
		/varset interruptFlag TRUE
		/echo ${Target.Name} at ${Target.PctHPs}%, cancelling heal.
	} else /if (${Me.Moving} || ${Me.Stunned}) {
		/interrupt
		/varset interruptFlag TRUE
		/echo STUN/MOVE - INTERRUPT HEAL ${Target.Name}
	}
/return

| Accepts a spell name, or a item name
Sub RefreshBuff(string Name, int Duration)

	/declare BuffName string local
	/declare i int local 0
	/declare freeslot int local 0

	| /if (${Spawn[radius 40 npc].ID} && ${Spawn[radius 40 npc].LineOfSight}) {
	|   /call DebugMsg "# Avoiding refresh of ${Name} cause of NPC in camp"
  |   /return
	| }
	
	/if (${Window[LootWnd].Open} || ${Cursor.ID}) /return

	| Is it a item clicky?
	/if (${FindItem[${Name}].ID}) {
		/varset BuffName ${FindItem[${Name}].Spell.Name}
	} else /if (${Spell[${Name}].ID}) {
		| It's a spell
		/if (${Spell[${Name}].Mana} < ${Me.CurrentMana}) /return
		/varset BuffName ${Name}
	} else {
		/echo UNKNOWN BUFF! ${Name}
		/return
	}
	
	| Do i have the buff on?
	/if (${Spell[${BuffName}].Stacks} && (!${Me.Buff[${BuffName}].ID} || ${Me.Buff[${BuffName}].Duration} < ${Duration})) {
		/call DebugMsg "BUFFING: ${BuffName} (${Me.Buff[${BuffName}].Duration} left)"
		/if (${FindItem[${Name}].ID}) {
			/call MQ2Cast "${Name}" item
		} else {
			| target myself för att group buffs ska träffa rätt (todo: går det göra en check ifall /target är nödvändigt?)
			/target myself
			| Hitta en ledig spell-slot
			/for i 1 to 9
				/if (!${Me.Gem[${i}].ID}) /varset freeslot ${i}
			/next i
			
			/if (${freeslot}) {
				/call MQ2Cast "${Name}" gem${freeslot}
			} else {			
				/call MQ2Cast "${Name}" gem6
			}
		}
		/doevents
		/return
	}

	/if (!${FindItem[${Name}].ID} && !${Me.Book[${Name}]}) /echo # ERROR: Can't find the spell ${Name}!

/return

Sub RefreshPetBuff(string SpellName)

	| note: petbuff duration is not exposed to mq atm, so only refreshing if buffs are gone
	| http://www.macroquest2.com/phpBB2/viewtopic.php?t=11930 for pet buff duration code
	/if (!${Me.PetBuff[${SpellName}]} && ${Me.Pet.Distance} < 100) {

		/if (!${Me.Book[${SpellName}]}) {
			/echo # You miss the spell ${SpellName}!
			/return
		}
		
		/target id ${Me.Pet.ID}
		/delay 10
		/call DebugMsg "BUFFING PET: ${SpellName} (${Me.PetBuff[${SpellName}].Duration} left)"
		/call MQ2Cast "${SpellName}" gem6
		/doevents
	}
/return

Sub HandleGroupHeal(int PctHPs, string SpellName, string Spell2Name)

	/declare i int local 0
	
	/if (${Window[LootWnd].Open}) /return
	
	/for i 0 to 5
		/if (${Group.Member[${i}].ID} && (${Group.Member[${i}].PctHPs} <= ${PctHPs}) && ${Group.Member[${i}].State.NotEqual["DEAD"]} && (${Group.Member[${i}].Distance} < ${Me.Gem[${Me.Gem["${SpellName}"]}].Range})) {
			/target pc ${Group.Member[${i}].Name}
				
			| check if player been healed before reaching this code
			/if (${Target.PctHPs} <= ${PctHPs}) {
				/if (${Me.AltAbilityReady["Gathering of Spirits"]} && (${Target.PctHPs} <= 18)) {
					/call DebugMsg "# GoS > ${Target.Name} < at ${Target.PctHPs}%"
					/casting "Gathering of Spirits" alt
				} else /if (${Spell2Name.Length} && ${Me.SpellReady[${Spell2Name}]} && (${Target.PctHPs} <= 32)) {
					/call DebugMsg "# QHEAL2 > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
					/call BotCast "${Spell2Name}" HealCallback
				} else /if (${Me.SpellReady[${SpellName}]}) {
					/call DebugMsg "# QHEAL > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
					/call BotCast "${SpellName}" HealCallback
				}
				/return
			}
		}
	/next i

	| Heal target if it's a player outside group
	/if (${Target.ID} && ${Target.Type.Equal[PC]} && ${Target.PctHPs} <= ${PctHPs}) {
		/call WaitForSpellReady "${SpellName}"

		/if (${Spell2Name.Length} && ${Me.SpellReady[${Spell2Name}]} && (${Target.PctHPs} <= 35)) {
			/call DebugMsg "# HoTT HEAL2!!! > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
			/call BotCast "${Spell2Name}" HealCallback
		} else {
			/call DebugMsg "# HoTT HEAL!!! > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
			/call BotCast "${SpellName}" HealCallback
		}
		/return
	}

	| See if HoTT is a player that needs a heal	
	/if (${Me.LAHoTT} && ${Me.TargetOfTarget.Type.Equal[PC]} && ${Me.TargetOfTarget.PctHPs} <= ${PctHPs}) {
		/target pc ${Me.TargetOfTarget.Name}
		/call WaitForSpellReady "${SpellName}"
		
		/if (${Spell2Name.Length} && ${Me.SpellReady[${Spell2Name}]} && (${Target.PctHPs} <= 35)) {
			/call DebugMsg "# HoTT HEAL2!!! > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
			/call BotCast "${Spell2Name}" HealCallback
		} else {
			/call DebugMsg "# HoTT HEAL!!! > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
			/call BotCast "${SpellName}" HealCallback
		}
		/return
	}
	
/return



Sub AttemptToStun(string name)

	/if (${Spawn[${name}].Level} > 75) /return

	/declare StunSpellName string local
	
	|Cleric stuns:
	/if (${Me.Gem["Sound of Divinity"]}) /varset StunSpellName "Sound of Divinity"

	|Druid stuns:
	/if (${Me.Gem["Stormwatch"]}) /varset StunSpellName "Stormwatch"
	/if (${Me.Gem["Gale of the Stormborn"]}) /varset StunSpellName "Gale of the Stormborn"
	/if (${Me.Gem["Gale of the Stormborn Rk. II"]}) /varset StunSpellName "Gale of the Stormborn Rk. II"
	
	/if (!${Me.Gem[${StunSpellName}]}) {
		/echo # Debug: No stun spells memmed
		/return
	}

	/if (${Me.Casting.ID} && ${Me.Casting.SpellType.Equal[Detrimental]} && ${Me.Casting.Name.NotEqual[${StunSpellName}]}) {
		|interrupta, ENBART om spellen som kastas e en nuke
		/echo # ABORTING cast of ${Me.Casting.Name} for stun!
		/keypress d
		/delay 1
		/keypress d
	}

	/if (${Spawn[${name}].ID} && ${Spawn[${name}].LineOfSight}) {
		/target npc ${name}
		/delay 5
		/echo * Trying to stun ${name}
		/call MQ2Cast ${StunSpellName}
	}

/return


| Loops until SpellName is ready to cast
Sub WaitForSpellReady(string SpellName)

	/if (!${Me.Book[${SpellName}]}) {
		/call DebugMsg "ERROR: I dont have the spell: ${SpellName}"
		/return
	}

	/if (!${Me.Gem["${SpellName}"]}) {
		|todo: memorize in first free slot, or use default slot
		/call DebugMsg "ERROR fixme: I dont have spell ${SpellName} MEMORIZED"
		/return
	}

	:loop
	/if (!${Me.SpellReady[${SpellName}]} || ${Me.Moving}) {
		/doevents
		/goto :loop
	}

/return