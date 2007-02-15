#define NUKE_SPELL		"Char"
#define MALO_SPELL		"Malosi"

| need a delay to not overnuke, but it need to be adjusted depending on tank gear/aa
#define NUKE_DELAY 20s

#define STARTNUKE_PERCENT	90


Sub HandleAssistCallMage(int SpawnId)

	/declare nukeName string local
	
	/varset nukeName NUKE_SPELL

	/if (!${SpawnId}) {
		/return
	}

	/declare zoneId int local ${Zone.ID}

	/target npc id ${SpawnId}
	/delay 20

	/call DebugMsg "Assisting on ${Target.CleanName} (${Me.PctMana}m)"

	/if (${Me.Pet.ID}) {

		/call MageWaitForTargetHP ${SpawnId} 98
		| Send in pet if u have one
		/call DebugMsg "Sending in pet"
		/pet attack
	}
	
	/if ((${Me.PctMana} > 30) && (${Target.PctHPs} <= 99) && (${Target.PctHPs} >= 70)) {
		/call MageWaitForTargetHP ${SpawnId} 92
		/call Cast MALO_SPELL
	}



	/varset AbortLoop 0
	:MageLoop
		/doevents
		/if (${Zone.ID} != ${zoneId}) {
			/call DebugMsg "Stopping loop, zoned"
			/return
		}

		/if (!${Spawn[npc id ${SpawnId}].ID}) {
			/call DebugMsg "Ending nuking (mob dead) (${Me.PctMana}m)"
			/goto :MageDone
		}

		/if (${Me.TargetOfTarget.ID} == ${Me.ID}) {
			/call DebugMsg "Got aggro, waiting"
			/delay 10s
		}

		/doevents
		/if (${AbortLoop}) {
			/call DebugMsg "Aborting nukage!"
			/goto :MageDone
		}
		
		/target npc id ${SpawnId}
		/doevents
		/delay 10
		
		/if (${Spawn[npc id ${SpawnId}].ID} && ${Spawn[npc id ${SpawnId}].LineOfSight} && (${Me.TargetOfTarget.ID} != ${Me.ID})) {

			/target npc id ${SpawnId}
			/delay 2s ${Target.ID}==${SpawnId}
			
			/call MageWaitForTargetHP ${SpawnId} STARTNUKE_PERCENT

			/call WaitForSpellReady "${nukeName}"
			/if (${Target.ID} && ${Target.LineOfSight} && !${Me.Moving} && (${Target.Distance} < ${Me.Gem[${Me.Gem[${nukeName}]}].Range})) {
				/if ((${Me.PctMana} > LOM_PERCENT) && (${Target.PctHPs} > 5)) {
					/if (!${timerNuke}) {
						/call DebugMsg "Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)"
						/call Cast "${nukeName}"
						/varset timerNuke NUKE_DELAY
					}
				} else {
					/if (!${Me.Moving} && ${Me.Standing} && (${Me.PctMana} < LOM_PERCENT)) {
						/if (${Spawn[npc radius 40 notid ${SpawnId}].LineOfSight}) {							
							/call DebugMsg "LOM (${Me.PctMana}m) - But ${SpawnCount[npc radius 40 notid ${SpawnId}]} adds in camp!"
							/delay 5s
						} else {
							/call DebugMsg "LOM - Medding (${Me.PctMana}m)"
							/delay 2s
							/if (${Me.Standing} && !${Me.Moving}) /sit
						}
					}
				}
			}
		}
		
	/goto :MageLoop

	:MageDone

	/call UpdateBuffs
	/call HandleEndOfFightCaster

/return

Sub MageUpdateBuffs
	/declare oldSpell string local ${Me.Gem[7]}

	/if (!${Me.Pet.ID}) {
		/call DebugMsg "Making pet"
		/call Cast "Greater Conjuration: Water"
	}
	
	/if (${Me.Pet.ID}) {
		/call RefreshPetBuff "Burnout III" 10
	}
	
	|/call RefreshBuff "Elemental Armor"
	/call RefreshBuff "Arch Shielding"

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/call DebugMsg "Restoring old spell gem"
		/memspell 7 "${oldSpell}"
	}

/return

Sub MageWaitForTargetHP(int ID, int pct)

	/declare oldid int local ${Target.ID}

	/if (${Target.ID} != ${ID}) {
		/target id ${ID}
		/delay 2s ${Target.ID}==${ID}
	}

	:Loop
	/if (${Target.PctHPs} > ${pct}) {
		/doevents
		/goto :Loop
	}
	
	/if (${oldid}) {
		/target id ${oldid}
		/delay 2s ${Target.ID}==${oldid}
	}

/return