| requires MQ2Cast & MQ2Exchange

| when im told to click AR, this one autoclicks
#event ClickAR "#1# tells you, '#*#next#*#'"
Sub Event_ClickAR(string line, string name, string txt)
	/if (${Zone.ShortName.NotEqual['theatera_raid']}) /return
	/echo ${name} Ayonae Ro event, some1 told u: ${line}
	/return

	/popup Clicking AR!
	/delay 1s
	/echo Clicking AR!
	/target npc Ayonae Ro
	/casting "Divine fetters of ro" item
/return

#event Heal "You have healed #1# for #2# points of damage."
Sub Event_Heal(string line, string name, int value)
	/echo Healed > ${name} < (${value} HP)
/return

#event TargetCured "Your target has been cured.#*#"
Sub Event_TargetCured
	/if (${Target.ID} && ${Target.Type.Equal["PC"]}) {
		|Dont know for sure who I actually cured
		/echo # Debug: ${Target.Name} (?) cured
	} else {
		/echo # Debug: Group member cured.
	}
/return

#event Silenced "You *CANNOT* cast spells, you have been silenced!"
Sub Event_Silenced
	/echo I AM SILENCED, delay 10s
	/delay 10s
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
		/delay 1s
		/call BotCast "Remove Greater Curse"
	}
/return

#event WeZoning "LOADING, PLEASE WAIT..."
Sub Event_WeZoning(string Line)
	/echo ZONING!!!!!
	/varset AbortLoop 1
	/delay 30s
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
	/if (${interruptFlag}) /return ${Cast.Result}
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
		| /echo Mob died, cancelling nuke.
		/interrupt
		/varset interruptFlag TRUE
		/return
	} else /if (${Krust.Healer} && ${Me.LAHoTT} && ${Me.TargetOfTarget.Type.Equal["PC"]} && (${Me.TargetOfTarget.PctHPs} <= QHEAL_PERCENT)) {
		/interrupt
		/varset interruptFlag TRUE
		/echo Player in HoTT need heal, cancelling nuke.
		/delay 2s ${Me.SpellReady[DRUID_QH1]}
		/return
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
	} else /if (${Me.Stunned}) {
		/interrupt
		/varset interruptFlag TRUE
		/echo STUN - INTERRUPT HEAL ${Target.Name}
	}
/return

| Accepts a spell name, or a item name
| Returns 1 if buff was refreshed, else returns 0
Sub RefreshBuff(string Name, int Duration)

	/declare BuffName string local
	/declare i int local 0
	/declare freeslot int local 0

	/if (${Window[LootWnd].Open} || ${Cursor.ID} || ${Me.Casting.ID}) /return 0

	| Is it a item clicky?
	/if (${FindItem[${Name}].ID}) {
		/varset BuffName ${FindItem[${Name}].Spell.Name}
	} else /if (${Spell[${Name}].ID}) {
		| It's a spell
		/if (${Me.CurrentMana} < ${Spell[${Name}].Mana}) /return 0
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
			| target myself för att group buffs ska träffa rätt, bara ifall det behövs (target=PC som ej är i gruppen & spellen är en group spell)
			/if (${Target.Type.Equal["PC"]} && !${Group.Member[${Target.Name}]} && ${Spell[${BuffName}].AERange}) /target myself
			| target myself för att single target buff ska träffa rätt
			/if (${Spell[${BuffName}].TargetType.Equal["Single"]}) /target myself

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
		/return 1
	}

	/if (!${FindItem[${Name}].ID} && !${Me.Book[${Name}]}) /echo # ERROR: Can't find the spell ${Name}!

/return 0

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

Sub PerformHeal(int SpawnId, int PctHPs)

	/if (!${Spawn[id ${SpawnId}].LineOfSight} && (${Spawn[id ${SpawnId}].Class.Name.Equal["Monk"]} || ${Spawn[id ${SpawnId}].Class.Name.Equal["Bard"]})) {
		/echo DEBUG: skipping heal of puller!
		/return
	}

	/if (${Target.ID}!=${SpawnId}) {
		/target id ${SpawnId}
		/delay 2s ${Target.ID}==${SpawnId}
		/delay 2
	}

	| Check if player been healed before reaching this code
	/if (${Target.PctHPs} > ${PctHPs}) /return
	
	/if (${Me.AltAbilityReady[DRUID_INSTANTHEAL]} && (${Target.PctHPs} <= 18) && (${Target.Distance} < ${Spell[DRUID_INSTANTHEAL].Range})) {
		/call DebugMsg "# GoS > ${Target.Name} < at ${Target.PctHPs}%"
		/casting "DRUID_INSTANTHEAL" alt
		/delay 1s !${Me.AltAbilityReady[DRUID_INSTANTHEAL]}
	} else /if (${Me.SpellReady[DRUID_QH2]} && (${Target.PctHPs} <= 32) && (${Target.Distance} < ${Spell[DRUID_QH2].Range})) {
		/call DebugMsg "# DRUID_QH2 > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
		/call BotCast "DRUID_QH2" HealCallback
	} else /if (!${FindItem["Mask of the Ancients"].Timer}) {
		/call DebugMsg "# CLICK HEAL > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
		/call MQ2Cast "Mask of the Ancients" item
	} else /if (${Me.SpellReady[DRUID_QH1]} && (${Target.Distance} < ${Spell[DRUID_QH1].Range})) {
		/call DebugMsg "# DRUID_QH1 > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
		/call BotCast "DRUID_QH1" HealCallback
	}
