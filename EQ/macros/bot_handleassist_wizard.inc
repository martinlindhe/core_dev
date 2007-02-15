#define NUKE_SPELL		"Strike of Solusek"

Sub HandleAssistCallWizard(int SpawnId)

	/if (!${SpawnId}) {
		/return
	}
	
	/varset AbortLoop 1
	/doevents

	/declare zoneId int local ${Zone.ID}

	/target npc id ${SpawnId}
	/delay 20

	/i msg IRC_CHANNEL # Assisting on ${Target.CleanName} (${Me.PctMana}m)

	:WaitTillTargetClose
	/if (!${Target.ID}) {
		/if (${Spawn[id ${SpawnId} npc].ID}) {
			/target npc id ${SpawnId}
			/delay 20
		} else {
			/i msg IRC_CHANNEL # Target died, aborting
			/goto :Done
		}
	}
	/if ((${Target.Distance} > 80) || !${Target.LineOfSight}) {
		/doevents
		/goto :WaitTillTargetClose
	}
	:TargetIsClose

	/if (${Target.ID} != ${SpawnId}) {
		/target npc id ${SpawnId}
		/delay 20		
	}

	/call WizardWaitForTargetHP 85
	/varset AbortLoop 0

	:Loop
		/doevents
		/if (${Zone.ID} != ${zoneId}) {
			/i msg IRC_CHANNEL # Stopping loop, zoned
			/return
		}

		/if (!${Spawn[npc id ${SpawnId}].ID}) {
			/i msg IRC_CHANNEL # Ending nuking (mob dead) (${Me.PctMana}m)
			/goto :Done
		}			

		/target npc id ${SpawnId}
		/doevents
		/delay 20

		/if (${Target.Type.NotEqual[NPC]}) {
			/i msg IRC_CHANNEL # Got wrong target: ${Target.CleanName} (not a npc!)
			/target npc id ${SpawnId}
			/delay 20
			/goto :Loop
		}
		/if (${AbortLoop}) {
			/goto :Done
		}
		/if (${Me.PctMana} < 10) {
			/i msg IRC_CHANNEL LOM - Medding (${Me.PctMana}m)
			/goto :Done
		}
		/if (${Me.TargetOfTarget.ID} == ${Me.ID}) {
			/i msg IRC_CHANNEL # Got aggro, waiting
			/delay 10s
		}

		/doevents
		/if (${AbortLoop}) {
			/i msg IRC_CHANNEL # Aborting nukage!
			/goto :Done
		}
		
		/target npc id ${SpawnId}
		/doevents
		/delay 10
		/if (${Target.ID}) {
			/if (${Target.PctHPs} > 5) {
				/i msg IRC_CHANNEL # Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)
				/call WaitForSpellReady "NUKE_SPELL"
				/call Cast "NUKE_SPELL"
				|/delay 7s
			} else {
				|Small nuke
				/i msg IRC_CHANNEL # Small nuke on ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)
				/call Cast "Draught of Ro"
				/goto :Done
			}
		}
		
		/if (${Me.PctMana} < 90) {
			|
			/if (${Me.AltAbilityReady["Harvest of Druzzil"]}) {
				/i msg IRC_CHANNEL # Getting mana - Harvest of Druzzil
				/aa act Harvest of Druzzil
			} else {
				/if (${Me.SpellReady["Harvest"]}) {
					/i msg IRC_CHANNEL # Getting mana - Harvest
					/call Cast "Harvest"
				}
			}
		}

	/goto :Loop

	:Done

	/call UpdateBuffs
	/call HandleEndOfFightCaster

/return

Sub WizardUpdateBuffs
	/declare oldSpell string local ${Me.Gem[7]}

	|Level 70 wizard buffs
	/if (!${Me.Buff["Wunshi's Focusing"].ID}) {
		|stackar inte med focus
		/call RefreshBuff "Shield of Maelin" 50
	}
	/call RefreshBuff "Force Shield" 50
	
	/if (!${Me.Pet.ID}) {
		/if (${Me.AltAbilityReady["Ro's Flaming Familiar"]}) {
			/i msg IRC_CHANNEL # Making familiar
			/aa act Ro's Flaming Familiar
		}
	}

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 7 "${oldSpell}"
	}

/return



Sub WizardWaitForTargetHP(int pct)

	|todo: kolla så man inte har aggro?

	:Loop
	/if (!${Target.ID}) {
		/return
	}
	/if (${Target.PctHPs} > ${pct}) {
		/doevents
		/goto :Loop
	}

/return