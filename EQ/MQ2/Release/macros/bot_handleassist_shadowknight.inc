#define NUKE_SPELL		"Spear of Muram"

#define DO_NUKE				1


Sub HandleAssistCallShadowKnight(int SpawnId)

	/if (!${SpawnId}) {
		/return
	}

	/declare zoneId int local ${Zone.ID}

	/i msg IRC_CHANNEL # Assisting on ${Target.CleanName} (${Me.PctMana}m)

	/target npc id ${SpawnId}
	/delay 20

	|vänta till target e tillräckligt nära
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
	/if ((${Target.Distance} > 50) || !${Target.LineOfSight}) {
		/doevents
		/goto :WaitTillTargetClose
	}
	:TargetIsClose


	|/stick 15 pin
	/stick 15
	/attack on

	/call ShadowKnightWaitForTargetHP 94
	/call WaitForSpellReady "Theft of Hate"
	/if (!${Me.Feigning} && ${Me.Combat}) {
		/call Cast "Theft of Hate"
		/delay 2s
	}

	/call ShadowKnightWaitForTargetHP 90
	/call WaitForSpellReady "Blood of Discord"
	/if (!${Me.Feigning} && ${Me.Combat}) {
		/call Cast "Blood of Discord"
		/delay 2s
	}


	/call ShadowKnightWaitForTargetHP 85
	/call WaitForSpellReady "Theft of Pain"
	/if (!${Me.Feigning} && ${Me.Combat}) {
		/call Cast "Theft of Pain"
		/delay 2s
	}


	/call ShadowKnightWaitForTargetHP 80
	/call WaitForSpellReady "Blood of Inruku"
	/if (!${Me.Feigning} && ${Me.Combat}) {
		/call Cast "Blood of Inruku"
		/delay 2s
	}


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

		/if (DO_NUKE && !${Me.Feigning} && !${Me.Moving} && ${Me.Combat}) {

			/call ShadowKnightWaitForTargetHP 40
			/call WaitForSpellReady "NUKE_SPELL"
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
					/call Cast "NUKE_SPELL"
					/delay 10s
				}
			}
		}

	/goto :Loop

	:Done

	/call UpdateBuffs

/return

Sub ShadowKnightUpdateBuffs
	/declare i int local 0
	/declare oldSpell string local ${Me.Gem[7]}

	/declare GroupHasEnchanter int local 0

	/for i 1 to 5
		/if (${Group.Member[${i}].Class.Name.Equal[Enchanter]}) /varset GroupHasEnchanter 1
	/next i

	/target myself

	|Level 70 sk buffs
	/call RefreshBuff "Mental Corruption" 10
	/if (!${GroupHasEnchanter}) {
		|skelly illusion mana regen:
		|/call RefreshBuff "Pact of Decay" 20
	}

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 7 "${oldSpell}"
	}

/return


Sub ShadowKnightWaitForTargetHP(int pct)

	:Loop
	/if (!${Target.ID}) {
		/return
	}
	/if (${Target.PctHPs} > ${pct}) {
		/doevents
		/goto :Loop
	}

/return