/return


Sub HandleGroupHeal(int PctHPs)

	/if (${Window[LootWnd].Open} || !${Krust.Healer} || ${Me.Casting.ID}) /return

	/declare i int local 0

	| Heal target if it's a player outside group
	/if (${Target.ID} && ${Target.Type.Equal["PC"]} && (${Target.Distance} < 200) && ${Target.PctHPs} <= ${PctHPs} && ${Target.Type.NotEqual["Corpse"]} && ${Target.State.NotEqual["DEAD"]} && ${Me.SpellReady[DRUID_QH1]}) {
		/call PerformHeal ${Target.ID} ${PctHPs}
		/return
	}

	| See if HoTT is a player that needs a heal	
	/if (${Me.LAHoTT} && ${Me.TargetOfTarget.Type.Equal["PC"]} && (${Me.TargetOfTarget.Distance} < 200) && ${Me.TargetOfTarget.PctHPs} <= ${PctHPs} && ${Me.TargetOfTarget.Type.NotEqual["Corpse"]} && ${Me.TargetOfTarget.State.NotEqual["DEAD"]} && ${Me.SpellReady[DRUID_QH1]}) {
		/call PerformHeal ${Me.TargetOfTarget.ID} ${PctHPs}
		/return
	}

	/for i 0 to 5
		/if (${Group.Member[${i}].ID} && (${Group.Member[${i}].Distance} < 200) && (${Group.Member[${i}].PctHPs} <= ${PctHPs}) && ${Group.Member[${i}].Type.NotEqual["Corpse"]} && ${Group.Member[${i}].State.NotEqual["DEAD"]} && ${Me.SpellReady[DRUID_QH1]}) {
			/call PerformHeal ${Group.Member[${i}].ID} ${PctHPs}
			/return
		}
	/next i

/return


Sub AttemptToStun(string MobName)

	/if (${Spawn[${MobName}].Level} > 75) /return

	/declare StunSpellName string local
	
	| Cleric stuns:
	/if (${Me.Gem["Sound of Divinity"]}) /varset StunSpellName "Sound of Divinity"

	| Druid stuns:
	/if (${Me.Gem["Stormwatch"]}) /varset StunSpellName "Stormwatch"
	/if (${Me.Gem["Gale of the Stormborn"]}) /varset StunSpellName "Gale of the Stormborn"
	/if (${Me.Gem["Gale of the Stormborn Rk. II"]}) /varset StunSpellName "Gale of the Stormborn Rk. II"
	
	/if (!${Me.Gem[${StunSpellName}]}) {
		/call DebugMsg "WARNING: No stun spells memmed!"
		/return
	}

	/if (${Me.Casting.ID} && ${Me.Casting.SpellType.Equal[Detrimental]} && ${Me.Casting.Name.NotEqual[${StunSpellName}]}) {
		|interrupta, ENBART om spellen som kastas e en nuke
		/call DebugMsg "ABORTING cast of ${Me.Casting.Name} for stun!"
		/keypress d
		/delay 1
		/keypress d
	}

	/if (${Spawn[${MobName}].ID} && ${Spawn[${MobName}].LineOfSight}) {
		/target npc ${MobName}
		/delay 5
		/call DebugMsg "Trying to stun ${MobName}"
		/call MQ2Cast ${StunSpellName}
	}

/return


| Loops until SpellName is ready to cast
Sub WaitForSpellReady(string SpellName)

	/if (!${Me.Book[${SpellName}]}) {
		/call DebugMsg "ERROR: I dont know the spell ${SpellName}"
		/return
	}

	/if (!${Me.Gem["${SpellName}"]}) {
		/call DebugMsg "ERROR fixme: I dont have spell ${SpellName} MEMORIZED"
		/return
	}

	:SpellReadyLoop
	/if (!${Me.SpellReady[${SpellName}]} || ${Me.Moving}) {
		/doevents
		/goto :SpellReadyLoop
	}

/return

Sub ShrinkSelf(string ShrinkItem)

	/call CheckNeutralZone
	/if (${Macro.Return} || ${Casting.ID}) /return

	| Note: When you are max shrinked, your Height == 2.00
	/target myself
	:ShrinkLoop
	/if (${Me.Height} > 2.00) {
		/casting "${ShrinkItem}" item
		/goto :ShrinkLoop
	}
	/echo # Done Shrinking
/return